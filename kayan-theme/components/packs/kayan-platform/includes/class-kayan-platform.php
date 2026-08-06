<?php
/**
 * Kayan_Platform — service container / facade for Phase 1 core.
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
		$this->countries = new Kayan_Country_Engine();
		$this->languages = new Kayan_Language_Engine();
		$this->context   = new Kayan_Context( $this->countries, $this->languages );
		$this->settings  = new Kayan_Country_Settings( $this->countries );
		$this->content   = new Kayan_Content_Locale();
		$this->urls      = new Kayan_URL( $this->countries, $this->languages );
		$this->seo       = new Kayan_SEO_Bridge(
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
		$this->seo->register();

		/**
		 * Fires when the platform core is ready.
		 *
		 * @param Kayan_Platform $platform Platform instance.
		 */
		do_action( 'kayan_platform_booted', $this );
	}
}
