<?php
/**
 * SEO Bridge — Rank Math is the ONLY SEO engine.
 *
 * KAYAN never prints competing:
 * - Meta titles / descriptions
 * - Canonical
 * - Schema / JSON-LD
 * - Open Graph / Twitter Cards
 * - XML Sitemaps
 * - Breadcrumbs
 * - hreflang link tags (provided to Rank Math via filters/data API)
 *
 * KAYAN only extends Rank Math through APIs/filters + supplies alternate URL data.
 * Country GTM remains analytics (not SEO metadata).
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

	/** @var Kayan_Country_Router|null */
	private $router;

	/** @var Kayan_Content_Locale|null */
	private $locale;

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
	 * Optional wiring after router/locale exist.
	 *
	 * @param Kayan_Country_Router $router Router.
	 * @param Kayan_Content_Locale $locale Locale.
	 * @return void
	 */
	public function set_routing_services( Kayan_Country_Router $router, Kayan_Content_Locale $locale ) {
		$this->router = $router;
		$this->locale = $locale;
	}

	/**
	 * @return void
	 */
	public function register() {
		// Analytics only (not SEO metadata).
		add_action( 'wp_head', array( $this, 'render_country_gtm' ), 3 );

		// Extend Rank Math — never replace it.
		add_filter( 'rank_math/frontend/canonical', array( $this, 'filter_rank_math_canonical' ), 20 );
		add_filter( 'rank_math/opengraph/facebook/og_locale', array( $this, 'filter_og_locale' ), 20 );
		add_filter( 'rank_math/frontend/title', array( $this, 'filter_rank_math_title' ), 20 );
		add_filter( 'rank_math/frontend/description', array( $this, 'filter_rank_math_description' ), 20 );

		// Hreflang data for Rank Math (filter names vary by RM version — both attempted).
		add_filter( 'rank_math/frontend/hreflang', array( $this, 'filter_rank_math_hreflang' ), 20 );
		add_filter( 'rank_math/front/hreflang', array( $this, 'filter_rank_math_hreflang' ), 20 );
	}

	/**
	 * @return bool
	 */
	public function is_rank_math_active() {
		return class_exists( 'RankMath' ) || class_exists( 'RankMath\\RankMath' );
	}

	/**
	 * Country GTM only — not an SEO tag.
	 *
	 * @return void
	 */
	public function render_country_gtm() {
		$gtm = (string) $this->settings->get( 'analytics.gtm_id', $this->context->country(), '' );
		if ( ! $gtm || ! preg_match( '/^GTM-[A-Z0-9]+$/i', $gtm ) ) {
			return;
		}
		echo "<!-- KAYAN Country GTM -->\n";
		echo '<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);})(window,document,\'script\',\'dataLayer\',\'' . esc_js( $gtm ) . '\');</script>' . "\n";
	}

	/**
	 * @param string $canonical Canonical.
	 * @return string
	 */
	public function filter_rank_math_canonical( $canonical ) {
		// Ensure canonical uses language-first URLs when RM returns legacy host/path.
		$host = (string) $this->settings->get( 'seo.canonical_host', $this->context->country(), '' );
		if ( $host && is_string( $canonical ) && $canonical ) {
			$parts  = wp_parse_url( $canonical );
			$path   = isset( $parts['path'] ) ? $parts['path'] : '/';
			$scheme = isset( $parts['scheme'] ) ? $parts['scheme'] : 'https';
			$host   = untrailingslashit( preg_replace( '#^https?://#', '', $host ) );
			$canonical = $scheme . '://' . $host . $path;
		}

		// Rewrite legacy /{cc}/en/ canonicals to /en/{cc}/.
		if ( is_string( $canonical ) && $canonical ) {
			$path = wp_parse_url( $canonical, PHP_URL_PATH );
			if ( is_string( $path ) && preg_match( '#^/([a-z]{2})/en(/.*)?$#', $path, $m ) ) {
				$rest = isset( $m[2] ) ? $m[2] : '/';
				$canonical = home_url( '/en/' . $m[1] . ( $rest ? $rest : '/' ) );
			}
		}

		return $canonical;
	}

	/**
	 * @param string $locale Locale.
	 * @return string
	 */
	public function filter_og_locale( $locale ) {
		return ( 'en' === $this->context->language() ) ? 'en_US' : 'ar_AR';
	}

	/**
	 * Feed Rank Math title from locale SEO meta when present (no theme <title> output).
	 *
	 * @param string $title Title.
	 * @return string
	 */
	public function filter_rank_math_title( $title ) {
		if ( ! is_singular() || ! $this->locale ) {
			return $title;
		}
		$seo = $this->locale->get_country_seo( get_queried_object_id(), $this->context->country() );
		if ( ! empty( $seo['title'] ) ) {
			return $seo['title'];
		}
		if ( function_exists( 'kayan_i18n_filter_seo_title' ) ) {
			return kayan_i18n_filter_seo_title( $title );
		}
		return $title;
	}

	/**
	 * @param string $description Description.
	 * @return string
	 */
	public function filter_rank_math_description( $description ) {
		if ( ! is_singular() || ! $this->locale ) {
			return $description;
		}
		$seo = $this->locale->get_country_seo( get_queried_object_id(), $this->context->country() );
		if ( ! empty( $seo['description'] ) ) {
			return $seo['description'];
		}
		if ( function_exists( 'kayan_i18n_filter_seo_description' ) ) {
			return kayan_i18n_filter_seo_description( $description );
		}
		return $description;
	}

	/**
	 * Provide hreflang map to Rank Math. Does not echo <link> tags.
	 *
	 * @param mixed $hreflang Existing.
	 * @return array<string,string>
	 */
	public function filter_rank_math_hreflang( $hreflang ) {
		$map = $this->get_hreflang_map();
		if ( empty( $map ) ) {
			return is_array( $hreflang ) ? $hreflang : array();
		}
		return $map;
	}

	/**
	 * Full alternate map for current request (all countries × languages) when route is known.
	 *
	 * @return array<string,string> hreflang => url
	 */
	public function get_hreflang_map() {
		$path_slug = $this->current_public_path_slug();
		$map       = array();
		$default   = null;

		foreach ( $this->countries->all() as $code => $data ) {
			foreach ( array_keys( $this->languages->all() ) as $lang ) {
				$url = $this->urls->build( $code, $lang, $path_slug );
				$hl  = $this->languages->get_hreflang( $lang );
				// Use plain language codes for global alternates; country variants share language code
				// (Google accepts one URL per hreflang code — prefer current-country language pair + x-default).
				if ( $code === $this->context->country() ) {
					$map[ $hl ] = $url;
				}
				if ( 'ar' === $lang && $code === $this->countries->get_default() ) {
					$default = $url;
				}
			}
		}

		if ( $default ) {
			$map['x-default'] = $default;
		}

		/**
		 * @param array $map Hreflang map.
		 */
		return apply_filters( 'kayan_platform_hreflang_map', $map );
	}

	/**
	 * Expanded alternates (country+lang) for future Rank Math / sitemap consumers.
	 *
	 * @return array<int,array<string,string>>
	 */
	public function get_hreflang_alternates() {
		$path_slug = $this->current_public_path_slug();
		$items     = array();
		$default   = null;

		foreach ( $this->countries->all() as $code => $data ) {
			foreach ( array_keys( $this->languages->all() ) as $lang ) {
				$url     = $this->urls->build( $code, $lang, $path_slug );
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
	 * Public path after country/lang prefixes, including CPT/tax segments when singular/archive.
	 *
	 * @return string
	 */
	private function current_public_path_slug() {
		if ( is_front_page() || is_home() || 'home' === get_query_var( 'kayan_route' ) ) {
			return '/';
		}

		if ( is_singular() ) {
			$post = get_queried_object();
			if ( $post && isset( $post->post_type ) ) {
				$slug = $this->locale
					? $this->locale->get_public_slug( $post->ID )
					: $post->post_name;
				$map  = $this->router ? $this->router->get_post_type_rewrite_map() : array();
				if ( isset( $map[ $post->post_type ] ) ) {
					return trim( $map[ $post->post_type ] . '/' . $slug, '/' );
				}
				return $slug;
			}
		}

		if ( is_post_type_archive() ) {
			$pt  = get_query_var( 'post_type' );
			$pt  = is_array( $pt ) ? reset( $pt ) : $pt;
			$map = $this->router ? $this->router->get_post_type_rewrite_map() : array();
			if ( $pt && isset( $map[ $pt ] ) ) {
				return $map[ $pt ];
			}
		}

		if ( is_category() || is_tax() ) {
			$term = get_queried_object();
			if ( $term && isset( $term->taxonomy, $term->slug ) ) {
				$map = $this->router ? $this->router->get_taxonomy_rewrite_map() : array();
				$base = isset( $map[ $term->taxonomy ] ) ? $map[ $term->taxonomy ] : $term->taxonomy;
				return trim( $base . '/' . $term->slug, '/' );
			}
		}

		return '/';
	}
}
