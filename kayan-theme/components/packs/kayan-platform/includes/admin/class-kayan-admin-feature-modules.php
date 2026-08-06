<?php
/**
 * Loader for Phase 3 functional Admin Platform modules.
 *
 * Each module owns its own registration + save/screen callbacks.
 * This loader only instantiates and calls register() — no logic here.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Feature_Modules {

	/** @var array<int,object> */
	private $modules = array();

	/**
	 * @return void
	 */
	public function register() {
		$dir = __DIR__ . '/modules/';
		$classes = array(
			'class-kayan-admin-dashboard-stats.php'       => 'Kayan_Admin_Dashboard_Stats',
			'class-kayan-admin-module-settings.php'       => 'Kayan_Admin_Module_Settings',
			'class-kayan-admin-module-countries.php'      => 'Kayan_Admin_Module_Countries',
			'class-kayan-admin-module-languages.php'      => 'Kayan_Admin_Module_Languages',
			'class-kayan-admin-module-entities.php'       => 'Kayan_Admin_Module_Entities',
			'class-kayan-admin-module-relationships.php'  => 'Kayan_Admin_Module_Relationships',
			'class-kayan-admin-module-permissions.php'    => 'Kayan_Admin_Module_Permissions',
			'class-kayan-admin-module-logs.php'           => 'Kayan_Admin_Module_Logs',
			'class-kayan-admin-module-system-health.php'  => 'Kayan_Admin_Module_System_Health',
			'class-kayan-admin-module-import-export.php'  => 'Kayan_Admin_Module_Import_Export',
			'class-kayan-admin-module-rankmath.php'       => 'Kayan_Admin_Module_Rankmath',
		);

		foreach ( $classes as $file => $class ) {
			$path = $dir . $file;
			if ( file_exists( $path ) ) {
				require_once $path;
			}
			if ( class_exists( $class ) ) {
				$instance = new $class();
				$instance->register();
				$this->modules[] = $instance;
			}
		}

		/**
		 * @param Kayan_Admin_Feature_Modules $feature_modules Loader.
		 */
		do_action( 'kayan_admin_feature_modules_registered', $this );
	}

	/**
	 * @return array<int,object>
	 */
	public function all() {
		return $this->modules;
	}
}
