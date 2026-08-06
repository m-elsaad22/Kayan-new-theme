<?php
/**
 * PSEO Generation Rules — selection filters + targets (storage API only).
 *
 * Phase 2.5 stores/validates rule definitions. No execution / page creation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Rules {

	const OPTION_KEY = 'kayan_pseo_rules';

	/** @var Kayan_PSEO_Patterns */
	private $patterns;

	public function __construct( Kayan_PSEO_Patterns $patterns ) {
		$this->patterns = $patterns;
	}

	/**
	 * @return void
	 */
	public function register() {
		// Architecture only — no admin UI hooks.
	}

	/**
	 * Rule schema defaults.
	 *
	 * @return array<string,mixed>
	 */
	public function schema_defaults() {
		return array(
			'id'            => '',
			'label'         => '',
			'pattern_id'    => '',
			'enabled'       => true,
			'filters'       => array(
				'countries'  => array(), // empty = all enabled countries
				'cities'     => array(), // term ids/slugs; empty = all
				'areas'      => array(),
				'districts'  => array(),
				'neighborhoods' => array(),
				'services'   => array(), // post ids/slugs
				'categories' => array(),
				'landmarks'  => array(),
				'brands'     => array(),
				'buildings'  => array(),
				'faqs'       => array(),
				'pricing'    => array(),
				'languages'  => array( 'ar' ),
			),
			'output'        => array(
				'post_status'   => 'draft', // draft|publish|future
				'schedule_at'   => '',      // GMT datetime for future
				'ai_enabled'    => false,
				'ai_provider'   => '',
				'regenerate_mode' => 'content_only', // content_only|full — never changes fingerprint/URL
			),
			'created_at'    => '',
			'updated_at'    => '',
		);
	}

	/**
	 * @return array<int,array<string,mixed>>
	 */
	public function all() {
		$rules = $this->read_option();
		/**
		 * @param array $rules Rules.
		 */
		return apply_filters( 'kayan_pseo_rules', $rules );
	}

	/**
	 * @param string $id Rule id.
	 * @return array<string,mixed>|null
	 */
	public function get( $id ) {
		$id = sanitize_key( $id );
		foreach ( $this->all() as $rule ) {
			if ( isset( $rule['id'] ) && $rule['id'] === $id ) {
				return $rule;
			}
		}
		return null;
	}

	/**
	 * Validate + normalize a rule definition (no persistence side effects beyond return).
	 *
	 * @param array<string,mixed> $rule Rule.
	 * @return array{ok:bool,rule?:array,errors?:string[]}
	 */
	public function validate( array $rule ) {
		$errors = array();
		$rule   = $this->merge_deep( $this->schema_defaults(), $rule );

		if ( empty( $rule['id'] ) ) {
			$rule['id'] = $this->generate_id();
		} else {
			$rule['id'] = sanitize_key( $rule['id'] );
		}

		$pattern_id = sanitize_key( (string) $rule['pattern_id'] );
		if ( ! $this->patterns->get( $pattern_id ) ) {
			$errors[] = 'unknown_pattern';
		}
		$rule['pattern_id'] = $pattern_id;

		foreach ( array( 'countries', 'cities', 'services', 'categories', 'languages', 'areas', 'districts', 'neighborhoods', 'landmarks', 'brands', 'buildings', 'faqs', 'pricing' ) as $key ) {
			if ( ! isset( $rule['filters'][ $key ] ) || ! is_array( $rule['filters'][ $key ] ) ) {
				$rule['filters'][ $key ] = array();
			}
			$rule['filters'][ $key ] = array_values( array_filter( array_map( 'sanitize_text_field', $rule['filters'][ $key ] ) ) );
		}

		$status = sanitize_key( (string) $rule['output']['post_status'] );
		if ( ! in_array( $status, array( 'draft', 'publish', 'future' ), true ) ) {
			$errors[] = 'invalid_post_status';
			$status   = 'draft';
		}
		$rule['output']['post_status'] = $status;

		if ( empty( $errors ) ) {
			return array(
				'ok'   => true,
				'rule' => $rule,
			);
		}

		return array(
			'ok'     => false,
			'errors' => $errors,
			'rule'   => $rule,
		);
	}

	/**
	 * Persist a rule definition (API for future admin — no UI in 2.5).
	 *
	 * @param array<string,mixed> $rule Rule.
	 * @return array{ok:bool,rule?:array,errors?:string[]}
	 */
	public function save( array $rule ) {
		$result = $this->validate( $rule );
		if ( empty( $result['ok'] ) ) {
			return $result;
		}

		$rule = $result['rule'];
		$now  = gmdate( 'c' );
		if ( empty( $rule['created_at'] ) ) {
			$rule['created_at'] = $now;
		}
		$rule['updated_at'] = $now;

		$all    = $this->read_option();
		$found  = false;
		foreach ( $all as $i => $existing ) {
			if ( isset( $existing['id'] ) && $existing['id'] === $rule['id'] ) {
				$all[ $i ] = $rule;
				$found     = true;
				break;
			}
		}
		if ( ! $found ) {
			$all[] = $rule;
		}

		$this->write_option( $all );

		return array(
			'ok'   => true,
			'rule' => $rule,
		);
	}

	/**
	 * @param string $id Rule id.
	 * @return bool
	 */
	public function delete( $id ) {
		$id  = sanitize_key( $id );
		$all = $this->read_option();
		$new = array();
		foreach ( $all as $rule ) {
			if ( ! isset( $rule['id'] ) || $rule['id'] !== $id ) {
				$new[] = $rule;
			}
		}
		return $this->write_option( $new );
	}

	/**
	 * Expand a rule into concrete combination specs (dry-run, no writes).
	 * Returns empty until entity catalogs are queried in a generation phase.
	 *
	 * @param string $rule_id Rule id.
	 * @return array{ok:bool,combinations?:array,errors?:string[],count?:int}
	 */
	public function preview_combinations( $rule_id ) {
		$rule = $this->get( $rule_id );
		if ( ! $rule ) {
			return array(
				'ok'     => false,
				'errors' => array( 'rule_not_found' ),
			);
		}

		$pattern = $this->patterns->get( $rule['pattern_id'] );
		if ( ! $pattern ) {
			return array(
				'ok'     => false,
				'errors' => array( 'pattern_not_found' ),
			);
		}

		/**
		 * Future generation phase hooks here to enumerate entity cartesian products.
		 * Phase 2.5 returns a structured empty preview contract.
		 *
		 * @param array $combinations Combinations.
		 * @param array $rule         Rule.
		 * @param array $pattern      Pattern.
		 */
		$combinations = apply_filters( 'kayan_pseo_preview_combinations', array(), $rule, $pattern );

		return array(
			'ok'            => true,
			'rule_id'       => $rule['id'],
			'pattern_id'    => $rule['pattern_id'],
			'filters'       => $rule['filters'],
			'combinations'  => is_array( $combinations ) ? $combinations : array(),
			'count'         => is_array( $combinations ) ? count( $combinations ) : 0,
			'note'          => 'Phase 2.5 architecture only — combination expansion executes in a later generation phase.',
		);
	}

	/**
	 * @return string
	 */
	private function generate_id() {
		return 'rule_' . substr( md5( uniqid( 'kayan_pseo', true ) ), 0, 12 );
	}

	/**
	 * @return array
	 */
	private function read_option() {
		if ( function_exists( 'yc_get_option' ) ) {
			$val = yc_get_option( self::OPTION_KEY, array() );
		} else {
			$val = get_option( self::OPTION_KEY, array() );
		}
		return is_array( $val ) ? array_values( $val ) : array();
	}

	/**
	 * @param array $rules Rules.
	 * @return bool
	 */
	private function write_option( array $rules ) {
		if ( function_exists( 'yc_update_option' ) ) {
			return (bool) yc_update_option( self::OPTION_KEY, array_values( $rules ) );
		}
		return (bool) update_option( self::OPTION_KEY, array_values( $rules ), false );
	}

	/**
	 * @param array $base Base.
	 * @param array $over Overlay.
	 * @return array
	 */
	private function merge_deep( array $base, array $over ) {
		foreach ( $over as $k => $v ) {
			if ( is_array( $v ) && isset( $base[ $k ] ) && is_array( $base[ $k ] ) ) {
				$base[ $k ] = $this->merge_deep( $base[ $k ], $v );
			} else {
				$base[ $k ] = $v;
			}
		}
		return $base;
	}
}
