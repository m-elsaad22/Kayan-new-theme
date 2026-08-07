<?php
/**
 * kayan-version — التحقق من الإصدار المُشغَّل فعلياً + تشخيص ذاتي (v1.4.2)
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

		$checks['إصلاحات FA Free (fa-free-fixes.css)'] = file_exists( $dir . '/components/styles/fa-free-fixes.css' );
		$checks['مساعد الأيقونات (icon-helpers)'] = file_exists( $dir . '/components/packs/SvgCenter/icon-helpers.php' );

		# الحزم الحرجة لإصدار 1.4.x
		$checks['حزمة الأقسام المخصصة (kayan-cpt)']   = is_dir( $dir . '/components/packs/kayan-cpt' );
		$checks['حزمة تلقيم المحتوى (kayan-seed)']     = is_dir( $dir . '/components/packs/kayan-seed' );
		$checks['نظام أزرار الاتصال (RuknContact)']    = is_dir( $dir . '/components/packs/RuknContact' );

		# يجب أن تكون محذوفة في 1.4.x — وجودها = نسخة قديمة
		$checks['نظام Home2026 محذوف (يجب ✅)']        = ! is_dir( $dir . '/components/packs/kayan-homepage' );

		# حقل كود الخريطة (أُضيف في 1.4.1)
		$footer_opts = $dir . '/components/packs/FieldsMachine/SetupFields/ThemeOptions/footer_options.php';
		$checks['حقل كود خريطة الفوتر (1.4.1+)'] = file_exists( $footer_opts ) && false !== strpos( file_get_contents( $footer_opts ), 'footer__map_embed' );

		# فقاعة الواتساب المكررة (يجب أن تكون محذوفة في 1.4.1)
		$rukn = $dir . '/components/packs/RuknContact/setup.php';
		$checks['فقاعة الواتساب المكررة محذوفة (يجب ✅)'] = file_exists( $rukn ) && false === strpos( file_get_contents( $rukn ), 'wa_mini' );

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
