<?php
/**
 * Null AI provider — always available as the safe fallback (matches the
 * Phase 2.5 contract: no API key configured ⇒ graceful, explicit failure).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_AI_Provider_Null implements Kayan_AI_Provider_Interface {

	/**
	 * @return string
	 */
	public function id() {
		return 'null';
	}

	/**
	 * @return string
	 */
	public function label() {
		return __( 'None configured', 'kayan' );
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		return false;
	}

	/**
	 * @return string[]
	 */
	public function capabilities() {
		return array();
	}

	/**
	 * @param array $request Request.
	 * @return array
	 */
	public function complete( array $request ) {
		unset( $request );
		return array( 'ok' => false, 'errors' => array( 'ai_not_configured' ) );
	}

	/**
	 * @param string $text    Text.
	 * @param string $from    From.
	 * @param string $to      To.
	 * @param array  $context Context.
	 * @return array
	 */
	public function translate( $text, $from, $to, array $context = array() ) {
		unset( $text, $from, $to, $context );
		return array( 'ok' => false, 'errors' => array( 'ai_not_configured' ) );
	}
}
