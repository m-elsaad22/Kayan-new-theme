<?php
/**
 * Admin module: Import / Export.
 *
 * Exports/imports platform-owned data only: global settings, country
 * profiles, and custom language registry. Never touches Theme Options
 * (YTS), booking/payment/track data, or WordPress core options.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Import_Export {

	const NONCE_EXPORT = 'kayan_admin_export';
	const NONCE_IMPORT = 'kayan_admin_import';

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'kayan_admin_register_modules', array( $this, 'register_module' ), 15 );
	}

	/**
	 * @param Kayan_Admin_Module_Registry $registry Registry.
	 * @return void
	 */
	public function register_module( $registry ) {
		// Combine the Phase 3.0 'import' + 'export' placeholder shells into one functional screen.
		$registry->register_module(
			'import',
			array(
				'label'       => __( 'Import / Export', 'kayan' ),
				'description' => __( 'Backup or transfer platform-owned settings (not Theme Options).', 'kayan' ),
				'icon'        => 'dashicons-database-import',
				'position'    => 90,
				'capability'  => 'kayan_manage_import',
				'group'       => 'tools',
				'screen'      => array( $this, 'screen' ),
				'save'        => array( $this, 'save' ),
				'permissions' => array( 'kayan_manage_export' ),
			)
		);
		$registry->register_module(
			'export',
			array(
				'label'      => __( 'Export', 'kayan' ),
				'capability' => 'kayan_manage_export',
				'group'      => 'tools',
				'nav'        => false, // merged into the Import module screen above
			)
		);
	}

	/**
	 * @return array<string,mixed>
	 */
	public function build_payload() {
		$platform  = kayan_platform();
		$countries = array();
		foreach ( $platform->countries->all() as $code => $data ) {
			unset( $data );
			$countries[ $code ] = $platform->settings->country_repository()->get_profile( $code );
		}

		return array(
			'kayan_export_version' => defined( 'KAYAN_PLATFORM_VERSION' ) ? KAYAN_PLATFORM_VERSION : '',
			'generated_at'         => gmdate( 'c' ),
			'global_settings'      => get_option( Kayan_Settings_Engine::OPTION_GLOBAL, array() ),
			'country_profiles'     => $countries,
			'custom_languages'     => get_option( Kayan_Admin_Module_Languages::OPTION_CUSTOM, array() ),
			'disabled_languages'   => get_option( Kayan_Admin_Module_Languages::OPTION_ENABLED, array() ),
		);
	}

	/**
	 * @param array $module  Module.
	 * @param array $context Context.
	 * @return void
	 */
	public function save( $module, $context ) {
		unset( $module );
		$action = isset( $_POST['kayan_ie_action'] ) ? sanitize_key( wp_unslash( $_POST['kayan_ie_action'] ) ) : '';

		if ( 'export' === $action && current_user_can( 'kayan_manage_export' ) ) {
			check_admin_referer( self::NONCE_EXPORT, '_kayan_nonce' );
			$payload = $this->build_payload();
			nocache_headers();
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename="kayan-platform-export-' . gmdate( 'Ymd-His' ) . '.json"' );
			echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			if ( function_exists( 'kayan_logger' ) ) {
				kayan_logger()->info( 'general', 'admin.export.completed' );
			}
			exit;
		}

		if ( 'import' === $action && current_user_can( 'kayan_manage_import' ) ) {
			check_admin_referer( self::NONCE_IMPORT, '_kayan_nonce' );

			if ( empty( $_FILES['kayan_import_file']['tmp_name'] ) || ! is_uploaded_file( $_FILES['kayan_import_file']['tmp_name'] ) ) {
				$context['admin']->redirect_module( 'import', 'error' );
				return;
			}

			$raw  = file_get_contents( $_FILES['kayan_import_file']['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$data = json_decode( (string) $raw, true );

			if ( ! is_array( $data ) || ! isset( $data['global_settings'], $data['country_profiles'] ) ) {
				$context['admin']->redirect_module( 'import', 'error' );
				return;
			}

			if ( is_array( $data['global_settings'] ) ) {
				update_option( Kayan_Settings_Engine::OPTION_GLOBAL, $data['global_settings'], false );
			}
			if ( is_array( $data['country_profiles'] ) ) {
				$repo = kayan_platform()->settings->country_repository();
				foreach ( $data['country_profiles'] as $code => $profile ) {
					if ( is_array( $profile ) && kayan_platform()->countries->exists( $code ) ) {
						$repo->update_profile( $code, $profile );
					}
				}
			}
			if ( isset( $data['custom_languages'] ) && is_array( $data['custom_languages'] ) ) {
				update_option( Kayan_Admin_Module_Languages::OPTION_CUSTOM, $data['custom_languages'], false );
			}
			if ( isset( $data['disabled_languages'] ) && is_array( $data['disabled_languages'] ) ) {
				update_option( Kayan_Admin_Module_Languages::OPTION_ENABLED, $data['disabled_languages'], false );
			}

			if ( function_exists( 'kayan_cache' ) ) {
				kayan_cache()->flush_group( 'settings' );
			}
			if ( function_exists( 'kayan_logger' ) ) {
				kayan_logger()->info( 'general', 'admin.import.completed' );
			}

			$context['admin']->redirect_module( 'import', 'imported' );
		}
	}

	/**
	 * @param array $module  Module.
	 * @param array $context Context.
	 * @return void
	 */
	public function screen( $module, $context ) {
		unset( $module );
		/** @var Kayan_Admin_UI $ui */
		$ui    = $context['ui'];
		$admin = $context['admin'];

		echo $ui->notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'type'    => 'info',
				'message' => __( 'Export/import covers global platform settings, country profiles, and custom languages only. Theme Options (YTS), booking, payment, and tracking data are never touched.', 'kayan' ),
			)
		);

		if ( current_user_can( 'kayan_manage_export' ) ) {
			ob_start();
			?>
			<form method="post" action="<?php echo esc_url( $admin->module_url( 'import' ) ); ?>">
				<?php wp_nonce_field( self::NONCE_EXPORT, '_kayan_nonce' ); ?>
				<input type="hidden" name="kayan_ie_action" value="export" />
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Download export (.json)', 'kayan' ); ?></button>
			</form>
			<?php
			echo $ui->card( array( 'title' => __( 'Export', 'kayan' ), 'content' => (string) ob_get_clean() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( current_user_can( 'kayan_manage_import' ) ) {
			ob_start();
			?>
			<form method="post" action="<?php echo esc_url( $admin->module_url( 'import' ) ); ?>" enctype="multipart/form-data">
				<?php wp_nonce_field( self::NONCE_IMPORT, '_kayan_nonce' ); ?>
				<input type="hidden" name="kayan_ie_action" value="import" />
				<p><input type="file" name="kayan_import_file" accept="application/json" required /></p>
				<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Import file', 'kayan' ); ?></button></p>
			</form>
			<?php
			echo $ui->card( array( 'title' => __( 'Import', 'kayan' ), 'content' => (string) ob_get_clean() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
