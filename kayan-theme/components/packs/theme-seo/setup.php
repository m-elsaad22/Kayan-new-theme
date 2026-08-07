<?php
/**
 * theme-seo — نظام SEO الداخلي للقالب
 * Rank Math يتولى SEO الكامل (title + meta + schema) عند تفعيله.
 * هذا الملف يُفعِّل title-tag support ليتمكن Rank Math / WordPress
 * من التحكم في <title> بشكل صحيح عبر pre_get_document_title.
 * ThemeSeo::Title() تبقى كـ fallback فقط عند غياب Rank Math وعدم دعم title-tag.
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
	 * عنوان الوثيقة المحسوب من إعدادات القالب (fallback فقط).
	 */
	public function resolve_title() {
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
	 * طباعة <title> يدوياً — تُستخدم فقط إن لم يكن title-tag مدعوماً
	 * (Rank Math / WordPress core يتوليان الأمر تلقائياً عبر title-tag support).
	 */
	public function Title() {
		if ( class_exists( 'RankMath' ) || class_exists( 'RankMath\\RankMath' ) ) {
			return;
		}
		# مع title-tag: لا نطبع يدوياً لتفادي التكرار — WordPress core يطبعها عبر wp_head
		if ( current_theme_supports( 'title-tag' ) ) {
			return;
		}
		echo '<title>' . esc_html( $this->resolve_title() ) . '</title>';
	}
}
