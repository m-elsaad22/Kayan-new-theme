<?php
/**
 * Kayan_PSEO_Engine — Phase 2.5 Programmatic SEO Engine facade.
 *
 * Orchestrates entities, patterns, templates, blocks, media, rules, identity,
 * storage, jobs, AI, generator.
 * Does not create content. Does not redesign admin UI.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Engine {

	/** @var Kayan_Programmatic_SEO */
	public $entities;

	/** @var Kayan_PSEO_Patterns */
	public $patterns;

	/** @var Kayan_PSEO_Blocks */
	public $blocks;

	/** @var Kayan_PSEO_Templates */
	public $templates;

	/** @var Kayan_PSEO_Media */
	public $media;

	/** @var Kayan_PSEO_Rules */
	public $rules;

	/** @var Kayan_PSEO_Identity */
	public $identity;

	/** @var Kayan_PSEO_Blueprint */
	public $blueprint;

	/** @var Kayan_PSEO_Storage */
	public $storage;

	/** @var Kayan_PSEO_Jobs */
	public $jobs;

	/** @var Kayan_PSEO_AI */
	public $ai;

	/** @var Kayan_PSEO_Generator */
	public $generator;

	/** @var Kayan_PSEO_Renderer */
	public $renderer;

	/** @var Kayan_PSEO_Scheduler */
	public $scheduler;

	/** @var Kayan_Content_Workflow|null */
	public $workflow;

	/** @var Kayan_Quality_Engine|null */
	public $quality;

	/** @var Kayan_Dependency_Graph|null */
	public $dependencies;

	/** @var Kayan_Country_Engine */
	private $countries;

	public function __construct(
		Kayan_Programmatic_SEO $entities,
		Kayan_Content_Locale $locale,
		Kayan_Country_Engine $countries
	) {
		$this->entities  = $entities;
		$this->countries = $countries;
		$this->blocks    = new Kayan_PSEO_Blocks();
		$this->templates = new Kayan_PSEO_Templates( $this->blocks );
		$this->media     = new Kayan_PSEO_Media();
		$this->patterns  = new Kayan_PSEO_Patterns();
		$this->rules     = new Kayan_PSEO_Rules( $this->patterns );
		$this->identity  = new Kayan_PSEO_Identity( $this->patterns );
		$this->blueprint = new Kayan_PSEO_Blueprint();
		$this->blueprint->set_engines( $this->blocks, $this->templates, $this->media );
		$this->storage   = new Kayan_PSEO_Storage( $this->blueprint, $locale );
		$this->storage->set_engines( $this->patterns, $this->media );
		$this->jobs      = new Kayan_PSEO_Jobs();
		$this->ai        = new Kayan_PSEO_AI( $this->blocks, $this->blueprint );
		$this->generator = new Kayan_PSEO_Generator(
			$this->entities,
			$this->patterns,
			$this->rules,
			$this->identity,
			$this->blueprint,
			$this->storage,
			$this->ai,
			$this->templates,
			$this->blocks,
			$this->media
		);
		$this->jobs->set_dependencies( $this->generator, $this->rules );
		$this->renderer  = new Kayan_PSEO_Renderer( $this->blueprint, $this->storage, $this->blocks );
		$this->scheduler = new Kayan_PSEO_Scheduler( $this->jobs );
	}

	/**
	 * Wired by Kayan_Platform once Workflow/Quality/Dependency Graph exist
	 * (they, in turn, depend on this engine's Storage) — avoids a
	 * constructor circularity while keeping every engine a real dependency.
	 *
	 * @param Kayan_Content_Workflow  $workflow     Workflow.
	 * @param Kayan_Quality_Engine    $quality      Quality engine.
	 * @param Kayan_Dependency_Graph  $dependencies Dependency graph.
	 * @return void
	 */
	public function set_workflow_services( Kayan_Content_Workflow $workflow, Kayan_Quality_Engine $quality, Kayan_Dependency_Graph $dependencies ) {
		$this->workflow     = $workflow;
		$this->quality      = $quality;
		$this->dependencies = $dependencies;
		$this->generator->set_workflow_services( $workflow, $quality, $dependencies );
	}

	/**
	 * Expand a rule and enqueue one bulk job (Bulk Generation entry point).
	 *
	 * @param string               $rule_id Rule id.
	 * @param array<string,mixed>  $options post_status, ai_enabled.
	 * @return array{ok:bool,job?:array,errors?:string[],count?:int,truncated?:bool}
	 */
	public function bulk_generate( $rule_id, array $options = array() ) {
		$expansion = $this->rules->preview_combinations( $rule_id );
		if ( empty( $expansion['ok'] ) ) {
			return $expansion;
		}
		if ( empty( $expansion['combinations'] ) ) {
			return array( 'ok' => false, 'errors' => array( 'no_combinations' ) );
		}

		$enqueue = $this->jobs->enqueue(
			'bulk',
			array(
				'rule_id'      => $rule_id,
				'combinations' => $expansion['combinations'],
				'options'      => $options,
			)
		);

		if ( ! empty( $enqueue['ok'] ) ) {
			$enqueue['count']     = $expansion['count'];
			$enqueue['truncated'] = $expansion['truncated'];
		}

		return $enqueue;
	}

	/**
	 * Enqueue a regenerate/ai_regenerate job for one or many posts.
	 *
	 * @param int[]  $post_ids Post ids.
	 * @param array  $options  Options (ai_enabled toggles job type).
	 * @return array{ok:bool,job?:array,errors?:string[]}
	 */
	public function regenerate_bulk( array $post_ids, array $options = array() ) {
		$type = ! empty( $options['ai_enabled'] ) ? 'ai_regenerate' : 'regenerate';
		return $this->jobs->enqueue(
			$type,
			array(
				'post_ids' => $post_ids,
				'options'  => $options,
			)
		);
	}

	/**
	 * Enqueue an AI translation job for one or more generated pages.
	 *
	 * @param int[]  $post_ids     Source post ids.
	 * @param string $target_lang  Target language code.
	 * @param array  $options      post_status, provider.
	 * @return array{ok:bool,job?:array,errors?:string[]}
	 */
	public function translate_bulk( array $post_ids, $target_lang, array $options = array() ) {
		$options['target_language'] = sanitize_key( $target_lang );
		return $this->jobs->enqueue(
			'translate',
			array(
				'post_ids' => $post_ids,
				'options'  => $options,
			)
		);
	}

	/**
	 * @return void
	 */
	public function register() {
		$this->blocks->register();
		$this->templates->register();
		$this->media->register();
		$this->patterns->register();
		$this->rules->register();
		$this->storage->register();
		$this->jobs->register();
		$this->ai->register();
		$this->renderer->register();
		$this->scheduler->register();

		add_action( 'init', array( $this, 'register_pattern_rewrites' ), 7 );

		/**
		 * @param Kayan_PSEO_Engine $engine Engine.
		 */
		do_action( 'kayan_pseo_engine_registered', $this );
	}

	/**
	 * Multi-segment rewrites for combination URLs (architecture readiness).
	 * Does not create pages — only allows future kayan_pseo public_slugs to resolve.
	 *
	 * @return void
	 */
	public function register_pattern_rewrites() {
		if ( ! function_exists( 'kayan_platform_owns_routing' ) || ! kayan_platform_owns_routing() ) {
			return;
		}
		if ( function_exists( 'kayan_i18n_is_enabled' ) && ! kayan_i18n_is_enabled() ) {
			return;
		}

		$country_alt = $this->country_regex_alt();
		if ( '' === $country_alt ) {
			return;
		}

		$bases = array( 'services', 'faqs', 'pricing', 'service-category', 'reviews', 'portfolio', 'before-after' );

		/**
		 * @param string[] $bases Base segments.
		 */
		$bases = apply_filters( 'kayan_pseo_pattern_rewrite_bases', $bases );

		$pt = Kayan_PSEO_Storage::POST_TYPE;

		foreach ( $bases as $base ) {
			$base = preg_quote( sanitize_title( $base ), '#' );
			if ( '' === $base ) {
				continue;
			}

			// /en/{country}/{base}/{a}/{b}/
			add_rewrite_rule(
				'^en/(' . $country_alt . ')/' . $base . '/([^/]+)/([^/]+)/?$',
				'index.php?kayan_lang=en&kayan_country=$matches[1]&post_type=' . $pt . '&kayan_public_slug=' . $base . '/$matches[2]/$matches[3]&name=$matches[2]-$matches[3]&kayan_route=singular',
				'top'
			);
			// /en/{base}/{a}/{b}/
			add_rewrite_rule(
				'^en/' . $base . '/([^/]+)/([^/]+)/?$',
				'index.php?kayan_lang=en&post_type=' . $pt . '&kayan_public_slug=' . $base . '/$matches[1]/$matches[2]&name=$matches[1]-$matches[2]&kayan_route=singular',
				'top'
			);
			// /{country}/{base}/{a}/{b}/
			add_rewrite_rule(
				'^(' . $country_alt . ')/' . $base . '/([^/]+)/([^/]+)/?$',
				'index.php?kayan_country=$matches[1]&post_type=' . $pt . '&kayan_public_slug=' . $base . '/$matches[2]/$matches[3]&name=$matches[2]-$matches[3]&kayan_route=singular',
				'top'
			);
			// /{base}/{a}/{b}/  (default country Arabic)
			add_rewrite_rule(
				'^' . $base . '/([^/]+)/([^/]+)/?$',
				'index.php?post_type=' . $pt . '&kayan_public_slug=' . $base . '/$matches[1]/$matches[2]&name=$matches[1]-$matches[2]&kayan_route=singular',
				'top'
			);

			// 3-segment combinations (e.g. brand × service × city)
			add_rewrite_rule(
				'^en/(' . $country_alt . ')/' . $base . '/([^/]+)/([^/]+)/([^/]+)/?$',
				'index.php?kayan_lang=en&kayan_country=$matches[1]&post_type=' . $pt . '&kayan_public_slug=' . $base . '/$matches[2]/$matches[3]/$matches[4]&name=$matches[2]-$matches[3]-$matches[4]&kayan_route=singular',
				'top'
			);
			add_rewrite_rule(
				'^(' . $country_alt . ')/' . $base . '/([^/]+)/([^/]+)/([^/]+)/?$',
				'index.php?kayan_country=$matches[1]&post_type=' . $pt . '&kayan_public_slug=' . $base . '/$matches[2]/$matches[3]/$matches[4]&name=$matches[2]-$matches[3]-$matches[4]&kayan_route=singular',
				'top'
			);
		}
	}

	/**
	 * Public API snapshot for integrators.
	 *
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'version'   => defined( 'KAYAN_PLATFORM_VERSION' ) ? KAYAN_PLATFORM_VERSION : '6.0.0',
			'status'    => $this->generator->status(),
			'entities'  => array_keys( $this->entities->get_entity_types() ),
			'patterns'  => array_keys( $this->patterns->all() ),
			'templates' => array_keys( $this->templates->all() ),
			'blocks'    => array_keys( $this->blocks->all() ),
			'storage'   => $this->storage->capabilities(),
			'entity_engine' => function_exists( 'kayan_entity' ) ? kayan_entity()->describe() : null,
			'data_tags'     => function_exists( 'kayan_tags' ) ? kayan_tags()->describe() : null,
			'apis'      => array(
				'preview'            => 'kayan_platform()->pseo->generator->preview()',
				'preview_rule'       => 'kayan_platform()->pseo->generator->preview_rule()',
				'save_rule'          => 'kayan_platform()->pseo->rules->save()',
				'enqueue_job'        => 'kayan_platform()->pseo->jobs->enqueue()',
				'fingerprint'        => 'kayan_platform()->pseo->identity->fingerprint()',
				'upgrade_template'   => 'kayan_platform()->pseo->blueprint->upgrade_template()',
				'replace_block'      => 'kayan_platform()->pseo->blueprint->replace_block()',
				'regenerate_block'   => 'kayan_platform()->pseo->generator->regenerate_block()',
				'entity_get'         => 'kayan_entity()->get( $type, $ref )',
				'tags_resolve'       => 'kayan_tags()->resolve( $template, $context )',
				'materialize'        => 'kayan_platform()->pseo->generator->materialize( $preview, $args )',
				'bulk_generate'      => 'kayan_platform()->pseo->bulk_generate( $rule_id, $options )',
				'regenerate_bulk'    => 'kayan_platform()->pseo->regenerate_bulk( $post_ids, $options )',
				'process_queue'      => 'kayan_platform()->pseo->scheduler->process_now()',
			),
		);
	}

	/**
	 * @return string
	 */
	private function country_regex_alt() {
		$codes   = array();
		$default = $this->countries->get_default();
		foreach ( array_keys( $this->countries->all() ) as $code ) {
			$code = $this->countries->normalize( $code );
			if ( '' === $code || $code === $default ) {
				continue;
			}
			$codes[] = preg_quote( $code, '#' );
		}
		return implode( '|', $codes );
	}
}
