<?php
/**
 * Country Engine — extensible registry for unlimited countries.
 *
 * Reuses kayan_i18n_get_countries() as the single source of truth.
 * New countries are added via the existing filter `kayan_i18n_countries`
 * (and/or `kayan_platform_countries`) — never by copying arrays.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Country_Engine {

	/**
	 * Default / root country (no URL prefix).
	 *
	 * @var string
	 */
	const DEFAULT_CODE = 'ae';

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function all() {
		$countries = array();

		if ( function_exists( 'kayan_i18n_get_countries' ) ) {
			$countries = kayan_i18n_get_countries();
		}

		if ( ! is_array( $countries ) || empty( $countries ) ) {
			$countries = $this->fallback_countries();
		}

		/**
		 * Platform-level extension point (runs after i18n filter).
		 *
		 * @param array $countries Country registry.
		 */
		$countries = apply_filters( 'kayan_platform_countries', $countries );

		return is_array( $countries ) ? $countries : array();
	}

	/**
	 * @param string $code Country code.
	 * @return bool
	 */
	public function exists( $code ) {
		$code = $this->normalize( $code );
		$all  = $this->all();
		return $code !== '' && isset( $all[ $code ] );
	}

	/**
	 * @param string|null $code Country code.
	 * @return array<string,mixed>
	 */
	public function get( $code = null ) {
		if ( null === $code || '' === $code ) {
			$code = $this->get_default();
		}
		$code = $this->normalize( $code );
		$all  = $this->all();

		if ( isset( $all[ $code ] ) ) {
			return $all[ $code ];
		}

		$default = $this->get_default();
		return isset( $all[ $default ] ) ? $all[ $default ] : array();
	}

	/**
	 * @return string
	 */
	public function get_default() {
		$default = self::DEFAULT_CODE;

		if ( function_exists( 'yc_get_option' ) ) {
			$stored = yc_get_option( 'kayan_i18n_default_country' );
			if ( is_string( $stored ) && $this->exists( $stored ) ) {
				$default = $this->normalize( $stored );
			}
		}

		/**
		 * @param string $default Default country code.
		 */
		$default = apply_filters( 'kayan_platform_default_country', $default );

		return $this->exists( $default ) ? $this->normalize( $default ) : self::DEFAULT_CODE;
	}

	/**
	 * URL path prefix for a country ('' for root/default).
	 *
	 * @param string|null $code Country code.
	 * @return string
	 */
	public function get_path( $code = null ) {
		if ( function_exists( 'kayan_i18n_get_country_path' ) && null !== $code ) {
			return (string) kayan_i18n_get_country_path( $code );
		}

		$data = $this->get( $code );
		return isset( $data['path'] ) ? (string) $data['path'] : '';
	}

	/**
	 * Register / merge a country into the i18n registry via filter (runtime).
	 * Persisted registration belongs in a later admin phase.
	 *
	 * @param string               $code Country code (iso-like).
	 * @param array<string,mixed>  $data Country data.
	 * @return void
	 */
	public function register( $code, array $data ) {
		$code = $this->normalize( $code );
		if ( '' === $code ) {
			return;
		}

		add_filter(
			'kayan_i18n_countries',
			static function ( $countries ) use ( $code, $data ) {
				if ( ! is_array( $countries ) ) {
					$countries = array();
				}
				$countries[ $code ] = array_merge(
					isset( $countries[ $code ] ) && is_array( $countries[ $code ] ) ? $countries[ $code ] : array(),
					$data
				);
				return $countries;
			},
			20
		);
	}

	/**
	 * @param string $code Raw code.
	 * @return string
	 */
	public function normalize( $code ) {
		$code = strtolower( sanitize_key( (string) $code ) );
		return $code;
	}

	/**
	 * Minimal fallback if kayan-i18n is disabled/missing — same codes, no duplication of logic paths in callers.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	private function fallback_countries() {
		return array(
			'ae' => array( 'path' => '', 'label_ar' => 'الإمارات', 'label_en' => 'UAE' ),
			'sa' => array( 'path' => '/sa', 'label_ar' => 'السعودية', 'label_en' => 'Saudi Arabia' ),
			'qa' => array( 'path' => '/qa', 'label_ar' => 'قطر', 'label_en' => 'Qatar' ),
			'kw' => array( 'path' => '/kw', 'label_ar' => 'الكويت', 'label_en' => 'Kuwait' ),
			'bh' => array( 'path' => '/bh', 'label_ar' => 'البحرين', 'label_en' => 'Bahrain' ),
			'om' => array( 'path' => '/om', 'label_ar' => 'عمان', 'label_en' => 'Oman' ),
			'eg' => array( 'path' => '/eg', 'label_ar' => 'مصر', 'label_en' => 'Egypt' ),
		);
	}
}
