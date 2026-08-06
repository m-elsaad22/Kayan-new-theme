<?php
/**
 * Admin module: Blueprints (Blueprint Engine).
 *
 * Lists PSEO-managed posts (posts carrying a `kayan_pseo_blueprint` meta
 * value, across the existing host post types) and lets an operator trigger
 * a content-only Regenerate. Uses the Query Engine's sanctioned wp_query()
 * escape hatch — never a second query layer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Blueprints {

	const NONCE_REGEN = 'kayan_admin_blueprints_regen';

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
			'blueprints',
			array(
				'label'       => __( 'Blueprints', 'kayan' ),
				'description' => __( 'Versioned, block-based page contracts for generated content.', 'kayan' ),
				'icon'        => 'dashicons-media-code',
				'position'    => 35,
				'capability'  => 'kayan_manage_blueprints',
				'group'       => 'pseo',
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
		if ( empty( $_POST['kayan_bp_action'] ) ) {
			return;
		}
		check_admin_referer( self::NONCE_REGEN, '_kayan_nonce' );

		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		if ( ! $post_id ) {
			$context['admin']->redirect_module( 'blueprints', 'error' );
			return;
		}

		$result = kayan_pseo()->generator->regenerate( $post_id, array( 'mode' => 'content_only' ) );

		$context['admin']->redirect_module( 'blueprints', ! empty( $result['ok'] ) ? 'updated' : 'error' );
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
		$posts = $this->query_blueprint_posts();

		$rows = array();
		foreach ( $posts as $post ) {
			$blueprint = get_post_meta( $post->ID, Kayan_PSEO_Blueprint::META_BLUEPRINT, true );
			$pattern   = get_post_meta( $post->ID, Kayan_PSEO_Identity::META_PATTERN, true );
			$locked    = isset( $blueprint['locks'] ) ? count( (array) $blueprint['locks'] ) : 0;

			ob_start();
			?>
			<form method="post" action="<?php echo esc_url( $admin->module_url( 'blueprints' ) ); ?>" style="display:inline">
				<?php wp_nonce_field( self::NONCE_REGEN, '_kayan_nonce' ); ?>
				<input type="hidden" name="kayan_bp_action" value="regenerate" />
				<input type="hidden" name="post_id" value="<?php echo esc_attr( $post->ID ); ?>" />
				<button type="submit" class="button button-small"><?php esc_html_e( 'Regenerate', 'kayan' ); ?></button>
			</form>
			<?php
			$actions = (string) ob_get_clean();
			$actions .= ' <a class="button button-small" href="' . esc_url( get_edit_post_link( $post->ID ) ) . '">' . esc_html__( 'Edit', 'kayan' ) . '</a>';

			$rows[] = array(
				'id'       => $post->ID,
				'title'    => esc_html( get_the_title( $post ) ),
				'type'     => '<code>' . esc_html( $post->post_type ) . '</code>',
				'pattern'  => esc_html( (string) $pattern ),
				'version'  => esc_html( (string) ( $blueprint['blueprint_version'] ?? 1 ) ),
				'locked'   => esc_html( (string) $locked ),
				'status'   => $ui->status( array( 'label' => ucfirst( $post->post_status ), 'type' => 'publish' === $post->post_status ? 'success' : 'neutral' ) ),
				'actions'  => $actions,
			);
		}

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'title'   => __( 'Title', 'kayan' ),
					'type'    => __( 'Post type', 'kayan' ),
					'pattern' => __( 'Pattern', 'kayan' ),
					'version' => __( 'Blueprint v', 'kayan' ),
					'locked'  => __( 'Locked blocks', 'kayan' ),
					'status'  => __( 'Status', 'kayan' ),
					'actions' => __( 'Actions', 'kayan' ),
				),
				'rows'    => $rows,
				'empty'   => __( 'No generated pages yet. Use the Programmatic SEO module to create a rule and generate content.', 'kayan' ),
			)
		);
	}

	/**
	 * @return WP_Post[]
	 */
	private function query_blueprint_posts() {
		if ( ! function_exists( 'kayan_query' ) ) {
			return array();
		}
		$q = kayan_query()->wp_query(
			array(
				'post_type'      => kayan_pseo()->storage->host_post_types(),
				'post_status'    => array( 'publish', 'draft', 'future', 'pending' ),
				'posts_per_page' => 50,
				'meta_key'       => Kayan_PSEO_Blueprint::META_BLUEPRINT, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'orderby'        => 'date',
				'order'          => 'DESC',
				'no_found_rows'  => true,
			)
		);
		return $q->posts;
	}
}
