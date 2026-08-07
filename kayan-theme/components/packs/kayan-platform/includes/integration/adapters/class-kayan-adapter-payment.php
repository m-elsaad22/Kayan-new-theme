<?php
/**
 * Adapter: kayan-payment ↔ country business profile.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Adapter_Payment {

	/**
	 * @return void
	 */
	public function register() {
		add_filter( 'pre_option_kayan_invoice_company_name', array( $this, 'pre_company_name' ), 10 );
	}

	/**
	 * @param mixed $pre Pre value.
	 * @return mixed
	 */
	public function pre_company_name( $pre ) {
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
		$busy = true;
		$name = Kayan_Theme_Integration::profile_field( 'business_name', '' );
		$busy = false;
		if ( is_string( $name ) && '' !== $name ) {
			return $name;
		}
		return $pre;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function status() {
		$active = class_exists( 'Kayan_Payment', false );
		return array(
			'id'     => 'payment',
			'state'  => $active ? 'adapter' : 'idle',
			'active' => $active,
			'bridge' => 'kayan_invoice_company_name → business_name',
			'notes'  => 'Payment pack unchanged; company name prefers country profile.',
		);
	}
}
