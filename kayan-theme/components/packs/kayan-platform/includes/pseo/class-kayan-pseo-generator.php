<?php
/**
 * PSEO Generator API — dry-run / preview only in Phase 2.5.
 *
 * materialize() / regenerate() intentionally refuse to write posts.
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

	public function __construct(
		Kayan_Programmatic_SEO $entities,
		Kayan_PSEO_Patterns $patterns,
		Kayan_PSEO_Rules $rules,
		Kayan_PSEO_Identity $identity,
		Kayan_PSEO_Blueprint $blueprint,
		Kayan_PSEO_Storage $storage,
		Kayan_PSEO_AI $ai
	) {
		$this->entities  = $entities;
		$this->patterns  = $patterns;
		$this->rules     = $rules;
		$this->identity  = $identity;
		$this->blueprint = $blueprint;
		$this->storage   = $storage;
		$this->ai        = $ai;
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
		$existing_id = $this->identity->find_post_id_by_fingerprint( $fingerprint, $this->storage->post_type() );
		$blueprint   = $this->blueprint->build_skeleton( $pattern, $entity_refs, $country, $language );

		return array(
			'ok'            => true,
			'pattern_id'    => $pattern_id,
			'entities'      => $entity_refs,
			'country'       => sanitize_key( $country ),
			'language'      => sanitize_key( $language ),
			'fingerprint'   => $fingerprint,
			'public_slug'   => $slug,
			'canonical_url' => $url,
			'existing_post' => $existing_id,
			'would_update'  => $existing_id > 0,
			'blueprint'     => $blueprint,
			'storage'       => array(
				'post_type'   => $this->storage->post_type(),
				'post_status' => 'draft',
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
	 * AI regenerate stub.
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
	 * Engine readiness report.
	 *
	 * @return array<string,mixed>
	 */
	public function status() {
		return array(
			'phase'              => '2.5',
			'generation_enabled' => false,
			'entity_types'       => count( $this->entities->get_entity_types() ),
			'patterns'           => count( $this->patterns->all() ),
			'rules'              => count( $this->rules->all() ),
			'storage'            => $this->storage->capabilities(),
			'ai_providers'       => $this->ai->list_providers(),
		);
	}
}
