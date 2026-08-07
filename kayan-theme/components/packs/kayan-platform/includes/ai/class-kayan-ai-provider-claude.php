<?php
/**
 * Anthropic Claude provider (Messages API).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_AI_Provider_Claude extends Kayan_AI_Provider_Base {

	/**
	 * @return string
	 */
	public function id() {
		return 'claude';
	}

	/**
	 * @return string
	 */
	public function label() {
		return 'Anthropic Claude';
	}

	/**
	 * @return string
	 */
	protected function default_model() {
		return 'claude-3-5-sonnet-latest';
	}

	/**
	 * @param array $request Request.
	 * @return array
	 */
	public function complete( array $request ) {
		if ( ! $this->is_available() ) {
			return array( 'ok' => false, 'errors' => array( 'ai_not_configured' ) );
		}

		$body = array(
			'model'      => $this->model,
			'max_tokens' => isset( $request['max_tokens'] ) ? absint( $request['max_tokens'] ) : 800,
			'messages'   => array(
				array( 'role' => 'user', 'content' => (string) ( $request['prompt'] ?? '' ) ),
			),
		);
		if ( ! empty( $request['system'] ) ) {
			$body['system'] = (string) $request['system'];
		}

		$result = $this->post_json(
			'https://api.anthropic.com/v1/messages',
			array(
				'x-api-key'         => $this->api_key,
				'anthropic-version' => '2023-06-01',
			),
			$body
		);

		if ( empty( $result['ok'] ) ) {
			return $result;
		}

		$text = $result['data']['content'][0]['text'] ?? '';
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
		return $this->complete( array( 'prompt' => $this->translation_prompt( $text, $from, $to ) ) );
	}
}
