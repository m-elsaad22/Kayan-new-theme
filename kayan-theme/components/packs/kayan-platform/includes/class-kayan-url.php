<?php
/**
 * URL Architecture helper.
 *
 * Phase 2 ACTIVE (canonical) mode = LANGUAGE_FIRST:
 *   / , /sa/ , /en/ , /en/sa/ …
 *
 * Legacy /{country}/en/… is not built anymore; Country Router 301s those requests.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_URL {

	/** @var Kayan_Country_Engine */
	private $countries;

	/** @var Kayan_Language_Engine */
	private $languages;

	public function __construct( Kayan_Country_Engine $countries, Kayan_Language_Engine $languages ) {
		$this->countries = $countries;
		$this->languages = $languages;
	}

	/**
	 * Canonical URL mode.
	 *
	 * @return string
	 */
	public function get_mode() {
		$mode = KAYAN_PLATFORM_URL_MODE_LANG_FIRST;

		/**
		 * @param string $mode URL mode.
		 */
		$mode = apply_filters( 'kayan_platform_url_mode', $mode );

		return ( KAYAN_PLATFORM_URL_MODE_LEGACY === $mode )
			? KAYAN_PLATFORM_URL_MODE_LEGACY
			: KAYAN_PLATFORM_URL_MODE_LANG_FIRST;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function get_target_architecture() {
		return array(
			'default_country'  => $this->countries->get_default(),
			'default_language' => $this->languages->get_default(),
			'canonical_mode'   => KAYAN_PLATFORM_URL_MODE_LANG_FIRST,
			'active_mode'      => $this->get_mode(),
			'patterns'         => array(
				'ar_default' => '/',
				'ar_country' => '/{country}/',
				'en_default' => '/en/',
				'en_country' => '/en/{country}/',
				'cpt'        => '/{lang?}/{country?}/{post_type}/{slug}/',
			),
			'examples'         => array(
				'/',
				'/sa/',
				'/qa/',
				'/en/',
				'/en/sa/',
				'/services/pest-control/',
				'/sa/services/pest-control/',
				'/en/services/pest-control/',
				'/en/sa/services/pest-control/',
			),
			'legacy_redirect'  => '/{country}/en/{path} → 301 → /en/{country}/{path}',
		);
	}

	/**
	 * Build a localized URL using the active canonical builder.
	 *
	 * @param string $country Country code.
	 * @param string $lang    Language code.
	 * @param string $slug    Path slug or '/'.
	 * @return string
	 */
	public function build( $country, $lang, $slug = '/' ) {
		$country = $this->countries->normalize( $country );
		$lang    = $this->languages->normalize( $lang );

		if ( KAYAN_PLATFORM_URL_MODE_LEGACY === $this->get_mode() ) {
			return $this->build_legacy( $country, $lang, $slug );
		}

		return $this->build_language_first( $country, $lang, $slug );
	}

	/**
	 * Language-first canonical builder.
	 *
	 * @param string $country Country code.
	 * @param string $lang    Language code.
	 * @param string $slug    Path slug.
	 * @return string
	 */
	public function build_language_first( $country, $lang, $slug = '/' ) {
		$base    = trailingslashit( home_url() );
		$country = $this->countries->normalize( $country );
		$lang    = $this->languages->normalize( $lang );
		$slug    = ( $slug && '/' !== $slug ) ? trim( (string) $slug, '/' ) : '';
		$default = $this->countries->get_default();
		$parts   = array();

		if ( $lang !== $this->languages->get_default() ) {
			$parts[] = $lang;
		}
		if ( $country !== $default ) {
			$parts[] = $country;
		}
		if ( '' !== $slug ) {
			$parts[] = $slug;
		}

		$path = implode( '/', $parts );
		return user_trailingslashit( $base . $path );
	}

	/**
	 * Legacy builder (kept for emergency rollback via filter only).
	 *
	 * @param string $country Country.
	 * @param string $lang    Lang.
	 * @param string $slug    Slug.
	 * @return string
	 */
	public function build_legacy( $country, $lang, $slug = '/' ) {
		$base         = trailingslashit( home_url() );
		$country_path = trim( $this->countries->get_path( $country ), '/' );
		$slug         = ( $slug && '/' !== $slug ) ? '/' . trim( (string) $slug, '/' ) : '';

		if ( 'en' === $lang ) {
			$prefix = $country_path ? $country_path . '/en' : 'en';
			return user_trailingslashit( $base . trim( $prefix . $slug, '/' ) );
		}

		if ( '' === $slug || '/' === $slug ) {
			return user_trailingslashit( $base . $country_path );
		}

		return user_trailingslashit( $base . trim( $country_path . $slug, '/' ) );
	}
}
