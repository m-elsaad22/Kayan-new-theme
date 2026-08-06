<?php
/**
 * PSEO Jobs API — draft / schedule / bulk / regenerate job contracts.
 *
 * Phase 2.5 persists job definitions only. No cron runners create posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Jobs {

	const OPTION_KEY = 'kayan_pseo_jobs';

	/**
	 * @return void
	 */
	public function register() {
		// No runners in Phase 2.5.
	}

	/**
	 * @return array<string,mixed>
	 */
	public function schema_defaults() {
		return array(
			'id'          => '',
			'type'        => 'bulk', // bulk|schedule|regenerate|ai_regenerate
			'rule_id'     => '',
			'post_ids'    => array(), // for regenerate jobs
			'status'      => 'queued', // queued|running|completed|failed|cancelled
			'schedule_at' => '',
			'created_at'  => '',
			'updated_at'  => '',
			'started_at'  => '',
			'finished_at' => '',
			'stats'       => array(
				'total'     => 0,
				'created'   => 0,
				'updated'   => 0,
				'skipped'   => 0,
				'failed'    => 0,
			),
			'errors'      => array(),
			'options'     => array(
				'post_status' => 'draft',
				'ai_enabled'  => false,
			),
		);
	}

	/**
	 * @return array<int,array<string,mixed>>
	 */
	public function all() {
		$jobs = $this->read_option();
		/**
		 * @param array $jobs Jobs.
		 */
		return apply_filters( 'kayan_pseo_jobs', $jobs );
	}

	/**
	 * @param string $id Job id.
	 * @return array<string,mixed>|null
	 */
	public function get( $id ) {
		$id = sanitize_key( $id );
		foreach ( $this->all() as $job ) {
			if ( isset( $job['id'] ) && $job['id'] === $id ) {
				return $job;
			}
		}
		return null;
	}

	/**
	 * Enqueue a job definition (does not execute generation).
	 *
	 * @param string               $type bulk|schedule|regenerate|ai_regenerate
	 * @param array<string,mixed>  $args Args.
	 * @return array{ok:bool,job?:array,errors?:string[]}
	 */
	public function enqueue( $type, array $args = array() ) {
		$type = sanitize_key( $type );
		if ( ! in_array( $type, array( 'bulk', 'schedule', 'regenerate', 'ai_regenerate' ), true ) ) {
			return array(
				'ok'     => false,
				'errors' => array( 'invalid_job_type' ),
			);
		}

		$job               = array_merge( $this->schema_defaults(), $args );
		$job['id']         = ! empty( $args['id'] ) ? sanitize_key( $args['id'] ) : $this->generate_id();
		$job['type']       = $type;
		$job['status']     = 'queued';
		$job['created_at'] = gmdate( 'c' );
		$job['updated_at'] = $job['created_at'];
		$job['rule_id']    = sanitize_key( (string) ( $job['rule_id'] ?? '' ) );
		$job['post_ids']   = array_values( array_filter( array_map( 'absint', (array) ( $job['post_ids'] ?? array() ) ) ) );

		if ( 'schedule' === $type && empty( $job['schedule_at'] ) ) {
			return array(
				'ok'     => false,
				'errors' => array( 'schedule_at_required' ),
			);
		}

		if ( in_array( $type, array( 'regenerate', 'ai_regenerate' ), true ) && empty( $job['post_ids'] ) && empty( $job['rule_id'] ) ) {
			return array(
				'ok'     => false,
				'errors' => array( 'post_ids_or_rule_required' ),
			);
		}

		$all   = $this->read_option();
		$all[] = $job;
		$this->write_option( $all );

		/**
		 * Fired when a PSEO job is queued (no execution in Phase 2.5).
		 *
		 * @param array $job Job.
		 */
		do_action( 'kayan_pseo_job_enqueued', $job );

		return array(
			'ok'  => true,
			'job' => $job,
		);
	}

	/**
	 * Mark job status (API for future workers).
	 *
	 * @param string              $id     Job id.
	 * @param string              $status Status.
	 * @param array<string,mixed> $patch  Extra fields.
	 * @return array{ok:bool,job?:array,errors?:string[]}
	 */
	public function update_status( $id, $status, array $patch = array() ) {
		$id     = sanitize_key( $id );
		$status = sanitize_key( $status );
		if ( ! in_array( $status, array( 'queued', 'running', 'completed', 'failed', 'cancelled' ), true ) ) {
			return array(
				'ok'     => false,
				'errors' => array( 'invalid_status' ),
			);
		}

		$all = $this->read_option();
		foreach ( $all as $i => $job ) {
			if ( ! isset( $job['id'] ) || $job['id'] !== $id ) {
				continue;
			}
			$job['status']     = $status;
			$job['updated_at'] = gmdate( 'c' );
			if ( 'running' === $status && empty( $job['started_at'] ) ) {
				$job['started_at'] = $job['updated_at'];
			}
			if ( in_array( $status, array( 'completed', 'failed', 'cancelled' ), true ) ) {
				$job['finished_at'] = $job['updated_at'];
			}
			foreach ( $patch as $k => $v ) {
				if ( 'id' === $k ) {
					continue;
				}
				$job[ $k ] = $v;
			}
			$all[ $i ] = $job;
			$this->write_option( $all );
			return array(
				'ok'  => true,
				'job' => $job,
			);
		}

		return array(
			'ok'     => false,
			'errors' => array( 'job_not_found' ),
		);
	}

	/**
	 * Intentionally does not process jobs in Phase 2.5.
	 *
	 * @param string $id Job id.
	 * @return array{ok:bool,errors:string[]}
	 */
	public function run( $id ) {
		unset( $id );
		return array(
			'ok'     => false,
			'errors' => array( 'generation_not_implemented_in_phase_2_5' ),
		);
	}

	/**
	 * @return string
	 */
	private function generate_id() {
		return 'job_' . substr( md5( uniqid( 'kayan_pseo_job', true ) ), 0, 12 );
	}

	/**
	 * @return array
	 */
	private function read_option() {
		if ( function_exists( 'yc_get_option' ) ) {
			$val = yc_get_option( self::OPTION_KEY, array() );
		} else {
			$val = get_option( self::OPTION_KEY, array() );
		}
		return is_array( $val ) ? array_values( $val ) : array();
	}

	/**
	 * @param array $jobs Jobs.
	 * @return bool
	 */
	private function write_option( array $jobs ) {
		if ( function_exists( 'yc_update_option' ) ) {
			return (bool) yc_update_option( self::OPTION_KEY, array_values( $jobs ) );
		}
		return (bool) update_option( self::OPTION_KEY, array_values( $jobs ), false );
	}
}
