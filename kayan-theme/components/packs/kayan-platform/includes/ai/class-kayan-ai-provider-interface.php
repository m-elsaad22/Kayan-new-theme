<?php
/**
 * Generic AI Provider contract (Phase 5).
 *
 * Every provider (OpenAI, Claude, Gemini, Mistral, future) implements the
 * SAME shape. Application code (PSEO block regeneration, translation) only
 * ever talks to Kayan_AI_Platform — never to a concrete provider class —
 * so no provider-specific logic leaks outside this directory.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface Kayan_AI_Provider_Interface {

	/**
	 * @return string Stable id (openai|claude|gemini|mistral|null|…).
	 */
	public function id();

	/**
	 * @return string Human label.
	 */
	public function label();

	/**
	 * Whether this provider is configured and ready (e.g. API key present).
	 *
	 * @return bool
	 */
	public function is_available();

	/**
	 * @return string[] e.g. array( 'text', 'translate' ).
	 */
	public function capabilities();

	/**
	 * Generic text completion.
	 *
	 * @param array<string,mixed> $request {
	 *     @type string $prompt      Prompt text.
	 *     @type string $system      Optional system instruction.
	 *     @type int    $max_tokens  Optional cap.
	 *     @type float  $temperature Optional.
	 *     @type string $format      'text'|'json' — hint to the provider/model.
	 * }
	 * @return array{ok:bool,text?:string,errors?:string[]}
	 */
	public function complete( array $request );

	/**
	 * Translate text while preserving meaning/tone; must not translate
	 * `{{dynamic_tags}}` tokens or HTML tag names.
	 *
	 * @param string $text From language text.
	 * @param string $from Source language code.
	 * @param string $to   Target language code.
	 * @param array  $context Optional extra context (glossary, tone).
	 * @return array{ok:bool,text?:string,errors?:string[]}
	 */
	public function translate( $text, $from, $to, array $context = array() );
}
