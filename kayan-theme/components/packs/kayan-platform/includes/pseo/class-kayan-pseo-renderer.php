<?php
/**
 * PSEO Renderer — turns a blueprint's blocks into front-end HTML at render
 * time (so dynamic tags like phone/whatsapp always stay fresh).
 *
 * Only touches posts that carry a `kayan_pseo_blueprint` meta value — every
 * existing post without that meta renders exactly as before (zero breaking
 * changes). Never prints SEO meta/schema/canonical — Rank Math owns that.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Renderer {

	/** @var Kayan_PSEO_Blueprint */
	private $blueprint;

	/** @var Kayan_PSEO_Storage */
	private $storage;

	/** @var Kayan_PSEO_Blocks */
	private $blocks;

	public function __construct( Kayan_PSEO_Blueprint $blueprint, Kayan_PSEO_Storage $storage, Kayan_PSEO_Blocks $blocks ) {
		$this->blueprint = $blueprint;
		$this->storage   = $storage;
		$this->blocks    = $blocks;
	}

	/**
	 * @return void
	 */
	public function register() {
		add_filter( 'the_content', array( $this, 'render' ), 20 );
	}

	/**
	 * @param string $content Existing content.
	 * @return string
	 */
	public function render( $content ) {
		if ( is_admin() || ! is_singular() ) {
			return $content;
		}

		$post = get_post();
		if ( ! $post || ! in_array( $post->post_type, $this->storage->host_post_types(), true ) ) {
			return $content;
		}

		$raw = get_post_meta( $post->ID, Kayan_PSEO_Blueprint::META_BLUEPRINT, true );
		if ( empty( $raw ) || ! is_array( $raw ) ) {
			return $content;
		}

		$blueprint = $this->blueprint->sanitize( $raw );
		if ( empty( $blueprint['blocks'] ) ) {
			return $content;
		}

		$entities = get_post_meta( $post->ID, Kayan_PSEO_Identity::META_ENTITIES, true );
		$entities = is_array( $entities ) ? $entities : array();
		$country  = get_post_meta( $post->ID, Kayan_Content_Locale::META_COUNTRIES, true );
		$country  = is_array( $country ) && ! empty( $country ) ? sanitize_key( (string) $country[0] ) : ( function_exists( 'kayan_platform_country' ) ? kayan_platform_country() : '' );
		$language = get_post_meta( $post->ID, Kayan_Content_Locale::META_LANG, true );
		$language = $language ? sanitize_key( (string) $language ) : ( function_exists( 'kayan_platform_language' ) ? kayan_platform_language() : 'ar' );

		$context = array(
			'country'   => $country,
			'language'  => $language,
			'entities'  => $entities,
			'post_id'   => $post->ID,
			'blueprint' => $blueprint,
		);

		$html = '<div class="kayan-pseo-page" data-kayan-pseo="1">';
		foreach ( $blueprint['blocks'] as $block_id => $instance ) {
			$html .= $this->render_block( $block_id, $instance, $context );
		}
		$html .= '</div>';

		/**
		 * @param string $html      Rendered blocks HTML.
		 * @param array  $blueprint Blueprint.
		 * @param array  $context   Context.
		 */
		$html = apply_filters( 'kayan_pseo_render_output', $html, $blueprint, $context );

		return $content . $html;
	}

	/**
	 * @param string               $block_id Block id.
	 * @param array<string,mixed>  $instance Block instance.
	 * @param array<string,mixed>  $context  Context.
	 * @return string
	 */
	private function render_block( $block_id, array $instance, array $context ) {
		$def  = $this->blocks->get( $block_id );
		$data = isset( $instance['data'] ) && is_array( $instance['data'] ) ? $instance['data'] : array();

		if ( ! $def || empty( $def['enabled'] ) || 'schema_source' === $block_id ) {
			return '';
		}
		if ( $this->is_effectively_empty( $data ) ) {
			return '';
		}

		$data = $this->resolve_tags_deep( $data, $context );

		$html = '';
		switch ( $block_id ) {
			case 'hero':
				$html .= '<h1>' . esc_html( (string) ( $data['headline'] ?? '' ) ) . '</h1>';
				if ( ! empty( $data['subheadline'] ) ) {
					$html .= '<p class="kayan-pseo-hero__sub">' . esc_html( (string) $data['subheadline'] ) . '</p>';
				}
				break;

			case 'cta':
				$html .= '<div class="kayan-pseo-cta">';
				if ( ! empty( $data['primary_label'] ) ) {
					$html .= '<a class="kayan-pseo-cta__primary" href="' . esc_url( (string) ( $data['primary_url'] ?? '#' ) ) . '">' . esc_html( (string) $data['primary_label'] ) . '</a>';
				}
				if ( ! empty( $data['whatsapp'] ) ) {
					$html .= '<a class="kayan-pseo-cta__whatsapp" href="https://wa.me/' . esc_attr( preg_replace( '/[^0-9]/', '', (string) $data['whatsapp'] ) ) . '">WhatsApp</a>';
				}
				$html .= '</div>';
				break;

			case 'gallery':
			case 'videos':
				$items = is_array( $data['items'] ?? null ) ? $data['items'] : array();
				if ( $items ) {
					$html .= '<div class="kayan-pseo-' . esc_attr( $block_id ) . '">';
					foreach ( $items as $item ) {
						$html .= '<figure>' . ( ! empty( $item['caption'] ) ? '<figcaption>' . esc_html( (string) $item['caption'] ) . '</figcaption>' : '' ) . '</figure>';
					}
					$html .= '</div>';
				}
				break;

			case 'faq':
				$items = is_array( $data['items'] ?? null ) ? $data['items'] : array();
				if ( $items ) {
					$html .= '<div class="kayan-pseo-faq">';
					foreach ( $items as $item ) {
						$html .= '<details><summary>' . esc_html( (string) ( $item['question'] ?? '' ) ) . '</summary><div>' . wp_kses_post( (string) ( $item['answer'] ?? '' ) ) . '</div></details>';
					}
					$html .= '</div>';
				}
				break;

			case 'pricing':
				$items = is_array( $data['items'] ?? null ) ? $data['items'] : array();
				if ( $items ) {
					$html .= '<table class="kayan-pseo-pricing"><tbody>';
					foreach ( $items as $item ) {
						$html .= '<tr><td>' . esc_html( (string) ( $item['label'] ?? '' ) ) . '</td><td>' . esc_html( (string) ( $item['price'] ?? '' ) ) . '</td></tr>';
					}
					$html .= '</tbody></table>';
				}
				break;

			case 'reviews':
			case 'related_services':
			case 'related_articles':
				$post_ids = is_array( $data['post_ids'] ?? null ) ? array_map( 'absint', $data['post_ids'] ) : array();
				if ( $post_ids ) {
					$html .= '<ul class="kayan-pseo-' . esc_attr( $block_id ) . '">';
					foreach ( $post_ids as $pid ) {
						$title = get_the_title( $pid );
						if ( ! $title ) {
							continue;
						}
						$html .= '<li><a href="' . esc_url( (string) get_permalink( $pid ) ) . '">' . esc_html( $title ) . '</a></li>';
					}
					$html .= '</ul>';
				}
				break;

			case 'related_cities':
				$term_ids = is_array( $data['term_ids'] ?? null ) ? array_map( 'absint', $data['term_ids'] ) : array();
				if ( $term_ids ) {
					$html .= '<ul class="kayan-pseo-related-cities">';
					foreach ( $term_ids as $tid ) {
						$term = get_term( $tid );
						if ( ! $term || is_wp_error( $term ) ) {
							continue;
						}
						$html .= '<li>' . esc_html( $term->name ) . '</li>';
					}
					$html .= '</ul>';
				}
				break;

			case 'areas':
			case 'internal_links':
				$items = is_array( $data['items'] ?? null ) ? $data['items'] : array();
				if ( $items ) {
					$html .= '<ul class="kayan-pseo-' . esc_attr( $block_id ) . '">';
					foreach ( $items as $item ) {
						if ( is_array( $item ) ) {
							$html .= '<li><a href="' . esc_url( (string) ( $item['url'] ?? '#' ) ) . '">' . esc_html( (string) ( $item['label'] ?? '' ) ) . '</a></li>';
						} else {
							$html .= '<li>' . esc_html( (string) $item ) . '</li>';
						}
					}
					$html .= '</ul>';
				}
				break;

			case 'map':
				if ( ! empty( $data['embed'] ) ) {
					$html .= '<div class="kayan-pseo-map">' . wp_kses_post( (string) $data['embed'] ) . '</div>';
				}
				break;

			case 'breadcrumb':
				$items = is_array( $data['items'] ?? null ) ? $data['items'] : array();
				if ( $items ) {
					$html .= '<nav class="kayan-pseo-breadcrumb"><ol>';
					foreach ( $items as $item ) {
						$html .= '<li><a href="' . esc_url( (string) ( $item['url'] ?? '#' ) ) . '">' . esc_html( (string) ( $item['label'] ?? '' ) ) . '</a></li>';
					}
					$html .= '</ol></nav>';
				}
				break;

			default:
				/**
				 * Let extensions render custom block types.
				 *
				 * @param string $html     Existing (empty) markup.
				 * @param string $block_id Block id.
				 * @param array  $data     Data.
				 * @param array  $context  Context.
				 */
				$html = apply_filters( 'kayan_pseo_render_block', '', $block_id, $data, $context );
		}

		if ( '' === $html ) {
			return '';
		}

		return '<section class="kayan-pseo-block kayan-pseo-block--' . esc_attr( $block_id ) . '">' . $html . '</section>';
	}

	/**
	 * @param array $data Data.
	 * @return bool
	 */
	private function is_effectively_empty( array $data ) {
		foreach ( $data as $value ) {
			if ( is_array( $value ) && ! empty( $value ) ) {
				return false;
			}
			if ( is_string( $value ) && '' !== trim( $value ) ) {
				return false;
			}
			if ( is_numeric( $value ) && 0 !== (int) $value ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @param array $data    Data.
	 * @param array $context Context.
	 * @return array
	 */
	private function resolve_tags_deep( array $data, array $context ) {
		if ( ! function_exists( 'kayan_tags' ) ) {
			return $data;
		}
		foreach ( $data as $key => $value ) {
			if ( is_string( $value ) && false !== strpos( $value, '{{' ) ) {
				$data[ $key ] = kayan_tags()->resolve_mixed( $value, $context );
			} elseif ( is_array( $value ) ) {
				$data[ $key ] = $this->resolve_tags_deep( $value, $context );
			}
		}
		return $data;
	}
}
