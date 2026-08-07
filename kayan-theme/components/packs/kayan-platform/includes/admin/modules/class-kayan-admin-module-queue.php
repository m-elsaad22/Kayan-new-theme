<?php
/**
 * Admin module: Queue (Kayan_PSEO_Jobs + Kayan_PSEO_Scheduler).
 *
 * Lists jobs from the real `kayan_pseo_queue` table and lets an operator
 * trigger a batch manually. The Scheduler already runs automatically
 * (WP-Cron + admin_init fallback) — this button is a convenience, not a
 * requirement.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Queue {

	const NONCE_ACTION = 'kayan_admin_queue_action';

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
			'queue',
			array(
				'label'       => __( 'Queue', 'kayan' ),
				'description' => __( 'Bulk generation and regeneration jobs.', 'kayan' ),
				'icon'        => 'dashicons-controls-repeat',
				'position'    => 60,
				'capability'  => 'kayan_manage_queue',
				'group'       => 'system',
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
		if ( empty( $_POST['kayan_queue_action'] ) ) {
			return;
		}
		check_admin_referer( self::NONCE_ACTION, '_kayan_nonce' );
		$action = sanitize_key( wp_unslash( $_POST['kayan_queue_action'] ) );
		$id     = isset( $_POST['job_id'] ) ? sanitize_key( wp_unslash( $_POST['job_id'] ) ) : '';

		if ( 'process_now' === $action ) {
			kayan_pseo()->scheduler->process_now();
			$context['admin']->redirect_module( 'queue', 'updated' );
			return;
		}
		if ( 'retry' === $action && $id ) {
			kayan_pseo()->jobs->retry( $id );
			$context['admin']->redirect_module( 'queue', 'updated' );
			return;
		}
		if ( 'cancel' === $action && $id ) {
			kayan_pseo()->jobs->cancel( $id );
			$context['admin']->redirect_module( 'queue', 'updated' );
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
		$jobs  = kayan_pseo()->jobs->all( array( 'limit' => 50 ) );

		ob_start();
		?>
		<form method="post" action="<?php echo esc_url( $admin->module_url( 'queue' ) ); ?>">
			<?php wp_nonce_field( self::NONCE_ACTION, '_kayan_nonce' ); ?>
			<input type="hidden" name="kayan_queue_action" value="process_now" />
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Process queue now', 'kayan' ); ?></button>
		</form>
		<?php
		echo $ui->card( array( 'title' => __( 'Scheduler', 'kayan' ), 'content' => (string) ob_get_clean() . '<p class="description">' . esc_html__( 'Runs automatically every ~5 minutes via WP-Cron, plus a light admin fallback. No manual step is required.', 'kayan' ) . '</p>' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$rows = array();
		foreach ( $jobs as $job ) {
			$progress = $job['total'] > 0 ? round( ( $job['cursor'] / $job['total'] ) * 100 ) : 0;
			$type_map = array(
				'success' => array( 'completed' ),
				'error'   => array( 'failed' ),
				'warning' => array( 'cancelled' ),
				'info'    => array( 'queued', 'running' ),
			);
			$status_type = 'neutral';
			foreach ( $type_map as $type => $statuses ) {
				if ( in_array( $job['status'], $statuses, true ) ) {
					$status_type = $type;
					break;
				}
			}

			ob_start();
			if ( 'failed' === $job['status'] ) {
				?>
				<form method="post" action="<?php echo esc_url( $admin->module_url( 'queue' ) ); ?>" style="display:inline">
					<?php wp_nonce_field( self::NONCE_ACTION, '_kayan_nonce' ); ?>
					<input type="hidden" name="kayan_queue_action" value="retry" />
					<input type="hidden" name="job_id" value="<?php echo esc_attr( $job['id'] ); ?>" />
					<button type="submit" class="button button-small"><?php esc_html_e( 'Retry', 'kayan' ); ?></button>
				</form>
				<?php
			}
			if ( 'queued' === $job['status'] ) {
				?>
				<form method="post" action="<?php echo esc_url( $admin->module_url( 'queue' ) ); ?>" style="display:inline">
					<?php wp_nonce_field( self::NONCE_ACTION, '_kayan_nonce' ); ?>
					<input type="hidden" name="kayan_queue_action" value="cancel" />
					<input type="hidden" name="job_id" value="<?php echo esc_attr( $job['id'] ); ?>" />
					<button type="submit" class="button button-small button-link-delete"><?php esc_html_e( 'Cancel', 'kayan' ); ?></button>
				</form>
				<?php
			}
			$actions = (string) ob_get_clean();

			$stats = $job['stats'];
			$rows[] = array(
				'id'       => $job['id'],
				'job'      => '<code>' . esc_html( $job['id'] ) . '</code>',
				'type'     => esc_html( (string) $job['type'] ),
				'status'   => $ui->status( array( 'label' => ucfirst( (string) $job['status'] ), 'type' => $status_type ) ),
				'progress' => esc_html( $job['cursor'] . ' / ' . $job['total'] . ' (' . $progress . '%)' ),
				'stats'    => esc_html( sprintf( 'C:%d U:%d S:%d F:%d', $stats['created'] ?? 0, $stats['updated'] ?? 0, $stats['skipped'] ?? 0, $stats['failed'] ?? 0 ) ),
				'created'  => esc_html( (string) $job['created_at'] ),
				'actions'  => $actions,
			);
		}

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'job'      => __( 'Job', 'kayan' ),
					'type'     => __( 'Type', 'kayan' ),
					'status'   => __( 'Status', 'kayan' ),
					'progress' => __( 'Progress', 'kayan' ),
					'stats'    => __( 'Created/Updated/Skipped/Failed', 'kayan' ),
					'created'  => __( 'Queued at', 'kayan' ),
					'actions'  => __( 'Actions', 'kayan' ),
				),
				'rows'    => $rows,
				'empty'   => __( 'No jobs yet. Bulk Generate from the Programmatic SEO module to enqueue one.', 'kayan' ),
			)
		);
	}
}
