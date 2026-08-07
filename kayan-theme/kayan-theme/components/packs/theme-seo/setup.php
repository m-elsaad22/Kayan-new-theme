<?php
/**
 * theme-seo — نظام SEO الداخلي للقالب
 * KAYAN v1.4.2+ — Rank Math يتولى SEO الكامل (title + meta + schema)
 * هذا الملف يُفعِّل title-tag support ليتمكن Rank Math / WordPress
 * من التحكم في <title> بشكل صحيح عبر pre_get_document_title.
 * ThemeSeo::Title() تبقى كـ fallback فقط عند غياب Rank Math.
 */

// ═══ تفعيل title-tag support — يُمكِّن Rank Math من التحكم في <title> ═══
add_action( 'after_setup_theme', function() {
    add_theme_support( 'title-tag' );
}, 1 );

class ThemeSeo {
    function __construct() {
        $this->seo__title_showsin = get_option('seo__title_showsin');
        if ( empty( $this->seo__title_showsin ) ) $this->seo__title_showsin = 'wordpress';

        $this->seo__site_name = get_option('seo__site_name');
        $this->LastWord = $this->seo__site_name;
    }

    /**
     * Title() — لا تُستدعى إلا عند غياب Rank Math (fallback فقط)
     * عندما يكون Rank Math مثبتاً، WordPress يُخرج <title> عبر wp_head()
     * بفضل add_theme_support('title-tag') أعلاه.
     */
    public function Title(){
        // إذا كان Rank Math مثبتاً، لا تخرج <title> — wp_head() يتولى الأمر
        if ( function_exists( 'kayan_is_rank_math_active' ) ? kayan_is_rank_math_active() : ( class_exists( 'RankMath' ) || class_exists( 'RankMath\RankMath' ) || defined( 'RANK_MATH_VERSION' ) ) ) {
            return;
        }

        if ( $this->seo__title_showsin == 'theme_seo' ) {
            $title = '';
            if ( is_page() ) {
                global $post;
                if ( $post->post_parent > 0 ) {
                    $title .= get_post( $post->post_parent )->post_title . ' ';
                }
                $title .= $post->post_title;
            } elseif ( is_author() ) {
                $curauth = ( get_query_var('author_name') )
                    ? get_user_by( 'slug', get_query_var('author_name') )
                    : get_userdata( get_query_var('author') );
                $title .= $curauth->display_name;
            } elseif ( is_category() ) {
                $obj = get_queried_object();
                if ( $obj->parent > 0 ) {
                    $title .= get_term( $obj->parent, 'category' )->name . ' ';
                }
                $title .= $obj->name;
            } elseif ( is_tax() ) {
                $obj = get_queried_object();
                if ( $obj->parent > 0 ) {
                    $title .= get_term( $obj->parent, $obj->taxonomy )->name . ' ';
                }
                $title .= $obj->name;
            } elseif ( is_single() ) {
                global $post;
                if ( $post->post_parent > 0 ) {
                    $title .= get_post( $post->post_parent )->post_title . ' ';
                }
                $title .= $post->post_title;
            } elseif ( is_home() ) {
                $title .= get_option('home__title');
            } else {
                $title .= get_option('default__title');
            }
            echo "<title>{$title}{$this->LastWord}</title>";
        } else {
            // Fallback WordPress standard title
            if ( is_home() ) {
                echo '<title>' . esc_html( get_bloginfo('name') ) . '</title>';
            } else {
                echo '<title>';
                wp_title('', true, 'right');
                echo esc_html( get_bloginfo('name') );
                echo '</title>';
            }
        }
    }
}
