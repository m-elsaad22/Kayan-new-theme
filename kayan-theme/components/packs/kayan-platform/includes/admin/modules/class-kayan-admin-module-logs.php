<?php
/**
 * Admin module: Logs.
 *
 * Reuses Kayan_Logger (option ring buffer) — no second logging store.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Logs {

	const NONCE_CLEAR = 'kayan_admin_logs_clear';

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
			'logs',
			array(
				'label'       => __( 'Logs', 'kayan' ),
				'description' => __( 'Recent platform log entries by channel and level.', 'kayan' ),
				'icon'        => 'dashicons-list-view',
				'position'    => 65,
				'capability'  => 'kayan_view_logs',
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
		if ( ! isset( $_POST['kayan_logs_action'] ) || 'clear' !== $_POST['kayan_logs_action'] ) {
			return;
		}
		check_admin_referer( self::NONCE_CLEAR, '_kayan_nonce' );
		kayan_logger()->clear();
		$context['admin']->redirect_module( 'logs', 'cleared' );
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
		$logger  = kayan_logger();
		$channel = isset( $_GET['channel'] ) ? sanitize_key( wp_unslash( $_GET['channel'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$level   = isset( $_GET['level'] ) ? sanitize_key( wp_unslash( $_GET['level'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		echo $ui->filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'action' => $admin->module_url( 'logs' ),
				'fields'  => array(
					array(
						'type'    => 'select',
						'name'    => 'channel',
						'label'   => __( 'Channel', 'kayan' ),
						'value'   => $channel,
						'options' => array_merge( array( '' => __( 'All channels', 'kayan' ) ), array_combine( $logger->channels(), $logger->channels() ) ),
					),
					array(
						'type'    => 'select',
						'name'    => 'level',
						'label'   => __( 'Level', 'kayan' ),
						'value'   => $level,
						'options' => array(
							''         => __( 'All levels', 'kayan' ),
							'debug'    => 'Debug',
							'info'     => 'Info',
							'warning'  => 'Warning',
							'error'    => 'Error',
							'critical' => 'Critical',
						),
					),
				),
			)
		);

		$entries = $logger->recent(
			array(
				'channel' => $channel,
				'level'   => $level,
				'limit'   => 100,
			)
		);

		$rows = array();
		foreach ( $entries as $entry ) {
			$level_type = 'info';
			if ( in_array( $entry['level'], array( 'error', 'critical' ), true ) ) {
				$level_type = 'error';
			} elseif ( 'warning' === $entry['level'] ) {
				$level_type = 'warning';
			}
			$rows[] = array(
				'id'      => $entry['id'],
				'time'    => esc_html( (string) $entry['timestamp'] ),
				'channel' => '<code>' . esc_html( (string) $entry['channel'] ) . '</code>',
				'level'   => $ui->status( array( 'label' => strtoupper( (string) $entry['level'] ), 'type' => $level_type ) ),
				'message' => esc_html( (string) $entry['message'] ),
				'context' => empty( $entry['context'] ) ? '—' : '<code>' . esc_html( wp_json_encode( $entry['context'] ) ) . '</code>',
			);
		}

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'time'    => __( 'Time (UTC)', 'kayan' ),
					'channel' => __( 'Channel', 'kayan' ),
					'level'   => __( 'Level', 'kayan' ),
					'message' => __( 'Message', 'kayan' ),
					'context' => __( 'Context', 'kayan' ),
				),
				'rows'    => $rows,
				'empty'   => __( 'No log entries yet.', 'kayan' ),
			)
		);

		?>
		<form method="post" action="<?php echo esc_url( $admin->module_url( 'logs' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Clear all logs?', 'kayan' ) ); ?>');">
			<?php wp_nonce_field( self::NONCE_CLEAR, '_kayan_nonce' ); ?>
			<input type="hidden" name="kayan_logs_action" value="clear" />
			<button type="submit" class="button"><?php esc_html_e( 'Clear all logs', 'kayan' ); ?></button>
		</form>
		<?php
	}
}
