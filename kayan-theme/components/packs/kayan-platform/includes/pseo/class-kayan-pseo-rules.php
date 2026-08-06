<?php
/**
 * PSEO Generation Rules — selection filters + targets + combination expansion.
 *
 * Phase 4: preview_combinations() enumerates real entity data via the
 * existing Query Engine (never a second query layer). Execution/page
 * creation lives in Kayan_PSEO_Generator + the Queue/Scheduler.
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
	 * Enumerates real entity data via the Query Engine — never a second
	 * query layer, never writes anything.
	 *
	 * @param string $rule_id Rule id.
	 * @return array{ok:bool,combinations?:array,errors?:string[],count?:int,truncated?:bool}
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
		 * @param int $limit Max combinations returned per preview/expansion.
		 */
		$limit = max( 1, (int) apply_filters( 'kayan_pseo_bulk_limit', 2000 ) );

		$countries = ! empty( $rule['filters']['countries'] ) ? $rule['filters']['countries'] : array( $this->default_country() );
		$languages = ! empty( $rule['filters']['languages'] ) ? $rule['filters']['languages'] : array( 'ar' );
		$entities  = (array) $pattern['entities'];

		$combinations = array();
		$truncated    = false;

		foreach ( $countries as $country ) {
			$country = sanitize_key( $country );
			foreach ( $languages as $language ) {
				$language = sanitize_key( $language );

				$sets = array();
				$empty_set = false;
				foreach ( $entities as $etype ) {
					if ( 'country' === $etype ) {
						$sets[ $etype ] = array( $country );
						continue;
					}
					$candidates = $this->candidates_for( $etype, $rule, $country );
					if ( empty( $candidates ) ) {
						$empty_set = true;
						break;
					}
					$sets[ $etype ] = $candidates;
				}
				if ( $empty_set ) {
					continue;
				}

				foreach ( $this->cartesian( $sets, $limit - count( $combinations ) ) as $combo ) {
					$tokens = array();
					foreach ( $combo as $etype => $slug ) {
						$tokens[ $etype . '_slug' ] = $slug;
					}
					$combinations[] = array(
						'pattern_id' => $rule['pattern_id'],
						'entities'   => $combo,
						'country'    => $country,
						'language'   => $language,
						'tokens'     => $tokens,
					);
					if ( count( $combinations ) >= $limit ) {
						break;
					}
				}
				if ( count( $combinations ) >= $limit ) {
					$truncated = true;
					break 2;
				}
			}
		}

		/**
		 * Extend or override the default enumeration (e.g. custom entity sources).
		 *
		 * @param array $combinations Combinations.
		 * @param array $rule         Rule.
		 * @param array $pattern      Pattern.
		 */
		$combinations = apply_filters( 'kayan_pseo_preview_combinations', $combinations, $rule, $pattern );

		return array(
			'ok'           => true,
			'rule_id'      => $rule['id'],
			'pattern_id'   => $rule['pattern_id'],
			'filters'      => $rule['filters'],
			'combinations' => is_array( $combinations ) ? array_values( $combinations ) : array(),
			'count'        => is_array( $combinations ) ? count( $combinations ) : 0,
			'truncated'    => $truncated,
			'limit'        => $limit,
		);
	}

	/**
	 * Candidate entity refs (slugs) for one entity type, honoring rule filters
	 * and (when available) country scoping via existing term meta.
	 *
	 * @param string               $type    Entity type.
	 * @param array<string,mixed>  $rule    Rule.
	 * @param string               $country Country code.
	 * @return string[]
	 */
	private function candidates_for( $type, array $rule, $country ) {
		if ( ! function_exists( 'kayan_query' ) ) {
			return array();
		}

		$resource_map = array(
			'service'  => 'service',
			'city'     => 'city',
			'category' => 'category',
			'faq'      => 'faq',
			'pricing'  => 'pricing',
			'review'   => 'review',
			'portfolio'=> 'portfolio',
		);
		$filter_map = array(
			'service'  => 'services',
			'city'     => 'cities',
			'category' => 'categories',
			'faq'      => 'faqs',
			'pricing'  => 'pricing',
			'review'   => 'services', // no dedicated review filter key yet — safe no-op default
			'portfolio'=> 'services',
		);

		if ( ! isset( $resource_map[ $type ] ) ) {
			// Unimplemented entity source (landmark/brand/building/area/district/neighborhood) —
			// intentionally empty until those become real WP content (taxonomy/CPT).
			return array();
		}

		$result = kayan_query()->query( $resource_map[ $type ], array( 'number' => 500 ) );
		$items  = isset( $result['items'] ) ? (array) $result['items'] : array();

		$allowed = isset( $rule['filters'][ $filter_map[ $type ] ] ) ? (array) $rule['filters'][ $filter_map[ $type ] ] : array();

		$slugs = array();
		foreach ( $items as $item ) {
			$slug = isset( $item['slug'] ) ? (string) $item['slug'] : '';
			$id   = isset( $item['id'] ) ? (string) $item['id'] : '';
			if ( '' === $slug ) {
				continue;
			}
			if ( ! empty( $allowed ) && ! in_array( $slug, $allowed, true ) && ! in_array( $id, $allowed, true ) ) {
				continue;
			}
			if ( 'city' === $type && $country && ! $this->city_matches_country( (int) ( $item['id'] ?? 0 ), $country ) ) {
				continue;
			}
			$slugs[] = $slug;
		}

		return array_values( array_unique( $slugs ) );
	}

	/**
	 * City term is included for a country if untagged (legacy/shared) or explicitly tagged.
	 * Mirrors the "empty meta = all" convention already used by Content Locale.
	 *
	 * @param int    $term_id City term id.
	 * @param string $country Country code.
	 * @return bool
	 */
	private function city_matches_country( $term_id, $country ) {
		if ( ! $term_id || ! function_exists( 'get_term_meta' ) ) {
			return true;
		}
		$tagged = get_term_meta( $term_id, 'kayan_country', true );
		if ( '' === $tagged || null === $tagged || false === $tagged ) {
			return true;
		}
		return sanitize_key( (string) $tagged ) === sanitize_key( (string) $country );
	}

	/**
	 * Iterative cartesian product with an early-stop limit (avoids building
	 * the full product in memory for very large entity catalogs).
	 *
	 * @param array<string,string[]> $sets  Entity type => candidate slugs.
	 * @param int                    $limit Max rows to produce.
	 * @return array<int,array<string,string>>
	 */
	private function cartesian( array $sets, $limit ) {
		if ( empty( $sets ) ) {
			return array();
		}
		$limit  = max( 0, (int) $limit );
		$result = array( array() );

		foreach ( $sets as $key => $values ) {
			$next = array();
			foreach ( $result as $combo ) {
				foreach ( $values as $value ) {
					$combo2         = $combo;
					$combo2[ $key ] = $value;
					$next[]         = $combo2;
					if ( count( $next ) >= $limit && $limit > 0 ) {
						break 2;
					}
				}
			}
			$result = $next;
			if ( empty( $result ) ) {
				break;
			}
		}

		return $limit > 0 ? array_slice( $result, 0, $limit ) : $result;
	}

	/**
	 * @return string
	 */
	private function default_country() {
		if ( function_exists( 'kayan_platform' ) ) {
			return kayan_platform()->countries->get_default();
		}
		return 'ae';
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
