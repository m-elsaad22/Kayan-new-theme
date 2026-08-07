<?php
/**
 * Adapter: header lang switcher hook ↔ kayan-i18n switcher.
 *
 * Wires do_action('rukn_v3_lang_switcher') to existing kayan_i18n_render_switcher().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Adapter_I18n_Switcher {

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'rukn_v3_lang_switcher', array( $this, 'render' ), 10 );
	}

	/**
	 * @return void
	 */
	public function render() {
		if ( function_exists( 'kayan_i18n_is_enabled' ) && ! kayan_i18n_is_enabled() ) {
			return;
		}
		if ( function_exists( 'yc_get_option' ) && ! empty( yc_get_option( 'kayan_i18n_disable' ) ) ) {
			return;
		}
		if ( ! function_exists( 'kayan_i18n_render_switcher' ) ) {
			return;
		}
		kayan_i18n_render_switcher(
			array(
				'instance_suffix' => 'header',
				'btn_class'       => 'lang-switcher-btn',
			)
		);
	}

	/**
	 * @return array<string,mixed>
	 */
	public function status() {
		return array(
			'id'     => 'i18n_switcher',
			'state'  => function_exists( 'kayan_i18n_render_switcher' ) ? 'adapter' : 'idle',
			'hook'   => 'rukn_v3_lang_switcher',
			'render' => 'kayan_i18n_render_switcher',
			'notes'  => 'No new switcher UI — connects existing header action to existing i18n renderer.',
		);
	}
}
