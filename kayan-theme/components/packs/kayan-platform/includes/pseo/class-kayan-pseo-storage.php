<?php
/**
 * PSEO Storage Contract — real WordPress posts (page-like CPT).
 *
 * Generated pages (future) are normal WP posts:
 * - editable individually
 * - searchable
 * - revisions
 * - Rank Math compatible (show_in_rest + public)
 * - locale meta via Content Locale (translations + country variants)
 *
 * Phase 2.5 registers the CPT + meta only. No posts are created.
 * Admin menu is hidden to avoid UI redesign.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Storage {

	const POST_TYPE = 'kayan_pseo';

	/** @var Kayan_PSEO_Blueprint */
	private $blueprint;

	/** @var Kayan_Content_Locale */
	private $locale;

	public function __construct( Kayan_PSEO_Blueprint $blueprint, Kayan_Content_Locale $locale ) {
		$this->blueprint = $blueprint;
		$this->locale    = $locale;
	}

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_post_type' ), 8 );
		add_action( 'init', array( $this, 'register_meta' ), 21 );
		add_filter( 'kayan_platform_content_post_types', array( $this, 'add_to_locale_types' ) );
		add_filter( 'kayan_platform_post_type_rewrite_map', array( $this, 'add_to_rewrite_map' ) );
	}

	/**
	 * @return void
	 */
	public function register_post_type() {
		if ( post_type_exists( self::POST_TYPE ) ) {
			return;
		}

		$args = array(
			'labels'              => array(
				'name'          => 'KAYAN SEO Pages',
				'singular_name' => 'KAYAN SEO Page',
			),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => false, // no admin redesign in Phase 2.5
			'show_in_rest'        => true,
			'rest_base'           => 'kayan-pseo',
			'has_archive'         => false,
			'rewrite'             => false, // Country Router owns localized URLs
			'hierarchical'        => false,
			'capability_type'     => 'page',
			'map_meta_cap'        => true,
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
				'revisions',
				'custom-fields',
				'page-attributes',
			),
			'delete_with_user'    => false,
		);

		/**
		 * @param array $args CPT args.
		 */
		$args = apply_filters( 'kayan_pseo_post_type_args', $args );

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * @return void
	 */
	public function register_meta() {
		$keys = array(
			Kayan_PSEO_Identity::META_FINGERPRINT => array(
				'type'              => 'string',
				'description'       => 'Stable combination fingerprint (URL identity).',
				'sanitize_callback' => array( $this, 'sanitize_fingerprint' ),
			),
			Kayan_PSEO_Identity::META_PATTERN     => array(
				'type'              => 'string',
				'description'       => 'PSEO pattern id.',
				'sanitize_callback' => 'sanitize_key',
			),
			Kayan_PSEO_Identity::META_ENTITIES    => array(
				'type'              => 'object',
				'description'       => 'Entity refs map.',
				'sanitize_callback' => array( $this, 'sanitize_entities_map' ),
			),
			Kayan_PSEO_Identity::META_RULE        => array(
				'type'              => 'string',
				'description'       => 'Source generation rule id.',
				'sanitize_callback' => 'sanitize_key',
			),
			Kayan_PSEO_Blueprint::META_BLUEPRINT  => array(
				'type'              => 'object',
				'description'       => 'Content blueprint slots.',
				'sanitize_callback' => array( $this->blueprint, 'sanitize' ),
			),
			'kayan_pseo_source'                   => array(
				'type'              => 'string',
				'description'       => 'manual|rule|ai|regenerate',
				'sanitize_callback' => 'sanitize_key',
			),
			'kayan_pseo_locked_slug'              => array(
				'type'              => 'string',
				'description'       => 'Immutable public slug after first publish.',
				'sanitize_callback' => 'sanitize_title',
			),
		);

		foreach ( $keys as $key => $args ) {
			register_post_meta(
				self::POST_TYPE,
				$key,
				array_merge(
					array(
						'single'        => true,
						'show_in_rest'  => true,
						'auth_callback' => static function () {
							return current_user_can( 'edit_pages' );
						},
					),
					$args
				)
			);
		}
	}

	/**
	 * @param string[] $types Types.
	 * @return string[]
	 */
	public function add_to_locale_types( $types ) {
		$types   = (array) $types;
		$types[] = self::POST_TYPE;
		return array_values( array_unique( $types ) );
	}

	/**
	 * Public path segment for Country Router (filterable).
	 *
	 * @param array<string,string> $map Map.
	 * @return array<string,string>
	 */
	public function add_to_rewrite_map( $map ) {
		$map = (array) $map;
		// Composite public slugs often include services/… — catch-all singular still resolves via public_slug.
		// Optional dedicated base segment for pure PSEO pages:
		$map[ self::POST_TYPE ] = 'p';
		return $map;
	}

	/**
	 * Storage post type name.
	 *
	 * @return string
	 */
	public function post_type() {
		return self::POST_TYPE;
	}

	/**
	 * Describe storage capabilities for API consumers.
	 *
	 * @return array<string,mixed>
	 */
	public function capabilities() {
		return array(
			'post_type'          => self::POST_TYPE,
			'behaves_like'       => 'page',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields', 'page-attributes' ),
			'searchable'         => true,
			'rank_math'          => true,
			'translations'       => true, // via Content Locale translation_group
			'country_variants'   => true, // via Content Locale countries / variant_group
			'rewrite_owned_by'   => 'kayan_platform_country_router',
			'generation'         => false, // Phase 2.5
		);
	}

	/**
	 * @param mixed $value Raw.
	 * @return string
	 */
	public function sanitize_fingerprint( $value ) {
		return preg_replace( '/[^a-f0-9]/', '', strtolower( (string) $value ) );
	}

	/**
	 * @param mixed $value Raw.
	 * @return array<string,string>
	 */
	public function sanitize_entities_map( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$clean = array();
		foreach ( $value as $type => $ref ) {
			$type = sanitize_key( (string) $type );
			$ref  = sanitize_text_field( (string) $ref );
			if ( $type && $ref ) {
				$clean[ $type ] = $ref;
			}
		}
		return $clean;
	}
}
