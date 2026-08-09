<?php
/**
 * kayan-seo / rank-math-bridge.php
 *
 * جسر قراءة بيانات Rank Math المخزّنة دون الاعتماد على إخراج الواجهة.
 * المفاتيح:
 * - rank_math_title
 * - rank_math_description
 *
 * مطابق لمنطق ServicesTheme(YourColor)/components/packs/kayan-seo/rank-math-bridge.php
 */

if ( ! function_exists( 'kayan_seo_get_queried_object_id' ) ) {
	function kayan_seo_get_queried_object_id() {
		if ( is_singular() ) {
			return (int) get_queried_object_id();
		}
		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			return ( $term && isset( $term->term_id ) ) ? (int) $term->term_id : 0;
		}
		return 0;
	}
}

if ( ! function_exists( 'kayan_seo_get_rank_math_meta' ) ) {
	/**
	 * قراءة ميتا Rank Math من منشور أو تصنيف.
	 *
	 * @param string $key  rank_math_title | rank_math_description | ...
	 * @param int    $id   post/term id (0 = الحالي)
	 * @return string
	 */
	function kayan_seo_get_rank_math_meta( $key, $id = 0 ) {
		$key = (string) $key;
		$id  = absint( $id );

		if ( ! $id ) {
			$id = kayan_seo_get_queried_object_id();
		}
		if ( ! $id ) {
			# إعدادات الصفحة الرئيسية من Rank Math (إن وُجدت)
			$home = get_option( 'rank_math_titles_homepage_title' );
			if ( 'rank_math_title' === $key && is_string( $home ) && '' !== $home ) {
				return kayan_seo_replace_rank_math_vars( $home );
			}
			$home_desc = get_option( 'rank_math_titles_homepage_description' );
			if ( 'rank_math_description' === $key && is_string( $home_desc ) && '' !== $home_desc ) {
				return kayan_seo_replace_rank_math_vars( $home_desc );
			}
			return '';
		}

		$value = '';
		if ( is_singular() || ( get_post( $id ) instanceof WP_Post ) ) {
			$value = get_post_meta( $id, $key, true );
		}
		if ( ( '' === $value || false === $value ) && ( is_category() || is_tag() || is_tax() ) ) {
			$value = get_term_meta( $id, $key, true );
		}
		if ( ! is_string( $value ) ) {
			$value = '';
		}
		$value = trim( $value );
		if ( '' === $value ) {
			return '';
		}
		return kayan_seo_replace_rank_math_vars( $value, $id );
	}
}

if ( ! function_exists( 'kayan_seo_replace_rank_math_vars' ) ) {
	/**
	 * استبدال متغيرات Rank Math الشائعة بقيم بسيطة.
	 */
	function kayan_seo_replace_rank_math_vars( $text, $id = 0 ) {
		$text = (string) $text;
		$id   = absint( $id );
		$title = $id ? get_the_title( $id ) : get_bloginfo( 'name' );
		$site  = get_bloginfo( 'name' );
		$sep   = '-';
		$desc  = $id ? wp_strip_all_tags( get_the_excerpt( $id ) ) : get_bloginfo( 'description' );

		$map = array(
			'%title%'         => $title,
			'%page%'          => '',
			'%sep%'           => $sep,
			'%sitename%'      => $site,
			'%name%'          => $site,
			'%excerpt%'       => $desc,
			'%excerpt_only%'  => $desc,
			'%seo_title%'     => $title,
			'%seo_description%'=> $desc,
		);
		$text = strtr( $text, $map );
		# تنظيف فواصل زائدة
		$text = preg_replace( '/\s+' . preg_quote( $sep, '/' ) . '\s+$/u', '', $text );
		$text = preg_replace( '/\s{2,}/u', ' ', $text );
		return trim( $text );
	}
}

if ( ! function_exists( 'kayan_seo_get_rank_math_title' ) ) {
	function kayan_seo_get_rank_math_title( $id = 0 ) {
		return kayan_seo_get_rank_math_meta( 'rank_math_title', $id );
	}
}

if ( ! function_exists( 'kayan_seo_get_rank_math_description' ) ) {
	function kayan_seo_get_rank_math_description( $id = 0 ) {
		return kayan_seo_get_rank_math_meta( 'rank_math_description', $id );
	}
}
