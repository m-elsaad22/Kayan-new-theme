<?php
/**
 * KAYAN Admin Module Registry — modular registration for the Admin Platform.
 *
 * Modules register nav, widgets, settings, tables, forms, cards, actions,
 * notifications, and permissions through one API — without modifying core.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Registry {

	/** @var array<string,array<string,mixed>> */
	private $modules = array();

	/** @var Kayan_Admin_Permissions */
	private $permissions;

	public function __construct( Kayan_Admin_Permissions $permissions ) {
		$this->permissions = $permissions;
	}

	/**
	 * @return void
	 */
	public function register() {
		/**
		 * Register admin modules: $registry->register_module( $id, $args ).
		 *
		 * @param Kayan_Admin_Module_Registry $registry Registry.
		 */
		do_action( 'kayan_admin_register_modules', $this );
	}

	/**
	 * Single module registration API.
	 *
	 * @param string              $id   Module id.
	 * @param array<string,mixed> $args Args.
	 * @return void
	 */
	public function register_module( $id, array $args ) {
		$id = sanitize_key( $id );
		if ( ! $id ) {
			return;
		}

		$defaults = array(
			'label'         => $id,
			'description'   => '',
			'icon'          => 'dashicons-admin-generic',
			'position'      => 50,
			'enabled'       => true,
			'capability'    => Kayan_Admin_Permissions::CAP_ACCESS,
			'parent'        => 'kayan-platform', // central shell — not isolated pages
			'nav'           => true, // show in admin nav
			'screen'        => null, // callable( $module, $context )
			'save'          => null, // callable( $module, $context ) — handles POST before render (admin_init)
			'widgets'       => array(), // dashboard widget ids/defs
			'settings'      => array(), // settings page sections
			'tables'        => array(),
			'forms'         => array(),
			'cards'         => array(),
			'actions'       => array(),
			'notifications' => array(),
			'permissions'   => array(), // extra caps this module needs
			'menu'          => array(), // optional menu overrides
			'group'         => 'general',
		);

		$module = array_merge( $defaults, $args, array( 'id' => $id ) );
		$module['capability'] = sanitize_key( (string) $module['capability'] );
		$module['position']   = (int) $module['position'];

		foreach ( (array) $module['permissions'] as $cap => $cap_args ) {
			if ( is_int( $cap ) ) {
				$this->permissions->register_capability( (string) $cap_args, array( 'group' => $id ) );
			} else {
				$this->permissions->register_capability( (string) $cap, is_array( $cap_args ) ? $cap_args : array( 'group' => $id ) );
			}
		}

		$this->modules[ $id ] = $module;

		/**
		 * @param array  $module Module.
		 * @param string $id     Id.
		 */
		do_action( 'kayan_admin_module_registered', $module, $id );
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function all() {
		$modules = $this->modules;
		uasort(
			$modules,
			static function ( $a, $b ) {
				return ( (int) $a['position'] ) <=> ( (int) $b['position'] );
			}
		);
		/**
		 * @param array $modules Modules.
		 */
		return apply_filters( 'kayan_admin_modules', $modules );
	}

	/**
	 * @param string $id Module.
	 * @return array<string,mixed>|null
	 */
	public function get( $id ) {
		$id      = sanitize_key( $id );
		$modules = $this->all();
		return isset( $modules[ $id ] ) ? $modules[ $id ] : null;
	}

	/**
	 * Modules visible in navigation for current user.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function nav_modules() {
		$out = array();
		foreach ( $this->all() as $id => $module ) {
			if ( empty( $module['enabled'] ) || empty( $module['nav'] ) ) {
				continue;
			}
			if ( ! $this->permissions->can( $module['capability'] ) ) {
				continue;
			}
			$out[ $id ] = $module;
		}
		return $out;
	}

	/**
	 * Register a piece onto an existing module without editing core.
	 *
	 * @param string $module_id Module.
	 * @param string $bucket    widgets|settings|tables|forms|cards|actions|notifications.
	 * @param string $item_id   Item id.
	 * @param array  $item      Item def.
	 * @return bool
	 */
	public function register_item( $module_id, $bucket, $item_id, array $item ) {
		$module_id = sanitize_key( $module_id );
		$bucket    = sanitize_key( $bucket );
		$item_id   = sanitize_key( $item_id );
		$allowed   = array( 'widgets', 'settings', 'tables', 'forms', 'cards', 'actions', 'notifications' );
		if ( ! isset( $this->modules[ $module_id ] ) || ! in_array( $bucket, $allowed, true ) || ! $item_id ) {
			return false;
		}
		if ( ! isset( $this->modules[ $module_id ][ $bucket ] ) || ! is_array( $this->modules[ $module_id ][ $bucket ] ) ) {
			$this->modules[ $module_id ][ $bucket ] = array();
		}
		$this->modules[ $module_id ][ $bucket ][ $item_id ] = array_merge( $item, array( 'id' => $item_id ) );
		return true;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'count'   => count( $this->modules ),
			'modules' => array_keys( $this->all() ),
			'apis'    => array(
				'register_module' => 'kayan_admin()->modules->register_module( $id, $args )',
				'register_item'   => 'kayan_admin()->modules->register_item( $module, $bucket, $id, $args )',
				'get'             => 'kayan_admin()->modules->get( $id )',
				'nav_modules'     => 'kayan_admin()->modules->nav_modules()',
			),
			'buckets' => array( 'nav', 'widgets', 'settings', 'tables', 'forms', 'cards', 'actions', 'notifications', 'permissions' ),
		);
	}
}
