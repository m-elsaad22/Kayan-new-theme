<?php
/**
 * Kayan_Entity_Engine — native Entity Relationship Engine facade.
 *
 * Platform foundation: reusable entities + relationships + Entity API.
 * Compatible with Rank Math and Phases 1–2.5.1. No generation / no admin UI.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Entity_Engine {

	/** @var Kayan_Entity_API */
	public $api;

	/** @var Kayan_Entity_Relationships */
	public $relationships;

	/** @var Kayan_Programmatic_SEO */
	private $programmatic;

	public function __construct(
		Kayan_Programmatic_SEO $programmatic,
		Kayan_Country_Engine $countries,
		Kayan_Country_Settings $settings,
		Kayan_Content_Locale $locale
	) {
		$this->programmatic  = $programmatic;
		$this->api           = new Kayan_Entity_API( $programmatic, $countries, $settings, $locale );
		$this->relationships = new Kayan_Entity_Relationships( $this->api );
	}

	/**
	 * @return void
	 */
	public function register() {
		$this->register_core_entity_types();
		$this->relationships->register();

		/**
		 * @param Kayan_Entity_Engine $engine Engine.
		 */
		do_action( 'kayan_entity_engine_registered', $this );
	}

	/**
	 * Proxy helpers — preferred public surface.
	 *
	 * @param string     $type Type.
	 * @param string|int $ref  Ref.
	 * @return array<string,mixed>|null
	 */
	public function get( $type, $ref ) {
		return $this->api->get( $type, $ref );
	}

	/**
	 * @param string     $type Type.
	 * @param string|int $ref  Ref.
	 * @return string
	 */
	public function name( $type, $ref ) {
		return $this->api->get_name( $type, $ref );
	}

	/**
	 * @param string     $type  Type.
	 * @param string|int $ref   Ref.
	 * @param string     $field Field.
	 * @param mixed      $default Default.
	 * @return mixed
	 */
	public function field( $type, $ref, $field, $default = '' ) {
		return $this->api->get_field( $type, $ref, $field, $default );
	}

	/**
	 * @param string      $type    Type.
	 * @param string|int  $ref     Ref.
	 * @param string|null $to_type To type.
	 * @param string|null $rel     Rel.
	 * @return array<int,array<string,mixed>>
	 */
	public function related( $type, $ref, $to_type = null, $rel = null ) {
		return $this->relationships->get_related_entities( $type, $ref, $to_type, $rel );
	}

	/**
	 * @param string     $from_type From.
	 * @param string|int $from_ref  From ref.
	 * @param string     $to_type   To.
	 * @param string|int $to_ref    To ref.
	 * @param string     $rel       Rel.
	 * @param array      $args      Args.
	 * @return array{ok:bool,errors?:string[],edge?:array}
	 */
	public function relate( $from_type, $from_ref, $to_type, $to_ref, $rel = 'related', array $args = array() ) {
		return $this->relationships->relate( $from_type, $from_ref, $to_type, $to_ref, $rel, $args );
	}

	/**
	 * Integrator snapshot.
	 *
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'version'       => defined( 'KAYAN_PLATFORM_VERSION' ) ? KAYAN_PLATFORM_VERSION : '2.6.0',
			'types'         => array_keys( $this->api->types() ),
			'relationships' => $this->relationships->capabilities(),
			'apis'          => array(
				'get'      => 'kayan_entity()->get( $type, $ref )',
				'name'     => 'kayan_entity()->name( $type, $ref )',
				'field'    => 'kayan_entity()->field( $type, $ref, $field )',
				'related'  => 'kayan_entity()->related( $type, $ref, $to_type )',
				'relate'   => 'kayan_entity()->relate( $from, $from_ref, $to, $to_ref, $rel )',
				'query'    => 'kayan_entity()->api->query( $type, $args )',
				'types'    => 'kayan_entity()->api->types()',
			),
			'rank_math'     => true,
			'generation'    => false,
		);
	}

	/**
	 * Ensure user-requested entity types exist (gallery, video, article, …)
	 * without duplicating existing programmatic registrations.
	 *
	 * @return void
	 */
	private function register_core_entity_types() {
		// Enrich existing types with Entity API field contracts.
		foreach ( $this->programmatic->get_entity_types() as $id => $def ) {
			$this->api->register_type(
				$id,
				array_merge(
					$def,
					array(
						'fields' => $this->default_fields_for( $id ),
					)
				)
			);
		}

		// Explicit user-requested types not previously first-class.
		$this->api->register_type(
			'gallery',
			array(
				'label'   => 'Gallery',
				'segment' => 'gallery',
				'kind'    => Kayan_Entity_API::KIND_VIRTUAL,
				'enabled' => false,
				'source'  => 'generated',
				'fields'  => array( 'name', 'slug', 'items', 'featured_image' ),
			)
		);

		$this->api->register_type(
			'video',
			array(
				'label'   => 'Video',
				'segment' => 'video',
				'kind'    => Kayan_Entity_API::KIND_VIRTUAL,
				'enabled' => false,
				'source'  => 'generated',
				'fields'  => array( 'name', 'slug', 'url', 'provider', 'poster' ),
			)
		);

		$this->api->register_type(
			'article',
			array(
				'label'     => 'Article',
				'segment'   => 'blog',
				'post_type' => 'post',
				'enabled'   => true,
				'source'    => 'manual',
				'fields'    => array( 'name', 'slug', 'excerpt', 'content', 'featured_image' ),
				'combinators' => array( 'service', 'city', 'category' ),
			)
		);

		/**
		 * @param Kayan_Entity_API $api API.
		 */
		do_action( 'kayan_entity_register_types', $this->api );
	}

	/**
	 * @param string $type Type.
	 * @return string[]
	 */
	private function default_fields_for( $type ) {
		$map = array(
			'country'      => array( 'name', 'slug', 'phone', 'whatsapp', 'currency' ),
			'city'         => array( 'name', 'slug', 'description', 'country' ),
			'area'         => array( 'name', 'slug', 'city' ),
			'district'     => array( 'name', 'slug', 'city' ),
			'neighborhood' => array( 'name', 'slug', 'city' ),
			'landmark'     => array( 'name', 'slug', 'city' ),
			'service'      => array( 'name', 'slug', 'excerpt', 'content', 'featured_image', 'price_from' ),
			'category'     => array( 'name', 'slug', 'description' ),
			'faq'          => array( 'name', 'slug', 'content' ),
			'pricing'      => array( 'name', 'slug', 'price_from', 'content' ),
			'review'       => array( 'name', 'slug', 'rating', 'content' ),
			'portfolio'    => array( 'name', 'slug', 'featured_image', 'gallery' ),
			'article'      => array( 'name', 'slug', 'excerpt', 'content', 'featured_image' ),
		);
		return isset( $map[ $type ] ) ? $map[ $type ] : array( 'name', 'slug', 'excerpt', 'content', 'featured_image' );
	}
}
