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
		$labels = function_exists( 'kayan_admin_module_labels_ar' ) ? kayan_admin_module_labels_ar() : array();
		$defs   = array(
			'dashboard' => array(
				'label'       => isset( $labels['dashboard'] ) ? $labels['dashboard'] : 'لوحة التحكم',
				'description' => 'أساس لوحة التحكم الإدارية.',
				'icon'        => 'dashicons-dashboard',
				'position'    => 1,
				'capability'  => 'kayan_manage_dashboard',
				'group'       => 'core',
				'screen'      => array( $this, 'screen_dashboard' ),
			),
			'countries' => array(
				'label'       => isset( $labels['countries'] ) ? $labels['countries'] : 'الدول',
				'icon'        => 'dashicons-admin-site-alt3',
				'position'    => 10,
				'capability'  => 'kayan_manage_countries',
				'group'       => 'locale',
			),
			'languages' => array(
				'label'       => isset( $labels['languages'] ) ? $labels['languages'] : 'اللغات',
				'icon'        => 'dashicons-translation',
				'position'    => 15,
				'capability'  => 'kayan_manage_languages',
				'group'       => 'locale',
			),
			'entities' => array(
				'label'       => isset( $labels['entities'] ) ? $labels['entities'] : 'الكيانات',
				'icon'        => 'dashicons-networking',
				'position'    => 20,
				'capability'  => 'kayan_manage_entities',
				'group'       => 'content',
			),
			'relationships' => array(
				'label'       => isset( $labels['relationships'] ) ? $labels['relationships'] : 'العلاقات',
				'icon'        => 'dashicons-share',
				'position'    => 25,
				'capability'  => 'kayan_manage_relationships',
				'group'       => 'content',
			),
			'templates' => array(
				'label'       => isset( $labels['templates'] ) ? $labels['templates'] : 'القوالب',
				'icon'        => 'dashicons-layout',
				'position'    => 30,
				'capability'  => 'kayan_manage_templates',
				'group'       => 'pseo',
			),
			'blueprints' => array(
				'label'       => isset( $labels['blueprints'] ) ? $labels['blueprints'] : 'المخططات',
				'icon'        => 'dashicons-media-code',
				'position'    => 35,
				'capability'  => 'kayan_manage_blueprints',
				'group'       => 'pseo',
			),
			'blocks' => array(
				'label'       => isset( $labels['blocks'] ) ? $labels['blocks'] : 'البلوكات',
				'icon'        => 'dashicons-block-default',
				'position'    => 40,
				'capability'  => 'kayan_manage_blocks',
				'group'       => 'pseo',
			),
			'pseo' => array(
				'label'       => isset( $labels['pseo'] ) ? $labels['pseo'] : 'SEO البرمجي',
				'icon'        => 'dashicons-chart-area',
				'position'    => 45,
				'capability'  => 'kayan_manage_pseo',
				'group'       => 'pseo',
			),
			'ai' => array(
				'label'       => isset( $labels['ai'] ) ? $labels['ai'] : 'الذكاء الاصطناعي',
				'icon'        => 'dashicons-superhero',
				'position'    => 50,
				'capability'  => 'kayan_manage_ai',
				'group'       => 'ai',
			),
			'media' => array(
				'label'       => isset( $labels['media'] ) ? $labels['media'] : 'الوسائط',
				'icon'        => 'dashicons-format-gallery',
				'position'    => 55,
				'capability'  => 'kayan_manage_media',
				'group'       => 'content',
			),
			'queue' => array(
				'label'       => isset( $labels['queue'] ) ? $labels['queue'] : 'قائمة المهام',
				'icon'        => 'dashicons-controls-repeat',
				'position'    => 60,
				'capability'  => 'kayan_manage_queue',
				'group'       => 'system',
			),
			'logs' => array(
				'label'       => isset( $labels['logs'] ) ? $labels['logs'] : 'السجلات',
				'icon'        => 'dashicons-list-view',
				'position'    => 65,
				'capability'  => 'kayan_view_logs',
				'group'       => 'system',
			),
			'analytics' => array(
				'label'       => isset( $labels['analytics'] ) ? $labels['analytics'] : 'التحليلات',
				'icon'        => 'dashicons-chart-bar',
				'position'    => 70,
				'capability'  => 'kayan_manage_analytics',
				'group'       => 'insights',
			),
			'performance' => array(
				'label'       => isset( $labels['performance'] ) ? $labels['performance'] : 'الأداء',
				'icon'        => 'dashicons-performance',
				'position'    => 75,
				'capability'  => 'kayan_manage_performance',
				'group'       => 'insights',
			),
			'security' => array(
				'label'       => isset( $labels['security'] ) ? $labels['security'] : 'الأمان',
				'icon'        => 'dashicons-shield',
				'position'    => 80,
				'capability'  => 'kayan_manage_security',
				'group'       => 'system',
			),
			'import' => array(
				'label'       => isset( $labels['import'] ) ? $labels['import'] : 'استيراد',
				'icon'        => 'dashicons-database-import',
				'position'    => 85,
				'capability'  => 'kayan_manage_import',
				'group'       => 'tools',
			),
			'export' => array(
				'label'       => isset( $labels['export'] ) ? $labels['export'] : 'تصدير',
				'icon'        => 'dashicons-database-export',
				'position'    => 90,
				'capability'  => 'kayan_manage_export',
				'group'       => 'tools',
			),
			'tools' => array(
				'label'       => isset( $labels['tools'] ) ? $labels['tools'] : 'أدوات',
				'icon'        => 'dashicons-admin-tools',
				'position'    => 95,
				'capability'  => 'kayan_manage_tools',
				'group'       => 'tools',
			),
			'system_health' => array(
				'label'       => isset( $labels['system_health'] ) ? $labels['system_health'] : 'صحة النظام',
				'icon'        => 'dashicons-heart',
				'position'    => 100,
				'capability'  => 'kayan_view_system_health',
				'group'       => 'system',
			),
			'rankmath' => array(
				'label'       => isset( $labels['rankmath'] ) ? $labels['rankmath'] : 'تكامل Rank Math',
				'icon'        => 'dashicons-chart-line',
				'position'    => 105,
				'capability'  => 'kayan_manage_rankmath',
				'group'       => 'seo',
				'description' => 'Rank Math هو محرك SEO الوحيد. هذه الوحدة تضبط الجسر والتكامل.',
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
