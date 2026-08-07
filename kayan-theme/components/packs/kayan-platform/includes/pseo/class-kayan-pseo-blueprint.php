<?php
/**
 * PSEO Content Blueprint — versioned, block-based page contract.
 *
 * - Templates define block structure
 * - Each block is independently regeneratable / lockable
 * - Media is first-class via Media Engine
 * - Rank Math fields are source values only (RM remains the SEO engine)
 *
 * Versioning allows safe template updates without wiping manual edits.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Blueprint {

	const META_BLUEPRINT = 'kayan_pseo_blueprint';
	const SCHEMA_VERSION = 2;

	/** @var Kayan_PSEO_Blocks|null */
	private $blocks;

	/** @var Kayan_PSEO_Templates|null */
	private $templates;

	/** @var Kayan_PSEO_Media|null */
	private $media;

	/**
	 * Optional wiring after engines exist.
	 *
	 * @param Kayan_PSEO_Blocks    $blocks    Blocks.
	 * @param Kayan_PSEO_Templates $templates Templates.
	 * @param Kayan_PSEO_Media     $media     Media.
	 * @return void
	 */
	public function set_engines( Kayan_PSEO_Blocks $blocks, Kayan_PSEO_Templates $templates, Kayan_PSEO_Media $media ) {
		$this->blocks    = $blocks;
		$this->templates = $templates;
		$this->media     = $media;
	}

	/**
	 * Versioned blueprint schema.
	 *
	 * @return array<string,mixed>
	 */
	public function schema() {
		$media_schema = $this->media ? $this->media->schema() : array();

		$schema = array(
			'schema_version'   => self::SCHEMA_VERSION,
			'blueprint_version'=> 1,
			'template_id'      => '',
			'template_version' => 1,
			'pattern_id'       => '',
			'blocks'           => array(), // block_id => instance
			'media'            => $media_schema,
			'rank_math'        => array(
				'title'         => '',
				'description'   => '',
				'focus_keyword' => '',
				'robots'        => array(),
			),
			'body'             => array(
				'html'     => '',
				'sections' => array(),
			),
			'locks'            => array(
				// Convenience index of locked block ids.
			),
			'history'          => array(
				// array( 'blueprint_version' => 1, 'at' => '', 'note' => '', 'changed_blocks' => array() )
			),
			// Legacy flat keys retained for backward compatibility during transition.
			'_legacy'          => array(),
		);

		/**
		 * @param array $schema Schema.
		 */
		return apply_filters( 'kayan_pseo_blueprint_schema', $schema );
	}

	/**
	 * Build a versioned blueprint skeleton from pattern + template.
	 *
	 * @param array<string,mixed>  $pattern  Pattern.
	 * @param array<string,string> $entities Entity refs.
	 * @param string               $country  Country.
	 * @param string               $language Language.
	 * @return array<string,mixed>
	 */
	public function build_skeleton( array $pattern, array $entities, $country, $language ) {
		$blueprint = $this->schema();
		$pattern_id = isset( $pattern['id'] ) ? sanitize_key( $pattern['id'] ) : '';
		$template_id = ! empty( $pattern['template_id'] ) ? sanitize_key( $pattern['template_id'] ) : '';

		$template = null;
		if ( $template_id && $this->templates ) {
			$template = $this->templates->get( $template_id );
		}

		$blueprint['pattern_id']        = $pattern_id;
		$blueprint['template_id']       = $template_id;
		$blueprint['template_version']  = $template ? (int) $template['version'] : 1;
		$blueprint['blueprint_version'] = 1;
		$blueprint['blocks']            = ( $template_id && $this->templates )
			? $this->templates->build_block_instances( $template_id )
			: array();

		if ( empty( $blueprint['blocks'] ) && ! empty( $pattern['blueprint_slots'] ) && $this->blocks ) {
			foreach ( (array) $pattern['blueprint_slots'] as $slot ) {
				$slot = sanitize_key( $slot );
				if ( $this->blocks->get( $slot ) ) {
					$blueprint['blocks'][ $slot ] = $this->blocks->empty_instance( $slot );
				}
			}
		}

		$blueprint['_meta'] = array(
			'entities' => $entities,
			'country'  => sanitize_key( $country ),
			'language' => sanitize_key( $language ),
		);

		$blueprint['history'][] = array(
			'blueprint_version' => 1,
			'at'                => gmdate( 'c' ),
			'note'              => 'skeleton_created',
			'changed_blocks'    => array_keys( $blueprint['blocks'] ),
		);

		/**
		 * @param array $blueprint Blueprint.
		 * @param array $pattern   Pattern.
		 * @param array $entities  Entities.
		 */
		return apply_filters( 'kayan_pseo_blueprint_skeleton', $blueprint, $pattern, $entities );
	}

	/**
	 * Apply a newer template version while preserving locked / manual blocks.
	 * Dry-run capable: returns new blueprint, does not write.
	 *
	 * @param array<string,mixed> $blueprint Current blueprint.
	 * @param string              $template_id Template id.
	 * @return array{ok:bool,blueprint?:array,errors?:string[],changed?:string[],preserved?:string[]}
	 */
	public function upgrade_template( array $blueprint, $template_id ) {
		if ( ! $this->templates || ! $this->blocks ) {
			return array(
				'ok'     => false,
				'errors' => array( 'engines_not_wired' ),
			);
		}

		$template = $this->templates->get( $template_id );
		if ( ! $template ) {
			return array(
				'ok'     => false,
				'errors' => array( 'template_not_found' ),
			);
		}

		$current_blocks = isset( $blueprint['blocks'] ) && is_array( $blueprint['blocks'] ) ? $blueprint['blocks'] : array();
		$new_instances  = $this->templates->build_block_instances( $template_id );
		$changed        = array();
		$preserved      = array();

		foreach ( $new_instances as $block_id => $instance ) {
			if ( isset( $current_blocks[ $block_id ] ) ) {
				$existing = $current_blocks[ $block_id ];
				$locked   = ! empty( $existing['locked'] ) || ( isset( $existing['source'] ) && 'manual' === $existing['source'] );
				if ( $locked ) {
					$new_instances[ $block_id ] = $existing;
					$preserved[]                = $block_id;
					continue;
				}
			}
			$changed[] = $block_id;
		}

		// Keep locked blocks that were removed from template? Preserve under _orphaned_locked.
		$orphans = array();
		foreach ( $current_blocks as $block_id => $existing ) {
			if ( isset( $new_instances[ $block_id ] ) ) {
				continue;
			}
			if ( ! empty( $existing['locked'] ) || ( isset( $existing['source'] ) && 'manual' === $existing['source'] ) ) {
				$orphans[ $block_id ] = $existing;
				$preserved[]          = $block_id;
			}
		}

		$blueprint['blocks']            = $new_instances;
		$blueprint['template_id']       = $template_id;
		$blueprint['template_version']  = (int) $template['version'];
		$blueprint['blueprint_version'] = absint( $blueprint['blueprint_version'] ?? 1 ) + 1;
		$blueprint['schema_version']    = self::SCHEMA_VERSION;
		if ( $orphans ) {
			$blueprint['_orphaned_locked'] = $orphans;
		}
		$blueprint['locks'] = $this->index_locks( $blueprint['blocks'] );
		$blueprint['history'][] = array(
			'blueprint_version' => $blueprint['blueprint_version'],
			'at'                => gmdate( 'c' ),
			'note'              => 'template_upgrade',
			'changed_blocks'    => $changed,
			'preserved_blocks'  => $preserved,
		);

		return array(
			'ok'         => true,
			'blueprint'  => $blueprint,
			'changed'    => $changed,
			'preserved'  => $preserved,
		);
	}

	/**
	 * Mark a block as manually locked.
	 *
	 * @param array  $blueprint Blueprint.
	 * @param string $block_id  Block id.
	 * @param bool   $locked    Locked.
	 * @return array
	 */
	public function set_block_lock( array $blueprint, $block_id, $locked = true ) {
		$block_id = sanitize_key( $block_id );
		if ( empty( $blueprint['blocks'][ $block_id ] ) || ! is_array( $blueprint['blocks'][ $block_id ] ) ) {
			return $blueprint;
		}
		$blueprint['blocks'][ $block_id ]['locked'] = (bool) $locked;
		if ( $locked ) {
			$blueprint['blocks'][ $block_id ]['source'] = 'manual';
		}
		$blueprint['locks'] = $this->index_locks( $blueprint['blocks'] );
		return $blueprint;
	}

	/**
	 * Replace one block payload (future AI/manual regen).
	 *
	 * @param array               $blueprint Blueprint.
	 * @param string              $block_id  Block.
	 * @param array<string,mixed> $data      Data.
	 * @param string              $source    ai|manual|template.
	 * @return array{ok:bool,blueprint?:array,errors?:string[]}
	 */
	public function replace_block( array $blueprint, $block_id, array $data, $source = 'ai' ) {
		$block_id = sanitize_key( $block_id );
		if ( empty( $blueprint['blocks'][ $block_id ] ) ) {
			return array(
				'ok'     => false,
				'errors' => array( 'block_not_in_blueprint' ),
			);
		}
		if ( ! empty( $blueprint['blocks'][ $block_id ]['locked'] ) && 'manual' !== $source ) {
			return array(
				'ok'     => false,
				'errors' => array( 'block_locked' ),
			);
		}

		$blueprint['blocks'][ $block_id ]['data']            = $data;
		$blueprint['blocks'][ $block_id ]['source']          = sanitize_key( $source );
		$blueprint['blocks'][ $block_id ]['content_version'] = absint( $blueprint['blocks'][ $block_id ]['content_version'] ?? 1 ) + 1;
		$blueprint['blueprint_version']                      = absint( $blueprint['blueprint_version'] ?? 1 ) + 1;
		$blueprint['history'][]                              = array(
			'blueprint_version' => $blueprint['blueprint_version'],
			'at'                => gmdate( 'c' ),
			'note'              => 'block_replace:' . $block_id,
			'changed_blocks'    => array( $block_id ),
		);

		return array(
			'ok'        => true,
			'blueprint' => $blueprint,
		);
	}

	/**
	 * @param int $post_id Post ID.
	 * @return array<string,mixed>
	 */
	public function get_for_post( $post_id ) {
		$stored = get_post_meta( $post_id, self::META_BLUEPRINT, true );
		if ( ! is_array( $stored ) ) {
			return $this->schema();
		}
		return $this->sanitize( $stored );
	}

	/**
	 * @param mixed $value Raw.
	 * @return array<string,mixed>
	 */
	public function sanitize( $value ) {
		$base = $this->schema();
		if ( ! is_array( $value ) ) {
			return $base;
		}

		// Migrate legacy flat blueprints (schema v1) into _legacy + empty blocks.
		if ( empty( $value['schema_version'] ) || (int) $value['schema_version'] < 2 ) {
			$base['_legacy'] = $value;
			$value           = array_merge( $base, array(
				'schema_version'    => self::SCHEMA_VERSION,
				'blueprint_version' => 1,
				'blocks'            => isset( $value['blocks'] ) && is_array( $value['blocks'] ) ? $value['blocks'] : array(),
			) );
		}

		$out                        = $this->merge_deep( $base, $value );
		$out['schema_version']      = self::SCHEMA_VERSION;
		$out['blueprint_version']   = absint( $out['blueprint_version'] ?? 1 );
		$out['template_id']         = sanitize_key( (string) ( $out['template_id'] ?? '' ) );
		$out['template_version']    = absint( $out['template_version'] ?? 1 );
		$out['pattern_id']          = sanitize_key( (string) ( $out['pattern_id'] ?? '' ) );

		if ( $this->media ) {
			$out['media'] = $this->media->sanitize( $out['media'] ?? array() );
		}

		if ( ! is_array( $out['blocks'] ) ) {
			$out['blocks'] = array();
		}
		$clean_blocks = array();
		foreach ( $out['blocks'] as $block_id => $instance ) {
			$block_id = sanitize_key( (string) $block_id );
			if ( ! $block_id || ! is_array( $instance ) ) {
				continue;
			}
			$clean_blocks[ $block_id ] = array(
				'block_id'        => $block_id,
				'data'            => isset( $instance['data'] ) && is_array( $instance['data'] ) ? $instance['data'] : array(),
				'locked'          => ! empty( $instance['locked'] ),
				'source'          => sanitize_key( (string) ( $instance['source'] ?? 'template' ) ),
				'content_version' => absint( $instance['content_version'] ?? 1 ),
				'ai'              => array(
					'prompt'         => sanitize_textarea_field( (string) ( $instance['ai']['prompt'] ?? '' ) ),
					'prompt_version' => absint( $instance['ai']['prompt_version'] ?? 1 ),
					'provider'       => sanitize_key( (string) ( $instance['ai']['provider'] ?? '' ) ),
					'model'          => sanitize_text_field( (string) ( $instance['ai']['model'] ?? '' ) ),
					'last_generated' => sanitize_text_field( (string) ( $instance['ai']['last_generated'] ?? '' ) ),
				),
			);
		}
		$out['blocks'] = $clean_blocks;
		$out['locks']  = $this->index_locks( $clean_blocks );

		return $out;
	}

	/**
	 * @param array $blocks Blocks.
	 * @return string[]
	 */
	private function index_locks( array $blocks ) {
		$locks = array();
		foreach ( $blocks as $id => $instance ) {
			if ( ! empty( $instance['locked'] ) ) {
				$locks[] = $id;
			}
		}
		return $locks;
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
