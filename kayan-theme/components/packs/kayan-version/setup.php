<?php
/**
 * kayan-version — التحقق من الإصدار المُشغَّل فعلياً + تشخيص ذاتي (v2.4.6)
 * ═══════════════════════════════════════════════════════════════════
 * يعرض إصدار القالب في شريط الأدمن العلوي، ويفحص تلقائياً وجود
 * الملفات الحرجة (خطوط Font Awesome، الحزم) ليكشف أي رفع ناقص.
 * ═══════════════════════════════════════════════════════════════════
 */

if ( ! function_exists( 'kayan_version_get' ) ) {

	function kayan_version_get() {
		$theme = wp_get_theme( get_template() );
		return $theme->get( 'Version' );
	}

	/**
	 * فحص ذاتي: هل كل الملفات الحرجة موجودة؟
	 */
	function kayan_version_healthcheck() {
		$dir    = get_template_directory();
		$checks = array();

		# خطوط Font Awesome — غيابها = كل الأيقونات مربعات فارغة
		$fa_css   = $dir . '/components/styles/FontAwesome/css/all.min.css';
		$fa_fonts = array( 'fa-solid-900.woff2', 'fa-regular-400.woff2', 'fa-brands-400.woff2' );

		$checks['ملف Font Awesome CSS'] = file_exists( $fa_css );
		$fonts_ok = true;
		foreach ( $fa_fonts as $f ) {
			if ( ! file_exists( $dir . '/components/styles/FontAwesome/webfonts/' . $f ) ) {
				$fonts_ok = false;
			}
		}
		$checks['خطوط Font Awesome (webfonts)'] = $fonts_ok;
		$checks['fa-free-fixes.css'] = file_exists( $dir . '/components/styles/fa-free-fixes.css' );

		# حزم أساسية
		$checks['حزمة الأقسام المخصصة (kayan-cpt)']   = is_dir( $dir . '/components/packs/kayan-cpt' );
		$checks['حزمة تلقيم المحتوى (kayan-seed)']     = is_dir( $dir . '/components/packs/kayan-seed' );
		$checks['نظام أزرار الاتصال (RuknContact)']    = is_dir( $dir . '/components/packs/RuknContact' );
		$checks['KAYAN Platform']                     = is_dir( $dir . '/components/packs/kayan-platform' );
		$checks['KAYAN SEO Bridge']                   = is_dir( $dir . '/components/packs/kayan-seo' );
		$checks['KAYAN Price/Pay']                    = is_dir( $dir . '/components/packs/kayan-price-pay' );
		$checks['نظام الحجز (kayan-booking)']         = is_dir( $dir . '/components/packs/kayan-booking' );
		$checks['admin-mobile.css']                   = file_exists( $dir . '/components/packs/FieldsMachine/UI/css/admin-mobile.css' );
		$checks['admin-ui-fixes.css']                 = file_exists( $dir . '/components/packs/FieldsMachine/UI/css/admin-ui-fixes.css' );

		# يجب أن تكون محذوفة — وجودها = نسخة قديمة / رفع متداخل
		$checks['نظام Home2026 محذوف (يجب ✅)']        = ! is_dir( $dir . '/components/packs/kayan-homepage' );
		$checks['لا يوجد قالب متداخل kayan-theme/']   = ! is_dir( $dir . '/kayan-theme' );

		# حقل كود الخريطة
		$footer_opts = $dir . '/components/packs/FieldsMachine/SetupFields/ThemeOptions/footer_options.php';
		$checks['حقل كود خريطة الفوتر'] = file_exists( $footer_opts ) && false !== strpos( (string) file_get_contents( $footer_opts ), 'footer__map_embed' );

		# فقاعة الواتساب المكررة (يجب أن تكون محذوفة)
		$rukn = $dir . '/components/packs/RuknContact/setup.php';
		$checks['فقاعة الواتساب المكررة محذوفة (يجب ✅)'] = file_exists( $rukn ) && false === strpos( (string) file_get_contents( $rukn ), 'wa_mini' );

		# الهيدر بلا Parse error شائع
		$header = $dir . '/components/packs/#header/part.php';
		$checks['هيدر بلا قوس زائد معروف'] = file_exists( $header ) && false === strpos( (string) file_get_contents( $header ), "}\n\t\t}\n\n\n\t\t//" );

		return $checks;
	}

	/**
	 * شارة الإصدار في شريط الأدمن
	 */
	function kayan_version_adminbar( $wp_admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$version = kayan_version_get();
		$checks  = kayan_version_healthcheck();
		$failed  = count( array_filter( $checks, function( $v ) { return ! $v; } ) );

		$color = $failed ? '#ffb900' : '#46b450';
		$icon  = $failed ? '⚠' : '●';

		$wp_admin_bar->add_node( array(
			'id'    => 'kayan-version',
			'title' => '<span style="color:'.$color.'">'.$icon.'</span> KAYAN v'.esc_html( $version ) . ( $failed ? ' — '.$failed.' تنبيه' : '' ),
			'href'  => admin_url( 'themes.php?page=kayan-seed-home' ),
			'meta'  => array( 'title' => 'إصدار القالب المُشغَّل فعلياً' ),
		) );
	}
	add_action( 'admin_bar_menu', 'kayan_version_adminbar', 100 );

	/**
	 * لوحة التشخيص (تُعرض داخل صفحة أداة التلقيم)
	 */
	function kayan_version_render_panel() {
		$version = kayan_version_get();
		$checks  = kayan_version_healthcheck();
		$failed  = count( array_filter( $checks, function( $v ) { return ! $v; } ) );

		echo '<div class="card" style="padding:16px;max-width:100%;margin-bottom:18px">';
			echo '<h2 style="margin-top:0">الإصدار المُشغَّل فعلياً: <code style="font-size:16px">v' . esc_html( $version ) . '</code></h2>';

			if ( $failed ) {
				echo '<div class="notice notice-warning inline" style="margin:10px 0"><p><strong>تحذير:</strong> ' . intval( $failed ) . ' فحص لم ينجح — على الأغلب الرفع ناقص أو النسخة قديمة. احذف مجلد القالب القديم بالكامل وارفع الجديد، ثم امسح كاش LiteSpeed.</p></div>';
			} else {
				echo '<div class="notice notice-success inline" style="margin:10px 0"><p>كل الفحوصات نجحت — النسخة مرفوعة بالكامل وسليمة.</p></div>';
			}

			echo '<table class="widefat striped" style="margin-top:10px"><tbody>';
				foreach ( $checks as $label => $ok ) {
					echo '<tr>';
						echo '<td style="width:70%">' . esc_html( $label ) . '</td>';
						echo '<td>' . ( $ok ? '<span style="color:#46b450;font-weight:700">✅ سليم</span>' : '<span style="color:#dc3232;font-weight:700">❌ مفقود</span>' ) . '</td>';
					echo '</tr>';
				}
			echo '</tbody></table>';
		echo '</div>';
	}
}
