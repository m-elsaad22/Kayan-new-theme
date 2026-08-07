<?php
/**
 * Admin module: Rank Math Integration.
 *
 * Read-only status view over the existing SEO Bridge + schema adapter.
 * Rank Math's own settings remain in Rank Math's admin — never duplicated here.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Rankmath {

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
			'rankmath',
			array(
				'label'       => __( 'Rank Math Integration', 'kayan' ),
				'description' => __( 'Status of the SEO Bridge extending Rank Math (the only SEO engine).', 'kayan' ),
				'icon'        => 'dashicons-chart-line',
				'position'    => 105,
				'capability'  => 'kayan_manage_rankmath',
				'group'       => 'seo',
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
		$ui     = $context['ui'];
		$active = Kayan_Theme_Integration::rank_math_active();

		echo $ui->card( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'title'   => __( 'Rank Math status', 'kayan' ),
				'status'  => $active ? __( 'Active', 'kayan' ) : __( 'Not active', 'kayan' ),
				'content' => '<p>' . ( $active
					? esc_html__( 'Rank Math is active. KAYAN extends it via filters — never competing head tags.', 'kayan' )
					: esc_html__( 'Rank Math is not detected on this site. Activate it to enable full SEO Bridge extensions.', 'kayan' ) )
					. '</p><p><a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=rank-math' ) ) . '">' . esc_html__( 'Open Rank Math', 'kayan' ) . '</a></p>',
			)
		);

		$filters = array(
			'rank_math/frontend/canonical'          => __( 'Language-first canonical rewrite', 'kayan' ),
			'rank_math/opengraph/facebook/og_locale' => __( 'OG locale from platform language', 'kayan' ),
			'rank_math/frontend/title'               => __( 'Locale SEO title override', 'kayan' ),
			'rank_math/frontend/description'         => __( 'Locale SEO description override', 'kayan' ),
			'rank_math/frontend/hreflang'             => __( 'hreflang alternates (platform-built)', 'kayan' ),
			'rank_math/front/hreflang'                => __( 'hreflang alternates (legacy filter name)', 'kayan' ),
		);
		$rows = array();
		foreach ( $filters as $hook => $label ) {
			$rows[] = array(
				'id'     => $hook,
				'hook'   => '<code>' . esc_html( $hook ) . '</code>',
				'label'  => esc_html( $label ),
				'status' => $ui->status( array( 'label' => __( 'Bridged', 'kayan' ), 'type' => 'success' ) ),
			);
		}
		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'hook'   => __( 'Rank Math filter', 'kayan' ),
					'label'  => __( 'Purpose', 'kayan' ),
					'status' => __( 'Status', 'kayan' ),
				),
				'rows'    => $rows,
			)
		);

		$schema_adapter = isset( kayan_platform()->integration ) ? kayan_platform()->integration->get_adapter( 'schema' ) : null;
		if ( $schema_adapter && method_exists( $schema_adapter, 'status' ) ) {
			$st = $schema_adapter->status();
			echo $ui->card( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'title'   => __( 'Theme schema pack', 'kayan' ),
					'status'  => ucfirst( (string) $st['state'] ),
					'content' => '<p>' . esc_html( (string) $st['notes'] ) . '</p>',
				)
			);
		}

		$countries = kayan_platform()->countries->all();
		$gtm_rows  = array();
		foreach ( $countries as $code => $data ) {
			unset( $data );
			$gtm = (string) kayan_platform()->settings->get( 'analytics.gtm_id', $code, '' );
			$gtm_rows[] = array(
				'id'      => $code,
				'country' => '<code>' . esc_html( $code ) . '</code>',
				'gtm'     => $gtm ? esc_html( $gtm ) : '—',
			);
		}
		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'country' => __( 'Country', 'kayan' ),
					'gtm'     => __( 'GTM ID', 'kayan' ),
				),
				'rows'    => $gtm_rows,
			)
		);
	}
}
