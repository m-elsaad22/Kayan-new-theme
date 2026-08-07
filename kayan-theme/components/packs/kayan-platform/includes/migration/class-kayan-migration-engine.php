<?php
/**
 * KAYAN Migration & Version Engine (Phase 4 prerequisite).
 *
 * Generic, reusable upgrade infrastructure so no site EVER needs a manual
 * upgrade step: theme version bumps, DB schema changes, custom table
 * creation/upgrades, option/meta/taxonomy/rewrite migrations all run
 * automatically, exactly once, in order, with history + rollback data.
 *
 * Design goals:
 * - Idempotent: running twice is always safe (checked via history table).
 * - Incremental: migrations are ordered by version but tracked by id, so
 *   adding a new migration later never re-runs old ones.
 * - Automatic: runs on theme activation and (rate-limited) on admin_init —
 *   no wp-admin button, WP-CLI command, or manual SQL is ever required.
 * - Reuse: existing packs (booking/payment/track) keep their own working
 *   ad-hoc version checks untouched. This engine is additive — other packs
 *   MAY register through it later via `kayan_migrations_register`.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Migration_Engine {

	const HISTORY_TABLE      = 'kayan_migrations';
	const OPTION_LOCK        = 'kayan_migrations_lock';
	const OPTION_RATE        = 'kayan_migrations_last_check';
	const OPTION_VERSION     = 'kayan_platform_schema_version';
	const STATUS_SUCCESS     = 'success';
	const STATUS_FAILED      = 'failed';
	const STATUS_ROLLED_BACK = 'rolled_back';
	const STATUS_RUNNING     = 'running';

	/** @var array<string,array<string,mixed>> */
	private $migrations = array();

	/** @var Kayan_Logger|null */
	private $logger;

	/** @var bool */
	private $table_ready = false;

	public function __construct( ?Kayan_Logger $logger = null ) {
		$this->logger = $logger;
	}

	/**
	 * @return void
	 */
	public function register() {
		$this->register_core_migrations();

		/**
		 * Other packs may register migrations here (additive — never forced).
		 *
		 * @param Kayan_Migration_Engine $engine Engine.
		 */
		do_action( 'kayan_migrations_register', $this );

		/**
		 * @param Kayan_Migration_Engine $engine Engine.
		 */
		do_action( 'kayan_migrations_registered', $this );

		// Fast path on every request: one cached option read, no DB table touch,
		// no dbDelta call. Only when a new migration was deployed does the
		// heavier, self-healing boot_check() run — automatically, on the very
		// next request of ANY kind. No manual step is ever required.
		$this->boot_check();

		add_action( 'after_switch_theme', array( $this, 'run' ) );
		add_action( 'admin_init', array( $this, 'maybe_run' ), 5 );
	}

	/**
	 * Cheap steady-state gate + self-healing full run when the cached
	 * schema version falls behind the registered target.
	 *
	 * @return void
	 */
	public function boot_check() {
		$cached = (int) get_option( self::OPTION_VERSION, 0 );
		$target = $this->target_version();

		if ( $cached >= $target ) {
			return;
		}

		$this->ensure_history_table();
		$result = $this->run();

		if ( $result['ok'] ) {
			update_option( self::OPTION_VERSION, $this->current_version(), true );
		}
	}

	/**
	 * Register a migration.
	 *
	 * @param string               $id   Unique, stable migration id (never reused/changed).
	 * @param array<string,mixed>  $args {
	 *     @type int      $version         Monotonic schema version this migration advances to.
	 *     @type string   $type            schema|table|option|meta|taxonomy|rewrite.
	 *     @type string   $description     Human description.
	 *     @type callable $up              function( Kayan_Migration_Engine $engine ): true|WP_Error|string[] (errors).
	 *     @type callable|null $down       Optional explicit rollback callable.
	 *     @type string[] $rollback_options Option names to snapshot before `up` runs (auto-restored on rollback if no $down).
	 * }
	 * @return void
	 */
	public function register_migration( $id, array $args ) {
		$id = sanitize_key( $id );
		if ( '' === $id ) {
			return;
		}
		$defaults = array(
			'version'          => 1,
			'type'             => 'schema',
			'description'      => '',
			'up'               => null,
			'down'             => null,
			'rollback_options' => array(),
		);
		$this->migrations[ $id ] = array_merge( $defaults, $args, array( 'id' => $id ) );
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function migrations() {
		/**
		 * @param array $migrations Migrations.
		 */
		return apply_filters( 'kayan_migrations_list', $this->migrations );
	}

	/**
	 * Rate-limited automatic check (admin_init). Cheap no-op when nothing pending.
	 *
	 * @return void
	 */
	public function maybe_run() {
		if ( ! current_user_can( 'manage_options' ) ) {
			// Still allow the check for admins only — avoids running DB writes on every visitor's admin_init edge cases (AJAX from lower roles).
			return;
		}
		$last = (int) get_transient( self::OPTION_RATE );
		if ( $last && ( time() - $last ) < 30 ) {
			return;
		}
		set_transient( self::OPTION_RATE, time(), 30 );
		$this->run();
	}

	/**
	 * Run every pending migration in version order. Safe to call anytime.
	 *
	 * @return array{ok:bool,ran:string[],skipped:string[],failed:string[]}
	 */
	public function run() {
		$this->ensure_history_table();

		if ( ! $this->acquire_lock() ) {
			return array( 'ok' => false, 'ran' => array(), 'skipped' => array(), 'failed' => array(), 'errors' => array( 'locked' ) );
		}

		$ran     = array();
		$skipped = array();
		$failed  = array();

		$migrations = $this->migrations();
		uasort(
			$migrations,
			static function ( $a, $b ) {
				return ( (int) $a['version'] ) <=> ( (int) $b['version'] );
			}
		);

		$applied = $this->applied_ids();

		foreach ( $migrations as $id => $migration ) {
			if ( isset( $applied[ $id ] ) && self::STATUS_SUCCESS === $applied[ $id ] ) {
				$skipped[] = $id;
				continue;
			}
			if ( ! is_callable( $migration['up'] ) ) {
				continue;
			}

			$ok = $this->run_one( $id, $migration );
			if ( $ok ) {
				$ran[] = $id;
			} else {
				$failed[] = $id;
				// Stop the batch on first failure to avoid cascading issues on out-of-order dependents.
				break;
			}
		}

		$this->release_lock();

		if ( $ran && $this->logger ) {
			$this->logger->log( 'migration', 'migrations.batch_completed', array( 'ran' => $ran, 'failed' => $failed ), $failed ? Kayan_Logger::LEVEL_ERROR : Kayan_Logger::LEVEL_INFO );
		}

		return array(
			'ok'      => empty( $failed ),
			'ran'     => $ran,
			'skipped' => $skipped,
			'failed'  => $failed,
		);
	}

	/**
	 * Roll back a single migration (best-effort).
	 *
	 * @param string $id Migration id.
	 * @return array{ok:bool,errors?:string[]}
	 */
	public function rollback( $id ) {
		$this->ensure_history_table();
		$id  = sanitize_key( $id );
		$row = $this->history_row( $id );
		if ( ! $row ) {
			return array( 'ok' => false, 'errors' => array( 'not_found' ) );
		}

		$migration = isset( $this->migrations()[ $id ] ) ? $this->migrations()[ $id ] : null;
		$rollback_data = json_decode( (string) $row['rollback_data'], true );
		$rollback_data = is_array( $rollback_data ) ? $rollback_data : array();

		$ok = true;
		if ( $migration && is_callable( $migration['down'] ) ) {
			$result = call_user_func( $migration['down'], $this, $rollback_data );
			$ok     = ( true === $result || ( is_array( $result ) && ! empty( $result['ok'] ) ) );
		} elseif ( ! empty( $rollback_data['options'] ) ) {
			foreach ( $rollback_data['options'] as $key => $value ) {
				if ( null === $value ) {
					delete_option( $key );
				} else {
					update_option( $key, $value, false );
				}
			}
		}

		$this->write_history_row(
			$id,
			array(
				'status'      => $ok ? self::STATUS_ROLLED_BACK : self::STATUS_FAILED,
				'message'     => $ok ? 'rolled_back' : 'rollback_failed',
				'finished_at' => current_time( 'mysql', true ),
			),
			true
		);

		if ( $this->logger ) {
			$this->logger->log( 'migration', 'migrations.rolled_back', array( 'id' => $id, 'ok' => $ok ), $ok ? Kayan_Logger::LEVEL_WARNING : Kayan_Logger::LEVEL_ERROR );
		}

		return array( 'ok' => $ok );
	}

	/**
	 * @return int Highest successfully-applied migration version.
	 */
	public function current_version() {
		global $wpdb;
		$this->ensure_history_table();
		$table = $this->table_name();
		$max = $wpdb->get_var( $wpdb->prepare( "SELECT MAX(version) FROM {$table} WHERE status = %s", self::STATUS_SUCCESS ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return (int) $max;
	}

	/**
	 * @return int Highest registered migration version (target).
	 */
	public function target_version() {
		$max = 0;
		foreach ( $this->migrations() as $migration ) {
			$max = max( $max, (int) $migration['version'] );
		}
		return $max;
	}

	/**
	 * Paginated migration history (for admin display).
	 *
	 * @param array<string,mixed> $args Args: limit, offset.
	 * @return array<int,array<string,mixed>>
	 */
	public function history( array $args = array() ) {
		global $wpdb;
		$this->ensure_history_table();
		$limit  = isset( $args['limit'] ) ? absint( $args['limit'] ) : 50;
		$offset = isset( $args['offset'] ) ? absint( $args['offset'] ) : 0;
		$table  = $this->table_name();
		$rows   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} ORDER BY id DESC LIMIT %d OFFSET %d", $limit, $offset ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return is_array( $rows ) ? $rows : array();
	}

	/**
	 * Generic helper: create or upgrade a custom table via dbDelta (idempotent).
	 * Available to any migration's `up` callback, and to other engines (e.g. PSEO queue).
	 *
	 * @param string $table_suffix Table name without prefix (e.g. 'kayan_pseo_queue').
	 * @param string $columns_sql  Column + key definitions (no CREATE TABLE wrapper, no charset clause).
	 * @return bool
	 */
	public function create_or_upgrade_table( $table_suffix, $columns_sql ) {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name      = $wpdb->prefix . sanitize_key( $table_suffix );
		$charset_collate = method_exists( $wpdb, 'get_charset_collate' ) ? $wpdb->get_charset_collate() : '';

		$sql = "CREATE TABLE {$table_name} (\n{$columns_sql}\n) {$charset_collate};";
		$result = dbDelta( $sql );

		if ( $this->logger ) {
			$this->logger->log( 'migration', 'migrations.table_synced', array( 'table' => $table_name, 'result' => is_array( $result ) ? array_values( $result ) : array() ) );
		}

		return true;
	}

	/**
	 * Snapshot option values before a mutating migration runs (for rollback).
	 *
	 * @param string[] $option_names Options to snapshot.
	 * @return array<string,mixed>
	 */
	public function snapshot_options( array $option_names ) {
		$snapshot = array();
		foreach ( $option_names as $name ) {
			$snapshot[ $name ] = get_option( $name, null );
		}
		return array( 'options' => $snapshot );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'current_version' => $this->current_version(),
			'target_version'  => $this->target_version(),
			'up_to_date'      => $this->current_version() >= $this->target_version(),
			'migrations'      => array_keys( $this->migrations() ),
			'history_table'   => $this->table_name(),
			'apis'            => array(
				'run'                    => 'kayan_migrations()->run()',
				'rollback'               => 'kayan_migrations()->rollback( $id )',
				'history'                => 'kayan_migrations()->history()',
				'register_migration'     => 'kayan_migrations()->register_migration( $id, $args )',
				'create_or_upgrade_table'=> 'kayan_migrations()->create_or_upgrade_table( $suffix, $columns_sql )',
			),
		);
	}

	/**
	 * @param string               $id        Migration id.
	 * @param array<string,mixed>  $migration Migration def.
	 * @return bool
	 */
	private function run_one( $id, array $migration ) {
		$started = microtime( true );
		$rollback_data = array();
		if ( ! empty( $migration['rollback_options'] ) ) {
			$rollback_data = $this->snapshot_options( (array) $migration['rollback_options'] );
		}

		$this->write_history_row(
			$id,
			array(
				'version'       => (int) $migration['version'],
				'type'          => sanitize_key( (string) $migration['type'] ),
				'description'   => sanitize_text_field( (string) $migration['description'] ),
				'status'        => self::STATUS_RUNNING,
				'rollback_data' => wp_json_encode( $rollback_data ),
				'started_at'    => current_time( 'mysql', true ),
			)
		);

		try {
			$result = call_user_func( $migration['up'], $this );
			$ok     = ( true === $result ) || ( is_wp_error( $result ) ? false : ( is_array( $result ) ? empty( $result['errors'] ) : (bool) $result ) );
			$message = is_wp_error( $result ) ? $result->get_error_message() : 'ok';
		} catch ( \Throwable $e ) {
			$ok      = false;
			$message = $e->getMessage();
		}

		$duration = (int) round( ( microtime( true ) - $started ) * 1000 );

		$this->write_history_row(
			$id,
			array(
				'status'      => $ok ? self::STATUS_SUCCESS : self::STATUS_FAILED,
				'message'     => sanitize_text_field( (string) $message ),
				'duration_ms' => $duration,
				'finished_at' => current_time( 'mysql', true ),
			),
			true
		);

		if ( $this->logger ) {
			$this->logger->log(
				'migration',
				'migrations.' . ( $ok ? 'applied' : 'failed' ),
				array( 'id' => $id, 'version' => $migration['version'], 'duration_ms' => $duration, 'message' => $message ),
				$ok ? Kayan_Logger::LEVEL_INFO : Kayan_Logger::LEVEL_ERROR
			);
		}

		return $ok;
	}

	/**
	 * @return void
	 */
	private function ensure_history_table() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table           = $this->table_name();
		$charset_collate = method_exists( $wpdb, 'get_charset_collate' ) ? $wpdb->get_charset_collate() : '';

		$sql = "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			migration_id VARCHAR(191) NOT NULL,
			version INT UNSIGNED NOT NULL DEFAULT 0,
			type VARCHAR(32) NOT NULL DEFAULT 'schema',
			description TEXT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'running',
			message TEXT NULL,
			rollback_data LONGTEXT NULL,
			started_at DATETIME NULL,
			finished_at DATETIME NULL,
			duration_ms INT UNSIGNED NULL,
			PRIMARY KEY  (id),
			KEY migration_id (migration_id),
			KEY status (status)
		) {$charset_collate};";

		dbDelta( $sql );
		$this->table_ready = true;
	}

	/**
	 * @return string
	 */
	private function table_name() {
		global $wpdb;
		return $wpdb->prefix . self::HISTORY_TABLE;
	}

	/**
	 * @return array<string,string> migration_id => last status
	 */
	private function applied_ids() {
		global $wpdb;
		if ( ! $this->table_ready ) {
			return array();
		}
		$table = $this->table_name();
		$rows  = $wpdb->get_results( "SELECT migration_id, status FROM {$table} ORDER BY id ASC", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$out   = array();
		foreach ( (array) $rows as $row ) {
			$out[ $row['migration_id'] ] = $row['status'];
		}
		return $out;
	}

	/**
	 * @param string $id Migration id.
	 * @return array<string,mixed>|null
	 */
	private function history_row( $id ) {
		global $wpdb;
		if ( ! $this->table_ready ) {
			return null;
		}
		$table = $this->table_name();
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE migration_id = %s ORDER BY id DESC LIMIT 1", $id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return is_array( $row ) ? $row : null;
	}

	/**
	 * Insert a new row or update the most recent row for this migration id within the same run.
	 *
	 * @param string               $id     Migration id.
	 * @param array<string,mixed>  $fields Fields.
	 * @param bool                 $update Update most recent 'running' row instead of inserting.
	 * @return void
	 */
	private function write_history_row( $id, array $fields, $update = false ) {
		global $wpdb;
		if ( ! $this->table_ready ) {
			return;
		}
		$table = $this->table_name();

		if ( $update ) {
			$running = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE migration_id = %s ORDER BY id DESC LIMIT 1", $id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			if ( $running ) {
				$wpdb->update( $table, $fields, array( 'id' => (int) $running ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				return;
			}
		}

		$fields['migration_id'] = $id;
		$wpdb->insert( $table, $fields ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	}

	/**
	 * @return bool
	 */
	private function acquire_lock() {
		if ( get_transient( self::OPTION_LOCK ) ) {
			return false;
		}
		set_transient( self::OPTION_LOCK, 1, 300 );
		return true;
	}

	/**
	 * @return void
	 */
	private function release_lock() {
		delete_transient( self::OPTION_LOCK );
	}

	/**
	 * Built-in platform migrations. Additional migrations register via
	 * `kayan_migrations_register` (see class docblock) without editing this list.
	 *
	 * @return void
	 */
	private function register_core_migrations() {
		$this->register_migration(
			'pseo_queue_table_v1',
			array(
				'version'     => 1,
				'type'        => 'table',
				'description' => 'Create the kayan_pseo_queue table backing the Generator Queue/Scheduler.',
				'up'          => function ( Kayan_Migration_Engine $engine ) {
					return $engine->create_or_upgrade_table(
						'kayan_pseo_queue',
						"id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
						job_id VARCHAR(64) NOT NULL,
						job_type VARCHAR(32) NOT NULL DEFAULT 'bulk',
						status VARCHAR(20) NOT NULL DEFAULT 'queued',
						priority INT NOT NULL DEFAULT 10,
						payload LONGTEXT NULL,
						result LONGTEXT NULL,
						cursor_pos INT UNSIGNED NOT NULL DEFAULT 0,
						attempts INT UNSIGNED NOT NULL DEFAULT 0,
						max_attempts INT UNSIGNED NOT NULL DEFAULT 3,
						run_after DATETIME NULL,
						created_at DATETIME NULL,
						started_at DATETIME NULL,
						finished_at DATETIME NULL,
						error TEXT NULL,
						PRIMARY KEY  (id),
						UNIQUE KEY job_id (job_id),
						KEY status (status),
						KEY job_type (job_type)"
					);
				},
			)
		);

		$this->register_migration(
			'pseo_dependencies_table_v1',
			array(
				'version'     => 2,
				'type'        => 'table',
				'description' => 'Create the kayan_pseo_dependencies table backing the Dependency Graph (Phase 5).',
				'up'          => function ( Kayan_Migration_Engine $engine ) {
					return $engine->create_or_upgrade_table(
						'kayan_pseo_dependencies',
						"id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
						post_id BIGINT UNSIGNED NOT NULL,
						entity_type VARCHAR(32) NOT NULL,
						entity_ref VARCHAR(191) NOT NULL,
						PRIMARY KEY  (id),
						UNIQUE KEY post_entity (post_id, entity_type, entity_ref),
						KEY entity_lookup (entity_type, entity_ref)"
					);
				},
			)
		);
	}
}
