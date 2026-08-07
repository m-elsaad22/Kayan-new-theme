<?php
/**
 * PSEO Generator API.
 *
 * preview() stays a pure dry-run. materialize()/regenerate() (Phase 4) are
 * the ONLY code paths that create/update PSEO-managed posts — always via
 * the stable fingerprint so URLs never change. AI-authored content stays a
 * null-provider stub until Phase 5; regenerate() here refreshes derived
 * data (contact info, related entities) without inventing text content.
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

	/** @var Kayan_Content_Workflow|null */
	private $workflow;

	/** @var Kayan_Quality_Engine|null */
	private $quality;

	/** @var Kayan_Dependency_Graph|null */
	private $dependency_graph;

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
	 * Wired by Kayan_PSEO_Engine after Workflow/Quality/Dependency Graph exist.
	 *
	 * @param Kayan_Content_Workflow $workflow     Workflow.
	 * @param Kayan_Quality_Engine   $quality      Quality engine.
	 * @param Kayan_Dependency_Graph $dependency_graph Dependency graph.
	 * @return void
	 */
	public function set_workflow_services( Kayan_Content_Workflow $workflow, Kayan_Quality_Engine $quality, Kayan_Dependency_Graph $dependency_graph ) {
		$this->workflow         = $workflow;
		$this->quality          = $quality;
		$this->dependency_graph = $dependency_graph;
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
	 * Create or update a WP post from a preview() payload.
	 * Never changes an existing post's fingerprint or locked slug.
	 *
	 * @param array<string,mixed> $preview Preview payload from preview().
	 * @param array<string,mixed> $args    post_status, schedule_at, rule_id, source.
	 * @return array{ok:bool,post_id?:int,created?:bool,url?:string,errors?:string[]}
	 */
	public function materialize( array $preview, array $args = array() ) {
		if ( empty( $preview['ok'] ) || empty( $preview['pattern_id'] ) ) {
			return array( 'ok' => false, 'errors' => array( 'invalid_preview' ) );
		}

		$pattern_id = sanitize_key( (string) $preview['pattern_id'] );
		$entities   = (array) $preview['entities'];
		$country    = sanitize_key( (string) $preview['country'] );
		$language   = sanitize_key( (string) $preview['language'] );
		$fingerprint = (string) $preview['fingerprint'];

		$post_type   = $this->storage->resolve_post_type( $pattern_id );
		$existing_id = $this->identity->find_post_id_by_fingerprint( $fingerprint, $this->storage->host_post_types() );

		// Safety: never silently overwrite a page an operator flagged as manually protected.
		if ( $existing_id && ! empty( $args['force'] ) === false && $this->is_manually_protected( $existing_id ) ) {
			return array( 'ok' => true, 'post_id' => $existing_id, 'created' => false, 'skipped' => true, 'reason' => 'manual_override_protected' );
		}

		$requested_status = isset( $args['post_status'] ) ? sanitize_key( $args['post_status'] ) : 'draft';
		if ( ! in_array( $requested_status, array( 'draft', 'publish', 'future', 'pending' ), true ) ) {
			$requested_status = 'draft';
		}

		$path_slug = isset( $preview['public_slug'] ) ? (string) $preview['public_slug'] : $this->identity->suggest_public_slug( $pattern_id, $preview['data_tags']['context']['tokens'] ?? array() );

		$tag_context = array(
			'country'   => $country,
			'language'  => $language,
			'entities'  => $entities,
			'blueprint' => $preview['blueprint'],
			'tokens'    => $preview['data_tags']['context']['tokens'] ?? array(),
		);
		$title   = $this->build_title( $pattern_id, $entities, $tag_context, $preview['blueprint'] );
		$excerpt = isset( $preview['blueprint']['rank_math']['description'] ) ? (string) $preview['blueprint']['rank_math']['description'] : '';

		// Always write as draft first; the Workflow engine (quality-gated) owns
		// the final live post_status — a single source of truth, never two
		// competing writers of post_status.
		$post_args = array(
			'post_type'    => $post_type,
			'post_status'  => 'draft',
			'post_title'   => $title,
			'post_excerpt' => $excerpt,
		);

		$created = false;
		if ( $existing_id ) {
			$post_args['ID'] = $existing_id;
			$post_id         = wp_update_post( $post_args, true );
		} else {
			$post_args['post_name']   = sanitize_title( implode( '-', array_values( $tag_context['tokens'] ) ) );
			$post_args['post_author'] = (int) apply_filters( 'kayan_pseo_default_author', 1 );
			$post_id                  = wp_insert_post( $post_args, true );
			$created                  = true;
		}

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return array( 'ok' => false, 'errors' => array( is_wp_error( $post_id ) ? $post_id->get_error_message() : 'write_failed' ) );
		}
		$post_id = (int) $post_id;

		$this->write_post_meta( $post_id, $created, $pattern_id, $entities, $country, $language, $path_slug, $preview['blueprint'], $args );

		if ( $this->dependency_graph ) {
			$this->dependency_graph->record( $post_id, $entities );
		}

		$workflow_result = $this->apply_workflow_state( $post_id, $created, $requested_status, $args );

		if ( function_exists( 'kayan_cache' ) ) {
			kayan_cache()->flush_group( 'query' );
		}
		if ( function_exists( 'kayan_logger' ) ) {
			kayan_logger()->log( 'generator', $created ? 'pseo.materialize.created' : 'pseo.materialize.updated', array( 'post_id' => $post_id, 'pattern_id' => $pattern_id ) );
		}

		return array(
			'ok'          => true,
			'post_id'     => $post_id,
			'created'     => $created,
			'url'         => $this->identity->build_canonical_url( $pattern_id, $tag_context['tokens'], $country, $language ),
			'fingerprint' => $fingerprint,
			'workflow'    => $workflow_result,
		);
	}

	/**
	 * @param int    $post_id          Post ID.
	 * @return bool
	 */
	private function is_manually_protected( $post_id ) {
		return (bool) get_post_meta( (int) $post_id, 'kayan_pseo_manual_override', true );
	}

	/**
	 * Advance a freshly written post through the Workflow to its requested
	 * lifecycle state. Quality-gated for publish/scheduled — a failed check
	 * leaves the post safely as a draft pending Human Review instead of
	 * hard-failing the whole materialize() call.
	 *
	 * @param int    $post_id          Post ID.
	 * @param bool   $created          Newly created.
	 * @param string $requested_status draft|publish|future|pending.
	 * @param array  $args             source, schedule_at, force.
	 * @return array<string,mixed>|null
	 */
	private function apply_workflow_state( $post_id, $created, $requested_status, array $args ) {
		if ( ! $this->workflow ) {
			return null;
		}

		$source = isset( $args['source'] ) ? sanitize_key( (string) $args['source'] ) : 'manual';
		$target = Kayan_Content_Workflow::DRAFT;
		if ( 'publish' === $requested_status ) {
			$target = Kayan_Content_Workflow::PUBLISHED;
		} elseif ( 'future' === $requested_status ) {
			$target = Kayan_Content_Workflow::SCHEDULED;
		} elseif ( 'pending' === $requested_status ) {
			$target = Kayan_Content_Workflow::HUMAN_REVIEW;
		} elseif ( in_array( $source, array( 'ai', 'ai_regenerate' ), true ) ) {
			$target = Kayan_Content_Workflow::AI_DRAFT;
		}

		// New drafts/ai-drafts/review states never fail a "transition" (map allows from DRAFT baseline);
		// force only for the very first assignment on brand-new posts to skip map validation noise.
		$result = $this->workflow->transition(
			$post_id,
			$target,
			array(
				'force'       => $created && Kayan_Content_Workflow::DRAFT !== $target ? true : ! empty( $args['force'] ),
				'schedule_at' => $args['schedule_at'] ?? '',
				'note'        => $created ? 'materialize:create' : 'materialize:update',
			)
		);

		if ( empty( $result['ok'] ) && in_array( $target, array( Kayan_Content_Workflow::PUBLISHED, Kayan_Content_Workflow::SCHEDULED ), true ) ) {
			// Quality gate rejected publish/schedule — route to Human Review instead of leaving it silently stuck.
			$this->workflow->transition( $post_id, Kayan_Content_Workflow::HUMAN_REVIEW, array( 'force' => true, 'note' => 'quality_gate_downgrade' ) );
			$result['downgraded_to'] = Kayan_Content_Workflow::HUMAN_REVIEW;
		}

		return $result;
	}

	/**
	 * Refresh derived block data (contact info, related entities, breadcrumb)
	 * for an existing post without touching its fingerprint/URL/locked slug.
	 * Locked blocks are always preserved untouched.
	 *
	 * @param int                 $post_id Post ID.
	 * @param array<string,mixed> $args    mode: content_only|full, dry_run.
	 * @return array{ok:bool,post_id?:int,changed_blocks?:string[],errors?:string[]}
	 */
	public function regenerate( $post_id, array $args = array() ) {
		$post_id = (int) $post_id;
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return array( 'ok' => false, 'errors' => array( 'post_not_found' ) );
		}
		if ( empty( $args['force'] ) && $this->is_manually_protected( $post_id ) ) {
			return array( 'ok' => false, 'errors' => array( 'manual_override_protected' ) );
		}
		// Full regeneration of an already-approved/published page needs explicit confirmation (safety).
		$current_state = $this->workflow ? $this->workflow->get_state( $post_id ) : '';
		if ( 'full' === ( $args['mode'] ?? 'content_only' )
			&& in_array( $current_state, array( Kayan_Content_Workflow::APPROVED, Kayan_Content_Workflow::PUBLISHED ), true )
			&& empty( $args['confirm'] ) && empty( $args['force'] )
		) {
			return array( 'ok' => false, 'errors' => array( 'confirmation_required' ), 'current_state' => $current_state );
		}

		$blueprint  = $this->blueprint->get_for_post( $post_id );
		$pattern_id = (string) get_post_meta( $post_id, Kayan_PSEO_Identity::META_PATTERN, true );
		$entities   = get_post_meta( $post_id, Kayan_PSEO_Identity::META_ENTITIES, true );
		$entities   = is_array( $entities ) ? $entities : array();
		$countries_meta = get_post_meta( $post_id, Kayan_Content_Locale::META_COUNTRIES, true );
		$country    = is_array( $countries_meta ) && ! empty( $countries_meta ) ? sanitize_key( (string) $countries_meta[0] ) : ( function_exists( 'kayan_platform_country' ) ? kayan_platform_country() : '' );
		$language   = (string) get_post_meta( $post_id, Kayan_Content_Locale::META_LANG, true );
		$language   = $language ? $language : 'ar';

		$mode = isset( $args['mode'] ) ? sanitize_key( $args['mode'] ) : 'content_only';
		if ( 'full' === $mode && $pattern_id ) {
			$upgrade = $this->blueprint->upgrade_template( $blueprint, $blueprint['template_id'] );
			if ( ! empty( $upgrade['ok'] ) ) {
				$blueprint = $upgrade['blueprint'];
			}
		}

		$context = array(
			'country'  => $country,
			'language' => $language,
			'entities' => $entities,
		);

		$changed = array();
		foreach ( $blueprint['blocks'] as $block_id => $instance ) {
			if ( ! empty( $instance['locked'] ) ) {
				continue;
			}
			$derived = $this->derive_block_data( $block_id, $instance['data'], $entities, $context );
			if ( null === $derived ) {
				continue;
			}
			$blueprint['blocks'][ $block_id ]['data'] = $derived;
			$blueprint['blocks'][ $block_id ]['source'] = 'regenerate';
			$changed[] = $block_id;
		}

		if ( ! empty( $args['dry_run'] ) ) {
			return array( 'ok' => true, 'post_id' => $post_id, 'changed_blocks' => $changed, 'blueprint' => $blueprint );
		}

		if ( $changed ) {
			$blueprint['blueprint_version'] = absint( $blueprint['blueprint_version'] ?? 1 ) + 1;
			$blueprint['history'][]         = array(
				'blueprint_version' => $blueprint['blueprint_version'],
				'at'                => gmdate( 'c' ),
				'note'              => 'regenerate:' . $mode,
				'changed_blocks'    => $changed,
			);
			update_post_meta( $post_id, Kayan_PSEO_Blueprint::META_BLUEPRINT, $this->blueprint->sanitize( $blueprint ) );
			update_post_meta( $post_id, 'kayan_pseo_source', 'regenerate' );
		}

		// A page that was flagged as stale is never silently re-published — it always lands
		// back in Human Review unless the caller explicitly asks to auto-republish (and even
		// then, publishing is still quality-gated).
		$workflow_result = null;
		if ( $this->workflow && in_array( $current_state, array( Kayan_Content_Workflow::NEEDS_REGENERATION, Kayan_Content_Workflow::NEEDS_UPDATE, Kayan_Content_Workflow::FAILED ), true ) ) {
			if ( ! empty( $args['auto_republish'] ) ) {
				$workflow_result = $this->workflow->transition( $post_id, Kayan_Content_Workflow::PUBLISHED, array( 'note' => 'regenerate:auto_republish' ) );
				if ( empty( $workflow_result['ok'] ) ) {
					$this->workflow->transition( $post_id, Kayan_Content_Workflow::HUMAN_REVIEW, array( 'force' => true, 'note' => 'regenerate:needs_review' ) );
				}
			} else {
				$workflow_result = $this->workflow->transition( $post_id, Kayan_Content_Workflow::HUMAN_REVIEW, array( 'force' => true, 'note' => 'regenerate:needs_review' ) );
			}
		}

		if ( function_exists( 'kayan_cache' ) ) {
			kayan_cache()->flush_group( 'query' );
		}
		if ( function_exists( 'kayan_logger' ) ) {
			kayan_logger()->log( 'generator', 'pseo.regenerate.completed', array( 'post_id' => $post_id, 'changed_blocks' => $changed ) );
		}

		return array( 'ok' => true, 'post_id' => $post_id, 'changed_blocks' => $changed, 'workflow' => $workflow_result );
	}

	/**
	 * AI-translate a generated page into another registered language,
	 * keeping the translation linked to its source via the existing
	 * Content Locale `kayan_translation_group` contract — never a
	 * second translation-linking system.
	 *
	 * Reuses materialize() for the actual write (same fingerprint/workflow/
	 * dependency-graph plumbing); this method only builds the translated
	 * blueprint and preview payload.
	 *
	 * @param int    $post_id      Source post ID.
	 * @param string $target_lang  Target language code.
	 * @param array  $args         post_status, provider.
	 * @return array{ok:bool,post_id?:int,created?:bool,source_post_id?:int,errors?:string[]}
	 */
	public function translate_post( $post_id, $target_lang, array $args = array() ) {
		$post_id     = (int) $post_id;
		$target_lang = sanitize_key( $target_lang );
		$post        = get_post( $post_id );

		if ( ! $post || '' === $target_lang ) {
			return array( 'ok' => false, 'errors' => array( 'invalid_arguments' ) );
		}
		if ( function_exists( 'kayan_platform' ) && ! kayan_platform()->languages->exists( $target_lang ) ) {
			return array( 'ok' => false, 'errors' => array( 'unknown_target_language' ) );
		}
		if ( ! function_exists( 'kayan_ai' ) || ! kayan_ai()->is_any_available() ) {
			return array( 'ok' => false, 'errors' => array( 'ai_not_configured' ) );
		}

		$pattern_id = (string) get_post_meta( $post_id, Kayan_PSEO_Identity::META_PATTERN, true );
		$entities   = get_post_meta( $post_id, Kayan_PSEO_Identity::META_ENTITIES, true );
		$entities   = is_array( $entities ) ? $entities : array();
		$countries  = get_post_meta( $post_id, Kayan_Content_Locale::META_COUNTRIES, true );
		$country    = is_array( $countries ) && $countries ? sanitize_key( (string) $countries[0] ) : '';
		$source_lang = (string) get_post_meta( $post_id, Kayan_Content_Locale::META_LANG, true );
		$source_lang = $source_lang ?: 'ar';
		$blueprint  = $this->blueprint->get_for_post( $post_id );

		if ( ! $pattern_id || ! $this->patterns->get( $pattern_id ) ) {
			return array( 'ok' => false, 'errors' => array( 'source_not_pseo_managed' ) );
		}
		if ( $source_lang === $target_lang ) {
			return array( 'ok' => false, 'errors' => array( 'same_language' ) );
		}

		// Keep the source ↔ translation link (existing Content Locale contract).
		$group = get_post_meta( $post_id, Kayan_Content_Locale::META_TRANSLATION_GROUP, true );
		if ( ! $group ) {
			$group = function_exists( 'wp_generate_uuid4' ) ? wp_generate_uuid4() : uniqid( 'kayan_tr_', true );
			update_post_meta( $post_id, Kayan_Content_Locale::META_TRANSLATION_GROUP, $group );
		}

		$translated_blueprint = $this->translate_blueprint( $blueprint, $source_lang, $target_lang, $args['provider'] ?? null );
		if ( is_wp_error( $translated_blueprint ) ) {
			return array( 'ok' => false, 'errors' => array( $translated_blueprint->get_error_message() ) );
		}

		$title_result = kayan_ai()->translate( (string) $post->post_title, $source_lang, $target_lang, array( 'provider' => $args['provider'] ?? null ) );
		$title        = ! empty( $title_result['ok'] ) ? $title_result['text'] : $post->post_title;
		$translated_blueprint['rank_math']['title'] = $title;

		$tokens = array();
		foreach ( $entities as $type => $ref ) {
			$tokens[ $type . '_slug' ] = $ref;
		}

		$preview = array(
			'ok'          => true,
			'pattern_id'  => $pattern_id,
			'entities'    => $entities,
			'country'     => $country,
			'language'    => $target_lang,
			'fingerprint' => $this->identity->fingerprint( $pattern_id, $entities, $country, $target_lang ),
			'public_slug' => $this->identity->suggest_public_slug( $pattern_id, $tokens ),
			'blueprint'   => $translated_blueprint,
			'data_tags'   => array( 'context' => array( 'tokens' => $tokens ) ),
		);

		$result = $this->materialize(
			$preview,
			array(
				'post_status' => $args['post_status'] ?? 'draft',
				'source'      => 'ai',
			)
		);

		if ( ! empty( $result['ok'] ) && ! empty( $result['post_id'] ) ) {
			update_post_meta( (int) $result['post_id'], Kayan_Content_Locale::META_TRANSLATION_GROUP, $group );
			update_post_meta( (int) $result['post_id'], Kayan_Content_Locale::META_LANG, $target_lang );
			$result['source_post_id'] = $post_id;
			$result['target_language'] = $target_lang;
		}

		return $result;
	}

	/**
	 * @param array       $blueprint   Source blueprint.
	 * @param string      $from        From lang.
	 * @param string      $to          To lang.
	 * @param string|null $provider    Optional provider override.
	 * @return array|WP_Error
	 */
	private function translate_blueprint( array $blueprint, $from, $to, $provider = null ) {
		if ( empty( $blueprint['blocks'] ) || ! is_array( $blueprint['blocks'] ) ) {
			return $blueprint;
		}

		foreach ( $blueprint['blocks'] as $block_id => &$instance ) {
			if ( ! empty( $instance['locked'] ) ) {
				continue; // safety: locked blocks are never translated/overwritten.
			}
			if ( empty( $instance['data'] ) || ! is_array( $instance['data'] ) ) {
				continue;
			}
			$instance['data'] = $this->translate_data_recursive( $instance['data'], $from, $to, $provider );
		}
		unset( $instance );

		return $blueprint;
	}

	/**
	 * @param array       $data     Data.
	 * @param string      $from     From.
	 * @param string      $to       To.
	 * @param string|null $provider Provider.
	 * @return array
	 */
	private function translate_data_recursive( array $data, $from, $to, $provider ) {
		foreach ( $data as $key => $value ) {
			// IDs / refs / numbers must never be "translated" — only human-readable strings.
			if ( is_string( $value ) && '' !== trim( $value ) && ! is_numeric( $value ) && ! in_array( $key, array( 'url', 'image_id', 'poster_id', 'embed', 'primary_url', 'secondary_url' ), true ) ) {
				$translated  = kayan_ai()->translate( $value, $from, $to, array( 'provider' => $provider ) );
				$data[ $key ] = ! empty( $translated['ok'] ) ? $translated['text'] : $value;
			} elseif ( is_array( $value ) ) {
				$data[ $key ] = $this->translate_data_recursive( $value, $from, $to, $provider );
			}
		}
		return $data;
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
	 * @param int                  $post_id    Post ID.
	 * @param bool                 $created    Newly created.
	 * @param string               $pattern_id Pattern.
	 * @param array<string,string> $entities   Entities.
	 * @param string               $country    Country.
	 * @param string               $language   Language.
	 * @param string               $path_slug  Full pattern path slug.
	 * @param array<string,mixed>  $blueprint  Blueprint.
	 * @param array<string,mixed>  $args       rule_id, source.
	 * @return void
	 */
	private function write_post_meta( $post_id, $created, $pattern_id, array $entities, $country, $language, $path_slug, array $blueprint, array $args ) {
		$fingerprint = $this->identity->fingerprint( $pattern_id, $entities, $country, $language );

		update_post_meta( $post_id, Kayan_PSEO_Identity::META_FINGERPRINT, $fingerprint );
		update_post_meta( $post_id, Kayan_PSEO_Identity::META_PATTERN, $pattern_id );
		update_post_meta( $post_id, Kayan_PSEO_Identity::META_ENTITIES, $entities );
		if ( ! empty( $args['rule_id'] ) ) {
			update_post_meta( $post_id, Kayan_PSEO_Identity::META_RULE, sanitize_key( (string) $args['rule_id'] ) );
		}

		$blueprint['pattern_id'] = $pattern_id;
		update_post_meta( $post_id, Kayan_PSEO_Blueprint::META_BLUEPRINT, $this->blueprint->sanitize( $blueprint ) );
		update_post_meta( $post_id, 'kayan_pseo_template_id', sanitize_key( (string) ( $blueprint['template_id'] ?? '' ) ) );
		update_post_meta( $post_id, 'kayan_pseo_source', sanitize_key( (string) ( $args['source'] ?? ( $created ? 'manual' : 'regenerate' ) ) ) );

		// Locked slug is written once and never overwritten on subsequent materialize() calls.
		$existing_locked = get_post_meta( $post_id, 'kayan_pseo_locked_slug', true );
		if ( '' === $existing_locked ) {
			update_post_meta( $post_id, 'kayan_pseo_locked_slug', sanitize_title( $path_slug ) );
		}

		if ( ! empty( $blueprint['media'] ) ) {
			update_post_meta( $post_id, Kayan_PSEO_Media::META_MEDIA, $this->media->sanitize( $blueprint['media'] ) );
		}

		// Content Locale — reuse existing meta contract, never a second locale system.
		update_post_meta( $post_id, Kayan_Content_Locale::META_LANG, $language );
		update_post_meta( $post_id, Kayan_Content_Locale::META_COUNTRIES, array( $country ) );
		update_post_meta( $post_id, Kayan_Content_Locale::META_PUBLIC_SLUG, $path_slug );

		// Rank Math source fields — write-through to RM's own postmeta contract only when supplied.
		if ( ! empty( $blueprint['rank_math'] ) && is_array( $blueprint['rank_math'] ) ) {
			$rm = $blueprint['rank_math'];
			if ( ! empty( $rm['title'] ) ) {
				update_post_meta( $post_id, 'rank_math_title', sanitize_text_field( (string) $rm['title'] ) );
			}
			if ( ! empty( $rm['description'] ) ) {
				update_post_meta( $post_id, 'rank_math_description', sanitize_textarea_field( (string) $rm['description'] ) );
			}
			if ( ! empty( $rm['focus_keyword'] ) ) {
				update_post_meta( $post_id, 'rank_math_focus_keyword', sanitize_text_field( (string) $rm['focus_keyword'] ) );
			}
			if ( ! empty( $rm['robots'] ) && is_array( $rm['robots'] ) ) {
				update_post_meta( $post_id, 'rank_math_robots', array_values( array_map( 'sanitize_key', $rm['robots'] ) ) );
			}
		}

		// Taxonomy assignment for entities that map to real taxonomies (reuse existing terms only).
		$tax_map = array( 'city' => 'cities', 'category' => 'service_categories' );
		foreach ( $tax_map as $etype => $taxonomy ) {
			if ( empty( $entities[ $etype ] ) || ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}
			$term = get_term_by( 'slug', sanitize_title( (string) $entities[ $etype ] ), $taxonomy );
			if ( $term && ! is_wp_error( $term ) ) {
				wp_set_object_terms( $post_id, array( (int) $term->term_id ), $taxonomy, false );
			}
		}
	}

	/**
	 * @param string               $pattern_id  Pattern.
	 * @param array<string,string> $entities    Entities.
	 * @param array<string,mixed>  $tag_context Dynamic tag context.
	 * @param array<string,mixed>  $blueprint   Blueprint.
	 * @return string
	 */
	private function build_title( $pattern_id, array $entities, array $tag_context, array $blueprint ) {
		if ( ! empty( $blueprint['rank_math']['title'] ) ) {
			return sanitize_text_field( (string) $blueprint['rank_math']['title'] );
		}
		if ( function_exists( 'kayan_tags' ) ) {
			$meta_title = (string) kayan_tags()->resolve_tag( 'meta_title', $tag_context );
			if ( '' !== $meta_title ) {
				return $meta_title;
			}
		}

		$pattern = $this->patterns->get( $pattern_id );
		$names   = array();
		foreach ( (array) ( $pattern['entities'] ?? array_keys( $entities ) ) as $etype ) {
			if ( function_exists( 'kayan_entity' ) && ! empty( $entities[ $etype ] ) ) {
				$name = kayan_entity()->name( $etype, $entities[ $etype ] );
				if ( $name ) {
					$names[] = $name;
				}
			}
		}

		/* translators: %s: entity names joined (e.g. "Plumbing – Dubai") */
		return $names ? implode( ' – ', $names ) : ucfirst( str_replace( '_', ' ', $pattern_id ) );
	}

	/**
	 * Recompute a block's derived fields from live data. Returns null when
	 * nothing to derive (block stays untouched) — never invents AI copy.
	 *
	 * @param string               $block_id Block id.
	 * @param array<string,mixed>  $data     Current data.
	 * @param array<string,string> $entities Entities.
	 * @param array<string,mixed>  $context  country/language/entities.
	 * @return array<string,mixed>|null
	 */
	private function derive_block_data( $block_id, array $data, array $entities, array $context ) {
		switch ( $block_id ) {
			case 'cta':
				if ( function_exists( 'kayan_platform_setting' ) ) {
					$country = $context['country'];
					if ( empty( $data['phone'] ) ) {
						$data['phone'] = (string) kayan_platform_setting( 'phone', $country, '' );
					}
					if ( empty( $data['whatsapp'] ) ) {
						$data['whatsapp'] = (string) kayan_platform_setting( 'whatsapp', $country, '' );
					}
				}
				return $data;

			case 'related_services':
				if ( empty( $data['post_ids'] ) && ! empty( $entities['service'] ) && function_exists( 'kayan_entity' ) ) {
					$related = kayan_entity()->related( 'service', $entities['service'], 'service' );
					$data['post_ids'] = array_slice( array_filter( array_map( static function ( $dto ) {
						return isset( $dto['id'] ) ? (int) $dto['id'] : 0;
					}, $related ) ), 0, 6 );
				}
				return $data;

			case 'related_cities':
				if ( empty( $data['term_ids'] ) && ! empty( $entities['city'] ) && function_exists( 'kayan_entity' ) ) {
					$related = kayan_entity()->related( 'city', $entities['city'], 'city' );
					$data['term_ids'] = array_slice( array_filter( array_map( static function ( $dto ) {
						return isset( $dto['id'] ) ? (int) $dto['id'] : 0;
					}, $related ) ), 0, 6 );
				}
				return $data;

			case 'reviews':
				if ( empty( $data['post_ids'] ) && function_exists( 'kayan_query' ) ) {
					$reviews = kayan_query()->reviews( array( 'number' => 3 ) );
					$data['post_ids'] = array_map( static function ( $item ) {
						return (int) $item['id'];
					}, $reviews['items'] ?? array() );
				}
				return $data;

			case 'breadcrumb':
				if ( function_exists( 'kayan_entity' ) ) {
					$items = array();
					foreach ( $entities as $etype => $ref ) {
						$name = kayan_entity()->name( $etype, $ref );
						if ( $name ) {
							$items[] = array( 'label' => $name, 'url' => '' );
						}
					}
					if ( $items ) {
						$data['items'] = $items;
					}
				}
				return $data;

			default:
				return null; // AI-authored blocks (hero/faq/gallery/videos/pricing/areas/internal_links) stay untouched until Phase 5.
		}
	}

	/**
	 * Engine readiness report.
	 *
	 * @return array<string,mixed>
	 */
	public function status() {
		return array(
			'phase'                => defined( 'KAYAN_PLATFORM_VERSION' ) ? KAYAN_PLATFORM_VERSION : '4.0.0',
			'generation_enabled'   => true,
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
