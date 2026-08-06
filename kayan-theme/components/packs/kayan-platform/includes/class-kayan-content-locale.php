<?php
/**
 * Content Locale contracts — meta registered for all public content types.
 *
 * Hybrid model (Phase 1 registers only; no migration / no query changes):
 * - Missing meta = legacy behavior (visible everywhere, language Arabic).
 * - Shared content: kayan_content_countries = list of countries.
 * - Country variants: unique WP post_name + kayan_public_slug for public path.
 * - Translations: kayan_translation_group links language variants.
 *
 * WP constraint: post_name is globally unique per post type — public_slug
 * is the future resolver key for identical public paths across countries.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Content_Locale {

	const META_LANG              = 'kayan_content_lang';
	const META_COUNTRIES         = 'kayan_content_countries';
	const META_TRANSLATION_GROUP = 'kayan_translation_group';
	const META_VARIANT_GROUP     = 'kayan_variant_group';
	const META_PUBLIC_SLUG       = 'kayan_public_slug';
	const META_VISIBILITY        = 'kayan_content_visibility';
	const META_LOCALE_SEO        = 'kayan_locale_seo';

	const TERM_META_COUNTRY      = 'kayan_country';

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_meta' ), 20 );
	}

	/**
	 * @return void
	 */
	public function register_meta() {
		$post_types = $this->get_supported_post_types();

		foreach ( $post_types as $post_type ) {
			$this->register_post_meta( $post_type, self::META_LANG, array(
				'type'              => 'string',
				'description'       => 'Content language code (ar, en, …). Empty = default Arabic.',
				'sanitize_callback' => array( $this, 'sanitize_lang' ),
			) );

			$this->register_post_meta( $post_type, self::META_COUNTRIES, array(
				'type'              => 'array',
				'description'       => 'Country codes where this content is visible. Empty = all countries (legacy).',
				'sanitize_callback' => array( $this, 'sanitize_countries' ),
			) );

			$this->register_post_meta( $post_type, self::META_TRANSLATION_GROUP, array(
				'type'              => 'string',
				'description'       => 'Shared UUID linking language translations of the same entity.',
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$this->register_post_meta( $post_type, self::META_VARIANT_GROUP, array(
				'type'              => 'string',
				'description'       => 'Shared UUID linking country variants that share a public slug.',
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$this->register_post_meta( $post_type, self::META_PUBLIC_SLUG, array(
				'type'              => 'string',
				'description'       => 'Public URL slug override (for country variants). Empty = use post_name.',
				'sanitize_callback' => 'sanitize_title',
			) );

			$this->register_post_meta( $post_type, self::META_VISIBILITY, array(
				'type'              => 'string',
				'description'       => 'Visibility: public|hidden. Empty = public.',
				'sanitize_callback' => array( $this, 'sanitize_visibility' ),
			) );

			$this->register_post_meta( $post_type, self::META_LOCALE_SEO, array(
				'type'              => 'object',
				'description'       => 'Per-country SEO overrides: { ae: { title, description }, … }.',
				'sanitize_callback' => array( $this, 'sanitize_locale_seo' ),
			) );
		}

		register_term_meta(
			'cities',
			self::TERM_META_COUNTRY,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'auth_callback'     => array( $this, 'can_edit_meta' ),
				'sanitize_callback' => 'sanitize_key',
				'description'       => 'Primary country code for this city term.',
			)
		);
	}

	/**
	 * Supported types: built-in + kayan-cpt. Filterable for future CPTs.
	 *
	 * @return string[]
	 */
	public function get_supported_post_types() {
		$types = array( 'post', 'page', 'services', 'reviews', 'faqs', 'pricing', 'portfolio', 'before_after' );

		/**
		 * @param string[] $types Post types.
		 */
		$types = apply_filters( 'kayan_platform_content_post_types', $types );

		$types = array_values( array_unique( array_filter( array_map( 'sanitize_key', (array) $types ) ) ) );
		return $types;
	}

	/**
	 * Read helpers — safe for legacy posts (no meta ⇒ defaults).
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public function get_lang( $post_id ) {
		$lang = get_post_meta( $post_id, self::META_LANG, true );
		if ( ! is_string( $lang ) || '' === $lang ) {
			// Reuse existing EN field convention without duplicating storage.
			return 'ar';
		}
		return sanitize_key( $lang );
	}

	/**
	 * @param int $post_id Post ID.
	 * @return string[] Empty = all countries.
	 */
	public function get_countries( $post_id ) {
		$countries = get_post_meta( $post_id, self::META_COUNTRIES, true );
		if ( ! is_array( $countries ) ) {
			return array();
		}
		return array_values( array_filter( array_map( 'sanitize_key', $countries ) ) );
	}

	/**
	 * @param int    $post_id Post ID.
	 * @param string $country Country code.
	 * @return bool
	 */
	public function is_visible_in_country( $post_id, $country ) {
		$visibility = get_post_meta( $post_id, self::META_VISIBILITY, true );
		if ( 'hidden' === $visibility ) {
			return false;
		}

		$countries = $this->get_countries( $post_id );
		if ( empty( $countries ) ) {
			return true; // legacy / global
		}

		return in_array( sanitize_key( $country ), $countries, true );
	}

	/**
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public function get_public_slug( $post_id ) {
		$slug = get_post_meta( $post_id, self::META_PUBLIC_SLUG, true );
		if ( is_string( $slug ) && '' !== $slug ) {
			return sanitize_title( $slug );
		}
		$post = get_post( $post_id );
		return $post ? (string) $post->post_name : '';
	}

	/**
	 * @param int    $post_id Post ID.
	 * @param string $country Country code.
	 * @return array{title?:string,description?:string}
	 */
	public function get_country_seo( $post_id, $country ) {
		$all = get_post_meta( $post_id, self::META_LOCALE_SEO, true );
		if ( ! is_array( $all ) ) {
			return array();
		}
		$country = sanitize_key( $country );
		return isset( $all[ $country ] ) && is_array( $all[ $country ] ) ? $all[ $country ] : array();
	}

	/**
	 * @param string               $post_type Post type.
	 * @param string               $key       Meta key.
	 * @param array<string,mixed>  $args      Args.
	 * @return void
	 */
	private function register_post_meta( $post_type, $key, array $args ) {
		$args = array_merge(
			array(
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => array( $this, 'can_edit_meta' ),
			),
			$args
		);
		register_post_meta( $post_type, $key, $args );
	}

	/**
	 * @return bool
	 */
	public function can_edit_meta() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * @param mixed $value Raw.
	 * @return string
	 */
	public function sanitize_lang( $value ) {
		return sanitize_key( (string) $value );
	}

	/**
	 * @param mixed $value Raw.
	 * @return string[]
	 */
	public function sanitize_countries( $value ) {
		if ( is_string( $value ) ) {
			$value = array_map( 'trim', explode( ',', $value ) );
		}
		if ( ! is_array( $value ) ) {
			return array();
		}
		return array_values( array_unique( array_filter( array_map( 'sanitize_key', $value ) ) ) );
	}

	/**
	 * @param mixed $value Raw.
	 * @return string
	 */
	public function sanitize_visibility( $value ) {
		$value = sanitize_key( (string) $value );
		return in_array( $value, array( 'public', 'hidden' ), true ) ? $value : 'public';
	}

	/**
	 * @param mixed $value Raw.
	 * @return array<string,array<string,string>>
	 */
	public function sanitize_locale_seo( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$clean = array();
		foreach ( $value as $country => $row ) {
			$country = sanitize_key( (string) $country );
			if ( '' === $country || ! is_array( $row ) ) {
				continue;
			}
			$clean[ $country ] = array(
				'title'       => isset( $row['title'] ) ? sanitize_text_field( $row['title'] ) : '',
				'description' => isset( $row['description'] ) ? sanitize_textarea_field( $row['description'] ) : '',
			);
		}
		return $clean;
	}
}
