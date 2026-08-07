<?php
/**
 * Admin module: Entities.
 *
 * Read-only inspector over the existing Entity Relationship Engine
 * (Kayan_Entity_API). No second entity registry.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Entities {

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
			'entities',
			array(
				'label'       => __( 'Entities', 'kayan' ),
				'description' => __( 'Inspect registered entity types and resolve a single entity.', 'kayan' ),
				'icon'        => 'dashicons-networking',
				'position'    => 20,
				'capability'  => 'kayan_manage_entities',
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
		$ui     = $context['ui'];
		$engine = kayan_entity();
		$types  = $engine->api->types();

		$rows = array();
		foreach ( $types as $id => $def ) {
			$count = $this->count_for_type( $def );
			$rows[] = array(
				'id'        => $id,
				'type'      => '<code>' . esc_html( $id ) . '</code>',
				'label'     => esc_html( (string) ( $def['label'] ?? $id ) ),
				'kind'      => esc_html( (string) ( $def['kind'] ?? '' ) ),
				'source'    => esc_html( (string) ( $def['post_type'] ?: ( $def['taxonomy'] ?: '' ) ) ),
				'fields'    => esc_html( implode( ', ', (array) ( $def['fields'] ?? array() ) ) ),
				'count'     => is_null( $count ) ? '—' : esc_html( (string) $count ),
			);
		}

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'type'   => __( 'Type', 'kayan' ),
					'label'  => __( 'Label', 'kayan' ),
					'kind'   => __( 'Kind', 'kayan' ),
					'source' => __( 'Source', 'kayan' ),
					'fields' => __( 'Fields', 'kayan' ),
					'count'  => __( 'Count', 'kayan' ),
				),
				'rows'    => $rows,
			)
		);

		$lookup_type = isset( $_GET['entity_type'] ) ? sanitize_key( wp_unslash( $_GET['entity_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$lookup_ref  = isset( $_GET['entity_ref'] ) ? sanitize_text_field( wp_unslash( $_GET['entity_ref'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$result_html = '';

		if ( $lookup_type && '' !== $lookup_ref ) {
			$dto = $engine->get( $lookup_type, $lookup_ref );
			$result_html = $dto
				? '<pre class="kayan-admin-json">' . esc_html( wp_json_encode( $dto, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) ) . '</pre>'
				: '<p class="description">' . esc_html__( 'No entity resolved for that type/ref.', 'kayan' ) . '</p>';
		}

		ob_start();
		?>
		<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
			<input type="hidden" name="page" value="kayan-platform-entities" />
			<?php
			echo $ui->field(
				array(
					'type'    => 'select',
					'name'    => 'entity_type',
					'label'   => __( 'Entity type', 'kayan' ),
					'value'   => $lookup_type,
					'options' => array_combine( array_keys( $types ), array_keys( $types ) ),
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'entity_ref', 'label' => __( 'Ref (ID / slug / country code)', 'kayan' ), 'value' => $lookup_ref ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Resolve entity', 'kayan' ); ?></button></p>
		</form>
		<?php echo $result_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php
		echo $ui->card( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'title'   => __( 'Entity inspector', 'kayan' ),
				'content' => (string) ob_get_clean(),
			)
		);
	}

	/**
	 * @param array $def Type def.
	 * @return int|null
	 */
	private function count_for_type( array $def ) {
		if ( ! empty( $def['post_type'] ) && post_type_exists( $def['post_type'] ) ) {
			$counts = wp_count_posts( $def['post_type'] );
			return isset( $counts->publish ) ? (int) $counts->publish : 0;
		}
		if ( ! empty( $def['taxonomy'] ) && taxonomy_exists( $def['taxonomy'] ) ) {
			$count = wp_count_terms( array( 'taxonomy' => $def['taxonomy'], 'hide_empty' => false ) );
			return is_numeric( $count ) ? (int) $count : 0;
		}
		if ( 'country' === ( $def['kind'] ?? '' ) ) {
			return count( kayan_platform()->countries->all() );
		}
		return null;
	}
}
