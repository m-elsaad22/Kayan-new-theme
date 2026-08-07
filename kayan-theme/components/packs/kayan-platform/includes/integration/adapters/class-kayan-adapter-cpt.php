<?php
/**
 * Adapter: kayan-cpt ↔ Content Locale + router rewrite maps.
 *
 * Prefer existing CPT pack. Only extends platform type lists when missing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Adapter_CPT {

	/** @var string[] */
	private $types = array( 'services', 'reviews', 'faqs', 'pricing', 'portfolio', 'before_after' );

	/**
	 * @return void
	 */
	public function register() {
		add_filter( 'kayan_platform_content_post_types', array( $this, 'content_types' ), 20 );
		add_filter( 'kayan_platform_post_type_rewrite_map', array( $this, 'rewrite_map' ), 20 );
	}

	/**
	 * @param string[] $types Types.
	 * @return string[]
	 */
	public function content_types( $types ) {
		if ( ! is_array( $types ) ) {
			$types = array();
		}
		foreach ( $this->types as $type ) {
			$types[] = $type;
		}
		$types[] = 'post';
		$types[] = 'page';
		return array_values( array_unique( $types ) );
	}

	/**
	 * @param array<string,string> $map Map.
	 * @return array<string,string>
	 */
	public function rewrite_map( $map ) {
		if ( ! is_array( $map ) ) {
			$map = array();
		}
		$defaults = array(
			'services'     => 'services',
			'reviews'      => 'reviews',
			'faqs'         => 'faqs',
			'pricing'      => 'pricing',
			'portfolio'    => 'portfolio',
			'before_after' => 'before-after',
		);
		foreach ( $defaults as $pt => $slug ) {
			if ( ! isset( $map[ $pt ] ) ) {
				$map[ $pt ] = $slug;
			}
		}
		return $map;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function status() {
		$registered = array();
		foreach ( $this->types as $type ) {
			$registered[ $type ] = post_type_exists( $type );
		}
		return array(
			'id'         => 'cpt',
			'state'      => 'extension',
			'types'      => $registered,
			'taxonomies' => array(
				'cities'             => taxonomy_exists( 'cities' ),
				'service_categories' => taxonomy_exists( 'service_categories' ),
			),
			'notes'      => 'Reuses kayan-cpt; extends locale post types + rewrite map only.',
		);
	}
}
