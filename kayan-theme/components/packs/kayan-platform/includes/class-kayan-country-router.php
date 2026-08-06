<?php
/**
 * Country Routing Engine (Phase 2)
 *
 * Canonical URL mode: language-first
 *   / , /sa/ , /en/ , /en/sa/ , /en/sa/services/{slug}/ …
 *
 * Legacy /{country}/en/… → 301 to /en/{country}/…
 *
 * Owns rewrite rules when kayan_platform_owns_routing() is true
 * (kayan-i18n rewrite registration is gated off — no duplication).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Country_Router {

	const REWRITE_VERSION = '2.5.1';

	/** @var Kayan_Country_Engine */
	private $countries;

	/** @var Kayan_Language_Engine */
	private $languages;

	/** @var Kayan_URL */
	private $urls;

	/** @var Kayan_Programmatic_SEO|null */
	private $programmatic;

	public function __construct(
		Kayan_Country_Engine $countries,
		Kayan_Language_Engine $languages,
		Kayan_URL $urls,
		$programmatic = null
	) {
		$this->countries    = $countries;
		$this->languages    = $languages;
		$this->urls         = $urls;
		$this->programmatic = $programmatic;
	}

	/**
	 * @return void
	 */
	public function register() {
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
		add_action( 'init', array( $this, 'register_rewrites' ), 6 );
		add_action( 'init', array( $this, 'maybe_flush_rewrites' ), 99 );
		add_action( 'template_redirect', array( $this, 'redirect_legacy_urls' ), 0 );
	}

	/**
	 * @param string[] $vars Query vars.
	 * @return string[]
	 */
	public function register_query_vars( $vars ) {
		$vars[] = 'kayan_route';
		$vars[] = 'kayan_public_slug';
		$vars[] = 'kayan_entity';
		return $vars;
	}

	/**
	 * @return void
	 */
	public function register_rewrites() {
		if ( ! kayan_platform_owns_routing() ) {
			return;
		}
		if ( function_exists( 'kayan_i18n_is_enabled' ) && ! kayan_i18n_is_enabled() ) {
			return;
		}

		$country_alt = $this->country_regex_alt();
		if ( '' === $country_alt ) {
			return;
		}

		$cpt_map = $this->get_post_type_rewrite_map();
		$tax_map = $this->get_taxonomy_rewrite_map();

		// ── Language-first country homes ──
		add_rewrite_rule( '^en/?$', 'index.php?kayan_lang=en&kayan_route=home', 'top' );
		add_rewrite_rule(
			'^en/(' . $country_alt . ')/?$',
			'index.php?kayan_lang=en&kayan_country=$matches[1]&kayan_route=home',
			'top'
		);
		add_rewrite_rule(
			'^(' . $country_alt . ')/?$',
			'index.php?kayan_country=$matches[1]&kayan_route=home',
			'top'
		);

		// ── CPT archives + singulars ──
		foreach ( $cpt_map as $post_type => $slug ) {
			$slug = preg_quote( $slug, '#' );

			// /en/{country}/{cpt}/
			add_rewrite_rule(
				'^en/(' . $country_alt . ')/' . $slug . '/?$',
				'index.php?kayan_lang=en&kayan_country=$matches[1]&post_type=' . $post_type . '&kayan_route=archive',
				'top'
			);
			// /en/{country}/{cpt}/{name}/
			add_rewrite_rule(
				'^en/(' . $country_alt . ')/' . $slug . '/([^/]+)/?$',
				'index.php?kayan_lang=en&kayan_country=$matches[1]&post_type=' . $post_type . '&name=$matches[2]&kayan_public_slug=$matches[2]&kayan_route=singular',
				'top'
			);
			// /en/{cpt}/
			add_rewrite_rule(
				'^en/' . $slug . '/?$',
				'index.php?kayan_lang=en&post_type=' . $post_type . '&kayan_route=archive',
				'top'
			);
			// /en/{cpt}/{name}/
			add_rewrite_rule(
				'^en/' . $slug . '/([^/]+)/?$',
				'index.php?kayan_lang=en&post_type=' . $post_type . '&name=$matches[1]&kayan_public_slug=$matches[1]&kayan_route=singular',
				'top'
			);
			// /{country}/{cpt}/
			add_rewrite_rule(
				'^(' . $country_alt . ')/' . $slug . '/?$',
				'index.php?kayan_country=$matches[1]&post_type=' . $post_type . '&kayan_route=archive',
				'top'
			);
			// /{country}/{cpt}/{name}/
			add_rewrite_rule(
				'^(' . $country_alt . ')/' . $slug . '/([^/]+)/?$',
				'index.php?kayan_country=$matches[1]&post_type=' . $post_type . '&name=$matches[2]&kayan_public_slug=$matches[2]&kayan_route=singular',
				'top'
			);
		}

		// ── Taxonomies ──
		foreach ( $tax_map as $taxonomy => $slug ) {
			$slug = preg_quote( $slug, '#' );

			add_rewrite_rule(
				'^en/(' . $country_alt . ')/' . $slug . '/([^/]+)/?$',
				'index.php?kayan_lang=en&kayan_country=$matches[1]&' . $taxonomy . '=$matches[2]&kayan_route=tax',
				'top'
			);
			add_rewrite_rule(
				'^en/' . $slug . '/([^/]+)/?$',
				'index.php?kayan_lang=en&' . $taxonomy . '=$matches[1]&kayan_route=tax',
				'top'
			);
			add_rewrite_rule(
				'^(' . $country_alt . ')/' . $slug . '/([^/]+)/?$',
				'index.php?kayan_country=$matches[1]&' . $taxonomy . '=$matches[2]&kayan_route=tax',
				'top'
			);
		}

		// ── Programmatic entity segments (future-ready; no generation yet) ──
		if ( $this->programmatic ) {
			foreach ( $this->programmatic->get_routable_entity_types() as $entity_id => $entity ) {
				if ( empty( $entity['segment'] ) || empty( $entity['enabled'] ) ) {
					continue;
				}
				// Skip entities already covered by CPT/tax maps.
				if ( ! empty( $entity['post_type'] ) || ! empty( $entity['taxonomy'] ) ) {
					continue;
				}
				$seg = preg_quote( (string) $entity['segment'], '#' );
				add_rewrite_rule(
					'^en/(' . $country_alt . ')/' . $seg . '/([^/]+)/?$',
					'index.php?kayan_lang=en&kayan_country=$matches[1]&kayan_entity=' . $entity_id . '&kayan_public_slug=$matches[2]&kayan_route=entity',
					'top'
				);
				add_rewrite_rule(
					'^(' . $country_alt . ')/' . $seg . '/([^/]+)/?$',
					'index.php?kayan_country=$matches[1]&kayan_entity=' . $entity_id . '&kayan_public_slug=$matches[2]&kayan_route=entity',
					'top'
				);
			}
		}

		// ── Posts / pages catch-all (after reserved CPT/tax/country) ──
		add_rewrite_rule(
			'^en/(' . $country_alt . ')/([^/]+)/?$',
			'index.php?kayan_lang=en&kayan_country=$matches[1]&name=$matches[2]&kayan_public_slug=$matches[2]&kayan_route=singular',
			'top'
		);
		add_rewrite_rule(
			'^en/([^/]+)/?$',
			'index.php?kayan_lang=en&name=$matches[1]&kayan_public_slug=$matches[1]&kayan_route=singular',
			'top'
		);
		add_rewrite_rule(
			'^(' . $country_alt . ')/([^/]+)/?$',
			'index.php?kayan_country=$matches[1]&name=$matches[2]&kayan_public_slug=$matches[2]&kayan_route=singular',
			'top'
		);
	}

	/**
	 * Legacy /{country}/en/… → canonical /en/{country}/…
	 *
	 * @return void
	 */
	public function redirect_legacy_urls() {
		if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}
		if ( ! kayan_platform_owns_routing() ) {
			return;
		}

		$path = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH );
		$path = is_string( $path ) ? $path : '/';

		$country_alt = $this->country_regex_alt();
		if ( '' === $country_alt ) {
			return;
		}

		if ( ! preg_match( '#^/(' . $country_alt . ')/en(/.*)?$#', $path, $m ) ) {
			return;
		}

		$country = $m[1];
		$rest    = isset( $m[2] ) ? $m[2] : '/';
		if ( '' === $rest ) {
			$rest = '/';
		}

		$target_path = '/en/' . $country . ( '/' === $rest ? '/' : $rest );
		$target      = home_url( $target_path );

		$query = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY );
		if ( is_string( $query ) && '' !== $query ) {
			$target .= ( strpos( $target, '?' ) === false ? '?' : '&' ) . $query;
		}

		wp_safe_redirect( $target, 301 );
		exit;
	}

	/**
	 * @return void
	 */
	public function maybe_flush_rewrites() {
		if ( ! kayan_platform_owns_routing() ) {
			return;
		}
		$stored = get_option( 'kayan_platform_rewrite_version' );
		if ( self::REWRITE_VERSION === $stored ) {
			return;
		}
		flush_rewrite_rules( false );
		update_option( 'kayan_platform_rewrite_version', self::REWRITE_VERSION, false );
	}

	/**
	 * post_type => rewrite slug
	 *
	 * @return array<string,string>
	 */
	public function get_post_type_rewrite_map() {
		$map = array(
			'services'     => 'services',
			'reviews'      => 'reviews',
			'faqs'         => 'faqs',
			'pricing'      => 'pricing',
			'portfolio'    => 'portfolio',
			'before_after' => 'before-after',
		);

		/**
		 * @param array<string,string> $map Map.
		 */
		return apply_filters( 'kayan_platform_post_type_rewrite_map', $map );
	}

	/**
	 * taxonomy => rewrite slug
	 *
	 * @return array<string,string>
	 */
	public function get_taxonomy_rewrite_map() {
		$map = array(
			'cities'             => 'city',
			'service_categories' => 'service-category',
			'category'           => 'category',
		);

		/**
		 * @param array<string,string> $map Map.
		 */
		return apply_filters( 'kayan_platform_taxonomy_rewrite_map', $map );
	}

	/**
	 * Non-default country codes as regex alternation.
	 *
	 * @return string
	 */
	private function country_regex_alt() {
		$codes = array();
		$default = $this->countries->get_default();
		foreach ( array_keys( $this->countries->all() ) as $code ) {
			$code = $this->countries->normalize( $code );
			if ( '' === $code || $code === $default ) {
				continue;
			}
			$codes[] = preg_quote( $code, '#' );
		}
		/**
		 * @param string[] $codes Country codes.
		 */
		$codes = apply_filters( 'kayan_platform_routable_country_codes', $codes );
		return implode( '|', array_filter( (array) $codes ) );
	}
}
