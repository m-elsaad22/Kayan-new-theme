<?php 
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Forbidden' );
}
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>KAYAN Track</title>
	<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;600;700;800&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
	<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
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
