<?php
/**
 * Language Engine — extensible registry for unlimited languages.
 *
 * Arabic is the default language. English is registered out of the box.
 * Additional languages are added via `kayan_platform_languages` — no core edits.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Language_Engine {

	const DEFAULT_CODE = 'ar';

	/**
	 * @return array<string,array<string,mixed>>
	 */
	public function all() {
		$languages = array(
			'ar' => array(
				'code'      => 'ar',
				'label_ar'  => 'العربية',
				'label_en'  => 'Arabic',
				'dir'       => 'rtl',
				'hreflang'  => 'ar',
				'is_default'=> true,
			),
			'en' => array(
				'code'       => 'en',
				'label_ar'   => 'الإنجليزية',
				'label_en'   => 'English',
				'dir'        => 'ltr',
				'hreflang'   => 'en',
				'is_default' => false,
			),
		);

		/**
		 * Register additional languages without changing core.
		 *
		 * @param array $languages Language registry.
		 */
		$languages = apply_filters( 'kayan_platform_languages', $languages );

		return is_array( $languages ) ? $languages : array();
	}

	/**
	 * @param string $code Language code.
	 * @return bool
	 */
	public function exists( $code ) {
		$code = $this->normalize( $code );
		$all  = $this->all();
		return $code !== '' && isset( $all[ $code ] );
	}

	/**
	 * @param string|null $code Language code.
	 * @return array<string,mixed>
	 */
	public function get( $code = null ) {
		if ( null === $code || '' === $code ) {
			$code = $this->get_default();
		}
		$code = $this->normalize( $code );
		$all  = $this->all();

		if ( isset( $all[ $code ] ) ) {
			return $all[ $code ];
		}

		return $all[ self::DEFAULT_CODE ];
	}

	/**
	 * @return string
	 */
	public function get_default() {
		/**
		 * @param string $default Default language code.
		 */
		$default = apply_filters( 'kayan_platform_default_language', self::DEFAULT_CODE );
		return $this->exists( $default ) ? $this->normalize( $default ) : self::DEFAULT_CODE;
	}

	/**
	 * @param string|null $code Language code.
	 * @return string rtl|ltr
	 */
	public function get_dir( $code = null ) {
		$data = $this->get( $code );
		return ( isset( $data['dir'] ) && 'ltr' === $data['dir'] ) ? 'ltr' : 'rtl';
	}

	/**
	 * @param string|null $code Language code.
	 * @return string
	 */
	public function get_hreflang( $code = null ) {
		$data = $this->get( $code );
		return isset( $data['hreflang'] ) ? (string) $data['hreflang'] : $this->get_default();
	}

	/**
	 * @param string $code Raw code.
	 * @return string
	 */
	public function normalize( $code ) {
		return strtolower( sanitize_key( (string) $code ) );
	}
}
