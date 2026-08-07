<?php
/**
 * Centralized Entity API — reusable accessors instead of isolated post meta.
 *
 * Resolves Country / City / Area / Service / FAQ / … into a normalized DTO.
 * Callers must use this API (or Entity Engine) rather than get_post_meta()
 * for entity fields. Architecture only — no generation, no admin UI.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Entity_API {

	const KIND_POST    = 'post';
	const KIND_TERM    = 'term';
	const KIND_COUNTRY = 'country';
	const KIND_VIRTUAL = 'virtual';

	/** @var Kayan_Programmatic_SEO */
	private $programmatic;

	/** @var Kayan_Country_Engine */
	private $countries;

	/** @var Kayan_Country_Settings */
	private $settings;

	/** @var Kayan_Content_Locale */
	private $locale;

	/** @var array<string,array<string,mixed>> Extra / overridden type defs. */
	private $extra_types = array();

	public function __construct(
		Kayan_Programmatic_SEO $programmatic,
		Kayan_Country_Engine $countries,
		Kayan_Country_Settings $settings,
		Kayan_Content_Locale $locale
	) {
		$this->programmatic = $programmatic;
		$this->countries    = $countries;
		$this->settings     = $settings;
		$this->locale       = $locale;
	}

	/**
	 * Register/override an entity type on the Entity API layer.
	 * Also mirrors into the Programmatic SEO registry when missing.
	 *
	 * @param string              $type Type id.
	 * @param array<string,mixed> $args Args.
	 * @return void
	 */
	public function register_type( $type, array $args ) {
		$type = sanitize_key( $type );
		if ( '' === $type ) {
			return;
		}

		$defaults = array(
			'label'       => $type,
			'kind'        => '', // post|term|country|virtual — auto from post_type/taxonomy
			'post_type'   => '',
			'taxonomy'    => '',
			'segment'     => $type,
			'enabled'     => true,
			'fields'      => array( 'name', 'slug', 'excerpt', 'content', 'featured_image' ),
			'combinators' => array(),
			'source'      => 'manual',
		);

		$def                       = array_merge( $defaults, $args, array( 'id' => $type ) );
		$def['kind']               = $this->infer_kind( $def );
		$this->extra_types[ $type ] = $def;

		$existing = $this->programmatic->get_entity_types();
		if ( ! isset( $existing[ $type ] ) ) {
			$this->programmatic->register_entity_type(
				$type,
				array(
					'label'       => $def['label'],
					'segment'     => $def['segment'],
					'enabled'     => $def['enabled'],
					'post_type'   => $def['post_type'],
					'taxonomy'    => $def['taxonomy'],
					'combinators' => $def['combinators'],
					'source'      => $def['source'],
				)
			);
		}
	}

	/**
	 * All entity types (programmatic + Entity API extras).
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function types() {
		$types = $this->programmatic->get_entity_types();
		foreach ( $this->extra_types as $id => $def ) {
			$base          = isset( $types[ $id ] ) ? $types[ $id ] : array();
			$types[ $id ]  = array_merge( $base, $def, array( 'id' => $id ) );
			$types[ $id ]['kind'] = $this->infer_kind( $types[ $id ] );
		}

		foreach ( $types as $id => $def ) {
			if ( empty( $types[ $id ]['kind'] ) ) {
				$types[ $id ]['kind'] = $this->infer_kind( $def );
			}
			if ( empty( $types[ $id ]['fields'] ) ) {
				$types[ $id ]['fields'] = array( 'name', 'slug', 'excerpt', 'content', 'featured_image' );
			}
		}

		/**
		 * @param array $types Types.
		 */
		return apply_filters( 'kayan_entity_types', $types );
	}

	/**
	 * @param string $type Type.
	 * @return array<string,mixed>|null
	 */
	public function get_type( $type ) {
		$type  = sanitize_key( $type );
		$types = $this->types();
		return isset( $types[ $type ] ) ? $types[ $type ] : null;
	}

	/**
	 * Resolve a single entity into a normalized DTO (or null).
	 *
	 * @param string     $type Entity type.
	 * @param string|int $ref  ID, slug, or country code.
	 * @return array<string,mixed>|null
	 */
	public function get( $type, $ref ) {
		$type = sanitize_key( $type );
		$def  = $this->get_type( $type );
		if ( ! $def ) {
			return null;
		}

		$kind = isset( $def['kind'] ) ? $def['kind'] : self::KIND_VIRTUAL;
		$dto  = null;

		switch ( $kind ) {
			case self::KIND_COUNTRY:
				$dto = $this->resolve_country( $ref );
				break;
			case self::KIND_TERM:
				$dto = $this->resolve_term( $type, $def, $ref );
				break;
			case self::KIND_POST:
				$dto = $this->resolve_post( $type, $def, $ref );
				break;
			case self::KIND_VIRTUAL:
			default:
				$dto = $this->resolve_virtual( $type, $def, $ref );
				break;
		}

		if ( ! $dto ) {
			return null;
		}

		/**
		 * @param array  $dto  DTO.
		 * @param string $type Type.
		 * @param mixed  $ref  Ref.
		 */
		return apply_filters( 'kayan_entity_dto', $dto, $type, $ref );
	}

	/**
	 * @param string          $type Type.
	 * @param array<int|string> $refs Refs.
	 * @return array<int,array<string,mixed>>
	 */
	public function get_many( $type, array $refs ) {
		$out = array();
		foreach ( $refs as $ref ) {
			$dto = $this->get( $type, $ref );
			if ( $dto ) {
				$out[] = $dto;
			}
		}
		return $out;
	}

	/**
	 * Query entities of a type (thin wrapper — no generation).
	 *
	 * @param string              $type Type.
	 * @param array<string,mixed> $args Query args.
	 * @return array<int,array<string,mixed>>
	 */
	public function query( $type, array $args = array() ) {
		$def = $this->get_type( $type );
		if ( ! $def ) {
			return array();
		}

		$kind  = isset( $def['kind'] ) ? $def['kind'] : self::KIND_VIRTUAL;
		$limit = isset( $args['number'] ) ? absint( $args['number'] ) : ( isset( $args['posts_per_page'] ) ? absint( $args['posts_per_page'] ) : 20 );
		$out   = array();

		if ( self::KIND_COUNTRY === $kind ) {
			foreach ( array_keys( $this->countries->all() ) as $code ) {
				$dto = $this->get( 'country', $code );
				if ( $dto ) {
					$out[] = $dto;
				}
			}
			return $out;
		}

		if ( self::KIND_TERM === $kind && ! empty( $def['taxonomy'] ) ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $def['taxonomy'],
					'hide_empty' => ! empty( $args['hide_empty'] ),
					'number'     => $limit,
					'search'     => isset( $args['search'] ) ? (string) $args['search'] : '',
				)
			);
			if ( is_wp_error( $terms ) ) {
				return array();
			}
			foreach ( $terms as $term ) {
				$dto = $this->get( $type, $term->term_id );
				if ( $dto ) {
					$out[] = $dto;
				}
			}
			return $out;
		}

		if ( self::KIND_POST === $kind && ! empty( $def['post_type'] ) && post_type_exists( $def['post_type'] ) ) {
			$posts = get_posts(
				array(
					'post_type'              => $def['post_type'],
					'post_status'            => isset( $args['post_status'] ) ? $args['post_status'] : 'publish',
					'posts_per_page'         => $limit ? $limit : 20,
					's'                      => isset( $args['search'] ) ? (string) $args['search'] : '',
					'fields'                 => 'ids',
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
				)
			);
			foreach ( $posts as $post_id ) {
				$dto = $this->get( $type, $post_id );
				if ( $dto ) {
					$out[] = $dto;
				}
			}
		}

		/**
		 * @param array  $out  Results.
		 * @param string $type Type.
		 * @param array  $args Args.
		 */
		return apply_filters( 'kayan_entity_query', $out, $type, $args );
	}

	/**
	 * @param string     $type Type.
	 * @param string|int $ref  Ref.
	 * @return bool
	 */
	public function exists( $type, $ref ) {
		return null !== $this->get( $type, $ref );
	}

	/**
	 * @param string     $type Type.
	 * @param string|int $ref  Ref.
	 * @return string
	 */
	public function get_name( $type, $ref ) {
		$dto = $this->get( $type, $ref );
		return $dto && isset( $dto['name'] ) ? (string) $dto['name'] : '';
	}

	/**
	 * @param string     $type Type.
	 * @param string|int $ref  Ref.
	 * @return string
	 */
	public function get_slug( $type, $ref ) {
		$dto = $this->get( $type, $ref );
		return $dto && isset( $dto['slug'] ) ? (string) $dto['slug'] : '';
	}

	/**
	 * Field accessor — never call get_post_meta from consumers for entity fields.
	 *
	 * @param string     $type  Type.
	 * @param string|int $ref   Ref.
	 * @param string     $field Field key.
	 * @param mixed      $default Default.
	 * @return mixed
	 */
	public function get_field( $type, $ref, $field, $default = '' ) {
		$dto = $this->get( $type, $ref );
		if ( ! $dto ) {
			return $default;
		}
		$field = sanitize_key( $field );
		if ( isset( $dto[ $field ] ) ) {
			return $dto[ $field ];
		}
		if ( isset( $dto['fields'][ $field ] ) ) {
			return $dto['fields'][ $field ];
		}
		if ( isset( $dto['media'][ $field ] ) ) {
			return $dto['media'][ $field ];
		}
		return $default;
	}

	/**
	 * @param string     $type Type.
	 * @param string|int $ref  Ref.
	 * @return array<string,mixed>
	 */
	public function get_fields( $type, $ref ) {
		$dto = $this->get( $type, $ref );
		return $dto && isset( $dto['fields'] ) && is_array( $dto['fields'] ) ? $dto['fields'] : array();
	}

	/**
	 * @param string     $type Type.
	 * @param string|int $ref  Ref.
	 * @return array<string,mixed>
	 */
	public function get_media( $type, $ref ) {
		$dto = $this->get( $type, $ref );
		return $dto && isset( $dto['media'] ) && is_array( $dto['media'] ) ? $dto['media'] : array();
	}

	/**
	 * @param string     $type Type.
	 * @param string|int $ref  Ref.
	 * @return array<string,mixed>
	 */
	public function get_locale( $type, $ref ) {
		$dto = $this->get( $type, $ref );
		return $dto && isset( $dto['locale'] ) && is_array( $dto['locale'] ) ? $dto['locale'] : array();
	}

	/**
	 * Country settings via Entity API (phone, whatsapp, …).
	 *
	 * @param string $country Country code.
	 * @param string $key     Setting key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	public function get_country_setting( $country, $key, $default = '' ) {
		return $this->settings->get( $key, $this->countries->normalize( $country ), $default );
	}

	/**
	 * Rank Math source field (read-only). Rank Math remains the SEO engine.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     rank_math_title|rank_math_description|…
	 * @return string
	 */
	public function get_rank_math_field( $post_id, $key ) {
		$post_id = absint( $post_id );
		$key     = sanitize_key( $key );
		if ( ! $post_id || ! $key ) {
			return '';
		}
		if ( 0 !== strpos( $key, 'rank_math_' ) ) {
			$key = 'rank_math_' . $key;
		}
		$value = get_post_meta( $post_id, $key, true );
		return is_string( $value ) ? $value : '';
	}

	/**
	 * Empty DTO contract.
	 *
	 * @param string $type Type.
	 * @return array<string,mixed>
	 */
	public function empty_dto( $type ) {
		return array(
			'type'   => sanitize_key( $type ),
			'ref'    => '',
			'id'     => 0,
			'kind'   => self::KIND_VIRTUAL,
			'slug'   => '',
			'name'   => '',
			'url'    => '',
			'fields' => array(),
			'media'  => array(
				'featured_image_id' => 0,
				'featured_image'    => '',
				'gallery'           => array(),
				'video'             => array(),
			),
			'locale' => array(
				'lang'      => '',
				'countries' => array(),
			),
			'seo'    => array(
				'title'       => '',
				'description' => '',
			),
			'raw'    => array(),
		);
	}

	/**
	 * @param array<string,mixed> $def Type def.
	 * @return string
	 */
	private function infer_kind( array $def ) {
		if ( ! empty( $def['kind'] ) ) {
			return sanitize_key( (string) $def['kind'] );
		}
		$id = isset( $def['id'] ) ? $def['id'] : '';
		if ( 'country' === $id ) {
			return self::KIND_COUNTRY;
		}
		if ( ! empty( $def['post_type'] ) ) {
			return self::KIND_POST;
		}
		if ( ! empty( $def['taxonomy'] ) ) {
			return self::KIND_TERM;
		}
		return self::KIND_VIRTUAL;
	}

	/**
	 * @param string|int $ref Ref.
	 * @return array<string,mixed>|null
	 */
	private function resolve_country( $ref ) {
		$code = $this->countries->normalize( (string) $ref );
		$all  = $this->countries->all();
		if ( ! $code || ! isset( $all[ $code ] ) ) {
			return null;
		}
		$row  = $all[ $code ];
		$name = '';
		if ( is_array( $row ) ) {
			$name = isset( $row['name'] ) ? (string) $row['name'] : ( isset( $row['label'] ) ? (string) $row['label'] : $code );
		} else {
			$name = (string) $row;
		}

		$dto                         = $this->empty_dto( 'country' );
		$dto['ref']                  = $code;
		$dto['id']                   = 0;
		$dto['kind']                 = self::KIND_COUNTRY;
		$dto['slug']                 = $code;
		$dto['name']                 = $name;
		$dto['fields']['phone']      = (string) $this->settings->get( 'phone', $code, '' );
		$dto['fields']['whatsapp']   = (string) $this->settings->get( 'whatsapp', $code, '' );
		$dto['fields']['currency']   = (string) $this->settings->get( 'currency', $code, '' );
		$dto['raw']['profile']       = $row;
		return $dto;
	}

	/**
	 * @param string              $type Type.
	 * @param array<string,mixed> $def  Def.
	 * @param string|int          $ref  Ref.
	 * @return array<string,mixed>|null
	 */
	private function resolve_term( $type, array $def, $ref ) {
		$taxonomy = isset( $def['taxonomy'] ) ? $def['taxonomy'] : '';
		if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			return null;
		}

		$term = null;
		if ( is_numeric( $ref ) ) {
			$term = get_term( (int) $ref, $taxonomy );
		} else {
			$term = get_term_by( 'slug', sanitize_title( (string) $ref ), $taxonomy );
			if ( ! $term ) {
				$term = get_term_by( 'name', (string) $ref, $taxonomy );
			}
		}
		if ( ! $term || is_wp_error( $term ) ) {
			return null;
		}

		$dto         = $this->empty_dto( $type );
		$dto['ref']  = (string) $term->term_id;
		$dto['id']   = (int) $term->term_id;
		$dto['kind'] = self::KIND_TERM;
		$dto['slug'] = (string) $term->slug;
		$dto['name'] = (string) $term->name;
		$dto['fields']['description'] = (string) $term->description;
		$dto['fields']['parent']      = (int) $term->parent;
		$dto['locale']['countries']   = array();
		$country                        = get_term_meta( $term->term_id, Kayan_Content_Locale::TERM_META_COUNTRY, true );
		if ( $country ) {
			$dto['locale']['countries'] = array( sanitize_key( (string) $country ) );
			$dto['fields']['country']   = sanitize_key( (string) $country );
		}
		$dto['raw']['term'] = array(
			'term_id'  => (int) $term->term_id,
			'taxonomy' => $taxonomy,
		);
		return $dto;
	}

	/**
	 * @param string              $type Type.
	 * @param array<string,mixed> $def  Def.
	 * @param string|int          $ref  Ref.
	 * @return array<string,mixed>|null
	 */
	private function resolve_post( $type, array $def, $ref ) {
		$post_type = isset( $def['post_type'] ) ? $def['post_type'] : '';
		if ( ! $post_type ) {
			return null;
		}

		$post = null;
		if ( is_numeric( $ref ) ) {
			$post = get_post( (int) $ref );
		} else {
			$posts = get_posts(
				array(
					'name'                   => sanitize_title( (string) $ref ),
					'post_type'              => $post_type,
					'post_status'            => array( 'publish', 'draft', 'private', 'future', 'pending' ),
					'posts_per_page'         => 1,
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
				)
			);
			$post = ! empty( $posts[0] ) ? $posts[0] : null;
		}

		if ( ! $post || $post_type !== $post->post_type ) {
			return null;
		}

		$thumb_id = (int) get_post_thumbnail_id( $post );
		$dto      = $this->empty_dto( $type );
		$dto['ref']  = (string) $post->ID;
		$dto['id']   = (int) $post->ID;
		$dto['kind'] = self::KIND_POST;
		$dto['slug'] = (string) $post->post_name;
		$dto['name'] = (string) get_the_title( $post );
		$dto['url']  = get_permalink( $post );
		$dto['fields']['excerpt'] = (string) $post->post_excerpt;
		$dto['fields']['content'] = (string) $post->post_content;
		$dto['fields']['status']  = (string) $post->post_status;
		$dto['media']['featured_image_id'] = $thumb_id;
		$dto['media']['featured_image']    = $thumb_id ? (string) wp_get_attachment_image_url( $thumb_id, 'full' ) : '';

		$dto['locale']['lang']      = (string) $this->locale->get_lang( $post->ID );
		$dto['locale']['countries'] = (array) $this->locale->get_countries( $post->ID );

		$rm_title = $this->get_rank_math_field( $post->ID, 'title' );
		$rm_desc  = $this->get_rank_math_field( $post->ID, 'description' );
		$dto['seo']['title']       = $rm_title;
		$dto['seo']['description'] = $rm_desc;

		// Common typed fields via controlled keys (API-owned, not scattered consumers).
		$dto['fields']['price_from'] = $this->read_post_field( $post->ID, array( 'price_from', 'kayan_price_from', '_price_from' ) );
		$dto['fields']['rating']     = $this->read_post_field( $post->ID, array( 'rating', 'kayan_rating', '_rating' ) );

		$dto['raw']['post_id'] = (int) $post->ID;
		return $dto;
	}

	/**
	 * Virtual / reserved entities (area, landmark, gallery, video, …) until storage exists.
	 *
	 * @param string              $type Type.
	 * @param array<string,mixed> $def  Def.
	 * @param string|int          $ref  Ref.
	 * @return array<string,mixed>|null
	 */
	private function resolve_virtual( $type, array $def, $ref ) {
		$ref = sanitize_title( (string) $ref );
		if ( '' === $ref ) {
			return null;
		}

		/**
		 * Allow packs to resolve virtual entity instances.
		 *
		 * @param array|null $dto  DTO or null.
		 * @param string     $type Type.
		 * @param string     $ref  Ref.
		 * @param array      $def  Def.
		 */
		$dto = apply_filters( 'kayan_entity_resolve_virtual', null, $type, $ref, $def );
		if ( is_array( $dto ) ) {
			return $dto;
		}

		// Architecture placeholder DTO so relationships/tags can reference future entities.
		$out         = $this->empty_dto( $type );
		$out['ref']  = $ref;
		$out['slug'] = $ref;
		$out['name'] = $ref;
		$out['kind'] = self::KIND_VIRTUAL;
		$out['raw']['placeholder'] = true;
		return $out;
	}

	/**
	 * @param int      $post_id Post ID.
	 * @param string[] $keys    Candidate meta keys.
	 * @return string
	 */
	private function read_post_field( $post_id, array $keys ) {
		foreach ( $keys as $key ) {
			$value = get_post_meta( $post_id, $key, true );
			if ( '' !== $value && null !== $value && false !== $value ) {
				return is_scalar( $value ) ? (string) $value : '';
			}
		}
		return '';
	}
}
