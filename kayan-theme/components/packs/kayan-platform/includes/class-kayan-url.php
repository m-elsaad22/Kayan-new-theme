<?php
/**
 * URL Architecture helper.
 *
 * Phase 1 ACTIVE mode = LEGACY (existing kayan-i18n builders):
 *   / , /sa/ , /en/ , /sa/en/ …
 *
 * TARGET mode (not activated — documented for Phase 2+ routing):
 *   / , /sa/ , /en/ , /en/sa/ …
 *
 * This class never registers rewrite rules.
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
	 * Active URL mode. Phase 1 is always legacy unless explicitly filtered
	 * (filters are for tests/staging — production must keep legacy).
	 *
	 * @return string
	 */
	public function get_mode() {
		$mode = KAYAN_PLATFORM_URL_MODE_LEGACY;

		/**
		 * Do not switch to language_first until a dedicated routing phase lands.
		 *
		 * @param string $mode URL mode.
		 */
		$mode = apply_filters( 'kayan_platform_url_mode', $mode );

		if ( KAYAN_PLATFORM_URL_MODE_LANG_FIRST === $mode ) {
			// Hard-guard in Phase 1: never activate language-first in this release.
			if ( ! defined( 'KAYAN_PLATFORM_ALLOW_LANG_FIRST' ) || ! KAYAN_PLATFORM_ALLOW_LANG_FIRST ) {
				return KAYAN_PLATFORM_URL_MODE_LEGACY;
			}
		}

		return $mode;
	}

	/**
	 * Target architecture documentation (not used for live URLs in Phase 1).
	 *
	 * @return array<string,mixed>
	 */
	public function get_target_architecture() {
		return array(
			'default_country'  => $this->countries->get_default(),
			'default_language' => $this->languages->get_default(),
			'patterns'         => array(
				'ar_default' => '/',
				'ar_country' => '/{country}/',
				'en_default' => '/en/',
				'en_country' => '/en/{country}/',
				'cpt'        => '/{country?}/{lang?}/{post_type}/{slug}/',
			),
			'examples'         => array(
				'/',
				'/services/pest-control/',
				'/sa/services/pest-control/',
				'/qa/services/pest-control/',
				'/eg/services/pest-control/',
				'/en/services/pest-control/',
				'/en/sa/services/pest-control/',
			),
			'active_mode'      => $this->get_mode(),
			'note'             => 'Target patterns activate in a future routing phase. Phase 1 preserves current /{country}/en/ URLs.',
		);
	}

	/**
	 * Build a localized URL using the ACTIVE (legacy) builder — no URL changes.
	 *
	 * @param string $country Country code.
	 * @param string $lang    Language code.
	 * @param string $slug    Path slug or '/'.
	 * @return string
	 */
	public function build( $country, $lang, $slug = '/' ) {
		$country = $this->countries->normalize( $country );
		$lang    = $this->languages->normalize( $lang );

		if ( function_exists( 'kayan_i18n_build_url' ) ) {
			return kayan_i18n_build_url( $country, $lang, $slug );
		}

		return $this->build_legacy_fallback( $country, $lang, $slug );
	}

	/**
	 * Future language-first builder (unused in Phase 1 production).
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
	 * @param string $country Country.
	 * @param string $lang    Lang.
	 * @param string $slug    Slug.
	 * @return string
	 */
	private function build_legacy_fallback( $country, $lang, $slug = '/' ) {
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

		$prefix = $country_path ? $country_path : '';
		return user_trailingslashit( $base . trim( $prefix . $slug, '/' ) );
	}
}
