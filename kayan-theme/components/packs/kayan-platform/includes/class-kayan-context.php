<?php
/**
 * Request Context — single resolved {country, language} per request.
 *
 * Reads from existing kayan-i18n query vars / helpers.
 * Does NOT register rewrite rules (no duplication, no URL changes).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Context {

	/** @var Kayan_Country_Engine */
	private $countries;

	/** @var Kayan_Language_Engine */
	private $languages;

	/** @var array{country:string,language:string}|null */
	private $resolved = null;

	public function __construct( Kayan_Country_Engine $countries, Kayan_Language_Engine $languages ) {
		$this->countries = $countries;
		$this->languages = $languages;
	}

	/**
	 * Resolve once per request.
	 *
	 * @return array{country:string,language:string}
	 */
	public function get() {
		if ( null !== $this->resolved ) {
			return $this->resolved;
		}

		$country  = $this->resolve_country();
		$language = $this->resolve_language();

		$this->resolved = array(
			'country'  => $country,
			'language' => $language,
		);

		/**
		 * @param array $context Resolved context.
		 */
		$this->resolved = apply_filters( 'kayan_platform_context', $this->resolved );

		return $this->resolved;
	}

	/**
	 * @return string
	 */
	public function country() {
		$ctx = $this->get();
		return $ctx['country'];
	}

	/**
	 * @return string
	 */
	public function language() {
		$ctx = $this->get();
		return $ctx['language'];
	}

	/**
	 * @return bool
	 */
	public function is_default_country() {
		return $this->country() === $this->countries->get_default();
	}

	/**
	 * @return bool
	 */
	public function is_default_language() {
		return $this->language() === $this->languages->get_default();
	}

	/**
	 * Clear memoization (tests / unusual sub-requests).
	 *
	 * @return void
	 */
	public function reset() {
		$this->resolved = null;
	}

	/**
	 * @return string
	 */
	private function resolve_country() {
		if ( function_exists( 'kayan_i18n_get_country' ) ) {
			$code = kayan_i18n_get_country();
			if ( $this->countries->exists( $code ) ) {
				return $this->countries->normalize( $code );
			}
		}

		$qv = get_query_var( 'kayan_country' );
		if ( is_string( $qv ) && $this->countries->exists( $qv ) ) {
			return $this->countries->normalize( $qv );
		}

		return $this->countries->get_default();
	}

	/**
	 * @return string
	 */
	private function resolve_language() {
		if ( function_exists( 'kayan_i18n_get_lang' ) ) {
			$code = kayan_i18n_get_lang();
			if ( $this->languages->exists( $code ) ) {
				return $this->languages->normalize( $code );
			}
		}

		$qv = get_query_var( 'kayan_lang' );
		if ( is_string( $qv ) && $this->languages->exists( $qv ) ) {
			return $this->languages->normalize( $qv );
		}

		return $this->languages->get_default();
	}
}
