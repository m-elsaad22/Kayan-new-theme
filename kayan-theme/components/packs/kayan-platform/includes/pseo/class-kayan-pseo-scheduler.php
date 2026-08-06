<?php
/**
 * PSEO Scheduler — drives the Queue (Kayan_PSEO_Jobs) automatically.
 *
 * Uses WP-Cron as the primary driver, with a lightweight admin_init
 * fallback so queued/scheduled jobs keep progressing even on sites where
 * real cron is disabled or infrequent — no manual "process queue" step is
 * ever required, though one is exposed in the admin UI for operators.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Scheduler {

	const CRON_HOOK       = 'kayan_pseo_queue_tick';
	const CRON_INTERVAL   = 'kayan_pseo_five_minutes';
	const OPTION_INLINE_RATE = 'kayan_pseo_queue_inline_last_run';

	/** @var Kayan_PSEO_Jobs */
	private $jobs;

	public function __construct( Kayan_PSEO_Jobs $jobs ) {
		$this->jobs = $jobs;
	}

	/**
	 * @return void
	 */
	public function register() {
		add_filter( 'cron_schedules', array( $this, 'register_cron_interval' ) ); // phpcs:ignore WordPress.WP.CronInterval.CronSchedulesInterval
		add_action( self::CRON_HOOK, array( $this, 'process_batch' ) );
		add_action( 'init', array( $this, 'ensure_scheduled' ), 20 );
		add_action( 'admin_init', array( $this, 'maybe_process_inline' ), 30 );
	}

	/**
	 * @param array $schedules Schedules.
	 * @return array
	 */
	public function register_cron_interval( $schedules ) {
		$schedules[ self::CRON_INTERVAL ] = array(
			'interval' => 5 * MINUTE_IN_SECONDS,
			'display'  => __( 'Every 5 minutes (KAYAN PSEO Queue)', 'kayan' ),
		);
		return $schedules;
	}

	/**
	 * @return void
	 */
	public function ensure_scheduled() {
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time() + 60, self::CRON_INTERVAL, self::CRON_HOOK );
		}
	}

	/**
	 * Lightweight fallback for sites without reliable real cron — processes a
	 * small batch at most once per minute during normal admin traffic.
	 *
	 * @return void
	 */
	public function maybe_process_inline() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$last = (int) get_transient( self::OPTION_INLINE_RATE );
		if ( $last && ( time() - $last ) < 60 ) {
			return;
		}
		set_transient( self::OPTION_INLINE_RATE, time(), 60 );
		$this->process_batch( 2, 10 );
	}

	/**
	 * Process a batch of due jobs. Safe to call repeatedly/manually.
	 *
	 * @param int $max_jobs      Max jobs to touch this call.
	 * @param int $items_per_job Items per job per call.
	 * @return array<string,mixed>
	 */
	public function process_batch( $max_jobs = 5, $items_per_job = 20 ) {
		$ids       = $this->jobs->due_job_ids( $max_jobs );
		$processed = array();

		foreach ( $ids as $id ) {
			$result = $this->jobs->run( $id, $items_per_job );
			$processed[ $id ] = ! empty( $result['ok'] );
		}

		/**
		 * @param array $processed job_id => ok.
		 */
		do_action( 'kayan_pseo_queue_processed', $processed );

		return array( 'ok' => true, 'processed' => $processed );
	}

	/**
	 * Manual "process now" for the admin UI.
	 *
	 * @return array<string,mixed>
	 */
	public function process_now() {
		return $this->process_batch( 10, 50 );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'cron_hook'      => self::CRON_HOOK,
			'next_scheduled' => wp_next_scheduled( self::CRON_HOOK ),
			'apis'           => array(
				'process_batch' => 'kayan_pseo()->scheduler->process_batch()',
				'process_now'   => 'kayan_pseo()->scheduler->process_now()',
			),
		);
	}
}
