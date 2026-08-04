<?php
ob_start();

if( !isset( $bodyClass ) ) $bodyClass = '';
# INTRO OPTIONS .

$site_color = get_option('site_color');
$text_Color = get_option('text_Color');

if( !isset($_GET['ajax']) ) {
	echo '<!DOCTYPE html>';
	echo '<html lang="ar" dir="rtl">';
	echo '<head>';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
		echo '<meta charset="utf-8">';
		echo '<meta name="theme-color" content="#0A1F4E">';
// ═══ KAYAN v1.4.2+ — meta description يُخرجه Rank Math عبر wp_head() ═══
// Rank Math يطبع meta description صحيحة لكل صفحة — لا نتدخل يدوياً
$hide__description_show = get_option('hide__description_show');
if ( empty( $hide__description_show ) && ! ( function_exists( 'kayan_is_rank_math_active' ) ? kayan_is_rank_math_active() : ( class_exists( 'RankMath' ) || class_exists( 'RankMath\\RankMath' ) || defined( 'RANK_MATH_VERSION' ) ) ) ) {
    $yc__desc = is_singular() ? wp_strip_all_tags( get_the_excerpt() ) : get_bloginfo('description');
    if ( ! empty( $yc__desc ) ) {
        echo '<meta name="description" content="' . esc_attr( $yc__desc ) . '">';
    }
}

// ═══ KAYAN v1.4.2+ — Title Management ═══
// Rank Math موجود → wp_head() يُخرج <title> تلقائياً (add_theme_support active في theme-seo)
// Rank Math غائب → ThemeSeo()->Title() تُخرجه مباشرةً (fallback)
$hide__theme_seo = get_option('hide__theme_seo');
$yc__rankmath_active = function_exists( 'kayan_is_rank_math_active' ) ? kayan_is_rank_math_active() : ( class_exists( 'RankMath' ) || class_exists( 'RankMath\\RankMath' ) || defined( 'RANK_MATH_VERSION' ) );
if ( empty( $hide__theme_seo ) && ! $yc__rankmath_active ) {
    (new ThemeSeo)->Title();
}
// Rank Math موجود → wp_head() أدناه سيُخرج <title> بشكل تلقائي

		do_action('BeforeWPHead');

		# RUKN v3 FONTS — Cairo + Tajawal
		echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
		echo '<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">';

		# ═══ v1.4.2: تحميل Font Awesome بلا اعتماد على jQuery ═══
		# كان يُحمَّل عبر data-loader-href الذي يحتاج jQuery ليحوّله إلى href؛
		# أي فشل/تأخّر في jQuery أو الكاش = كل الأيقونات مربعات فارغة.
		# النمط أدناه غير حاجب للعرض ويعمل بلا JS إطلاقاً، مع بديل داخل noscript.
		$yc__fa_url = get_template_directory_uri().'/components/styles/FontAwesome/css/all.min.css';
		echo '<link rel="preload" as="style" href="'.esc_url( $yc__fa_url ).'" />';
		echo '<link rel="stylesheet" href="'.esc_url( $yc__fa_url ).'" media="print" onload="this.media=\'all\';this.onload=null;" />';
		echo '<noscript><link rel="stylesheet" href="'.esc_url( $yc__fa_url ).'" /></noscript>';
		echo '<link rel="stylesheet" href="'.esc_url( get_template_directory_uri().'/components/styles/fa-free-fixes.css?v=1.4.7' ).'" />';
		echo '<style id="kayan-logo-critical">a.logo,a.flogo{display:inline-flex!important;align-items:center;gap:8px;visibility:visible!important;opacity:1!important;z-index:5;position:relative}a.logo.has-logo-image .mark,a.flogo.has-logo-image .mark{display:none!important}a.logo img,a.flogo img,.kayan-logo-img{display:block!important;max-height:56px!important;max-width:min(55vw,240px)!important;width:auto!important;height:auto!important;visibility:visible!important;opacity:1!important;object-fit:contain!important}</style>';

		echo ( ( IsSpeed() == false && ( is_single() || is_page() || ( isset( $Widgets__list ) && in_array( 'works_v1',$Widgets__list ) ) ) ) ) ? '<link rel="stylesheet" data-loader-href="https://unpkg.com/photoswipe@5.2.2/dist/photoswipe.css">' : '';

		if( isset( $HeadCode ) && !empty( $HeadCode ) ){
			echo $HeadCode;
		}else{
			echo get_option('header___codes');
		}

		wp_head();

		$Styles['forms'] = 'forms.css';

       	$ips = ( is_array( get_option( "open_css" ) ) ) ? get_option( "open_css" ) : array();
		if( isset($_GET['open__css']) ) {
	       $ips[$_SERVER['REMOTE_ADDR']] = true;
	       update_option("open_css", $ips);
		}

		if( isset( $ips[ $_SERVER['REMOTE_ADDR'] ]) ) {
			echo '<style>';
				$this->Part('fonts');
			echo '</style>';
			if(isset($Styles)){
				foreach ($Styles as $skey => $meky) {
					echo '<link rel="stylesheet" data-style-ajax="'.$skey.'" type="text/css" href="'.$this->StylesURL.$meky.'?v='.rand().'" />';
				}
			}

			echo '<link rel="stylesheet" data-style-ajax="main" type="text/css" href="'.$this->StylesURL.'main.css?v='.rand().'" />';
			echo '<link rel="stylesheet" data-style-ajax="hover" type="text/css" href="'.$this->StylesURL.'hover.css?v='.rand().'" />';
			echo '<link rel="stylesheet" data-style-ajax="responsive" type="text/css" href="'.$this->StylesURL.'responsive.css?v='.rand().'" />';
			echo '<link rel="stylesheet" data-style-ajax="rukn-v3" type="text/css" href="'.$this->StylesURL.'rukn-v3.css?v='.rand().'" />';
		}else{
		    echo '<style>';
				$this->Part('fonts');
				if(isset($Styles)){
					foreach ($Styles as $skey => $meky) {
		    			require ($this->StylesPath.$meky);
					}
				}
		    	require ($this->StylesPath."/main.css");
		    	require ($this->StylesPath."/hover.css");
		    	require ($this->StylesPath."/responsive.css");
		    	require ($this->StylesPath."/rukn-v3.css");
		    echo '</style>';
		}

		do_action('AfterWPHead');
		if( !empty( get_option('favicon') ) ) {
			echo '<link rel="shortcut icon" type="image/png" href="'.get_option('favicon').'">';
		}

		echo '<meta name="apple-mobile-web-app-title" content="'.get_bloginfo('name').'">';
		echo '<meta http-equiv="Cache-control" content="public">';
		echo '<meta name="application-name" content="'.get_bloginfo('name').'">';
		echo '<link rel="preload" as="font">';
		echo '<meta name="msapplication-TileColor" content="#0A1F4E">';
		//
		echo '<style>';
			echo 'body { ';
				echo ( ( !empty( $site_color ) ) ) ? '--uicolor:'.$site_color.';' : '';
				echo ( ( !empty( $text_Color ) ) ) ? '--primary-text:'.$text_Color.';' : '';
			echo '}';
		echo '</style>';
	echo '</head>';
	echo '<body mode="light" class="before-start '.$bodyClass.'">';
	do_action('yc_hook_body_start');
}

# ════════════════════════════════════════════════════════
# LOGO DATA — نفس منطق لوحة التحكم القديم
# ════════════════════════════════════════════════════════
	$logo__data = get_option( 'logo__data' );
	$logo__data = ( ( is_array( $logo__data ) ) ) ? $logo__data : array();
	if( empty( $logo__data ) && ( !isset( $logo__data['logo__mode'] ) || ( isset( $logo__data['logo__mode'] ) && isset( $logo__data[ $logo__data['logo__mode'] ] ) ) ) )
		$logo__data = array( 'logo__mode'=>'Text','Text'=>array('logo_Text'=>'ركن {%التطور%}') );

	# دالة صغيرة بترسم اللوجو بكلاسات التصميم الجديد (هيدر أو فوتر)
	# v1.4.6: أولوية الرابط الكامل المحفوظ + منع LazyLoad (LiteSpeed) + إخفاء أيقونة الدرع مع الصورة
	if( !function_exists('rukn_v3_render_logo') ){
		function rukn_v3_render_logo( $logo__data, $css_class = 'logo', $img_size = 'full' ){
			$logo__data = is_array( $logo__data ) ? $logo__data : array();
			$mode = isset( $logo__data['logo__mode'] ) ? $logo__data['logo__mode'] : 'Text';
			$site_name = get_bloginfo( 'name' );
			$alt  = ( isset( $logo__data[ $mode ]['header__alt'] ) && !empty( $logo__data[ $mode ]['header__alt'] ) ) ? $logo__data[ $mode ]['header__alt'] : $site_name;

			$logo_id = 0;
			$logo_url = '';
			if ( 'Image' === $mode && isset( $logo__data['Image'] ) && is_array( $logo__data['Image'] ) ) {
				$img_box = $logo__data['Image'];
				# حقل File يحفظ الرابط في image_logo والـ ID في image_logo_id
				if ( ! empty( $img_box['image_logo_id'] ) ) {
					$logo_id = absint( $img_box['image_logo_id'] );
				} elseif ( ! empty( $img_box['image_logo']['id'] ) ) {
					$logo_id = absint( $img_box['image_logo']['id'] );
				} elseif ( ! empty( $img_box['image_logo'] ) && is_numeric( $img_box['image_logo'] ) ) {
					$logo_id = absint( $img_box['image_logo'] );
				}
				if ( ! empty( $img_box['image_logo']['url'] ) && is_string( $img_box['image_logo']['url'] ) ) {
					$logo_url = $img_box['image_logo']['url'];
				} elseif ( ! empty( $img_box['image_logo'] ) && is_string( $img_box['image_logo'] ) && preg_match( '#^(https?:)?//#', $img_box['image_logo'] ) ) {
					$logo_url = $img_box['image_logo'];
				}
				# فضّل الملف الأصلي دائماً (تجنّب مقاسات وسيطة ناقصة/مكسورة)
				if ( $logo_id ) {
					$full = wp_get_attachment_url( $logo_id );
					if ( $full ) {
						$logo_url = $full;
					} elseif ( ! $logo_url ) {
						$logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
					}
				}
			}

			$has_image = ( 'Image' === $mode && ! empty( $logo_url ) );
			$classes = trim( $css_class . ( $has_image ? ' has-logo-image' : ' has-logo-text' ) );

			echo '<a href="'.esc_url( home_url( '/' ) ).'" class="'.esc_attr( $classes ).'" title="'.esc_attr( $alt ).'">';
				# أيقونة الدرع فقط مع الشعار النصي
				if ( ! $has_image ) {
					echo '<span class="mark" aria-hidden="true"><i class="fas fa-shield-halved"></i></span>';
				}

				$rendered = false;
				if ( $has_image ) {
					# data-no-lazy + skip-lazy: منع LiteSpeed/كاش من كسر src
					echo '<img class="YourColor--Theme--image kayan-logo-img skip-lazy" src="'.esc_url( $logo_url ).'" alt="'.esc_attr( $alt ).'" loading="eager" decoding="async" fetchpriority="high" data-no-lazy="1" data-skip-lazy="1" onerror="this.onerror=null;this.style.display=\'none\';var t=document.createElement(\'span\');t.className=\'kayan-logo-fallback\';t.textContent=\''.esc_js( $site_name ).'\';this.parentNode.appendChild(t);" />';
					$rendered = true;
				}

				if ( ! $rendered ) {
					$logo_Text = '';
					if ( isset( $logo__data[ $mode ]['logo_Text'] ) ) {
						$logo_Text = $logo__data[ $mode ]['logo_Text'];
					} elseif ( isset( $logo__data['Text']['logo_Text'] ) ) {
						$logo_Text = $logo__data['Text']['logo_Text'];
					}
					if ( '' === $logo_Text ) {
						$logo_Text = $site_name;
					}
					if( strpos( $logo_Text,'{%') !== FALSE && strpos( $logo_Text,'%}') !== FALSE ){
						$logo_Text = str_replace('{%','<b>',$logo_Text);
						$logo_Text = str_replace('%}','</b>',$logo_Text);
					}
					echo '<span class="kayan-logo-text">'.wp_kses( $logo_Text, array( 'b' => array(), 'span' => array( 'class' => true ), 'strong' => array() ) ).'</span>';
				}
			echo '</a>';
		}
	}

# ════════════════════════════════════════════════════════
# MENU DATA — شجرة القائمة الرئيسية (أب/أبناء)
# ════════════════════════════════════════════════════════
	$rukn_menu_tree = array();
	$menu_locations = get_nav_menu_locations();
	if( isset( $menu_locations['main-menu'] ) ){
		$NavItems = wp_get_nav_menu_items( $menu_locations['main-menu'] );
		$NavItems = is_array( $NavItems ) ? $NavItems : array();
		foreach ( $NavItems as $NavItem ) {
			if( empty( $NavItem->menu_item_parent ) ){
				$rukn_menu_tree[ $NavItem->ID ] = array( 'item'=>$NavItem, 'subs'=>array() );
			}
		}
		foreach ( $NavItems as $NavItem ) {
			if( !empty( $NavItem->menu_item_parent ) && isset( $rukn_menu_tree[ $NavItem->menu_item_parent ] ) ){
				$rukn_menu_tree[ $NavItem->menu_item_parent ]['subs'][] = $NavItem;
			}
		}
	}

# CONTACT OPTIONS .
	$phonenumber_h    = get_option('phonenumber');
	$whatsapp_h       = get_option('whatsapp_number');

# SEARCHING AREA EDITS .
	$hide_search = get_option( 'hide_search' );
	if( empty( $hide_search ) ) {
		$search_placeholder = get_option('search_placeholder');
		$search_title = get_option('search_title');
		$search__Button = get_option('search__Button');
		$searchButtonType = 'icon';
	}
	if( !empty( $search__Button ) && isset( $search__Button['button_mode'] ) && $search__Button['button_mode'] == 'Text' && isset( $search__Button[ $search__Button['button_mode'] ] ) ) {
		$Text_search_button = $search__Button[ $search__Button['button_mode'] ]['logo_Text'];
		$searchButtonType = $search__Button['button_mode'];
	}else{
		$Text_search_button = '<i class="fas fa-search"></i>';
	}

echo '<root>';

	# ════════════════════════════════════════════════════════
	# LOADER — شاشة التحميل الافتتاحية
	# ════════════════════════════════════════════════════════
	if( !isset($_GET['ajax']) ){
		echo '<div id="loader">';
			echo '<div class="ld-logo">';
				$ld_mode = isset( $logo__data['logo__mode'] ) ? $logo__data['logo__mode'] : 'Text';
				$ld_text = ( $ld_mode == 'Text' && isset( $logo__data[ $ld_mode ]['logo_Text'] ) ) ? $logo__data[ $ld_mode ]['logo_Text'] : get_bloginfo('name');
				$ld_text = str_replace('{%','<span>',$ld_text);
				$ld_text = str_replace('%}','</span>',$ld_text);
				echo $ld_text;
			echo '</div>';
			echo '<div class="ld-bar"><i></i></div>';
		echo '</div>';
	}

	# ════════════════════════════════════════════════════════
	# HEADER — تصميم Rukn v3
	# ════════════════════════════════════════════════════════
	echo '<header id="hdr">';
		echo '<div class="wrap nav">';

			# SITE LOGO
			rukn_v3_render_logo( $logo__data, 'logo', 'logo__size' );

			# MAIN MENU
			echo '<nav class="menu">';
				foreach ( $rukn_menu_tree as $branch ) {
					$item = $branch['item'];
					$has_sub = !empty( $branch['subs'] );
					if( $has_sub ){
						echo '<div class="has-sub">';
							echo '<a href="'.$item->url.'">'.$item->title.'<i class="fas fa-chevron-down car"></i></a>';
							echo '<div class="sub">';
								foreach ( $branch['subs'] as $sub_item ) {
									echo '<a href="'.$sub_item->url.'">'.$sub_item->title.'</a>';
								}
							echo '</div>';
						echo '</div>';
					}else{
						echo '<a href="'.$item->url.'">'.$item->title.'</a>';
					}
				}
			echo '</nav>';

			# HEADER TOOLS
			echo '<div class="nav-cta">';

				# SEARCH — بيشتغل بنفس نظام البحث القديم (setup.js)
				if( empty( $hide_search ) ){
					if( empty( $search_placeholder ) ) $search_placeholder = 'ابحث';
					echo '<button class="icon-btn --open--searching --search--buttonType-'.$searchButtonType.'" aria-label="بحث" data-button="open-searching" data-searching-argums="'.base64_encode( json_encode( array('Text_search_button'=>$Text_search_button,'search_placeholder'=>$search_placeholder,'search_title'=>$search_title ) ) ).'">'.$Text_search_button.'</button>';
				}

				# WHATSAPP CTA (يختفي في الموبايل عبر CSS)
				if( !empty( $whatsapp_h ) ){
					echo '<a href="https://wa.me/'.$whatsapp_h.'" target="_blank" rel="noopener" class="btn btn-wa"><i class="fab fa-whatsapp"></i> واتساب</a>';
				}

				# MOBILE MENU BUTTON
				echo '<button class="icon-btn" onclick="ruknToggleMob(true)" aria-label="القائمة"><i class="fas fa-bars"></i></button>';

			echo '</div>';

		echo '</div>';
	echo '</header>';

	# ════════════════════════════════════════════════════════
	# MOBILE MENU — لوحة جانبية كحلية
	# ════════════════════════════════════════════════════════
	echo '<div class="mob" id="ruknMob">';
		echo '<button class="mob-close" onclick="ruknToggleMob(false)" aria-label="إغلاق"><i class="fas fa-xmark"></i></button>';

		# SEARCH داخل قائمة الموبايل — بنفس نظام البحث القديم (setup.js)
		if( empty( $hide_search ) ){
			echo '<button class="mob-search" data-button="open-searching" data-searching-argums="'.base64_encode( json_encode( array('Text_search_button'=>$Text_search_button,'search_placeholder'=>$search_placeholder,'search_title'=>$search_title ) ) ).'"><i class="fas fa-magnifying-glass"></i> ابحث في الموقع</button>';
		}

		# مفتاح اللغة/الدولة — هوك لنظام الـ Geo Switcher الحالي
		do_action('rukn_v3_lang_switcher');

		foreach ( $rukn_menu_tree as $branch ) {
			$item = $branch['item'];
			echo '<a href="'.$item->url.'">'.$item->title.'</a>';
			if( !empty( $branch['subs'] ) ){
				echo '<div class="sub-mob">';
					foreach ( $branch['subs'] as $sub_item ) {
						echo '<a href="'.$sub_item->url.'">'.$sub_item->title.'</a>';
					}
				echo '</div>';
			}
		}

		if( !empty( $whatsapp_h ) )
			echo '<a href="https://wa.me/'.$whatsapp_h.'" target="_blank" rel="noopener" class="btn btn-wa"><i class="fab fa-whatsapp"></i> تواصل عبر واتساب</a>';
		if( !empty( $phonenumber_h ) )
			echo '<a href="tel:'.$phonenumber_h.'" class="btn btn-call"><i class="fas fa-phone"></i> اتصل الآن</a>';

	echo '</div>';

	# ════════════════════════════════════════════════════════
	# INLINE JS مضمون — تعريف دالة القائمة في الهيدر نفسه
	# (مش معتمدة على سكربت الفوتر، فالقائمة شغالة حتى لو حصل خطأ JS لاحق)
	# ════════════════════════════════════════════════════════
	echo '<script type="text/javascript">';
		echo 'window.ruknToggleMob=function(open){var m=document.getElementById("ruknMob");if(!m)return;if(typeof open==="undefined"){m.classList.toggle("open")}else{m.classList.toggle("open",!!open)}};';
		echo 'document.addEventListener("click",function(e){';
			echo 'var link=e.target.closest("#ruknMob a");';
			echo 'if(link){var m=document.getElementById("ruknMob");if(m)m.classList.remove("open")}';
		echo '},true);';
	echo '</script>';
