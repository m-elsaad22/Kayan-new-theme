<?php
/**
 * Admin module: Blocks (Block Engine).
 *
 * Read-only view over the existing Kayan_PSEO_Blocks registry.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Blocks {

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
			'blocks',
			array(
				'label'       => __( 'Blocks', 'kayan' ),
				'description' => __( 'Reusable, independently regeneratable page blocks.', 'kayan' ),
				'icon'        => 'dashicons-block-default',
				'position'    => 40,
				'capability'  => 'kayan_manage_blocks',
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
		$ui     = $context['ui'];
		$blocks = kayan_pseo()->blocks->all();

		$rows = array();
		foreach ( $blocks as $id => $block ) {
			$rows[] = array(
				'id'       => $id,
				'block'    => '<code>' . esc_html( $id ) . '</code>',
				'label'    => esc_html( (string) $block['label'] ),
				'media'    => $block['supports_media'] ? $ui->status( array( 'label' => __( 'Yes', 'kayan' ), 'type' => 'info' ) ) : '—',
				'ai'       => $block['ai_enabled'] ? $ui->status( array( 'label' => __( 'Phase 5', 'kayan' ), 'type' => 'neutral' ) ) : '—',
				'rankmath' => $block['rank_math_related'] ? $ui->status( array( 'label' => __( 'Data source', 'kayan' ), 'type' => 'success' ) ) : '—',
			);
		}

		echo $ui->notice( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'type'    => 'info',
				'message' => __( 'AI-authored block content arrives in Phase 5. The Generator can already refresh derived fields (contact info, related entities) via Regenerate.', 'kayan' ),
			)
		);

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'block'    => __( 'Block', 'kayan' ),
					'label'    => __( 'Label', 'kayan' ),
					'media'    => __( 'Media', 'kayan' ),
					'ai'       => __( 'AI content', 'kayan' ),
					'rankmath' => __( 'Rank Math', 'kayan' ),
				),
				'rows'    => $rows,
			)
		);
	}
}
