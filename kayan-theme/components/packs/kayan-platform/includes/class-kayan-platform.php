<?php
/**
 * Kayan_Platform — service container / facade (Phases 1–2.5).
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

	/** @var Kayan_Country_Settings */
	public $settings;

	/** @var Kayan_Content_Locale */
	public $content;

	/** @var Kayan_URL */
	public $urls;

	/** @var Kayan_SEO_Bridge */
	public $seo;

	/** @var Kayan_Programmatic_SEO */
	public $programmatic;

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
		$this->programmatic = new Kayan_Programmatic_SEO();
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
		$this->pseo->register();
		$this->router->register();
		$this->resolver->register();
		$this->seo->set_routing_services( $this->router, $this->content );
		$this->seo->register();

		/**
		 * @param Kayan_Platform $platform Platform instance.
		 */
		do_action( 'kayan_platform_booted', $this );
	}
}
