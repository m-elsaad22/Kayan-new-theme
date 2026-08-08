<?php 
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Forbidden' );
}

// أصول محلية بالكامل — لا اعتماد على أي CDN خارجي (Google Fonts / cdnjs /
// jsdelivr) حتى تعمل الصفحة كاملة (كل التابات) بدون اتصال إنترنت خارجي.
// هذا الملف يُعرض كصفحة HTML خام (require + exit من admin_init) ولا يمر عبر
// wp_head()/wp_footer()، لذا نستخدم wp_enqueue_script() + wp_print_scripts()
// مباشرة لطباعة السكربت مع دعم wp_add_inline_script() القياسي، بدل <script src> خام.
wp_enqueue_script( 'kayan-track-chartjs', kayan_track_pack_url() . 'admin/js/chart.umd.min.js', array(), '4.5.1', true );
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>KAYAN Track</title>
	<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/components/styles/kayan-admin-font.css' ); ?>">
	<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/components/styles/FontAwesome/css/all.min.css' ); ?>">
	<link rel="stylesheet" href="<?php echo esc_url( kayan_track_pack_url() . 'admin/css/track-admin.css' ); ?>">
</head>
<body class="kt-app">
	<div class="kt-shell">
		<aside class="kt-sidebar">
			<div class="kt-brand"><i class="fas fa-chart-line"></i> KAYAN Track</div>
			<nav class="kt-nav">
				<button type="button" class="kt-nav-btn active" data-tab="overview"><i class="fas fa-home"></i> نظرة عامة</button>
				<button type="button" class="kt-nav-btn" data-tab="numbers"><i class="fas fa-phone"></i> الأرقام</button>
				<button type="button" class="kt-nav-btn" data-tab="conversions"><i class="fas fa-list"></i> التحويلات</button>
				<button type="button" class="kt-nav-btn" data-tab="pages"><i class="fas fa-file-alt"></i> تقرير الصفحات</button>
				<button type="button" class="kt-nav-btn" data-tab="analytics"><i class="fas fa-chart-pie"></i> تحليل الأداء</button>
				<button type="button" class="kt-nav-btn" data-tab="visitors"><i class="fas fa-users"></i> الزوار والاحتيال</button>
				<button type="button" class="kt-nav-btn" data-tab="settings"><i class="fas fa-cog"></i> الإعدادات</button>
			</nav>
		</aside>
		<main class="kt-main">
			<div class="qf-bar" id="kt-filters">
				<button type="button" class="qf-btn active" data-preset="today">اليوم</button>
				<button type="button" class="qf-btn" data-preset="yesterday">أمس</button>
				<button type="button" class="qf-btn" data-preset="7d">آخر 7 أيام</button>
				<button type="button" class="qf-btn" data-preset="month">هذا الشهر</button>
				<span class="date-range">
					<input type="date" class="date-inp" id="kt-date-from">
					<span>—</span>
					<input type="date" class="date-inp" id="kt-date-to">
					<button type="button" class="apply-btn" id="kt-apply-custom">تطبيق</button>
				</span>
			</div>
			<div id="kt-toast" class="kt-toast" hidden></div>
			<div id="kt-content"></div>
		</main>
	</div>
	<?php wp_print_scripts( 'kayan-track-chartjs' ); ?>
	<script>
		window.KayanTrackAdmin = {
			ajaxUrl: <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>,
			nonce: <?php echo wp_json_encode( wp_create_nonce( 'kayan_track_nonce' ) ); ?>,
			homeUrl: <?php echo wp_json_encode( home_url( '/' ) ); ?>
		};
	</script>
	<script src="<?php echo esc_url( kayan_track_pack_url() . 'admin/js/track-admin.js' ); ?>"></script>
</body>
</html>
