<?php
/**
 * Content Resolution Engine (Phase 2)
 *
 * Resolves localized requests into the correct WP object using:
 * - Shared content (empty countries meta = all countries)
 * - Country-specific variants (countries list / variant group + public_slug)
 * - Language translations (translation_group + content_lang)
 *
 * No data migration: missing locale meta ⇒ legacy global Arabic visibility.
 * Single resolution path — no duplicated query logic per CPT.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Content_Resolver {

	/** @var Kayan_Context */
	private $context;

	/** @var Kayan_Content_Locale */
	private $locale;

	/** @var Kayan_Country_Router */
	private $router;

	public function __construct(
		Kayan_Context $context,
		Kayan_Content_Locale $locale,
		Kayan_Country_Router $router
	) {
		$this->context = $context;
		$this->locale  = $locale;
		$this->router  = $router;
	}

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'pre_get_posts', array( $this, 'resolve_main_query' ), 2 );
	}

	/**
	 * @param WP_Query $query Query.
	 * @return void
	 */
	public function resolve_main_query( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( ! kayan_platform_owns_routing() ) {
			return;
		}

		$route = (string) get_query_var( 'kayan_route' );

		// Reset memoized context so country/lang read current query vars.
		$this->context->reset();
		$lang    = $this->context->language();
		$country = $this->context->country();

		if ( '' === $route && ! get_query_var( 'kayan_country' ) && ! get_query_var( 'kayan_lang' ) ) {
			return;
		}

		if ( 'home' === $route || ( '' === $route && ( get_query_var( 'kayan_country' ) || get_query_var( 'kayan_lang' ) ) && ! get_query_var( 'name' ) && ! get_query_var( 'post_type' ) && ! $this->has_tax_query_var() ) ) {
			$query->set( 'post_type', '' );
			$query->is_home       = true;
			$query->is_front_page = true;
			$query->is_singular   = false;
			$query->is_archive    = false;
			$query->is_single     = false;
			$query->is_page       = false;
			return;
		}

		if ( 'archive' === $route ) {
			$post_type = get_query_var( 'post_type' );
			if ( is_array( $post_type ) ) {
				$post_type = reset( $post_type );
			}
			$post_type = sanitize_key( (string) $post_type );
			if ( $post_type ) {
				$query->set( 'post_type', $post_type );
				$query->set( 'name', '' );
				$query->is_post_type_archive = true;
				$query->is_archive           = true;
				$query->is_home              = false;
				$query->is_singular          = false;
				$this->apply_locale_constraints( $query, $country, $lang, false );
			}
			return;
		}

		if ( 'tax' === $route ) {
			$query->is_home = false;
			$this->apply_locale_constraints( $query, $country, $lang, false );
			return;
		}

		if ( 'entity' === $route ) {
			// Future programmatic entities — no generation in Phase 2.
			$query->set_404();
			status_header( 404 );
			return;
		}

		// Singular (explicit route or catch-all name).
		$slug = (string) get_query_var( 'kayan_public_slug' );
		if ( '' === $slug ) {
			$slug = (string) get_query_var( 'name' );
		}
		$slug = sanitize_title( $slug );
		if ( '' === $slug ) {
			return;
		}

		$post_type = get_query_var( 'post_type' );
		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}
		$post_type = sanitize_key( (string) $post_type );

		$types = $post_type
			? array( $post_type )
			: array( 'post', 'page' );

		$resolved = $this->resolve_singular( $slug, $types, $country, $lang );
		if ( ! $resolved ) {
			$query->set_404();
			status_header( 404 );
			return;
		}

		$query->set( 'post_type', $resolved->post_type );
		$query->set( 'name', '' );
		$query->set( 'pagename', '' );
		$query->set( 'kayan_public_slug', $slug );
		if ( 'page' === $resolved->post_type ) {
			$query->set( 'page_id', $resolved->ID );
			$query->set( 'p', 0 );
			$query->is_page   = true;
			$query->is_single = false;
		} else {
			$query->set( 'p', $resolved->ID );
			$query->set( 'page_id', 0 );
			$query->is_page   = false;
			$query->is_single = true;
		}
		$query->is_singular = true;
		$query->is_home     = false;
		$query->is_archive  = false;
		$query->is_404      = false;
	}

	/**
	 * Resolve best matching content object.
	 *
	 * Priority:
	 * 1) Country variant + language match
	 * 2) Shared content + language match
	 * 3) Shared / variant + default language fallback (no content duplication required)
	 *
	 * @param string   $slug    Public slug or post_name.
	 * @param string[] $types   Post types to search.
	 * @param string   $country Country code.
	 * @param string   $lang    Language code.
	 * @return WP_Post|null
	 */
	public function resolve_singular( $slug, array $types, $country, $lang ) {
		$slug    = sanitize_title( $slug );
		$country = sanitize_key( $country );
		$lang    = sanitize_key( $lang );
		$types   = array_values( array_filter( array_map( 'sanitize_key', $types ) ) );

		if ( '' === $slug || empty( $types ) ) {
			return null;
		}

		$candidates = $this->collect_candidates( $slug, $types );
		if ( empty( $candidates ) ) {
			return null;
		}

		$scored = array();
		foreach ( $candidates as $post ) {
			if ( ! $this->locale->is_visible_in_country( $post->ID, $country ) ) {
				continue;
			}

			$score     = 0;
			$post_lang = $this->locale->get_lang( $post->ID );
			$countries = $this->locale->get_countries( $post->ID );

			if ( $post_lang === $lang ) {
				$score += 100;
			} elseif ( $lang !== 'ar' && 'ar' === $post_lang ) {
				// Translation missing — allow shared Arabic content (no forced duplication).
				$score += 20;
			} elseif ( $lang === 'ar' && 'ar' === $post_lang ) {
				$score += 100;
			} else {
				continue;
			}

			if ( empty( $countries ) ) {
				$score += 30; // shared
			} elseif ( 1 === count( $countries ) && $countries[0] === $country ) {
				$score += 60; // dedicated country variant
			} elseif ( in_array( $country, $countries, true ) ) {
				$score += 45; // multi-country assignment
			}

			// Prefer public_slug exact match slightly when post_name differs.
			$public = $this->locale->get_public_slug( $post->ID );
			if ( $public === $slug && $post->post_name !== $slug ) {
				$score += 5;
			}

			$scored[] = array(
				'score' => $score,
				'post'  => $post,
			);
		}

		if ( empty( $scored ) ) {
			return null;
		}

		usort(
			$scored,
			static function ( $a, $b ) {
				if ( $a['score'] === $b['score'] ) {
					return $b['post']->ID <=> $a['post']->ID;
				}
				return $b['score'] <=> $a['score'];
			}
		);

		/**
		 * @param WP_Post $post    Resolved post.
		 * @param string  $slug    Slug.
		 * @param string  $country Country.
		 * @param string  $lang    Language.
		 */
		return apply_filters( 'kayan_platform_resolved_singular', $scored[0]['post'], $slug, $country, $lang );
	}

	/**
	 * @param string   $slug  Slug.
	 * @param string[] $types Types.
	 * @return WP_Post[]
	 */
	private function collect_candidates( $slug, array $types ) {
		$by_name = get_posts(
			array(
				'name'           => $slug,
				'post_type'      => $types,
				'post_status'    => 'publish',
				'posts_per_page' => 20,
				'no_found_rows'  => true,
			)
		);

		$by_public = get_posts(
			array(
				'post_type'      => $types,
				'post_status'    => 'publish',
				'posts_per_page' => 20,
				'no_found_rows'  => true,
				'meta_key'       => Kayan_Content_Locale::META_PUBLIC_SLUG,
				'meta_value'     => $slug,
			)
		);

		$map = array();
		foreach ( array_merge( $by_name, $by_public ) as $post ) {
			if ( $post instanceof WP_Post ) {
				$map[ $post->ID ] = $post;
			}
		}

		return array_values( $map );
	}

	/**
	 * Soft locale constraints for archives/tax (legacy-safe).
	 *
	 * @param WP_Query $query   Query.
	 * @param string   $country Country.
	 * @param string   $lang    Language.
	 * @param bool     $strict  Unused reserved.
	 * @return void
	 */
	private function apply_locale_constraints( $query, $country, $lang, $strict = false ) {
		unset( $strict );
		$meta_query = (array) $query->get( 'meta_query' );

		// Visibility: shared (meta missing) OR country assigned.
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key'     => Kayan_Content_Locale::META_COUNTRIES,
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => Kayan_Content_Locale::META_COUNTRIES,
				'value'   => '"' . sanitize_key( $country ) . '"',
				'compare' => 'LIKE',
			),
		);

		// Language: missing/default OR exact lang. Always include Arabic fallback for EN requests.
		$lang_or = array(
			'relation' => 'OR',
			array(
				'key'     => Kayan_Content_Locale::META_LANG,
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => Kayan_Content_Locale::META_LANG,
				'value'   => sanitize_key( $lang ),
				'compare' => '=',
			),
		);
		if ( 'en' === $lang ) {
			$lang_or[] = array(
				'key'     => Kayan_Content_Locale::META_LANG,
				'value'   => 'ar',
				'compare' => '=',
			);
		}
		$meta_query[] = $lang_or;

		// Hidden content.
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key'     => Kayan_Content_Locale::META_VISIBILITY,
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => Kayan_Content_Locale::META_VISIBILITY,
				'value'   => 'hidden',
				'compare' => '!=',
			),
		);

		$query->set( 'meta_query', $meta_query );
	}

	/**
	 * @return bool
	 */
	private function has_tax_query_var() {
		foreach ( array_keys( $this->router->get_taxonomy_rewrite_map() ) as $taxonomy ) {
			if ( get_query_var( $taxonomy ) ) {
				return true;
			}
		}
		return false;
	}
}
