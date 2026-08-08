<?php
/**
 * Real dashboard widget content for registered Admin Platform slots.
 *
 * Overrides widget ids already registered as placeholder slots in
 * Kayan_Admin_Dashboard — no new widget IDs, no new registry.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Dashboard_Stats {

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'kayan_admin_register_dashboard_widgets', array( $this, 'register_widgets' ), 20 );
	}

	/**
	 * @param Kayan_Admin_Dashboard $dashboard Dashboard.
	 * @return void
	 */
	public function register_widgets( $dashboard ) {
		$dashboard->register_widget(
			'countries',
			array(
				'title'       => __( 'Countries', 'kayan' ),
				'module'      => 'countries',
				'capability'  => 'kayan_manage_countries',
				'position'    => 20,
				'placeholder' => false,
				'callback'    => array( $this, 'render_countries' ),
			)
		);
		$dashboard->register_widget(
			'languages',
			array(
				'title'       => __( 'Languages', 'kayan' ),
				'module'      => 'languages',
				'capability'  => 'kayan_manage_languages',
				'position'    => 30,
				'placeholder' => false,
				'callback'    => array( $this, 'render_languages' ),
			)
		);
		// Core slot id is `seo`; also refresh `rankmath` if present.
		foreach ( array( 'seo', 'rankmath' ) as $seo_widget_id ) {
			$dashboard->register_widget(
				$seo_widget_id,
				array(
					'title'       => __( 'Rank Math', 'kayan' ),
					'module'      => 'rankmath',
					'capability'  => 'kayan_manage_rankmath',
					'position'    => 10,
					'placeholder' => false,
					'callback'    => array( $this, 'render_rankmath' ),
				)
			);
		}
		$dashboard->register_widget(
			'logs',
			array(
				'title'       => __( 'Recent Logs', 'kayan' ),
				'module'      => 'logs',
				'capability'  => 'kayan_view_logs',
				'position'    => 100,
				'placeholder' => false,
				'callback'    => array( $this, 'render_logs' ),
			)
		);
		$dashboard->register_widget(
			'programmatic_seo',
			array(
				'title'       => __( 'Programmatic SEO', 'kayan' ),
				'module'      => 'pseo',
				'capability'  => 'kayan_manage_pseo',
				'position'    => 40,
				'placeholder' => false,
				'callback'    => array( $this, 'render_pseo' ),
			)
		);
		$dashboard->register_widget(
			'queue',
			array(
				'title'       => __( 'Queue', 'kayan' ),
				'module'      => 'queue',
				'capability'  => 'kayan_manage_queue',
				'position'    => 50,
				'placeholder' => false,
				'callback'    => array( $this, 'render_queue' ),
			)
		);
		$dashboard->register_widget(
			'ai',
			array(
				'title'       => __( 'AI', 'kayan' ),
				'module'      => 'ai',
				'capability'  => 'kayan_manage_ai',
				'position'    => 60,
				'placeholder' => false,
				'callback'    => array( $this, 'render_ai' ),
			)
		);
		$dashboard->register_widget(
			'performance',
			array(
				'title'       => __( 'Performance', 'kayan' ),
				'module'      => 'performance',
				'capability'  => 'kayan_manage_performance',
				'position'    => 70,
				'placeholder' => false,
				'callback'    => array( $this, 'render_performance' ),
			)
		);
		$dashboard->register_widget(
			'analytics',
			array(
				'title'       => __( 'Analytics', 'kayan' ),
				'module'      => 'analytics',
				'capability'  => 'kayan_manage_analytics',
				'position'    => 80,
				'placeholder' => false,
				'callback'    => array( $this, 'render_analytics' ),
			)
		);
	}

	/**
	 * @return string
	 */
	public function render_performance() {
		$enabled = function_exists( 'kayan_perf_is_enabled' ) ? kayan_perf_is_enabled() : false;
		$status  = $enabled ? __( 'Optimizations active', 'kayan' ) : __( 'Disabled', 'kayan' );
		$bits    = array();
		if ( $enabled ) {
			if ( empty( yc_get_option( 'kayan_perf_disable_preload' ) ) ) {
				$bits[] = __( 'LCP preload', 'kayan' );
			}
			$bits[] = __( 'Resource hints', 'kayan' );
		}
		$html = '<p class="kayan-admin-stat">' . esc_html( $status ) . '</p>';
		if ( $bits ) {
			$html .= '<p class="description">' . esc_html( implode( ' · ', $bits ) ) . '</p>';
		}
		return $html;
	}

	/**
	 * @return string
	 */
	public function render_analytics() {
		if ( ! function_exists( 'kayan_track_is_enabled' ) || ! kayan_track_is_enabled() ) {
			return '<p class="description">' . esc_html__( 'KAYAN Track is disabled.', 'kayan' ) . '</p>';
		}
		if ( ! function_exists( 'kayan_track_table' ) || ! function_exists( 'kayan_track_date_range' ) ) {
			return '<p class="description">' . esc_html__( 'Track helpers unavailable.', 'kayan' ) . '</p>';
		}

		global $wpdb;
		$range = kayan_track_date_range( '7d' );
		$table = kayan_track_table( 'conversions' );
		$row   = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT COUNT(*) AS total,
					SUM(click_type = 'call') AS calls,
					SUM(click_type = 'whatsapp') AS whatsapp
				FROM {$table}
				WHERE is_duplicate = 0 AND created_at >= %s AND created_at <= %s",
				$range['from'],
				$range['to']
			),
			ARRAY_A
		);

		$total = isset( $row['total'] ) ? (int) $row['total'] : 0;
		$calls = isset( $row['calls'] ) ? (int) $row['calls'] : 0;
		$wa    = isset( $row['whatsapp'] ) ? (int) $row['whatsapp'] : 0;

		return '<p class="kayan-admin-stat"><strong>' . esc_html( (string) $total ) . '</strong> ' . esc_html__( 'conversions (7d)', 'kayan' ) . '</p>'
			. '<p class="description">' . esc_html( sprintf(
				/* translators: 1: call clicks, 2: whatsapp clicks */
				__( '%1$d calls · %2$d WhatsApp', 'kayan' ),
				$calls,
				$wa
			) ) . '</p>';
	}

	/**
	 * @return string
	 */
	public function render_ai() {
		if ( ! function_exists( 'kayan_ai' ) ) {
			return '<p class="description">' . esc_html__( 'AI Platform not available.', 'kayan' ) . '</p>';
		}
		$default = kayan_ai()->default_provider_id();
		$label   = 'null' === $default ? __( 'None configured', 'kayan' ) : kayan_ai()->get_provider( $default )->label();
		return '<p class="kayan-admin-stat">' . esc_html( $label ) . '</p>';
	}

	/**
	 * @return string
	 */
	public function render_pseo() {
		if ( ! function_exists( 'kayan_pseo' ) ) {
			return '<p class="description">' . esc_html__( 'Programmatic SEO not available.', 'kayan' ) . '</p>';
		}
		$rules = kayan_pseo()->rules->all();
		return '<p class="kayan-admin-stat"><strong>' . esc_html( (string) count( $rules ) ) . '</strong> ' . esc_html__( 'generation rules', 'kayan' ) . '</p>';
	}

	/**
	 * @return string
	 */
	public function render_queue() {
		if ( ! function_exists( 'kayan_pseo' ) ) {
			return '<p class="description">' . esc_html__( 'Queue not available.', 'kayan' ) . '</p>';
		}
		$running = kayan_pseo()->jobs->all( array( 'status' => 'running', 'limit' => 5 ) );
		$queued  = kayan_pseo()->jobs->all( array( 'status' => 'queued', 'limit' => 5 ) );
		return '<p class="kayan-admin-stat"><strong>' . esc_html( (string) count( $running ) ) . '</strong> ' . esc_html__( 'running', 'kayan' ) . ', <strong>' . esc_html( (string) count( $queued ) ) . '</strong> ' . esc_html__( 'queued', 'kayan' ) . '</p>';
	}

	/**
	 * @return string
	 */
	public function render_countries() {
		$platform = kayan_platform();
		$count    = count( $platform->countries->all() );
		return '<p class="kayan-admin-stat"><strong>' . esc_html( (string) $count ) . '</strong> ' . esc_html__( 'registered countries', 'kayan' ) . '</p>'
			. '<p class="description">' . esc_html( sprintf( /* translators: %s: country code */ __( 'Default: %s', 'kayan' ), $platform->countries->get_default() ) ) . '</p>';
	}

	/**
	 * @return string
	 */
	public function render_languages() {
		$platform = kayan_platform();
		$all      = $platform->languages->all();
		$disabled = class_exists( 'Kayan_Admin_Module_Languages' ) ? count( Kayan_Admin_Module_Languages::disabled_codes() ) : 0;
		return '<p class="kayan-admin-stat"><strong>' . esc_html( (string) count( $all ) ) . '</strong> ' . esc_html__( 'registered languages', 'kayan' ) . '</p>'
			. '<p class="description">' . esc_html( sprintf( /* translators: %d: number of disabled languages */ __( '%d disabled', 'kayan' ), $disabled ) ) . '</p>';
	}

	/**
	 * @return string
	 */
	public function render_rankmath() {
		$active = Kayan_Theme_Integration::rank_math_active();
		return '<p class="kayan-admin-stat">' . ( $active
			? esc_html__( 'Active — SEO Bridge extending it', 'kayan' )
			: esc_html__( 'Not detected on this site', 'kayan' ) ) . '</p>';
	}

	/**
	 * @return string
	 */
	public function render_logs() {
		$recent = kayan_logger()->recent( array( 'limit' => 5 ) );
		if ( empty( $recent ) ) {
			return '<p class="description">' . esc_html__( 'No log entries yet.', 'kayan' ) . '</p>';
		}
		$items = '';
		foreach ( $recent as $entry ) {
			$items .= '<li><code>' . esc_html( (string) $entry['channel'] ) . '</code> — ' . esc_html( (string) $entry['message'] ) . '</li>';
		}
		return '<ul class="kayan-admin-stat-list">' . $items . '</ul>';
	}
}
