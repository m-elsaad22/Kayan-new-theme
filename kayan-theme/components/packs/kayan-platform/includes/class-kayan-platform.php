<?php
/**
 * Kayan_Platform — service container / facade (Phases 1–3.0).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Platform {

	/** @var self|null */
	private static $instance = null;

	/** @var Kayan_Country_Engine */
	public $countries;

	/** @var Kayan_Language_Engine */
	public $languages;

	/** @var Kayan_Context */
	public $context;

	/**
	 * Country settings repository (BC). Prefer settings_engine for new code.
	 *
	 * @var Kayan_Country_Settings
	 */
	public $settings;

	/** @var Kayan_Settings_Engine */
	public $settings_engine;

	/** @var Kayan_Content_Locale */
	public $content;

	/** @var Kayan_URL */
	public $urls;

	/** @var Kayan_SEO_Bridge */
	public $seo;

	/** @var Kayan_Programmatic_SEO */
	public $programmatic;

	/** @var Kayan_Entity_Engine */
	public $entity;

	/** @var Kayan_Dynamic_Data_Tags */
	public $tags;

	/** @var Kayan_Cache_Engine */
	public $cache;

	/** @var Kayan_Logger */
	public $logger;

	/** @var Kayan_Query_Engine */
	public $query;

	/** @var Kayan_Docs_Generator */
	public $docs;

	/** @var Kayan_Admin_Platform */
	public $admin;

	/** @var Kayan_PSEO_Engine */
	public $pseo;

	/** @var Kayan_Country_Router */
	public $router;

	/** @var Kayan_Content_Resolver */
	public $resolver;

	/** @var bool */
	private $booted = false;

	/**
	 * @return self
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->countries    = new Kayan_Country_Engine();
		$this->languages    = new Kayan_Language_Engine();
		$this->context      = new Kayan_Context( $this->countries, $this->languages );
		$this->settings     = new Kayan_Country_Settings( $this->countries );
		$this->content      = new Kayan_Content_Locale();
		$this->urls         = new Kayan_URL( $this->countries, $this->languages );

		$this->cache           = new Kayan_Cache_Engine();
		$this->logger          = new Kayan_Logger();
		$this->settings_engine = new Kayan_Settings_Engine( $this->countries, $this->languages, $this->settings );
		$this->settings_engine->set_cache( $this->cache );
		$this->query = new Kayan_Query_Engine( $this->cache, $this->logger, $this->countries );

		$this->programmatic = new Kayan_Programmatic_SEO();
		$this->entity       = new Kayan_Entity_Engine(
			$this->programmatic,
			$this->countries,
			$this->settings,
			$this->content
		);
		$this->tags         = new Kayan_Dynamic_Data_Tags(
			$this->entity,
			$this->settings,
			$this->countries
		);
		$this->pseo         = new Kayan_PSEO_Engine( $this->programmatic, $this->content, $this->countries );
		$this->router       = new Kayan_Country_Router(
			$this->countries,
			$this->languages,
			$this->urls,
			$this->programmatic
		);
		$this->resolver     = new Kayan_Content_Resolver( $this->context, $this->content, $this->router );
		$this->seo          = new Kayan_SEO_Bridge(
			$this->context,
			$this->settings,
			$this->urls,
			$this->countries,
			$this->languages
		);
		$this->admin        = new Kayan_Admin_Platform( $this->logger );
		$this->docs         = new Kayan_Docs_Generator();
	}

	/**
	 * @return void
	 */
	public function boot() {
		if ( $this->booted ) {
			return;
		}
		$this->booted = true;

		$this->content->register();
		$this->programmatic->register();
		$this->entity->register();
		$this->tags->register();

		$this->cache->register();
		$this->settings_engine->register();
		$this->logger->register();
		$this->query->register();

		$this->pseo->register();
		$this->router->register();
		$this->resolver->register();
		$this->seo->set_routing_services( $this->router, $this->content );
		$this->seo->register();

		$this->admin->register();

		$this->logger->info(
			'general',
			'platform.booted',
			array(
				'version' => defined( 'KAYAN_PLATFORM_VERSION' ) ? KAYAN_PLATFORM_VERSION : '',
			)
		);

		/**
		 * @param Kayan_Platform $platform Platform instance.
		 */
		do_action( 'kayan_platform_booted', $this );
	}
}
