<?php
/**
 * theme-seo — نظام SEO الداخلي للقالب
 * KAYAN v1.4.8+
 *
 * - Rank Math Active للتخزين فقط (واجهته معطّلة عبر kayan-seo/compatibility.php)
 * - العنوان/الوصف يُقرأان من rank_math_* عبر الجسر، ويُخرجان بواسطة KAYAN SEO
 * - ThemeSeo::Title() تبقى كـ fallback عند تعطيل title-tag أو غياب الجسر
 */

add_action( 'after_setup_theme', function() {
	add_theme_support( 'title-tag' );
}, 1 );

class ThemeSeo {
	function __construct() {
		$this->seo__title_showsin = get_option( 'seo__title_showsin' );
		if ( empty( $this->seo__title_showsin ) ) {
			$this->seo__title_showsin = 'wordpress';
		}

		$this->seo__site_name = get_option( 'seo__site_name' );
		$this->LastWord       = $this->seo__site_name;
	}

	/**
	 * عنوان الوثيقة — يفضّل بيانات Rank Math المخزّنة ثم إعدادات القالب
	 */
	public function resolve_title() {
		if ( function_exists( 'kayan_seo_get_rank_math_title' ) ) {
			$rm = kayan_seo_get_rank_math_title();
			if ( '' !== $rm ) {
				return $rm;
			}
		}

		$title = '';
		if ( $this->seo__title_showsin == 'theme_seo' ) {
			if ( is_page() ) {
				global $post;
				if ( $post && $post->post_parent > 0 ) {
					$title .= get_post( $post->post_parent )->post_title . ' ';
				}
				$title .= $post ? $post->post_title : '';
			} elseif ( is_author() ) {
				$curauth = ( get_query_var( 'author_name' ) )
					? get_user_by( 'slug', get_query_var( 'author_name' ) )
					: get_userdata( get_query_var( 'author' ) );
				$title .= $curauth ? $curauth->display_name : '';
			} elseif ( is_category() ) {
				$obj = get_queried_object();
				if ( $obj && $obj->parent > 0 ) {
					$title .= get_term( $obj->parent, 'category' )->name . ' ';
				}
				$title .= $obj ? $obj->name : '';
			} elseif ( is_tax() ) {
				$obj = get_queried_object();
				if ( $obj && $obj->parent > 0 ) {
					$title .= get_term( $obj->parent, $obj->taxonomy )->name . ' ';
				}
				$title .= $obj ? $obj->name : '';
			} elseif ( is_single() ) {
				global $post;
				if ( $post && $post->post_parent > 0 ) {
					$title .= get_post( $post->post_parent )->post_title . ' ';
				}
				$title .= $post ? $post->post_title : '';
			} elseif ( is_home() ) {
				$title .= get_option( 'home__title' );
			} else {
				$title .= get_option( 'default__title' );
			}
			$title .= $this->LastWord;
		} else {
			if ( is_home() ) {
				$title = get_bloginfo( 'name' );
			} else {
				$title = trim( wp_title( '', false, 'right' ) . ' ' . get_bloginfo( 'name' ) );
			}
		}
		return $title;
	}

	/**
	 * طباعة <title> يدوياً — تُستخدم فقط إن لم يكن title-tag كافياً
	 * (الهيدر الحالي يعتمد على title-tag + جسر Rank Math)
	 */
	public function Title() {
		# مع title-tag: لا نطبع يدوياً لتفادي التكرار — الجسر يضبط pre_get_document_title
		if ( current_theme_supports( 'title-tag' ) ) {
			return;
		}
		echo '<title>' . esc_html( $this->resolve_title() ) . '</title>';
	}
}
