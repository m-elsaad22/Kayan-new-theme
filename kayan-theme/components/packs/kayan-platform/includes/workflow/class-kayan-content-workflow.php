<?php
/**
 * KAYAN Content Workflow — explicit lifecycle for every generated page.
 *
 * Every post the Generator creates gets a `kayan_workflow_state` meta value
 * (draft by default). Publishing/scheduling is gated by the Quality Engine
 * unless explicitly forced. State transitions are validated against a
 * fixed map and recorded in a history log — never a silent status change.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Content_Workflow {

	const META_STATE   = 'kayan_workflow_state';
	const META_HISTORY = 'kayan_workflow_history';

	const DRAFT               = 'draft';
	const AI_DRAFT             = 'ai_draft';
	const HUMAN_REVIEW         = 'human_review';
	const APPROVED             = 'approved';
	const SCHEDULED            = 'scheduled';
	const PUBLISHED            = 'published';
	const NEEDS_UPDATE         = 'needs_update';
	const NEEDS_REGENERATION   = 'needs_regeneration';
	const ARCHIVED             = 'archived';
	const FAILED               = 'failed';

	/** @var Kayan_PSEO_Storage|null */
	private $storage;

	/** @var Kayan_Quality_Engine|null */
	private $quality;

	/** @var Kayan_Logger|null */
	private $logger;

	public function __construct( ?Kayan_Logger $logger = null ) {
		$this->logger = $logger;
	}

	/**
	 * Wired after the PSEO Storage + Quality Engine exist.
	 *
	 * @param Kayan_PSEO_Storage    $storage Storage.
	 * @param Kayan_Quality_Engine  $quality Quality engine.
	 * @return void
	 */
	public function set_dependencies( Kayan_PSEO_Storage $storage, Kayan_Quality_Engine $quality ) {
		$this->storage = $storage;
		$this->quality = $quality;
	}

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_meta' ), 23 );
	}

	/**
	 * @return void
	 */
	public function register_meta() {
		if ( ! $this->storage ) {
			return;
		}
		foreach ( $this->storage->host_post_types() as $post_type ) {
			register_post_meta(
				$post_type,
				self::META_STATE,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'default'           => self::DRAFT,
					'sanitize_callback' => array( $this, 'sanitize_state' ),
					'auth_callback'     => static function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
			register_post_meta(
				$post_type,
				self::META_HISTORY,
				array(
					'type'          => 'array',
					'single'        => true,
					'show_in_rest'  => false,
					'auth_callback' => static function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}

	/**
	 * @return array<string,string>
	 */
	public function states() {
		return array(
			self::DRAFT               => __( 'Draft', 'kayan' ),
			self::AI_DRAFT            => __( 'AI Draft', 'kayan' ),
			self::HUMAN_REVIEW        => __( 'Human Review', 'kayan' ),
			self::APPROVED            => __( 'Approved', 'kayan' ),
			self::SCHEDULED           => __( 'Scheduled', 'kayan' ),
			self::PUBLISHED           => __( 'Published', 'kayan' ),
			self::NEEDS_UPDATE        => __( 'Needs Update', 'kayan' ),
			self::NEEDS_REGENERATION  => __( 'Needs Regeneration', 'kayan' ),
			self::ARCHIVED            => __( 'Archived', 'kayan' ),
			self::FAILED              => __( 'Failed', 'kayan' ),
		);
	}

	/**
	 * Allowed transition map. Extend via `kayan_workflow_transitions` — never
	 * hardcode a second copy elsewhere.
	 *
	 * @return array<string,string[]>
	 */
	public function transition_map() {
		$map = array(
			self::DRAFT              => array( self::AI_DRAFT, self::HUMAN_REVIEW, self::APPROVED, self::PUBLISHED, self::FAILED, self::ARCHIVED, self::NEEDS_REGENERATION ),
			self::AI_DRAFT           => array( self::HUMAN_REVIEW, self::DRAFT, self::APPROVED, self::FAILED, self::ARCHIVED, self::NEEDS_REGENERATION ),
			self::HUMAN_REVIEW       => array( self::APPROVED, self::DRAFT, self::AI_DRAFT, self::NEEDS_UPDATE, self::ARCHIVED, self::NEEDS_REGENERATION, self::PUBLISHED ),
			self::APPROVED           => array( self::SCHEDULED, self::PUBLISHED, self::HUMAN_REVIEW, self::ARCHIVED, self::NEEDS_REGENERATION ),
			self::SCHEDULED          => array( self::PUBLISHED, self::APPROVED, self::ARCHIVED, self::FAILED, self::NEEDS_REGENERATION ),
			self::PUBLISHED          => array( self::NEEDS_UPDATE, self::NEEDS_REGENERATION, self::ARCHIVED ),
			self::NEEDS_UPDATE       => array( self::HUMAN_REVIEW, self::DRAFT, self::APPROVED, self::PUBLISHED, self::ARCHIVED, self::NEEDS_REGENERATION ),
			self::NEEDS_REGENERATION => array( self::DRAFT, self::AI_DRAFT, self::HUMAN_REVIEW, self::APPROVED, self::PUBLISHED, self::ARCHIVED ),
			self::ARCHIVED           => array( self::DRAFT, self::HUMAN_REVIEW ),
			self::FAILED             => array( self::DRAFT, self::AI_DRAFT, self::HUMAN_REVIEW ),
		);

		/**
		 * @param array $map Transition map.
		 */
		return apply_filters( 'kayan_workflow_transitions', $map );
	}

	/**
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public function get_state( $post_id ) {
		$state = get_post_meta( (int) $post_id, self::META_STATE, true );
		return $state && isset( $this->states()[ $state ] ) ? $state : self::DRAFT;
	}

	/**
	 * @param string $from From state.
	 * @param string $to   To state.
	 * @return bool
	 */
	public function can_transition( $from, $to ) {
		$map = $this->transition_map();
		return isset( $map[ $from ] ) && in_array( $to, $map[ $from ], true );
	}

	/**
	 * @param int                  $post_id Post ID.
	 * @param string               $to      Target state.
	 * @param array<string,mixed>  $context force, note, by, schedule_at.
	 * @return array{ok:bool,state?:string,errors?:string[],quality?:array}
	 */
	public function transition( $post_id, $to, array $context = array() ) {
		$post_id = (int) $post_id;
		$to      = $this->sanitize_state( $to );
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return array( 'ok' => false, 'errors' => array( 'post_not_found' ) );
		}

		$from  = $this->get_state( $post_id );
		$force = ! empty( $context['force'] );

		if ( $from === $to && ! $force ) {
			return array( 'ok' => true, 'state' => $to );
		}
		if ( ! $force && ! $this->can_transition( $from, $to ) ) {
			return array( 'ok' => false, 'errors' => array( 'invalid_transition' ) );
		}

		// Publishing/scheduling is quality-gated unless explicitly forced.
		if ( in_array( $to, array( self::PUBLISHED, self::SCHEDULED ), true ) && ! $force && $this->quality ) {
			$result = $this->quality->validate( $post_id );
			if ( empty( $result['ok'] ) ) {
				return array( 'ok' => false, 'errors' => array( 'quality_check_failed' ), 'quality' => $result );
			}
		}

		update_post_meta( $post_id, self::META_STATE, $to );
		$this->append_history( $post_id, $from, $to, $context );
		$this->sync_post_status( $post_id, $to, $context );

		if ( $this->logger ) {
			$this->logger->log( 'general', 'workflow.transitioned', array( 'post_id' => $post_id, 'from' => $from, 'to' => $to ) );
		}

		/**
		 * @param int    $post_id Post ID.
		 * @param string $from    From.
		 * @param string $to      To.
		 * @param array  $context Context.
		 */
		do_action( 'kayan_workflow_transitioned', $post_id, $from, $to, $context );

		return array( 'ok' => true, 'state' => $to );
	}

	/**
	 * @param int    $post_id Post ID.
	 * @return array<int,array<string,mixed>>
	 */
	public function history( $post_id ) {
		$history = get_post_meta( (int) $post_id, self::META_HISTORY, true );
		return is_array( $history ) ? $history : array();
	}

	/**
	 * @param string $value Raw.
	 * @return string
	 */
	public function sanitize_state( $value ) {
		$value = sanitize_key( (string) $value );
		return isset( $this->states()[ $value ] ) ? $value : self::DRAFT;
	}

	/**
	 * @param int    $post_id Post ID.
	 * @param string $from    From.
	 * @param string $to      To.
	 * @param array  $context Context.
	 * @return void
	 */
	private function append_history( $post_id, $from, $to, array $context ) {
		$history   = $this->history( $post_id );
		$history[] = array(
			'from'  => $from,
			'to'    => $to,
			'at'    => gmdate( 'c' ),
			'by'    => get_current_user_id(),
			'note'  => isset( $context['note'] ) ? sanitize_text_field( (string) $context['note'] ) : '',
			'forced'=> ! empty( $context['force'] ),
		);
		if ( count( $history ) > 50 ) {
			$history = array_slice( $history, -50 );
		}
		update_post_meta( $post_id, self::META_HISTORY, $history );
	}

	/**
	 * @param int    $post_id Post ID.
	 * @param string $to      Target workflow state.
	 * @param array  $context Context (schedule_at for SCHEDULED).
	 * @return void
	 */
	private function sync_post_status( $post_id, $to, array $context ) {
		$map = array(
			self::DRAFT        => 'draft',
			self::AI_DRAFT      => 'draft',
			self::HUMAN_REVIEW  => 'draft',
			self::APPROVED      => 'draft',
			self::SCHEDULED     => 'future',
			self::PUBLISHED     => 'publish',
			self::ARCHIVED      => 'private',
		);
		if ( ! isset( $map[ $to ] ) ) {
			return; // needs_update / needs_regeneration / failed never change live status.
		}

		$args = array( 'ID' => $post_id, 'post_status' => $map[ $to ] );
		if ( self::SCHEDULED === $to && ! empty( $context['schedule_at'] ) ) {
			$ts = strtotime( $context['schedule_at'] . ' UTC' );
			if ( $ts && $ts > time() ) {
				$args['post_date']     = gmdate( 'Y-m-d H:i:s', $ts );
				$args['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $ts );
			}
		}
		wp_update_post( $args );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'states'    => $this->states(),
			'transitions' => $this->transition_map(),
			'apis'      => array(
				'get_state'  => 'kayan_workflow()->get_state( $post_id )',
				'transition' => 'kayan_workflow()->transition( $post_id, $to, $context )',
				'history'    => 'kayan_workflow()->history( $post_id )',
			),
		);
	}
}
