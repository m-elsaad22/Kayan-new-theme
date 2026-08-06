<?php
/**
 * Registers core Admin Platform modules (architecture shells).
 *
 * Each module registers through the Module Registry — no isolated admin pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Core_Modules {

	/** @var Kayan_Admin_Module_Registry */
	private $modules;

	/** @var Kayan_Admin_UI */
	private $ui;

	public function __construct( Kayan_Admin_Module_Registry $modules, Kayan_Admin_UI $ui ) {
		$this->modules = $modules;
		$this->ui      = $ui;
	}

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'kayan_admin_register_modules', array( $this, 'register_modules' ), 5 );
	}

	/**
	 * @param Kayan_Admin_Module_Registry $registry Registry.
	 * @return void
	 */
	public function register_modules( $registry ) {
		$defs = array(
			'dashboard' => array(
				'label'       => 'Dashboard',
				'description' => 'Admin dashboard foundation.',
				'icon'        => 'dashicons-dashboard',
				'position'    => 1,
				'capability'  => 'kayan_manage_dashboard',
				'group'       => 'core',
				'screen'      => array( $this, 'screen_dashboard' ),
			),
			'countries' => array(
				'label'       => 'Countries',
				'icon'        => 'dashicons-admin-site-alt3',
				'position'    => 10,
				'capability'  => 'kayan_manage_countries',
				'group'       => 'locale',
			),
			'languages' => array(
				'label'       => 'Languages',
				'icon'        => 'dashicons-translation',
				'position'    => 15,
				'capability'  => 'kayan_manage_languages',
				'group'       => 'locale',
			),
			'entities' => array(
				'label'       => 'Entities',
				'icon'        => 'dashicons-networking',
				'position'    => 20,
				'capability'  => 'kayan_manage_entities',
				'group'       => 'content',
			),
			'relationships' => array(
				'label'       => 'Relationships',
				'icon'        => 'dashicons-share',
				'position'    => 25,
				'capability'  => 'kayan_manage_relationships',
				'group'       => 'content',
			),
			'templates' => array(
				'label'       => 'Templates',
				'icon'        => 'dashicons-layout',
				'position'    => 30,
				'capability'  => 'kayan_manage_templates',
				'group'       => 'pseo',
			),
			'blueprints' => array(
				'label'       => 'Blueprints',
				'icon'        => 'dashicons-media-code',
				'position'    => 35,
				'capability'  => 'kayan_manage_blueprints',
				'group'       => 'pseo',
			),
			'blocks' => array(
				'label'       => 'Blocks',
				'icon'        => 'dashicons-block-default',
				'position'    => 40,
				'capability'  => 'kayan_manage_blocks',
				'group'       => 'pseo',
			),
			'pseo' => array(
				'label'       => 'Programmatic SEO',
				'icon'        => 'dashicons-chart-area',
				'position'    => 45,
				'capability'  => 'kayan_manage_pseo',
				'group'       => 'pseo',
			),
			'ai' => array(
				'label'       => 'AI',
				'icon'        => 'dashicons-superhero',
				'position'    => 50,
				'capability'  => 'kayan_manage_ai',
				'group'       => 'ai',
			),
			'media' => array(
				'label'       => 'Media',
				'icon'        => 'dashicons-format-gallery',
				'position'    => 55,
				'capability'  => 'kayan_manage_media',
				'group'       => 'content',
			),
			'queue' => array(
				'label'       => 'Queue',
				'icon'        => 'dashicons-controls-repeat',
				'position'    => 60,
				'capability'  => 'kayan_manage_queue',
				'group'       => 'system',
			),
			'logs' => array(
				'label'       => 'Logs',
				'icon'        => 'dashicons-list-view',
				'position'    => 65,
				'capability'  => 'kayan_view_logs',
				'group'       => 'system',
			),
			'analytics' => array(
				'label'       => 'Analytics',
				'icon'        => 'dashicons-chart-bar',
				'position'    => 70,
				'capability'  => 'kayan_manage_analytics',
				'group'       => 'insights',
			),
			'performance' => array(
				'label'       => 'Performance',
				'icon'        => 'dashicons-performance',
				'position'    => 75,
				'capability'  => 'kayan_manage_performance',
				'group'       => 'insights',
			),
			'security' => array(
				'label'       => 'Security',
				'icon'        => 'dashicons-shield',
				'position'    => 80,
				'capability'  => 'kayan_manage_security',
				'group'       => 'system',
			),
			'import' => array(
				'label'       => 'Import',
				'icon'        => 'dashicons-database-import',
				'position'    => 85,
				'capability'  => 'kayan_manage_import',
				'group'       => 'tools',
			),
			'export' => array(
				'label'       => 'Export',
				'icon'        => 'dashicons-database-export',
				'position'    => 90,
				'capability'  => 'kayan_manage_export',
				'group'       => 'tools',
			),
			'tools' => array(
				'label'       => 'Tools',
				'icon'        => 'dashicons-admin-tools',
				'position'    => 95,
				'capability'  => 'kayan_manage_tools',
				'group'       => 'tools',
			),
			'system_health' => array(
				'label'       => 'System Health',
				'icon'        => 'dashicons-heart',
				'position'    => 100,
				'capability'  => 'kayan_view_system_health',
				'group'       => 'system',
			),
			'rankmath' => array(
				'label'       => 'Rank Math Integration',
				'icon'        => 'dashicons-chart-line',
				'position'    => 105,
				'capability'  => 'kayan_manage_rankmath',
				'group'       => 'seo',
				'description' => 'Rank Math remains the only SEO engine. This module will expose integration controls later.',
			),
		);

		foreach ( $defs as $id => $args ) {
			if ( empty( $args['screen'] ) ) {
				$args['screen'] = array( $this, 'screen_placeholder' );
			}
			$registry->register_module( $id, $args );
		}

		unset( $registry );
	}

	/**
	 * @param array $module Module.
	 * @param array $context Context.
	 * @return void
	 */
	public function screen_dashboard( $module, $context ) {
		unset( $module );
		$dashboard = isset( $context['dashboard'] ) ? $context['dashboard'] : null;
		if ( $dashboard && method_exists( $dashboard, 'render' ) ) {
			echo $dashboard->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}
		echo $this->ui->empty_state( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'title'       => __( 'Dashboard', 'kayan' ),
				'description' => __( 'Dashboard foundation is loading.', 'kayan' ),
			)
		);
	}

	/**
	 * Generic architecture placeholder for registered modules.
	 *
	 * @param array $module  Module.
	 * @param array $context Context.
	 * @return void
	 */
	public function screen_placeholder( $module, $context ) {
		unset( $context );
		$label = isset( $module['label'] ) ? (string) $module['label'] : 'Module';
		$desc  = ! empty( $module['description'] )
			? (string) $module['description']
			: sprintf(
				/* translators: %s: module label */
				__( '%s module is registered in the Admin Platform. Feature UI arrives in a later phase.', 'kayan' ),
				$label
			);

		echo $this->ui->empty_state( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'title'       => $label,
				'description' => $desc,
			)
		);

		$meta = $this->ui->table(
			array(
				'columns' => array(
					'key'   => 'Key',
					'value' => 'Value',
				),
				'rows'    => array(
					array(
						'id'    => 'id',
						'key'   => 'Module ID',
						'value' => esc_html( (string) ( $module['id'] ?? '' ) ),
					),
					array(
						'id'    => 'cap',
						'key'   => 'Capability',
						'value' => esc_html( (string) ( $module['capability'] ?? '' ) ),
					),
					array(
						'id'    => 'group',
						'key'   => 'Group',
						'value' => esc_html( (string) ( $module['group'] ?? '' ) ),
					),
				),
			)
		);
		echo $this->ui->card( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'title'   => __( 'Registration contract', 'kayan' ),
				'content' => $meta,
			)
		);
	}
}
