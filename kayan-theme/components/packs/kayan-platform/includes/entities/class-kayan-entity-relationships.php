<?php
/**
 * Entity Relationship Engine — typed edges between reusable entities.
 *
 * Storage is owned by this engine (single meta contract). Consumers must
 * use relate() / get_related() instead of reading relationship meta directly.
 *
 * Architecture only: no generation, no admin UI, no template changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Entity_Relationships {

	const META_RELS      = 'kayan_entity_relationships';
	const TERM_META_RELS = 'kayan_entity_relationships';

	/** @var Kayan_Entity_API */
	private $api;

	/** @var array<string,array<int,string>> Allowed to-types per from-type. */
	private $allowed = array();

	public function __construct( Kayan_Entity_API $api ) {
		$this->api = $api;
	}

	/**
	 * @return void
	 */
	public function register() {
		$this->register_default_matrix();
		add_action( 'init', array( $this, 'register_meta' ), 22 );

		/**
		 * @param Kayan_Entity_Relationships $engine Engine.
		 */
		do_action( 'kayan_entity_relationships_registered', $this );
	}

	/**
	 * @return void
	 */
	public function register_meta() {
		$post_types = array( 'post', 'page', 'services', 'faqs', 'pricing', 'reviews', 'portfolio', 'before_after', 'kayan_pseo' );
		/**
		 * @param string[] $post_types Types.
		 */
		$post_types = apply_filters( 'kayan_entity_relationship_post_types', $post_types );

		foreach ( $post_types as $post_type ) {
			$post_type = sanitize_key( $post_type );
			if ( ! $post_type ) {
				continue;
			}
			register_post_meta(
				$post_type,
				self::META_RELS,
				array(
					'type'              => 'array',
					'single'            => true,
					'show_in_rest'      => true,
					'description'       => 'KAYAN entity relationship edges (owned by Entity Relationship Engine).',
					'auth_callback'     => static function () {
						return current_user_can( 'edit_posts' );
					},
					'sanitize_callback' => array( $this, 'sanitize_edges' ),
				)
			);
		}

		$taxonomies = array( 'cities', 'service_categories', 'category' );
		/**
		 * @param string[] $taxonomies Taxonomies.
		 */
		$taxonomies = apply_filters( 'kayan_entity_relationship_taxonomies', $taxonomies );

		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy = sanitize_key( $taxonomy );
			if ( ! $taxonomy ) {
				continue;
			}
			register_term_meta(
				$taxonomy,
				self::TERM_META_RELS,
				array(
					'type'              => 'array',
					'single'            => true,
					'show_in_rest'      => true,
					'description'       => 'KAYAN entity relationship edges.',
					'auth_callback'     => static function () {
						return current_user_can( 'edit_posts' );
					},
					'sanitize_callback' => array( $this, 'sanitize_edges' ),
				)
			);
		}
	}

	/**
	 * Allowed relationship matrix.
	 *
	 * @return array<string,array<int,string>>
	 */
	public function allowed_matrix() {
		/**
		 * @param array $allowed Matrix.
		 */
		return apply_filters( 'kayan_entity_allowed_relationships', $this->allowed );
	}

	/**
	 * Declare that $from may relate to $to.
	 *
	 * @param string $from From type.
	 * @param string $to   To type.
	 * @return void
	 */
	public function allow( $from, $to ) {
		$from = sanitize_key( $from );
		$to   = sanitize_key( $to );
		if ( ! $from || ! $to ) {
			return;
		}
		if ( ! isset( $this->allowed[ $from ] ) ) {
			$this->allowed[ $from ] = array();
		}
		if ( ! in_array( $to, $this->allowed[ $from ], true ) ) {
			$this->allowed[ $from ][] = $to;
		}
	}

	/**
	 * Create / upsert a relationship edge.
	 *
	 * @param string               $from_type From type.
	 * @param string|int           $from_ref  From ref.
	 * @param string               $to_type   To type.
	 * @param string|int           $to_ref    To ref.
	 * @param string               $rel       Relationship kind.
	 * @param array<string,mixed>  $args      Args: bidirectional, meta.
	 * @return array{ok:bool,errors?:string[],edge?:array}
	 */
	public function relate( $from_type, $from_ref, $to_type, $to_ref, $rel = 'related', array $args = array() ) {
		$from_type = sanitize_key( $from_type );
		$to_type   = sanitize_key( $to_type );
		$rel       = sanitize_key( $rel ? $rel : 'related' );
		$from_ref  = $this->normalize_ref( $from_type, $from_ref );
		$to_ref    = $this->normalize_ref( $to_type, $to_ref );

		if ( ! $from_type || ! $to_type || '' === $from_ref || '' === $to_ref ) {
			return array(
				'ok'     => false,
				'errors' => array( 'invalid_refs' ),
			);
		}

		if ( ! $this->is_allowed( $from_type, $to_type ) ) {
			return array(
				'ok'     => false,
				'errors' => array( 'relationship_not_allowed' ),
			);
		}

		$edge = array(
			'type' => $to_type,
			'ref'  => $to_ref,
			'rel'  => $rel,
			'meta' => isset( $args['meta'] ) && is_array( $args['meta'] ) ? $args['meta'] : array(),
		);

		$edges   = $this->get_edges( $from_type, $from_ref );
		$edges   = $this->upsert_edge( $edges, $edge );
		$written = $this->write_edges( $from_type, $from_ref, $edges );
		if ( ! $written ) {
			return array(
				'ok'     => false,
				'errors' => array( 'storage_unavailable' ),
			);
		}

		if ( ! empty( $args['bidirectional'] ) || $this->is_bidirectional_rel( $rel ) ) {
			$inverse = $this->inverse_rel( $rel );
			$back    = array(
				'type' => $from_type,
				'ref'  => $from_ref,
				'rel'  => $inverse,
				'meta' => array(),
			);
			$back_edges = $this->get_edges( $to_type, $to_ref );
			$back_edges = $this->upsert_edge( $back_edges, $back );
			$this->write_edges( $to_type, $to_ref, $back_edges );
		}

		/**
		 * @param array  $edge      Edge.
		 * @param string $from_type From.
		 * @param string $from_ref  From ref.
		 */
		do_action( 'kayan_entity_related', $edge, $from_type, $from_ref );

		return array(
			'ok'   => true,
			'edge' => $edge,
		);
	}

	/**
	 * @param string     $from_type From type.
	 * @param string|int $from_ref  From ref.
	 * @param string     $to_type   To type.
	 * @param string|int $to_ref    To ref.
	 * @param string|null $rel      Optional rel filter.
	 * @return array{ok:bool,errors?:string[]}
	 */
	public function unrelate( $from_type, $from_ref, $to_type, $to_ref, $rel = null ) {
		$from_type = sanitize_key( $from_type );
		$to_type   = sanitize_key( $to_type );
		$from_ref  = $this->normalize_ref( $from_type, $from_ref );
		$to_ref    = $this->normalize_ref( $to_type, $to_ref );
		$rel       = null !== $rel ? sanitize_key( $rel ) : null;

		$edges = $this->get_edges( $from_type, $from_ref );
		$keep  = array();
		foreach ( $edges as $edge ) {
			$match = ( $edge['type'] === $to_type && (string) $edge['ref'] === (string) $to_ref );
			if ( $match && ( null === $rel || $edge['rel'] === $rel ) ) {
				continue;
			}
			$keep[] = $edge;
		}
		$this->write_edges( $from_type, $from_ref, $keep );

		return array( 'ok' => true );
	}

	/**
	 * @param string      $type    Type.
	 * @param string|int  $ref     Ref.
	 * @param string|null $to_type Optional target type filter.
	 * @param string|null $rel     Optional rel filter.
	 * @return array<int,array<string,mixed>>
	 */
	public function get_related( $type, $ref, $to_type = null, $rel = null ) {
		$edges   = $this->get_edges( $type, $ref );
		$to_type = $to_type ? sanitize_key( $to_type ) : null;
		$rel     = $rel ? sanitize_key( $rel ) : null;
		$out     = array();

		foreach ( $edges as $edge ) {
			if ( $to_type && $edge['type'] !== $to_type ) {
				continue;
			}
			if ( $rel && $edge['rel'] !== $rel ) {
				continue;
			}
			$out[] = $edge;
		}

		/**
		 * @param array  $out  Edges.
		 * @param string $type Type.
		 * @param mixed  $ref  Ref.
		 */
		return apply_filters( 'kayan_entity_get_related', $out, $type, $ref );
	}

	/**
	 * Related entities as DTOs via Entity API.
	 *
	 * @param string      $type    Type.
	 * @param string|int  $ref     Ref.
	 * @param string|null $to_type Target type.
	 * @param string|null $rel     Rel.
	 * @return array<int,array<string,mixed>>
	 */
	public function get_related_entities( $type, $ref, $to_type = null, $rel = null ) {
		$out = array();
		foreach ( $this->get_related( $type, $ref, $to_type, $rel ) as $edge ) {
			$dto = $this->api->get( $edge['type'], $edge['ref'] );
			if ( $dto ) {
				$dto['_rel'] = $edge['rel'];
				$out[]       = $dto;
			}
		}
		return $out;
	}

	/**
	 * @param string     $from_type From.
	 * @param string|int $from_ref  From ref.
	 * @param string     $to_type   To.
	 * @param string|int $to_ref    To ref.
	 * @param string|null $rel      Rel.
	 * @return bool
	 */
	public function has( $from_type, $from_ref, $to_type, $to_ref, $rel = null ) {
		$to_ref = $this->normalize_ref( $to_type, $to_ref );
		foreach ( $this->get_related( $from_type, $from_ref, $to_type, $rel ) as $edge ) {
			if ( (string) $edge['ref'] === (string) $to_ref ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Raw edges for an entity (API-owned read).
	 *
	 * @param string     $type Type.
	 * @param string|int $ref  Ref.
	 * @return array<int,array<string,mixed>>
	 */
	public function get_edges( $type, $ref ) {
		$storage = $this->resolve_storage( $type, $ref );
		$edges   = array();

		if ( $storage ) {
			if ( 'post' === $storage['kind'] ) {
				$raw = get_post_meta( $storage['id'], self::META_RELS, true );
			} else {
				$raw = get_term_meta( $storage['id'], self::TERM_META_RELS, true );
			}
			$edges = $this->sanitize_edges( $raw );
		}

		// Bridge legacy city→country term meta into the relationship graph (read path).
		if ( 'city' === sanitize_key( $type ) && $storage && 'term' === $storage['kind'] ) {
			$country = get_term_meta( $storage['id'], Kayan_Content_Locale::TERM_META_COUNTRY, true );
			if ( $country ) {
				$edges = $this->upsert_edge(
					$edges,
					array(
						'type' => 'country',
						'ref'  => sanitize_key( (string) $country ),
						'rel'  => 'located_in',
						'meta' => array( 'source' => 'term_meta_kayan_country' ),
					)
				);
			}
		}

		/**
		 * Virtual / country relationships may be supplied via filter until storage exists.
		 *
		 * @param array  $edges Edges.
		 * @param string $type  Type.
		 * @param mixed  $ref   Ref.
		 */
		return $this->sanitize_edges( apply_filters( 'kayan_entity_virtual_relationships', $edges, $type, $ref ) );
	}

	/**
	 * @param mixed $value Raw.
	 * @return array<int,array<string,mixed>>
	 */
	public function sanitize_edges( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$out = array();
		foreach ( $value as $edge ) {
			if ( ! is_array( $edge ) ) {
				continue;
			}
			$type = sanitize_key( (string) ( $edge['type'] ?? '' ) );
			$ref  = sanitize_text_field( (string) ( $edge['ref'] ?? '' ) );
			$rel  = sanitize_key( (string) ( $edge['rel'] ?? 'related' ) );
			if ( ! $type || '' === $ref ) {
				continue;
			}
			$out[] = array(
				'type' => $type,
				'ref'  => $ref,
				'rel'  => $rel ? $rel : 'related',
				'meta' => isset( $edge['meta'] ) && is_array( $edge['meta'] ) ? $edge['meta'] : array(),
			);
		}
		return $out;
	}

	/**
	 * Describe capabilities for integrators.
	 *
	 * @return array<string,mixed>
	 */
	public function capabilities() {
		return array(
			'meta_key'      => self::META_RELS,
			'term_meta_key' => self::TERM_META_RELS,
			'allowed'       => $this->allowed_matrix(),
			'bidirectional' => array( 'related', 'serves', 'located_in', 'contains', 'in_category' ),
			'generation'    => false,
		);
	}

	/**
	 * @return void
	 */
	private function register_default_matrix() {
		// Geo hierarchy.
		$this->allow( 'country', 'city' );
		$this->allow( 'city', 'country' );
		$this->allow( 'city', 'area' );
		$this->allow( 'city', 'district' );
		$this->allow( 'city', 'neighborhood' );
		$this->allow( 'city', 'landmark' );
		$this->allow( 'area', 'city' );
		$this->allow( 'area', 'district' );
		$this->allow( 'area', 'neighborhood' );
		$this->allow( 'district', 'city' );
		$this->allow( 'district', 'area' );
		$this->allow( 'neighborhood', 'city' );
		$this->allow( 'neighborhood', 'area' );
		$this->allow( 'landmark', 'city' );
		$this->allow( 'landmark', 'area' );

		// Service graph.
		foreach ( array( 'city', 'area', 'district', 'neighborhood', 'landmark', 'category', 'faq', 'pricing', 'review', 'portfolio', 'gallery', 'video', 'article', 'brand', 'building' ) as $to ) {
			$this->allow( 'service', $to );
			$this->allow( $to, 'service' );
		}
		$this->allow( 'category', 'city' );
		$this->allow( 'city', 'category' );
		$this->allow( 'article', 'city' );
		$this->allow( 'article', 'category' );
		$this->allow( 'faq', 'category' );
		$this->allow( 'pricing', 'city' );
		$this->allow( 'review', 'city' );
		$this->allow( 'portfolio', 'city' );
		$this->allow( 'gallery', 'city' );
		$this->allow( 'video', 'service' );
		$this->allow( 'brand', 'city' );
		$this->allow( 'building', 'city' );

		/**
		 * @param Kayan_Entity_Relationships $engine Engine.
		 */
		do_action( 'kayan_entity_register_relationships', $this );
	}

	/**
	 * @param string $from From.
	 * @param string $to   To.
	 * @return bool
	 */
	private function is_allowed( $from, $to ) {
		$matrix = $this->allowed_matrix();
		if ( empty( $matrix[ $from ] ) ) {
			return false;
		}
		return in_array( $to, $matrix[ $from ], true );
	}

	/**
	 * @param string     $type Type.
	 * @param string|int $ref  Ref.
	 * @return string
	 */
	private function normalize_ref( $type, $ref ) {
		$dto = $this->api->get( $type, $ref );
		if ( $dto && isset( $dto['ref'] ) ) {
			return (string) $dto['ref'];
		}
		return sanitize_text_field( (string) $ref );
	}

	/**
	 * @param string     $type Type.
	 * @param string|int $ref  Ref.
	 * @return array{kind:string,id:int}|null
	 */
	private function resolve_storage( $type, $ref ) {
		$dto = $this->api->get( $type, $ref );
		if ( ! $dto ) {
			return null;
		}
		if ( Kayan_Entity_API::KIND_POST === $dto['kind'] && ! empty( $dto['id'] ) ) {
			return array(
				'kind' => 'post',
				'id'   => (int) $dto['id'],
			);
		}
		if ( Kayan_Entity_API::KIND_TERM === $dto['kind'] && ! empty( $dto['id'] ) ) {
			return array(
				'kind' => 'term',
				'id'   => (int) $dto['id'],
			);
		}
		return null;
	}

	/**
	 * @param string                          $type  Type.
	 * @param string|int                      $ref   Ref.
	 * @param array<int,array<string,mixed>>  $edges Edges.
	 * @return bool
	 */
	private function write_edges( $type, $ref, array $edges ) {
		$storage = $this->resolve_storage( $type, $ref );
		if ( ! $storage ) {
			/**
			 * Persist virtual/country edges externally if needed.
			 *
			 * @param bool   $written Written.
			 * @param string $type    Type.
			 * @param mixed  $ref     Ref.
			 * @param array  $edges   Edges.
			 */
			return (bool) apply_filters( 'kayan_entity_write_virtual_relationships', false, $type, $ref, $edges );
		}

		$edges = $this->sanitize_edges( $edges );
		if ( 'post' === $storage['kind'] ) {
			update_post_meta( $storage['id'], self::META_RELS, $edges );
			return true;
		}
		update_term_meta( $storage['id'], self::TERM_META_RELS, $edges );
		return true;
	}

	/**
	 * @param array<int,array<string,mixed>> $edges Edges.
	 * @param array<string,mixed>            $edge  Edge.
	 * @return array<int,array<string,mixed>>
	 */
	private function upsert_edge( array $edges, array $edge ) {
		$found = false;
		foreach ( $edges as $i => $existing ) {
			if ( $existing['type'] === $edge['type'] && (string) $existing['ref'] === (string) $edge['ref'] && $existing['rel'] === $edge['rel'] ) {
				$edges[ $i ] = $edge;
				$found       = true;
				break;
			}
		}
		if ( ! $found ) {
			$edges[] = $edge;
		}
		return $edges;
	}

	/**
	 * @param string $rel Rel.
	 * @return bool
	 */
	private function is_bidirectional_rel( $rel ) {
		return in_array( $rel, array( 'related', 'serves', 'located_in', 'contains', 'in_category' ), true );
	}

	/**
	 * @param string $rel Rel.
	 * @return string
	 */
	private function inverse_rel( $rel ) {
		$map = array(
			'contains'   => 'located_in',
			'located_in' => 'contains',
			'serves'     => 'served_by',
			'served_by'  => 'serves',
			'in_category'=> 'has_item',
			'has_item'   => 'in_category',
		);
		return isset( $map[ $rel ] ) ? $map[ $rel ] : 'related';
	}
}
