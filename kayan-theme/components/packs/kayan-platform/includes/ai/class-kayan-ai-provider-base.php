<?php
/**
 * Shared HTTP plumbing for real AI providers — keeps each concrete
 * provider file tiny (just endpoint/payload/parsing), so swapping or
 * adding a vendor never touches application code.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Kayan_AI_Provider_Base implements Kayan_AI_Provider_Interface {

	/** @var string */
	protected $api_key = '';

	/** @var string */
	protected $model = '';

	public function __construct() {
		$this->api_key = (string) $this->setting( 'api_key', '' );
		$this->model   = (string) $this->setting( 'model', $this->default_model() );
	}

	/**
	 * @return string
	 */
	abstract protected function default_model();

	/**
	 * @param string $key     Key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	protected function setting( $key, $default = '' ) {
		if ( function_exists( 'kayan_settings' ) ) {
			return kayan_settings()->get_module( 'ai_' . $this->id(), $key, $default );
		}
		return $default;
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		return '' !== trim( $this->api_key );
	}

	/**
	 * @return string[]
	 */
	public function capabilities() {
		return array( 'text', 'translate' );
	}

	/**
	 * @param string               $url     URL.
	 * @param array<string,string> $headers Headers.
	 * @param array<string,mixed>  $body    Body (JSON-encoded).
	 * @return array{ok:bool,data?:array,errors?:string[]}
	 */
	protected function post_json( $url, array $headers, array $body ) {
		if ( ! function_exists( 'wp_remote_post' ) ) {
			return array( 'ok' => false, 'errors' => array( 'http_unavailable' ) );
		}

		$response = wp_remote_post(
			$url,
			array(
				'timeout' => 30,
				'headers' => array_merge( array( 'Content-Type' => 'application/json' ), $headers ),
				'body'    => wp_json_encode( $body ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return array( 'ok' => false, 'errors' => array( $response->get_error_message() ) );
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$raw  = wp_remote_retrieve_body( $response );
		$data = json_decode( (string) $raw, true );

		if ( $code < 200 || $code >= 300 ) {
			$message = is_array( $data ) ? $this->extract_error_message( $data ) : ( 'http_' . $code );
			return array( 'ok' => false, 'errors' => array( $message ) );
		}

		return array( 'ok' => true, 'data' => is_array( $data ) ? $data : array() );
	}

	/**
	 * @param array $data Decoded error payload.
	 * @return string
	 */
	protected function extract_error_message( array $data ) {
		if ( isset( $data['error']['message'] ) ) {
			return (string) $data['error']['message'];
		}
		if ( isset( $data['message'] ) ) {
			return (string) $data['message'];
		}
		return 'request_failed';
	}

	/**
	 * Build a translation prompt shared by all vendors — keeps behavior
	 * consistent regardless of which provider is active.
	 *
	 * @param string $text Text.
	 * @param string $from From.
	 * @param string $to   To.
	 * @return string
	 */
	protected function translation_prompt( $text, $from, $to ) {
		return sprintf(
			"Translate the following text from %s to %s. Preserve meaning and tone. Do NOT translate anything inside {{double curly braces}} — copy those tokens exactly as-is. Return only the translated text, no explanation.\n\nText:\n%s",
			$from,
			$to,
			$text
		);
	}
}
