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
// v2.4.1's "scope FieldsMachine assets to yts-* pages only" optimization broke
// the Theme Options screen itself on a real install (some tab/page-arg
// combination did not match the check) - reverted to unconditional loading,
// same as the known-good v1.4.9 baseline. Assert it stays unconditional.
assert_true( false !== strpos( $fm, 'should_load_admin_assets' ), 'FieldsMachine keeps the (now no-op) admin-assets gate for easy re-scoping later' );
assert_true( false !== strpos( $fm, 'return true' ), 'FieldsMachine admin assets load unconditionally again (matches v1.4.9 known-good baseline)' );
$js = file_get_contents( "$theme/components/packs/FieldsMachine/UI/Custom-Setup.js" );
assert_true( false !== strpos( $js, 'kayanInitSortables' ), 'Custom-Setup calls kayanInitSortables' );
$cssfix = file_get_contents( "$theme/components/packs/FieldsMachine/UI/Custom-Style.css" );
assert_true( false !== strpos( $cssfix, 'flex-direction: column' ), 'title fields stack vertically' );
assert_true( false !== strpos( $cssfix, '.-Radio-Box-Item em' ) && false !== strpos( $cssfix, 'white-space: normal' ), 'radio labels wrap' );
$adminfix = file_get_contents( "$theme/components/packs/FieldsMachine/UI/css/admin-ui-fixes.css" );
assert_true( false !== strpos( $adminfix, '.-Text-form-InnerTitle > descor:before' ), 'admin title descor override' );

# ── Fragile "explode(get_template_directory())[1]" URL-building pattern ──
# Broke silently (undefined array index -> empty/wrong asset URLs) on any
# host where __FILE__'s path isn't a literal substring match of
# get_template_directory() (symlinks, custom WP_CONTENT_DIR, Multisite
# domain mapping). Must be gone everywhere, replaced with a
# wp_normalize_path()-based diff + a hardcoded fallback.
$explode_pattern_files = array();
$rii2 = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $theme ) );
foreach ( $rii2 as $file2 ) {
	if ( ! $file2->isFile() || 'php' !== strtolower( $file2->getExtension() ) ) {
		continue;
	}
	$src = file_get_contents( $file2->getPathname() );
	if ( false !== strpos( $src, 'explode(get_template_directory()' ) || false !== strpos( $src, "explode( get_template_directory()" ) ) {
		$explode_pattern_files[] = $file2->getPathname();
	}
}
assert_true( 0 === count( $explode_pattern_files ), 'no more fragile explode(get_template_directory())[1] URL pattern anywhere (' . implode( ', ', $explode_pattern_files ) . ')' );

foreach ( array(
	'FieldsMachine/setup.php'      => 'components/packs/FieldsMachine',
	'export-import/setup.php'      => 'components/packs/export-import',
	'YourColorWidgets/setup.php'   => 'components/packs/YourColorWidgets',
) as $rel => $fallback_suffix ) {
	$src = file_get_contents( "$theme/components/packs/$rel" );
	assert_true( false !== strpos( $src, 'wp_normalize_path' ), "$rel builds its URL via wp_normalize_path (robust against symlinks)" );
	assert_true( false !== strpos( $src, $fallback_suffix ), "$rel has a hardcoded fallback URL if the path diff fails" );
}

# ── FieldsMachine admin CSS/JS: same inline-fallback pattern as
# Kayan_Admin_Platform::enqueue_assets() — must survive the external file
# request being blocked/interfered with by hosting/security/optimizers ──
$fm2 = file_get_contents( "$theme/components/packs/FieldsMachine/Enqueues.php" );
assert_true( false !== strpos( $fm2, 'wp_add_inline_style' ), 'FieldsMachine CSS has an inline fallback (wp_add_inline_style)' );
assert_true( false !== strpos( $fm2, 'wp_add_inline_script' ), 'FieldsMachine JS (Custom-Setup.js) has an inline fallback (wp_add_inline_script)' );
foreach ( array( 'codemirror.css', 'richtext.min.css', 'bootstrap-colorpicker.css', 'Custom-Style.css', 'admin-mobile.css', 'admin-ui-fixes.css', 'fa-free-fixes.css', 'flatpickr.min.css' ) as $needle ) {
	assert_true( false !== strpos( $fm2, $needle ), "FieldsMachine still enqueues $needle" );
}

# ── CSRF fix: kayan_payment_verify_otp.php must nonce-check like its siblings ──
$otp_file = file_get_contents( "$theme/components/packs/AjaxCenter/kayan_payment_verify_otp.php" );
assert_true( false !== strpos( $otp_file, "wp_verify_nonce( sanitize_text_field( wp_unslash( \$_POST['kb_nonce'] ) ), 'kayan_booking_form' )" ), 'kayan_payment_verify_otp.php verifies kb_nonce like its sibling payment/booking endpoints' );
assert_true( false !== strpos( $otp_file, '$kb_nonce_ok' ), 'kayan_payment_verify_otp.php uses the same $kb_nonce_ok convention as siblings' );

# ── Unified admin font (YourColor / Montserrat Arabic), 100% local ──
assert_true( file_exists( "$theme/components/styles/kayan-admin-font.css" ), 'shared kayan-admin-font.css exists' );
$font_css = file_get_contents( "$theme/components/styles/kayan-admin-font.css" );
assert_true( substr_count( $font_css, '@font-face' ) === 7, 'kayan-admin-font.css declares all 7 YourColor weights' );
assert_true( false !== strpos( $font_css, "--kayan-font: 'YourColor'" ), 'kayan-admin-font.css defines the --kayan-font custom property' );
foreach ( array( 'ExtraLight', 'Light', 'Regular', 'Medium', 'Bold', 'ExtraBold', 'Black' ) as $weight ) {
	$ttf = "$theme/components/packs/FieldsMachine/UI/Font/YourColor/Montserrat-Arabic-$weight.ttf";
	assert_true( file_exists( $ttf ), "font file exists: Montserrat-Arabic-$weight.ttf" );
	assert_true( false !== strpos( $font_css, "Montserrat-Arabic-$weight.ttf" ), "kayan-admin-font.css references Montserrat-Arabic-$weight.ttf" );
}
assert_true( false === strpos( $font_css, 'fonts.googleapis.com' ), 'kayan-admin-font.css has no Google Fonts dependency' );

$admin_css = file_get_contents( "$theme/components/packs/kayan-platform/assets/admin/kayan-admin.css" );
assert_true( false !== strpos( $admin_css, 'font-family: var(--kayan-font)' ), 'KAYAN Platform shell uses the shared --kayan-font' );
$admin_platform_php = file_get_contents( "$theme/components/packs/kayan-platform/includes/admin/class-kayan-admin-platform.php" );
assert_true( false !== strpos( $admin_platform_php, 'kayan-admin-font.css' ), 'Kayan_Admin_Platform enqueues kayan-admin-font.css' );
assert_true( false !== strpos( $admin_platform_php, "array( 'kayan-admin-font' )" ), 'kayan-admin.css declares kayan-admin-font as a dependency' );

# ── RTL for KAYAN Platform screens ──
assert_true( false !== strpos( $admin_css, 'direction: rtl' ), 'kayan-admin.css sets direction: rtl on .kayan-admin-shell' );
assert_true( false === strpos( $admin_css, 'margin-left: auto' ), 'kayan-admin.css drawer no longer uses a physical margin-left (logical margin-inline-start instead)' );
assert_true( false !== strpos( $admin_css, 'margin-inline-start: auto' ), 'kayan-admin.css drawer uses the RTL-safe logical property' );

# ── KAYAN Track: zero external CDN dependencies, same local font ──
$dashboard_app = file_get_contents( "$theme/components/packs/kayan-track/admin/views/dashboard-app.php" );
foreach ( array( 'fonts.googleapis.com', 'cdnjs.cloudflare.com', 'cdn.jsdelivr.net' ) as $cdn ) {
	assert_true( false === strpos( $dashboard_app, $cdn ), "KAYAN Track dashboard-app.php has no $cdn reference left" );
}
assert_true( false !== strpos( $dashboard_app, 'kayan-admin-font.css' ), 'KAYAN Track loads the shared local admin font' );
assert_true( false !== strpos( $dashboard_app, "FontAwesome/css/all.min.css" ), 'KAYAN Track loads the local FontAwesome bundle' );
assert_true( false !== strpos( $dashboard_app, "wp_enqueue_script( 'kayan-track-chartjs'" ), 'KAYAN Track enqueues chart.js via wp_enqueue_script (not a raw <script src>)' );
assert_true( false !== strpos( $dashboard_app, "wp_print_scripts( 'kayan-track-chartjs' )" ), 'KAYAN Track prints the enqueued chart.js handle' );
assert_true( file_exists( "$theme/components/packs/kayan-track/admin/js/chart.umd.min.js" ), 'local chart.umd.min.js exists' );
assert_true( filesize( "$theme/components/packs/kayan-track/admin/js/chart.umd.min.js" ) > 100000, 'local chart.umd.min.js is a real, non-truncated build (> 100KB)' );
$track_css = file_get_contents( "$theme/components/packs/kayan-track/admin/css/track-admin.css" );
assert_true( false === strpos( $track_css, 'Tajawal' ), 'track-admin.css no longer references Tajawal' );
assert_true( false !== strpos( $track_css, 'var(--kayan-font' ), 'track-admin.css uses the shared --kayan-font variable' );

# ── Arabic translation (languages/kayan-ar.po + .mo, load_theme_textdomain) ──
assert_true( file_exists( "$theme/languages/kayan-ar.mo" ), 'languages/kayan-ar.mo exists' );
assert_true( file_exists( "$theme/languages/kayan-ar.po" ), 'languages/kayan-ar.po source exists' );
$mo_data = file_get_contents( "$theme/languages/kayan-ar.mo" );
$magic = unpack( 'Vmagic', substr( $mo_data, 0, 4 ) )['magic'];
assert_true( dechex( $magic ) === '950412de', 'kayan-ar.mo has a valid GNU MO magic number' );
$functions_php = file_get_contents( "$theme/functions.php" );
assert_true( false !== strpos( $functions_php, "load_theme_textdomain( 'kayan', get_template_directory() . '/languages' )" ), 'functions.php calls load_theme_textdomain for the kayan domain' );
assert_true( false !== strpos( $functions_php, "add_action( 'after_setup_theme'" ), 'load_theme_textdomain is hooked on after_setup_theme' );
// Parse the compiled .mo with an independent, minimal reader (not relying on
// system gettext/locale availability) and spot-check a handful of entries.
function read_mo_entries( $path ) {
	$data   = file_get_contents( $path );
	$header = unpack( 'Vmagic/Vrevision/Vcount/VoffsetOriginals/VoffsetTranslations', substr( $data, 0, 20 ) );
	$entries = array();
	for ( $i = 0; $i < $header['count']; $i++ ) {
		$o = unpack( 'Vlen/Voffset', substr( $data, $header['offsetOriginals'] + $i * 8, 8 ) );
		$t = unpack( 'Vlen/Voffset', substr( $data, $header['offsetTranslations'] + $i * 8, 8 ) );
		$entries[ substr( $data, $o['offset'], $o['len'] ) ] = substr( $data, $t['offset'], $t['len'] );
	}
	return $entries;
}
$mo_entries = read_mo_entries( "$theme/languages/kayan-ar.mo" );
assert_true( count( $mo_entries ) >= 373, 'kayan-ar.mo contains at least 373 translated strings (' . count( $mo_entries ) . ' found)' );
foreach ( array(
	'Dashboard'    => 'لوحة التحكم',
	'Settings'     => 'الإعدادات',
	'Countries'    => 'الدول',
	'Ready'        => 'جاهز',
	'Save'         => 'حفظ',
) as $en => $ar ) {
	assert_true( isset( $mo_entries[ $en ] ) && $mo_entries[ $en ] === $ar, "kayan-ar.mo translates \"$en\" to \"$ar\"" );
}

echo "\n=== Result: $passed passed, $failed failed ===\n";
exit( $failed > 0 ? 1 : 0 );
