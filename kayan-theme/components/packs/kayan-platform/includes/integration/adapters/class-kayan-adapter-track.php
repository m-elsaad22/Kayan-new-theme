<?php
/**
 * Adapter: kayan-track / DNI ↔ Theme Options phone keys.
 *
 * DNI reads contact_number (often empty). Bridge to phonenumber / country phone.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Adapter_Track {

	/**
	 * @return void
	 */
	public function register() {
		add_filter( 'option_contact_number', array( $this, 'bridge_contact_number' ), 20 );
		add_filter( 'pre_option_contact_number', array( $this, 'pre_contact_number' ), 10 );
	}

	/**
	 * @param mixed $pre Pre value.
	 * @return mixed
	 */
	public function pre_contact_number( $pre ) {
		if ( false !== $pre && null !== $pre && '' !== $pre ) {
			return $pre;
		}
		if ( Kayan_Theme_Integration::skip_frontend_option_bridge() ) {
			return $pre;
		}
		static $busy = false;
		if ( $busy ) {
			return $pre;
		}
		$busy  = true;
		$phone = Kayan_Theme_Integration::profile_field( 'phone', '' );
		$busy  = false;
		if ( is_string( $phone ) && '' !== $phone ) {
			return $phone;
		}
		return $pre;
	}

	/**
	 * @param mixed $value Stored value.
	 * @return mixed
	 */
	public function bridge_contact_number( $value ) {
		if ( ! empty( $value ) ) {
			return $value;
		}
		if ( Kayan_Theme_Integration::skip_frontend_option_bridge() ) {
			return $value;
		}
		static $busy = false;
		if ( $busy ) {
			return $value;
		}
		$busy = true;
		$phone = Kayan_Theme_Integration::profile_field( 'phone', '' );
		if ( '' === $phone || null === $phone ) {
			// Theme Options adapter may already bridge phonenumber → profile.
			$phone = get_option( 'phonenumber', '' );
		}
		$busy = false;
		return ( '' !== $phone && false !== $phone ) ? $phone : $value;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function status() {
		$active = defined( 'KAYAN_TRACK_VERSION' ) || class_exists( 'Kayan_DNI', false );
		return array(
			'id'     => 'track',
			'state'  => $active ? 'adapter' : 'idle',
			'active' => $active,
			'bridge' => 'contact_number → phone / phonenumber',
			'notes'  => 'Fixes DNI key mismatch without changing track pack storage.',
		);
	}
}
