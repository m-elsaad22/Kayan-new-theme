<?php
/**
 * Adapter: Admin Platform modules → existing theme admin screens.
 *
 * Phase 3 built real screens for Countries / Languages / Rank Math inside
 * the Admin Platform, so those are no longer bridged here. Analytics and
 * Tools remain thin links to KAYAN Track / Theme Options (out of Phase 3
 * scope), avoiding a second tracking/settings UI.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Adapter_Admin_Bridges {

	/**
	 * @return void
	 */
	public function register() {
		add_filter( 'kayan_admin_modules', array( $this, 'bridge_modules' ), 50 );
	}

	/**
	 * @param array<string,array<string,mixed>> $modules Modules.
	 * @return array<string,array<string,mixed>>
	 */
	public function bridge_modules( $modules ) {
		if ( ! is_array( $modules ) ) {
			return $modules;
		}

		$bridges = array(
			'analytics'  => array(
				'label' => __( 'KAYAN Track (existing)', 'kayan' ),
				'url'   => admin_url( 'admin.php?page=kayan-track-pro' ),
				'note'  => __( 'Tracking remains in KAYAN Track. Admin Platform analytics module is a bridge only.', 'kayan' ),
			),
			'tools'      => array(
				'label' => __( 'Theme Options (existing)', 'kayan' ),
				'url'   => admin_url( 'admin.php?page=YTS' ),
				'note'  => __( 'Use existing Theme Options (YTS) for theme settings.', 'kayan' ),
			),
		);

		// Booking is not a core admin module id — expose via tools description if pack present.
		foreach ( $bridges as $id => $bridge ) {
			if ( ! isset( $modules[ $id ] ) ) {
				continue;
			}
			$modules[ $id ]['description'] = $bridge['note'];
			$modules[ $id ]['screen']      = function ( $module, $context ) use ( $bridge ) {
				unset( $module, $context );
				$this->render_bridge( $bridge );
			};
		}

		/**
		 * Optional booking bridge card attached to tools module meta — keep tools bridge as YTS.
		 * If booking pack exists, append link in tools screen via filter note only.
		 */
		if ( isset( $modules['tools'] ) && class_exists( 'Kayan_Booking', false ) ) {
			$modules['tools']['bridge_extra'] = array(
				'booking' => admin_url( 'admin.php?page=kayan-bookings' ),
			);
			$extra = $modules['tools']['bridge_extra'];
			$base  = $bridges['tools'];
			$modules['tools']['screen'] = function ( $module, $context ) use ( $base, $extra ) {
				unset( $module, $context );
				$this->render_bridge( $base, $extra );
			};
		}

		return $modules;
	}

	/**
	 * @param array<string,string>      $bridge Bridge.
	 * @param array<string,string>|null $extra  Extra links.
	 * @return void
	 */
	private function render_bridge( array $bridge, $extra = null ) {
		echo '<div class="wrap kayan-admin-bridge">';
		echo '<h1>' . esc_html__( 'Existing theme integration', 'kayan' ) . '</h1>';
		echo '<p>' . esc_html( $bridge['note'] ) . '</p>';
		echo '<p><a class="button button-primary" href="' . esc_url( $bridge['url'] ) . '">' . esc_html( $bridge['label'] ) . '</a></p>';
		if ( is_array( $extra ) ) {
			foreach ( $extra as $key => $url ) {
				$label = 'booking' === $key
					? __( 'Bookings (existing)', 'kayan' )
					: ucfirst( (string) $key );
				echo '<p><a class="button" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></p>';
			}
		}
		echo '<p class="description">' . esc_html__( 'This module bridges to an existing screen rather than duplicating it.', 'kayan' ) . '</p>';
		echo '</div>';
	}

	/**
	 * @return array<string,mixed>
	 */
	public function status() {
		return array(
			'id'      => 'admin_bridges',
			'state'   => 'adapter',
			'bridges' => array( 'analytics', 'tools' ),
			'notes'   => 'Links remaining placeholder modules (analytics, tools) to existing Track / Theme Options screens. Countries, Languages, and Rank Math are now real Admin Platform modules (Phase 3).',
		);
	}
}
