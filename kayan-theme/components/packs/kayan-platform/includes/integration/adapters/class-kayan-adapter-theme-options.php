<?php
/**
 * Adapter: Theme Options (YTS) ↔ Country Settings.
 *
 * Frontend option bridges only. Does not migrate writes or create settings UI.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Adapter_Theme_Options {

	/** @var array<string,string> Theme option key => profile field */
	private $bridges = array(
		'phonenumber'     => 'phone',
		'whatsapp_number' => 'whatsapp',
		'company__mail'   => 'email',
		'sitename'        => 'business_name',
		'logo__data'      => 'logo',
	);

	/**
	 * @return void
	 */
	public function register() {
		foreach ( $this->bridges as $option => $field ) {
			add_filter(
				'option_' . $option,
				function ( $value ) use ( $field ) {
					return $this->bridge_option( $value, $field );
				},
				20
			);
		}
	}

	/**
	 * @param mixed  $value Stored option.
	 * @param string $field Profile field.
	 * @return mixed
	 */
	private function bridge_option( $value, $field ) {
		if ( Kayan_Theme_Integration::skip_frontend_option_bridge() ) {
			return $value;
		}

		static $busy = false;
		if ( $busy ) {
			return $value;
		}
		$busy = true;
		$profile = Kayan_Theme_Integration::profile_field( $field, null );
		$busy    = false;

		if ( null !== $profile && false !== $profile && '' !== $profile ) {
			return $profile;
		}
		return $value;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function status() {
		return array(
			'id'          => 'theme_options',
			'state'       => 'adapter',
			'bridges'     => array_keys( $this->bridges ),
			'admin_slug'  => 'YTS',
			'notes'       => 'Frontend dual-read from country profiles; Theme Options remains write source.',
		);
	}
}
