<?php
/**
 * KAYAN Admin Permissions — roles & capabilities for the Admin Platform.
 *
 * Architecture only. Registers capabilities and role maps; does not redesign WP Users UI.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Permissions {

	const CAP_ACCESS = 'kayan_access_admin';

	/** @var array<string,array<string,mixed>> */
	private $roles = array();

	/** @var array<string,array<string,mixed>> */
	private $capabilities = array();

	/** @var bool */
	private $roles_synced = false;

	/**
	 * @return void
	 */
	public function register() {
		$this->register_core_capabilities();
		$this->register_core_roles();

		add_action( 'init', array( $this, 'sync_roles' ), 30 );

		/**
		 * @param Kayan_Admin_Permissions $permissions Permissions.
		 */
		do_action( 'kayan_admin_register_permissions', $this );
	}

	/**
	 * @param string              $cap  Capability.
	 * @param array<string,mixed> $args Args.
	 * @return void
	 */
	public function register_capability( $cap, array $args = array() ) {
		$cap = sanitize_key( $cap );
		if ( ! $cap ) {
			return;
		}
		$defaults = array(
			'label'       => $cap,
			'description' => '',
			'group'       => 'general',
		);
		$this->capabilities[ $cap ] = array_merge( $defaults, $args, array( 'id' => $cap ) );
	}

	/**
	 * @param string              $role Role slug.
	 * @param array<string,mixed> $args Args: label, capabilities[], grant_to_wp_roles[].
	 * @return void
	 */
	public function register_role( $role, array $args = array() ) {
		$role = sanitize_key( $role );
		if ( ! $role ) {
			return;
		}
		$defaults = array(
			'label'              => $role,
			'description'        => '',
			'capabilities'       => array(),
			'grant_to_wp_roles'  => array(), // also attach caps to existing WP roles
			'create_wp_role'     => false,   // create dedicated WP role
		);
		$args['capabilities'] = array_values( array_unique( array_map( 'sanitize_key', (array) ( $args['capabilities'] ?? array() ) ) ) );
		$args['grant_to_wp_roles'] = array_values( array_unique( array_map( 'sanitize_key', (array) ( $args['grant_to_wp_roles'] ?? array() ) ) ) );
		$this->roles[ $role ] = array_merge( $defaults, $args, array( 'id' => $role ) );
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function capabilities() {
		/**
		 * @param array $caps Caps.
		 */
		return apply_filters( 'kayan_admin_capabilities', $this->capabilities );
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function roles() {
		/**
		 * @param array $roles Roles.
		 */
		return apply_filters( 'kayan_admin_roles', $this->roles );
	}

	/**
	 * @param string      $capability Capability.
	 * @param int|null    $user_id    User ID.
	 * @return bool
	 */
	public function can( $capability, $user_id = null ) {
		$capability = sanitize_key( $capability );
		if ( ! $capability ) {
			return false;
		}
		if ( null === $user_id ) {
			return current_user_can( $capability ) || current_user_can( 'manage_options' );
		}
		return user_can( (int) $user_id, $capability ) || user_can( (int) $user_id, 'manage_options' );
	}

	/**
	 * Whether user can access the Admin Platform at all.
	 *
	 * @param int|null $user_id User.
	 * @return bool
	 */
	public function can_access( $user_id = null ) {
		return $this->can( self::CAP_ACCESS, $user_id );
	}

	/**
	 * Sync capabilities onto WP roles (idempotent).
	 *
	 * @return void
	 */
	public function sync_roles() {
		if ( $this->roles_synced || ! function_exists( 'get_role' ) ) {
			return;
		}
		$this->roles_synced = true;

		foreach ( $this->roles() as $role_id => $role ) {
			$caps = array( self::CAP_ACCESS => true );
			foreach ( (array) $role['capabilities'] as $cap ) {
				$caps[ $cap ] = true;
			}

			if ( ! empty( $role['create_wp_role'] ) ) {
				$existing = get_role( $role_id );
				if ( ! $existing ) {
					add_role( $role_id, (string) $role['label'], $caps );
				} else {
					foreach ( array_keys( $caps ) as $cap ) {
						$existing->add_cap( $cap );
					}
				}
			}

			foreach ( (array) $role['grant_to_wp_roles'] as $wp_role_name ) {
				$wp_role = get_role( $wp_role_name );
				if ( ! $wp_role ) {
					continue;
				}
				foreach ( array_keys( $caps ) as $cap ) {
					$wp_role->add_cap( $cap );
				}
			}
		}

		// Administrators always get full KAYAN caps.
		$admin = get_role( 'administrator' );
		if ( $admin ) {
			foreach ( array_keys( $this->capabilities() ) as $cap ) {
				$admin->add_cap( $cap );
			}
			$admin->add_cap( self::CAP_ACCESS );
		}
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'access_cap'   => self::CAP_ACCESS,
			'roles'        => array_keys( $this->roles() ),
			'capabilities' => array_keys( $this->capabilities() ),
			'apis'         => array(
				'can'               => 'kayan_admin()->permissions->can( $cap )',
				'can_access'        => 'kayan_admin()->permissions->can_access()',
				'register_role'     => 'kayan_admin()->permissions->register_role( $id, $args )',
				'register_capability'=> 'kayan_admin()->permissions->register_capability( $cap, $args )',
			),
		);
	}

	/**
	 * @return void
	 */
	private function register_core_capabilities() {
		$caps = array(
			self::CAP_ACCESS              => array( 'label' => 'Access KAYAN Admin', 'group' => 'core' ),
			'kayan_manage_dashboard'      => array( 'label' => 'Manage Dashboard', 'group' => 'core' ),
			'kayan_manage_countries'      => array( 'label' => 'Manage Countries', 'group' => 'locale' ),
			'kayan_manage_languages'      => array( 'label' => 'Manage Languages', 'group' => 'locale' ),
			'kayan_manage_entities'       => array( 'label' => 'Manage Entities', 'group' => 'content' ),
			'kayan_manage_relationships'  => array( 'label' => 'Manage Relationships', 'group' => 'content' ),
			'kayan_manage_templates'      => array( 'label' => 'Manage Templates', 'group' => 'pseo' ),
			'kayan_manage_blueprints'     => array( 'label' => 'Manage Blueprints', 'group' => 'pseo' ),
			'kayan_manage_blocks'         => array( 'label' => 'Manage Blocks', 'group' => 'pseo' ),
			'kayan_manage_pseo'           => array( 'label' => 'Manage Programmatic SEO', 'group' => 'pseo' ),
			'kayan_manage_ai'             => array( 'label' => 'Manage AI', 'group' => 'ai' ),
			'kayan_manage_media'          => array( 'label' => 'Manage Media', 'group' => 'content' ),
			'kayan_manage_queue'          => array( 'label' => 'Manage Queue', 'group' => 'system' ),
			'kayan_view_logs'             => array( 'label' => 'View Logs', 'group' => 'system' ),
			'kayan_manage_analytics'      => array( 'label' => 'Manage Analytics', 'group' => 'insights' ),
			'kayan_manage_performance'    => array( 'label' => 'Manage Performance', 'group' => 'insights' ),
			'kayan_manage_security'       => array( 'label' => 'Manage Security', 'group' => 'system' ),
			'kayan_manage_import'         => array( 'label' => 'Manage Import', 'group' => 'tools' ),
			'kayan_manage_export'         => array( 'label' => 'Manage Export', 'group' => 'tools' ),
			'kayan_manage_tools'          => array( 'label' => 'Manage Tools', 'group' => 'tools' ),
			'kayan_view_system_health'    => array( 'label' => 'View System Health', 'group' => 'system' ),
			'kayan_manage_rankmath'       => array( 'label' => 'Manage Rank Math Integration', 'group' => 'seo' ),
			'kayan_translate'             => array( 'label' => 'Translate Content', 'group' => 'locale' ),
			'kayan_manage_settings'       => array( 'label' => 'Manage Settings', 'group' => 'core' ),
		);

		foreach ( $caps as $cap => $args ) {
			$this->register_capability( $cap, $args );
		}
	}

	/**
	 * @return void
	 */
	private function register_core_roles() {
		$all_caps = array_keys( $this->capabilities );

		$this->register_role(
			'administrator',
			array(
				'label'             => 'Administrator',
				'capabilities'      => $all_caps,
				'grant_to_wp_roles' => array( 'administrator' ),
				'create_wp_role'    => false,
			)
		);

		$this->register_role(
			'kayan_seo_manager',
			array(
				'label'             => 'SEO Manager',
				'create_wp_role'    => true,
				'grant_to_wp_roles' => array( 'editor' ),
				'capabilities'      => array(
					self::CAP_ACCESS,
					'kayan_manage_dashboard',
					'kayan_manage_countries',
					'kayan_manage_languages',
					'kayan_manage_pseo',
					'kayan_manage_templates',
					'kayan_manage_blueprints',
					'kayan_manage_blocks',
					'kayan_manage_rankmath',
					'kayan_view_logs',
					'kayan_manage_analytics',
					'kayan_manage_media',
				),
			)
		);

		$this->register_role(
			'kayan_content_manager',
			array(
				'label'          => 'Content Manager',
				'create_wp_role' => true,
				'capabilities'   => array(
					self::CAP_ACCESS,
					'kayan_manage_dashboard',
					'kayan_manage_entities',
					'kayan_manage_relationships',
					'kayan_manage_media',
					'kayan_manage_templates',
					'kayan_translate',
				),
			)
		);

		$this->register_role(
			'kayan_editor',
			array(
				'label'             => 'Editor',
				'create_wp_role'    => false,
				'grant_to_wp_roles' => array( 'editor' ),
				'capabilities'      => array(
					self::CAP_ACCESS,
					'kayan_manage_dashboard',
					'kayan_manage_entities',
					'kayan_manage_media',
				),
			)
		);

		$this->register_role(
			'kayan_translator',
			array(
				'label'          => 'Translator',
				'create_wp_role' => true,
				'capabilities'   => array(
					self::CAP_ACCESS,
					'kayan_translate',
					'kayan_manage_languages',
				),
			)
		);

		$this->register_role(
			'kayan_marketing',
			array(
				'label'          => 'Marketing',
				'create_wp_role' => true,
				'capabilities'   => array(
					self::CAP_ACCESS,
					'kayan_manage_dashboard',
					'kayan_manage_analytics',
					'kayan_manage_media',
					'kayan_view_logs',
				),
			)
		);

		$this->register_role(
			'kayan_developer',
			array(
				'label'          => 'Developer',
				'create_wp_role' => true,
				'capabilities'   => array(
					self::CAP_ACCESS,
					'kayan_manage_dashboard',
					'kayan_manage_tools',
					'kayan_view_system_health',
					'kayan_view_logs',
					'kayan_manage_queue',
					'kayan_manage_performance',
					'kayan_manage_security',
					'kayan_manage_import',
					'kayan_manage_export',
					'kayan_manage_settings',
				),
			)
		);
	}
}
