<?php
/**
 * PSEO Pattern Registry — reusable entity combinations (no generation).
 *
 * One pattern definition drives Service×City, FAQ×Service, etc.
 * without duplicated generators per combination.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Patterns {

	/** @var array<string,array<string,mixed>> */
	private $patterns = array();

	/**
	 * @return void
	 */
	public function register() {
		$this->register_core_patterns();

		/**
		 * @param Kayan_PSEO_Patterns $registry Registry.
		 */
		do_action( 'kayan_pseo_register_patterns', $this );
	}

	/**
	 * @param string              $id   Pattern id.
	 * @param array<string,mixed> $args Args.
	 * @return void
	 */
	public function register_pattern( $id, array $args ) {
		$id = sanitize_key( $id );
		if ( '' === $id ) {
			return;
		}

		$defaults = array(
			'label'           => $id,
			'description'     => '',
			'entities'        => array(), // ordered entity type ids, e.g. array( 'service', 'city' )
			'required'        => array(), // subset that must be present
			'optional'        => array(),
			'url_template'    => '{primary}/{secondary}', // path after country/lang prefixes
			'primary_entity'  => '',
			'enabled'         => true,
			'blueprint_slots' => array(
				'hero',
				'cta',
				'faq',
				'reviews',
				'images',
				'internal_links',
				'breadcrumb',
				'schema',
			),
		);

		$args['entities'] = array_values( array_filter( array_map( 'sanitize_key', (array) ( $args['entities'] ?? array() ) ) ) );
		if ( empty( $args['required'] ) ) {
			$args['required'] = $args['entities'];
		}

		$this->patterns[ $id ] = array_merge( $defaults, $args, array( 'id' => $id ) );
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function all() {
		/**
		 * @param array $patterns Patterns.
		 */
		return apply_filters( 'kayan_pseo_patterns', $this->patterns );
	}

	/**
	 * @param string $id Pattern id.
	 * @return array<string,mixed>|null
	 */
	public function get( $id ) {
		$id       = sanitize_key( $id );
		$patterns = $this->all();
		return isset( $patterns[ $id ] ) ? $patterns[ $id ] : null;
	}

	/**
	 * Build relative path segment from template + entity tokens (no HTTP, no writes).
	 *
	 * @param string                $pattern_id Pattern id.
	 * @param array<string,string>  $tokens     Token map (service_slug, city_slug, …).
	 * @return string
	 */
	public function build_path_slug( $pattern_id, array $tokens ) {
		$pattern = $this->get( $pattern_id );
		if ( ! $pattern ) {
			return '';
		}

		$template = (string) $pattern['url_template'];
		$path     = $template;

		foreach ( $tokens as $key => $value ) {
			$path = str_replace( '{' . $key . '}', sanitize_title( (string) $value ), $path );
		}

		// Drop unresolved optional tokens.
		$path = preg_replace( '/\{[a-z0-9_]+\}/', '', $path );
		$path = preg_replace( '#/+#', '/', (string) $path );
		$path = trim( (string) $path, '/' );

		/**
		 * @param string $path       Path slug.
		 * @param string $pattern_id Pattern.
		 * @param array  $tokens     Tokens.
		 */
		return apply_filters( 'kayan_pseo_pattern_path_slug', $path, $pattern_id, $tokens );
	}

	/**
	 * @return void
	 */
	private function register_core_patterns() {
		$this->register_pattern(
			'service_city',
			array(
				'label'          => 'Service × City',
				'entities'       => array( 'service', 'city' ),
				'primary_entity' => 'service',
				'url_template'   => 'services/{service_slug}/{city_slug}',
			)
		);

		$this->register_pattern(
			'service_area',
			array(
				'label'          => 'Service × Area',
				'entities'       => array( 'service', 'area' ),
				'primary_entity' => 'service',
				'url_template'   => 'services/{service_slug}/{area_slug}',
			)
		);

		$this->register_pattern(
			'service_country',
			array(
				'label'          => 'Service × Country',
				'entities'       => array( 'service', 'country' ),
				'primary_entity' => 'service',
				'url_template'   => 'services/{service_slug}',
			)
		);

		$this->register_pattern(
			'category_city',
			array(
				'label'          => 'Category × City',
				'entities'       => array( 'category', 'city' ),
				'primary_entity' => 'category',
				'url_template'   => 'service-category/{category_slug}/{city_slug}',
			)
		);

		$this->register_pattern(
			'faq_service',
			array(
				'label'          => 'FAQ × Service',
				'entities'       => array( 'faq', 'service' ),
				'primary_entity' => 'faq',
				'url_template'   => 'faqs/{faq_slug}/{service_slug}',
			)
		);

		$this->register_pattern(
			'pricing_service',
			array(
				'label'          => 'Pricing × Service',
				'entities'       => array( 'pricing', 'service' ),
				'primary_entity' => 'pricing',
				'url_template'   => 'pricing/{pricing_slug}/{service_slug}',
			)
		);

		$this->register_pattern(
			'landmark_service',
			array(
				'label'          => 'Landmark × Service',
				'entities'       => array( 'landmark', 'service' ),
				'primary_entity' => 'service',
				'url_template'   => 'services/{service_slug}/{landmark_slug}',
			)
		);

		$this->register_pattern(
			'country_service_city',
			array(
				'label'          => 'Country × Service × City',
				'entities'       => array( 'country', 'service', 'city' ),
				'primary_entity' => 'service',
				'url_template'   => 'services/{service_slug}/{city_slug}',
			)
		);

		$this->register_pattern(
			'service_neighborhood',
			array(
				'label'          => 'Service × Neighborhood',
				'entities'       => array( 'service', 'neighborhood' ),
				'primary_entity' => 'service',
				'url_template'   => 'services/{service_slug}/{neighborhood_slug}',
			)
		);

		$this->register_pattern(
			'service_building',
			array(
				'label'          => 'Service × Building',
				'entities'       => array( 'service', 'building' ),
				'primary_entity' => 'service',
				'url_template'   => 'services/{service_slug}/{building_slug}',
			)
		);

		$this->register_pattern(
			'brand_service_city',
			array(
				'label'          => 'Brand × Service × City',
				'entities'       => array( 'brand', 'service', 'city' ),
				'primary_entity' => 'service',
				'url_template'   => 'services/{service_slug}/{brand_slug}/{city_slug}',
			)
		);
	}
}
