<?php
/**
 * KAYAN Admin Dashboard Foundation — widget slots only (no statistics yet).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Dashboard {

	/** @var array<string,array<string,mixed>> */
	private $widgets = array();

	/** @var Kayan_Admin_UI */
	private $ui;

	/** @var Kayan_Admin_Permissions */
	private $permissions;

	public function __construct( Kayan_Admin_UI $ui, Kayan_Admin_Permissions $permissions ) {
		$this->ui          = $ui;
		$this->permissions = $permissions;
	}

	/**
	 * @return void
	 */
	public function register() {
		$this->register_core_widget_slots();

		/**
		 * @param Kayan_Admin_Dashboard $dashboard Dashboard.
		 */
		do_action( 'kayan_admin_register_dashboard_widgets', $this );
	}

	/**
	 * @param string              $id   Widget id.
	 * @param array<string,mixed> $args Args.
	 * @return void
	 */
	public function register_widget( $id, array $args ) {
		$id = sanitize_key( $id );
		if ( ! $id ) {
			return;
		}
		$defaults = array(
			'title'       => $id,
			'description' => '',
			'module'      => '',
			'capability'  => Kayan_Admin_Permissions::CAP_ACCESS,
			'position'    => 50,
			'enabled'     => true,
			'callback'    => null, // callable — not implemented for stats yet
			'placeholder' => true,
		);
		$this->widgets[ $id ] = array_merge( $defaults, $args, array( 'id' => $id ) );
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function widgets() {
		$widgets = $this->widgets;
		uasort(
			$widgets,
			static function ( $a, $b ) {
				return ( (int) $a['position'] ) <=> ( (int) $b['position'] );
			}
		);
		/**
		 * @param array $widgets Widgets.
		 */
		return apply_filters( 'kayan_admin_dashboard_widgets', $widgets );
	}

	/**
	 * Render dashboard architecture (placeholders only).
	 *
	 * @return string
	 */
	public function render() {
		$cards = array();
		foreach ( $this->widgets() as $widget ) {
			if ( empty( $widget['enabled'] ) ) {
				continue;
			}
			if ( ! $this->permissions->can( $widget['capability'] ) ) {
				continue;
			}

			$content = '';
			if ( is_callable( $widget['callback'] ) ) {
				$content = (string) call_user_func( $widget['callback'], $widget );
			} else {
				$content = '<p class="kayan-admin-widget-placeholder">' . esc_html__( 'Widget slot ready. Statistics will arrive in a later phase.', 'kayan' ) . '</p>';
				if ( ! empty( $widget['description'] ) ) {
					$content .= '<p class="description">' . esc_html( (string) $widget['description'] ) . '</p>';
				}
			}

			$cards[] = $this->ui->card(
				array(
					'title'  => (string) $widget['title'],
					'content'=> $content,
					'status' => 'ready',
					'class'  => 'kayan-admin-dashboard__widget kayan-admin-dashboard__widget--' . sanitize_html_class( $widget['id'] ),
					'id'     => 'kayan-widget-' . $widget['id'],
				)
			);
		}

		$intro = $this->ui->notice(
			array(
				'type'    => 'info',
				'message' => __( 'KAYAN Admin Dashboard foundation is active. Widgets are registered as slots — no statistics in Phase 3.0.', 'kayan' ),
			)
		);

		return '<div class="kayan-admin-dashboard">' . $intro . '<div class="kayan-admin-dashboard__grid">' . implode( '', $cards ) . '</div></div>';
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'widgets' => array_keys( $this->widgets() ),
			'stats'   => false,
			'apis'    => array(
				'register_widget' => 'kayan_admin()->dashboard->register_widget( $id, $args )',
				'render'          => 'kayan_admin()->dashboard->render()',
			),
		);
	}

	/**
	 * Future widget slots — no data/statistics.
	 *
	 * @return void
	 */
	private function register_core_widget_slots() {
		$slots = array(
			'seo' => array(
				'title'       => 'SEO',
				'description' => 'Future SEO overview widget.',
				'module'      => 'rankmath',
				'capability'  => 'kayan_manage_rankmath',
				'position'    => 10,
			),
			'countries' => array(
				'title'       => 'Countries',
				'description' => 'Future countries overview widget.',
				'module'      => 'countries',
				'capability'  => 'kayan_manage_countries',
				'position'    => 20,
			),
			'languages' => array(
				'title'       => 'Languages',
				'description' => 'Future languages overview widget.',
				'module'      => 'languages',
				'capability'  => 'kayan_manage_languages',
				'position'    => 30,
			),
			'programmatic_seo' => array(
				'title'       => 'Programmatic SEO',
				'description' => 'Future PSEO overview widget.',
				'module'      => 'pseo',
				'capability'  => 'kayan_manage_pseo',
				'position'    => 40,
			),
			'queue' => array(
				'title'       => 'Queue',
				'description' => 'Future job queue widget.',
				'module'      => 'queue',
				'capability'  => 'kayan_manage_queue',
				'position'    => 50,
			),
			'ai' => array(
				'title'       => 'AI',
				'description' => 'Future AI status widget.',
				'module'      => 'ai',
				'capability'  => 'kayan_manage_ai',
				'position'    => 60,
			),
			'performance' => array(
				'title'       => 'Performance',
				'description' => 'Future performance widget.',
				'module'      => 'performance',
				'capability'  => 'kayan_manage_performance',
				'position'    => 70,
			),
			'analytics' => array(
				'title'       => 'Analytics',
				'description' => 'Future analytics widget.',
				'module'      => 'analytics',
				'capability'  => 'kayan_manage_analytics',
				'position'    => 80,
			),
			'rankmath' => array(
				'title'       => 'Rank Math',
				'description' => 'Future Rank Math integration widget.',
				'module'      => 'rankmath',
				'capability'  => 'kayan_manage_rankmath',
				'position'    => 90,
			),
			'logs' => array(
				'title'       => 'Logs',
				'description' => 'Future logs overview widget.',
				'module'      => 'logs',
				'capability'  => 'kayan_view_logs',
				'position'    => 100,
			),
		);

		foreach ( $slots as $id => $args ) {
			$this->register_widget( $id, $args );
		}
	}
}
