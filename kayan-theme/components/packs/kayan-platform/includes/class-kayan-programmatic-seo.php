<?php
/**
 * Programmatic SEO Architecture (Phase 2 foundation)
 *
 * First-class entity registry for million-page scale generation later.
 * Phase 2 registers entity types and URL segment contracts only —
 * no page generation, no template changes, no duplicated routers.
 *
 * Future phases generate URLs/pages from:
 * Country × City × Area × District × Service × Category × FAQ × Landmark × Pricing
 * through this single registry + Country Router + Content Resolver.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Programmatic_SEO {

	/** @var array<string,array<string,mixed>> */
	private $entities = array();

	/**
	 * @return void
	 */
	public function register() {
		$this->register_core_entities();

		/**
		 * Register additional programmatic entity types.
		 *
		 * @param Kayan_Programmatic_SEO $registry Registry.
		 */
		do_action( 'kayan_platform_register_entities', $this );
	}

	/**
	 * @param string              $id   Entity id.
	 * @param array<string,mixed> $args Args.
	 * @return void
	 */
	public function register_entity_type( $id, array $args ) {
		$id = sanitize_key( $id );
		if ( '' === $id ) {
			return;
		}

		$defaults = array(
			'label'       => $id,
			'segment'     => $id,
			'enabled'     => true,
			'post_type'   => '',
			'taxonomy'    => '',
			'pattern'     => '/{country?}/{lang?}/{segment}/{slug}/',
			'combinators' => array(), // e.g. array( 'country', 'city', 'service' )
			'source'      => 'manual', // manual|generated (future)
		);

		$this->entities[ $id ] = array_merge( $defaults, $args, array( 'id' => $id ) );
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function get_entity_types() {
		/**
		 * @param array $entities Entities.
		 */
		return apply_filters( 'kayan_platform_entity_types', $this->entities );
	}

	/**
	 * Entity types that expose dedicated URL segments to the Country Router.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function get_routable_entity_types() {
		$out = array();
		foreach ( $this->get_entity_types() as $id => $entity ) {
			if ( empty( $entity['enabled'] ) ) {
				continue;
			}
			$out[ $id ] = $entity;
		}
		return $out;
	}

	/**
	 * Build a canonical programmatic path (no HTTP). Used by future generators.
	 *
	 * @param string               $entity_id Entity id.
	 * @param array<string,string> $tokens    Tokens: country, lang, slug, city, …
	 * @return string Relative path.
	 */
	public function build_entity_path( $entity_id, array $tokens = array() ) {
		$entities = $this->get_entity_types();
		if ( ! isset( $entities[ $entity_id ] ) ) {
			return '';
		}

		$entity  = $entities[ $entity_id ];
		$segment = isset( $entity['segment'] ) ? trim( (string) $entity['segment'], '/' ) : $entity_id;
		$slug    = isset( $tokens['slug'] ) ? sanitize_title( $tokens['slug'] ) : '';
		$country = isset( $tokens['country'] ) ? sanitize_key( $tokens['country'] ) : '';
		$lang    = isset( $tokens['lang'] ) ? sanitize_key( $tokens['lang'] ) : 'ar';

		$parts = array();
		if ( 'en' === $lang ) {
			$parts[] = 'en';
		}
		if ( $country && 'ae' !== $country ) {
			$parts[] = $country;
		}

		if ( ! empty( $entity['post_type'] ) ) {
			$map = array(
				'services'     => 'services',
				'reviews'      => 'reviews',
				'faqs'         => 'faqs',
				'pricing'      => 'pricing',
				'portfolio'    => 'portfolio',
				'before_after' => 'before-after',
			);
			$parts[] = isset( $map[ $entity['post_type'] ] ) ? $map[ $entity['post_type'] ] : $segment;
		} elseif ( ! empty( $entity['taxonomy'] ) ) {
			$tax_map = array(
				'cities'             => 'city',
				'service_categories' => 'service-category',
				'category'           => 'category',
			);
			$parts[] = isset( $tax_map[ $entity['taxonomy'] ] ) ? $tax_map[ $entity['taxonomy'] ] : $segment;
		} else {
			$parts[] = $segment;
		}

		if ( $slug ) {
			$parts[] = $slug;
		}

		$path = '/' . implode( '/', array_filter( $parts ) ) . '/';

		/**
		 * @param string $path      Path.
		 * @param string $entity_id Entity.
		 * @param array  $tokens    Tokens.
		 */
		return apply_filters( 'kayan_platform_entity_path', $path, $entity_id, $tokens );
	}

	/**
	 * @return void
	 */
	private function register_core_entities() {
		$this->register_entity_type(
			'country',
			array(
				'label'       => 'Country',
				'segment'     => '',
				'pattern'     => '/{country?}/{lang?}/',
				'combinators' => array( 'country', 'language' ),
				'source'      => 'manual',
			)
		);

		$this->register_entity_type(
			'city',
			array(
				'label'       => 'City',
				'segment'     => 'city',
				'taxonomy'    => 'cities',
				'combinators' => array( 'country', 'city' ),
			)
		);

		$this->register_entity_type(
			'area',
			array(
				'label'       => 'Area',
				'segment'     => 'area',
				'enabled'     => false, // reserved — enable when taxonomy/CPT exists
				'combinators' => array( 'country', 'city', 'area' ),
				'source'      => 'generated',
			)
		);

		$this->register_entity_type(
			'district',
			array(
				'label'       => 'District',
				'segment'     => 'district',
				'enabled'     => false,
				'combinators' => array( 'country', 'city', 'district' ),
				'source'      => 'generated',
			)
		);

		$this->register_entity_type(
			'neighborhood',
			array(
				'label'       => 'Neighborhood',
				'segment'     => 'neighborhood',
				'enabled'     => false,
				'combinators' => array( 'country', 'city', 'neighborhood' ),
				'source'      => 'generated',
			)
		);

		$this->register_entity_type(
			'brand',
			array(
				'label'       => 'Brand',
				'segment'     => 'brand',
				'enabled'     => false,
				'combinators' => array( 'brand', 'service', 'city' ),
				'source'      => 'generated',
			)
		);

		$this->register_entity_type(
			'building',
			array(
				'label'       => 'Building',
				'segment'     => 'building',
				'enabled'     => false,
				'combinators' => array( 'country', 'city', 'building' ),
				'source'      => 'generated',
			)
		);

		$this->register_entity_type(
			'service',
			array(
				'label'       => 'Service',
				'segment'     => 'services',
				'post_type'   => 'services',
				'combinators' => array( 'country', 'city', 'service' ),
			)
		);

		$this->register_entity_type(
			'category',
			array(
				'label'       => 'Category',
				'segment'     => 'service-category',
				'taxonomy'    => 'service_categories',
				'combinators' => array( 'country', 'category' ),
			)
		);

		$this->register_entity_type(
			'faq',
			array(
				'label'     => 'FAQ',
				'segment'   => 'faqs',
				'post_type' => 'faqs',
			)
		);

		$this->register_entity_type(
			'landmark',
			array(
				'label'   => 'Landmark',
				'segment' => 'landmark',
				'enabled' => false,
				'source'  => 'generated',
			)
		);

		$this->register_entity_type(
			'pricing',
			array(
				'label'     => 'Pricing',
				'segment'   => 'pricing',
				'post_type' => 'pricing',
			)
		);

		$this->register_entity_type(
			'review',
			array(
				'label'     => 'Review',
				'segment'   => 'reviews',
				'post_type' => 'reviews',
			)
		);

		$this->register_entity_type(
			'portfolio',
			array(
				'label'     => 'Portfolio',
				'segment'   => 'portfolio',
				'post_type' => 'portfolio',
			)
		);

		$this->register_entity_type(
			'before_after',
			array(
				'label'     => 'Before/After',
				'segment'   => 'before-after',
				'post_type' => 'before_after',
			)
		);
	}
}
