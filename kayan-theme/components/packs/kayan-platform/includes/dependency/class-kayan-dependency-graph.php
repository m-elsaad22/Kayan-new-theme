<?php
/**
 * KAYAN Dependency Graph — tracks which generated pages depend on which
 * source entities (service/city/faq/pricing/portfolio/review/article/…)
 * so a change automatically flags only the affected pages as
 * "Needs Regeneration" — never a full-site regeneration sweep.
 *
 * Backed by the `kayan_pseo_dependencies` table (Migration Engine).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Dependency_Graph {

	const TABLE = 'kayan_pseo_dependencies';

	/** @var Kayan_Content_Workflow|null */
	private $workflow;

	/** @var Kayan_Programmatic_SEO|null */
	private $entities;

	/** @var Kayan_Logger|null */
	private $logger;

	public function __construct( ?Kayan_Logger $logger = null ) {
		$this->logger = $logger;
	}

	/**
	 * @param Kayan_Content_Workflow $workflow Workflow.
	 * @param Kayan_Programmatic_SEO $entities Entity registry.
	 * @return void
	 */
	public function set_dependencies( Kayan_Content_Workflow $workflow, Kayan_Programmatic_SEO $entities ) {
		$this->workflow = $workflow;
		$this->entities = $entities;
	}

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'save_post', array( $this, 'on_post_saved' ), 20, 2 );
		add_action( 'edited_term', array( $this, 'on_term_saved' ), 20, 3 );
	}

	/**
	 * Record (replace) the dependency rows for a generated page.
	 *
	 * @param int                  $post_id  Post ID.
	 * @param array<string,string> $entities Entity type => ref.
	 * @return void
	 */
	public function record( $post_id, array $entities ) {
		global $wpdb;
		$post_id = (int) $post_id;
		$table   = $this->table();

		$wpdb->delete( $table, array( 'post_id' => $post_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

		foreach ( $entities as $type => $ref ) {
			$type = sanitize_key( (string) $type );
			$ref  = sanitize_title( (string) $ref );
			if ( ! $type || ! $ref ) {
				continue;
			}
			$wpdb->insert( $table, array( 'post_id' => $post_id, 'entity_type' => $type, 'entity_ref' => $ref ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		}
	}

	/**
	 * @param string $entity_type Entity type.
	 * @param string $entity_ref  Entity ref (slug).
	 * @return int[]
	 */
	public function find_affected( $entity_type, $entity_ref ) {
		global $wpdb;
		$table = $this->table();
		$rows  = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT post_id FROM {$table} WHERE entity_type = %s AND entity_ref = %s", // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				sanitize_key( $entity_type ),
				sanitize_title( $entity_ref )
			),
			ARRAY_A
		);
		return array_map( static function ( $r ) { return (int) $r['post_id']; }, (array) $rows );
	}

	/**
	 * Flag every page depending on an entity as Needs Regeneration.
	 *
	 * @param string $entity_type    Entity type.
	 * @param string $entity_ref     Entity ref.
	 * @param int    $exclude_post_id Skip this post (e.g. the page that just triggered the change).
	 * @return int Number of pages flagged.
	 */
	public function mark_affected( $entity_type, $entity_ref, $exclude_post_id = 0 ) {
		if ( ! $this->workflow ) {
			return 0;
		}
		$affected = $this->find_affected( $entity_type, $entity_ref );
		$flagged  = 0;

		foreach ( $affected as $post_id ) {
			if ( $post_id === (int) $exclude_post_id ) {
				continue;
			}
			$current = $this->workflow->get_state( $post_id );
			if ( in_array( $current, array( Kayan_Content_Workflow::NEEDS_REGENERATION, Kayan_Content_Workflow::ARCHIVED, Kayan_Content_Workflow::FAILED ), true ) ) {
				continue; // already flagged, or not in an active lifecycle.
			}
			$result = $this->workflow->transition( $post_id, Kayan_Content_Workflow::NEEDS_REGENERATION, array( 'force' => true, 'note' => 'dependency:' . $entity_type . ':' . $entity_ref ) );
			if ( ! empty( $result['ok'] ) ) {
				++$flagged;
			}
		}

		if ( $flagged && $this->logger ) {
			$this->logger->log( 'general', 'dependency.marked_affected', array( 'entity_type' => $entity_type, 'entity_ref' => $entity_ref, 'count' => $flagged ) );
		}

		return $flagged;
	}

	/**
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post.
	 * @return void
	 */
	public function on_post_saved( $post_id, $post ) {
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! $this->entities ) {
			return;
		}
		$type = $this->entity_type_for_post_type( $post->post_type );
		if ( ! $type || empty( $post->post_name ) ) {
			return;
		}
		$this->mark_affected( $type, $post->post_name, (int) $post_id );
	}

	/**
	 * @param int    $term_id  Term ID.
	 * @param int    $tt_id    Term taxonomy id.
	 * @param string $taxonomy Taxonomy.
	 * @return void
	 */
	public function on_term_saved( $term_id, $tt_id, $taxonomy ) {
		unset( $tt_id );
		if ( ! $this->entities ) {
			return;
		}
		$type = $this->entity_type_for_taxonomy( $taxonomy );
		$term = get_term( $term_id, $taxonomy );
		if ( ! $type || ! $term || is_wp_error( $term ) ) {
			return;
		}
		$this->mark_affected( $type, $term->slug );
	}

	/**
	 * @param string $post_type Post type.
	 * @return string
	 */
	private function entity_type_for_post_type( $post_type ) {
		foreach ( $this->entities->get_entity_types() as $id => $def ) {
			if ( ! empty( $def['post_type'] ) && $def['post_type'] === $post_type ) {
				return $id;
			}
		}
		if ( 'post' === $post_type ) {
			return 'article';
		}
		return '';
	}

	/**
	 * @param string $taxonomy Taxonomy.
	 * @return string
	 */
	private function entity_type_for_taxonomy( $taxonomy ) {
		foreach ( $this->entities->get_entity_types() as $id => $def ) {
			if ( ! empty( $def['taxonomy'] ) && $def['taxonomy'] === $taxonomy ) {
				return $id;
			}
		}
		return '';
	}

	/**
	 * @return string
	 */
	private function table() {
		global $wpdb;
		return $wpdb->prefix . self::TABLE;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'table' => $this->table(),
			'apis'  => array(
				'record'        => 'kayan_dependencies()->record( $post_id, $entities )',
				'find_affected' => 'kayan_dependencies()->find_affected( $type, $ref )',
				'mark_affected' => 'kayan_dependencies()->mark_affected( $type, $ref )',
			),
		);
	}
}
