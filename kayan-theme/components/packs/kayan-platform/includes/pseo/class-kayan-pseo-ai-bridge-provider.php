<?php
/**
 * Bridges the existing PSEO AI contract (Kayan_PSEO_AI_Provider_Interface)
 * to the central Kayan_AI_Platform registry (Phase 5).
 *
 * This is the ONLY place PSEO block regeneration knows about "AI" at all —
 * it never touches OpenAI/Claude/etc. directly, and swapping the active
 * vendor (Settings → AI) never requires a code change here.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_AI_Bridge_Provider implements Kayan_PSEO_AI_Provider_Interface {

	/** @var Kayan_PSEO_Blocks */
	private $blocks;

	/** @var string[] Block ids whose primary content is plain/simple text an LLM can safely fill. */
	private $text_blocks = array( 'hero', 'cta' );

	/** @var string[] Block ids whose content is a JSON list an LLM can populate. */
	private $list_blocks = array( 'faq' );

	public function __construct( Kayan_PSEO_Blocks $blocks ) {
		$this->blocks = $blocks;
	}

	/**
	 * @return string
	 */
	public function id() {
		return 'bridge';
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		return function_exists( 'kayan_ai' ) && kayan_ai()->is_any_available();
	}

	/**
	 * @param array $context Context: entities, country, language, tokens, pattern.
	 * @return array{ok:bool,blueprint?:array,errors?:string[]}
	 */
	public function generate_blueprint( array $context ) {
		if ( ! $this->is_available() ) {
			return array( 'ok' => false, 'errors' => array( 'ai_not_configured' ) );
		}

		$tokens = isset( $context['tokens'] ) ? (array) $context['tokens'] : array();
		$blocks = array();
		$errors = array();

		foreach ( array_merge( $this->text_blocks, $this->list_blocks ) as $block_id ) {
			$result = $this->regenerate_block( 0, $block_id, array( 'tokens' => $tokens, 'context' => $context ) );
			if ( ! empty( $result['ok'] ) ) {
				$blocks[ $block_id ] = array( 'data' => $result['data'], 'source' => 'ai' );
			} else {
				$errors = array_merge( $errors, (array) ( $result['errors'] ?? array() ) );
			}
		}

		return array( 'ok' => ! empty( $blocks ), 'blueprint' => array( 'blocks' => $blocks ), 'errors' => $errors );
	}

	/**
	 * @param int   $post_id Post ID.
	 * @param array $context Context.
	 * @return array{ok:bool,blueprint?:array,errors?:string[]}
	 */
	public function regenerate( $post_id, array $context ) {
		$post_id = (int) $post_id;
		if ( ! $post_id || ! function_exists( 'kayan_pseo' ) ) {
			return array( 'ok' => false, 'errors' => array( 'invalid_post' ) );
		}

		$blueprint = kayan_pseo()->blueprint->get_for_post( $post_id );
		$tokens    = isset( $context['tokens'] ) ? (array) $context['tokens'] : array();
		$changed   = array();

		foreach ( array_merge( $this->text_blocks, $this->list_blocks ) as $block_id ) {
			if ( empty( $blueprint['blocks'][ $block_id ] ) || ! empty( $blueprint['blocks'][ $block_id ]['locked'] ) ) {
				continue; // respect locked blocks — safety.
			}
			$result = $this->regenerate_block( $post_id, $block_id, array( 'tokens' => $tokens, 'context' => $context ) );
			if ( ! empty( $result['ok'] ) ) {
				$blueprint['blocks'][ $block_id ]['data']   = $result['data'];
				$blueprint['blocks'][ $block_id ]['source'] = 'ai';
				$changed[] = $block_id;
			}
		}

		return array( 'ok' => ! empty( $changed ), 'blueprint' => $blueprint, 'changed' => $changed );
	}

	/**
	 * @param int    $post_id  Post ID (0 when generating a fresh blueprint, not yet saved).
	 * @param string $block_id Block id.
	 * @param array  $context  tokens, prompt override, provider.
	 * @return array{ok:bool,data?:array,errors?:string[]}
	 */
	public function regenerate_block( $post_id, $block_id, array $context ) {
		unset( $post_id );
		$block_id = sanitize_key( $block_id );

		if ( ! in_array( $block_id, array_merge( $this->text_blocks, $this->list_blocks ), true ) ) {
			return array( 'ok' => false, 'errors' => array( 'block_not_ai_editable' ) );
		}
		if ( ! $this->is_available() ) {
			return array( 'ok' => false, 'errors' => array( 'ai_not_configured' ) );
		}

		$tokens   = isset( $context['tokens'] ) ? (array) $context['tokens'] : array();
		$override = isset( $context['prompt'] ) ? (string) $context['prompt'] : null;
		$prompt   = $this->blocks->resolve_prompt( $block_id, $tokens, $override );
		$is_list  = in_array( $block_id, $this->list_blocks, true );

		if ( $is_list ) {
			$prompt .= "\n\nRespond ONLY with valid JSON in this exact shape: {\"items\":[{\"question\":\"…\",\"answer\":\"…\"}]} with 4-6 items. No markdown, no explanation.";
		}

		$response = kayan_ai()->complete(
			array(
				'prompt'      => $prompt,
				'provider'    => isset( $context['provider'] ) ? $context['provider'] : null,
				'format'      => $is_list ? 'json' : 'text',
				'temperature' => 0.7,
			)
		);

		if ( empty( $response['ok'] ) ) {
			return array( 'ok' => false, 'errors' => $response['errors'] ?? array( 'ai_request_failed' ) );
		}

		$data = $this->map_text_to_block_data( $block_id, (string) $response['text'] );
		if ( null === $data ) {
			return array( 'ok' => false, 'errors' => array( 'unparseable_ai_response' ) );
		}

		return array( 'ok' => true, 'data' => $data, 'prompt' => $prompt, 'provider' => $response['provider'] ?? '' );
	}

	/**
	 * @param string $block_id Block id.
	 * @param string $text     Raw AI text.
	 * @return array<string,mixed>|null
	 */
	private function map_text_to_block_data( $block_id, $text ) {
		$text = trim( $text );
		if ( '' === $text ) {
			return null;
		}

		if ( 'hero' === $block_id ) {
			$lines = preg_split( '/\r?\n/', $text, 2 );
			return array(
				'headline'    => trim( $lines[0] ?? '' ),
				'subheadline' => trim( $lines[1] ?? '' ),
			);
		}

		if ( 'cta' === $block_id ) {
			$lines = preg_split( '/\r?\n/', $text );
			return array( 'primary_label' => trim( $lines[0] ?? $text ) );
		}

		if ( 'faq' === $block_id ) {
			// Strip accidental markdown code fences before decoding.
			$clean   = preg_replace( '/^```(json)?|```$/m', '', $text );
			$decoded = json_decode( trim( (string) $clean ), true );
			if ( is_array( $decoded ) && ! empty( $decoded['items'] ) && is_array( $decoded['items'] ) ) {
				$items = array();
				foreach ( $decoded['items'] as $item ) {
					if ( is_array( $item ) && ! empty( $item['question'] ) ) {
						$items[] = array(
							'question' => sanitize_text_field( (string) $item['question'] ),
							'answer'   => sanitize_textarea_field( (string) ( $item['answer'] ?? '' ) ),
						);
					}
				}
				return $items ? array( 'items' => $items ) : null;
			}
			return null;
		}

		return null;
	}
}
