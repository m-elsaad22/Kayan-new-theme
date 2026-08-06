<?php
/**
 * Admin module: Templates (Template Engine).
 *
 * Read-only view over the existing Kayan_PSEO_Templates registry —
 * templates are code-registered (extend via `kayan_pseo_register_templates`),
 * this screen just makes the composition visible/inspectable.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Templates {

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
			'templates',
			array(
				'label'       => __( 'Templates', 'kayan' ),
				'description' => __( 'Assignable page structures composed of blocks.', 'kayan' ),
				'icon'        => 'dashicons-layout',
				'position'    => 30,
				'capability'  => 'kayan_manage_templates',
				'group'       => 'pseo',
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
		$ui        = $context['ui'];
		$templates = kayan_pseo()->templates->all();
		$patterns  = kayan_pseo()->patterns->all();

		$rows = array();
		foreach ( $templates as $id => $tpl ) {
			$preferred = array();
			foreach ( (array) $tpl['preferred_for'] as $pattern_id ) {
				$preferred[] = isset( $patterns[ $pattern_id ] ) ? $patterns[ $pattern_id ]['label'] : $pattern_id;
			}
			$rows[] = array(
				'id'        => $id,
				'template'  => '<code>' . esc_html( $id ) . '</code>',
				'label'     => esc_html( (string) $tpl['label'] ),
				'version'   => esc_html( (string) $tpl['version'] ),
				'blocks'    => esc_html( (string) count( (array) $tpl['blocks'] ) ),
				'preferred' => esc_html( implode( ', ', $preferred ) ),
				'status'    => $ui->status( array( 'label' => ! empty( $tpl['enabled'] ) ? __( 'Enabled', 'kayan' ) : __( 'Disabled', 'kayan' ), 'type' => ! empty( $tpl['enabled'] ) ? 'success' : 'neutral' ) ),
			);
		}

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'template'  => __( 'Template', 'kayan' ),
					'label'     => __( 'Label', 'kayan' ),
					'version'   => __( 'Version', 'kayan' ),
					'blocks'    => __( 'Block count', 'kayan' ),
					'preferred' => __( 'Preferred for patterns', 'kayan' ),
					'status'    => __( 'Status', 'kayan' ),
				),
				'rows'    => $rows,
			)
		);

		$detail_id = isset( $_GET['template_id'] ) ? sanitize_key( wp_unslash( $_GET['template_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $detail_id && isset( $templates[ $detail_id ] ) ) {
			$tpl = $templates[ $detail_id ];
			echo $ui->card( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'title'   => sprintf( /* translators: %s: template label */ __( 'Block order: %s', 'kayan' ), $tpl['label'] ),
					'content' => '<ol><li>' . implode( '</li><li>', array_map( 'esc_html', (array) $tpl['blocks'] ) ) . '</li></ol>',
				)
			);
		}
	}
}
