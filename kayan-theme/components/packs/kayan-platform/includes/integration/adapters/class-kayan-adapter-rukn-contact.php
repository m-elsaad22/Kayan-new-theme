<?php
/**
 * Adapter: RuknContact ↔ platform CPTs + country contact settings.
 *
 * Extends post type list. Phone/WhatsApp come from Theme Options adapter bridges.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Adapter_Rukn_Contact {

	/**
	 * @return void
	 */
	public function register() {
		add_filter( 'rukn_cs_post_types', array( $this, 'extend_post_types' ), 20 );
	}

	/**
	 * @param string[] $types Post types.
	 * @return string[]
	 */
	public function extend_post_types( $types ) {
		if ( ! is_array( $types ) ) {
			$types = array();
		}
		$extra = array( 'services', 'reviews', 'faqs', 'pricing', 'portfolio', 'before_after', 'page', 'post' );
		/**
		 * @param string[] $extra Extra post types for RuknContact.
		 */
		$extra = apply_filters( 'kayan_adapter_rukn_contact_post_types', $extra );
		return array_values( array_unique( array_merge( $types, $extra ) ) );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function status() {
		$active = class_exists( 'Rukn_Contact_System', false );
		return array(
			'id'     => 'rukn_contact',
			'state'  => $active ? 'extension' : 'idle',
			'active' => $active,
			'notes'  => 'Extends rukn_cs_post_types; contact numbers via Theme Options adapter.',
		);
	}
}
