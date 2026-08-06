<?php
/**
 * Dynamic Data Tag system — {{tag}} tokens for templates & future AI.
 *
 * Templates and AI must consume tags instead of hardcoded values.
 * Resolvers use Entity API / Relationships / Country Settings / Rank Math
 * source fields. Architecture only — no generation, no template redesign.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Dynamic_Data_Tags {

	/** @var array<string,array<string,mixed>> */
	private $tags = array();

	/** @var Kayan_Entity_Engine */
	private $entities;

	/** @var Kayan_Country_Settings */
	private $settings;

	/** @var Kayan_Country_Engine */
	private $countries;

	public function __construct(
		Kayan_Entity_Engine $entities,
		Kayan_Country_Settings $settings,
		Kayan_Country_Engine $countries
	) {
		$this->entities  = $entities;
		$this->settings  = $settings;
		$this->countries = $countries;
	}

	/**
	 * @return void
	 */
	public function register() {
		$this->register_core_tags();

		/**
		 * @param Kayan_Dynamic_Data_Tags $tags Tags registry.
		 */
		do_action( 'kayan_register_data_tags', $this );
	}

	/**
	 * @param string              $tag  Tag id without braces.
	 * @param array<string,mixed> $args Args: label, callback, entity, field, …
	 * @return void
	 */
	public function register_tag( $tag, array $args ) {
		$tag = $this->normalize_tag( $tag );
		if ( '' === $tag ) {
			return;
		}

		$defaults = array(
			'label'       => $tag,
			'description' => '',
			'callback'    => null, // callable( $context, $tag_def ): string|array
			'entity'      => '',   // shortcut: pull entity field
			'field'       => '',
			'format'      => 'text', // text|html|list|url|number
			'group'       => 'general',
		);

		$this->tags[ $tag ] = array_merge( $defaults, $args, array( 'id' => $tag ) );
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function all() {
		/**
		 * @param array $tags Tags.
		 */
		return apply_filters( 'kayan_data_tags', $this->tags );
	}

	/**
	 * @param string $tag Tag.
	 * @return array<string,mixed>|null
	 */
	public function get( $tag ) {
		$tag  = $this->normalize_tag( $tag );
		$tags = $this->all();
		return isset( $tags[ $tag ] ) ? $tags[ $tag ] : null;
	}

	/**
	 * @param string $tag Tag.
	 * @return bool
	 */
	public function has( $tag ) {
		return null !== $this->get( $tag );
	}

	/**
	 * Expand all {{tags}} in a string.
	 *
	 * @param string              $template Template with {{tags}}.
	 * @param array<string,mixed> $context  Context.
	 * @return string
	 */
	public function resolve( $template, array $context = array() ) {
		$template = (string) $template;
		if ( '' === $template || false === strpos( $template, '{{' ) ) {
			return $template;
		}

		$context = $this->normalize_context( $context );

		return (string) preg_replace_callback(
			'/\{\{\s*([a-z0-9_]+)\s*\}\}/i',
			function ( $matches ) use ( $context ) {
				$value = $this->resolve_tag( $matches[1], $context );
				return $this->stringify( $value );
			},
			$template
		);
	}

	/**
	 * Resolve a single tag.
	 *
	 * @param string              $tag     Tag.
	 * @param array<string,mixed> $context Context.
	 * @return mixed
	 */
	public function resolve_tag( $tag, array $context = array() ) {
		$tag     = $this->normalize_tag( $tag );
		$context = $this->normalize_context( $context );
		$def     = $this->get( $tag );

		if ( ! $def ) {
			/**
			 * Unknown tag fallback.
			 *
			 * @param mixed  $value   Value.
			 * @param string $tag     Tag.
			 * @param array  $context Context.
			 */
			return apply_filters( 'kayan_data_tag_missing', '', $tag, $context );
		}

		$value = '';
		if ( is_callable( $def['callback'] ) ) {
			$value = call_user_func( $def['callback'], $context, $def );
		} elseif ( ! empty( $def['entity'] ) ) {
			$ref = $this->entity_ref( $context, (string) $def['entity'] );
			if ( $ref ) {
				$field = ! empty( $def['field'] ) ? (string) $def['field'] : 'name';
				if ( 'name' === $field ) {
					$value = $this->entities->name( $def['entity'], $ref );
				} else {
					$value = $this->entities->field( $def['entity'], $ref, $field, '' );
				}
			}
		}

		/**
		 * @param mixed  $value   Value.
		 * @param string $tag     Tag.
		 * @param array  $context Context.
		 * @param array  $def     Def.
		 */
		return apply_filters( 'kayan_data_tag_value', $value, $tag, $context, $def );
	}

	/**
	 * Also expand legacy {token} placeholders used by PSEO prompts.
	 * Maps {service} → service_name when possible; keeps unresolved tokens.
	 *
	 * @param string              $template Template.
	 * @param array<string,mixed> $context  Context.
	 * @return string
	 */
	public function resolve_mixed( $template, array $context = array() ) {
		$out = $this->resolve( $template, $context );
		$context = $this->normalize_context( $context );

		$legacy_map = array(
			'service'  => 'service_name',
			'city'     => 'city_name',
			'country'  => 'country_name',
			'language' => 'language',
			'category' => 'category_name',
			'faq'      => 'faq',
			'pricing'  => 'price_from',
		);

		return (string) preg_replace_callback(
			'/\{([a-z0-9_]+)\}/i',
			function ( $matches ) use ( $context, $legacy_map ) {
				$key = strtolower( $matches[1] );
				if ( isset( $legacy_map[ $key ] ) && $this->has( $legacy_map[ $key ] ) ) {
					return $this->stringify( $this->resolve_tag( $legacy_map[ $key ], $context ) );
				}
				if ( $this->has( $key ) ) {
					return $this->stringify( $this->resolve_tag( $key, $context ) );
				}
				// Entity name shortcut: {service_slug} style left alone; {service} already mapped.
				$ref = $this->entity_ref( $context, $key );
				if ( $ref ) {
					return $this->entities->name( $key, $ref );
				}
				return $matches[0];
			},
			$out
		);
	}

	/**
	 * Context contract for tag resolution.
	 *
	 * @param array<string,mixed> $context Context.
	 * @return array<string,mixed>
	 */
	public function normalize_context( array $context ) {
		$base = array(
			'country'   => '',
			'language'  => 'ar',
			'entities'  => array(), // type => ref
			'post_id'   => 0,
			'blueprint' => null,
			'tokens'    => array(), // optional precomputed overrides
		);
		$out = array_merge( $base, $context );
		$out['country']  = sanitize_key( (string) $out['country'] );
		$out['language'] = sanitize_key( (string) $out['language'] );
		$out['post_id']  = absint( $out['post_id'] );
		if ( ! is_array( $out['entities'] ) ) {
			$out['entities'] = array();
		}
		if ( ! is_array( $out['tokens'] ) ) {
			$out['tokens'] = array();
		}

		// Convenience: fill country entity ref.
		if ( $out['country'] && empty( $out['entities']['country'] ) ) {
			$out['entities']['country'] = $out['country'];
		}

		/**
		 * @param array $out Context.
		 */
		return apply_filters( 'kayan_data_tag_context', $out );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'tags'       => array_keys( $this->all() ),
			'syntax'     => '{{tag_name}}',
			'context'    => array( 'country', 'language', 'entities', 'post_id', 'blueprint', 'tokens' ),
			'apis'       => array(
				'resolve'      => 'kayan_tags()->resolve( $template, $context )',
				'resolve_tag'  => 'kayan_tags()->resolve_tag( $tag, $context )',
				'resolve_mixed'=> 'kayan_tags()->resolve_mixed( $template, $context )',
				'register_tag' => 'kayan_tags()->register_tag( $tag, $args )',
			),
			'consumers'  => array( 'pseo_templates', 'pseo_blocks_ai', 'future_ai', 'future_renderers' ),
			'generation' => false,
		);
	}

	/**
	 * @return void
	 */
	private function register_core_tags() {
		// Entity names.
		$this->register_tag(
			'service_name',
			array(
				'label'  => 'Service Name',
				'group'  => 'entity',
				'entity' => 'service',
				'field'  => 'name',
			)
		);
		$this->register_tag(
			'city_name',
			array(
				'label'  => 'City Name',
				'group'  => 'entity',
				'entity' => 'city',
				'field'  => 'name',
			)
		);
		$this->register_tag(
			'country_name',
			array(
				'label'  => 'Country Name',
				'group'  => 'entity',
				'entity' => 'country',
				'field'  => 'name',
			)
		);
		$this->register_tag(
			'category_name',
			array(
				'label'  => 'Category Name',
				'group'  => 'entity',
				'entity' => 'category',
				'field'  => 'name',
			)
		);
		$this->register_tag(
			'language',
			array(
				'label'    => 'Language',
				'group'    => 'context',
				'callback' => static function ( $context ) {
					return isset( $context['language'] ) ? (string) $context['language'] : 'ar';
				},
			)
		);

		// Content / media.
		$this->register_tag(
			'faq',
			array(
				'label'    => 'FAQ',
				'group'    => 'content',
				'format'   => 'list',
				'callback' => array( $this, 'tag_faq' ),
			)
		);
		$this->register_tag(
			'gallery',
			array(
				'label'    => 'Gallery',
				'group'    => 'media',
				'format'   => 'list',
				'callback' => array( $this, 'tag_gallery' ),
			)
		);
		$this->register_tag(
			'featured_image',
			array(
				'label'    => 'Featured Image',
				'group'    => 'media',
				'format'   => 'url',
				'callback' => array( $this, 'tag_featured_image' ),
			)
		);
		$this->register_tag(
			'price_from',
			array(
				'label'    => 'Price From',
				'group'    => 'commerce',
				'callback' => array( $this, 'tag_price_from' ),
			)
		);

		// Contact (country settings).
		$this->register_tag(
			'phone',
			array(
				'label'    => 'Phone',
				'group'    => 'contact',
				'callback' => array( $this, 'tag_phone' ),
			)
		);
		$this->register_tag(
			'whatsapp',
			array(
				'label'    => 'WhatsApp',
				'group'    => 'contact',
				'callback' => array( $this, 'tag_whatsapp' ),
			)
		);

		// Social proof.
		$this->register_tag(
			'average_rating',
			array(
				'label'    => 'Average Rating',
				'group'    => 'reviews',
				'format'   => 'number',
				'callback' => array( $this, 'tag_average_rating' ),
			)
		);
		$this->register_tag(
			'review_count',
			array(
				'label'    => 'Review Count',
				'group'    => 'reviews',
				'format'   => 'number',
				'callback' => array( $this, 'tag_review_count' ),
			)
		);

		// Related.
		$this->register_tag(
			'related_services',
			array(
				'label'    => 'Related Services',
				'group'    => 'related',
				'format'   => 'list',
				'callback' => array( $this, 'tag_related_services' ),
			)
		);
		$this->register_tag(
			'related_articles',
			array(
				'label'    => 'Related Articles',
				'group'    => 'related',
				'format'   => 'list',
				'callback' => array( $this, 'tag_related_articles' ),
			)
		);

		// Blueprint / block sourced (future AI + templates).
		$this->register_tag(
			'cta_title',
			array(
				'label'    => 'CTA Title',
				'group'    => 'blueprint',
				'callback' => array( $this, 'tag_blueprint_block_field' ),
				'block'    => 'cta',
				'path'     => array( 'primary_label' ),
			)
		);
		$this->register_tag(
			'hero_title',
			array(
				'label'    => 'Hero Title',
				'group'    => 'blueprint',
				'callback' => array( $this, 'tag_blueprint_block_field' ),
				'block'    => 'hero',
				'path'     => array( 'headline' ),
			)
		);

		// Rank Math compatible source (read-only — RM remains SEO engine).
		$this->register_tag(
			'meta_title',
			array(
				'label'    => 'Meta Title',
				'group'    => 'seo',
				'callback' => array( $this, 'tag_meta_title' ),
			)
		);
	}

	/**
	 * @param array $context Context.
	 * @return string
	 */
	public function tag_faq( array $context ) {
		if ( ! empty( $context['tokens']['faq'] ) ) {
			return $this->stringify( $context['tokens']['faq'] );
		}
		$service = $this->entity_ref( $context, 'service' );
		if ( $service ) {
			$faqs = $this->entities->related( 'service', $service, 'faq' );
			$names = array();
			foreach ( $faqs as $faq ) {
				$names[] = $faq['name'];
			}
			if ( $names ) {
				return implode( ', ', $names );
			}
		}
		$faq = $this->entity_ref( $context, 'faq' );
		return $faq ? $this->entities->name( 'faq', $faq ) : '';
	}

	/**
	 * @param array $context Context.
	 * @return string
	 */
	public function tag_gallery( array $context ) {
		if ( ! empty( $context['tokens']['gallery'] ) ) {
			return $this->stringify( $context['tokens']['gallery'] );
		}
		if ( ! empty( $context['blueprint']['media']['gallery'] ) && is_array( $context['blueprint']['media']['gallery'] ) ) {
			$ids = array();
			foreach ( $context['blueprint']['media']['gallery'] as $item ) {
				if ( is_array( $item ) && ! empty( $item['id'] ) ) {
					$ids[] = (int) $item['id'];
				}
			}
			return implode( ',', $ids );
		}
		$service = $this->entity_ref( $context, 'service' );
		if ( $service ) {
			$media = $this->entities->api->get_media( 'service', $service );
			if ( ! empty( $media['gallery'] ) && is_array( $media['gallery'] ) ) {
				return $this->stringify( $media['gallery'] );
			}
		}
		return '';
	}

	/**
	 * @param array $context Context.
	 * @return string
	 */
	public function tag_featured_image( array $context ) {
		if ( ! empty( $context['tokens']['featured_image'] ) ) {
			return (string) $context['tokens']['featured_image'];
		}
		if ( ! empty( $context['blueprint']['media']['featured_image_id'] ) ) {
			$url = wp_get_attachment_image_url( (int) $context['blueprint']['media']['featured_image_id'], 'full' );
			if ( $url ) {
				return (string) $url;
			}
		}
		foreach ( array( 'service', 'article', 'portfolio', 'pricing' ) as $type ) {
			$ref = $this->entity_ref( $context, $type );
			if ( ! $ref ) {
				continue;
			}
			$media = $this->entities->api->get_media( $type, $ref );
			if ( ! empty( $media['featured_image'] ) ) {
				return (string) $media['featured_image'];
			}
		}
		return '';
	}

	/**
	 * @param array $context Context.
	 * @return string
	 */
	public function tag_price_from( array $context ) {
		if ( ! empty( $context['tokens']['price_from'] ) ) {
			return (string) $context['tokens']['price_from'];
		}
		foreach ( array( 'pricing', 'service' ) as $type ) {
			$ref = $this->entity_ref( $context, $type );
			if ( ! $ref ) {
				continue;
			}
			$value = $this->entities->field( $type, $ref, 'price_from', '' );
			if ( '' !== $value && null !== $value ) {
				return (string) $value;
			}
		}
		return '';
	}

	/**
	 * @param array $context Context.
	 * @return string
	 */
	public function tag_phone( array $context ) {
		$country = ! empty( $context['country'] ) ? $context['country'] : $this->countries->get_default();
		return (string) $this->settings->get( 'phone', $country, '' );
	}

	/**
	 * @param array $context Context.
	 * @return string
	 */
	public function tag_whatsapp( array $context ) {
		$country = ! empty( $context['country'] ) ? $context['country'] : $this->countries->get_default();
		return (string) $this->settings->get( 'whatsapp', $country, '' );
	}

	/**
	 * @param array $context Context.
	 * @return string
	 */
	public function tag_average_rating( array $context ) {
		if ( isset( $context['tokens']['average_rating'] ) && '' !== $context['tokens']['average_rating'] ) {
			return (string) $context['tokens']['average_rating'];
		}
		$ratings = $this->collect_review_ratings( $context );
		if ( ! $ratings ) {
			return '';
		}
		$avg = array_sum( $ratings ) / count( $ratings );
		return (string) round( $avg, 1 );
	}

	/**
	 * @param array $context Context.
	 * @return string
	 */
	public function tag_review_count( array $context ) {
		if ( isset( $context['tokens']['review_count'] ) && '' !== $context['tokens']['review_count'] ) {
			return (string) $context['tokens']['review_count'];
		}
		return (string) count( $this->collect_review_ratings( $context ) );
	}

	/**
	 * @param array $context Context.
	 * @return string
	 */
	public function tag_related_services( array $context ) {
		$names = array();
		$base  = $this->entity_ref( $context, 'service' );
		if ( $base ) {
			foreach ( $this->entities->related( 'service', $base, 'service' ) as $dto ) {
				$names[] = $dto['name'];
			}
		}
		$city = $this->entity_ref( $context, 'city' );
		if ( ! $names && $city ) {
			foreach ( $this->entities->related( 'city', $city, 'service' ) as $dto ) {
				$names[] = $dto['name'];
			}
		}
		return implode( ', ', $names );
	}

	/**
	 * @param array $context Context.
	 * @return string
	 */
	public function tag_related_articles( array $context ) {
		$names   = array();
		$service = $this->entity_ref( $context, 'service' );
		if ( $service ) {
			foreach ( $this->entities->related( 'service', $service, 'article' ) as $dto ) {
				$names[] = $dto['name'];
			}
		}
		return implode( ', ', $names );
	}

	/**
	 * Generic blueprint block field resolver (uses tag def block/path).
	 *
	 * @param array              $context Context.
	 * @param array<string,mixed> $def     Tag def.
	 * @return string
	 */
	public function tag_blueprint_block_field( array $context, array $def ) {
		$block = isset( $def['block'] ) ? sanitize_key( (string) $def['block'] ) : '';
		$path  = isset( $def['path'] ) && is_array( $def['path'] ) ? $def['path'] : array();
		$token = isset( $def['id'] ) ? $def['id'] : '';
		if ( $token && ! empty( $context['tokens'][ $token ] ) ) {
			return (string) $context['tokens'][ $token ];
		}
		if ( ! $block || empty( $context['blueprint']['blocks'][ $block ]['data'] ) || ! is_array( $context['blueprint']['blocks'][ $block ]['data'] ) ) {
			return '';
		}
		$cursor = $context['blueprint']['blocks'][ $block ]['data'];
		foreach ( $path as $key ) {
			if ( ! is_array( $cursor ) || ! array_key_exists( $key, $cursor ) ) {
				return '';
			}
			$cursor = $cursor[ $key ];
		}
		return is_scalar( $cursor ) ? (string) $cursor : '';
	}

	/**
	 * Meta title — Rank Math source when post context exists; else blueprint rank_math.title.
	 *
	 * @param array $context Context.
	 * @return string
	 */
	public function tag_meta_title( array $context ) {
		if ( ! empty( $context['tokens']['meta_title'] ) ) {
			return (string) $context['tokens']['meta_title'];
		}
		if ( ! empty( $context['blueprint']['rank_math']['title'] ) ) {
			return (string) $context['blueprint']['rank_math']['title'];
		}
		if ( ! empty( $context['post_id'] ) ) {
			return $this->entities->api->get_rank_math_field( (int) $context['post_id'], 'title' );
		}
		$service = $this->entity_ref( $context, 'service' );
		if ( $service ) {
			$dto = $this->entities->get( 'service', $service );
			if ( $dto && ! empty( $dto['seo']['title'] ) ) {
				return (string) $dto['seo']['title'];
			}
		}
		return '';
	}

	/**
	 * @param array  $context Context.
	 * @param string $type    Type.
	 * @return string
	 */
	private function entity_ref( array $context, $type ) {
		$type = sanitize_key( $type );
		if ( ! empty( $context['entities'][ $type ] ) ) {
			return (string) $context['entities'][ $type ];
		}
		return '';
	}

	/**
	 * @param array $context Context.
	 * @return float[]
	 */
	private function collect_review_ratings( array $context ) {
		$ratings = array();
		$service = $this->entity_ref( $context, 'service' );
		$reviews = array();
		if ( $service ) {
			$reviews = $this->entities->related( 'service', $service, 'review' );
		}
		if ( ! $reviews ) {
			$city = $this->entity_ref( $context, 'city' );
			if ( $city ) {
				$reviews = $this->entities->related( 'city', $city, 'review' );
			}
		}
		foreach ( $reviews as $review ) {
			$rating = isset( $review['fields']['rating'] ) ? $review['fields']['rating'] : '';
			if ( is_numeric( $rating ) ) {
				$ratings[] = (float) $rating;
			}
		}
		return $ratings;
	}

	/**
	 * @param string $tag Tag.
	 * @return string
	 */
	private function normalize_tag( $tag ) {
		$tag = strtolower( trim( (string) $tag ) );
		$tag = trim( $tag, '{}' );
		return preg_replace( '/[^a-z0-9_]/', '', $tag );
	}

	/**
	 * @param mixed $value Value.
	 * @return string
	 */
	private function stringify( $value ) {
		if ( is_array( $value ) ) {
			$flat = array();
			foreach ( $value as $item ) {
				if ( is_scalar( $item ) ) {
					$flat[] = (string) $item;
				} elseif ( is_array( $item ) && isset( $item['name'] ) ) {
					$flat[] = (string) $item['name'];
				}
			}
			return implode( ', ', $flat );
		}
		if ( is_bool( $value ) ) {
			return $value ? '1' : '0';
		}
		if ( null === $value ) {
			return '';
		}
		return (string) $value;
	}
}
