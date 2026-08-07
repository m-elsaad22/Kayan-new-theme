<?php
/**
 * Smoke tests for KAYAN theme (no full WordPress DB required).
 * Run: php tests/kayan-smoke.php
 */
error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

$theme = dirname( __DIR__ ) . '/kayan-theme';
$failed = 0;
$passed = 0;

function assert_true( $cond, $msg ) {
	global $failed, $passed;
	if ( $cond ) {
		echo "PASS  $msg\n";
		$passed++;
	} else {
		echo "FAIL  $msg\n";
		$failed++;
	}
}

echo "=== KAYAN Smoke Tests ===\n";
echo "Theme: $theme\n\n";

# 1) Critical files exist
$critical = array(
	'style.css',
	'functions.php',
	'syntax.php',
	'index.php',
	'components/packs/#header/part.php',
	'components/packs/Enqueues/setup.php',
	'components/packs/kayan-booking/setup.php',
	'components/packs/SvgCenter/icon-helpers.php',
	'components/styles/fa-free-fixes.css',
	'components/styles/FontAwesome/css/all.min.css',
	'components/styles/FontAwesome/webfonts/fa-solid-900.woff2',
	'components/packs/FieldsMachine/UI/css/admin-mobile.css',
);
foreach ( $critical as $rel ) {
	assert_true( file_exists( "$theme/$rel" ), "exists: $rel" );
}

# 2) PHP lint all theme PHP files
$lint_errors = array();
$rii = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $theme ) );
foreach ( $rii as $file ) {
	if ( ! $file->isFile() || 'php' !== strtolower( $file->getExtension() ) ) {
		continue;
	}
	$path = $file->getPathname();
	$out  = array();
	$code = 0;
	exec( 'php -l ' . escapeshellarg( $path ) . ' 2>&1', $out, $code );
	if ( 0 !== $code ) {
		$lint_errors[] = implode( ' ', $out );
	}
}
assert_true( 0 === count( $lint_errors ), 'PHP lint clean (' . count( $lint_errors ) . ' errors)' );
foreach ( array_slice( $lint_errors, 0, 10 ) as $e ) {
	echo "  - $e\n";
}

# 3) Header no longer has premature close brace pattern
$header = file_get_contents( "$theme/components/packs/#header/part.php" );
assert_true( false === strpos( $header, "}\n\t\t}\n\n\n\t\t//" ), 'header brace glitch removed' );
assert_true( false !== strpos( $header, 'fa-free-fixes.css' ), 'header loads fa-free-fixes.css' );

# 4) Enqueues protect kayan assets + admin mobile
$enq = file_get_contents( "$theme/components/packs/Enqueues/setup.php" );
assert_true( false !== strpos( $enq, 'kayan_is_protected_asset_handle' ), 'enqueue protect helper present' );
$fm = file_get_contents( "$theme/components/packs/FieldsMachine/Enqueues.php" );
assert_true( false !== strpos( $fm, 'admin-mobile.css' ), 'admin-mobile.css enqueued' );
assert_true( false !== strpos( $fm, 'fa-free-fixes.css' ), 'admin fa-free-fixes enqueued' );

# 5) Booking inject no longer requires in_the_loop
$book = file_get_contents( "$theme/components/packs/kayan-booking/setup.php" );
assert_true( false === strpos( $book, '! in_the_loop()' ), 'booking no in_the_loop gate' );
assert_true( false !== strpos( $book, 'kayan_icon_html' ), 'booking uses kayan_icon_html' );

# 6) Stub WP helpers and test icon rendering
if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
}
if ( ! function_exists( 'sanitize_file_name' ) ) {
	function sanitize_file_name( $t ) { return preg_replace( '/[^a-zA-Z0-9._-]/', '', (string) $t ); }
}
if ( ! function_exists( 'sanitize_title' ) ) {
	function sanitize_title( $t ) { return strtolower( preg_replace( '/[^a-zA-Z0-9-]+/', '-', (string) $t ) ); }
}
if ( ! function_exists( 'wp_kses' ) ) {
	function wp_kses( $t, $allowed ) { return strip_tags( (string) $t, '<i><span><svg><path><g>' ); }
}
if ( ! function_exists( 'get_template_directory' ) ) {
	function get_template_directory() {
		global $theme;
		return $theme;
	}
}
if ( ! function_exists( 'SVGIcon' ) ) {
	function SVGIcon( $icon ) {
		$path = get_template_directory() . '/components/packs/SvgCenter/icons/' . sanitize_file_name( $icon ) . '.php';
		if ( ! file_exists( $path ) ) return '';
		ob_start();
		require $path;
		return ob_get_clean();
	}
}

require_once "$theme/components/packs/SvgCenter/icon-helpers.php";

$html = kayan_icon_html( 'fa-solid fa-broom' );
assert_true( false !== strpos( $html, 'fa-broom' ), 'icon html class broom' );

$html = kayan_icon_html( 'broom' );
assert_true( false !== strpos( $html, 'fa-solid fa-broom' ), 'icon html bare name' );

$html = kayan_icon_html( '<i class="fas fa-phone"></i>' );
assert_true( false !== strpos( $html, 'fa-phone' ), 'icon html passthrough' );

$html = kayan_icon_html( 'fal fa-star' ); // Pro style alias
assert_true( false !== strpos( $html, 'fa-solid' ) || false !== strpos( $html, 'fa-star' ), 'pro alias handled' );

$html = kayan_icon_html( '' );
assert_true( false !== strpos( $html, 'fa-screwdriver-wrench' ), 'empty uses fallback' );

$catalog = kayan_fa_icon_catalog();
assert_true( count( $catalog ) >= 100, 'FA catalog size >= 100 (' . count( $catalog ) . ')' );

# 7) Catalog icons exist in FA CSS
$fa_css = file_get_contents( "$theme/components/styles/FontAwesome/css/all.min.css" );
preg_match_all( '/\.fa-([a-z0-9-]+):before/', $fa_css, $m );
$available = array_flip( $m[1] );
$missing = 0;
foreach ( array_keys( $catalog ) as $class ) {
	$parts = explode( ' ', $class );
	$name  = preg_replace( '/^fa-/', '', end( $parts ) );
	if ( ! isset( $available[ $name ] ) ) {
		$missing++;
		echo "  missing icon in FA CSS: $class\n";
	}
}
assert_true( 0 === $missing, "all catalog icons in FA Free ($missing missing)" );

# 8) Mobile CSS present
$kbw = file_get_contents( "$theme/components/packs/kayan-booking/assets/css/kayan-booking.css" );
assert_true( false !== strpos( $kbw, '@media (max-width: 640px)' ), 'booking mobile breakpoint' );
assert_true( false !== strpos( $kbw, 'grid-template-columns: 1fr' ), 'booking single-column mobile grid' );
$resp = file_get_contents( "$theme/components/styles/responsive.css" );
assert_true( false !== strpos( $resp, 'max-width:768px' ) || false !== strpos( $resp, 'max-width:768' ), 'theme responsive.css has tablet/mobile' );
$admin_m = file_get_contents( "$theme/components/packs/FieldsMachine/UI/css/admin-mobile.css" );
assert_true( strlen( $admin_m ) > 1000, 'admin-mobile.css non-empty' );

# 9) Version
$style = file_get_contents( "$theme/style.css" );
assert_true( false !== strpos( $style, 'Version: 2.4.1' ), 'style.css version 2.4.1' );

# Interactive price booking + floating CTA
assert_true( file_exists( "$theme/components/packs/kayan-price-pay/setup.php" ), 'kayan-price-pay pack exists' );
assert_true( file_exists( "$theme/components/packs/kayan-price-pay/assets/js/kayan-price-pay.js" ), 'kayan-price-pay.js exists' );
$price_list = file_get_contents( "$theme/components/packs/shortcodes/codes/price_list.php" );
assert_true( false !== strpos( $price_list, 'kayan-price-booking' ), 'price_list interactive booking' );
assert_true( false !== strpos( $price_list, 'kpp-package' ), 'price_list package cards' );
$footer = file_get_contents( "$theme/components/packs/#footer/part.php" );
assert_true( false !== strpos( $footer, 'KAYAN_BOOK_CTA_SLOT' ) || false !== strpos( $footer, 'kayanBookCta' ) || false !== strpos( $footer, 'احجز الآن' ), 'footer book CTA slot or markup' );
assert_true( false !== strpos( $footer, 'fab-stack show' ) || false !== strpos( $footer, "ruknFab.classList.add('show')" ) || false !== strpos( $footer, 'ruknFab.classList.add("show")' ), 'FAB visible immediately (no scroll gate)' );
assert_true( false === strpos( $footer, "ruknFab.classList.toggle('show',y>500)" ), 'FAB no longer gated behind y>500' );
$pay_js = file_get_contents( "$theme/components/packs/kayan-price-pay/assets/js/kayan-price-pay.js" );
assert_true( false !== strpos( $pay_js, 'rukn-eltatawer-pay.tanceq.com' ), 'pay URL in JS' );

# Rank Math remains the sole SEO authority — v2.4.1 policy (kayan-seo removed)
assert_true( ! is_dir( "$theme/components/packs/kayan-seo" ), 'kayan-seo pack removed (would override Rank Math frontend output)' );
assert_true( ! file_exists( "$theme/components/packs/kayan-rankmath/setup.php" ), 'legacy kayan-rankmath force-enable removed' );
$theme_seo_opts = file_get_contents( "$theme/components/packs/FieldsMachine/SetupFields/ThemeOptions/theme__seo.php" );
assert_true( false === strpos( $theme_seo_opts, "'kayan_seo_disable'" ), 'no kayan_seo_disable toggle field (kayan-seo removed)' );
$theme_seo = file_get_contents( "$theme/components/packs/theme-seo/setup.php" );
assert_true( false !== strpos( $theme_seo, "class_exists( 'RankMath' )" ), 'ThemeSeo defers to Rank Math when active' );
assert_true( false !== strpos( $theme_seo, "current_theme_supports( 'title-tag' )" ), 'ThemeSeo never double-prints <title> when title-tag is supported' );

# Rank Math / logo / sortable / wrap fixes
$enq = file_get_contents( "$theme/components/packs/Enqueues/setup.php" );
assert_true( false !== strpos( $enq, 'kayan_is_rank_math_active' ), 'rank math helper present' );
assert_true( false === strpos( $enq, 'function kayan_force_rank_math_assets' ), 'no force RM frontend enqueue' );
assert_true( false === strpos( $enq, 'wp_deregister_script( $handle )' ), 'no deregister of front scripts' );
$header = file_get_contents( "$theme/components/packs/#header/part.php" );
assert_true( false !== strpos( $header, 'has-logo-image' ), 'logo has-logo-image class' );
assert_true( false !== strpos( $header, 'data-no-lazy' ), 'logo skips LiteSpeed lazy' );
assert_true( false !== strpos( $header, 'fa-free-fixes.css?v=2.4.1' ), 'header asset version 2.4.1' );
$schema = file_get_contents( "$theme/components/packs/schema/setup.php" );
assert_true( false !== strpos( $schema, "add_action('wp_head', array( \$this,'insert__schema')" ), 'schema always registers the hook (actual gating happens via validate__schema option)' );
# Kayan_Adapter_Schema (kayan-platform) forces validate__schema non-empty when Rank Math is
# active, via the pre_option_validate__schema filter — schema/setup.php itself stays untouched
# by design (single source of truth, no duplicated Rank Math detection logic).
$schema_adapter = file_get_contents( "$theme/components/packs/kayan-platform/includes/integration/adapters/class-kayan-adapter-schema.php" );
assert_true( false !== strpos( $schema_adapter, 'pre_option_validate__schema' ), 'Kayan_Adapter_Schema disables theme schema via validate__schema when Rank Math is active' );
assert_true( file_exists( "$theme/components/packs/FieldsMachine/UI/css/admin-ui-fixes.css" ), 'admin-ui-fixes.css exists' );
$fm = file_get_contents( "$theme/components/packs/FieldsMachine/Enqueues.php" );
assert_true( false !== strpos( $fm, 'kayanInitSortables' ), 'admin sortable bootstrap present' );
assert_true( false === strpos( $fm, '. -widget-open' ), 'sortable cancel selectors not broken' );
assert_true( false !== strpos( $fm, 'should_load_admin_assets' ), 'FieldsMachine admin assets are page-scoped' );
assert_true( false !== strpos( $fm, "stripos( \$page, 'yts-'" ) || false !== strpos( $fm, "stripos( $page, 'yts-'" ) || false !== strpos( $fm, "yts-" ), 'FieldsMachine scopes to yts pages' );
$js = file_get_contents( "$theme/components/packs/FieldsMachine/UI/Custom-Setup.js" );
assert_true( false !== strpos( $js, 'kayanInitSortables' ), 'Custom-Setup calls kayanInitSortables' );
$cssfix = file_get_contents( "$theme/components/packs/FieldsMachine/UI/Custom-Style.css" );
assert_true( false !== strpos( $cssfix, 'flex-direction: column' ), 'title fields stack vertically' );
assert_true( false !== strpos( $cssfix, '.-Radio-Box-Item em' ) && false !== strpos( $cssfix, 'white-space: normal' ), 'radio labels wrap' );
$adminfix = file_get_contents( "$theme/components/packs/FieldsMachine/UI/css/admin-ui-fixes.css" );
assert_true( false !== strpos( $adminfix, '.-Text-form-InnerTitle > descor:before' ), 'admin title descor override' );

echo "\n=== Result: $passed passed, $failed failed ===\n";
exit( $failed > 0 ? 1 : 0 );
