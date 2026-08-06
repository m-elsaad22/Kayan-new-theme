<?php
/**
 * Kayan_Admin_Platform — centralized Admin Platform facade (Phase 3.0).
 *
 * One WP admin shell. Modules register through the Module Registry.
 * No isolated admin pages. No statistics / generation / AI in this phase.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Platform {

	const MENU_SLUG = 'kayan-platform';

	/** @var Kayan_Admin_Permissions */
	public $permissions;

	/** @var Kayan_Admin_Module_Registry */
	public $modules;

	/** @var Kayan_Admin_UI */
	public $ui;

	/** @var Kayan_Admin_Dashboard */
	public $dashboard;

	/** @var Kayan_Admin_Core_Modules */
	private $core_modules;

	/** @var Kayan_Logger|null */
	private $logger;

	/** @var bool */
	private $booted = false;

	public function __construct( ?Kayan_Logger $logger = null ) {
		$this->logger       = $logger;
		$this->permissions  = new Kayan_Admin_Permissions();
		$this->modules      = new Kayan_Admin_Module_Registry( $this->permissions );
		$this->ui           = new Kayan_Admin_UI();
		$this->dashboard    = new Kayan_Admin_Dashboard( $this->ui, $this->permissions );
		$this->core_modules = new Kayan_Admin_Core_Modules( $this->modules, $this->ui );
	}

	/**
	 * @return void
	 */
	public function register() {
		if ( $this->booted ) {
			return;
		}
		$this->booted = true;

		$this->permissions->register();
		$this->ui->register();
		$this->dashboard->register();
		$this->core_modules->register();
		$this->modules->register();

		add_action( 'admin_menu', array( $this, 'register_menu' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		/**
		 * @param Kayan_Admin_Platform $admin Admin platform.
		 */
		do_action( 'kayan_admin_platform_registered', $this );

		if ( $this->logger ) {
			$this->logger->info( 'general', 'admin.platform.registered', array( 'modules' => count( $this->modules->all() ) ) );
		}
	}

	/**
	 * Single top-level menu — modules become query-routed screens (and WP submenus for discoverability).
	 *
	 * @return void
	 */
	public function register_menu() {
		if ( ! $this->permissions->can_access() ) {
			return;
		}

		add_menu_page(
			__( 'KAYAN Platform', 'kayan' ),
			__( 'KAYAN', 'kayan' ),
			Kayan_Admin_Permissions::CAP_ACCESS,
			self::MENU_SLUG,
			array( $this, 'render_shell' ),
			'dashicons-admin-site-alt3',
			3
		);

		foreach ( $this->modules->nav_modules() as $id => $module ) {
			$slug = self::MENU_SLUG;
			// First module uses parent slug; others use query args via submenu pointing to same callback.
			$submenu_slug = self::MENU_SLUG . '&module=' . rawurlencode( $id );
			add_submenu_page(
				self::MENU_SLUG,
				(string) $module['label'],
				(string) $module['label'],
				(string) $module['capability'],
				// WordPress submenu slugs cannot reliably carry query args — use dedicated slug mapped in render.
				self::MENU_SLUG . '-' . $id,
				array( $this, 'render_shell' )
			);
			unset( $slug, $submenu_slug );
		}

		// Remove automatic duplicate first submenu (WP clones parent).
		remove_submenu_page( self::MENU_SLUG, self::MENU_SLUG );
	}

	/**
	 * @param string $hook Hook.
	 * @return void
	 */
	public function enqueue_assets( $hook ) {
		if ( false === strpos( (string) $hook, self::MENU_SLUG ) ) {
			return;
		}

		$base = defined( 'KAYAN_PLATFORM_DIR' ) ? KAYAN_PLATFORM_DIR : dirname( __DIR__, 2 );
		$url  = $this->pack_url();
		$ver  = defined( 'KAYAN_PLATFORM_VERSION' ) ? KAYAN_PLATFORM_VERSION : '3.0.0';

		$css = $base . '/assets/admin/kayan-admin.css';
		$js  = $base . '/assets/admin/kayan-admin.js';

		if ( file_exists( $css ) ) {
			wp_enqueue_style( 'kayan-admin', $url . '/assets/admin/kayan-admin.css', array(), $ver );
		}
		if ( file_exists( $js ) ) {
			wp_enqueue_script( 'kayan-admin', $url . '/assets/admin/kayan-admin.js', array(), $ver, true );
		}
	}

	/**
	 * Central shell renderer — dispatches to the active module screen.
	 *
	 * @return void
	 */
	public function render_shell() {
		if ( ! $this->permissions->can_access() ) {
			wp_die( esc_html__( 'You do not have permission to access the KAYAN Admin Platform.', 'kayan' ) );
		}

		$module_id = $this->current_module_id();
		$module    = $this->modules->get( $module_id );
		if ( ! $module ) {
			$module_id = 'dashboard';
			$module    = $this->modules->get( 'dashboard' );
		}

		if ( $module && ! $this->permissions->can( $module['capability'] ) ) {
			wp_die( esc_html__( 'You do not have permission to access this module.', 'kayan' ) );
		}

		$nav = $this->modules->nav_modules();
		?>
		<div class="wrap kayan-admin-shell" data-kayan-admin>
			<h1><?php echo esc_html__( 'KAYAN Platform', 'kayan' ); ?></h1>
			<div class="kayan-admin-shell__layout">
				<aside class="kayan-admin-shell__nav" aria-label="<?php esc_attr_e( 'KAYAN modules', 'kayan' ); ?>">
					<ul>
						<?php foreach ( $nav as $id => $item ) : ?>
							<?php
							$url    = $this->module_url( $id );
							$active = $id === $module_id ? ' is-active' : '';
							?>
							<li class="kayan-admin-shell__nav-item<?php echo esc_attr( $active ); ?>">
								<a href="<?php echo esc_url( $url ); ?>">
									<span class="dashicons <?php echo esc_attr( (string) $item['icon'] ); ?>"></span>
									<span><?php echo esc_html( (string) $item['label'] ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</aside>
				<main class="kayan-admin-shell__main">
					<header class="kayan-admin-shell__header">
						<h2><?php echo esc_html( (string) ( $module['label'] ?? $module_id ) ); ?></h2>
						<?php
						echo $this->ui->status( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							array(
								'label' => 'Phase 3.0',
								'type'  => 'info',
							)
						);
						?>
					</header>
					<div class="kayan-admin-shell__content">
						<?php $this->render_module_screen( $module ); ?>
					</div>
				</main>
			</div>
		</div>
		<?php
	}

	/**
	 * @param string $module_id Module.
	 * @return string
	 */
	public function module_url( $module_id ) {
		$module_id = sanitize_key( $module_id );
		return add_query_arg(
			array(
				'page'   => self::MENU_SLUG . '-' . $module_id,
			),
			admin_url( 'admin.php' )
		);
	}

	/**
	 * @return string
	 */
	public function current_module_id() {
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( self::MENU_SLUG === $page ) {
			return 'dashboard';
		}
		$prefix = self::MENU_SLUG . '-';
		if ( 0 === strpos( $page, $prefix ) ) {
			return sanitize_key( substr( $page, strlen( $prefix ) ) );
		}
		if ( isset( $_GET['module'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return sanitize_key( wp_unslash( $_GET['module'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		return 'dashboard';
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'version'     => '3.0.0',
			'menu_slug'   => self::MENU_SLUG,
			'modules'     => $this->modules->describe(),
			'permissions' => $this->permissions->describe(),
			'ui'          => $this->ui->describe(),
			'dashboard'   => $this->dashboard->describe(),
			'apis'        => array(
				'register_module' => 'kayan_admin()->modules->register_module( $id, $args )',
				'can'             => 'kayan_admin()->permissions->can( $cap )',
				'ui_card'         => 'kayan_admin()->ui->card( $args )',
				'widget'          => 'kayan_admin()->dashboard->register_widget( $id, $args )',
			),
			'note'        => 'Architecture foundation only — no statistics, generation, or AI.',
		);
	}

	/**
	 * @param array|null $module Module.
	 * @return void
	 */
	private function render_module_screen( $module ) {
		if ( ! is_array( $module ) ) {
			echo $this->ui->notice( array( 'type' => 'error', 'message' => __( 'Module not found.', 'kayan' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$context = array(
			'admin'     => $this,
			'dashboard' => $this->dashboard,
			'ui'        => $this->ui,
			'module_id' => $module['id'],
		);

		if ( ! empty( $module['screen'] ) && is_callable( $module['screen'] ) ) {
			call_user_func( $module['screen'], $module, $context );
			return;
		}

		echo $this->ui->empty_state( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'title'       => (string) $module['label'],
				'description' => __( 'Module registered. Screen callback not set.', 'kayan' ),
			)
		);
	}

	/**
	 * @return string
	 */
	private function pack_url() {
		if ( function_exists( 'get_template_directory_uri' ) ) {
			return trailingslashit( get_template_directory_uri() ) . 'components/packs/kayan-platform';
		}
		return '';
	}
}
