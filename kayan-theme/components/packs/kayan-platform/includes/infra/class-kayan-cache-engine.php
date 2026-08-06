<?php
/**
 * KAYAN Cache Engine — unified cache API for the whole platform.
 *
 * Do not scatter transients / wp_cache calls across modules.
 * Drivers: object cache, transients, future Redis / Memcached — same API.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface Kayan_Cache_Driver_Interface {

	/**
	 * @return string
	 */
	public function id();

	/**
	 * @return bool
	 */
	public function is_available();

	/**
	 * @param string $key Cache key.
	 * @return mixed|null Null on miss (use array('__kayan_miss'=>true) internally if needed).
	 */
	public function get( $key );

	/**
	 * @param string $key        Key.
	 * @param mixed  $value      Value.
	 * @param int    $expiration Seconds (0 = no expiry where supported).
	 * @return bool
	 */
	public function set( $key, $value, $expiration = 0 );

	/**
	 * @param string $key Key.
	 * @return bool
	 */
	public function delete( $key );

	/**
	 * @param string $group Group / prefix.
	 * @return bool
	 */
	public function flush_group( $group );
}

/**
 * WordPress object cache driver (Redis/Memcached when a persistent object-cache drop-in is present).
 */
class Kayan_Cache_Driver_Object implements Kayan_Cache_Driver_Interface {

	/**
	 * @return string
	 */
	public function id() {
		return 'object';
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		return function_exists( 'wp_cache_get' ) && function_exists( 'wp_cache_set' );
	}

	/**
	 * @param string $key Key.
	 * @return mixed|null
	 */
	public function get( $key ) {
		$found = false;
		$value = wp_cache_get( $key, 'kayan', false, $found );
		if ( ! $found ) {
			return null;
		}
		return $value;
	}

	/**
	 * @param string $key        Key.
	 * @param mixed  $value      Value.
	 * @param int    $expiration Expiration.
	 * @return bool
	 */
	public function set( $key, $value, $expiration = 0 ) {
		return (bool) wp_cache_set( $key, $value, 'kayan', (int) $expiration );
	}

	/**
	 * @param string $key Key.
	 * @return bool
	 */
	public function delete( $key ) {
		return (bool) wp_cache_delete( $key, 'kayan' );
	}

	/**
	 * Object cache has no native group flush for custom groups — delete tracked keys via option index.
	 *
	 * @param string $group Group.
	 * @return bool
	 */
	public function flush_group( $group ) {
		unset( $group );
		return true;
	}
}

/**
 * Transient driver (DB or external object cache backed).
 */
class Kayan_Cache_Driver_Transient implements Kayan_Cache_Driver_Interface {

	/**
	 * @return string
	 */
	public function id() {
		return 'transient';
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		return function_exists( 'get_transient' );
	}

	/**
	 * @param string $key Key.
	 * @return mixed|null
	 */
	public function get( $key ) {
		$value = get_transient( $this->transient_key( $key ) );
		return false === $value ? null : $value;
	}

	/**
	 * @param string $key        Key.
	 * @param mixed  $value      Value.
	 * @param int    $expiration Expiration.
	 * @return bool
	 */
	public function set( $key, $value, $expiration = 0 ) {
		$expiration = (int) $expiration;
		if ( $expiration <= 0 ) {
			$expiration = YEAR_IN_SECONDS;
		}
		return (bool) set_transient( $this->transient_key( $key ), $value, $expiration );
	}

	/**
	 * @param string $key Key.
	 * @return bool
	 */
	public function delete( $key ) {
		return (bool) delete_transient( $this->transient_key( $key ) );
	}

	/**
	 * @param string $group Group.
	 * @return bool
	 */
	public function flush_group( $group ) {
		unset( $group );
		return true;
	}

	/**
	 * @param string $key Key.
	 * @return string
	 */
	private function transient_key( $key ) {
		$hash = md5( $key );
		return 'kayan_c_' . substr( $hash, 0, 40 );
	}
}

/**
 * Future Redis driver stub — register a real driver without changing app code.
 */
class Kayan_Cache_Driver_Redis_Stub implements Kayan_Cache_Driver_Interface {

	/**
	 * @return string
	 */
	public function id() {
		return 'redis';
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		return false;
	}

	/**
	 * @param string $key Key.
	 * @return mixed|null
	 */
	public function get( $key ) {
		unset( $key );
		return null;
	}

	/**
	 * @param string $key        Key.
	 * @param mixed  $value      Value.
	 * @param int    $expiration Expiration.
	 * @return bool
	 */
	public function set( $key, $value, $expiration = 0 ) {
		unset( $key, $value, $expiration );
		return false;
	}

	/**
	 * @param string $key Key.
	 * @return bool
	 */
	public function delete( $key ) {
		unset( $key );
		return false;
	}

	/**
	 * @param string $group Group.
	 * @return bool
	 */
	public function flush_group( $group ) {
		unset( $group );
		return false;
	}
}

/**
 * Future Memcached driver stub.
 */
class Kayan_Cache_Driver_Memcached_Stub implements Kayan_Cache_Driver_Interface {

	/**
	 * @return string
	 */
	public function id() {
		return 'memcached';
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		return false;
	}

	/**
	 * @param string $key Key.
	 * @return mixed|null
	 */
	public function get( $key ) {
		unset( $key );
		return null;
	}

	/**
	 * @param string $key        Key.
	 * @param mixed  $value      Value.
	 * @param int    $expiration Expiration.
	 * @return bool
	 */
	public function set( $key, $value, $expiration = 0 ) {
		unset( $key, $value, $expiration );
		return false;
	}

	/**
	 * @param string $key Key.
	 * @return bool
	 */
	public function delete( $key ) {
		unset( $key );
		return false;
	}

	/**
	 * @param string $group Group.
	 * @return bool
	 */
	public function flush_group( $group ) {
		unset( $group );
		return false;
	}
}

class Kayan_Cache_Engine {

	const GROUP_INDEX_OPTION = 'kayan_cache_group_index';

	/** @var array<string,Kayan_Cache_Driver_Interface> */
	private $drivers = array();

	/** @var string */
	private $default_driver = 'object';

	/** @var string */
	private $prefix = 'kayan';

	/**
	 * @return void
	 */
	public function register() {
		$this->register_driver( new Kayan_Cache_Driver_Object() );
		$this->register_driver( new Kayan_Cache_Driver_Transient() );
		$this->register_driver( new Kayan_Cache_Driver_Redis_Stub() );
		$this->register_driver( new Kayan_Cache_Driver_Memcached_Stub() );

		// Prefer object cache when a persistent backend is present; else transients.
		if ( $this->should_prefer_transient() ) {
			$this->default_driver = 'transient';
		}

		/**
		 * @param Kayan_Cache_Engine $cache Cache engine.
		 */
		do_action( 'kayan_cache_register_drivers', $this );

		/**
		 * @param string $driver Default driver id.
		 */
		$this->default_driver = sanitize_key( (string) apply_filters( 'kayan_cache_default_driver', $this->default_driver ) );
	}

	/**
	 * @param Kayan_Cache_Driver_Interface $driver Driver.
	 * @return void
	 */
	public function register_driver( Kayan_Cache_Driver_Interface $driver ) {
		$this->drivers[ $driver->id() ] = $driver;
	}

	/**
	 * @param string|null $id Driver id.
	 * @return Kayan_Cache_Driver_Interface|null
	 */
	public function get_driver( $id = null ) {
		$id = $id ? sanitize_key( $id ) : $this->default_driver;
		if ( isset( $this->drivers[ $id ] ) && $this->drivers[ $id ]->is_available() ) {
			return $this->drivers[ $id ];
		}
		if ( isset( $this->drivers['transient'] ) && $this->drivers['transient']->is_available() ) {
			return $this->drivers['transient'];
		}
		return isset( $this->drivers['object'] ) ? $this->drivers['object'] : null;
	}

	/**
	 * @param string      $key     Logical key.
	 * @param string      $group   Group.
	 * @param string|null $driver  Driver override.
	 * @return mixed|null
	 */
	public function get( $key, $group = 'default', $driver = null ) {
		$full = $this->full_key( $key, $group );
		$d    = $this->get_driver( $driver );
		if ( ! $d ) {
			return null;
		}
		$value = $d->get( $full );
		/**
		 * @param mixed  $value Value.
		 * @param string $key   Key.
		 * @param string $group Group.
		 */
		return apply_filters( 'kayan_cache_get', $value, $key, $group );
	}

	/**
	 * @param string      $key        Key.
	 * @param mixed       $value      Value.
	 * @param int         $expiration Seconds.
	 * @param string      $group      Group.
	 * @param string|null $driver     Driver.
	 * @return bool
	 */
	public function set( $key, $value, $expiration = 3600, $group = 'default', $driver = null ) {
		$full = $this->full_key( $key, $group );
		$d    = $this->get_driver( $driver );
		if ( ! $d ) {
			return false;
		}
		$this->track_key( $group, $full );
		return $d->set( $full, $value, (int) $expiration );
	}

	/**
	 * Remember: get from cache or execute callback and store.
	 *
	 * @param string      $key        Key.
	 * @param callable    $callback   Producer.
	 * @param int         $expiration Seconds.
	 * @param string      $group      Group.
	 * @param string|null $driver     Driver.
	 * @return mixed
	 */
	public function remember( $key, $callback, $expiration = 3600, $group = 'default', $driver = null ) {
		$cached = $this->get( $key, $group, $driver );
		if ( null !== $cached ) {
			return $cached;
		}
		$value = call_user_func( $callback );
		$this->set( $key, $value, $expiration, $group, $driver );
		return $value;
	}

	/**
	 * @param string      $key    Key.
	 * @param string      $group  Group.
	 * @param string|null $driver Driver.
	 * @return bool
	 */
	public function delete( $key, $group = 'default', $driver = null ) {
		$full = $this->full_key( $key, $group );
		$d    = $this->get_driver( $driver );
		return $d ? $d->delete( $full ) : false;
	}

	/**
	 * Flush all tracked keys in a group across available drivers.
	 *
	 * @param string $group Group.
	 * @return void
	 */
	public function flush_group( $group ) {
		$group = sanitize_key( $group );
		$index = get_option( self::GROUP_INDEX_OPTION, array() );
		$keys  = ( is_array( $index ) && isset( $index[ $group ] ) && is_array( $index[ $group ] ) )
			? $index[ $group ]
			: array();

		foreach ( $this->drivers as $driver ) {
			if ( ! $driver->is_available() ) {
				continue;
			}
			foreach ( $keys as $full_key ) {
				$driver->delete( $full_key );
			}
			$driver->flush_group( $group );
		}

		if ( is_array( $index ) ) {
			unset( $index[ $group ] );
			update_option( self::GROUP_INDEX_OPTION, $index, false );
		}
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		$drivers = array();
		foreach ( $this->drivers as $id => $driver ) {
			$drivers[ $id ] = array(
				'id'        => $id,
				'available' => $driver->is_available(),
			);
		}
		return array(
			'default_driver' => $this->default_driver,
			'drivers'        => $drivers,
			'apis'           => array(
				'get'          => 'kayan_cache()->get( $key, $group )',
				'set'          => 'kayan_cache()->set( $key, $value, $ttl, $group )',
				'remember'     => 'kayan_cache()->remember( $key, $callback, $ttl, $group )',
				'delete'       => 'kayan_cache()->delete( $key, $group )',
				'flush_group'  => 'kayan_cache()->flush_group( $group )',
			),
			'future'         => array( 'redis', 'memcached' ),
		);
	}

	/**
	 * @param string $key   Key.
	 * @param string $group Group.
	 * @return string
	 */
	private function full_key( $key, $group ) {
		$group = sanitize_key( $group );
		$key   = preg_replace( '/[^a-zA-Z0-9_\-\.:]/', '_', (string) $key );
		return $this->prefix . ':' . $group . ':' . $key;
	}

	/**
	 * @param string $group Group.
	 * @param string $full  Full key.
	 * @return void
	 */
	private function track_key( $group, $full ) {
		$group = sanitize_key( $group );
		$index = get_option( self::GROUP_INDEX_OPTION, array() );
		if ( ! is_array( $index ) ) {
			$index = array();
		}
		if ( ! isset( $index[ $group ] ) || ! is_array( $index[ $group ] ) ) {
			$index[ $group ] = array();
		}
		if ( ! in_array( $full, $index[ $group ], true ) ) {
			$index[ $group ][] = $full;
			// Cap index size per group.
			if ( count( $index[ $group ] ) > 500 ) {
				$index[ $group ] = array_slice( $index[ $group ], -500 );
			}
			update_option( self::GROUP_INDEX_OPTION, $index, false );
		}
	}

	/**
	 * @return bool
	 */
	private function should_prefer_transient() {
		// wp_using_ext_object_cache() means Redis/Memcached drop-in — keep object driver.
		if ( function_exists( 'wp_using_ext_object_cache' ) && wp_using_ext_object_cache() ) {
			return false;
		}
		return true;
	}
}
