<?php
/**
 * Real dashboard widget content for the modules completed in Phase 3.
 *
 * Overrides only the widget ids already registered as placeholder slots in
 * Kayan_Admin_Dashboard (Phase 3.0) — no new widget IDs, no new registry.
 * PSEO / Queue / AI / Performance / Analytics stay placeholders (later phases).
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
		$dashboard->register_widget(
			'rankmath',
			array(
				'title'       => __( 'Rank Math', 'kayan' ),
				'module'      => 'rankmath',
				'capability'  => 'kayan_manage_rankmath',
				'position'    => 10,
				'placeholder' => false,
				'callback'    => array( $this, 'render_rankmath' ),
			)
		);
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
