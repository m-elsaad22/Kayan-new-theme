<?php
/**
 * Adapter: kayan-booking ↔ country currency / contact.
 *
 * Uses pre_option bridges only — booking pack remains the booking system.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Adapter_Booking {

	/**
	 * @return void
	 */
	public function register() {
		add_filter( 'pre_option_kayan_booking_currency', array( $this, 'pre_currency' ), 10 );
		// WhatsApp for booking confirm URL uses get_option('whatsapp_number') — Theme Options adapter covers it.
	}

	/**
	 * @param mixed $pre Pre value.
	 * @return mixed
	 */
	public function pre_currency( $pre ) {
		if ( false !== $pre && null !== $pre ) {
			return $pre;
		}
		if ( Kayan_Theme_Integration::skip_frontend_option_bridge() ) {
			return $pre;
		}
		static $busy = false;
		if ( $busy ) {
			return $pre;
		}
		$busy     = true;
		$currency = Kayan_Theme_Integration::profile_field( 'currency', '' );
		$busy     = false;
		if ( is_string( $currency ) && '' !== $currency ) {
			return $currency;
		}
		return $pre;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function status() {
		$active = class_exists( 'Kayan_Booking', false );
		return array(
			'id'     => 'booking',
			'state'  => $active ? 'adapter' : 'idle',
			'active' => $active,
			'bridge' => 'kayan_booking_currency → country profile currency',
			'notes'  => 'No second booking stack. WhatsApp via theme_options adapter.',
		);
	}
}
