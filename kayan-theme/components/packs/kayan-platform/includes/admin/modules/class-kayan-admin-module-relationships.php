<?php
/**
 * Admin module: Relationships.
 *
 * Read-only view over the existing Entity Relationship Engine matrix and
 * a small browser to inspect related entities for a given from-type/ref.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Relationships {

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
			'relationships',
			array(
				'label'       => __( 'Relationships', 'kayan' ),
				'description' => __( 'Allowed relationship matrix + related-entity browser.', 'kayan' ),
				'icon'        => 'dashicons-share',
				'position'    => 25,
				'capability'  => 'kayan_manage_relationships',
				'group'       => 'content',
				'screen'      => array( $this, 'screen' ),
			)
		);
	}

	/**
	 * @param array $module  Module.
	 * @param array $context Context.
	 * @return void
	 */
	public function screen( $module, $context ) {
		unset( $module );
		/** @var Kayan_Admin_UI $ui */
		$ui      = $context['ui'];
		$engine  = kayan_entity()->relationships;
		$matrix  = $engine->allowed_matrix();
		$caps    = $engine->capabilities();

		$rows = array();
		foreach ( $matrix as $from => $to_types ) {
			$rows[] = array(
				'id'   => $from,
				'from' => '<code>' . esc_html( $from ) . '</code>',
				'to'   => implode( ', ', array_map( static function ( $t ) {
					return '<code>' . esc_html( $t ) . '</code>';
				}, (array) $to_types ) ),
			);
		}

		echo $ui->card( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'title'   => __( 'Engine contract', 'kayan' ),
				'content' => '<p>' . sprintf(
					/* translators: 1: meta key, 2: bidirectional list */
					esc_html__( 'Storage meta key: %1$s. Bidirectional relations: %2$s.', 'kayan' ),
					'<code>' . esc_html( $caps['meta_key'] ) . '</code>',
					esc_html( implode( ', ', (array) $caps['bidirectional'] ) )
				) . '</p>',
			)
		);

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'from' => __( 'From type', 'kayan' ),
					'to'   => __( 'Allowed to types', 'kayan' ),
				),
				'rows'    => $rows,
			)
		);

		$this->render_browser( $ui );
	}

	/**
	 * @param Kayan_Admin_UI $ui UI.
	 * @return void
	 */
	private function render_browser( $ui ) {
		$type = isset( $_GET['rel_type'] ) ? sanitize_key( wp_unslash( $_GET['rel_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$ref  = isset( $_GET['rel_ref'] ) ? sanitize_text_field( wp_unslash( $_GET['rel_ref'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$to   = isset( $_GET['rel_to'] ) ? sanitize_key( wp_unslash( $_GET['rel_to'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$result = '';
		if ( $type && '' !== $ref ) {
			$related = kayan_entity()->related( $type, $ref, $to ?: null );
			$result  = $related
				? '<pre class="kayan-admin-json">' . esc_html( wp_json_encode( $related, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) ) . '</pre>'
				: '<p class="description">' . esc_html__( 'No related entities found.', 'kayan' ) . '</p>';
		}

		ob_start();
		?>
		<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
			<input type="hidden" name="page" value="kayan-platform-relationships" />
			<?php
			echo $ui->field( array( 'type' => 'text', 'name' => 'rel_type', 'label' => __( 'From type', 'kayan' ), 'value' => $type, 'placeholder' => 'city' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'rel_ref', 'label' => __( 'From ref', 'kayan' ), 'value' => $ref ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'rel_to', 'label' => __( 'To type (optional)', 'kayan' ), 'value' => $to ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Browse related', 'kayan' ); ?></button></p>
		</form>
		<?php echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php
		echo $ui->card( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'title'   => __( 'Related entity browser', 'kayan' ),
				'content' => (string) ob_get_clean(),
			)
		);
	}
}
