<?php
/**
 * KAYAN Settings Engine — unified settings API.
 *
 * Future code should not call get_option() directly.
 * Scopes: global · country · language · module.
 *
 * Wraps existing Country Settings repository for BC.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Settings_Engine {

	const OPTION_GLOBAL   = 'kayan_settings_global';
	const OPTION_LANGUAGE = 'kayan_settings_language_%s'; // %s = lang
	const OPTION_MODULE   = 'kayan_settings_module_%s';   // %s = module

	/** @var Kayan_Country_Engine */
	private $countries;

	/** @var Kayan_Language_Engine */
	private $languages;

	/** @var Kayan_Country_Settings */
	private $country_settings;

	/** @var Kayan_Cache_Engine|null */
	private $cache;

	public function __construct(
		Kayan_Country_Engine $countries,
		Kayan_Language_Engine $languages,
		Kayan_Country_Settings $country_settings
	) {
		$this->countries        = $countries;
		$this->languages        = $languages;
		$this->country_settings = $country_settings;
	}

	/**
	 * @param Kayan_Cache_Engine $cache Cache.
	 * @return void
	 */
	public function set_cache( Kayan_Cache_Engine $cache ) {
		$this->cache = $cache;
	}

	/**
	 * @return void
	 */
	public function register() {
		/**
		 * @param Kayan_Settings_Engine $settings Settings.
		 */
		do_action( 'kayan_settings_engine_registered', $this );
	}

	/**
	 * Universal getter.
	 *
	 * @param string              $key     Dot path key.
	 * @param array<string,mixed> $context Context: scope, country, language, module.
	 * @param mixed               $default Default.
	 * @return mixed
	 */
	public function get( $key, array $context = array(), $default = null ) {
		$scope = isset( $context['scope'] ) ? sanitize_key( $context['scope'] ) : $this->infer_scope( $context );

		switch ( $scope ) {
			case 'country':
				$country = isset( $context['country'] ) ? $context['country'] : null;
				return $this->get_country( $key, $country, $default );
			case 'language':
				$lang = isset( $context['language'] ) ? $context['language'] : 'ar';
				return $this->get_language( $key, $lang, $default );
			case 'module':
				$module = isset( $context['module'] ) ? $context['module'] : '';
				return $this->get_module( $module, $key, $default );
			case 'global':
			default:
				return $this->get_global( $key, $default );
		}
	}

	/**
	 * Universal setter.
	 *
	 * @param string              $key     Key.
	 * @param mixed               $value   Value.
	 * @param array<string,mixed> $context Context.
	 * @return bool
	 */
	public function set( $key, $value, array $context = array() ) {
		$scope = isset( $context['scope'] ) ? sanitize_key( $context['scope'] ) : $this->infer_scope( $context );

		switch ( $scope ) {
			case 'country':
				$country = isset( $context['country'] ) ? $context['country'] : $this->countries->get_default();
				return $this->set_country( $key, $value, $country );
			case 'language':
				$lang = isset( $context['language'] ) ? $context['language'] : 'ar';
				return $this->set_language( $key, $value, $lang );
			case 'module':
				$module = isset( $context['module'] ) ? $context['module'] : '';
				return $this->set_module( $module, $key, $value );
			case 'global':
			default:
				return $this->set_global( $key, $value );
		}
	}

	/**
	 * @param string $key     Key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	public function get_global( $key, $default = null ) {
		$all = $this->read_option( self::OPTION_GLOBAL, array() );
		return $this->dot_get( is_array( $all ) ? $all : array(), $key, $default );
	}

	/**
	 * @param string $key   Key.
	 * @param mixed  $value Value.
	 * @return bool
	 */
	public function set_global( $key, $value ) {
		$all = $this->read_option( self::OPTION_GLOBAL, array() );
		if ( ! is_array( $all ) ) {
			$all = array();
		}
		$all = $this->dot_set( $all, $key, $value );
		return $this->write_option( self::OPTION_GLOBAL, $all );
	}

	/**
	 * Country settings — delegates to Country Settings repository (legacy dual-read preserved).
	 *
	 * @param string      $key     Key.
	 * @param string|null $country Country.
	 * @param mixed       $default Default.
	 * @return mixed
	 */
	public function get_country( $key, $country = null, $default = null ) {
		if ( null === $country || '' === $country ) {
			$country = $this->countries->get_default();
		}
		$cache_key = 'country:' . $country . ':' . $key;
		if ( $this->cache ) {
			$cached = $this->cache->get( $cache_key, 'settings' );
			if ( null !== $cached ) {
				return $cached;
			}
		}
		$value = $this->country_settings->get( $key, $country, $default );
		if ( $this->cache ) {
			$this->cache->set( $cache_key, $value, 300, 'settings' );
		}
		return $value;
	}

	/**
	 * @param string $key     Key.
	 * @param mixed  $value   Value.
	 * @param string $country Country.
	 * @return bool
	 */
	public function set_country( $key, $value, $country ) {
		$country = $this->countries->normalize( $country );
		if ( method_exists( $this->country_settings, 'set' ) ) {
			$result = (bool) $this->country_settings->set( $key, $value, $country );
		} elseif ( method_exists( $this->country_settings, 'update' ) ) {
			$result = (bool) $this->country_settings->update( $key, $value, $country );
		} else {
			// Fallback: write profile option directly via known contract.
			$option = 'kayan_country_profile_' . $country;
			$profile = $this->read_option( $option, array() );
			if ( ! is_array( $profile ) ) {
				$profile = array();
			}
			$profile = $this->dot_set( $profile, $key, $value );
			$result  = $this->write_option( $option, $profile );
		}
		if ( $this->cache ) {
			$this->cache->flush_group( 'settings' );
		}
		return $result;
	}

	/**
	 * @param string $key      Key.
	 * @param string $language Language.
	 * @param mixed  $default  Default.
	 * @return mixed
	 */
	public function get_language( $key, $language = 'ar', $default = null ) {
		$language = sanitize_key( $language );
		$option   = sprintf( self::OPTION_LANGUAGE, $language );
		$all      = $this->read_option( $option, array() );
		return $this->dot_get( is_array( $all ) ? $all : array(), $key, $default );
	}

	/**
	 * @param string $key      Key.
	 * @param mixed  $value    Value.
	 * @param string $language Language.
	 * @return bool
	 */
	public function set_language( $key, $value, $language = 'ar' ) {
		$language = sanitize_key( $language );
		$option   = sprintf( self::OPTION_LANGUAGE, $language );
		$all      = $this->read_option( $option, array() );
		if ( ! is_array( $all ) ) {
			$all = array();
		}
		$all = $this->dot_set( $all, $key, $value );
		return $this->write_option( $option, $all );
	}

	/**
	 * @param string $module  Module id (pseo, seo, track, …).
	 * @param string $key     Key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	public function get_module( $module, $key, $default = null ) {
		$module = sanitize_key( $module );
		if ( ! $module ) {
			return $default;
		}
		$option = sprintf( self::OPTION_MODULE, $module );
		$all    = $this->read_option( $option, array() );
		return $this->dot_get( is_array( $all ) ? $all : array(), $key, $default );
	}

	/**
	 * @param string $module Module.
	 * @param string $key    Key.
	 * @param mixed  $value  Value.
	 * @return bool
	 */
	public function set_module( $module, $key, $value ) {
		$module = sanitize_key( $module );
		if ( ! $module ) {
			return false;
		}
		$option = sprintf( self::OPTION_MODULE, $module );
		$all    = $this->read_option( $option, array() );
		if ( ! is_array( $all ) ) {
			$all = array();
		}
		$all = $this->dot_set( $all, $key, $value );
		$ok  = $this->write_option( $option, $all );
		if ( $ok && $this->cache ) {
			$this->cache->delete( 'module:' . $module, 'settings' );
		}
		return $ok;
	}

	/**
	 * All values for a scope (debug / admin future).
	 *
	 * @param string              $scope   Scope.
	 * @param array<string,mixed> $context Context.
	 * @return array<string,mixed>
	 */
	public function all( $scope = 'global', array $context = array() ) {
		$scope = sanitize_key( $scope );
		switch ( $scope ) {
			case 'country':
				$country = isset( $context['country'] ) ? $context['country'] : $this->countries->get_default();
				if ( method_exists( $this->country_settings, 'all' ) ) {
					return (array) $this->country_settings->all( $country );
				}
				if ( method_exists( $this->country_settings, 'get_profile' ) ) {
					return (array) $this->country_settings->get_profile( $country );
				}
				return (array) $this->read_option( 'kayan_country_profile_' . $this->countries->normalize( $country ), array() );
			case 'language':
				$lang = isset( $context['language'] ) ? sanitize_key( $context['language'] ) : 'ar';
				return (array) $this->read_option( sprintf( self::OPTION_LANGUAGE, $lang ), array() );
			case 'module':
				$module = isset( $context['module'] ) ? sanitize_key( $context['module'] ) : '';
				return $module ? (array) $this->read_option( sprintf( self::OPTION_MODULE, $module ), array() ) : array();
			case 'global':
			default:
				return (array) $this->read_option( self::OPTION_GLOBAL, array() );
		}
	}

	/**
	 * Underlying country settings repository (BC).
	 *
	 * @return Kayan_Country_Settings
	 */
	public function country_repository() {
		return $this->country_settings;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'scopes'  => array( 'global', 'country', 'language', 'module' ),
			'options' => array(
				'global'   => self::OPTION_GLOBAL,
				'language' => self::OPTION_LANGUAGE,
				'module'   => self::OPTION_MODULE,
				'country'  => 'kayan_country_profile_{code} (via Country Settings)',
			),
			'apis'    => array(
				'get'          => 'kayan_settings()->get( $key, $context, $default )',
				'set'          => 'kayan_settings()->set( $key, $value, $context )',
				'get_global'   => 'kayan_settings()->get_global( $key )',
				'get_country'  => 'kayan_settings()->get_country( $key, $country )',
				'get_language' => 'kayan_settings()->get_language( $key, $lang )',
				'get_module'   => 'kayan_settings()->get_module( $module, $key )',
			),
			'note'    => 'Do not call get_option() from application modules — use this engine.',
		);
	}

	/**
	 * @param array<string,mixed> $context Context.
	 * @return string
	 */
	private function infer_scope( array $context ) {
		if ( ! empty( $context['module'] ) ) {
			return 'module';
		}
		if ( ! empty( $context['country'] ) ) {
			return 'country';
		}
		if ( ! empty( $context['language'] ) ) {
			return 'language';
		}
		return 'global';
	}

	/**
	 * Single option reader (only place infra should touch get_option for settings).
	 *
	 * @param string $option  Option.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	private function read_option( $option, $default = null ) {
		return get_option( $option, $default );
	}

	/**
	 * @param string $option Option.
	 * @param mixed  $value  Value.
	 * @return bool
	 */
	private function write_option( $option, $value ) {
		return (bool) update_option( $option, $value, false );
	}

	/**
	 * @param array  $data Data.
	 * @param string $path Dot path.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	private function dot_get( array $data, $path, $default = null ) {
		$path = trim( (string) $path );
		if ( '' === $path ) {
			return $default;
		}
		if ( array_key_exists( $path, $data ) ) {
			return $data[ $path ];
		}
		$parts = explode( '.', $path );
		$cursor = $data;
		foreach ( $parts as $part ) {
			if ( ! is_array( $cursor ) || ! array_key_exists( $part, $cursor ) ) {
				return $default;
			}
			$cursor = $cursor[ $part ];
		}
		return $cursor;
	}

	/**
	 * @param array  $data  Data.
	 * @param string $path  Path.
	 * @param mixed  $value Value.
	 * @return array
	 */
	private function dot_set( array $data, $path, $value ) {
		$path = trim( (string) $path );
		if ( false === strpos( $path, '.' ) ) {
			$data[ $path ] = $value;
			return $data;
		}
		$parts = explode( '.', $path );
		$cursor =& $data;
		foreach ( $parts as $i => $part ) {
			if ( $i === count( $parts ) - 1 ) {
				$cursor[ $part ] = $value;
				break;
			}
			if ( ! isset( $cursor[ $part ] ) || ! is_array( $cursor[ $part ] ) ) {
				$cursor[ $part ] = array();
			}
			$cursor =& $cursor[ $part ];
		}
		return $data;
	}
}
