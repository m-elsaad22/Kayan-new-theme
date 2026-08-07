<?php
/**
 * Admin module: Programmatic SEO — Rules control center.
 *
 * Create/edit generation Rules (existing Kayan_PSEO_Rules storage),
 * preview combination counts, and trigger Bulk Generation — which enqueues
 * one Queue job that the Scheduler processes automatically.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Pseo {

	const NONCE_SAVE = 'kayan_admin_pseo_rule_save';
	const NONCE_RUN  = 'kayan_admin_pseo_rule_run';

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
			'pseo',
			array(
				'label'       => __( 'Programmatic SEO', 'kayan' ),
				'description' => __( 'Generation rules, preview, bulk generation, and regeneration.', 'kayan' ),
				'icon'        => 'dashicons-chart-area',
				'position'    => 45,
				'capability'  => 'kayan_manage_pseo',
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
		$admin = $context['admin'];

		if ( isset( $_POST['kayan_pseo_action'] ) && 'save_rule' === $_POST['kayan_pseo_action'] ) {
			check_admin_referer( self::NONCE_SAVE, '_kayan_nonce' );
			$rule = array(
				'id'         => isset( $_POST['id'] ) ? sanitize_key( wp_unslash( $_POST['id'] ) ) : '',
				'label'      => isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '',
				'pattern_id' => isset( $_POST['pattern_id'] ) ? sanitize_key( wp_unslash( $_POST['pattern_id'] ) ) : '',
				'filters'    => array(
					'countries' => $this->csv( $_POST['countries'] ?? '' ),
					'cities'    => $this->csv( $_POST['cities'] ?? '' ),
					'services'  => $this->csv( $_POST['services'] ?? '' ),
					'languages' => $this->csv( $_POST['languages'] ?? 'ar' ),
				),
				'output'     => array(
					'post_status' => isset( $_POST['post_status'] ) ? sanitize_key( wp_unslash( $_POST['post_status'] ) ) : 'draft',
					'ai_enabled'  => ! empty( $_POST['ai_enabled'] ),
				),
			);
			$result = kayan_pseo()->rules->save( $rule );
			$admin->redirect_module( 'pseo', ! empty( $result['ok'] ) ? 'updated' : 'error' );
			return;
		}

		if ( isset( $_POST['kayan_pseo_action'] ) && 'delete_rule' === $_POST['kayan_pseo_action'] ) {
			check_admin_referer( self::NONCE_RUN, '_kayan_nonce' );
			$id = isset( $_POST['rule_id'] ) ? sanitize_key( wp_unslash( $_POST['rule_id'] ) ) : '';
			kayan_pseo()->rules->delete( $id );
			$admin->redirect_module( 'pseo', 'removed' );
			return;
		}

		if ( isset( $_POST['kayan_pseo_action'] ) && 'bulk_generate' === $_POST['kayan_pseo_action'] ) {
			check_admin_referer( self::NONCE_RUN, '_kayan_nonce' );
			$id     = isset( $_POST['rule_id'] ) ? sanitize_key( wp_unslash( $_POST['rule_id'] ) ) : '';
			$rule   = kayan_pseo()->rules->get( $id );
			$result = kayan_pseo()->bulk_generate(
				$id,
				array(
					'post_status' => $rule['output']['post_status'] ?? 'draft',
					'ai_enabled'  => ! empty( $rule['output']['ai_enabled'] ),
				)
			);
			$admin->redirect_module( 'pseo', ! empty( $result['ok'] ) ? 'updated' : 'error', array( 'rule' => $id ) );
			return;
		}
	}

	/**
	 * @param string $csv Comma separated values.
	 * @return string[]
	 */
	private function csv( $csv ) {
		$csv = sanitize_text_field( wp_unslash( (string) $csv ) );
		return array_values( array_filter( array_map( 'trim', explode( ',', $csv ) ) ) );
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
		$admin   = $context['admin'];
		$pseo    = kayan_pseo();
		$rules   = $pseo->rules->all();
		$patterns = $pseo->patterns->all();

		$editing = isset( $_GET['rule'] ) ? sanitize_key( wp_unslash( $_GET['rule'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$rule    = $editing ? $pseo->rules->get( $editing ) : null;

		$rows = array();
		foreach ( $rules as $r ) {
			$preview_count = '';
			if ( isset( $_GET['preview'] ) && sanitize_key( wp_unslash( $_GET['preview'] ) ) === $r['id'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$expansion     = $pseo->rules->preview_combinations( $r['id'] );
				$preview_count = ! empty( $expansion['ok'] ) ? (string) $expansion['count'] . ( ! empty( $expansion['truncated'] ) ? '+' : '' ) : '—';
			}

			ob_start();
			?>
			<a class="button button-small" href="<?php echo esc_url( add_query_arg( array( 'rule' => $r['id'] ), $admin->module_url( 'pseo' ) ) ); ?>"><?php esc_html_e( 'Edit', 'kayan' ); ?></a>
			<a class="button button-small" href="<?php echo esc_url( add_query_arg( array( 'preview' => $r['id'] ), $admin->module_url( 'pseo' ) ) ); ?>"><?php esc_html_e( 'Preview', 'kayan' ); ?></a>
			<form method="post" action="<?php echo esc_url( $admin->module_url( 'pseo' ) ); ?>" style="display:inline" onsubmit="return confirm('<?php echo esc_js( __( 'Generate all matching combinations now?', 'kayan' ) ); ?>');">
				<?php wp_nonce_field( self::NONCE_RUN, '_kayan_nonce' ); ?>
				<input type="hidden" name="kayan_pseo_action" value="bulk_generate" />
				<input type="hidden" name="rule_id" value="<?php echo esc_attr( $r['id'] ); ?>" />
				<button type="submit" class="button button-small button-primary"><?php esc_html_e( 'Bulk Generate', 'kayan' ); ?></button>
			</form>
			<form method="post" action="<?php echo esc_url( $admin->module_url( 'pseo' ) ); ?>" style="display:inline">
				<?php wp_nonce_field( self::NONCE_RUN, '_kayan_nonce' ); ?>
				<input type="hidden" name="kayan_pseo_action" value="delete_rule" />
				<input type="hidden" name="rule_id" value="<?php echo esc_attr( $r['id'] ); ?>" />
				<button type="submit" class="button button-small button-link-delete"><?php esc_html_e( 'Delete', 'kayan' ); ?></button>
			</form>
			<?php
			$rows[] = array(
				'id'       => $r['id'],
				'label'    => esc_html( (string) $r['label'] ),
				'pattern'  => esc_html( isset( $patterns[ $r['pattern_id'] ] ) ? $patterns[ $r['pattern_id'] ]['label'] : $r['pattern_id'] ),
				'status'   => esc_html( (string) $r['output']['post_status'] ),
				'preview'  => $preview_count ? esc_html( $preview_count ) : '—',
				'actions'  => (string) ob_get_clean(),
			);
		}

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'label'   => __( 'Rule', 'kayan' ),
					'pattern' => __( 'Pattern', 'kayan' ),
					'status'  => __( 'Output status', 'kayan' ),
					'preview' => __( 'Combinations', 'kayan' ),
					'actions' => __( 'Actions', 'kayan' ),
				),
				'rows'    => $rows,
				'empty'   => __( 'No rules yet — create one below.', 'kayan' ),
			)
		);

		echo $this->render_form( $ui, $admin, $patterns, $rule ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$recent = $this->recent_generated_summary();
		echo $ui->card( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'title'   => __( 'Generated content', 'kayan' ),
				'content' => '<p>' . sprintf( /* translators: %d: number of generated posts */ esc_html__( '%d posts carry a PSEO blueprint.', 'kayan' ), (int) $recent ) . ' <a href="' . esc_url( $admin->module_url( 'blueprints' ) ) . '">' . esc_html__( 'View in Blueprints', 'kayan' ) . '</a></p>',
			)
		);
	}

	/**
	 * @param Kayan_Admin_UI       $ui       UI.
	 * @param Kayan_Admin_Platform $admin    Admin.
	 * @param array                $patterns Patterns.
	 * @param array|null           $rule     Rule being edited.
	 * @return string
	 */
	private function render_form( $ui, $admin, array $patterns, $rule ) {
		$defaults = kayan_pseo()->rules->schema_defaults();
		$rule     = $rule ? $rule : $defaults;

		$pattern_options = array();
		foreach ( $patterns as $id => $p ) {
			$pattern_options[ $id ] = $p['label'];
		}

		ob_start();
		?>
		<form method="post" action="<?php echo esc_url( $admin->module_url( 'pseo' ) ); ?>">
			<?php wp_nonce_field( self::NONCE_SAVE, '_kayan_nonce' ); ?>
			<input type="hidden" name="kayan_pseo_action" value="save_rule" />
			<input type="hidden" name="id" value="<?php echo esc_attr( (string) $rule['id'] ); ?>" />
			<?php
			echo $ui->field( array( 'type' => 'text', 'name' => 'label', 'label' => __( 'Rule label', 'kayan' ), 'value' => $rule['label'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'select', 'name' => 'pattern_id', 'label' => __( 'Pattern', 'kayan' ), 'value' => $rule['pattern_id'], 'options' => $pattern_options ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'countries', 'label' => __( 'Countries (comma-separated, empty = default)', 'kayan' ), 'value' => implode( ', ', (array) $rule['filters']['countries'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'cities', 'label' => __( 'Cities (slugs, empty = all)', 'kayan' ), 'value' => implode( ', ', (array) $rule['filters']['cities'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'services', 'label' => __( 'Services (slugs, empty = all)', 'kayan' ), 'value' => implode( ', ', (array) $rule['filters']['services'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'text', 'name' => 'languages', 'label' => __( 'Languages (comma-separated)', 'kayan' ), 'value' => implode( ', ', (array) $rule['filters']['languages'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'type'    => 'select',
					'name'    => 'post_status',
					'label'   => __( 'Generate as', 'kayan' ),
					'value'   => $rule['output']['post_status'],
					'options' => array( 'draft' => __( 'Draft', 'kayan' ), 'publish' => __( 'Publish', 'kayan' ), 'future' => __( 'Scheduled', 'kayan' ) ),
				)
			);
			echo $ui->field( array( 'type' => 'checkbox', 'name' => 'ai_enabled', 'label' => __( 'AI enabled', 'kayan' ), 'value' => $rule['output']['ai_enabled'], 'description' => __( 'AI content generation arrives in Phase 5.', 'kayan' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<p class="submit">
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Save rule', 'kayan' ); ?></button>
			</p>
		</form>
		<?php
		return $ui->card(
			array(
				'title'   => $rule['id'] ? __( 'Edit rule', 'kayan' ) : __( 'Create a rule', 'kayan' ),
				'content' => (string) ob_get_clean(),
			)
		);
	}

	/**
	 * @return int
	 */
	private function recent_generated_summary() {
		if ( ! function_exists( 'kayan_query' ) ) {
			return 0;
		}
		$q = kayan_query()->wp_query(
			array(
				'post_type'      => kayan_pseo()->storage->host_post_types(),
				'post_status'    => array( 'publish', 'draft', 'future', 'pending' ),
				'posts_per_page' => -1,
				'meta_key'       => Kayan_PSEO_Blueprint::META_BLUEPRINT, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		return count( $q->posts );
	}
}
