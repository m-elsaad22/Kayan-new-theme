<?php
/**
 * Admin module: Blueprints (Blueprint Engine).
 *
 * Lists PSEO-managed posts (posts carrying a `kayan_pseo_blueprint` meta
 * value, across the existing host post types). Surfaces the Content
 * Workflow state, a Quality Engine score, and actions to transition state,
 * regenerate, or translate — all delegating to the existing engines.
 * Uses the Query Engine's sanctioned wp_query() escape hatch — never a
 * second query layer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Blueprints {

	const NONCE_REGEN      = 'kayan_admin_blueprints_regen';
	const NONCE_TRANSITION = 'kayan_admin_blueprints_transition';
	const NONCE_TRANSLATE  = 'kayan_admin_blueprints_translate';

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
				'description' => __( 'Versioned, block-based page contracts, workflow state, and quality score for generated content.', 'kayan' ),
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
		$admin   = $context['admin'];
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		if ( isset( $_POST['kayan_bp_action'] ) && 'regenerate' === $_POST['kayan_bp_action'] ) {
			check_admin_referer( self::NONCE_REGEN, '_kayan_nonce' );
			if ( ! $post_id ) {
				$admin->redirect_module( 'blueprints', 'error' );
				return;
			}
			$confirm = ! empty( $_POST['confirm'] );
			$result  = kayan_pseo()->generator->regenerate( $post_id, array( 'mode' => 'content_only', 'confirm' => $confirm ) );
			$admin->redirect_module( 'blueprints', ! empty( $result['ok'] ) ? 'updated' : 'error' );
			return;
		}

		if ( isset( $_POST['kayan_bp_action'] ) && 'transition' === $_POST['kayan_bp_action'] ) {
			check_admin_referer( self::NONCE_TRANSITION, '_kayan_nonce' );
			$to = isset( $_POST['state'] ) ? sanitize_key( wp_unslash( $_POST['state'] ) ) : '';
			if ( ! $post_id || ! $to ) {
				$admin->redirect_module( 'blueprints', 'error' );
				return;
			}
			$result = kayan_workflow()->transition( $post_id, $to );
			$admin->redirect_module( 'blueprints', ! empty( $result['ok'] ) ? 'updated' : 'error' );
			return;
		}

		if ( isset( $_POST['kayan_bp_action'] ) && 'translate' === $_POST['kayan_bp_action'] ) {
			check_admin_referer( self::NONCE_TRANSLATE, '_kayan_nonce' );
			$lang = isset( $_POST['target_language'] ) ? sanitize_key( wp_unslash( $_POST['target_language'] ) ) : '';
			if ( ! $post_id || ! $lang ) {
				$admin->redirect_module( 'blueprints', 'error' );
				return;
			}
			$result = kayan_pseo()->translate_bulk( array( $post_id ), $lang, array( 'post_status' => 'draft' ) );
			$admin->redirect_module( 'blueprints', ! empty( $result['ok'] ) ? 'updated' : 'error' );
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
		$ui    = $context['ui'];
		$admin = $context['admin'];
		$posts = $this->query_blueprint_posts();

		$rows = array();
		foreach ( $posts as $post ) {
			$blueprint = get_post_meta( $post->ID, Kayan_PSEO_Blueprint::META_BLUEPRINT, true );
			$pattern   = get_post_meta( $post->ID, Kayan_PSEO_Identity::META_PATTERN, true );
			$locked    = isset( $blueprint['locks'] ) ? count( (array) $blueprint['locks'] ) : 0;
			$state     = kayan_workflow()->get_state( $post->ID );
			$quality   = kayan_quality()->validate( $post->ID );

			$rows[] = array(
				'id'       => $post->ID,
				'title'    => esc_html( get_the_title( $post ) ) . ( get_post_meta( $post->ID, 'kayan_pseo_manual_override', true ) ? ' ' . $ui->status( array( 'label' => __( 'Protected', 'kayan' ), 'type' => 'warning' ) ) : '' ),
				'type'     => '<code>' . esc_html( $post->post_type ) . '</code>',
				'pattern'  => esc_html( (string) $pattern ),
				'version'  => esc_html( (string) ( $blueprint['blueprint_version'] ?? 1 ) ),
				'locked'   => esc_html( (string) $locked ),
				'workflow' => $ui->status( array( 'label' => kayan_workflow()->states()[ $state ] ?? $state, 'type' => $this->state_badge_type( $state ) ) ),
				'quality'  => $ui->status( array( 'label' => round( $quality['score'] * 100 ) . '%', 'type' => $quality['ok'] ? 'success' : ( empty( $quality['blockers'] ) ? 'warning' : 'error' ) ) ),
				'actions'  => $this->render_row_actions( $admin, $post, $state ),
			);
		}

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'title'    => __( 'Title', 'kayan' ),
					'type'     => __( 'Post type', 'kayan' ),
					'pattern'  => __( 'Pattern', 'kayan' ),
					'version'  => __( 'Blueprint v', 'kayan' ),
					'locked'   => __( 'Locked blocks', 'kayan' ),
					'workflow' => __( 'Workflow', 'kayan' ),
					'quality'  => __( 'Quality', 'kayan' ),
					'actions'  => __( 'Actions', 'kayan' ),
				),
				'rows'    => $rows,
				'empty'   => __( 'No generated pages yet. Use the Programmatic SEO module to create a rule and generate content.', 'kayan' ),
			)
		);

		$detail_id = isset( $_GET['quality_for'] ) ? absint( wp_unslash( $_GET['quality_for'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $detail_id ) {
			echo $this->render_quality_detail( $ui, $detail_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * @param Kayan_Admin_Platform $admin Admin.
	 * @param WP_Post              $post  Post.
	 * @param string               $state Workflow state.
	 * @return string
	 */
	private function render_row_actions( $admin, $post, $state ) {
		ob_start();
		?>
		<a class="button button-small" href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>"><?php esc_html_e( 'Edit', 'kayan' ); ?></a>
		<a class="button button-small" href="<?php echo esc_url( add_query_arg( 'quality_for', $post->ID, $admin->module_url( 'blueprints' ) ) ); ?>"><?php esc_html_e( 'Quality report', 'kayan' ); ?></a>

		<form method="post" action="<?php echo esc_url( $admin->module_url( 'blueprints' ) ); ?>" style="display:inline">
			<?php wp_nonce_field( self::NONCE_REGEN, '_kayan_nonce' ); ?>
			<input type="hidden" name="kayan_bp_action" value="regenerate" />
			<input type="hidden" name="post_id" value="<?php echo esc_attr( $post->ID ); ?>" />
			<?php if ( in_array( $state, array( Kayan_Content_Workflow::APPROVED, Kayan_Content_Workflow::PUBLISHED ), true ) ) : ?>
				<input type="hidden" name="confirm" value="1" />
				<button type="submit" class="button button-small" onclick="return confirm('<?php echo esc_js( __( 'This page is approved/published — regenerate its derived content anyway?', 'kayan' ) ); ?>');"><?php esc_html_e( 'Regenerate', 'kayan' ); ?></button>
			<?php else : ?>
				<button type="submit" class="button button-small"><?php esc_html_e( 'Regenerate', 'kayan' ); ?></button>
			<?php endif; ?>
		</form>

		<?php foreach ( $this->quick_transitions( $state ) as $to => $label ) : ?>
			<form method="post" action="<?php echo esc_url( $admin->module_url( 'blueprints' ) ); ?>" style="display:inline">
				<?php wp_nonce_field( self::NONCE_TRANSITION, '_kayan_nonce' ); ?>
				<input type="hidden" name="kayan_bp_action" value="transition" />
				<input type="hidden" name="post_id" value="<?php echo esc_attr( $post->ID ); ?>" />
				<input type="hidden" name="state" value="<?php echo esc_attr( $to ); ?>" />
				<button type="submit" class="button button-small"><?php echo esc_html( $label ); ?></button>
			</form>
		<?php endforeach; ?>

		<?php
		$languages = function_exists( 'kayan_platform' ) ? kayan_platform()->languages->all() : array();
		$source_lang = (string) get_post_meta( $post->ID, Kayan_Content_Locale::META_LANG, true );
		$targets   = array_diff( array_keys( $languages ), array( $source_lang ?: 'ar' ) );
		if ( $targets && function_exists( 'kayan_ai' ) && kayan_ai()->is_any_available() ) :
			?>
			<form method="post" action="<?php echo esc_url( $admin->module_url( 'blueprints' ) ); ?>" style="display:inline">
				<?php wp_nonce_field( self::NONCE_TRANSLATE, '_kayan_nonce' ); ?>
				<input type="hidden" name="kayan_bp_action" value="translate" />
				<input type="hidden" name="post_id" value="<?php echo esc_attr( $post->ID ); ?>" />
				<select name="target_language">
					<?php foreach ( $targets as $code ) : ?>
						<option value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $languages[ $code ]['label_en'] ?? $code ); ?></option>
					<?php endforeach; ?>
				</select>
				<button type="submit" class="button button-small"><?php esc_html_e( 'Translate', 'kayan' ); ?></button>
			</form>
		<?php endif; ?>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param string $state Current state.
	 * @return array<string,string> target_state => button label
	 */
	private function quick_transitions( $state ) {
		$map = array(
			Kayan_Content_Workflow::DRAFT              => array( Kayan_Content_Workflow::HUMAN_REVIEW => __( 'Send to Review', 'kayan' ) ),
			Kayan_Content_Workflow::AI_DRAFT            => array( Kayan_Content_Workflow::HUMAN_REVIEW => __( 'Send to Review', 'kayan' ) ),
			Kayan_Content_Workflow::HUMAN_REVIEW        => array( Kayan_Content_Workflow::APPROVED => __( 'Approve', 'kayan' ) ),
			Kayan_Content_Workflow::APPROVED            => array( Kayan_Content_Workflow::PUBLISHED => __( 'Publish', 'kayan' ) ),
			Kayan_Content_Workflow::NEEDS_UPDATE        => array( Kayan_Content_Workflow::HUMAN_REVIEW => __( 'Send to Review', 'kayan' ) ),
			Kayan_Content_Workflow::NEEDS_REGENERATION  => array( Kayan_Content_Workflow::HUMAN_REVIEW => __( 'Send to Review', 'kayan' ) ),
			Kayan_Content_Workflow::PUBLISHED           => array( Kayan_Content_Workflow::ARCHIVED => __( 'Archive', 'kayan' ) ),
			Kayan_Content_Workflow::FAILED              => array( Kayan_Content_Workflow::DRAFT => __( 'Reset to Draft', 'kayan' ) ),
			Kayan_Content_Workflow::ARCHIVED             => array( Kayan_Content_Workflow::DRAFT => __( 'Restore', 'kayan' ) ),
		);
		return $map[ $state ] ?? array();
	}

	/**
	 * @param string $state State.
	 * @return string
	 */
	private function state_badge_type( $state ) {
		$types = array(
			Kayan_Content_Workflow::PUBLISHED          => 'success',
			Kayan_Content_Workflow::APPROVED           => 'success',
			Kayan_Content_Workflow::SCHEDULED          => 'info',
			Kayan_Content_Workflow::HUMAN_REVIEW       => 'warning',
			Kayan_Content_Workflow::NEEDS_UPDATE       => 'warning',
			Kayan_Content_Workflow::NEEDS_REGENERATION => 'warning',
			Kayan_Content_Workflow::FAILED             => 'error',
			Kayan_Content_Workflow::ARCHIVED           => 'neutral',
		);
		return $types[ $state ] ?? 'neutral';
	}

	/**
	 * @param Kayan_Admin_UI $ui      UI.
	 * @param int            $post_id Post ID.
	 * @return string
	 */
	private function render_quality_detail( $ui, $post_id ) {
		$result = kayan_quality()->validate( $post_id );
		$rows   = array();
		foreach ( $result['checks'] as $check ) {
			$type = self::pass_type( $check['status'] );
			$rows[] = array(
				'id'      => $check['id'],
				'check'   => esc_html( $check['label'] ),
				'status'  => $ui->status( array( 'label' => strtoupper( $check['status'] ), 'type' => $type ) ),
				'message' => esc_html( $check['message'] ),
			);
		}
		$table = $ui->table(
			array(
				'columns' => array( 'check' => __( 'Check', 'kayan' ), 'status' => __( 'Status', 'kayan' ), 'message' => __( 'Details', 'kayan' ) ),
				'rows'    => $rows,
			)
		);
		return $ui->card(
			array(
				'title'   => sprintf( /* translators: %d: post id */ __( 'Quality report — post #%d', 'kayan' ), $post_id ),
				'status'  => round( $result['score'] * 100 ) . '%',
				'content' => $table,
			)
		);
	}

	/**
	 * @param string $status pass|warn|fail.
	 * @return string
	 */
	private static function pass_type( $status ) {
		if ( 'pass' === $status ) {
			return 'success';
		}
		return 'fail' === $status ? 'error' : 'warning';
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
				'post_status'    => array( 'publish', 'draft', 'future', 'pending', 'private' ),
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
