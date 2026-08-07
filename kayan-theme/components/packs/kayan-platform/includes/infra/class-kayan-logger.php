<?php
/**
 * KAYAN Logger — single logging API for the platform.
 *
 * Channels: ai, generator, queue, seo, errors, performance, security, general.
 * Architecture only — no admin UI. Storage: option ring buffer + error_log fallback.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Logger {

	const OPTION_LOGS   = 'kayan_logger_entries';
	const MAX_ENTRIES   = 200;
	const LEVEL_DEBUG   = 'debug';
	const LEVEL_INFO    = 'info';
	const LEVEL_WARNING = 'warning';
	const LEVEL_ERROR   = 'error';
	const LEVEL_CRITICAL = 'critical';

	/** @var string[] */
	private $channels = array(
		'ai',
		'generator',
		'queue',
		'seo',
		'errors',
		'performance',
		'security',
		'general',
		'migration',
	);

	/** @var bool */
	private $enabled = true;

	/**
	 * @return void
	 */
	public function register() {
		/**
		 * @param bool $enabled Enabled.
		 */
		$this->enabled = (bool) apply_filters( 'kayan_logger_enabled', true );

		/**
		 * @param string[] $channels Channels.
		 */
		$this->channels = array_values( array_unique( array_map( 'sanitize_key', (array) apply_filters( 'kayan_logger_channels', $this->channels ) ) ) );

		/**
		 * @param Kayan_Logger $logger Logger.
		 */
		do_action( 'kayan_logger_registered', $this );
	}

	/**
	 * @return string[]
	 */
	public function channels() {
		return $this->channels;
	}

	/**
	 * Core log method.
	 *
	 * @param string               $channel Channel.
	 * @param string               $message Message.
	 * @param array<string,mixed>  $context Context.
	 * @param string               $level   Level.
	 * @return array<string,mixed>
	 */
	public function log( $channel, $message, array $context = array(), $level = self::LEVEL_INFO ) {
		$entry = $this->build_entry( $channel, $message, $context, $level );

		if ( ! $this->enabled ) {
			return $entry;
		}

		/**
		 * Short-circuit or mutate before persist.
		 *
		 * @param array|false $entry Entry or false to skip.
		 */
		$filtered = apply_filters( 'kayan_logger_entry', $entry );
		if ( false === $filtered || null === $filtered ) {
			return $entry;
		}
		if ( is_array( $filtered ) ) {
			$entry = $filtered;
		}

		$this->persist( $entry );

		if ( in_array( $entry['level'], array( self::LEVEL_ERROR, self::LEVEL_CRITICAL ), true ) ) {
			$this->mirror_error_log( $entry );
		}

		/**
		 * @param array $entry Entry.
		 */
		do_action( 'kayan_logger_logged', $entry );

		return $entry;
	}

	/**
	 * @param string $channel Channel.
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @return array
	 */
	public function debug( $channel, $message, array $context = array() ) {
		return $this->log( $channel, $message, $context, self::LEVEL_DEBUG );
	}

	/**
	 * @param string $channel Channel.
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @return array
	 */
	public function info( $channel, $message, array $context = array() ) {
		return $this->log( $channel, $message, $context, self::LEVEL_INFO );
	}

	/**
	 * @param string $channel Channel.
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @return array
	 */
	public function warning( $channel, $message, array $context = array() ) {
		return $this->log( $channel, $message, $context, self::LEVEL_WARNING );
	}

	/**
	 * @param string $channel Channel.
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @return array
	 */
	public function error( $channel, $message, array $context = array() ) {
		return $this->log( $channel, $message, $context, self::LEVEL_ERROR );
	}

	/**
	 * @param string $channel Channel.
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @return array
	 */
	public function critical( $channel, $message, array $context = array() ) {
		return $this->log( $channel, $message, $context, self::LEVEL_CRITICAL );
	}

	// Channel shortcuts.
	public function ai( $message, array $context = array(), $level = self::LEVEL_INFO ) {
		return $this->log( 'ai', $message, $context, $level );
	}

	public function generator( $message, array $context = array(), $level = self::LEVEL_INFO ) {
		return $this->log( 'generator', $message, $context, $level );
	}

	public function queue( $message, array $context = array(), $level = self::LEVEL_INFO ) {
		return $this->log( 'queue', $message, $context, $level );
	}

	public function seo( $message, array $context = array(), $level = self::LEVEL_INFO ) {
		return $this->log( 'seo', $message, $context, $level );
	}

	public function performance( $message, array $context = array(), $level = self::LEVEL_INFO ) {
		return $this->log( 'performance', $message, $context, $level );
	}

	public function security( $message, array $context = array(), $level = self::LEVEL_WARNING ) {
		return $this->log( 'security', $message, $context, $level );
	}

	/**
	 * Timed performance measurement helper.
	 *
	 * @param string   $label    Label.
	 * @param callable $callback Callback.
	 * @param array    $context  Context.
	 * @return mixed
	 */
	public function time( $label, $callback, array $context = array() ) {
		$start = microtime( true );
		$result = call_user_func( $callback );
		$ms     = round( ( microtime( true ) - $start ) * 1000, 2 );
		$context['duration_ms'] = $ms;
		$context['label']       = $label;
		$this->performance( $label, $context );
		return $result;
	}

	/**
	 * Recent entries (for future admin / debugging).
	 *
	 * @param array<string,mixed> $args Args: channel, level, limit.
	 * @return array<int,array<string,mixed>>
	 */
	public function recent( array $args = array() ) {
		$limit   = isset( $args['limit'] ) ? absint( $args['limit'] ) : 50;
		$channel = isset( $args['channel'] ) ? sanitize_key( $args['channel'] ) : '';
		$level   = isset( $args['level'] ) ? sanitize_key( $args['level'] ) : '';
		$entries = get_option( self::OPTION_LOGS, array() );
		if ( ! is_array( $entries ) ) {
			return array();
		}
		$out = array();
		foreach ( array_reverse( $entries ) as $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}
			if ( $channel && ( ! isset( $entry['channel'] ) || $entry['channel'] !== $channel ) ) {
				continue;
			}
			if ( $level && ( ! isset( $entry['level'] ) || $entry['level'] !== $level ) ) {
				continue;
			}
			$out[] = $entry;
			if ( count( $out ) >= $limit ) {
				break;
			}
		}
		return $out;
	}

	/**
	 * @return void
	 */
	public function clear() {
		delete_option( self::OPTION_LOGS );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'enabled'  => $this->enabled,
			'channels' => $this->channels,
			'levels'   => array( self::LEVEL_DEBUG, self::LEVEL_INFO, self::LEVEL_WARNING, self::LEVEL_ERROR, self::LEVEL_CRITICAL ),
			'storage'  => self::OPTION_LOGS,
			'max'      => self::MAX_ENTRIES,
			'apis'     => array(
				'log'         => 'kayan_logger()->log( $channel, $message, $context, $level )',
				'error'       => 'kayan_logger()->error( $channel, $message )',
				'ai'          => 'kayan_logger()->ai( $message )',
				'generator'   => 'kayan_logger()->generator( $message )',
				'queue'       => 'kayan_logger()->queue( $message )',
				'seo'         => 'kayan_logger()->seo( $message )',
				'performance' => 'kayan_logger()->performance( $message )',
				'security'    => 'kayan_logger()->security( $message )',
				'time'        => 'kayan_logger()->time( $label, $callback )',
			),
		);
	}

	/**
	 * @param string $channel Channel.
	 * @param string $message Message.
	 * @param array  $context Context.
	 * @param string $level   Level.
	 * @return array<string,mixed>
	 */
	private function build_entry( $channel, $message, array $context, $level ) {
		$channel = sanitize_key( $channel );
		if ( ! in_array( $channel, $this->channels, true ) ) {
			$channel = 'general';
		}
		$level = sanitize_key( $level );
		$allowed = array( self::LEVEL_DEBUG, self::LEVEL_INFO, self::LEVEL_WARNING, self::LEVEL_ERROR, self::LEVEL_CRITICAL );
		if ( ! in_array( $level, $allowed, true ) ) {
			$level = self::LEVEL_INFO;
		}

		return array(
			'id'        => uniqid( 'log_', true ),
			'channel'   => $channel,
			'level'     => $level,
			'message'   => is_scalar( $message ) ? (string) $message : wp_json_encode( $message ),
			'context'   => $this->sanitize_context( $context ),
			'timestamp' => gmdate( 'c' ),
		);
	}

	/**
	 * @param array $context Context.
	 * @return array
	 */
	private function sanitize_context( array $context ) {
		$clean = array();
		foreach ( $context as $key => $value ) {
			$key = sanitize_key( (string) $key );
			if ( is_scalar( $value ) || null === $value ) {
				$clean[ $key ] = $value;
			} elseif ( is_array( $value ) ) {
				$clean[ $key ] = $this->sanitize_context( $value );
			} else {
				$clean[ $key ] = wp_json_encode( $value );
			}
		}
		return $clean;
	}

	/**
	 * @param array $entry Entry.
	 * @return void
	 */
	private function persist( array $entry ) {
		$entries = get_option( self::OPTION_LOGS, array() );
		if ( ! is_array( $entries ) ) {
			$entries = array();
		}
		$entries[] = $entry;
		/**
		 * @param int $max Max entries.
		 */
		$max = (int) apply_filters( 'kayan_logger_max_entries', self::MAX_ENTRIES );
		if ( $max < 20 ) {
			$max = 20;
		}
		if ( count( $entries ) > $max ) {
			$entries = array_slice( $entries, -$max );
		}
		update_option( self::OPTION_LOGS, $entries, false );
	}

	/**
	 * @param array $entry Entry.
	 * @return void
	 */
	private function mirror_error_log( array $entry ) {
		if ( ! function_exists( 'error_log' ) ) {
			return;
		}
		$line = sprintf(
			'[KAYAN][%s][%s] %s',
			strtoupper( $entry['level'] ),
			$entry['channel'],
			$entry['message']
		);
		error_log( $line );
	}
}
