<?php
/**
 * Admin module: Settings (Settings Framework).
 *
 * Platform-level settings only — global scope of Kayan_Settings_Engine.
 * Does NOT duplicate Theme Options (YTS); those remain the theme's
 * contact/business/SEO source. This screen controls the platform's own
 * behavior: routing mode, cache/log tuning, default locale overrides.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Settings {

	const NONCE_SAVE = 'kayan_admin_settings_save';

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'kayan_admin_register_modules', array( $this, 'register_module' ), 15 );
		add_action( 'kayan_platform_booted', array( $this, 'apply_stored_settings' ), 20 );
	}

	/**
	 * Feed the stored global settings into existing engine filters/setters —
	 * this module does not introduce a second config path.
	 *
	 * @param Kayan_Platform $platform Platform.
	 * @return void
	 */
	public function apply_stored_settings( $platform ) {
		$settings = $platform->settings_engine;
		$defaults = $this->defaults();

		add_filter(
			'kayan_platform_url_mode',
			static function ( $mode ) use ( $settings, $defaults ) {
				return $settings->get_global( 'routing_mode', $defaults['routing_mode'] );
			}
		);
		add_filter(
			'kayan_logger_enabled',
			static function ( $enabled ) use ( $settings, $defaults ) {
				unset( $enabled );
				return (bool) $settings->get_global( 'logger_enabled', $defaults['logger_enabled'] );
			}
		);
		add_filter(
			'kayan_logger_max_entries',
			static function ( $max ) use ( $settings, $defaults ) {
				unset( $max );
				return (int) $settings->get_global( 'logger_max_entries', $defaults['logger_max_entries'] );
			}
		);

		$platform->query->set_default_ttl( (int) $settings->get_global( 'cache_default_ttl', $defaults['cache_default_ttl'] ) );
	}

	/**
	 * @param Kayan_Admin_Module_Registry $registry Registry.
	 * @return void
	 */
	public function register_module( $registry ) {
		$registry->register_module(
			'settings',
			array(
				'label'       => __( 'Settings', 'kayan' ),
				'description' => __( 'Platform-level behavior — not a replacement for Theme Options.', 'kayan' ),
				'icon'        => 'dashicons-admin-generic',
				'position'    => 5,
				'capability'  => 'kayan_manage_settings',
				'group'       => 'core',
				'screen'      => array( $this, 'screen' ),
				'save'        => array( $this, 'save' ),
			)
		);
	}

	/**
	 * @return array<string,mixed>
	 */
	private function defaults() {
		return array(
			'routing_mode'         => KAYAN_PLATFORM_URL_MODE_LANG_FIRST,
			'cache_default_ttl'    => 300,
			'logger_max_entries'   => Kayan_Logger::MAX_ENTRIES,
			'logger_enabled'       => true,
		);
	}

	/**
	 * @param array $module  Module.
	 * @param array $context Context.
	 * @return void
	 */
	public function save( $module, $context ) {
		unset( $module );
		if ( ! isset( $_POST['kayan_settings_save'] ) ) {
			return;
		}
		check_admin_referer( self::NONCE_SAVE, '_kayan_nonce' );

		$settings = kayan_settings();
		$settings->set_global( 'routing_mode', isset( $_POST['routing_mode'] ) && KAYAN_PLATFORM_URL_MODE_LEGACY === $_POST['routing_mode'] ? KAYAN_PLATFORM_URL_MODE_LEGACY : KAYAN_PLATFORM_URL_MODE_LANG_FIRST );
		$settings->set_global( 'cache_default_ttl', isset( $_POST['cache_default_ttl'] ) ? max( 0, absint( $_POST['cache_default_ttl'] ) ) : 300 );
		$settings->set_global( 'logger_max_entries', isset( $_POST['logger_max_entries'] ) ? max( 20, absint( $_POST['logger_max_entries'] ) ) : Kayan_Logger::MAX_ENTRIES );
		$settings->set_global( 'logger_enabled', ! empty( $_POST['logger_enabled'] ) );

		if ( function_exists( 'kayan_logger' ) ) {
			kayan_logger()->info( 'general', 'admin.settings.updated' );
		}

		$context['admin']->redirect_module( 'settings', 'updated' );
	}

	/**
	 * @param array $module  Module.
	 * @param array $context Context.
	 * @return void
	 */
	public function screen( $module, $context ) {
		unset( $module );
		/** @var Kayan_Admin_UI $ui */
		$ui       = $context['ui'];
		$admin    = $context['admin'];
		$settings = kayan_settings();
		$defaults = $this->defaults();

		$values = array();
		foreach ( $defaults as $key => $default ) {
			$values[ $key ] = $settings->get_global( $key, $default );
		}

		echo $ui->notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'type'    => 'info',
				'message' => __( 'These are platform-level settings. Contact details, currency, and theme appearance stay in Theme Options (YTS) — use the Countries module for per-country business profile.', 'kayan' ),
			)
		);

		ob_start();
		?>
		<form method="post" action="<?php echo esc_url( $admin->module_url( 'settings' ) ); ?>">
			<?php wp_nonce_field( self::NONCE_SAVE, '_kayan_nonce' ); ?>
			<input type="hidden" name="kayan_settings_save" value="1" />
			<?php
			echo $ui->field( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'type'        => 'select',
					'name'        => 'routing_mode',
					'label'       => __( 'URL routing mode', 'kayan' ),
					'value'       => $values['routing_mode'],
					'options'     => array(
						KAYAN_PLATFORM_URL_MODE_LANG_FIRST => __( 'Language-first (/en/sa/…) — recommended', 'kayan' ),
						KAYAN_PLATFORM_URL_MODE_LEGACY      => __( 'Legacy', 'kayan' ),
					),
					'description' => __( 'Read by kayan_platform_url_mode filter consumers.', 'kayan' ),
				)
			);
			echo $ui->field( array( 'type' => 'number', 'name' => 'cache_default_ttl', 'label' => __( 'Cache default TTL (seconds)', 'kayan' ), 'value' => $values['cache_default_ttl'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'number', 'name' => 'logger_max_entries', 'label' => __( 'Logger ring-buffer size', 'kayan' ), 'value' => $values['logger_max_entries'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'checkbox', 'name' => 'logger_enabled', 'label' => __( 'Logger', 'kayan' ), 'value' => $values['logger_enabled'], 'description' => __( 'Enable platform logging.', 'kayan' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<p class="submit">
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Save settings', 'kayan' ); ?></button>
			</p>
		</form>
		<?php
		echo $ui->card( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'title'   => __( 'Platform settings', 'kayan' ),
				'content' => (string) ob_get_clean(),
			)
		);
	}
}
