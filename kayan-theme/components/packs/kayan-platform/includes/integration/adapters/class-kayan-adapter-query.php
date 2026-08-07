<?php
/**
 * Adapter: Query Engine ↔ kayan-cpt resources.
 *
 * Ensures city resource stays on canonical `cities`. Optional legacy dual-read helper.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Adapter_Query {

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'kayan_query_register_resources', array( $this, 'ensure_resources' ), 20 );
	}

	/**
	 * @param Kayan_Query_Engine $query Query.
	 * @return void
	 */
	public function ensure_resources( $query ) {
		if ( ! $query || ! method_exists( $query, 'register_resource' ) ) {
			return;
		}

		$resources = method_exists( $query, 'resources' ) ? $query->resources() : array();
		if ( ! isset( $resources['city'] ) ) {
			$query->register_resource(
				'city',
				array(
					'label'    => 'City',
					'kind'     => 'term',
					'taxonomy' => 'cities',
				)
			);
		}

		// Legacy taxonomy as separate resource when terms still exist.
		if ( taxonomy_exists( 'city' ) ) {
			$query->register_resource(
				'legacy_city',
				array(
					'label'    => 'Legacy City',
					'kind'     => 'term',
					'taxonomy' => 'city',
					'enabled'  => true,
				)
			);
		}

		$cpt_map = array(
			'service'      => 'services',
			'review'       => 'reviews',
			'faq'          => 'faqs',
			'pricing'      => 'pricing',
			'portfolio'    => 'portfolio',
			'before_after' => 'before_after',
		);
		foreach ( $cpt_map as $id => $post_type ) {
			if ( isset( $resources[ $id ] ) ) {
				continue;
			}
			if ( ! post_type_exists( $post_type ) ) {
				continue;
			}
			$query->register_resource(
				$id,
				array(
					'label'     => ucwords( str_replace( '_', ' ', $id ) ),
					'kind'      => 'post',
					'post_type' => $post_type,
				)
			);
		}
	}

	/**
	 * @return array<string,mixed>
	 */
	public function status() {
		return array(
			'id'    => 'query',
			'state' => 'extension',
			'notes' => 'Ensures Query Engine resources for kayan-cpt; legacy_city resource when taxonomy remains.',
		);
	}
}
