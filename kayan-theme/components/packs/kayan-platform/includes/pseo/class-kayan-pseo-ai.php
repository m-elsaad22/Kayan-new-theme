<?php
/**
 * PSEO AI Provider Interface — blueprint/block-shaped AI content contract.
 *
 * Phase 5: the default provider is a bridge to the central Kayan_AI_Platform
 * (Kayan_PSEO_AI_Bridge_Provider) — real OpenAI/Claude/Gemini/Mistral calls
 * happen there, never here. This registry still supports fully custom
 * PSEO-shaped providers via `kayan_pseo_register_ai_providers` if ever needed.
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

	/**
	 * Regenerate a single block without modifying other blocks.
	 *
	 * @param int                 $post_id  Post ID.
	 * @param string              $block_id Block id.
	 * @param array<string,mixed> $context  Context (tokens, prompt override, …).
	 * @return array{ok:bool,data?:array,blueprint?:array,errors?:string[]}
	 */
	public function regenerate_block( $post_id, $block_id, array $context );
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

	/**
	 * @param int    $post_id  Post ID.
	 * @param string $block_id Block.
	 * @param array  $context  Context.
	 * @return array
	 */
	public function regenerate_block( $post_id, $block_id, array $context ) {
		unset( $post_id, $block_id, $context );
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
	private $default_provider = 'bridge';

	/** @var Kayan_PSEO_Blocks|null */
	private $blocks;

	/** @var Kayan_PSEO_Blueprint|null */
	private $blueprint;

	/**
	 * @param Kayan_PSEO_Blocks|null    $blocks    Blocks engine.
	 * @param Kayan_PSEO_Blueprint|null $blueprint Blueprint engine.
	 */
	public function __construct( ?Kayan_PSEO_Blocks $blocks = null, ?Kayan_PSEO_Blueprint $blueprint = null ) {
		$this->blocks    = $blocks;
		$this->blueprint = $blueprint;
	}

	/**
	 * @return void
	 */
	public function register() {
		$this->register_provider( new Kayan_PSEO_AI_Null_Provider() );
		if ( $this->blocks ) {
			$this->register_provider( new Kayan_PSEO_AI_Bridge_Provider( $this->blocks ) );
		}

		/**
		 * Register additional PSEO-shaped AI providers: $ai->register_provider( $instance ).
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
	 * Resolve prompt for a block (no API call).
	 *
	 * @param string               $block_id Block id.
	 * @param array<string,string> $tokens   Tokens.
	 * @param string|null          $override Override prompt.
	 * @return string
	 */
	public function resolve_block_prompt( $block_id, array $tokens = array(), $override = null ) {
		if ( ! $this->blocks ) {
			return is_string( $override ) ? $override : '';
		}
		return $this->blocks->resolve_prompt( $block_id, $tokens, $override );
	}

	/**
	 * Per-block regeneration entry point (provider stub in this phase).
	 *
	 * @param int                 $post_id  Post ID.
	 * @param string              $block_id Block id.
	 * @param array<string,mixed> $args     Args.
	 * @return array{ok:bool,errors?:string[],blueprint?:array,prompt?:string}
	 */
	public function regenerate_block( $post_id, $block_id, array $args = array() ) {
		$block_id = sanitize_key( $block_id );
		$tokens   = isset( $args['tokens'] ) && is_array( $args['tokens'] ) ? $args['tokens'] : array();
		$override = isset( $args['prompt'] ) ? (string) $args['prompt'] : null;
		$prompt   = $this->resolve_block_prompt( $block_id, $tokens, $override );

		if ( $this->blueprint && $post_id > 0 ) {
			$blueprint = $this->blueprint->get_for_post( $post_id );
			if ( ! empty( $blueprint['blocks'][ $block_id ]['locked'] ) && empty( $args['force'] ) ) {
				return array(
					'ok'     => false,
					'errors' => array( 'block_locked' ),
					'prompt' => $prompt,
				);
			}
		}

		$provider = $this->get_provider( isset( $args['provider'] ) ? $args['provider'] : null );
		$result   = $provider->regenerate_block( $post_id, $block_id, array_merge( $args, array( 'prompt' => $prompt ) ) );

		if ( ! isset( $result['prompt'] ) ) {
			$result['prompt'] = $prompt;
		}

		return $result;
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function list_providers() {
		$out = array();
		foreach ( $this->providers as $id => $provider ) {
			$out[ $id ] = array(
				'id'            => $id,
				'available'     => $provider->is_available(),
				'block_capable' => true,
			);
		}
		return $out;
	}
}
