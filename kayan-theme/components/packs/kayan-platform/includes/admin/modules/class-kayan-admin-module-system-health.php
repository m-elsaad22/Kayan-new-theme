<?php
/**
 * Admin module: System Health.
 *
 * Read-only checks derived from existing engines and adapters —
 * no new monitoring system, no duplicate site-health page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_System_Health {

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'kayan_admin_register_modules', array( $this, 'register_module' ), 15 );
	}

	/**
	 * @param Kayan_Admin_Module_Registry $registry Registry.
	 * @return void
	 */
	public function register_module( $registry ) {
		$registry->register_module(
			'system_health',
			array(
				'label'       => __( 'System Health', 'kayan' ),
				'description' => __( 'Routing, SEO, cache, and integration status checks.', 'kayan' ),
				'icon'        => 'dashicons-heart',
				'position'    => 100,
				'capability'  => 'kayan_view_system_health',
				'group'       => 'system',
				'screen'      => array( $this, 'screen' ),
			)
		);
	}

	/**
	 * @param array $module  Module.
	 * @param array $context Context.
	 * @return void
	 */
	public function screen( $module, $context ) {
		unset( $module );
		/** @var Kayan_Admin_UI $ui */
		$ui = $context['ui'];

		$cards = array();
		foreach ( $this->checks() as $check ) {
			$cards[] = $ui->card(
				array(
					'title'   => $check['label'],
					'status'  => $check['status_label'],
					'content' => '<p>' . esc_html( $check['detail'] ) . '</p>',
					'class'   => 'kayan-admin-health-card kayan-admin-health-card--' . $check['type'],
				)
			);
		}

		echo '<div class="kayan-admin-dashboard__grid">' . implode( '', $cards ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * @return array<int,array<string,string>>
	 */
	private function checks() {
		$platform = kayan_platform();
		$checks   = array();

		$rm = Kayan_Theme_Integration::rank_math_active();
		$checks[] = array(
			'label'        => __( 'Rank Math', 'kayan' ),
			'type'         => $rm ? 'success' : 'warning',
			'status_label' => $rm ? __( 'Active', 'kayan' ) : __( 'Not detected', 'kayan' ),
			'detail'       => $rm
				? __( 'Rank Math is the active SEO engine. Theme schema is silenced by the schema adapter.', 'kayan' )
				: __( 'Rank Math is not detected. Theme schema pack runs unmodified until Rank Math is activated.', 'kayan' ),
		);

		$owns = function_exists( 'kayan_platform_owns_routing' ) ? kayan_platform_owns_routing() : false;
		$checks[] = array(
			'label'        => __( 'Routing ownership', 'kayan' ),
			'type'         => $owns ? 'success' : 'warning',
			'status_label' => $owns ? __( 'Platform', 'kayan' ) : __( 'Legacy i18n', 'kayan' ),
			'detail'       => $owns
				? __( 'Country Router + Content Resolver own rewrites. kayan-i18n rewrites are skipped.', 'kayan' )
				: __( 'kayan-i18n owns routing (filtered off). Confirm this is intentional.', 'kayan' ),
		);

		$permalink = get_option( 'permalink_structure' );
		$checks[] = array(
			'label'        => __( 'Permalinks', 'kayan' ),
			'type'         => $permalink ? 'success' : 'error',
			'status_label' => $permalink ? __( 'Pretty', 'kayan' ) : __( 'Plain', 'kayan' ),
			'detail'       => $permalink
				? esc_html( $permalink )
				: __( 'Plain permalinks break language-first routing. Set a pretty permalink structure.', 'kayan' ),
		);

		$cache_desc = $platform->cache->describe();
		$active_driver = isset( $cache_desc['drivers'] ) ? $this->first_available_driver( $cache_desc['drivers'] ) : '';
		$checks[] = array(
			'label'        => __( 'Cache Engine', 'kayan' ),
			'type'         => $active_driver ? 'success' : 'warning',
			'status_label' => $active_driver ? strtoupper( $active_driver ) : __( 'None', 'kayan' ),
			'detail'       => $active_driver
				? sprintf( /* translators: %s: driver id */ __( 'Using %s driver.', 'kayan' ), $active_driver )
				: __( 'No cache driver available.', 'kayan' ),
		);

		$logger_desc = $platform->logger->describe();
		$checks[] = array(
			'label'        => __( 'Logger', 'kayan' ),
			'type'         => $logger_desc['enabled'] ? 'success' : 'neutral',
			'status_label' => $logger_desc['enabled'] ? __( 'Enabled', 'kayan' ) : __( 'Disabled', 'kayan' ),
			'detail'       => sprintf( /* translators: %d: number of entries stored */ __( 'Ring buffer capacity: %d entries.', 'kayan' ), (int) $logger_desc['max'] ),
		);

		$countries_count = count( $platform->countries->all() );
		$languages_count = count( $platform->languages->all() );
		$checks[] = array(
			'label'        => __( 'Locale registry', 'kayan' ),
			'type'         => 'info',
			'status_label' => sprintf( '%d / %d', $countries_count, $languages_count ),
			'detail'       => __( 'Countries / Languages registered (built-in + custom).', 'kayan' ),
		);

		if ( isset( $platform->integration ) ) {
			$adapters = $platform->integration->adapters();
			$checks[] = array(
				'label'        => __( 'Theme Integration adapters', 'kayan' ),
				'type'         => count( $adapters ) > 0 ? 'success' : 'warning',
				'status_label' => (string) count( $adapters ),
				'detail'       => __( 'Adapters connecting existing theme packs to the platform.', 'kayan' ),
			);

			if ( isset( $adapters['legacy_city'] ) ) {
				$st = $adapters['legacy_city']->status();
				$checks[] = array(
					'label'        => __( 'Legacy city taxonomy', 'kayan' ),
					'type'         => 'deprecated' === $st['state'] ? 'warning' : 'success',
					'status_label' => ucfirst( (string) $st['state'] ),
					'detail'       => sprintf( /* translators: %d: term count */ __( '%d legacy term(s) remaining.', 'kayan' ), (int) ( $st['legacy_term_count'] ?? 0 ) ),
				);
			}
		}

		$checks[] = array(
			'label'        => __( 'PHP version', 'kayan' ),
			'type'         => version_compare( PHP_VERSION, '7.4', '>=' ) ? 'success' : 'error',
			'status_label' => PHP_VERSION,
			'detail'       => __( 'Minimum supported: PHP 7.4.', 'kayan' ),
		);

		global $wp_version;
		$checks[] = array(
			'label'        => __( 'WordPress version', 'kayan' ),
			'type'         => 'info',
			'status_label' => isset( $wp_version ) ? $wp_version : __( 'Unknown', 'kayan' ),
			'detail'       => __( 'Current WordPress core version.', 'kayan' ),
		);

		if ( isset( $platform->migrations ) ) {
			$cur   = $platform->migrations->current_version();
			$tgt   = $platform->migrations->target_version();
			$up    = $cur >= $tgt;
			$checks[] = array(
				'label'        => __( 'Schema migrations', 'kayan' ),
				'type'         => $up ? 'success' : 'warning',
				'status_label' => $cur . ' / ' . $tgt,
				'detail'       => $up
					? __( 'Up to date. Migrations run automatically — no manual step required.', 'kayan' )
					: __( 'Pending migrations will run automatically on the next request.', 'kayan' ),
			);
		}

		if ( function_exists( 'kayan_pseo' ) ) {
			$queued = count( kayan_pseo()->jobs->all( array( 'status' => 'queued', 'limit' => 100 ) ) );
			$failed = count( kayan_pseo()->jobs->all( array( 'status' => 'failed', 'limit' => 100 ) ) );
			$checks[] = array(
				'label'        => __( 'PSEO queue', 'kayan' ),
				'type'         => $failed > 0 ? 'warning' : 'success',
				'status_label' => sprintf( '%d / %d', $queued, $failed ),
				'detail'       => __( 'Queued / failed jobs. The Scheduler processes the queue automatically.', 'kayan' ),
			);
		}

		return $checks;
	}

	/**
	 * @param array<string,array<string,mixed>> $drivers Drivers.
	 * @return string
	 */
	private function first_available_driver( array $drivers ) {
		foreach ( $drivers as $id => $driver ) {
			if ( ! empty( $driver['available'] ) ) {
				return (string) $id;
			}
		}
		return '';
	}
}
