<?php
/**
 * Country Settings Repository — per-country independent site profile.
 *
 * Storage: one option array per country: `kayan_country_profile_{code}`
 * Read path: country profile → legacy Theme Options keys → empty default.
 * Phase 1 does NOT migrate data and does NOT add admin UI.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Country_Settings {

	const OPTION_PREFIX = 'kayan_country_profile_';

	/** @var Kayan_Country_Engine */
	private $countries;

	/** @var array<string,array<string,mixed>> */
	private $runtime_cache = array();

	public function __construct( Kayan_Country_Engine $countries ) {
		$this->countries = $countries;
	}

	/**
	 * Canonical schema for every country profile.
	 *
	 * @return array<string,mixed>
	 */
	public function schema_defaults() {
		return array(
			// Identity / chrome
			'homepage'           => array(), // reserved: widget bag / page id (future)
			'logo'               => '',
			'header'             => array(),
			'footer'             => array(),
			'hero'               => array(),
			'menus'              => array(
				'primary' => 0,
				'footer'  => 0,
			),

			// Contact / business
			'phone'              => '',
			'whatsapp'           => '',
			'email'              => '',
			'business_name'      => '',
			'business_address'   => '',
			'business_geo'       => array(
				'lat' => '',
				'lng' => '',
			),
			'opening_hours'      => array(),
			'currency'           => '',

			// SEO defaults
			'seo'                => array(
				'title'       => '',
				'description' => '',
				'robots'      => '',
				'canonical_host' => '',
			),

			// Schema defaults
			'schema'             => array(
				'local_business_type' => 'LocalBusiness',
				'price_range'         => '',
				'image'               => '',
			),

			// Analytics / verification
			'analytics'          => array(
				'gtm_id'          => '',
				'ga_id'           => '',
				'gsc_verification'=> '',
			),

			// Sitemap
			'sitemap'            => array(
				'enabled' => true,
				'include' => array(),
			),
		);
	}

	/**
	 * @param string $country Country code.
	 * @return string
	 */
	public function option_key( $country ) {
		return self::OPTION_PREFIX . $this->countries->normalize( $country );
	}

	/**
	 * Full profile with dual-read fallbacks (no writes).
	 *
	 * @param string|null $country Country code.
	 * @return array<string,mixed>
	 */
	public function get_profile( $country = null ) {
		if ( null === $country || '' === $country ) {
			$country = $this->countries->get_default();
		}
		$country = $this->countries->normalize( $country );

		if ( isset( $this->runtime_cache[ $country ] ) ) {
			return $this->runtime_cache[ $country ];
		}

		$stored = $this->read_option( $country );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$profile = $this->merge_deep( $this->schema_defaults(), $stored );
		$profile = $this->apply_legacy_fallbacks( $profile, $country );

		/**
		 * @param array  $profile Country profile.
		 * @param string $country Country code.
		 */
		$profile = apply_filters( 'kayan_platform_country_profile', $profile, $country );

		$this->runtime_cache[ $country ] = $profile;
		return $profile;
	}

	/**
	 * @param string      $key     Dot path (e.g. analytics.gtm_id) or top-level key.
	 * @param string|null $country Country code.
	 * @param mixed       $default Default.
	 * @return mixed
	 */
	public function get( $key, $country = null, $default = '' ) {
		$profile = $this->get_profile( $country );
		$parts   = explode( '.', (string) $key );
		$value   = $profile;

		foreach ( $parts as $part ) {
			if ( ! is_array( $value ) || ! array_key_exists( $part, $value ) ) {
				return $default;
			}
			$value = $value[ $part ];
		}

		return ( '' === $value || null === $value ) ? $default : $value;
	}

	/**
	 * Programmatic write API (no admin UI in Phase 1).
	 *
	 * @param string               $country Country code.
	 * @param array<string,mixed>  $data    Partial profile.
	 * @return bool
	 */
	public function update_profile( $country, array $data ) {
		$country = $this->countries->normalize( $country );
		if ( ! $this->countries->exists( $country ) ) {
			return false;
		}

		$current = $this->read_option( $country );
		if ( ! is_array( $current ) ) {
			$current = array();
		}

		$merged = $this->merge_deep( $current, $data );
		$ok     = $this->write_option( $country, $merged );
		unset( $this->runtime_cache[ $country ] );
		return (bool) $ok;
	}

	/**
	 * Map legacy Theme Options into empty profile fields only (dual-read, no migration write).
	 *
	 * @param array<string,mixed> $profile Profile.
	 * @param string              $country Country code.
	 * @return array<string,mixed>
	 */
	private function apply_legacy_fallbacks( array $profile, $country ) {
		// Legacy globals apply as fallback for every country until profiles are filled.
		$legacy_phone = $this->legacy_option( 'phonenumber', '' );
		$legacy_wa    = $this->legacy_option( 'whatsapp_number', '' );
		$legacy_email = $this->legacy_option( 'company__mail', '' );
		$legacy_logo  = $this->legacy_option( 'logo__data', '' );
		$legacy_name  = $this->legacy_option( 'sitename', '' );
		if ( '' === $legacy_name ) {
			$legacy_name = get_bloginfo( 'name' );
		}

		if ( '' === $profile['phone'] && $legacy_phone ) {
			$profile['phone'] = $legacy_phone;
		}
		if ( '' === $profile['whatsapp'] && $legacy_wa ) {
			$profile['whatsapp'] = $legacy_wa;
		}
		if ( '' === $profile['email'] && $legacy_email ) {
			$profile['email'] = $legacy_email;
		}
		if ( '' === $profile['logo'] && $legacy_logo ) {
			$profile['logo'] = $legacy_logo;
		}
		if ( '' === $profile['business_name'] && $legacy_name ) {
			$profile['business_name'] = $legacy_name;
		}

		if ( function_exists( 'kayan_i18n_country_address' ) && '' === $profile['business_address'] ) {
			$profile['business_address'] = (string) kayan_i18n_country_address( $country );
		}

		$legacy_home_title = $this->legacy_option( 'home__title', '' );
		$legacy_home_desc  = $this->legacy_option( 'home__description', '' );
		if ( '' === $profile['seo']['title'] && $legacy_home_title ) {
			$profile['seo']['title'] = $legacy_home_title;
		}
		if ( '' === $profile['seo']['description'] && $legacy_home_desc ) {
			$profile['seo']['description'] = $legacy_home_desc;
		}

		return $profile;
	}

	/**
	 * @param string $key     Option key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	private function legacy_option( $key, $default = '' ) {
		if ( function_exists( 'yc_get_option' ) ) {
			// Theme Options historically use plain get_option — try both without duplicating writes.
			$val = yc_get_option( $key, null );
			if ( null !== $val && false !== $val && '' !== $val ) {
				return $val;
			}
		}
		$val = get_option( $key, $default );
		return ( false === $val ) ? $default : $val;
	}

	/**
	 * @param string $country Country code.
	 * @return mixed
	 */
	private function read_option( $country ) {
		$key = $this->option_key( $country );
		if ( function_exists( 'yc_get_option' ) ) {
			return yc_get_option( $key, array() );
		}
		return get_option( $key, array() );
	}

	/**
	 * @param string               $country Country code.
	 * @param array<string,mixed>  $value   Profile.
	 * @return bool
	 */
	private function write_option( $country, array $value ) {
		$key = $this->option_key( $country );
		if ( function_exists( 'yc_update_option' ) ) {
			return (bool) yc_update_option( $key, $value );
		}
		return (bool) update_option( $key, $value, false );
	}

	/**
	 * @param array $base  Base.
	 * @param array $over  Overlay.
	 * @return array
	 */
	private function merge_deep( array $base, array $over ) {
		foreach ( $over as $k => $v ) {
			if ( is_array( $v ) && isset( $base[ $k ] ) && is_array( $base[ $k ] ) ) {
				$base[ $k ] = $this->merge_deep( $base[ $k ], $v );
			} else {
				$base[ $k ] = $v;
			}
		}
		return $base;
	}
}
