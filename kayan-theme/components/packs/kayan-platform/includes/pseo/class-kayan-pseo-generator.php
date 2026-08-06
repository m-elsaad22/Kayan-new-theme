<?php
/**
 * PSEO Generator API — dry-run / preview only in Phase 2.5.
 *
 * materialize() / regenerate() intentionally refuse to write posts.
 * Block-level AI regenerate is contract-only (null provider).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Generator {

	/** @var Kayan_Programmatic_SEO */
	private $entities;

	/** @var Kayan_PSEO_Patterns */
	private $patterns;

	/** @var Kayan_PSEO_Rules */
	private $rules;

	/** @var Kayan_PSEO_Identity */
	private $identity;

	/** @var Kayan_PSEO_Blueprint */
	private $blueprint;

	/** @var Kayan_PSEO_Storage */
	private $storage;

	/** @var Kayan_PSEO_AI */
	private $ai;

	/** @var Kayan_PSEO_Templates */
	private $templates;

	/** @var Kayan_PSEO_Blocks */
	private $blocks;

	/** @var Kayan_PSEO_Media */
	private $media;

	public function __construct(
		Kayan_Programmatic_SEO $entities,
		Kayan_PSEO_Patterns $patterns,
		Kayan_PSEO_Rules $rules,
		Kayan_PSEO_Identity $identity,
		Kayan_PSEO_Blueprint $blueprint,
		Kayan_PSEO_Storage $storage,
		Kayan_PSEO_AI $ai,
		Kayan_PSEO_Templates $templates,
		Kayan_PSEO_Blocks $blocks,
		Kayan_PSEO_Media $media
	) {
		$this->entities  = $entities;
		$this->patterns  = $patterns;
		$this->rules     = $rules;
		$this->identity  = $identity;
		$this->blueprint = $blueprint;
		$this->storage   = $storage;
		$this->ai        = $ai;
		$this->templates = $templates;
		$this->blocks    = $blocks;
		$this->media     = $media;
	}

	/**
	 * Preview a single combination without writing to the database.
	 *
	 * @param string               $pattern_id Pattern id.
	 * @param array<string,string> $entity_refs Entity type => ref.
	 * @param string               $country    Country.
	 * @param string               $language   Language.
	 * @param array<string,string> $tokens     URL tokens.
	 * @return array<string,mixed>
	 */
	public function preview( $pattern_id, array $entity_refs, $country, $language, array $tokens = array() ) {
		$pattern = $this->patterns->get( $pattern_id );
		if ( ! $pattern ) {
			return array(
				'ok'     => false,
				'errors' => array( 'pattern_not_found' ),
			);
		}

		$fingerprint = $this->identity->fingerprint( $pattern_id, $entity_refs, $country, $language );
		$slug        = $this->identity->suggest_public_slug( $pattern_id, $tokens );
		$url         = $this->identity->build_canonical_url( $pattern_id, $tokens, $country, $language );
		$post_type   = $this->storage->resolve_post_type( $pattern_id );
		$existing_id = $this->identity->find_post_id_by_fingerprint( $fingerprint, $this->storage->host_post_types() );
		$blueprint   = $this->blueprint->build_skeleton( $pattern, $entity_refs, $country, $language );
		$template_id = isset( $pattern['template_id'] ) ? (string) $pattern['template_id'] : '';
		$template    = $template_id ? $this->templates->get( $template_id ) : null;

		$tag_context = array(
			'country'   => sanitize_key( $country ),
			'language'  => sanitize_key( $language ),
			'entities'  => $entity_refs,
			'blueprint' => $blueprint,
			'tokens'    => $tokens,
		);
		$sample_tags = array();
		if ( function_exists( 'kayan_tags' ) ) {
			foreach ( array( 'service_name', 'city_name', 'country_name', 'phone', 'whatsapp', 'hero_title', 'meta_title' ) as $tag ) {
				$sample_tags[ $tag ] = kayan_tags()->resolve_tag( $tag, $tag_context );
			}
		}

		return array(
			'ok'            => true,
			'pattern_id'    => $pattern_id,
			'template_id'   => $template_id,
			'template'      => $template,
			'entities'      => $entity_refs,
			'country'       => sanitize_key( $country ),
			'language'      => sanitize_key( $language ),
			'fingerprint'   => $fingerprint,
			'public_slug'   => $slug,
			'canonical_url' => $url,
			'existing_post' => $existing_id,
			'would_update'  => $existing_id > 0,
			'blueprint'     => $blueprint,
			'blocks'        => array_keys( isset( $blueprint['blocks'] ) ? (array) $blueprint['blocks'] : array() ),
			'media'         => $this->media->schema(),
			'data_tags'     => array(
				'context' => $tag_context,
				'sample'  => $sample_tags,
			),
			'storage'       => array(
				'post_type'          => $post_type,
				'preferred_post_type'=> isset( $pattern['preferred_post_type'] ) ? $pattern['preferred_post_type'] : '',
				'fallback_post_type' => isset( $pattern['fallback_post_type'] ) ? $pattern['fallback_post_type'] : Kayan_PSEO_Storage::POST_TYPE,
				'host_post_types'    => $this->storage->host_post_types(),
				'post_status'        => 'draft',
			),
			'note'          => 'Dry-run only. Call materialize() in a future generation phase.',
		);
	}

	/**
	 * Preview all combinations for a rule (enumeration hooked later).
	 *
	 * @param string $rule_id Rule id.
	 * @return array<string,mixed>
	 */
	public function preview_rule( $rule_id ) {
		return $this->rules->preview_combinations( $rule_id );
	}

	/**
	 * Create/update WP posts — NOT implemented in Phase 2.5.
	 *
	 * @param array<string,mixed> $preview Preview payload from preview().
	 * @return array{ok:bool,errors:string[]}
	 */
	public function materialize( array $preview ) {
		unset( $preview );
		return array(
			'ok'     => false,
			'errors' => array( 'generation_not_implemented_in_phase_2_5' ),
		);
	}

	/**
	 * Regenerate content for an existing post without changing fingerprint/URL — stub.
	 *
	 * @param int                 $post_id Post ID.
	 * @param array<string,mixed> $args    Args.
	 * @return array{ok:bool,errors:string[]}
	 */
	public function regenerate( $post_id, array $args = array() ) {
		unset( $post_id, $args );
		return array(
			'ok'     => false,
			'errors' => array( 'generation_not_implemented_in_phase_2_5' ),
		);
	}

	/**
	 * AI regenerate full page (provider contract).
	 *
	 * @param int                 $post_id Post ID.
	 * @param array<string,mixed> $args    Args.
	 * @return array{ok:bool,errors:string[]}
	 */
	public function ai_regenerate( $post_id, array $args = array() ) {
		$provider = $this->ai->get_provider( isset( $args['provider'] ) ? $args['provider'] : null );
		return $provider->regenerate( $post_id, $args );
	}

	/**
	 * Regenerate a single block without modifying the rest of the page.
	 * Architecture contract only — providers are stubs in this phase.
	 *
	 * @param int                 $post_id  Post ID.
	 * @param string              $block_id Block id.
	 * @param array<string,mixed> $args     Args (tokens, provider, prompt override).
	 * @return array{ok:bool,errors?:string[],blueprint?:array}
	 */
	public function regenerate_block( $post_id, $block_id, array $args = array() ) {
		$block_id = sanitize_key( $block_id );
		if ( ! $this->blocks->get( $block_id ) ) {
			return array(
				'ok'     => false,
				'errors' => array( 'block_not_registered' ),
			);
		}

		return $this->ai->regenerate_block( (int) $post_id, $block_id, $args );
	}

	/**
	 * Dry-run template upgrade preserving locked/manual blocks.
	 *
	 * @param int    $post_id     Post ID.
	 * @param string $template_id Template id (optional; uses blueprint template).
	 * @return array{ok:bool,errors?:string[],blueprint?:array,changed?:string[],preserved?:string[]}
	 */
	public function preview_template_upgrade( $post_id, $template_id = '' ) {
		$blueprint = $this->blueprint->get_for_post( (int) $post_id );
		if ( ! $template_id ) {
			$template_id = isset( $blueprint['template_id'] ) ? (string) $blueprint['template_id'] : '';
		}
		if ( ! $template_id ) {
			return array(
				'ok'     => false,
				'errors' => array( 'template_id_required' ),
			);
		}
		return $this->blueprint->upgrade_template( $blueprint, $template_id );
	}

	/**
	 * Engine readiness report.
	 *
	 * @return array<string,mixed>
	 */
	public function status() {
		return array(
			'phase'                => '2.6.0',
			'generation_enabled'   => false,
			'entity_types'         => count( $this->entities->get_entity_types() ),
			'patterns'             => count( $this->patterns->all() ),
			'templates'            => count( $this->templates->all() ),
			'blocks'               => count( $this->blocks->all() ),
			'rules'                => count( $this->rules->all() ),
			'storage'              => $this->storage->capabilities(),
			'ai_providers'         => $this->ai->list_providers(),
			'blueprint_schema'     => Kayan_PSEO_Blueprint::SCHEMA_VERSION,
			'media_schema'         => true,
			'block_ai'             => true,
			'blueprint_versioning' => true,
		);
	}
}
