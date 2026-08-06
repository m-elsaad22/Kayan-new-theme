<?php
/**
 * KAYAN Query Engine — centralized data access layer.
 *
 * Future modules must use this instead of WP_Query / get_posts / get_post /
 * get_post_meta / get_terms / get_users directly.
 *
 * Cache-ready via Cache Engine. Entity-aware resource map for current + future types.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Query_Engine {

	/** @var Kayan_Cache_Engine|null */
	private $cache;

	/** @var Kayan_Logger|null */
	private $logger;

	/** @var Kayan_Country_Engine|null */
	private $countries;

	/** @var array<string,array<string,mixed>> */
	private $resources = array();

	/** @var int Default cache TTL for list queries. */
	private $default_ttl = 300;

	/**
	 * @param Kayan_Cache_Engine|null  $cache     Cache.
	 * @param Kayan_Logger|null        $logger    Logger.
	 * @param Kayan_Country_Engine|null $countries Countries.
	 */
	public function __construct( ?Kayan_Cache_Engine $cache = null, ?Kayan_Logger $logger = null, ?Kayan_Country_Engine $countries = null ) {
		$this->cache     = $cache;
		$this->logger    = $logger;
		$this->countries = $countries;
	}

	/**
	 * @param Kayan_Cache_Engine $cache Cache.
	 * @return void
	 */
	public function set_cache( Kayan_Cache_Engine $cache ) {
		$this->cache = $cache;
	}

	/**
	 * @param Kayan_Logger $logger Logger.
	 * @return void
	 */
	public function set_logger( Kayan_Logger $logger ) {
		$this->logger = $logger;
	}

	/**
	 * @return void
	 */
	public function register() {
		$this->register_core_resources();

		/**
		 * @param Kayan_Query_Engine $query Query engine.
		 */
		do_action( 'kayan_query_register_resources', $this );

		/**
		 * @param Kayan_Query_Engine $query Query.
		 */
		do_action( 'kayan_query_engine_registered', $this );
	}

	/**
	 * Register a queryable resource.
	 *
	 * @param string              $id   Resource id (service, city, …).
	 * @param array<string,mixed> $args Args.
	 * @return void
	 */
	public function register_resource( $id, array $args ) {
		$id = sanitize_key( $id );
		if ( ! $id ) {
			return;
		}
		$defaults = array(
			'label'     => $id,
			'kind'      => 'post', // post|term|country|user|hybrid
			'post_type' => '',
			'taxonomy'  => '',
			'cache_ttl' => $this->default_ttl,
			'enabled'   => true,
		);
		$this->resources[ $id ] = array_merge( $defaults, $args, array( 'id' => $id ) );
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function resources() {
		/**
		 * @param array $resources Resources.
		 */
		return apply_filters( 'kayan_query_resources', $this->resources );
	}

	/**
	 * @param string $id Resource.
	 * @return array<string,mixed>|null
	 */
	public function get_resource( $id ) {
		$id        = sanitize_key( $id );
		$resources = $this->resources();
		return isset( $resources[ $id ] ) ? $resources[ $id ] : null;
	}

	/**
	 * Query a resource (list).
	 *
	 * @param string              $resource Resource id.
	 * @param array<string,mixed> $args     Query args.
	 * @return array{items:array,total:int,resource:string,cached:bool}
	 */
	public function query( $resource, array $args = array() ) {
		$def = $this->get_resource( $resource );
		if ( ! $def || empty( $def['enabled'] ) ) {
			return array(
				'items'    => array(),
				'total'    => 0,
				'resource' => sanitize_key( $resource ),
				'cached'   => false,
				'errors'   => array( 'resource_not_found' ),
			);
		}

		$use_cache = ! isset( $args['cache'] ) || false !== $args['cache'];
		$ttl       = isset( $args['cache_ttl'] ) ? absint( $args['cache_ttl'] ) : (int) $def['cache_ttl'];
		unset( $args['cache'], $args['cache_ttl'] );

		$cache_key = 'q:' . $resource . ':' . md5( wp_json_encode( $args ) );
		if ( $use_cache && $this->cache ) {
			$cached = $this->cache->get( $cache_key, 'query' );
			if ( is_array( $cached ) && isset( $cached['items'] ) ) {
				$cached['cached'] = true;
				return $cached;
			}
		}

		$started = microtime( true );
		$result  = $this->execute_query( $def, $args );
		$result['cached']   = false;
		$result['resource'] = $def['id'];

		if ( $use_cache && $this->cache ) {
			$this->cache->set( $cache_key, $result, $ttl ? $ttl : $this->default_ttl, 'query' );
		}

		if ( $this->logger ) {
			$this->logger->performance(
				'query.resource',
				array(
					'resource'    => $def['id'],
					'duration_ms' => round( ( microtime( true ) - $started ) * 1000, 2 ),
					'total'       => isset( $result['total'] ) ? $result['total'] : 0,
				)
			);
		}

		return $result;
	}

	/**
	 * Get one item by id/slug.
	 *
	 * @param string     $resource Resource.
	 * @param string|int $ref      ID or slug.
	 * @param array      $args     Args.
	 * @return array<string,mixed>|null
	 */
	public function get( $resource, $ref, array $args = array() ) {
		$def = $this->get_resource( $resource );
		if ( ! $def ) {
			return null;
		}

		$use_cache = ! isset( $args['cache'] ) || false !== $args['cache'];
		$cache_key = 'g:' . $resource . ':' . md5( (string) $ref );
		if ( $use_cache && $this->cache ) {
			$cached = $this->cache->get( $cache_key, 'query' );
			if ( is_array( $cached ) ) {
				return $cached;
			}
		}

		$item = $this->execute_get( $def, $ref, $args );
		if ( $item && $use_cache && $this->cache ) {
			$ttl = isset( $def['cache_ttl'] ) ? (int) $def['cache_ttl'] : $this->default_ttl;
			$this->cache->set( $cache_key, $item, $ttl, 'query' );
		}
		return $item;
	}

	/**
	 * Meta accessor — modules should not call get_post_meta() directly.
	 *
	 * @param int          $post_id Post ID.
	 * @param string       $key     Meta key.
	 * @param bool         $single  Single.
	 * @param array        $args    Args: cache.
	 * @return mixed
	 */
	public function meta( $post_id, $key, $single = true, array $args = array() ) {
		$post_id = absint( $post_id );
		$key     = (string) $key;
		if ( ! $post_id || '' === $key ) {
			return $single ? '' : array();
		}

		$use_cache = ! isset( $args['cache'] ) || false !== $args['cache'];
		$cache_key = 'm:' . $post_id . ':' . $key . ':' . ( $single ? '1' : '0' );
		if ( $use_cache && $this->cache ) {
			$cached = $this->cache->get( $cache_key, 'query_meta' );
			if ( null !== $cached ) {
				return $cached;
			}
		}

		$value = get_post_meta( $post_id, $key, (bool) $single );
		if ( $use_cache && $this->cache ) {
			$this->cache->set( $cache_key, $value, 120, 'query_meta' );
		}
		return $value;
	}

	/**
	 * Term meta accessor.
	 *
	 * @param int    $term_id Term ID.
	 * @param string $key     Key.
	 * @param bool   $single  Single.
	 * @return mixed
	 */
	public function term_meta( $term_id, $key, $single = true ) {
		$term_id = absint( $term_id );
		if ( ! $term_id ) {
			return $single ? '' : array();
		}
		return get_term_meta( $term_id, $key, (bool) $single );
	}

	/**
	 * Terms query.
	 *
	 * @param string              $resource Resource (city, category, …) or taxonomy name.
	 * @param array<string,mixed> $args     Args.
	 * @return array{items:array,total:int}
	 */
	public function terms( $resource, array $args = array() ) {
		$def = $this->get_resource( $resource );
		if ( $def && 'term' === $def['kind'] ) {
			return $this->query( $resource, $args );
		}
		$taxonomy = $def && ! empty( $def['taxonomy'] ) ? $def['taxonomy'] : sanitize_key( $resource );
		$args['taxonomy'] = $taxonomy;
		$fake = array(
			'id'       => $taxonomy,
			'kind'     => 'term',
			'taxonomy' => $taxonomy,
			'cache_ttl'=> $this->default_ttl,
			'enabled'  => true,
		);
		return $this->execute_query( $fake, $args );
	}

	/**
	 * Users query.
	 *
	 * @param array<string,mixed> $args Args.
	 * @return array{items:array,total:int}
	 */
	public function users( array $args = array() ) {
		return $this->query( 'user', $args );
	}

	/**
	 * Convenience: services.
	 *
	 * @param array $args Args.
	 * @return array
	 */
	public function services( array $args = array() ) {
		return $this->query( 'service', $args );
	}

	public function articles( array $args = array() ) {
		return $this->query( 'article', $args );
	}

	public function cities( array $args = array() ) {
		return $this->query( 'city', $args );
	}

	public function countries( array $args = array() ) {
		return $this->query( 'country', $args );
	}

	public function faqs( array $args = array() ) {
		return $this->query( 'faq', $args );
	}

	public function reviews( array $args = array() ) {
		return $this->query( 'review', $args );
	}

	public function portfolio( array $args = array() ) {
		return $this->query( 'portfolio', $args );
	}

	public function pricing( array $args = array() ) {
		return $this->query( 'pricing', $args );
	}

	public function programmatic_pages( array $args = array() ) {
		return $this->query( 'programmatic_page', $args );
	}

	/**
	 * Invalidate query caches (after writes in future phases).
	 *
	 * @param string|null $resource Optional resource group hint.
	 * @return void
	 */
	public function flush( $resource = null ) {
		if ( ! $this->cache ) {
			return;
		}
		$this->cache->flush_group( 'query' );
		$this->cache->flush_group( 'query_meta' );
		unset( $resource );
	}

	/**
	 * Low-level WP_Query escape hatch — still goes through engine (logged + optional cache).
	 * Prefer resource query() instead.
	 *
	 * @param array<string,mixed> $args WP_Query args.
	 * @return WP_Query
	 */
	public function wp_query( array $args ) {
		if ( $this->logger ) {
			$this->logger->debug( 'general', 'query.wp_query', array( 'args' => array_keys( $args ) ) );
		}
		return new WP_Query( $args );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'resources'   => array_keys( $this->resources() ),
			'cache_ready' => (bool) $this->cache,
			'default_ttl' => $this->default_ttl,
			'apis'        => array(
				'query'   => 'kayan_query()->query( $resource, $args )',
				'get'     => 'kayan_query()->get( $resource, $ref )',
				'meta'    => 'kayan_query()->meta( $post_id, $key )',
				'terms'   => 'kayan_query()->terms( $resource, $args )',
				'users'   => 'kayan_query()->users( $args )',
				'services'=> 'kayan_query()->services()',
				'flush'   => 'kayan_query()->flush()',
			),
			'replaces'    => array( 'WP_Query', 'get_posts', 'get_post', 'get_post_meta', 'get_terms', 'get_users' ),
			'note'        => 'Application modules must not call WP query APIs directly.',
		);
	}

	/**
	 * @return void
	 */
	private function register_core_resources() {
		$this->register_resource(
			'service',
			array(
				'label'     => 'Services',
				'kind'      => 'post',
				'post_type' => 'services',
			)
		);
		$this->register_resource(
			'article',
			array(
				'label'     => 'Articles',
				'kind'      => 'post',
				'post_type' => 'post',
			)
		);
		$this->register_resource(
			'city',
			array(
				'label'    => 'Cities',
				'kind'     => 'term',
				'taxonomy' => 'cities',
			)
		);
		$this->register_resource(
			'country',
			array(
				'label' => 'Countries',
				'kind'  => 'country',
			)
		);
		$this->register_resource(
			'area',
			array(
				'label'   => 'Areas',
				'kind'    => 'term',
				'taxonomy'=> '', // reserved
				'enabled' => false,
			)
		);
		$this->register_resource(
			'district',
			array(
				'label'   => 'Districts',
				'kind'    => 'term',
				'enabled' => false,
			)
		);
		$this->register_resource(
			'faq',
			array(
				'label'     => 'FAQ',
				'kind'      => 'post',
				'post_type' => 'faqs',
			)
		);
		$this->register_resource(
			'review',
			array(
				'label'     => 'Reviews',
				'kind'      => 'post',
				'post_type' => 'reviews',
			)
		);
		$this->register_resource(
			'portfolio',
			array(
				'label'     => 'Portfolio',
				'kind'      => 'post',
				'post_type' => 'portfolio',
			)
		);
		$this->register_resource(
			'pricing',
			array(
				'label'     => 'Pricing',
				'kind'      => 'post',
				'post_type' => 'pricing',
			)
		);
		$this->register_resource(
			'programmatic_page',
			array(
				'label'     => 'Programmatic Pages',
				'kind'      => 'post',
				'post_type' => 'kayan_pseo',
			)
		);
		$this->register_resource(
			'category',
			array(
				'label'    => 'Categories',
				'kind'     => 'term',
				'taxonomy' => 'service_categories',
			)
		);
		$this->register_resource(
			'user',
			array(
				'label' => 'Users',
				'kind'  => 'user',
			)
		);
		$this->register_resource(
			'page',
			array(
				'label'     => 'Pages',
				'kind'      => 'post',
				'post_type' => 'page',
			)
		);
		$this->register_resource(
			'before_after',
			array(
				'label'     => 'Before/After',
				'kind'      => 'post',
				'post_type' => 'before_after',
			)
		);
	}

	/**
	 * @param array               $def  Resource def.
	 * @param array<string,mixed> $args Args.
	 * @return array{items:array,total:int}
	 */
	private function execute_query( array $def, array $args ) {
		$kind = isset( $def['kind'] ) ? $def['kind'] : 'post';

		if ( 'country' === $kind ) {
			return $this->query_countries( $args );
		}
		if ( 'term' === $kind ) {
			return $this->query_terms( $def, $args );
		}
		if ( 'user' === $kind ) {
			return $this->query_users( $args );
		}
		return $this->query_posts( $def, $args );
	}

	/**
	 * @param array               $def  Def.
	 * @param string|int          $ref  Ref.
	 * @param array<string,mixed> $args Args.
	 * @return array<string,mixed>|null
	 */
	private function execute_get( array $def, $ref, array $args ) {
		unset( $args );
		$kind = isset( $def['kind'] ) ? $def['kind'] : 'post';

		if ( 'country' === $kind ) {
			$result = $this->query_countries( array( 'code' => (string) $ref ) );
			return ! empty( $result['items'][0] ) ? $result['items'][0] : null;
		}

		if ( 'term' === $kind ) {
			$taxonomy = ! empty( $def['taxonomy'] ) ? $def['taxonomy'] : '';
			if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
				return null;
			}
			$term = is_numeric( $ref ) ? get_term( (int) $ref, $taxonomy ) : get_term_by( 'slug', sanitize_title( (string) $ref ), $taxonomy );
			if ( ! $term || is_wp_error( $term ) ) {
				return null;
			}
			return $this->normalize_term( $term );
		}

		if ( 'user' === $kind ) {
			$user = is_numeric( $ref ) ? get_user_by( 'id', (int) $ref ) : get_user_by( 'login', (string) $ref );
			return $user ? $this->normalize_user( $user ) : null;
		}

		$post_type = ! empty( $def['post_type'] ) ? $def['post_type'] : 'post';
		$post      = null;
		if ( is_numeric( $ref ) ) {
			$post = get_post( (int) $ref );
		} else {
			$posts = get_posts(
				array(
					'name'           => sanitize_title( (string) $ref ),
					'post_type'      => $post_type,
					'posts_per_page' => 1,
					'post_status'    => array( 'publish', 'draft', 'private', 'future', 'pending' ),
				)
			);
			$post = ! empty( $posts[0] ) ? $posts[0] : null;
		}
		if ( ! $post || ( $post_type && $post->post_type !== $post_type ) ) {
			return null;
		}
		return $this->normalize_post( $post );
	}

	/**
	 * @param array               $def  Def.
	 * @param array<string,mixed> $args Args.
	 * @return array{items:array,total:int}
	 */
	private function query_posts( array $def, array $args ) {
		$post_type = ! empty( $def['post_type'] ) ? $def['post_type'] : 'post';
		if ( $post_type && ! post_type_exists( $post_type ) ) {
			return array(
				'items' => array(),
				'total' => 0,
			);
		}

		$query_args = array(
			'post_type'              => $post_type,
			'post_status'            => isset( $args['status'] ) ? $args['status'] : ( isset( $args['post_status'] ) ? $args['post_status'] : 'publish' ),
			'posts_per_page'         => isset( $args['number'] ) ? absint( $args['number'] ) : ( isset( $args['posts_per_page'] ) ? absint( $args['posts_per_page'] ) : 20 ),
			'paged'                  => isset( $args['paged'] ) ? absint( $args['paged'] ) : 1,
			'orderby'                => isset( $args['orderby'] ) ? $args['orderby'] : 'date',
			'order'                  => isset( $args['order'] ) ? $args['order'] : 'DESC',
			's'                      => isset( $args['search'] ) ? (string) $args['search'] : ( isset( $args['s'] ) ? (string) $args['s'] : '' ),
			'fields'                 => 'all',
			'no_found_rows'          => empty( $args['count_total'] ),
			'update_post_meta_cache' => ! empty( $args['update_meta_cache'] ),
			'update_post_term_cache' => ! empty( $args['update_term_cache'] ),
		);

		if ( ! empty( $args['ids'] ) && is_array( $args['ids'] ) ) {
			$query_args['post__in'] = array_map( 'absint', $args['ids'] );
		}
		if ( ! empty( $args['meta_query'] ) ) {
			$query_args['meta_query'] = $args['meta_query'];
		}
		if ( ! empty( $args['tax_query'] ) ) {
			$query_args['tax_query'] = $args['tax_query'];
		}

		/**
		 * @param array $query_args WP args.
		 * @param array $def        Resource.
		 * @param array $args       Original.
		 */
		$query_args = apply_filters( 'kayan_query_posts_args', $query_args, $def, $args );

		$q     = new WP_Query( $query_args );
		$items = array();
		foreach ( $q->posts as $post ) {
			$items[] = $this->normalize_post( $post );
		}

		return array(
			'items' => $items,
			'total' => ! empty( $args['count_total'] ) ? (int) $q->found_posts : count( $items ),
		);
	}

	/**
	 * @param array               $def  Def.
	 * @param array<string,mixed> $args Args.
	 * @return array{items:array,total:int}
	 */
	private function query_terms( array $def, array $args ) {
		$taxonomy = ! empty( $def['taxonomy'] ) ? $def['taxonomy'] : ( isset( $args['taxonomy'] ) ? $args['taxonomy'] : '' );
		if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			return array(
				'items' => array(),
				'total' => 0,
			);
		}

		$term_args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => ! empty( $args['hide_empty'] ),
			'number'     => isset( $args['number'] ) ? absint( $args['number'] ) : 0,
			'search'     => isset( $args['search'] ) ? (string) $args['search'] : '',
			'parent'     => isset( $args['parent'] ) ? absint( $args['parent'] ) : '',
			'orderby'    => isset( $args['orderby'] ) ? $args['orderby'] : 'name',
			'order'      => isset( $args['order'] ) ? $args['order'] : 'ASC',
		);
		if ( '' === $term_args['parent'] ) {
			unset( $term_args['parent'] );
		}

		$terms = get_terms( $term_args );
		if ( is_wp_error( $terms ) ) {
			return array(
				'items' => array(),
				'total' => 0,
			);
		}
		$items = array();
		foreach ( $terms as $term ) {
			$items[] = $this->normalize_term( $term );
		}
		return array(
			'items' => $items,
			'total' => count( $items ),
		);
	}

	/**
	 * @param array<string,mixed> $args Args.
	 * @return array{items:array,total:int}
	 */
	private function query_countries( array $args ) {
		$items = array();
		if ( ! $this->countries ) {
			return array(
				'items' => array(),
				'total' => 0,
			);
		}
		$code_filter = isset( $args['code'] ) ? $this->countries->normalize( $args['code'] ) : '';
		foreach ( $this->countries->all() as $code => $row ) {
			$code = $this->countries->normalize( $code );
			if ( $code_filter && $code !== $code_filter ) {
				continue;
			}
			$name = is_array( $row ) ? (string) ( $row['name'] ?? $row['label'] ?? $code ) : (string) $row;
			$items[] = array(
				'id'     => 0,
				'ref'    => $code,
				'kind'   => 'country',
				'slug'   => $code,
				'name'   => $name,
				'status' => 'publish',
				'raw'    => is_array( $row ) ? $row : array( 'label' => $name ),
			);
		}
		return array(
			'items' => $items,
			'total' => count( $items ),
		);
	}

	/**
	 * @param array<string,mixed> $args Args.
	 * @return array{items:array,total:int}
	 */
	private function query_users( array $args ) {
		$user_args = array(
			'number'  => isset( $args['number'] ) ? absint( $args['number'] ) : 20,
			'orderby' => isset( $args['orderby'] ) ? $args['orderby'] : 'login',
			'order'   => isset( $args['order'] ) ? $args['order'] : 'ASC',
			'role'    => isset( $args['role'] ) ? $args['role'] : '',
			'search'  => isset( $args['search'] ) ? '*' . (string) $args['search'] . '*' : '',
			'fields'  => 'all',
		);
		if ( '' === $user_args['role'] ) {
			unset( $user_args['role'] );
		}
		if ( '' === $user_args['search'] ) {
			unset( $user_args['search'] );
		}
		$users = get_users( $user_args );
		$items = array();
		foreach ( $users as $user ) {
			$items[] = $this->normalize_user( $user );
		}
		return array(
			'items' => $items,
			'total' => count( $items ),
		);
	}

	/**
	 * @param WP_Post $post Post.
	 * @return array<string,mixed>
	 */
	private function normalize_post( $post ) {
		return array(
			'id'      => (int) $post->ID,
			'ref'     => (string) $post->ID,
			'kind'    => 'post',
			'slug'    => (string) $post->post_name,
			'name'    => (string) get_the_title( $post ),
			'status'  => (string) $post->post_status,
			'type'    => (string) $post->post_type,
			'excerpt' => (string) $post->post_excerpt,
			'url'     => get_permalink( $post ),
		);
	}

	/**
	 * @param WP_Term $term Term.
	 * @return array<string,mixed>
	 */
	private function normalize_term( $term ) {
		return array(
			'id'          => (int) $term->term_id,
			'ref'         => (string) $term->term_id,
			'kind'        => 'term',
			'slug'        => (string) $term->slug,
			'name'        => (string) $term->name,
			'taxonomy'    => (string) $term->taxonomy,
			'parent'      => (int) $term->parent,
			'description' => (string) $term->description,
		);
	}

	/**
	 * @param WP_User $user User.
	 * @return array<string,mixed>
	 */
	private function normalize_user( $user ) {
		return array(
			'id'           => (int) $user->ID,
			'ref'          => (string) $user->ID,
			'kind'         => 'user',
			'slug'         => (string) $user->user_login,
			'name'         => (string) $user->display_name,
			'email'        => (string) $user->user_email,
			'roles'        => array_values( (array) $user->roles ),
		);
	}
}
