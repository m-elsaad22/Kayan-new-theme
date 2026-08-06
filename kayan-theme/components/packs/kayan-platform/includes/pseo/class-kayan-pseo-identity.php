<?php
/**
 * PSEO Identity — stable fingerprints so regeneration never breaks URLs.
 *
 * fingerprint = hash(pattern_id + normalized entity refs + country + language)
 * The public slug / post_name is derived once and reused on regenerate.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Identity {

	const META_FINGERPRINT = 'kayan_pseo_fingerprint';
	const META_PATTERN     = 'kayan_pseo_pattern_id';
	const META_ENTITIES    = 'kayan_pseo_entities';
	const META_RULE        = 'kayan_pseo_rule_id';

	/** @var Kayan_PSEO_Patterns */
	private $patterns;

	public function __construct( Kayan_PSEO_Patterns $patterns ) {
		$this->patterns = $patterns;
	}

	/**
	 * Build a stable fingerprint for a combination.
	 *
	 * @param string               $pattern_id Pattern id.
	 * @param array<string,string> $entities   Map entity_type => stable ref (slug or id string).
	 * @param string               $country    Country code.
	 * @param string               $language   Language code.
	 * @return string 64-char hex.
	 */
	public function fingerprint( $pattern_id, array $entities, $country, $language ) {
		$pattern_id = sanitize_key( $pattern_id );
		$country    = sanitize_key( $country );
		$language   = sanitize_key( $language );

		ksort( $entities );
		$normalized = array();
		foreach ( $entities as $type => $ref ) {
			$type = sanitize_key( (string) $type );
			$ref  = sanitize_title( (string) $ref );
			if ( '' === $type || '' === $ref ) {
				continue;
			}
			$normalized[ $type ] = $ref;
		}

		$payload = wp_json_encode(
			array(
				'v'        => 1,
				'pattern'  => $pattern_id,
				'entities' => $normalized,
				'country'  => $country,
				'language' => $language,
			)
		);

		$fp = hash( 'sha256', (string) $payload );

		/**
		 * @param string $fp         Fingerprint.
		 * @param string $pattern_id Pattern.
		 * @param array  $entities   Entities.
		 * @param string $country    Country.
		 * @param string $language   Language.
		 */
		return apply_filters( 'kayan_pseo_fingerprint', $fp, $pattern_id, $normalized, $country, $language );
	}

	/**
	 * Suggest a public slug from pattern + tokens (does not write).
	 *
	 * @param string               $pattern_id Pattern.
	 * @param array<string,string> $tokens     Tokens.
	 * @return string
	 */
	public function suggest_public_slug( $pattern_id, array $tokens ) {
		return $this->patterns->build_path_slug( $pattern_id, $tokens );
	}

	/**
	 * Find an existing generated post by fingerprint (0 if none).
	 * Accepts one post type or a list (searches all PSEO host CPTs).
	 *
	 * @param string          $fingerprint Fingerprint.
	 * @param string|string[] $post_type   Storage post type(s).
	 * @return int
	 */
	public function find_post_id_by_fingerprint( $fingerprint, $post_type = 'kayan_pseo' ) {
		$fingerprint = preg_replace( '/[^a-f0-9]/', '', strtolower( (string) $fingerprint ) );
		if ( ! $fingerprint ) {
			return 0;
		}

		$types = is_array( $post_type ) ? $post_type : array( $post_type );
		$types = array_values(
			array_filter(
				array_map(
					static function ( $type ) {
						return sanitize_key( (string) $type );
					},
					$types
				)
			)
		);
		if ( ! $types ) {
			$types = array( 'kayan_pseo' );
		}

		$posts = get_posts(
			array(
				'post_type'              => $types,
				'post_status'            => array( 'publish', 'draft', 'future', 'pending', 'private' ),
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_key'               => self::META_FINGERPRINT,
				'meta_value'             => $fingerprint,
			)
		);

		return ! empty( $posts[0] ) ? (int) $posts[0] : 0;
	}

	/**
	 * Canonical URL for a combination using platform URL builder.
	 *
	 * @param string               $pattern_id Pattern.
	 * @param array<string,string> $tokens     Tokens for path.
	 * @param string               $country    Country.
	 * @param string               $language   Language.
	 * @return string
	 */
	public function build_canonical_url( $pattern_id, array $tokens, $country, $language ) {
		$slug = $this->suggest_public_slug( $pattern_id, $tokens );
		if ( function_exists( 'kayan_platform_url' ) ) {
			return kayan_platform_url( $slug ? $slug : '/', $country, $language );
		}
		return home_url( '/' . ltrim( $slug, '/' ) . '/' );
	}
}
