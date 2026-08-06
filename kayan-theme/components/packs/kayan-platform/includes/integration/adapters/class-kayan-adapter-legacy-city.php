<?php
/**
 * Adapter: legacy taxonomy `city` ↔ canonical `cities` (kayan-cpt).
 *
 * Does not migrate terms. Unregisters empty legacy taxonomy only.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Adapter_Legacy_City {

	/** @var string */
	private $legacy = 'city';

	/** @var string */
	private $canonical = 'cities';

	/**
	 * @return void
	 */
	public function register() {
		// After ThemeTree Initialize + kayan-cpt registration.
		add_action( 'init', array( $this, 'maybe_unregister_legacy' ), 99 );
		add_filter( 'kayan_platform_taxonomy_rewrite_map', array( $this, 'rewrite_map' ), 20 );
	}

	/**
	 * Keep platform map pointing cities → city slug (already default). Document only if needed.
	 *
	 * @param array<string,string> $map Map.
	 * @return array<string,string>
	 */
	public function rewrite_map( $map ) {
		if ( ! is_array( $map ) ) {
			$map = array();
		}
		if ( ! isset( $map[ $this->canonical ] ) ) {
			$map[ $this->canonical ] = 'city';
		}
		return $map;
	}

	/**
	 * @return void
	 */
	public function maybe_unregister_legacy() {
		if ( ! taxonomy_exists( $this->legacy ) ) {
			return;
		}
		if ( taxonomy_exists( $this->canonical ) && $this->legacy_term_count() === 0 ) {
			unregister_taxonomy( $this->legacy );
			if ( function_exists( 'kayan_logger' ) ) {
				kayan_logger()->info(
					'general',
					'theme.integration.legacy_city_unregistered',
					array( 'reason' => 'empty' )
				);
			}
		}
	}

	/**
	 * @return int
	 */
	private function legacy_term_count() {
		$terms = get_terms(
			array(
				'taxonomy'   => $this->legacy,
				'hide_empty' => false,
				'fields'     => 'ids',
				'number'     => 1,
			)
		);
		if ( is_wp_error( $terms ) ) {
			return 0;
		}
		return is_array( $terms ) ? count( $terms ) : 0;
	}

	/**
	 * Full count for status (may be heavier).
	 *
	 * @return int
	 */
	public function legacy_count() {
		if ( ! taxonomy_exists( $this->legacy ) ) {
			return 0;
		}
		$terms = get_terms(
			array(
				'taxonomy'   => $this->legacy,
				'hide_empty' => false,
				'fields'     => 'count',
			)
		);
		return is_numeric( $terms ) ? (int) $terms : 0;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function status() {
		$legacy_exists = taxonomy_exists( $this->legacy );
		$count         = $legacy_exists ? $this->legacy_count() : 0;
		$state         = 'compatible';
		if ( $legacy_exists && $count > 0 ) {
			$state = 'deprecated';
		} elseif ( ! $legacy_exists && taxonomy_exists( $this->canonical ) ) {
			$state = 'compatible';
		}
		return array(
			'id'                 => 'legacy_city',
			'state'              => $state,
			'legacy_taxonomy'    => $this->legacy,
			'canonical_taxonomy' => $this->canonical,
			'legacy_exists'      => $legacy_exists,
			'legacy_term_count'  => $count,
			'notes'              => 'Canonical cities taxonomy from kayan-cpt. Empty legacy city unregistered; non-empty kept for BC.',
		);
	}
}
