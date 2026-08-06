<?php
/**
 * KAYAN Theme Integration — Phase 3.1 facade.
 *
 * Connects existing theme packs to the platform via adapters.
 * Zero breaking changes. No new admin UI / features.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Theme_Integration {

	/** @var array<string,object> */
	private $adapters = array();

	/** @var Kayan_Logger|null */
	private $logger;

	/** @var Kayan_Compatibility_Report|null */
	public $report;

	public function __construct( ?Kayan_Logger $logger = null ) {
		$this->logger = $logger;
	}

	/**
	 * @return void
	 */
	public function register() {
		$this->load_adapters();
		$this->report = new Kayan_Compatibility_Report( $this );

		foreach ( $this->adapters as $adapter ) {
			if ( method_exists( $adapter, 'register' ) ) {
				$adapter->register();
			}
		}

		if ( $this->logger ) {
			$this->logger->info(
				'general',
				'theme.integration.registered',
				array(
					'adapters' => array_keys( $this->adapters ),
				)
			);
		}

		/**
		 * @param Kayan_Theme_Integration $integration Integration.
		 */
		do_action( 'kayan_theme_integration_registered', $this );
	}

	/**
	 * @return array<string,object>
	 */
	public function adapters() {
		return $this->adapters;
	}

	/**
	 * @param string $id Adapter id.
	 * @return object|null
	 */
	public function get_adapter( $id ) {
		$id = sanitize_key( $id );
		return isset( $this->adapters[ $id ] ) ? $this->adapters[ $id ] : null;
	}

	/**
	 * Whether Rank Math is active.
	 *
	 * @return bool
	 */
	public static function rank_math_active() {
		return defined( 'RANK_MATH_VERSION' )
			|| class_exists( 'RankMath', false )
			|| function_exists( 'rank_math' );
	}

	/**
	 * Read a field from the raw country profile option (no legacy dual-read).
	 * Safe inside option_* filters — avoids recursion with Country Settings.
	 *
	 * @param string      $field   Flat key (phone, whatsapp, currency, …).
	 * @param mixed       $default Default.
	 * @param string|null $country Country code.
	 * @return mixed
	 */
	public static function profile_field( $field, $default = '', $country = null ) {
		$field = (string) $field;
		if ( '' === $field ) {
			return $default;
		}
		if ( null === $country ) {
			$country = function_exists( 'kayan_platform_country' ) ? kayan_platform_country() : '';
		}
		$country = sanitize_key( (string) $country );
		if ( ! $country ) {
			return $default;
		}

		$key = 'kayan_country_profile_' . $country;
		$raw = get_option( $key, array() );
		if ( ! is_array( $raw ) ) {
			return $default;
		}

		if ( false !== strpos( $field, '.' ) ) {
			$parts = explode( '.', $field );
			$cur   = $raw;
			foreach ( $parts as $part ) {
				if ( ! is_array( $cur ) || ! array_key_exists( $part, $cur ) ) {
					return $default;
				}
				$cur = $cur[ $part ];
			}
			return ( null === $cur || '' === $cur ) ? $default : $cur;
		}

		if ( ! array_key_exists( $field, $raw ) ) {
			return $default;
		}
		$val = $raw[ $field ];
		return ( null === $val || '' === $val ) ? $default : $val;
	}

	/**
	 * Whether option filters should stay out of the way (admin Theme Options screens).
	 *
	 * @return bool
	 */
	public static function skip_frontend_option_bridge() {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return true;
		}
		/**
		 * @param bool $skip Skip bridges.
		 */
		return (bool) apply_filters( 'kayan_theme_integration_skip_option_bridge', false );
	}

	/**
	 * Safe theme option reader — prefers country profile for mapped contact keys.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	public static function theme_option( $key, $default = '' ) {
		$map = array(
			'phonenumber'       => 'phone',
			'whatsapp_number'   => 'whatsapp',
			'company__mail'     => 'email',
			'sitename'          => 'business_name',
			'logo__data'        => 'logo',
			'home__title'       => 'seo.title',
			'home__description' => 'seo.description',
			'contact_number'    => 'phone',
			'kayan_booking_currency' => 'currency',
			'kayan_invoice_company_name' => 'business_name',
		);

		if ( isset( $map[ $key ] ) ) {
			$val = self::profile_field( $map[ $key ], null );
			if ( null !== $val && false !== $val && '' !== $val ) {
				return $val;
			}
			// Fall through to platform setting (includes legacy dual-read) when not inside option filter.
			if ( ! self::skip_frontend_option_bridge() && function_exists( 'kayan_platform_setting' ) ) {
				$val = kayan_platform_setting( $map[ $key ], null, null );
				if ( null !== $val && false !== $val && '' !== $val ) {
					return $val;
				}
			}
		}

		if ( function_exists( 'yc_get_option' ) ) {
			return yc_get_option( $key, $default );
		}
		$val = get_option( $key, $default );
		return ( false === $val ) ? $default : $val;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		$statuses = array();
		foreach ( $this->adapters as $id => $adapter ) {
			$statuses[ $id ] = method_exists( $adapter, 'status' ) ? $adapter->status() : array( 'id' => $id );
		}
		return array(
			'phase'    => '3.1',
			'adapters' => $statuses,
			'apis'     => array(
				'theme_option'  => 'Kayan_Theme_Integration::theme_option( $key )',
				'profile_field' => 'Kayan_Theme_Integration::profile_field( $field )',
				'rank_math'     => 'Kayan_Theme_Integration::rank_math_active()',
				'report'        => 'kayan_platform()->integration->report->generate()',
			),
		);
	}

	/**
	 * @return void
	 */
	private function load_adapters() {
		$dir = __DIR__ . '/adapters/';
		$map = array(
			'schema'        => 'Kayan_Adapter_Schema',
			'rukn_contact'  => 'Kayan_Adapter_Rukn_Contact',
			'booking'       => 'Kayan_Adapter_Booking',
			'payment'       => 'Kayan_Adapter_Payment',
			'track'         => 'Kayan_Adapter_Track',
			'i18n_switcher' => 'Kayan_Adapter_I18n_Switcher',
			'legacy_city'   => 'Kayan_Adapter_Legacy_City',
			'theme_options' => 'Kayan_Adapter_Theme_Options',
			'admin_bridges' => 'Kayan_Adapter_Admin_Bridges',
			'cpt'           => 'Kayan_Adapter_CPT',
			'query'         => 'Kayan_Adapter_Query',
		);

		foreach ( $map as $id => $class ) {
			$file = $dir . 'class-kayan-adapter-' . str_replace( '_', '-', $id ) . '.php';
			if ( file_exists( $file ) ) {
				require_once $file;
			}
			if ( class_exists( $class ) ) {
				$this->adapters[ $id ] = new $class();
			}
		}
	}
}
