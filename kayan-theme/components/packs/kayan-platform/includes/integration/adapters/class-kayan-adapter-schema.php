<?php
/**
 * Adapter: Theme Schema pack ↔ Rank Math (only SEO engine).
 *
 * When Rank Math is active, disable theme JSON-LD via existing validate__schema kill switch.
 * Does not replace schema pack or invent a second schema system.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Adapter_Schema {

	/**
	 * @return void
	 */
	public function register() {
		add_filter( 'pre_option_validate__schema', array( $this, 'pre_validate_schema' ), 5 );
	}

	/**
	 * Non-empty validate__schema disables YourColor__Schema::insert__schema.
	 *
	 * @param mixed $pre Pre value.
	 * @return mixed
	 */
	public function pre_validate_schema( $pre ) {
		if ( false !== $pre && null !== $pre ) {
			return $pre;
		}
		if ( Kayan_Theme_Integration::rank_math_active() ) {
			return 'rank_math';
		}
		return $pre;
	}

	/**
	 * @return array<string,mixed>
	 */
	public function status() {
		$rm = Kayan_Theme_Integration::rank_math_active();
		return array(
			'id'              => 'schema',
			'state'           => $rm ? 'adapter' : 'compatible_idle',
			'rank_math'       => $rm,
			'kill_switch'     => 'validate__schema',
			'theme_schema_on' => ! $rm && empty( get_option( 'validate__schema' ) ),
			'notes'           => 'Theme schema silenced when Rank Math active; pack code unchanged.',
		);
	}
}
