<?php
/**
 * Admin module: Languages.
 *
 * Reuses Kayan_Language_Engine (ar/en built-in). Extra languages are stored
 * in one option and merged via the existing `kayan_platform_languages`
 * filter — no second language registry.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Languages {

	const OPTION_CUSTOM  = 'kayan_platform_custom_languages';
	const OPTION_ENABLED = 'kayan_platform_disabled_languages';
	const NONCE_SAVE     = 'kayan_admin_languages_save';
	const NONCE_ADD      = 'kayan_admin_languages_add';
	const NONCE_REMOVE   = 'kayan_admin_languages_remove';

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'kayan_admin_register_modules', array( $this, 'register_module' ), 15 );
		add_filter( 'kayan_platform_languages', array( $this, 'merge_custom_languages' ), 20 );
	}

	/**
	 * @param Kayan_Admin_Module_Registry $registry Registry.
	 * @return void
	 */
	public function register_module( $registry ) {
		$registry->register_module(
			'languages',
			array(
				'label'       => __( 'Languages', 'kayan' ),
				'description' => __( 'Arabic (default) and English are built in. Register additional languages without editing code.', 'kayan' ),
				'icon'        => 'dashicons-translation',
				'position'    => 15,
				'capability'  => 'kayan_manage_languages',
				'group'       => 'locale',
				'screen'      => array( $this, 'screen' ),
				'save'        => array( $this, 'save' ),
			)
		);
	}

	/**
	 * @param array $languages Languages.
	 * @return array
	 */
	public function merge_custom_languages( $languages ) {
		if ( ! is_array( $languages ) ) {
			$languages = array();
		}
		$custom = get_option( self::OPTION_CUSTOM, array() );
		if ( is_array( $custom ) ) {
			foreach ( $custom as $code => $data ) {
				$code = sanitize_key( $code );
				if ( ! $code || isset( $languages[ $code ] ) ) {
					continue;
				}
				$languages[ $code ] = array_merge(
					array(
						'code'       => $code,
						'label_ar'   => '',
						'label_en'   => $code,
						'dir'        => 'ltr',
						'hreflang'   => $code,
						'is_default' => false,
					),
					(array) $data
				);
			}
		}

		$disabled = self::disabled_codes();
		foreach ( $languages as $code => $data ) {
			$languages[ $code ]['enabled'] = ! in_array( $code, $disabled, true );
		}

		return $languages;
	}

	/**
	 * @return string[]
	 */
	public static function disabled_codes() {
		$disabled = get_option( self::OPTION_ENABLED, array() );
		return is_array( $disabled ) ? array_map( 'sanitize_key', $disabled ) : array();
	}

	/**
	 * Languages available for frontend use (enabled only). Reuse this instead of a new registry.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function enabled_languages() {
		$all      = kayan_platform()->languages->all();
		$disabled = self::disabled_codes();
		return array_diff_key( $all, array_flip( $disabled ) );
	}

	/**
	 * @param array $module  Module.
	 * @param array $context Context.
	 * @return void
	 */
	public function save( $module, $context ) {
		unset( $module );
		$admin = $context['admin'];

		if ( isset( $_POST['kayan_lang_action'] ) && 'add' === $_POST['kayan_lang_action'] ) {
			check_admin_referer( self::NONCE_ADD, '_kayan_nonce' );
			$code = isset( $_POST['code'] ) ? sanitize_key( wp_unslash( $_POST['code'] ) ) : '';
			if ( ! $code || kayan_platform()->languages->exists( $code ) ) {
				$admin->redirect_module( 'languages', 'error' );
				return;
			}
			$custom = get_option( self::OPTION_CUSTOM, array() );
			if ( ! is_array( $custom ) ) {
				$custom = array();
			}
			$custom[ $code ] = array(
				'label_ar'   => isset( $_POST['label_ar'] ) ? sanitize_text_field( wp_unslash( $_POST['label_ar'] ) ) : $code,
				'label_en'   => isset( $_POST['label_en'] ) ? sanitize_text_field( wp_unslash( $_POST['label_en'] ) ) : $code,
				'dir'        => ( isset( $_POST['dir'] ) && 'rtl' === $_POST['dir'] ) ? 'rtl' : 'ltr',
				'hreflang'   => isset( $_POST['hreflang'] ) ? sanitize_text_field( wp_unslash( $_POST['hreflang'] ) ) : $code,
				'is_default' => false,
			);
			update_option( self::OPTION_CUSTOM, $custom, false );
			if ( function_exists( 'kayan_logger' ) ) {
				kayan_logger()->info( 'general', 'admin.languages.added', array( 'code' => $code ) );
			}
			$admin->redirect_module( 'languages', 'updated' );
			return;
		}

		if ( isset( $_POST['kayan_lang_action'] ) && 'remove' === $_POST['kayan_lang_action'] ) {
			check_admin_referer( self::NONCE_REMOVE, '_kayan_nonce' );
			$code = isset( $_POST['code'] ) ? sanitize_key( wp_unslash( $_POST['code'] ) ) : '';
			$custom = get_option( self::OPTION_CUSTOM, array() );
			if ( is_array( $custom ) && isset( $custom[ $code ] ) ) {
				unset( $custom[ $code ] );
				update_option( self::OPTION_CUSTOM, $custom, false );
			}
			$admin->redirect_module( 'languages', 'removed' );
			return;
		}

		if ( isset( $_POST['kayan_lang_action'] ) && 'toggle' === $_POST['kayan_lang_action'] ) {
			check_admin_referer( self::NONCE_SAVE, '_kayan_nonce' );
			$code = isset( $_POST['code'] ) ? sanitize_key( wp_unslash( $_POST['code'] ) ) : '';
			if ( ! $code || 'ar' === $code ) { // Arabic default must stay enabled.
				$admin->redirect_module( 'languages', 'error' );
				return;
			}
			$disabled = self::disabled_codes();
			if ( in_array( $code, $disabled, true ) ) {
				$disabled = array_values( array_diff( $disabled, array( $code ) ) );
			} else {
				$disabled[] = $code;
			}
			update_option( self::OPTION_ENABLED, array_values( array_unique( $disabled ) ), false );
			$admin->redirect_module( 'languages', 'updated' );
			return;
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
		$ui     = $context['ui'];
		$admin  = $context['admin'];
		$engine = kayan_platform()->languages;
		$all    = $engine->all();
		$custom = get_option( self::OPTION_CUSTOM, array() );

		echo $ui->notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'type'    => 'info',
				'message' => __( 'Arabic is the default and cannot be disabled. English is built in. Additional languages register through the existing kayan_platform_languages filter.', 'kayan' ),
			)
		);

		$rows = array();
		foreach ( $all as $code => $data ) {
			$is_builtin = in_array( $code, array( 'ar', 'en' ), true );
			$enabled    = empty( array_intersect( array( $code ), self::disabled_codes() ) );
			$toggle     = '';
			if ( 'ar' !== $code ) {
				$toggle = '<form method="post" action="' . esc_url( $admin->module_url( 'languages' ) ) . '" style="display:inline">'
					. wp_nonce_field( self::NONCE_SAVE, '_kayan_nonce', true, false )
					. '<input type="hidden" name="kayan_lang_action" value="toggle" />'
					. '<input type="hidden" name="code" value="' . esc_attr( $code ) . '" />'
					. '<button type="submit" class="button button-small">' . esc_html( $enabled ? __( 'Disable', 'kayan' ) : __( 'Enable', 'kayan' ) ) . '</button>'
					. '</form>';
			}
			$remove = '';
			if ( ! $is_builtin ) {
				$remove = ' <form method="post" action="' . esc_url( $admin->module_url( 'languages' ) ) . '" style="display:inline">'
					. wp_nonce_field( self::NONCE_REMOVE, '_kayan_nonce', true, false )
					. '<input type="hidden" name="kayan_lang_action" value="remove" />'
					. '<input type="hidden" name="code" value="' . esc_attr( $code ) . '" />'
					. '<button type="submit" class="button button-small button-link-delete">' . esc_html__( 'Remove', 'kayan' ) . '</button>'
					. '</form>';
			}
			$rows[] = array(
				'id'      => $code,
				'code'    => '<code>' . esc_html( $code ) . '</code>' . ( ! empty( $data['is_default'] ) ? ' ' . $ui->status( array( 'label' => __( 'Default', 'kayan' ), 'type' => 'success' ) ) : '' ),
				'label'   => esc_html( ( $data['label_en'] ?? $code ) . ' / ' . ( $data['label_ar'] ?? '' ) ),
				'dir'     => esc_html( strtoupper( (string) ( $data['dir'] ?? 'ltr' ) ) ),
				'status'  => $ui->status( array( 'label' => $enabled ? __( 'Enabled', 'kayan' ) : __( 'Disabled', 'kayan' ), 'type' => $enabled ? 'success' : 'neutral' ) ),
				'source'  => esc_html( $is_builtin ? __( 'Built-in', 'kayan' ) : __( 'Custom', 'kayan' ) ),
				'actions' => $toggle . $remove,
			);
		}

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'code'    => __( 'Code', 'kayan' ),
					'label'   => __( 'Label', 'kayan' ),
					'dir'     => __( 'Direction', 'kayan' ),
					'status'  => __( 'Status', 'kayan' ),
					'source'  => __( 'Source', 'kayan' ),
					'actions' => __( 'Actions', 'kayan' ),
				),
				'rows'    => $rows,
			)
		);

		ob_start();
		?>
		<form method="post" action="<?php echo esc_url( $admin->module_url( 'languages' ) ); ?>">
			<?php wp_nonce_field( self::NONCE_ADD, '_kayan_nonce' ); ?>
			<input type="hidden" name="kayan_lang_action" value="add" />
			<?php
			echo $ui->field( array( 'type' => 'text', 'name' => 'code', 'label' => __( 'Language code', 'kayan' ), 'placeholder' => 'fr' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'label_en', 'label' => __( 'Label (English)', 'kayan' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'label_ar', 'label' => __( 'Label (Arabic)', 'kayan' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field(
				array(
					'type'    => 'select',
					'name'    => 'dir',
					'label'   => __( 'Direction', 'kayan' ),
					'options' => array( 'ltr' => 'LTR', 'rtl' => 'RTL' ),
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'hreflang', 'label' => __( 'hreflang', 'kayan' ), 'placeholder' => 'fr' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<p class="submit">
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Add language', 'kayan' ); ?></button>
			</p>
		</form>
		<?php
		echo $ui->card( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'title'   => __( 'Register a new language', 'kayan' ),
				'content' => (string) ob_get_clean(),
			)
		);
		unset( $custom );
	}
}
