<?php
/**
 * Google Gemini provider (generateContent API).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_AI_Provider_Gemini extends Kayan_AI_Provider_Base {

	/**
	 * @return string
	 */
	public function id() {
		return 'gemini';
	}

	/**
	 * @return string
	 */
	public function label() {
		return 'Google Gemini';
	}

	/**
	 * @return string
	 */
	protected function default_model() {
		return 'gemini-1.5-flash';
	}

	/**
	 * @param array $request Request.
	 * @return array
	 */
	public function complete( array $request ) {
		if ( ! $this->is_available() ) {
			return array( 'ok' => false, 'errors' => array( 'ai_not_configured' ) );
		}

		$prompt = (string) ( $request['prompt'] ?? '' );
		if ( ! empty( $request['system'] ) ) {
			$prompt = $request['system'] . "\n\n" . $prompt;
		}

		$url = sprintf(
			'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
			rawurlencode( $this->model ),
			rawurlencode( $this->api_key )
		);

		$result = $this->post_json(
			$url,
			array(),
			array(
				'contents'         => array(
					array( 'parts' => array( array( 'text' => $prompt ) ) ),
				),
				'generationConfig' => array(
					'maxOutputTokens' => isset( $request['max_tokens'] ) ? absint( $request['max_tokens'] ) : 800,
					'temperature'     => isset( $request['temperature'] ) ? (float) $request['temperature'] : 0.7,
				),
			)
		);

		if ( empty( $result['ok'] ) ) {
			return $result;
		}

		$text = $result['data']['candidates'][0]['content']['parts'][0]['text'] ?? '';
		return array( 'ok' => '' !== trim( (string) $text ), 'text' => trim( (string) $text ), 'errors' => '' === trim( (string) $text ) ? array( 'empty_response' ) : array() );
	}

	/**
	 * @param string $text    Text.
	 * @param string $from    From.
	 * @param string $to      To.
	 * @param array  $context Context.
	 * @return array
	 */
	public function translate( $text, $from, $to, array $context = array() ) {
		unset( $context );
		return $this->complete( array( 'prompt' => $this->translation_prompt( $text, $from, $to ), 'temperature' => 0.2 ) );
	}
}
