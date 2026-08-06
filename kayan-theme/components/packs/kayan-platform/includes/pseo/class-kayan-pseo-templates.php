<?php
/**
 * PSEO Template Engine — assignable page structures composed of Blocks.
 *
 * Patterns reference a template_id. Templates own block order + defaults.
 * No rendering in this phase.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Templates {

	/** @var Kayan_PSEO_Blocks */
	private $blocks;

	/** @var array<string,array<string,mixed>> */
	private $templates = array();

	public function __construct( Kayan_PSEO_Blocks $blocks ) {
		$this->blocks = $blocks;
	}

	/**
	 * @return void
	 */
	public function register() {
		$this->register_core_templates();

		/**
		 * @param Kayan_PSEO_Templates $registry Registry.
		 */
		do_action( 'kayan_pseo_register_templates', $this );
	}

	/**
	 * @param string              $id   Template id.
	 * @param array<string,mixed> $args Args.
	 * @return void
	 */
	public function register_template( $id, array $args ) {
		$id = sanitize_key( $id );
		if ( '' === $id ) {
			return;
		}

		$defaults = array(
			'label'            => $id,
			'description'      => '',
			'version'          => 1,
			'enabled'          => true,
			'blocks'           => array(), // ordered block ids
			'optional_blocks'  => array(),
			'media_profile'    => 'standard', // standard|service|faq
			'preferred_for'    => array(), // pattern ids that default to this template
		);

		$args['blocks'] = array_values( array_filter( array_map( 'sanitize_key', (array) ( $args['blocks'] ?? array() ) ) ) );
		$this->templates[ $id ] = array_merge( $defaults, $args, array( 'id' => $id ) );
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function all() {
		/**
		 * @param array $templates Templates.
		 */
		return apply_filters( 'kayan_pseo_templates', $this->templates );
	}

	/**
	 * @param string $id Template id.
	 * @return array<string,mixed>|null
	 */
	public function get( $id ) {
		$id        = sanitize_key( $id );
		$templates = $this->all();
		return isset( $templates[ $id ] ) ? $templates[ $id ] : null;
	}

	/**
	 * Build ordered block instances for a template (empty data).
	 *
	 * @param string $template_id Template id.
	 * @return array<string,array<string,mixed>>
	 */
	public function build_block_instances( $template_id ) {
		$template = $this->get( $template_id );
		if ( ! $template ) {
			return array();
		}

		$instances = array();
		foreach ( (array) $template['blocks'] as $block_id ) {
			if ( ! $this->blocks->get( $block_id ) ) {
				continue;
			}
			$instances[ $block_id ] = $this->blocks->empty_instance( $block_id );
		}

		/**
		 * @param array  $instances   Instances.
		 * @param string $template_id Template.
		 */
		return apply_filters( 'kayan_pseo_template_block_instances', $instances, $template_id );
	}

	/**
	 * @return void
	 */
	private function register_core_templates() {
		$this->register_template(
			'tpl_service_city',
			array(
				'label'         => 'Service × City',
				'version'       => 1,
				'preferred_for' => array( 'service_city' ),
				'blocks'        => array(
					'hero',
					'cta',
					'gallery',
					'faq',
					'pricing',
					'reviews',
					'map',
					'areas',
					'related_services',
					'related_articles',
					'related_cities',
					'internal_links',
					'videos',
					'schema_source',
					'breadcrumb',
				),
			)
		);

		$this->register_template(
			'tpl_country_service_city',
			array(
				'label'         => 'Country × Service × City',
				'version'       => 1,
				'preferred_for' => array( 'country_service_city' ),
				'blocks'        => array(
					'hero',
					'cta',
					'gallery',
					'faq',
					'pricing',
					'reviews',
					'map',
					'areas',
					'related_services',
					'related_articles',
					'related_cities',
					'internal_links',
					'videos',
					'schema_source',
					'breadcrumb',
				),
			)
		);

		$this->register_template(
			'tpl_service_area',
			array(
				'label'         => 'Service × Area',
				'version'       => 1,
				'preferred_for' => array( 'service_area', 'service_neighborhood' ),
				'blocks'        => array(
					'hero',
					'cta',
					'faq',
					'areas',
					'map',
					'related_services',
					'internal_links',
					'schema_source',
					'breadcrumb',
				),
			)
		);

		$this->register_template(
			'tpl_faq_service',
			array(
				'label'         => 'FAQ × Service',
				'version'       => 1,
				'preferred_for' => array( 'faq_service' ),
				'media_profile' => 'faq',
				'blocks'        => array(
					'hero',
					'faq',
					'cta',
					'related_services',
					'internal_links',
					'schema_source',
					'breadcrumb',
				),
			)
		);

		$this->register_template(
			'tpl_pricing_service',
			array(
				'label'         => 'Pricing × Service',
				'version'       => 1,
				'preferred_for' => array( 'pricing_service' ),
				'blocks'        => array(
					'hero',
					'pricing',
					'faq',
					'cta',
					'reviews',
					'related_services',
					'internal_links',
					'schema_source',
					'breadcrumb',
				),
			)
		);

		$this->register_template(
			'tpl_service_country',
			array(
				'label'         => 'Service × Country',
				'version'       => 1,
				'preferred_for' => array( 'service_country' ),
				'blocks'        => array(
					'hero',
					'cta',
					'gallery',
					'faq',
					'pricing',
					'reviews',
					'related_cities',
					'related_services',
					'internal_links',
					'videos',
					'schema_source',
					'breadcrumb',
				),
			)
		);

		$this->register_template(
			'tpl_landmark_service',
			array(
				'label'         => 'Landmark × Service',
				'version'       => 1,
				'preferred_for' => array( 'landmark_service' ),
				'blocks'        => array(
					'hero',
					'cta',
					'map',
					'faq',
					'gallery',
					'related_services',
					'internal_links',
					'schema_source',
					'breadcrumb',
				),
			)
		);

		$this->register_template(
			'tpl_category_city',
			array(
				'label'         => 'Category × City',
				'version'       => 1,
				'preferred_for' => array( 'category_city' ),
				'blocks'        => array(
					'hero',
					'cta',
					'related_services',
					'areas',
					'faq',
					'internal_links',
					'schema_source',
					'breadcrumb',
				),
			)
		);

		$this->register_template(
			'tpl_brand_service_city',
			array(
				'label'         => 'Brand × Service × City',
				'version'       => 1,
				'preferred_for' => array( 'brand_service_city', 'service_building' ),
				'blocks'        => array(
					'hero',
					'cta',
					'gallery',
					'faq',
					'map',
					'reviews',
					'related_services',
					'internal_links',
					'schema_source',
					'breadcrumb',
				),
			)
		);
	}
}
