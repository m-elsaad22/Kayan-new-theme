<?php
/**
 * Admin module: AI Platform.
 *
 * Configure interchangeable AI providers (API key + model) via the
 * existing Settings Engine (module scope `ai_{provider}`) and choose the
 * default provider. Never duplicates a provider's own dashboard —
 * this is configuration only.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_AI {

	const NONCE_SAVE = 'kayan_admin_ai_save';

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
			'ai',
			array(
				'label'       => __( 'AI', 'kayan' ),
				'description' => __( 'Interchangeable AI providers for PSEO block regeneration and translation.', 'kayan' ),
				'icon'        => 'dashicons-superhero',
				'position'    => 50,
				'capability'  => 'kayan_manage_ai',
				'group'       => 'ai',
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
		if ( empty( $_POST['kayan_ai_save'] ) ) {
			return;
		}
		check_admin_referer( self::NONCE_SAVE, '_kayan_nonce' );

		$settings = kayan_settings();
		foreach ( array_keys( kayan_ai()->providers() ) as $id ) {
			if ( 'null' === $id ) {
				continue;
			}
			// The API key field is never pre-filled with the stored secret (see
			// render_settings_form()), so an empty submission means "leave the
			// existing key unchanged", not "clear it".
			if ( isset( $_POST[ 'api_key_' . $id ] ) && '' !== $_POST[ 'api_key_' . $id ] ) {
				$settings->set_module( 'ai_' . $id, 'api_key', sanitize_text_field( wp_unslash( $_POST[ 'api_key_' . $id ] ) ) );
			}
			$settings->set_module( 'ai_' . $id, 'model', isset( $_POST[ 'model_' . $id ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'model_' . $id ] ) ) : '' );
		}
		$settings->set_global( 'ai_default_provider', isset( $_POST['default_provider'] ) ? sanitize_key( wp_unslash( $_POST['default_provider'] ) ) : '' );

		$context['admin']->redirect_module( 'ai', 'updated' );
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
		$ai       = kayan_ai();
		$status   = $ai->status();

		$rows = array();
		foreach ( $status as $id => $info ) {
			$rows[] = array(
				'id'           => $id,
				'provider'     => esc_html( $info['label'] ),
				'available'    => $ui->status( array( 'label' => $info['available'] ? __( 'Configured', 'kayan' ) : __( 'Not configured', 'kayan' ), 'type' => $info['available'] ? 'success' : 'neutral' ) ),
				'capabilities' => esc_html( implode( ', ', $info['capabilities'] ) ),
				'default'      => $ai->default_provider_id() === $id ? $ui->status( array( 'label' => __( 'Default', 'kayan' ), 'type' => 'info' ) ) : '',
			);
		}

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'provider'     => __( 'Provider', 'kayan' ),
					'available'    => __( 'Status', 'kayan' ),
					'capabilities' => __( 'Capabilities', 'kayan' ),
					'default'      => '',
				),
				'rows'    => $rows,
			)
		);

		echo $ui->notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'type'    => 'info',
				'message' => __( 'Application code never talks to a provider directly — PSEO block regeneration and translation always go through this registry, so switching providers here never requires a code change.', 'kayan' ),
			)
		);

		echo $this->render_settings_form( $ui, $admin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * @param Kayan_Admin_UI       $ui    UI.
	 * @param Kayan_Admin_Platform $admin Admin.
	 * @return string
	 */
	private function render_settings_form( $ui, $admin ) {
		$settings = kayan_settings();
		$providers = kayan_ai()->providers();
		$default  = kayan_settings()->get_global( 'ai_default_provider', '' );

		ob_start();
		?>
		<form method="post" action="<?php echo esc_url( $admin->module_url( 'ai' ) ); ?>">
			<?php wp_nonce_field( self::NONCE_SAVE, '_kayan_nonce' ); ?>
			<input type="hidden" name="kayan_ai_save" value="1" />
			<?php
			$options = array( '' => __( 'Auto (first available)', 'kayan' ) );
			foreach ( $providers as $id => $provider ) {
				if ( 'null' === $id ) {
					continue;
				}
				$options[ $id ] = $provider->label();
			}
			echo $ui->field( array( 'type' => 'select', 'name' => 'default_provider', 'label' => __( 'Default provider', 'kayan' ), 'value' => $default, 'options' => $options ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<?php foreach ( $providers as $id => $provider ) : ?>
				<?php if ( 'null' === $id ) { continue; } ?>
				<h3><?php echo esc_html( $provider->label() ); ?></h3>
				<?php
				$has_key = '' !== (string) $settings->get_module( 'ai_' . $id, 'api_key', '' );
				echo $ui->field( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'type'        => 'password',
						'name'        => 'api_key_' . $id,
						'label'       => __( 'API key', 'kayan' ),
						// Never echo the stored secret back into the page (it would be
						// readable in the rendered HTML/DOM). Leave blank to keep it.
						'value'       => '',
						'placeholder' => $has_key ? __( '•••••••• (leave blank to keep current key)', 'kayan' ) : __( 'Not set', 'kayan' ),
					)
				);
				echo $ui->field( array( 'type' => 'text', 'name' => 'model_' . $id, 'label' => __( 'Model (optional)', 'kayan' ), 'value' => (string) $settings->get_module( 'ai_' . $id, 'model', '' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			<?php endforeach; ?>
			<p class="submit">
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Save AI settings', 'kayan' ); ?></button>
			</p>
		</form>
		<?php
		return $ui->card(
			array(
				'title'   => __( 'Provider configuration', 'kayan' ),
				'content' => (string) ob_get_clean(),
			)
		);
	}
}
