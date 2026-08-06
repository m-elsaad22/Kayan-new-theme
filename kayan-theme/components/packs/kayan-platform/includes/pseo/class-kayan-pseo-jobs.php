<?php
/**
 * PSEO Jobs / Queue — draft / schedule / bulk / regenerate execution.
 *
 * Backed by the `kayan_pseo_queue` table (created by the Migration Engine —
 * see Kayan_Migration_Engine::register_core_migrations()). Same public API
 * contract as the earlier option-backed version; storage was upgraded
 * because bulk jobs can enumerate thousands of combinations, which does
 * not belong in a single serialized wp_options row.
 *
 * run() processes jobs in chunks (default 20 items per call) so a single
 * PHP request never times out on a large bulk job — the Scheduler calls
 * run() repeatedly until each job's cursor reaches its total.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Jobs {

	const TABLE = 'kayan_pseo_queue';

	/** @var Kayan_PSEO_Generator|null */
	private $generator;

	/** @var Kayan_PSEO_Rules|null */
	private $rules;

	/**
	 * @return void
	 */
	public function register() {
		// Table lifecycle is owned by the Migration Engine (pseo_queue_table_v1).
	}

	/**
	 * Wired after Generator/Rules exist (breaks constructor circularity).
	 *
	 * @param Kayan_PSEO_Generator $generator Generator.
	 * @param Kayan_PSEO_Rules     $rules     Rules.
	 * @return void
	 */
	public function set_dependencies( Kayan_PSEO_Generator $generator, Kayan_PSEO_Rules $rules ) {
		$this->generator = $generator;
		$this->rules     = $rules;
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
			'total'       => 0,
			'cursor'      => 0,
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
	 * @param array<string,mixed> $args Args: status, type, limit, offset.
	 * @return array<int,array<string,mixed>>
	 */
	public function all( array $args = array() ) {
		global $wpdb;
		$table = $this->table();
		$where = array( '1=1' );
		if ( ! empty( $args['status'] ) ) {
			$where[] = $wpdb->prepare( 'status = %s', sanitize_key( $args['status'] ) );
		}
		if ( ! empty( $args['type'] ) ) {
			$where[] = $wpdb->prepare( 'job_type = %s', sanitize_key( $args['type'] ) );
		}
		$limit  = isset( $args['limit'] ) ? absint( $args['limit'] ) : 50;
		$offset = isset( $args['offset'] ) ? absint( $args['offset'] ) : 0;

		$sql  = "SELECT * FROM {$table} WHERE " . implode( ' AND ', $where ) . ' ORDER BY id DESC';
		$sql .= $wpdb->prepare( ' LIMIT %d OFFSET %d', $limit, $offset );
		$rows = $wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$jobs = array();
		foreach ( (array) $rows as $row ) {
			$jobs[] = $this->row_to_job( $row );
		}

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
		$row = $this->fetch_row( sanitize_key( $id ) );
		return $row ? $this->row_to_job( $row ) : null;
	}

	/**
	 * Enqueue a job (does not execute — the Scheduler / manual run() does).
	 *
	 * @param string               $type bulk|schedule|regenerate|ai_regenerate.
	 * @param array<string,mixed>  $args rule_id, post_ids, combinations, options, schedule_at, priority.
	 * @return array{ok:bool,job?:array,errors?:string[]}
	 */
	public function enqueue( $type, array $args = array() ) {
		global $wpdb;
		$type = sanitize_key( $type );
		if ( ! in_array( $type, array( 'bulk', 'schedule', 'regenerate', 'ai_regenerate' ), true ) ) {
			return array( 'ok' => false, 'errors' => array( 'invalid_job_type' ) );
		}

		$rule_id  = sanitize_key( (string) ( $args['rule_id'] ?? '' ) );
		$post_ids = array_values( array_filter( array_map( 'absint', (array) ( $args['post_ids'] ?? array() ) ) ) );
		$combos   = isset( $args['combinations'] ) && is_array( $args['combinations'] ) ? array_values( $args['combinations'] ) : array();

		if ( 'schedule' === $type && empty( $args['schedule_at'] ) ) {
			return array( 'ok' => false, 'errors' => array( 'schedule_at_required' ) );
		}
		if ( in_array( $type, array( 'regenerate', 'ai_regenerate' ), true ) && empty( $post_ids ) && empty( $rule_id ) ) {
			return array( 'ok' => false, 'errors' => array( 'post_ids_or_rule_required' ) );
		}
		if ( 'bulk' === $type && empty( $combos ) && empty( $rule_id ) ) {
			return array( 'ok' => false, 'errors' => array( 'combinations_or_rule_required' ) );
		}

		$total = $combos ? count( $combos ) : count( $post_ids );
		$id    = ! empty( $args['id'] ) ? sanitize_key( $args['id'] ) : $this->generate_id();

		$payload = array(
			'rule_id'      => $rule_id,
			'post_ids'     => $post_ids,
			'combinations' => $combos,
			'options'      => wp_parse_args( (array) ( $args['options'] ?? array() ), array( 'post_status' => 'draft', 'ai_enabled' => false ) ),
			'schedule_at'  => sanitize_text_field( (string) ( $args['schedule_at'] ?? '' ) ),
		);

		$run_after = null;
		if ( 'schedule' === $type ) {
			$ts = strtotime( $payload['schedule_at'] . ' UTC' );
			$run_after = $ts ? gmdate( 'Y-m-d H:i:s', $ts ) : null;
		}

		$wpdb->insert(
			$this->table(),
			array(
				'job_id'       => $id,
				'job_type'     => $type,
				'status'       => 'queued',
				'priority'     => isset( $args['priority'] ) ? absint( $args['priority'] ) : 10,
				'payload'      => wp_json_encode( $payload ),
				'result'       => wp_json_encode( array( 'stats' => array( 'total' => $total, 'created' => 0, 'updated' => 0, 'skipped' => 0, 'failed' => 0 ), 'errors' => array() ) ),
				'cursor_pos'   => 0,
				'attempts'     => 0,
				'max_attempts' => 3,
				'run_after'    => $run_after,
				'created_at'   => current_time( 'mysql', true ),
			)
		); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

		$job = $this->get( $id );

		/**
		 * @param array $job Job.
		 */
		do_action( 'kayan_pseo_job_enqueued', $job );

		return array( 'ok' => true, 'job' => $job );
	}

	/**
	 * @param string              $id     Job id.
	 * @param string              $status Status.
	 * @param array<string,mixed> $patch  Extra fields (merged into result/payload as appropriate).
	 * @return array{ok:bool,job?:array,errors?:string[]}
	 */
	public function update_status( $id, $status, array $patch = array() ) {
		global $wpdb;
		$id     = sanitize_key( $id );
		$status = sanitize_key( $status );
		if ( ! in_array( $status, array( 'queued', 'running', 'completed', 'failed', 'cancelled' ), true ) ) {
			return array( 'ok' => false, 'errors' => array( 'invalid_status' ) );
		}
		$row = $this->fetch_row( $id );
		if ( ! $row ) {
			return array( 'ok' => false, 'errors' => array( 'job_not_found' ) );
		}

		$fields = array( 'status' => $status );
		$now    = current_time( 'mysql', true );
		if ( 'running' === $status && empty( $row['started_at'] ) ) {
			$fields['started_at'] = $now;
		}
		if ( in_array( $status, array( 'completed', 'failed', 'cancelled' ), true ) ) {
			$fields['finished_at'] = $now;
		}
		if ( isset( $patch['stats'] ) || isset( $patch['errors'] ) ) {
			$result             = json_decode( (string) $row['result'], true );
			$result             = is_array( $result ) ? $result : array();
			if ( isset( $patch['stats'] ) ) {
				$result['stats'] = array_merge( $result['stats'] ?? array(), (array) $patch['stats'] );
			}
			if ( isset( $patch['errors'] ) ) {
				$result['errors'] = array_merge( $result['errors'] ?? array(), (array) $patch['errors'] );
			}
			$fields['result'] = wp_json_encode( $result );
		}
		if ( isset( $patch['cursor'] ) ) {
			$fields['cursor_pos'] = absint( $patch['cursor'] );
		}
		if ( isset( $patch['error'] ) ) {
			$fields['error'] = sanitize_text_field( (string) $patch['error'] );
		}

		$wpdb->update( $this->table(), $fields, array( 'job_id' => $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

		return array( 'ok' => true, 'job' => $this->get( $id ) );
	}

	/**
	 * Process one chunk of a job. Safe to call repeatedly (Scheduler does).
	 *
	 * @param string $id    Job id.
	 * @param int    $limit Items to process this call.
	 * @return array{ok:bool,job?:array,errors?:string[]}
	 */
	public function run( $id, $limit = 20 ) {
		global $wpdb;
		$id  = sanitize_key( $id );
		$row = $this->fetch_row( $id );
		if ( ! $row ) {
			return array( 'ok' => false, 'errors' => array( 'job_not_found' ) );
		}
		if ( in_array( $row['status'], array( 'completed', 'cancelled' ), true ) ) {
			return array( 'ok' => true, 'job' => $this->row_to_job( $row ) );
		}
		if ( ! $this->generator ) {
			return array( 'ok' => false, 'errors' => array( 'generator_not_wired' ) );
		}

		$payload = json_decode( (string) $row['payload'], true );
		$payload = is_array( $payload ) ? $payload : array();
		$result  = json_decode( (string) $row['result'], true );
		$result  = is_array( $result ) ? $result : array( 'stats' => array( 'total' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0, 'failed' => 0 ), 'errors' => array() );

		if ( 'queued' === $row['status'] ) {
			$wpdb->update( $this->table(), array( 'status' => 'running', 'started_at' => current_time( 'mysql', true ) ), array( 'job_id' => $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		}

		$type      = $row['job_type'];
		$cursor    = (int) $row['cursor_pos'];
		$errors    = array();

		try {
			$items = $this->resolve_work_items( $type, $payload );
			$total = count( $items );
			$slice = array_slice( $items, $cursor, max( 1, (int) $limit ) );

			foreach ( $slice as $item ) {
				try {
					$this->process_item( $type, $item, $payload, $result );
				} catch ( \Throwable $e ) {
					$result['stats']['failed'] = (int) ( $result['stats']['failed'] ?? 0 ) + 1;
					$errors[] = $e->getMessage();
				}
				++$cursor;
			}
		} catch ( \Throwable $e ) {
			// Catastrophic failure (e.g. expansion itself threw) — back off with exponential delay.
			$attempts = (int) $row['attempts'] + 1;
			$backoff  = min( 60, $attempts * 5 );
			$status   = $attempts >= (int) $row['max_attempts'] ? 'failed' : 'queued';
			$wpdb->update(
				$this->table(),
				array(
					'attempts'  => $attempts,
					'status'    => $status,
					'error'     => sanitize_text_field( $e->getMessage() ),
					'run_after' => gmdate( 'Y-m-d H:i:s', time() + ( $backoff * 60 ) ),
				),
				array( 'job_id' => $id )
			); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			return array( 'ok' => false, 'errors' => array( $e->getMessage() ) );
		}

		if ( $errors ) {
			$result['errors'] = array_slice( array_merge( (array) ( $result['errors'] ?? array() ), $errors ), -50 );
		}
		$result['stats']['total'] = $total;

		$done   = $cursor >= $total;
		$fields = array(
			'cursor_pos' => $cursor,
			'result'     => wp_json_encode( $result ),
			'status'     => $done ? 'completed' : 'running',
		);
		if ( $done ) {
			$fields['finished_at'] = current_time( 'mysql', true );
		}
		$wpdb->update( $this->table(), $fields, array( 'job_id' => $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

		if ( function_exists( 'kayan_logger' ) ) {
			kayan_logger()->log( 'queue', 'pseo.queue.tick', array( 'job_id' => $id, 'processed' => count( $slice ), 'cursor' => $cursor, 'total' => $total, 'done' => $done ) );
		}

		return array( 'ok' => true, 'job' => $this->get( $id ) );
	}

	/**
	 * @param string $type    Job type.
	 * @param array  $payload Payload.
	 * @return array<int,mixed>
	 */
	private function resolve_work_items( $type, array $payload ) {
		if ( 'bulk' === $type || 'schedule' === $type ) {
			$combos = isset( $payload['combinations'] ) ? (array) $payload['combinations'] : array();
			if ( empty( $combos ) && ! empty( $payload['rule_id'] ) && $this->rules ) {
				$expansion = $this->rules->preview_combinations( $payload['rule_id'] );
				$combos    = ! empty( $expansion['combinations'] ) ? $expansion['combinations'] : array();
			}
			return $combos;
		}
		return isset( $payload['post_ids'] ) ? (array) $payload['post_ids'] : array();
	}

	/**
	 * @param string $type    Job type.
	 * @param mixed  $item    Combination array (bulk/schedule) or post id (regenerate).
	 * @param array  $payload Payload.
	 * @param array  $result  Result (by reference semantics via return in run()).
	 * @return void
	 */
	private function process_item( $type, $item, array $payload, array &$result ) {
		$options = isset( $payload['options'] ) ? (array) $payload['options'] : array();

		if ( 'bulk' === $type || 'schedule' === $type ) {
			$combo    = (array) $item;
			$preview  = $this->generator->preview(
				(string) ( $combo['pattern_id'] ?? '' ),
				(array) ( $combo['entities'] ?? array() ),
				(string) ( $combo['country'] ?? '' ),
				(string) ( $combo['language'] ?? 'ar' ),
				(array) ( $combo['tokens'] ?? array() )
			);
			if ( empty( $preview['ok'] ) ) {
				$result['stats']['skipped'] = (int) ( $result['stats']['skipped'] ?? 0 ) + 1;
				return;
			}
			$mat = $this->generator->materialize(
				$preview,
				array(
					'post_status' => $options['post_status'] ?? 'draft',
					'schedule_at' => $payload['schedule_at'] ?? '',
					'rule_id'     => $payload['rule_id'] ?? '',
					'source'      => 'rule',
				)
			);
			if ( empty( $mat['ok'] ) ) {
				$result['stats']['failed'] = (int) ( $result['stats']['failed'] ?? 0 ) + 1;
				return;
			}
			$key = ! empty( $mat['created'] ) ? 'created' : 'updated';
			$result['stats'][ $key ] = (int) ( $result['stats'][ $key ] ?? 0 ) + 1;
			return;
		}

		$post_id = absint( $item );
		if ( 'ai_regenerate' === $type ) {
			$res = $this->generator->ai_regenerate( $post_id, $options );
		} else {
			$res = $this->generator->regenerate( $post_id, $options );
		}
		if ( empty( $res['ok'] ) ) {
			$result['stats']['failed'] = (int) ( $result['stats']['failed'] ?? 0 ) + 1;
			return;
		}
		$result['stats']['updated'] = (int) ( $result['stats']['updated'] ?? 0 ) + 1;
	}

	/**
	 * Job ids ready for processing right now (queued/running, due, attempts left),
	 * ordered by priority then age. Used by the Scheduler.
	 *
	 * @param int $limit Max ids.
	 * @return string[]
	 */
	public function due_job_ids( $limit = 5 ) {
		global $wpdb;
		$table = $this->table();
		$sql   = $wpdb->prepare(
			"SELECT job_id FROM {$table}
			 WHERE status IN ('queued','running')
			 AND (run_after IS NULL OR run_after <= %s)
			 AND attempts < max_attempts
			 ORDER BY priority ASC, id ASC
			 LIMIT %d",
			current_time( 'mysql', true ),
			absint( $limit )
		); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$rows = $wpdb->get_results( $sql, ARRAY_A );
		return array_map(
			static function ( $row ) {
				return $row['job_id'];
			},
			(array) $rows
		);
	}

	/**
	 * Cancel a queued job (no-op if already running/completed).
	 *
	 * @param string $id Job id.
	 * @return array{ok:bool,errors?:string[]}
	 */
	public function cancel( $id ) {
		$row = $this->fetch_row( sanitize_key( $id ) );
		if ( ! $row || 'queued' !== $row['status'] ) {
			return array( 'ok' => false, 'errors' => array( 'not_cancellable' ) );
		}
		return $this->update_status( $id, 'cancelled' );
	}

	/**
	 * Requeue a failed job (resets attempts + status).
	 *
	 * @param string $id Job id.
	 * @return array{ok:bool,errors?:string[]}
	 */
	public function retry( $id ) {
		global $wpdb;
		$id  = sanitize_key( $id );
		$row = $this->fetch_row( $id );
		if ( ! $row ) {
			return array( 'ok' => false, 'errors' => array( 'job_not_found' ) );
		}
		$wpdb->update( $this->table(), array( 'status' => 'queued', 'attempts' => 0, 'error' => '', 'run_after' => null ), array( 'job_id' => $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		return array( 'ok' => true, 'job' => $this->get( $id ) );
	}

	/**
	 * @param array $row Table row.
	 * @return array<string,mixed>
	 */
	private function row_to_job( array $row ) {
		$payload = json_decode( (string) $row['payload'], true );
		$payload = is_array( $payload ) ? $payload : array();
		$result  = json_decode( (string) $row['result'], true );
		$result  = is_array( $result ) ? $result : array();

		$job = $this->schema_defaults();
		$job['id']          = $row['job_id'];
		$job['type']        = $row['job_type'];
		$job['status']      = $row['status'];
		$job['rule_id']     = $payload['rule_id'] ?? '';
		$job['post_ids']    = $payload['post_ids'] ?? array();
		$job['schedule_at'] = $payload['schedule_at'] ?? '';
		$job['options']     = $payload['options'] ?? $job['options'];
		$job['created_at']  = $row['created_at'] ?? '';
		$job['started_at']  = $row['started_at'] ?? '';
		$job['finished_at'] = $row['finished_at'] ?? '';
		$job['updated_at']  = $job['finished_at'] ?: ( $job['started_at'] ?: $job['created_at'] );
		$job['cursor']      = (int) $row['cursor_pos'];
		$job['total']       = (int) ( $result['stats']['total'] ?? 0 );
		$job['stats']       = $result['stats'] ?? $job['stats'];
		$job['errors']      = $result['errors'] ?? array();
		return $job;
	}

	/**
	 * @param string $id Job id.
	 * @return array<string,mixed>|null
	 */
	private function fetch_row( $id ) {
		global $wpdb;
		$table = $this->table();
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE job_id = %s", $id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return is_array( $row ) ? $row : null;
	}

	/**
	 * @return string
	 */
	private function table() {
		global $wpdb;
		return $wpdb->prefix . self::TABLE;
	}

	/**
	 * @return string
	 */
	private function generate_id() {
		return 'job_' . substr( md5( uniqid( 'kayan_pseo_job', true ) ), 0, 12 );
	}
}
