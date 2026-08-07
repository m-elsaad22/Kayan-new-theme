<?php
/**
 * OpenAI provider (Chat Completions API). Thin adapter — all orchestration
 * (prompt building, provider selection, fallback) stays in Kayan_AI_Platform.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_AI_Provider_OpenAI extends Kayan_AI_Provider_Base {

	/**
	 * @return string
	 */
	public function id() {
		return 'openai';
	}

	/**
	 * @return string
	 */
	public function label() {
		return 'OpenAI';
	}

	/**
	 * @return string
	 */
	protected function default_model() {
		return 'gpt-4o-mini';
	}

	/**
	 * @param array $request Request.
	 * @return array
	 */
	public function complete( array $request ) {
		if ( ! $this->is_available() ) {
			return array( 'ok' => false, 'errors' => array( 'ai_not_configured' ) );
		}

		$messages = array();
		if ( ! empty( $request['system'] ) ) {
			$messages[] = array( 'role' => 'system', 'content' => (string) $request['system'] );
		}
		$messages[] = array( 'role' => 'user', 'content' => (string) ( $request['prompt'] ?? '' ) );

		$result = $this->post_json(
			'https://api.openai.com/v1/chat/completions',
			array( 'Authorization' => 'Bearer ' . $this->api_key ),
			array(
				'model'       => $this->model,
				'messages'    => $messages,
				'max_tokens'  => isset( $request['max_tokens'] ) ? absint( $request['max_tokens'] ) : 800,
				'temperature' => isset( $request['temperature'] ) ? (float) $request['temperature'] : 0.7,
			)
		);

		if ( empty( $result['ok'] ) ) {
			return $result;
		}

		$text = $result['data']['choices'][0]['message']['content'] ?? '';
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
