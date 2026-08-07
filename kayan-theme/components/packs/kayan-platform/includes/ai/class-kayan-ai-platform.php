<?php
/**
 * KAYAN AI Platform — the ONLY place that knows about concrete AI vendors.
 *
 * Application code (PSEO block regeneration, translation) calls
 * kayan_ai()->complete()/translate() and never touches a provider class.
 * Swapping/adding providers never changes calling code — interchangeable
 * by design (Kayan_AI_Provider_Interface).
 *
 * API keys/model live in the existing Settings Engine (module scope
 * `ai_{provider_id}`) — no new options table.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_AI_Platform {

	/** @var array<string,Kayan_AI_Provider_Interface> */
	private $providers = array();

	/** @var Kayan_Logger|null */
	private $logger;

	public function __construct( ?Kayan_Logger $logger = null ) {
		$this->logger = $logger;
	}

	/**
	 * @return void
	 */
	public function register() {
		$dir = __DIR__ . '/';
		$builtin = array(
			'openai'  => 'Kayan_AI_Provider_OpenAI',
			'claude'  => 'Kayan_AI_Provider_Claude',
			'gemini'  => 'Kayan_AI_Provider_Gemini',
			'mistral' => 'Kayan_AI_Provider_Mistral',
		);
		foreach ( $builtin as $id => $class ) {
			if ( class_exists( $class ) ) {
				$this->register_provider( new $class() );
			}
		}
		$this->register_provider( new Kayan_AI_Provider_Null() );

		/**
		 * Register additional/future providers: $ai->register_provider( $instance ).
		 *
		 * @param Kayan_AI_Platform $ai Platform.
		 */
		do_action( 'kayan_ai_register_providers', $this );

		/**
		 * @param Kayan_AI_Platform $ai Platform.
		 */
		do_action( 'kayan_ai_platform_registered', $this );
	}

	/**
	 * @param Kayan_AI_Provider_Interface $provider Provider.
	 * @return void
	 */
	public function register_provider( Kayan_AI_Provider_Interface $provider ) {
		$this->providers[ $provider->id() ] = $provider;
	}

	/**
	 * @return array<string,Kayan_AI_Provider_Interface>
	 */
	public function providers() {
		return $this->providers;
	}

	/**
	 * @param string|null $id Provider id.
	 * @return Kayan_AI_Provider_Interface
	 */
	public function get_provider( $id = null ) {
		$id = $id ? sanitize_key( $id ) : $this->default_provider_id();
		if ( isset( $this->providers[ $id ] ) ) {
			return $this->providers[ $id ];
		}
		return $this->providers['null'];
	}

	/**
	 * Configured default provider id (falls back to the first available one).
	 *
	 * @return string
	 */
	public function default_provider_id() {
		$configured = function_exists( 'kayan_settings' ) ? (string) kayan_settings()->get_global( 'ai_default_provider', '' ) : '';
		if ( $configured && isset( $this->providers[ $configured ] ) && $this->providers[ $configured ]->is_available() ) {
			return $configured;
		}
		foreach ( $this->providers as $id => $provider ) {
			if ( 'null' !== $id && $provider->is_available() ) {
				return $id;
			}
		}
		return 'null';
	}

	/**
	 * @return bool
	 */
	public function is_any_available() {
		foreach ( $this->providers as $id => $provider ) {
			if ( 'null' !== $id && $provider->is_available() ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Generic text completion via the configured (or specified) provider.
	 *
	 * @param array<string,mixed> $request prompt, system, max_tokens, temperature, provider.
	 * @return array{ok:bool,text?:string,errors?:string[],provider?:string}
	 */
	public function complete( array $request ) {
		$provider = $this->get_provider( $request['provider'] ?? null );
		$started  = microtime( true );
		$result   = $provider->complete( $request );
		$result['provider'] = $provider->id();

		if ( $this->logger ) {
			$this->logger->log(
				'ai',
				'ai.complete',
				array( 'provider' => $provider->id(), 'ok' => ! empty( $result['ok'] ), 'duration_ms' => round( ( microtime( true ) - $started ) * 1000, 2 ) ),
				empty( $result['ok'] ) ? Kayan_Logger::LEVEL_WARNING : Kayan_Logger::LEVEL_INFO
			);
		}

		return $result;
	}

	/**
	 * Translate text via the configured (or specified) provider.
	 *
	 * @param string $text     Text.
	 * @param string $from     From lang.
	 * @param string $to       To lang.
	 * @param array  $args     provider override + context.
	 * @return array{ok:bool,text?:string,errors?:string[],provider?:string}
	 */
	public function translate( $text, $from, $to, array $args = array() ) {
		$provider = $this->get_provider( $args['provider'] ?? null );
		$result   = $provider->translate( $text, sanitize_key( $from ), sanitize_key( $to ), $args );
		$result['provider'] = $provider->id();

		if ( $this->logger ) {
			$this->logger->log(
				'ai',
				'ai.translate',
				array( 'provider' => $provider->id(), 'from' => $from, 'to' => $to, 'ok' => ! empty( $result['ok'] ) ),
				empty( $result['ok'] ) ? Kayan_Logger::LEVEL_WARNING : Kayan_Logger::LEVEL_INFO
			);
		}

		return $result;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function status() {
		$out = array();
		foreach ( $this->providers as $id => $provider ) {
			$out[ $id ] = array(
				'id'           => $id,
				'label'        => $provider->label(),
				'available'    => $provider->is_available(),
				'capabilities' => $provider->capabilities(),
			);
		}
		return $out;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'providers'        => array_keys( $this->providers ),
			'default_provider' => $this->default_provider_id(),
			'any_available'    => $this->is_any_available(),
			'apis'             => array(
				'complete'         => 'kayan_ai()->complete( array( "prompt" => $prompt ) )',
				'translate'        => 'kayan_ai()->translate( $text, $from, $to )',
				'register_provider'=> 'kayan_ai()->register_provider( $provider )',
			),
		);
	}
}
