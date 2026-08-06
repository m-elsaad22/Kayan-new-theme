<?php
/**
 * PSEO Content Blueprint — unique per-page slot contract.
 *
 * Slots are data contracts only in Phase 2.5. Future generators/AI fill them.
 * Rank Math owns title/description/canonical/schema output — blueprint may
 * supply *source data* via filters later, never competing head tags.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Blueprint {

	const META_BLUEPRINT = 'kayan_pseo_blueprint';

	/**
	 * Empty blueprint schema for a generated page.
	 *
	 * @return array<string,mixed>
	 */
	public function schema() {
		$schema = array(
			'hero'           => array(
				'headline'    => '',
				'subheadline' => '',
				'image_id'    => 0,
				'overlay'     => '',
			),
			'cta'            => array(
				'primary_label' => '',
				'primary_url'   => '',
				'secondary_label' => '',
				'secondary_url'   => '',
				'phone'         => '',
				'whatsapp'      => '',
			),
			'faq'            => array(
				// array of array( 'question' => '', 'answer' => '' )
				'items' => array(),
			),
			'reviews'        => array(
				'post_ids' => array(),
				'note'     => '',
			),
			'images'         => array(
				'gallery_ids' => array(),
				'og_image_id' => 0,
			),
			'internal_links' => array(
				// array of array( 'title' => '', 'url' => '', 'rel' => '' )
				'items' => array(),
			),
			'breadcrumb'     => array(
				// array of array( 'label' => '', 'url' => '' )
				'items' => array(),
			),
			'schema'         => array(
				// Source payloads for Rank Math / future RM filters — NOT printed by KAYAN.
				'types'   => array( 'Service', 'FAQPage', 'BreadcrumbList' ),
				'payload' => array(),
			),
			'rank_math'      => array(
				// Values intended to be written into Rank Math meta on materialize (future).
				'title'       => '',
				'description' => '',
				'focus_keyword' => '',
				'robots'      => array(),
			),
			'body'           => array(
				'sections' => array(), // future structured sections
				'html'     => '',      // optional pre-rendered HTML for post_content
			),
			'ai'             => array(
				'provider'       => '',
				'model'          => '',
				'prompt_version' => '',
				'last_generated' => '',
			),
		);

		/**
		 * @param array $schema Blueprint schema.
		 */
		return apply_filters( 'kayan_pseo_blueprint_schema', $schema );
	}

	/**
	 * Build a blueprint skeleton from pattern + entity context (no AI, no writes).
	 *
	 * @param array<string,mixed>  $pattern  Pattern.
	 * @param array<string,string> $entities Entity refs.
	 * @param string               $country  Country.
	 * @param string               $language Language.
	 * @return array<string,mixed>
	 */
	public function build_skeleton( array $pattern, array $entities, $country, $language ) {
		$blueprint = $this->schema();

		$blueprint['_meta'] = array(
			'pattern_id' => isset( $pattern['id'] ) ? $pattern['id'] : '',
			'entities'   => $entities,
			'country'    => sanitize_key( $country ),
			'language'   => sanitize_key( $language ),
			'version'    => 1,
		);

		/**
		 * @param array $blueprint Blueprint.
		 * @param array $pattern   Pattern.
		 * @param array $entities  Entities.
		 */
		return apply_filters( 'kayan_pseo_blueprint_skeleton', $blueprint, $pattern, $entities );
	}

	/**
	 * Read blueprint from a post.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string,mixed>
	 */
	public function get_for_post( $post_id ) {
		$stored = get_post_meta( $post_id, self::META_BLUEPRINT, true );
		if ( ! is_array( $stored ) ) {
			return $this->schema();
		}
		return $this->merge_deep( $this->schema(), $stored );
	}

	/**
	 * Sanitize blueprint array.
	 *
	 * @param mixed $value Raw.
	 * @return array<string,mixed>
	 */
	public function sanitize( $value ) {
		if ( ! is_array( $value ) ) {
			return $this->schema();
		}
		// Shallow merge with schema; deep sanitize of strings.
		$clean = $this->merge_deep( $this->schema(), $value );
		array_walk_recursive(
			$clean,
			static function ( &$item ) {
				if ( is_string( $item ) ) {
					$item = wp_kses_post( $item );
				}
			}
		);
		return $clean;
	}

	/**
	 * @param array $base Base.
	 * @param array $over Overlay.
	 * @return array
	 */
	private function merge_deep( array $base, array $over ) {
		foreach ( $over as $k => $v ) {
			if ( is_array( $v ) && isset( $base[ $k ] ) && is_array( $base[ $k ] ) ) {
				$base[ $k ] = $this->merge_deep( $base[ $k ], $v );
			} else {
				$base[ $k ] = $v;
			}
		}
		return $base;
	}
}
