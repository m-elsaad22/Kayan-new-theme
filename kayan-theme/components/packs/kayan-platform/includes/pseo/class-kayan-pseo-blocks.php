<?php
/**
 * PSEO Block Engine — reusable, independently regeneratable page blocks.
 *
 * Architecture only: registry + schemas + AI prompt contracts.
 * No rendering and no content generation in this phase.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_PSEO_Blocks {

	/** @var array<string,array<string,mixed>> */
	private $blocks = array();

	/**
	 * @return void
	 */
	public function register() {
		$this->register_core_blocks();

		/**
		 * @param Kayan_PSEO_Blocks $registry Registry.
		 */
		do_action( 'kayan_pseo_register_blocks', $this );
	}

	/**
	 * @param string              $id   Block id.
	 * @param array<string,mixed> $args Args.
	 * @return void
	 */
	public function register_block( $id, array $args ) {
		$id = sanitize_key( $id );
		if ( '' === $id ) {
			return;
		}

		$defaults = array(
			'label'              => $id,
			'description'        => '',
			'enabled'            => true,
			'regeneratable'      => true,
			'ai_enabled'         => true,
			'ai_prompt'          => '', // default prompt template; tokens: {service}, {city}, …
			'ai_prompt_version'  => 1,
			'data_schema'        => array(),
			'supports_media'     => false,
			'rank_math_related'  => false, // true for schema_source only as data feed
		);

		$this->blocks[ $id ] = array_merge( $defaults, $args, array( 'id' => $id ) );
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function all() {
		/**
		 * @param array $blocks Blocks.
		 */
		return apply_filters( 'kayan_pseo_blocks', $this->blocks );
	}

	/**
	 * @param string $id Block id.
	 * @return array<string,mixed>|null
	 */
	public function get( $id ) {
		$id     = sanitize_key( $id );
		$blocks = $this->all();
		return isset( $blocks[ $id ] ) ? $blocks[ $id ] : null;
	}

	/**
	 * Empty data payload for a block instance.
	 *
	 * @param string $id Block id.
	 * @return array<string,mixed>
	 */
	public function empty_instance( $id ) {
		$block = $this->get( $id );
		$schema = $block && isset( $block['data_schema'] ) && is_array( $block['data_schema'] )
			? $block['data_schema']
			: array();

		return array(
			'block_id'        => sanitize_key( $id ),
			'data'            => $schema,
			'locked'          => false, // manual lock — skip on template/AI regenerate
			'source'          => 'template', // template|ai|manual
			'content_version' => 1,
			'ai'              => array(
				'prompt'         => $block ? (string) $block['ai_prompt'] : '',
				'prompt_version' => $block ? (int) $block['ai_prompt_version'] : 1,
				'provider'       => '',
				'model'          => '',
				'last_generated' => '',
			),
		);
	}

	/**
	 * Resolve AI prompt for a block with token replacement (no API call).
	 *
	 * @param string               $block_id Block id.
	 * @param array<string,string> $tokens   Tokens.
	 * @param string|null          $override Optional prompt override.
	 * @return string
	 */
	public function resolve_prompt( $block_id, array $tokens = array(), $override = null ) {
		$block  = $this->get( $block_id );
		$prompt = null !== $override ? (string) $override : ( $block ? (string) $block['ai_prompt'] : '' );

		foreach ( $tokens as $key => $value ) {
			$prompt = str_replace( '{' . $key . '}', (string) $value, $prompt );
		}

		/**
		 * @param string $prompt   Prompt.
		 * @param string $block_id Block.
		 * @param array  $tokens   Tokens.
		 */
		return apply_filters( 'kayan_pseo_block_prompt', $prompt, $block_id, $tokens );
	}

	/**
	 * @return void
	 */
	private function register_core_blocks() {
		$this->register_block(
			'hero',
			array(
				'label'       => 'Hero',
				'ai_prompt'   => 'Write a compelling hero headline and subheadline for {service} in {city}, {country}. Language: {language}.',
				'supports_media' => true,
				'data_schema' => array(
					'headline'    => '',
					'subheadline' => '',
					'image_id'    => 0,
					'overlay'     => '',
				),
			)
		);

		$this->register_block(
			'cta',
			array(
				'label'     => 'CTA',
				'ai_prompt' => 'Write a primary and secondary CTA for booking {service} in {city}. Language: {language}.',
				'data_schema' => array(
					'primary_label'   => '',
					'primary_url'     => '',
					'secondary_label' => '',
					'secondary_url'   => '',
					'phone'           => '',
					'whatsapp'        => '',
				),
			)
		);

		$this->register_block(
			'gallery',
			array(
				'label'          => 'Gallery',
				'ai_prompt'      => 'Suggest gallery captions for {service} photos in {city}.',
				'supports_media' => true,
				'ai_enabled'     => true,
				'data_schema'    => array(
					'items' => array(), // array of media refs
				),
			)
		);

		$this->register_block(
			'faq',
			array(
				'label'     => 'FAQ',
				'ai_prompt' => 'Generate 6 FAQs about {service} in {city}, {country}. Language: {language}.',
				'data_schema' => array(
					'items' => array(), // question/answer
				),
			)
		);

		$this->register_block(
			'pricing',
			array(
				'label'     => 'Pricing',
				'ai_prompt' => 'Summarize pricing guidance for {service} in {city}. Currency context: {country}.',
				'data_schema' => array(
					'items'   => array(),
					'note'    => '',
					'post_ids'=> array(),
				),
			)
		);

		$this->register_block(
			'reviews',
			array(
				'label'     => 'Reviews',
				'ai_prompt' => 'Select review highlights for {service} in {city}.',
				'data_schema' => array(
					'post_ids' => array(),
					'note'     => '',
				),
			)
		);

		$this->register_block(
			'map',
			array(
				'label'       => 'Map',
				'ai_enabled'  => false,
				'data_schema' => array(
					'lat'     => '',
					'lng'     => '',
					'embed'   => '',
					'label'   => '',
				),
			)
		);

		$this->register_block(
			'areas',
			array(
				'label'     => 'Areas',
				'ai_prompt' => 'List nearby service areas related to {city} for {service}.',
				'data_schema' => array(
					'items' => array(),
				),
			)
		);

		$this->register_block(
			'related_services',
			array(
				'label'     => 'Related Services',
				'ai_prompt' => 'Recommend related services for users interested in {service} in {city}.',
				'data_schema' => array(
					'post_ids' => array(),
				),
			)
		);

		$this->register_block(
			'related_articles',
			array(
				'label'     => 'Related Articles',
				'ai_prompt' => 'Recommend related articles for {service} in {city}.',
				'data_schema' => array(
					'post_ids' => array(),
				),
			)
		);

		$this->register_block(
			'related_cities',
			array(
				'label'     => 'Related Cities',
				'ai_prompt' => 'Recommend nearby cities for {service} around {city}, {country}.',
				'data_schema' => array(
					'term_ids' => array(),
					'items'    => array(),
				),
			)
		);

		$this->register_block(
			'internal_links',
			array(
				'label'     => 'Internal Links',
				'ai_prompt' => 'Propose internal links for a {service} page targeting {city}.',
				'data_schema' => array(
					'items' => array(),
				),
			)
		);

		$this->register_block(
			'videos',
			array(
				'label'          => 'Videos',
				'supports_media' => true,
				'ai_prompt'      => 'Suggest video titles/descriptions for {service} in {city}.',
				'data_schema'    => array(
					'items' => array(),
				),
			)
		);

		$this->register_block(
			'schema_source',
			array(
				'label'             => 'Schema Source',
				'rank_math_related' => true,
				'ai_prompt'         => 'Provide structured data fields for {service} LocalBusiness/Service in {city}.',
				'data_schema'       => array(
					'types'   => array( 'Service', 'FAQPage', 'BreadcrumbList' ),
					'payload' => array(),
				),
			)
		);

		$this->register_block(
			'breadcrumb',
			array(
				'label'      => 'Breadcrumb',
				'ai_enabled' => false,
				'data_schema'=> array(
					'items' => array(),
				),
			)
		);
	}
}
