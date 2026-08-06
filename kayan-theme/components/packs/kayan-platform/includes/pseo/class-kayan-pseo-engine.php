<?php
/**
 * Kayan_PSEO_Engine — Phase 2.5 Programmatic SEO Engine facade.
 *
 * Orchestrates entities, patterns, rules, identity, storage, jobs, AI, generator.
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

	/** @var Kayan_Country_Engine */
	private $countries;

	public function __construct(
		Kayan_Programmatic_SEO $entities,
		Kayan_Content_Locale $locale,
		Kayan_Country_Engine $countries
	) {
		$this->entities  = $entities;
		$this->countries = $countries;
		$this->patterns  = new Kayan_PSEO_Patterns();
		$this->rules     = new Kayan_PSEO_Rules( $this->patterns );
		$this->identity  = new Kayan_PSEO_Identity( $this->patterns );
		$this->blueprint = new Kayan_PSEO_Blueprint();
		$this->storage   = new Kayan_PSEO_Storage( $this->blueprint, $locale );
		$this->jobs      = new Kayan_PSEO_Jobs();
		$this->ai        = new Kayan_PSEO_AI();
		$this->generator = new Kayan_PSEO_Generator(
			$this->entities,
			$this->patterns,
			$this->rules,
			$this->identity,
			$this->blueprint,
			$this->storage,
			$this->ai
		);
	}

	/**
	 * @return void
	 */
	public function register() {
		$this->patterns->register();
		$this->rules->register();
		$this->storage->register();
		$this->jobs->register();
		$this->ai->register();

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
			'version'   => '2.5.0',
			'status'    => $this->generator->status(),
			'entities'  => array_keys( $this->entities->get_entity_types() ),
			'patterns'  => array_keys( $this->patterns->all() ),
			'storage'   => $this->storage->capabilities(),
			'apis'      => array(
				'preview'            => 'kayan_platform()->pseo->generator->preview()',
				'preview_rule'       => 'kayan_platform()->pseo->generator->preview_rule()',
				'save_rule'          => 'kayan_platform()->pseo->rules->save()',
				'enqueue_job'        => 'kayan_platform()->pseo->jobs->enqueue()',
				'fingerprint'        => 'kayan_platform()->pseo->identity->fingerprint()',
				'materialize'        => 'disabled until generation phase',
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
