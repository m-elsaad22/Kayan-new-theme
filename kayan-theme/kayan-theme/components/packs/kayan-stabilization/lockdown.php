<?php 
/**
 * Phase 1.9 — Production lockdown: KAYAN Theme is the only source of frontend behavior.
 */

if ( ! function_exists( 'kayan_lockdown_is_active' ) ) {
	/**
	 * Production lockdown is ON by default (v2027.3.9+).
	 * Disable via theme option kayan_lockdown_disable = 1 (staging only).
	 */
	function kayan_lockdown_is_active() {
		if ( ! empty( yc_get_option( 'kayan_lockdown_disable' ) ) ) {
			return false;
		}
		return true;
	}
}

if ( ! function_exists( 'kayan_lockdown_filter_header_injection' ) ) {
	function kayan_lockdown_filter_header_injection( $code ) {
		if ( ! kayan_lockdown_is_active() || is_admin() ) {
			return $code;
		}
		if ( ! empty( yc_get_option( 'kayan_lockdown_allow_header_injection' ) ) ) {
			return $code;
		}
		return '';
	}
}

if ( ! function_exists( 'kayan_lockdown_sanitize_frontend_html' ) ) {
	function kayan_lockdown_sanitize_frontend_html( $html ) {
		if ( ! is_string( $html ) || '' === $html ) {
			return $html;
		}
		if ( ! kayan_lockdown_is_active() || is_admin() ) {
			return $html;
		}

		// ═══ إصلاح الشاشة البيضاء (v1.1.1) ═══
		// كل نمط مربوط بكلمة دليلية: لا يُشغَّل الـ regex إلا لو الكلمة موجودة فعلاً
		// في الـ HTML (strpos رخيصة) — يمنع الـ backtracking الكارثي على الصفحات الكبيرة.
		$patterns = array(
			// Legacy inline trackers (Code Snippets).
			'kayan-tracking-js' => '#<script[^>]*id=["\']kayan-tracking-js["\'][^>]*>.*?</script>#is',
			'rukn_track_click'  => '#<script\b[^>]*>(?:(?!</script>).)*?rukn_track_click(?:(?!</script>).)*?</script>#is',
			'_rsa_sid'          => '#<script\b[^>]*>(?:(?!</script>).)*?_rsa_sid(?:(?!</script>).)*?</script>#is',
			'rukn_track_pv'     => '#<script\b[^>]*>(?:(?!</script>).)*?rukn_track_pv(?:(?!</script>).)*?</script>#is',
			'kt_track_click'    => '#<script\b[^>]*>(?:(?!</script>).)*?kt_track_click(?:(?!</script>).)*?</script>#is',
		);

// ═══ KAYAN v1.4.2+ — Rank Math schema لا يُحذف — مُفعَّل بالكامل ═══
// تم إزالة أنماط rank-math-schema و yoast-schema-graph عمداً

		foreach ( $patterns as $needle => $pattern ) {
			if ( false === strpos( $html, $needle ) ) {
				continue;
			}
			$clean = preg_replace( $pattern, '', $html );
			// preg_replace يرجع NULL عند فشل PCRE — لا نستبدل أبداً بقيمة فاشلة
			if ( is_string( $clean ) ) {
				$html = $clean;
			}
		}

		return $html;
	}
}

if ( ! function_exists( 'kayan_lockdown_start_output_buffer' ) ) {
	function kayan_lockdown_start_output_buffer() {
		if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}
		if ( kayan_lockdown_is_active() ) {
			ob_start( 'kayan_lockdown_sanitize_frontend_html' );
		}
	}
}
add_action( 'template_redirect', 'kayan_lockdown_start_output_buffer', -1 );

if ( ! function_exists( 'kayan_lockdown_force_single_authority' ) ) {
	function kayan_lockdown_force_single_authority() {
		if ( ! kayan_lockdown_is_active() ) {
			return;
		}

// ═══ KAYAN v1.4.2+ — Rank Math frontend مُفعَّل بالكامل ═══
// disable_rank_math_frontend() محذوفة — Rank Math يعمل طبيعياً
	}
}
add_action( 'plugins_loaded', 'kayan_lockdown_force_single_authority', PHP_INT_MAX );
add_action( 'init', 'kayan_lockdown_force_single_authority', PHP_INT_MAX );
add_action( 'wp', 'kayan_lockdown_force_single_authority', PHP_INT_MAX );

if ( ! function_exists( 'kayan_lockdown_get_external_dependency_registry' ) ) {
	/**
	 * Ops checklist — sources that must NOT run on production alongside KAYAN Theme.
	 */
	function kayan_lockdown_get_external_dependency_registry() {
		return array(
			array(
				'name'     => 'KAYAN legacy tracker (kt_*)',
				'type'     => 'code_snippet',
				'path'     => 'WP Admin → Snippets → kayan-tracking-js (or similar)',
				'injects'  => 'Inline script: kt_register_visit, kt_track_click, admin-ajax handlers',
				'why'      => 'Pre-KAYAN Track conversion tracking',
				'decision' => 'REMOVE',
			),
			array(
				'name'     => 'rukn_track_* tracker',
				'type'     => 'code_snippet',
				'path'     => 'WP Admin → Snippets → rukn_track (inline)',
				'injects'  => 'rukn_track_click, rukn_track_pv via admin-ajax',
				'why'      => 'Custom RSA pageview/click tracking',
				'decision' => 'REMOVE',
			),
			array(
				'name'     => '_rsa_sid session pageview',
				'type'     => 'code_snippet',
				'path'     => 'WP Admin → Snippets → _rsa_sid',
				'injects'  => 'sessionStorage pageview beacon',
				'why'      => 'Legacy session analytics',
				'decision' => 'REMOVE',
			),
			array(
				'name'     => 'Organization JSON-LD snippet',
				'type'     => 'code_snippet',
				'path'     => 'Snippet with sameAs:[] Organization block',
				'injects'  => 'Duplicate Organization schema in <head>',
				'why'      => 'Manual schema before KAYAN SEO',
				'decision' => 'REMOVE',
			),
			array(
				'name'     => 'Rank Math SEO',
				'type'     => 'plugin',
				'path'     => 'wp-content/plugins/seo-by-rank-math/',
				'injects'  => 'Meta, canonical, OG, rank-math-schema JSON-LD via wp_head',
				'why'      => 'SEO plugin — KAYAN v1.4.6+ keeps Rank Math fully active on frontend',
				'decision' => 'KEEP (full frontend + admin)',
			),
			array(
				'name'     => 'LiteSpeed Cache',
				'type'     => 'plugin',
				'path'     => 'wp-content/plugins/litespeed-cache/',
				'injects'  => 'HTML cache, lazy-load script, speculationrules prefetch',
				'why'      => 'Performance — purge after theme deploy',
				'decision' => 'KEEP',
			),
			array(
				'name'     => 'header___codes theme option',
				'type'     => 'theme_option',
				'path'     => 'Theme options → header codes / HeadCode',
				'injects'  => 'Raw HTML/JS in <head> before wp_head',
				'why'      => 'Legacy custom head injection',
				'decision' => 'REMOVE when lockdown active (blocked in theme)',
			),
			array(
				'name'     => 'Child theme',
				'type'     => 'theme',
				'path'     => 'wp-content/themes/*-child/',
				'injects'  => 'functions.php overrides, duplicate enqueues',
				'why'      => 'Should not exist — KAYAN is parent only',
				'decision' => 'REMOVE if present',
			),
			array(
				'name'     => 'Mu-plugins',
				'type'     => 'mu-plugin',
				'path'     => 'wp-content/mu-plugins/',
				'injects'  => 'Unknown — audit server filesystem',
				'why'      => 'Must verify manually on server',
				'decision' => 'AUDIT on server',
			),
			array(
				'name'     => 'Insert Headers / WPCode / Code Snippets Pro',
				'type'     => 'plugin',
				'path'     => 'wp-content/plugins/wpcode* or insert-headers-and-footers',
				'injects'  => 'Arbitrary head/footer scripts',
				'why'      => 'Overlaps KAYAN Track + SEO',
				'decision' => 'REMOVE tracking/SEO snippets; deactivate if empty',
			),
		);
	}
}
