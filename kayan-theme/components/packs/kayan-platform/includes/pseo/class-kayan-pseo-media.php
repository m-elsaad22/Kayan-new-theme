<?php
/**
 * PSEO Media Engine — media contract for blueprints / blocks.
 *
 * Supports featured, OG, gallery, video, icons, ALT, caption,
 * and future AI image generation — without architecture changes.
 *
 * Does not generate media in this phase.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Media {

	const META_MEDIA = 'kayan_pseo_media';

	/**
	 * @return void
	 */
	public function register() {
		/**
		 * @param Kayan_PSEO_Media $media Media engine.
		 */
		do_action( 'kayan_pseo_media_registered', $this );
	}

	/**
	 * Canonical media schema.
	 *
	 * @return array<string,mixed>
	 */
	public function schema() {
		$schema = array(
			'featured_image_id' => 0,
			'og_image_id'       => 0,
			'gallery'           => array(
				// array( 'id' => 0, 'alt' => '', 'caption' => '' )
			),
			'videos'            => array(
				// array( 'url' => '', 'provider' => '', 'poster_id' => 0, 'title' => '', 'caption' => '' )
			),
			'icons'             => array(
				// array( 'id' => 0, 'alt' => '', 'role' => '' )
			),
			'ai'                => array(
				'enabled'        => false,
				'provider'       => '',
				'model'          => '',
				'prompt'         => '',
				'prompt_version' => 1,
				'last_generated' => '',
			),
		);

		/**
		 * @param array $schema Schema.
		 */
		return apply_filters( 'kayan_pseo_media_schema', $schema );
	}

	/**
	 * Empty media item helpers.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function item_schemas() {
		return array(
			'gallery_item' => array(
				'id'      => 0,
				'alt'     => '',
				'caption' => '',
			),
			'video_item'   => array(
				'url'       => '',
				'provider'  => '',
				'poster_id' => 0,
				'title'     => '',
				'caption'   => '',
			),
			'icon_item'    => array(
				'id'   => 0,
				'alt'  => '',
				'role' => '',
			),
		);
	}

	/**
	 * @param mixed $value Raw.
	 * @return array<string,mixed>
	 */
	public function sanitize( $value ) {
		$base = $this->schema();
		if ( ! is_array( $value ) ) {
			return $base;
		}

		$out                        = $base;
		$out['featured_image_id']   = absint( $value['featured_image_id'] ?? 0 );
		$out['og_image_id']         = absint( $value['og_image_id'] ?? 0 );
		$out['gallery']             = $this->sanitize_gallery( $value['gallery'] ?? array() );
		$out['videos']              = $this->sanitize_videos( $value['videos'] ?? array() );
		$out['icons']               = $this->sanitize_icons( $value['icons'] ?? array() );

		if ( isset( $value['ai'] ) && is_array( $value['ai'] ) ) {
			$out['ai']['enabled']        = ! empty( $value['ai']['enabled'] );
			$out['ai']['provider']       = sanitize_key( (string) ( $value['ai']['provider'] ?? '' ) );
			$out['ai']['model']          = sanitize_text_field( (string) ( $value['ai']['model'] ?? '' ) );
			$out['ai']['prompt']         = sanitize_textarea_field( (string) ( $value['ai']['prompt'] ?? '' ) );
			$out['ai']['prompt_version'] = absint( $value['ai']['prompt_version'] ?? 1 );
			$out['ai']['last_generated'] = sanitize_text_field( (string) ( $value['ai']['last_generated'] ?? '' ) );
		}

		return $out;
	}

	/**
	 * Future AI image generation stub.
	 *
	 * @param array<string,mixed> $context Context.
	 * @return array{ok:bool,errors:string[]}
	 */
	public function generate_ai_image( array $context ) {
		unset( $context );
		return array(
			'ok'     => false,
			'errors' => array( 'ai_media_not_implemented_in_phase_2_5' ),
		);
	}

	/**
	 * @param mixed $items Items.
	 * @return array<int,array<string,mixed>>
	 */
	private function sanitize_gallery( $items ) {
		if ( ! is_array( $items ) ) {
			return array();
		}
		$out = array();
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$out[] = array(
				'id'      => absint( $item['id'] ?? 0 ),
				'alt'     => sanitize_text_field( (string) ( $item['alt'] ?? '' ) ),
				'caption' => sanitize_text_field( (string) ( $item['caption'] ?? '' ) ),
			);
		}
		return $out;
	}

	/**
	 * @param mixed $items Items.
	 * @return array<int,array<string,mixed>>
	 */
	private function sanitize_videos( $items ) {
		if ( ! is_array( $items ) ) {
			return array();
		}
		$out = array();
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$out[] = array(
				'url'       => esc_url_raw( (string) ( $item['url'] ?? '' ) ),
				'provider'  => sanitize_key( (string) ( $item['provider'] ?? '' ) ),
				'poster_id' => absint( $item['poster_id'] ?? 0 ),
				'title'     => sanitize_text_field( (string) ( $item['title'] ?? '' ) ),
				'caption'   => sanitize_text_field( (string) ( $item['caption'] ?? '' ) ),
			);
		}
		return $out;
	}

	/**
	 * @param mixed $items Items.
	 * @return array<int,array<string,mixed>>
	 */
	private function sanitize_icons( $items ) {
		if ( ! is_array( $items ) ) {
			return array();
		}
		$out = array();
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$out[] = array(
				'id'   => absint( $item['id'] ?? 0 ),
				'alt'  => sanitize_text_field( (string) ( $item['alt'] ?? '' ) ),
				'role' => sanitize_key( (string) ( $item['role'] ?? '' ) ),
			);
		}
		return $out;
	}
}
