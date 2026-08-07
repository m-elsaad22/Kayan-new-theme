<?php
/**
 * Admin module: Countries.
 *
 * Reuses Kayan_Country_Engine (source: kayan_i18n_get_countries) and
 * Kayan_Country_Settings (kayan_country_profile_{code}). Does not
 * duplicate the i18n country registry — only edits the platform profile
 * layer (contact/business/SEO/currency) already used by Theme Integration.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Countries {

	const NONCE_ACTION = 'kayan_admin_countries_save';

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
		$registry->register_module(
			'countries',
			array(
				'label'       => __( 'Countries', 'kayan' ),
				'description' => __( 'Manage per-country business profile: phone, WhatsApp, currency, SEO defaults.', 'kayan' ),
				'icon'        => 'dashicons-admin-site-alt3',
				'position'    => 10,
				'capability'  => 'kayan_manage_countries',
				'group'       => 'locale',
				'screen'      => array( $this, 'screen' ),
				'save'        => array( $this, 'save' ),
			)
		);
	}

	/**
	 * @param array $module  Module.
	 * @param array $context Context.
	 * @return void
	 */
	public function save( $module, $context ) {
		unset( $module );
		if ( ! isset( $_POST['kayan_country_code'] ) ) {
			return;
		}
		check_admin_referer( self::NONCE_ACTION, '_kayan_nonce' );

		$country = sanitize_key( wp_unslash( $_POST['kayan_country_code'] ) );
		$engine  = kayan_platform()->countries;
		if ( ! $engine->exists( $country ) ) {
			$context['admin']->redirect_module( 'countries', 'error' );
			return;
		}

		$data = array(
			'phone'            => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
			'whatsapp'         => isset( $_POST['whatsapp'] ) ? sanitize_text_field( wp_unslash( $_POST['whatsapp'] ) ) : '',
			'email'            => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
			'business_name'    => isset( $_POST['business_name'] ) ? sanitize_text_field( wp_unslash( $_POST['business_name'] ) ) : '',
			'business_address' => isset( $_POST['business_address'] ) ? sanitize_text_field( wp_unslash( $_POST['business_address'] ) ) : '',
			'currency'         => isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : '',
			'seo'              => array(
				'title'       => isset( $_POST['seo_title'] ) ? sanitize_text_field( wp_unslash( $_POST['seo_title'] ) ) : '',
				'description' => isset( $_POST['seo_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['seo_description'] ) ) : '',
			),
			'analytics'        => array(
				'gtm_id' => isset( $_POST['gtm_id'] ) ? sanitize_text_field( wp_unslash( $_POST['gtm_id'] ) ) : '',
			),
		);

		kayan_platform()->settings->update_profile( $country, $data );

		if ( function_exists( 'kayan_cache' ) ) {
			kayan_cache()->flush_group( 'settings' );
		}
		if ( function_exists( 'kayan_logger' ) ) {
			kayan_logger()->info( 'general', 'admin.countries.updated', array( 'country' => $country ) );
		}

		$context['admin']->redirect_module( 'countries', 'updated', array( 'country' => $country ) );
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
		$engine   = kayan_platform()->countries;
		$settings = kayan_platform()->settings;
		$all      = $engine->all();
		$default  = $engine->get_default();
		$editing  = isset( $_GET['country'] ) ? sanitize_key( wp_unslash( $_GET['country'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $editing && $engine->exists( $editing ) ) {
			echo $this->render_editor( $ui, $admin, $engine, $settings, $editing, $default ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$rows = array();
		foreach ( $all as $code => $data ) {
			$profile = $settings->get_profile( $code );
			$rows[]  = array(
				'id'      => $code,
				'code'    => '<code>' . esc_html( $code ) . '</code>' . ( $code === $default ? ' ' . $ui->status( array( 'label' => __( 'Default', 'kayan' ), 'type' => 'success' ) ) : '' ),
				'label'   => esc_html( isset( $data['label_en'] ) ? $data['label_en'] : $code ),
				'path'    => '<code>' . esc_html( isset( $data['path'] ) ? ( '' === $data['path'] ? '/' : $data['path'] ) : '' ) . '</code>',
				'phone'   => esc_html( (string) ( $profile['phone'] ?? '' ) ),
				'currency'=> esc_html( (string) ( $profile['currency'] ?? '' ) ),
				'actions' => '<a class="button button-small" href="' . esc_url( add_query_arg( 'country', $code, $admin->module_url( 'countries' ) ) ) . '">' . esc_html__( 'Edit profile', 'kayan' ) . '</a>',
			);
		}

		echo $ui->notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'type'    => 'info',
				'message' => __( 'Countries come from the existing kayan-i18n registry. This screen edits the platform business profile only (phone, currency, SEO defaults) — it does not create or remove countries.', 'kayan' ),
			)
		);

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'code'     => __( 'Code', 'kayan' ),
					'label'    => __( 'Label', 'kayan' ),
					'path'     => __( 'URL path', 'kayan' ),
					'phone'    => __( 'Phone', 'kayan' ),
					'currency' => __( 'Currency', 'kayan' ),
					'actions'  => __( 'Actions', 'kayan' ),
				),
				'rows'    => $rows,
			)
		);
	}

	/**
	 * @param Kayan_Admin_UI         $ui       UI.
	 * @param Kayan_Admin_Platform   $admin    Admin.
	 * @param Kayan_Country_Engine   $engine   Engine.
	 * @param Kayan_Country_Settings $settings Country settings repository.
	 * @param string                 $code     Country.
	 * @param string                 $default  Default code.
	 * @return string
	 */
	private function render_editor( $ui, $admin, $engine, $settings, $code, $default ) {
		$profile = $settings->get_profile( $code );
		$data    = $engine->get( $code );

		ob_start();
		?>
		<p><a href="<?php echo esc_url( $admin->module_url( 'countries' ) ); ?>">&larr; <?php esc_html_e( 'Back to countries', 'kayan' ); ?></a></p>
		<form method="post" action="<?php echo esc_url( $admin->module_url( 'countries' ) ); ?>">
			<?php wp_nonce_field( self::NONCE_ACTION, '_kayan_nonce' ); ?>
			<input type="hidden" name="kayan_country_code" value="<?php echo esc_attr( $code ); ?>" />
			<h3>
				<?php echo esc_html( isset( $data['label_en'] ) ? $data['label_en'] : $code ); ?>
				<?php if ( $code === $default ) : ?>
					<?php echo $ui->status( array( 'label' => __( 'Default country', 'kayan' ), 'type' => 'success' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
			</h3>
			<?php
			echo $ui->field( array( 'type' => 'text', 'name' => 'phone', 'label' => __( 'Phone', 'kayan' ), 'value' => $profile['phone'], 'description' => __( 'Falls back to Theme Options phonenumber when empty.', 'kayan' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'whatsapp', 'label' => __( 'WhatsApp', 'kayan' ), 'value' => $profile['whatsapp'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'email', 'name' => 'email', 'label' => __( 'Email', 'kayan' ), 'value' => $profile['email'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'business_name', 'label' => __( 'Business name', 'kayan' ), 'value' => $profile['business_name'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'business_address', 'label' => __( 'Business address', 'kayan' ), 'value' => $profile['business_address'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'currency', 'label' => __( 'Currency', 'kayan' ), 'value' => $profile['currency'], 'placeholder' => 'AED', 'description' => __( 'Used by the Booking adapter when set.', 'kayan' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'seo_title', 'label' => __( 'Homepage SEO title', 'kayan' ), 'value' => $profile['seo']['title'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'textarea', 'name' => 'seo_description', 'label' => __( 'Homepage SEO description', 'kayan' ), 'value' => $profile['seo']['description'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'gtm_id', 'label' => __( 'GTM ID', 'kayan' ), 'value' => $profile['analytics']['gtm_id'] ?? '', 'placeholder' => 'GTM-XXXXXXX' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<p class="submit">
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Save country profile', 'kayan' ); ?></button>
			</p>
		</form>
		<?php
		return (string) ob_get_clean();
	}
}
