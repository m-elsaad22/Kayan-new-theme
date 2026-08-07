<?php
/**
 * KAYAN Quality Engine — validates a generated page before it is allowed
 * to publish. Reuses the existing Blueprint/Storage/Entity/Tags/Locale
 * contracts; never a second content model.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Quality_Engine {

	const STATUS_PASS = 'pass';
	const STATUS_WARN = 'warn';
	const STATUS_FAIL = 'fail';

	/**
	 * @return void
	 */
	public function register() {
		/**
		 * @param Kayan_Quality_Engine $engine Engine.
		 */
		do_action( 'kayan_quality_engine_registered', $this );
	}

	/**
	 * Validate a generated page. Only meaningful for posts carrying a
	 * PSEO blueprint — returns ok=true/no-op for anything else.
	 *
	 * @param int $post_id Post ID.
	 * @return array{ok:bool,score:float,checks:array,blockers:string[]}
	 */
	public function validate( $post_id ) {
		$post_id = (int) $post_id;
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return array( 'ok' => false, 'score' => 0.0, 'checks' => array(), 'blockers' => array( 'post_not_found' ) );
		}

		$blueprint = function_exists( 'kayan_pseo' ) ? kayan_pseo()->blueprint->get_for_post( $post_id ) : array();
		$fingerprint = get_post_meta( $post_id, 'kayan_pseo_fingerprint', true );
		if ( empty( $fingerprint ) ) {
			// Not a PSEO-managed page — nothing to validate, always "pass".
			return array( 'ok' => true, 'score' => 1.0, 'checks' => array(), 'blockers' => array(), 'not_applicable' => true );
		}

		$entities = get_post_meta( $post_id, 'kayan_pseo_entities', true );
		$entities = is_array( $entities ) ? $entities : array();
		$template = ! empty( $blueprint['template_id'] ) && function_exists( 'kayan_pseo' ) ? kayan_pseo()->templates->get( $blueprint['template_id'] ) : null;
		$blocks   = isset( $blueprint['blocks'] ) && is_array( $blueprint['blocks'] ) ? $blueprint['blocks'] : array();

		$checks = array(
			$this->check_content_length( $post, $blocks ),
			$this->check_heading_structure( $blocks ),
			$this->check_duplicate_detection( $post ),
			$this->check_internal_links( $blocks ),
			$this->check_external_links( $blocks ),
			$this->check_image_alt_coverage( $blueprint ),
			$this->check_schema_source( $blocks, $blueprint ),
			$this->check_dynamic_tag_resolution( $post_id, $entities ),
			$this->check_country_consistency( $post_id, $entities ),
			$this->check_language_consistency( $post_id ),
			$this->check_blueprint_completeness( $blocks, $template ),
			$this->check_broken_relationships( $blocks ),
			$this->check_missing_entities( $entities ),
			$this->check_required_block( $blocks, $template, 'cta', __( 'Missing CTA', 'kayan' ) ),
			$this->check_required_block( $blocks, $template, 'faq', __( 'Missing FAQ', 'kayan' ) ),
			$this->check_required_block( $blocks, $template, 'reviews', __( 'Missing Reviews', 'kayan' ) ),
			$this->check_required_block( $blocks, $template, 'pricing', __( 'Missing Pricing', 'kayan' ) ),
			$this->check_seo_completeness( $post_id, $blueprint ),
		);

		/**
		 * @param array $checks  Checks.
		 * @param int   $post_id Post ID.
		 */
		$checks = apply_filters( 'kayan_quality_checks', $checks, $post_id );

		$blockers = array();
		$sum      = 0.0;
		foreach ( $checks as $check ) {
			$sum += self::STATUS_PASS === $check['status'] ? 1 : ( self::STATUS_WARN === $check['status'] ? 0.5 : 0 );
			if ( self::STATUS_FAIL === $check['status'] ) {
				$blockers[] = $check['id'];
			}
		}
		$score = $checks ? round( $sum / count( $checks ), 2 ) : 1.0;

		return array(
			'ok'       => empty( $blockers ),
			'score'    => $score,
			'checks'   => $checks,
			'blockers' => $blockers,
		);
	}

	/**
	 * @param string $id      Check id.
	 * @param string $label   Label.
	 * @param string $status  pass|warn|fail.
	 * @param string $message Message.
	 * @return array<string,string>
	 */
	private function result( $id, $label, $status, $message ) {
		return array( 'id' => $id, 'label' => $label, 'status' => $status, 'message' => $message );
	}

	/**
	 * @param array $blocks Blocks.
	 * @return string
	 */
	private function flatten_text( array $blocks ) {
		$out = array();
		array_walk_recursive(
			$blocks,
			static function ( $v ) use ( &$out ) {
				if ( is_string( $v ) ) {
					$out[] = $v;
				}
			}
		);
		return implode( ' ', $out );
	}

	private function check_content_length( $post, array $blocks ) {
		$len = strlen( wp_strip_all_tags( (string) $post->post_title ) ) + strlen( wp_strip_all_tags( (string) $post->post_excerpt ) ) + strlen( $this->flatten_text( $blocks ) );
		if ( $len < 50 ) {
			return $this->result( 'content_length', __( 'Content length', 'kayan' ), self::STATUS_FAIL, __( 'Almost no content present.', 'kayan' ) );
		}
		if ( $len < 300 ) {
			return $this->result( 'content_length', __( 'Content length', 'kayan' ), self::STATUS_WARN, __( 'Content is thin.', 'kayan' ) );
		}
		return $this->result( 'content_length', __( 'Content length', 'kayan' ), self::STATUS_PASS, sprintf( /* translators: %d: character count */ __( '%d characters.', 'kayan' ), $len ) );
	}

	private function check_heading_structure( array $blocks ) {
		$headline = $blocks['hero']['data']['headline'] ?? '';
		if ( '' === trim( (string) $headline ) ) {
			return $this->result( 'heading_structure', __( 'Heading structure', 'kayan' ), self::STATUS_FAIL, __( 'Hero headline (H1) is empty.', 'kayan' ) );
		}
		return $this->result( 'heading_structure', __( 'Heading structure', 'kayan' ), self::STATUS_PASS, __( 'Hero headline present.', 'kayan' ) );
	}

	private function check_duplicate_detection( $post ) {
		if ( ! function_exists( 'kayan_query' ) ) {
			return $this->result( 'duplicate_detection', __( 'Duplicate detection', 'kayan' ), self::STATUS_PASS, __( 'Not checked.', 'kayan' ) );
		}
		$dupes = get_posts(
			array(
				'post_type'      => $post->post_type,
				'title'          => $post->post_title,
				'post_status'    => array( 'publish', 'draft', 'future', 'pending' ),
				'exclude'        => array( $post->ID ),
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( ! empty( $dupes ) ) {
			return $this->result( 'duplicate_detection', __( 'Duplicate detection', 'kayan' ), self::STATUS_WARN, __( 'Another post shares the exact same title.', 'kayan' ) );
		}
		return $this->result( 'duplicate_detection', __( 'Duplicate detection', 'kayan' ), self::STATUS_PASS, __( 'No title duplicates found.', 'kayan' ) );
	}

	private function check_internal_links( array $blocks ) {
		$has = ! empty( $blocks['internal_links']['data']['items'] ) || ! empty( $blocks['related_services']['data']['post_ids'] ) || ! empty( $blocks['related_articles']['data']['post_ids'] );
		return $has
			? $this->result( 'internal_links', __( 'Internal links', 'kayan' ), self::STATUS_PASS, __( 'Internal links present.', 'kayan' ) )
			: $this->result( 'internal_links', __( 'Internal links', 'kayan' ), self::STATUS_WARN, __( 'No internal links found.', 'kayan' ) );
	}

	private function check_external_links( array $blocks ) {
		$text  = $this->flatten_text( $blocks );
		$count = preg_match_all( '#https?://[^\s"\']+#i', $text, $m );
		if ( $count > 5 ) {
			return $this->result( 'external_links', __( 'External links', 'kayan' ), self::STATUS_WARN, sprintf( /* translators: %d: link count */ __( '%d external links — review for relevance.', 'kayan' ), $count ) );
		}
		return $this->result( 'external_links', __( 'External links', 'kayan' ), self::STATUS_PASS, sprintf( /* translators: %d: link count */ __( '%d external links.', 'kayan' ), $count ) );
	}

	private function check_image_alt_coverage( array $blueprint ) {
		$gallery = $blueprint['media']['gallery'] ?? array();
		if ( empty( $gallery ) ) {
			return $this->result( 'image_alt_coverage', __( 'Image ALT coverage', 'kayan' ), self::STATUS_PASS, __( 'No gallery images to check.', 'kayan' ) );
		}
		foreach ( $gallery as $item ) {
			if ( empty( $item['alt'] ) ) {
				return $this->result( 'image_alt_coverage', __( 'Image ALT coverage', 'kayan' ), self::STATUS_FAIL, __( 'One or more gallery images are missing ALT text.', 'kayan' ) );
			}
		}
		return $this->result( 'image_alt_coverage', __( 'Image ALT coverage', 'kayan' ), self::STATUS_PASS, __( 'All gallery images have ALT text.', 'kayan' ) );
	}

	private function check_schema_source( array $blocks, array $blueprint ) {
		$has = ! empty( $blocks['schema_source']['data']['payload'] ) || ! empty( $blueprint['rank_math'] );
		return $has
			? $this->result( 'schema_source', __( 'Schema source completeness', 'kayan' ), self::STATUS_PASS, __( 'Schema source data present.', 'kayan' ) )
			: $this->result( 'schema_source', __( 'Schema source completeness', 'kayan' ), self::STATUS_WARN, __( 'No schema source data supplied (Rank Math still owns output schema).', 'kayan' ) );
	}

	private function check_dynamic_tag_resolution( $post_id, array $entities ) {
		if ( ! function_exists( 'kayan_tags' ) ) {
			return $this->result( 'dynamic_tag_resolution', __( 'Dynamic tag resolution', 'kayan' ), self::STATUS_PASS, __( 'Not checked.', 'kayan' ) );
		}
		$country  = get_post_meta( $post_id, Kayan_Content_Locale::META_COUNTRIES, true );
		$country  = is_array( $country ) && $country ? (string) $country[0] : '';
		$language = (string) get_post_meta( $post_id, Kayan_Content_Locale::META_LANG, true );
		$context  = array( 'country' => $country, 'language' => $language ?: 'ar', 'entities' => $entities, 'post_id' => $post_id );

		$critical = array( 'phone', 'meta_title' );
		foreach ( $critical as $tag ) {
			$value = (string) kayan_tags()->resolve_tag( $tag, $context );
			if ( '' === trim( $value ) ) {
				return $this->result( 'dynamic_tag_resolution', __( 'Dynamic tag resolution', 'kayan' ), self::STATUS_WARN, sprintf( /* translators: %s: tag name */ __( '{{%s}} resolved empty.', 'kayan' ), $tag ) );
			}
		}
		return $this->result( 'dynamic_tag_resolution', __( 'Dynamic tag resolution', 'kayan' ), self::STATUS_PASS, __( 'Core tags resolve correctly.', 'kayan' ) );
	}

	private function check_country_consistency( $post_id, array $entities ) {
		if ( empty( $entities['country'] ) ) {
			return $this->result( 'country_consistency', __( 'Country consistency', 'kayan' ), self::STATUS_PASS, __( 'Pattern is not country-specific.', 'kayan' ) );
		}
		$countries = get_post_meta( $post_id, Kayan_Content_Locale::META_COUNTRIES, true );
		$countries = is_array( $countries ) ? $countries : array();
		if ( ! in_array( sanitize_key( (string) $entities['country'] ), array_map( 'sanitize_key', $countries ), true ) ) {
			return $this->result( 'country_consistency', __( 'Country consistency', 'kayan' ), self::STATUS_FAIL, __( 'Post country meta does not match its entity country.', 'kayan' ) );
		}
		return $this->result( 'country_consistency', __( 'Country consistency', 'kayan' ), self::STATUS_PASS, __( 'Country meta matches entities.', 'kayan' ) );
	}

	private function check_language_consistency( $post_id ) {
		$lang = (string) get_post_meta( $post_id, Kayan_Content_Locale::META_LANG, true );
		if ( '' === $lang ) {
			return $this->result( 'language_consistency', __( 'Language consistency', 'kayan' ), self::STATUS_WARN, __( 'No language meta set.', 'kayan' ) );
		}
		if ( function_exists( 'kayan_platform' ) && ! kayan_platform()->languages->exists( $lang ) ) {
			return $this->result( 'language_consistency', __( 'Language consistency', 'kayan' ), self::STATUS_FAIL, sprintf( /* translators: %s: language code */ __( 'Unknown language code "%s".', 'kayan' ), $lang ) );
		}
		return $this->result( 'language_consistency', __( 'Language consistency', 'kayan' ), self::STATUS_PASS, __( 'Language is registered.', 'kayan' ) );
	}

	private function check_blueprint_completeness( array $blocks, $template ) {
		if ( ! $template ) {
			return $this->result( 'blueprint_completeness', __( 'Blueprint completeness', 'kayan' ), self::STATUS_WARN, __( 'No template assigned.', 'kayan' ) );
		}
		$missing = array();
		foreach ( (array) $template['blocks'] as $block_id ) {
			if ( ! isset( $blocks[ $block_id ] ) ) {
				$missing[] = $block_id;
			}
		}
		if ( $missing ) {
			return $this->result( 'blueprint_completeness', __( 'Blueprint completeness', 'kayan' ), self::STATUS_FAIL, sprintf( /* translators: %s: block ids */ __( 'Missing blocks: %s', 'kayan' ), implode( ', ', $missing ) ) );
		}
		return $this->result( 'blueprint_completeness', __( 'Blueprint completeness', 'kayan' ), self::STATUS_PASS, __( 'All template blocks present.', 'kayan' ) );
	}

	private function check_broken_relationships( array $blocks ) {
		$broken = 0;
		foreach ( array( 'related_services' => 'post_ids', 'related_articles' => 'post_ids', 'reviews' => 'post_ids' ) as $block_id => $field ) {
			foreach ( (array) ( $blocks[ $block_id ]['data'][ $field ] ?? array() ) as $ref_id ) {
				if ( ! get_post( (int) $ref_id ) ) {
					++$broken;
				}
			}
		}
		foreach ( (array) ( $blocks['related_cities']['data']['term_ids'] ?? array() ) as $term_id ) {
			$term = get_term( (int) $term_id );
			if ( ! $term || is_wp_error( $term ) ) {
				++$broken;
			}
		}
		return $broken > 0
			? $this->result( 'broken_relationships', __( 'Broken relationships', 'kayan' ), self::STATUS_FAIL, sprintf( /* translators: %d: number of broken refs */ __( '%d dangling reference(s) found.', 'kayan' ), $broken ) )
			: $this->result( 'broken_relationships', __( 'Broken relationships', 'kayan' ), self::STATUS_PASS, __( 'All related references resolve.', 'kayan' ) );
	}

	private function check_missing_entities( array $entities ) {
		if ( ! function_exists( 'kayan_entity' ) || empty( $entities ) ) {
			return $this->result( 'missing_entities', __( 'Missing entities', 'kayan' ), self::STATUS_PASS, __( 'No entities to check.', 'kayan' ) );
		}
		$missing = array();
		foreach ( $entities as $type => $ref ) {
			if ( ! kayan_entity()->get( $type, $ref ) ) {
				$missing[] = $type;
			}
		}
		return $missing
			? $this->result( 'missing_entities', __( 'Missing entities', 'kayan' ), self::STATUS_FAIL, sprintf( /* translators: %s: entity types */ __( 'Cannot resolve: %s', 'kayan' ), implode( ', ', $missing ) ) )
			: $this->result( 'missing_entities', __( 'Missing entities', 'kayan' ), self::STATUS_PASS, __( 'All source entities resolve.', 'kayan' ) );
	}

	private function check_required_block( array $blocks, $template, $block_id, $label ) {
		$required = $template && in_array( $block_id, (array) $template['blocks'], true );
		if ( ! $required ) {
			return $this->result( 'missing_' . $block_id, $label, self::STATUS_PASS, __( 'Not required by this template.', 'kayan' ) );
		}
		$data = $blocks[ $block_id ]['data'] ?? array();
		$has  = false;
		foreach ( $data as $value ) {
			if ( ( is_array( $value ) && ! empty( $value ) ) || ( is_string( $value ) && '' !== trim( $value ) ) ) {
				$has = true;
				break;
			}
		}
		return $has
			? $this->result( 'missing_' . $block_id, $label, self::STATUS_PASS, __( 'Present.', 'kayan' ) )
			: $this->result( 'missing_' . $block_id, $label, self::STATUS_FAIL, __( 'Required by template but empty.', 'kayan' ) );
	}

	private function check_seo_completeness( $post_id, array $blueprint ) {
		$title = get_post_meta( $post_id, 'rank_math_title', true ) ?: ( $blueprint['rank_math']['title'] ?? '' );
		$desc  = get_post_meta( $post_id, 'rank_math_description', true ) ?: ( $blueprint['rank_math']['description'] ?? '' );

		if ( '' === trim( (string) $title ) && '' === trim( (string) $desc ) ) {
			return $this->result( 'seo_completeness', __( 'SEO completeness', 'kayan' ), self::STATUS_FAIL, __( 'No SEO title or description supplied to Rank Math.', 'kayan' ) );
		}
		if ( '' === trim( (string) $title ) || '' === trim( (string) $desc ) ) {
			return $this->result( 'seo_completeness', __( 'SEO completeness', 'kayan' ), self::STATUS_WARN, __( 'SEO title or description missing.', 'kayan' ) );
		}
		return $this->result( 'seo_completeness', __( 'SEO completeness', 'kayan' ), self::STATUS_PASS, __( 'SEO title and description supplied.', 'kayan' ) );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'apis' => array(
				'validate' => 'kayan_quality()->validate( $post_id )',
			),
		);
	}
}
