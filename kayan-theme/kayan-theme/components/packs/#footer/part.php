<?php
# ════════════════════════════════════════════════════════
# FOOTER — تصميم Rukn v3
# ════════════════════════════════════════════════════════
	$whatsapp        = get_option('whatsapp_number');
	$phonenumber_f   = get_option('phonenumber');
	$company__adress = get_option('company__adress');

	echo '<footer>';
		echo '<div class="wrap">';
			echo '<div class="fgrid">';

				# ═══════════ العمود الأول: اللوجو + الوصف + التواصل + السوشيال ═══════════
				$hide_footer__first__slice = get_option('hide_footer__first__slice');
				if( empty( $hide_footer__first__slice ) ){
					echo '<div class="fcol">';

						# LOGO FOOTER .
						$hide_logo_footer = get_option('hide_logo_footer');
						if( empty( $hide_logo_footer ) ){
							$footer__logo = get_option( 'footer__logo' );
							$footer__logo = ( ( is_array( $footer__logo ) ) ) ? $footer__logo : array();
							if( empty( $footer__logo ) && ( !isset( $footer__logo['logo__mode'] ) || ( isset( $footer__logo['logo__mode'] ) && isset( $footer__logo[ $footer__logo['logo__mode'] ] ) ) ) )
								$footer__logo = array( 'logo__mode'=>'Text','Text'=>array('logo_Text'=>'ركن {%التطور%}') );
							echo '<div class="flogo-wrap">';
								rukn_v3_render_logo( $footer__logo, 'flogo', 'footer_sizelogo' );
							echo '</div>';
						}

						# FOOTER DESCRIPTION .
						$hide_description_footer = get_option('hide_description_footer');
						if( empty( $hide_description_footer ) )	$footer__content = get_option('footer__content');
						if( empty( $hide_description_footer ) && !empty( $footer__content ) ) echo '<p>'.$footer__content.'</p>';

						# CONTACT LINKS .
						echo '<div class="fcontact">';
							if( !empty( $phonenumber_f ) )
								echo '<a href="tel:'.$phonenumber_f.'"><i class="fas fa-phone"></i> '.$phonenumber_f.'</a>';
							if( !empty( $whatsapp ) )
								echo '<a href="https://wa.me/'.$whatsapp.'" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> تواصل عبر واتساب</a>';
							if( !empty( $company__adress ) )
								echo '<a href="'.( ( !empty( get_option('footer__company__adress_url') ) ) ? get_option('footer__company__adress_url') : '#' ).'"><i class="fas fa-location-dot"></i> '.$company__adress.'</a>';
						echo '</div>';

						# SOCIAL ICONS .
						$social_footer = get_option( 'social_footer' );
						if( empty( $social_footer ) ) {
							$social_footer_list = get_option('social_footer_list');
							$social_footer_list = ( ( is_array( $social_footer_list ) ) ) ? $social_footer_list : array();
							if( !empty( $social_footer_list ) ){
								$SocialIcon = array(
									'facebook'=>'<i class="fab fa-facebook-f"></i>',
									'twitter'=>'<i class="fab fa-twitter"></i>',
									'telegram'=>'<i class="fa-brands fa-telegram"></i>',
									'youtube'=>'<i class="fab fa-youtube"></i>',
									'linkedin'=>'<i class="fab fa-linkedin-in"></i>',
									'instagram'=>'<i class="fab fa-instagram"></i>',
									'tiktok'=>'<i class="fab fa-tiktok"></i>',
									'whatsapp'=>'<i class="fab fa-whatsapp"></i>',
								);
								echo '<div class="fsocial">';
									foreach ( $social_footer_list as $social__item ) {
										$social_value = get_option($social__item);
										if( !empty($social_value) && isset( $SocialIcon[ $social__item ] ) ) {
											echo'<a class="'.$social__item.'" title="'.$social__item.'" aria-label="'.$social__item.'" target="_blank" rel="noopener" href="'.$social_value.'">'.$SocialIcon[ $social__item ].'</a>';
										}
									}
								echo '</div>';
							}
						}

					echo '</div>';
				}

				# ═══════════ العمود الثاني: قائمة الخدمات ═══════════
				$hide_footer__first_menu = get_option('hide_footer__first_menu');
				if( empty( $hide_footer__first_menu ) ) $footer__first_menu = get_option('footer__first_menu');

				if( empty( $hide_footer__first_menu ) && !empty( $footer__first_menu ) ){
					echo '<div class="fcol">';
						$footer__title_first_menu = get_option('footer__title_first_menu');
						echo '<h4>'.( ( !empty( $footer__title_first_menu ) ) ? $footer__title_first_menu : 'الخدمات' ).'</h4>';
						$NavList = wp_get_nav_menu_items($footer__first_menu);
						echo '<ul>';
							foreach ( is_array( $NavList ) ? $NavList : array() as $pages) {
								echo '<li><a href="'.$pages->url.'" class="activable"><i class="fas fa-chevron-left"></i> '.$pages->title.'</a></li>';
							}
						echo '</ul>';
					echo '</div>';
				}

				# ═══════════ العمود الثالث: قائمة المدن ═══════════
				$hide_footer__second_menu = get_option('hide_footer__second_menu');
				if( empty( $hide_footer__second_menu ) ) $footer__second_menu = get_option('footer__second_menu');

				if( empty( $hide_footer__second_menu ) && !empty( $footer__second_menu ) ){
					echo '<div class="fcol">';
						$footer__title_second_menu = get_option('footer__title_second_menu');
						echo '<h4>'.( ( !empty( $footer__title_second_menu ) ) ? $footer__title_second_menu : 'المدن' ).'</h4>';
						$NavList = wp_get_nav_menu_items($footer__second_menu);
						echo '<ul>';
							foreach ( is_array( $NavList ) ? $NavList : array() as $pages) {
								echo '<li><a href="'.$pages->url.'" class="activable"><i class="fas fa-chevron-left"></i> '.$pages->title.'</a></li>';
							}
						echo '</ul>';
					echo '</div>';
				}

				# ═══════════ العمود الرابع: روابط سريعة (قائمة جديدة) + زر تحدث مع خبير ═══════════
				$hide_footer__third_menu = get_option('hide_footer__third_menu');
				if( empty( $hide_footer__third_menu ) ){
					$footer__third_menu = get_option('footer__third_menu');
					echo '<div class="fcol">';
						$footer__title_third_menu = get_option('footer__title_third_menu');
						echo '<h4>'.( ( !empty( $footer__title_third_menu ) ) ? $footer__title_third_menu : 'روابط سريعة' ).'</h4>';
						echo '<ul>';
							if( !empty( $footer__third_menu ) ){
								$NavList = wp_get_nav_menu_items($footer__third_menu);
								foreach ( is_array( $NavList ) ? $NavList : array() as $pages) {
									echo '<li><a href="'.$pages->url.'" class="activable"><i class="fas fa-chevron-left"></i> '.$pages->title.'</a></li>';
								}
							}else{
								# روابط افتراضية لحد ما تتظبط قائمة من لوحة التحكم
								echo '<li><a href="'.home_url().'" class="activable"><i class="fas fa-chevron-left"></i> الرئيسية</a></li>';
								echo '<li><a href="'.home_url('/blog/').'" class="activable"><i class="fas fa-chevron-left"></i> المدونة</a></li>';
								echo '<li><a href="'.home_url('/contact-us/').'" class="activable"><i class="fas fa-chevron-left"></i> اتصل بنا</a></li>';
							}
						echo '</ul>';
						$footer__expert_url = get_option('footer__expert_url');
						if( empty( $footer__expert_url ) ) $footer__expert_url = home_url('/contact-us/');
						echo '<a href="'.$footer__expert_url.'" class="btn btn-quote" style="margin-top:6px"><i class="fas fa-headset"></i> تحدث مع خبير</a>';
					echo '</div>';
				}

			echo '</div>'; # .fgrid

		echo '</div>'; # .wrap

		# ═══════════ شريط الخريطة ═══════════
		$hide_footer__map = get_option('hide_footer__map');
		if( empty( $hide_footer__map ) ){
			$footer__map_embed = trim( (string) get_option('footer__map_embed') );

			# ═══ v1.4.1: يقبل الرابط المباشر أو كود <iframe> كاملاً منسوخاً من جوجل ═══
			if( !empty( $footer__map_embed ) && false !== stripos( $footer__map_embed, '<iframe' ) ){
				if( preg_match( '/src=["\']([^"\']+)["\']/i', $footer__map_embed, $m__src ) ){
					$footer__map_embed = $m__src[1];
				} else {
					$footer__map_embed = '';
				}
			}
			# قبول روابط الخرائط فقط (حماية من حقن روابط خارجية)
			if( !empty( $footer__map_embed ) && !preg_match( '#^https://([a-z0-9\-]+\.)*(google\.[a-z.]+|maps\.google\.[a-z.]+|goo\.gl)/#i', $footer__map_embed ) ){
				$footer__map_embed = '';
			}
			if( empty( $footer__map_embed ) ) $footer__map_embed = 'https://maps.google.com/maps?q=Dubai,United+Arab+Emirates&z=12&output=embed';
			echo '<div class="fmap">';
				echo '<div class="fmap-addr-row">';
					echo '<i class="fas fa-location-dot"></i>';
					echo '<div><b>مقرنا الرئيسي</b><span>'.( ( !empty( $company__adress ) ) ? $company__adress : 'دبي، الإمارات العربية المتحدة' ).'</span></div>';
				echo '</div>';
				echo '<iframe class="fmap-frame" src="'.esc_url( $footer__map_embed ).'" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="'.esc_attr( 'خريطة موقع '.get_bloginfo('name') ).'"></iframe>';
			echo '</div>';
		}

		# ═══════════ الشريط السفلي ═══════════
		echo '<div class="wrap">';
			$hide_copyrights = get_option('hide_copyrights');
			if( empty( $hide_copyrights ) ){
				$copyrights = get_option('copyrights');
				if( empty( $copyrights ) ) $copyrights = '© {%YEAR%} '.get_bloginfo('name').'. جميع الحقوق محفوظة.';
				$currentYear = date('Y');
				$copyrights = str_replace('{%YEAR%}', $currentYear, $copyrights);
				echo '<div class="fbottom">'.$copyrights.'</div>';
			}
		echo '</div>';

	echo '</footer>';

	# ════════════════════════════════════════════════════════
	# FLOATING BUTTONS — اتصال + واتساب (بنفس منطق الأرقام لكل بوست)
	# ════════════════════════════════════════════════════════
	if( is_single() || is_page() ){
		global $post;
		$phonenumber = get_post_meta( $post->ID,'phone_number',true );
		if( empty( $phonenumber ) ) $phonenumber = get_option('phonenumber');

		$whatsapp_number = get_post_meta( $post->ID,'whatsapp_number',true );
		if( empty( $whatsapp_number ) ) $whatsapp_number = get_option('whatsapp_number');
	}else{
		$phonenumber = get_option('phonenumber');
		$whatsapp_number = get_option('whatsapp_number');
	}

	echo '<div class="fab-stack" id="ruknFab">';
		if( !empty( $phonenumber ) )
			echo '<a href="tel:'.$phonenumber.'" class="fab-btn fab-call" aria-label="اتصال" data-call="Phone"><i class="fas fa-phone"></i></a>';
		if( !empty( $whatsapp_number ) )
			echo '<a href="https://wa.me/'.$whatsapp_number.'" target="_blank" rel="noopener" class="fab-btn fab-wa" aria-label="واتساب" data-call="whatsapp"><i class="fab fa-whatsapp"></i></a>';
	echo '</div>';

echo '</root>';

$HTML_otput = ob_get_clean();
$SVG_List = array();
if( strpos( $HTML_otput , 'data-svg-loaders="') !== FALSE ){
	$SVGLoader = explode('data-svg-loaders="', $HTML_otput);unset($SVGLoader[0]);
	foreach ( $SVGLoader as $w => $ee) {
		$SvgName = explode('"', $ee)[0];
		if( !isset( $SVG_List[ $SvgName ] ) ){
			$SVG_List[ $SvgName ] = SVGIcon($SvgName);
		}
	}
}

echo $HTML_otput;

# STYLE CSS
$Css_List = array();
if(isset($Styles)){
	foreach ($Styles as $skey => $meky) {
		$Css_List[$skey] = $this->StylesURL.$meky.'?v='.rand();
	}
}

if( isset($_GET['ajax']) ) {

	$output = ob_get_clean();
	$title = wp_title( '|', false, 'right' );
	$CurrentURL = $this->GetCurrentURL();
	$CurrentURL = remove_query_arg( 'ajax', $CurrentURL );
	if( is_search() ) {
		$search_query = get_search_query();
		$CurrentURL = home_url('/search/'.str_replace(' ', '-', $search_query).'/');
	}
	$array = array(
		"output"	=> $output,
		"title"		=> $title,
		"url"		=> $CurrentURL,
		"Css_List" => $Css_List,
		"SVG_List" => $SVG_List,
	);
	$json = safe_json_encode($array);
	footer('Content-Length: '.strlen($json)); // HERE IS THE PROBLEM
	footer('Content-type: application/json');
	echo $json;
}else {
	###
	global $current_user;
	###
	$TempDIR = $this->TempURL;
	echo '<script type="text/javascript">';
		echo "var WPAdminAjax = '".admin_url('admin-ajax.php')."';";
		echo "var AdminAjax = '".home_url('/AjaxCenter/')."';";
		echo "var HomeURL = '".home_url()."';";
		echo "var TmpDIR = '".get_template_directory_uri()."';";
		echo "var ISMobile = ".((wp_is_mobile()) ? 'true' : 'false').";";
		echo "var IsSpeed = ".( ( IsSpeed() != false ) ? 'true' : 'false').";";
		echo "var IsHome = ".((is_home()) ? 'true' : 'false').";";
		echo "var IsSingle = ".(is_single() ? 'true' : 'false').";";

		echo "var Currentuser_ID = '".$current_user->ID."';";
		echo "var Currentuser_first_name = '".$current_user->first_name."';";
		echo "var Currentuser_last_name = '".$current_user->last_name."';";
		echo "var Currentuser_display_name = '".$current_user->display_name."';";
		echo "var Currentuser_email = '".$current_user->user_email."';";
		echo "var Currentuser_Logged = true;";
		echo "var SVG_List = ".json_encode($SVG_List).";";
		echo 'function onTouchStart() {}document.addEventListener(\'touchstart\', onTouchStart, {passive: true});';
	echo '</script>';

	# ════════════════════════════════════════════════════════
	# RUKN v3 JS — اللودر + الهيدر اللاصق + الأزرار العائمة + قائمة الموبايل
	# ════════════════════════════════════════════════════════
	echo '<script type="text/javascript">';
		echo "window.addEventListener('load',function(){var l=document.getElementById('loader');if(l)setTimeout(function(){l.classList.add('out')},700)});";
		echo "setTimeout(function(){var l=document.getElementById('loader');if(l)l.classList.add('out')},4000);";
		echo "var ruknHdr=document.getElementById('hdr'),ruknFab=document.getElementById('ruknFab');";
		echo "function ruknOnScroll(){var y=window.scrollY;if(ruknHdr)ruknHdr.classList.toggle('scrolled',y>40);if(ruknFab)ruknFab.classList.toggle('show',y>500);}";
		echo "window.addEventListener('scroll',ruknOnScroll,{passive:true});ruknOnScroll();";
		echo "function ruknToggleMob(open){var m=document.getElementById('ruknMob');if(m)m.classList.toggle('open',open)}";
		echo "var ruknRv=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('on');ruknRv.unobserve(e.target)}})},{threshold:.12});";
		echo "document.querySelectorAll('.rv,.rv-l').forEach(function(el){ruknRv.observe(el)});";
	echo '</script>';

	if( IsSpeed() == false && ( is_single() || is_page() || ( isset( $Widgets__list ) && in_array( 'works_v1',$Widgets__list ) ) ) ){
		echo "<script id='rendered-js' type='module'>";
			echo "import PhotoSwipeLightbox from 'https://unpkg.com/photoswipe/dist/photoswipe-lightbox.esm.js';";
			echo "if( $( '[pswp]' ).length ){";
				echo "var lightbox = new PhotoSwipeLightbox({";
				  echo "gallery: '[pswp]',";
				  echo "children: 'a',";
				  echo "initialZoomLevel: 'fit',";
				  echo "secondaryZoomLevel: 1,";
				  echo "maxZoomLevel: 5,";
				  echo "pswpModule: () => import('https://unpkg.com/photoswipe') });";

				  echo "function LightboxInit() {";
				  	echo "lightbox.init();";
				  echo "}";
				  echo "LightboxInit();";
				echo "$( document ).ajaxComplete(function() {";
					echo "LightboxInit();";
				echo "});";
			echo "}";

			echo "if( $( '[data-gallery-popover]' ).length ){";
				echo "const loadGalleryElements = document.querySelectorAll('[data-gallery-popover]');";
				echo "const lightboxes = [];";

				echo "loadGalleryElements.forEach((element, index) => {";
				  	echo "const argumentsBase64 = element.getAttribute('data-gallery-popover');";
				  	echo "const decodedArguments = atob(argumentsBase64);";
				  	echo "const parsedArguments = JSON.parse(decodedArguments);";

					echo "const options = {";
					  	echo "dataSource: parsedArguments,";
					  	echo "showHideAnimationType: 'none',";
					  	echo "pswpModule: () => import('https://unpkg.com/photoswipe'),";
					echo "};";

				  	echo "const lightbox = new PhotoSwipeLightbox(options);";
				  	echo "lightbox.init();";

				  	echo "element.onclick = () => {";
				    	echo "lightbox.loadAndOpen(index);";
				  	echo "};";
				echo "});";
			echo "}";

	    echo "</script>";
	}

	wp_footer();

	echo '</body>';
	echo '</html>';
}
