<?php
/**
 * SEO Bridge — Rank Math compatible head layer for Phase 1.
 *
 * Ownership rules:
 * - Rank Math (when active): title, meta description, canonical, OG, Twitter, sitemaps.
 * - KAYAN Platform: safe hreflang / x-default only where URLs already resolve.
 * - Country analytics / GSC verification: output ONLY when profile values are set.
 *
 * Phase 1 does NOT emit competing JSON-LD, XML sitemaps, or duplicate canonical tags.
 * Phase 1 does NOT emit full country-matrix hreflang (CPT routing not ready — would risk 404 alternates).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_SEO_Bridge {

	/** @var Kayan_Context */
	private $context;

	/** @var Kayan_Country_Settings */
	private $settings;

	/** @var Kayan_URL */
	private $urls;

	/** @var Kayan_Country_Engine */
	private $countries;

	/** @var Kayan_Language_Engine */
	private $languages;

	public function __construct(
		Kayan_Context $context,
		Kayan_Country_Settings $settings,
		Kayan_URL $urls,
		Kayan_Country_Engine $countries,
		Kayan_Language_Engine $languages
	) {
		$this->context   = $context;
		$this->settings  = $settings;
		$this->urls      = $urls;
		$this->countries = $countries;
		$this->languages = $languages;
	}

	/**
	 * @return void
	 */
	public function register() {
		// Prefer platform-safe renderer. Do not hook legacy kayan_i18n_render_hreflang
		// globally — it can emit unresolved CPT/country alternates.
		if ( ! has_action( 'wp_head', array( $this, 'render_safe_hreflang' ) ) ) {
			add_action( 'wp_head', array( $this, 'render_safe_hreflang' ), 2 );
		}

		add_action( 'wp_head', array( $this, 'render_country_head_tags' ), 3 );

		add_filter( 'rank_math/frontend/canonical', array( $this, 'filter_rank_math_canonical' ), 20 );
		add_filter( 'rank_math/opengraph/facebook/og_locale', array( $this, 'filter_og_locale' ), 20 );
	}

	/**
	 * @return bool
	 */
	public function is_rank_math_active() {
		return class_exists( 'RankMath' ) || class_exists( 'RankMath\\RankMath' );
	}

	/**
	 * Emit ar / en / x-default only for request shapes already supported by kayan-i18n:
	 * - Home / front page (all countries)
	 * - Singular `post` (all countries)
	 *
	 * Skips CPT/page archives until the routing phase can guarantee resolution.
	 *
	 * @return void
	 */
	public function render_safe_hreflang() {
		if ( function_exists( 'kayan_i18n_is_enabled' ) && ! kayan_i18n_is_enabled() ) {
			return;
		}

		if ( ! $this->is_safe_hreflang_context() ) {
			return;
		}

		$country = $this->context->country();
		$slug    = $this->safe_slug_for_hreflang();

		$ar = $this->urls->build( $country, 'ar', $slug );
		$en = $this->urls->build( $country, 'en', $slug );

		if ( ! $ar || ! $en || $ar === $en ) {
			return;
		}

		echo '<link rel="alternate" hreflang="ar" href="' . esc_url( $ar ) . '" />' . "\n";
		echo '<link rel="alternate" hreflang="en" href="' . esc_url( $en ) . '" />' . "\n";
		echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $ar ) . '" />' . "\n";
	}

	/**
	 * GTM / GSC verification from country profile — no-op when empty (no HTML change).
	 *
	 * @return void
	 */
	public function render_country_head_tags() {
		$country = $this->context->country();
		$gtm     = (string) $this->settings->get( 'analytics.gtm_id', $country, '' );
		$gsc     = (string) $this->settings->get( 'analytics.gsc_verification', $country, '' );

		if ( $gsc ) {
			echo '<meta name="google-site-verification" content="' . esc_attr( $gsc ) . '" />' . "\n";
		}

		if ( $gtm && preg_match( '/^GTM-[A-Z0-9]+$/i', $gtm ) ) {
			echo "<!-- KAYAN Country GTM -->\n";
			echo '<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);})(window,document,\'script\',\'dataLayer\',\'' . esc_js( $gtm ) . '\');</script>' . "\n";
		}
	}

	/**
	 * @param string $canonical Canonical URL.
	 * @return string
	 */
	public function filter_rank_math_canonical( $canonical ) {
		$host = (string) $this->settings->get( 'seo.canonical_host', $this->context->country(), '' );
		if ( '' === $host || ! is_string( $canonical ) || '' === $canonical ) {
			return $canonical;
		}

		$parts = wp_parse_url( $canonical );
		if ( empty( $parts['host'] ) ) {
			return $canonical;
		}

		$host   = preg_replace( '#^https?://#', '', $host );
		$host   = untrailingslashit( $host );
		$path   = isset( $parts['path'] ) ? $parts['path'] : '/';
		$scheme = isset( $parts['scheme'] ) ? $parts['scheme'] : 'https';

		return $scheme . '://' . $host . $path;
	}

	/**
	 * @param string $locale OG locale.
	 * @return string
	 */
	public function filter_og_locale( $locale ) {
		return ( 'en' === $this->context->language() ) ? 'en_US' : 'ar_AR';
	}

	/**
	 * Full matrix data provider for a future routing/sitemap phase (no head output).
	 *
	 * @return array<int,array<string,string>>
	 */
	public function get_hreflang_alternates() {
		$slug    = $this->safe_slug_for_hreflang();
		$items   = array();
		$default = null;

		foreach ( $this->countries->all() as $code => $data ) {
			foreach ( array_keys( $this->languages->all() ) as $lang ) {
				$url     = $this->urls->build( $code, $lang, $slug );
				$items[] = array(
					'hreflang' => $this->languages->get_hreflang( $lang ),
					'href'     => $url,
					'country'  => $code,
					'language' => $lang,
				);
				if ( 'ar' === $lang && $code === $this->countries->get_default() ) {
					$default = $url;
				}
			}
		}

		if ( $default ) {
			$items[] = array(
				'hreflang' => 'x-default',
				'href'     => $default,
				'country'  => $this->countries->get_default(),
				'language' => 'ar',
			);
		}

		/**
		 * @param array $items Alternates.
		 */
		return apply_filters( 'kayan_platform_hreflang_alternates', $items );
	}

	/**
	 * @return bool
	 */
	private function is_safe_hreflang_context() {
		if ( is_front_page() || is_home() ) {
			return true;
		}
		if ( is_singular( 'post' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * @return string
	 */
	private function safe_slug_for_hreflang() {
		if ( is_front_page() || is_home() ) {
			return '/';
		}
		if ( is_singular( 'post' ) ) {
			$post = get_queried_object();
			if ( $post && ! empty( $post->post_name ) ) {
				return (string) $post->post_name;
			}
		}
		return '/';
	}
}
