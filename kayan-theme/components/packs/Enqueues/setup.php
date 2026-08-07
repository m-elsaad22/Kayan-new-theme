<?php 
add_action( 'wp_enqueue_scripts', function(){
} );
add_action( 'wp_footer', function(){
wp_enqueue_script ( 'yourcolor-script', get_template_directory_uri() . '/components/packs/'.urlencode('#footer').'/js/jquery-3.4.1.min.js' );
wp_enqueue_script ( 'yourcolor-owlcarousel', get_template_directory_uri() . '/components/packs/'.urlencode('#footer').'/js/owl.carousel.min.js' );
wp_enqueue_script ( 'yourcolor-init', get_template_directory_uri() . '/components/packs/'.urlencode('#footer').'/js/setup.js');
});

function disable_unnecessary_scripts() {
        wp_dequeue_script( 'jquery' );
        wp_dequeue_script( 'owl.carousel.min' );
        wp_dequeue_script( 'wp-embed' );
        wp_dequeue_script( 'wp-emoji' );
        wp_dequeue_script( 'comment-reply' );
        wp_dequeue_script( 'gtag-js' );
}
add_action( 'wp_enqueue_scripts', 'disable_unnecessary_scripts', 999 );

function disable_classic_theme_styless() {
    wp_deregister_style('classic-theme-styles');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('wp-block-library-rtl-css');
}
if(!is_admin()){
    add_filter('wp_enqueue_scripts', 'disable_classic_theme_styless', 100);
}

function remove_youtube_embed_scripts() {
    wp_deregister_style( 'wp-youtube-embed' );
    wp_deregister_script( 'wp-youtube-embed' );
    wp_deregister_script( 'base' );
}
add_action( 'wp_enqueue_scripts', 'remove_youtube_embed_scripts', 999 );

/**
 * تعطيل السكربتات الزائدة مع حماية كاملة لـ Rank Math وتبعياته
 * KAYAN v1.4.6+ — Rank Math يبقى مسؤولاً كاملاً عن مخرجاته على الواجهة؛
 * هذه الحماية تمنع فقط تعطيل أصوله عن طريق الخطأ عبر disable_all_scripts أدناه.
 */
function kayan_is_rank_math_active() {
    return class_exists( 'RankMath' )
        || class_exists( 'RankMath\\RankMath' )
        || defined( 'RANK_MATH_VERSION' )
        || function_exists( 'rank_math' );
}

function kayan_is_protected_asset_handle( $handle ) {
    $handle = (string) $handle;
    $protected_prefixes = array(
        'rank-math',
        'rank_math',
        'seo-by-rank-math',
        'kayan-',
        'yourcolor-',
        // تبعيات Rank Math / WP الأساسية التي يعتمد عليها الإضافة
        'wp-i18n',
        'wp-hooks',
        'wp-element',
        'wp-components',
        'wp-api-fetch',
        'wp-url',
        'wp-data',
        'wp-dom-ready',
        'lodash',
        'wp-polyfill',
        'regenerator-runtime',
    );
    foreach ( $protected_prefixes as $prefix ) {
        if ( 0 === strpos( $handle, $prefix ) ) {
            return true;
        }
    }
    return false;
}

function kayan_is_protected_asset_src( $src ) {
    if ( ! is_string( $src ) || '' === $src ) {
        return false;
    }
    $needles = array( 'rank-math', 'seo-by-rank-math', '/kayan-', 'fa-free-fixes' );
    foreach ( $needles as $needle ) {
        if ( false !== strpos( $src, $needle ) ) {
            return true;
        }
    }
    return false;
}

function disable_all_scripts() {
    // في الأدمن لا نلمس أي شيء — Rank Math ولوحة التحكم يحتاجان كل الأصول
    if ( is_admin() ) {
        return;
    }

    global $wp_scripts, $wp_styles;

    if ( isset( $wp_scripts->queue ) && is_array( $wp_scripts->queue ) ) {
        foreach ( $wp_scripts->queue as $handle ) {
            if ( kayan_is_protected_asset_handle( $handle ) ) {
                continue;
            }
            $src = isset( $wp_scripts->registered[ $handle ]->src ) ? $wp_scripts->registered[ $handle ]->src : '';
            if ( kayan_is_protected_asset_src( $src ) ) {
                continue;
            }
            wp_dequeue_script( $handle );
        }
    }

    if ( isset( $wp_styles->queue ) && is_array( $wp_styles->queue ) ) {
        foreach ( $wp_styles->queue as $handle ) {
            if ( kayan_is_protected_asset_handle( $handle ) ) {
                continue;
            }
            $src = isset( $wp_styles->registered[ $handle ]->src ) ? $wp_styles->registered[ $handle ]->src : '';
            if ( kayan_is_protected_asset_src( $src ) ) {
                continue;
            }
            wp_dequeue_style( $handle );
        }
    }
}
add_action('wp_enqueue_scripts', 'disable_all_scripts', 999999);

# Rank Math على الواجهة يبقى نشطاً بالكامل — نحمي أصوله فقط من disable_all_scripts أعلاه
# (الذي أصلاً لا يعمل في is_admin()، لكن هذه الحماية تغطي أي سياق frontend مستقبلي).

# لا تحذف ?ver= من أصول Rank Math (قد يسبب كاش قديم بعد تحديث الإضافة)
function remove_script_version($src) {
    if ( ! is_string( $src ) ) {
        return $src;
    }
    if ( false !== strpos( $src, 'rank-math' ) || false !== strpos( $src, 'seo-by-rank-math' ) ) {
        return $src;
    }
    if (strpos($src, 'ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'remove_script_version', 15, 1);

function remove_css_version($src) {
    if ( ! is_string( $src ) ) {
        return $src;
    }
    if ( false !== strpos( $src, 'rank-math' ) || false !== strpos( $src, 'seo-by-rank-math' ) ) {
        return $src;
    }
    if (strpos($src, 'ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'remove_css_version', 15, 1);

add_action( 'wp_print_styles', 'wps_deregister_styles', 100 );
function wps_deregister_styles() {
    wp_dequeue_style( 'wp-block-library' );
}

function removeAppleTouchIcon() {
    remove_action('wp_head', 'wp_site_icon', 99);
}
add_action('init', 'removeAppleTouchIcon');

function update_protocol_links($html) {
    $html = preg_replace_callback('/(src|href)=[\"\']\/\/(.*?)[\"\']/', function($match) {
        return $match[1] . '="https://' . $match[2] . '"';
    }, $html);
    $html = preg_replace_callback('/Permissions-Policy: (.*)\r\n/', function($match) {
        $policies = explode(',', $match[1]);
        $supported_policies = array('geolocation','midi','notifications','push','sync-xhr','microphone','camera','magnetometer','gyroscope','speaker','vibrate','fullscreen','payment','usb','accelerometer','vr','xr-spatial-tracking');
        $filtered_policies = array_filter($policies, function($policy) use ($supported_policies) {
            return in_array(trim($policy), $supported_policies);
        });
        return 'Permissions-Policy: ' . implode(',', $filtered_policies) . "\r\n";
    }, $html);
    $html = str_replace(array('ch-ua-form-factor', 'ch-ua-mobile'), '', $html);
    return $html;
}

function orderHeader($test) {
    ob_start();
}
function orderFooter($test) {
    $html = ob_get_clean();
    $html = update_protocol_links($html);
    $html = str_replace('target="_blank"', 'target="_blank" rel="nofollow noopener noreferrer"', $html);
    echo $html;
}
add_action( 'BeforeHeader', 'orderHeader' );
add_action( 'AfterFooter', 'orderFooter' );

function remove_wp_emoji() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', 'disable_wp_emoji');
}
add_action('init', 'remove_wp_emoji');

function disable_wp_emoji($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    }
    return array();
}
