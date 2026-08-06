<?php
/**
 * PSEO AI Provider Interface — future AI content / regeneration.
 *
 * Phase 2.5 registers the contract + null provider. No external calls.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface Kayan_PSEO_AI_Provider_Interface {

	/**
	 * @return string Provider id.
	 */
	public function id();

	/**
	 * @return bool
	 */
	public function is_available();

	/**
	 * Generate blueprint slot content from entity context.
	 *
	 * @param array<string,mixed> $context Context.
	 * @return array{ok:bool,blueprint?:array,errors?:string[]}
	 */
	public function generate_blueprint( array $context );

	/**
	 * Regenerate selected blueprint slots without changing URL identity.
	 *
	 * @param int                 $post_id Post ID.
	 * @param array<string,mixed> $context Context.
	 * @return array{ok:bool,blueprint?:array,errors?:string[]}
	 */
	public function regenerate( $post_id, array $context );
}

class Kayan_PSEO_AI_Null_Provider implements Kayan_PSEO_AI_Provider_Interface {

	/**
	 * @return string
	 */
	public function id() {
		return 'null';
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		return false;
	}

	/**
	 * @param array $context Context.
	 * @return array
	 */
	public function generate_blueprint( array $context ) {
		unset( $context );
		return array(
			'ok'     => false,
			'errors' => array( 'ai_not_configured' ),
		);
	}

	/**
	 * @param int   $post_id Post ID.
	 * @param array $context Context.
	 * @return array
	 */
	public function regenerate( $post_id, array $context ) {
		unset( $post_id, $context );
		return array(
			'ok'     => false,
			'errors' => array( 'ai_not_configured' ),
		);
	}
}

class Kayan_PSEO_AI {

	/** @var array<string,Kayan_PSEO_AI_Provider_Interface> */
	private $providers = array();

	/** @var string */
	private $default_provider = 'null';

	/**
	 * @return void
	 */
	public function register() {
		$this->register_provider( new Kayan_PSEO_AI_Null_Provider() );

		/**
		 * Register AI providers: $ai->register_provider( $instance ).
		 *
		 * @param Kayan_PSEO_AI $ai AI manager.
		 */
		do_action( 'kayan_pseo_register_ai_providers', $this );
	}

	/**
	 * @param Kayan_PSEO_AI_Provider_Interface $provider Provider.
	 * @return void
	 */
	public function register_provider( Kayan_PSEO_AI_Provider_Interface $provider ) {
		$this->providers[ $provider->id() ] = $provider;
	}

	/**
	 * @param string|null $id Provider id.
	 * @return Kayan_PSEO_AI_Provider_Interface
	 */
	public function get_provider( $id = null ) {
		$id = $id ? sanitize_key( $id ) : $this->default_provider;
		if ( isset( $this->providers[ $id ] ) ) {
			return $this->providers[ $id ];
		}
		return $this->providers['null'];
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function list_providers() {
		$out = array();
		foreach ( $this->providers as $id => $provider ) {
			$out[ $id ] = array(
				'id'        => $id,
				'available' => $provider->is_available(),
			);
		}
		return $out;
	}
}
