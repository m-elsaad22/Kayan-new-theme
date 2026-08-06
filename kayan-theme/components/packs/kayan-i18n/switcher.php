<?php 
if ( ! function_exists( 'kayan_i18n_render_switcher' ) ) {
	/**
	 * مبدّل الدولة واللغة — يُستخدم في الهيدر والصفحة الرئيسية.
	 *
	 * @param array $args {
	 *   @type string $instance_suffix لتمييز معرفات DOM عند تكرار المبدّل.
	 *   @type string $btn_class       كلاسات إضافية للزر.
	 * }
	 */
	function kayan_i18n_render_switcher( $args = array() ) {
		if ( ! kayan_i18n_is_enabled() ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'instance_suffix' => '',
				'btn_class'       => '--open--searching --search--buttonType-icon',
			)
		);

		$suffix  = sanitize_html_class( (string) $args['instance_suffix'] );
		$wrap_id = 'kayanSwitcher' . $suffix;
		$btn_id  = 'kayanSwitcherBtn' . $suffix;
		$drop_id = 'kayanDropdown' . $suffix;
		$flag_id = 'kayanFlag' . $suffix;
		$lang_id = 'kayanLang' . $suffix;

		$country = kayan_i18n_get_country();
		$lang    = kayan_i18n_get_lang();
		$data    = kayan_i18n_get_country_data( $country );
		$flag    = isset( $data['flag'] ) ? $data['flag'] : '🌐';
		$dir     = kayan_i18n_is_english() ? 'ltr' : 'rtl';

		static $script_printed = false;

		echo '<div class="kayan-switcher-wrap" id="' . esc_attr( $wrap_id ) . '" dir="' . esc_attr( $dir ) . '">';
		echo '<button class="kayan-switcher-btn ' . esc_attr( $args['btn_class'] ) . '" id="' . esc_attr( $btn_id ) . '" aria-haspopup="true" aria-expanded="false" aria-label="' . esc_attr( kayan_i18n_t( 'switcher_label' ) ) . '" title="' . esc_attr( kayan_i18n_t( 'switcher_label' ) ) . '">';
		echo '<span class="ksw-flag" id="' . esc_attr( $flag_id ) . '" aria-hidden="true">' . esc_html( $flag ) . '</span>';
		echo '<span class="ksw-lang-label" id="' . esc_attr( $lang_id ) . '">' . esc_html( strtoupper( $lang ) ) . '</span>';
		echo '</button>';
		echo '<div class="ksw-dropdown" id="' . esc_attr( $drop_id ) . '" role="menu" aria-hidden="true">';
		echo '<div class="ksw-arrow" aria-hidden="true"></div>';
		echo '<div class="ksw-section">';
		echo '<p class="ksw-section-label"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>' . esc_html( kayan_i18n_t( 'section_country' ) ) . '</p>';
		echo '<div class="ksw-countries" role="group">';
		foreach ( kayan_i18n_get_countries() as $code => $row ) {
			$active = $code === $country ? ' is-active' : '';
			echo '<button class="ksw-country-btn' . esc_attr( $active ) . '" data-country="' . esc_attr( $code ) . '" data-instance="' . esc_attr( $suffix ) . '" role="menuitem" aria-pressed="' . ( $code === $country ? 'true' : 'false' ) . '">';
			echo '<span class="ksw-btn-flag" aria-hidden="true">' . esc_html( $row['flag'] ) . '</span>';
			echo '<span>' . esc_html( kayan_i18n_country_label( $code, kayan_i18n_get_lang() ) ) . '</span>';
			echo '<svg class="ksw-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';
			echo '</button>';
		}
		echo '</div></div>';
		echo '<div class="ksw-divider" role="separator" aria-hidden="true"></div>';
		echo '<div class="ksw-section">';
		echo '<p class="ksw-section-label"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 8l6 6"/><path d="M4 14l6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="M22 22l-5-10-5 10"/><path d="M14 18h6"/></svg>' . esc_html( kayan_i18n_t( 'section_language' ) ) . '</p>';
		echo '<div class="ksw-langs" role="group">';
		echo '<button class="ksw-lang-btn' . ( 'ar' === $lang ? ' is-active' : '' ) . '" data-lang="ar" data-instance="' . esc_attr( $suffix ) . '" role="menuitem" aria-pressed="' . ( 'ar' === $lang ? 'true' : 'false' ) . '" aria-label="' . esc_attr( kayan_i18n_t( 'lang_ar' ) ) . '"><span class="ksw-lang-badge" aria-hidden="true">ع</span><span>' . esc_html( kayan_i18n_t( 'lang_ar' ) ) . '</span><svg class="ksw-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg></button>';
		echo '<button class="ksw-lang-btn' . ( 'en' === $lang ? ' is-active' : '' ) . '" data-lang="en" data-instance="' . esc_attr( $suffix ) . '" role="menuitem" aria-pressed="' . ( 'en' === $lang ? 'true' : 'false' ) . '" aria-label="' . esc_attr( kayan_i18n_t( 'lang_en' ) ) . '"><span class="ksw-lang-badge" aria-hidden="true">EN</span><span>' . esc_html( kayan_i18n_t( 'lang_en' ) ) . '</span><svg class="ksw-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg></button>';
		echo '</div></div>';
		echo '</div></div>';

		if ( ! $script_printed ) {
			$script_printed = true;
			kayan_i18n_print_switcher_script();
		}
	}
}

if ( ! function_exists( 'kayan_i18n_print_switcher_script' ) ) {
	function kayan_i18n_print_switcher_script() {
		$cfg = wp_json_encode( kayan_i18n_get_switcher_config() );
		?>
<script>
(function(){
"use strict";
var CFG=<?php echo $cfg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;
function detect(){
  var p=location.pathname;
  var country="ae";
  var paths=CFG.countryPaths||{};
  Object.keys(paths).forEach(function(code){
    var base=paths[code]||"";
    if(!base)return;
    if(p===base||p===base+"/"||p.indexOf(base+"/")===0)country=code;
  });
  var countryBase=paths[country]||"";
  var pathAfterCountry=p.slice(countryBase.length)||"/";
  var isEn=(pathAfterCountry==="/en"||pathAfterCountry==="/en/"||pathAfterCountry.indexOf("/en/")===0);
  var lang=isEn?"en":"ar";
  var slug=pathAfterCountry;
  if(isEn){slug=pathAfterCountry.replace(/^\/en\/?/,"/")||"/"; if(slug!=="/"&&slug[0]!=="/")slug="/"+slug;}
  if(slug!=="/"&&slug.slice(-1)==="/")slug=slug.slice(0,-1);
  return{country:country,lang:lang,slug:slug};
}
function buildURL(country,lang,slug){
  var base=location.protocol+"//"+CFG.baseDomain;
  var countryPath=CFG.countryPaths[country]||"";
  var s=(slug&&slug!=="/")?slug:"";
  if(lang==="en") return base+countryPath+"/en"+(s||"")+"/";
  return base+countryPath+(s||"/");
}
function navigate(country,lang){
  var s=detect();
  var url=buildURL(country,lang,s.slug);
  try{localStorage.setItem(CFG.storageKey,JSON.stringify({country:country,lang:lang,ts:Date.now()}));}catch(e){}
  fetch(url,{method:"HEAD"}).then(function(r){
    window.location.href=r.ok?url:(location.protocol+"//"+CFG.baseDomain+(CFG.countryPaths[country]||"")+"/");
  }).catch(function(){
    window.location.href=location.protocol+"//"+CFG.baseDomain+(CFG.countryPaths[country]||"")+"/";
  });
}
function ids(suffix){
  suffix=suffix||"";
  return{wrap:"kayanSwitcher"+suffix,btn:"kayanSwitcherBtn"+suffix,drop:"kayanDropdown"+suffix,flag:"kayanFlag"+suffix,lang:"kayanLang"+suffix};
}
function updateUI(country,lang,suffix){
  var I=ids(suffix);
  var f=document.getElementById(I.flag),l=document.getElementById(I.lang);
  if(f)f.textContent=(CFG.flags&&CFG.flags[country])||"🌐";
  if(l)l.textContent=lang==="en"?"EN":"AR";
  document.querySelectorAll('.ksw-country-btn[data-instance="'+suffix+'"]').forEach(function(b){
    var a=b.dataset.country===country;b.classList.toggle("is-active",a);b.setAttribute("aria-pressed",a?"true":"false");
  });
  document.querySelectorAll('.ksw-lang-btn[data-instance="'+suffix+'"]').forEach(function(b){
    var a=b.dataset.lang===lang;b.classList.toggle("is-active",a);b.setAttribute("aria-pressed",a?"true":"false");
  });
}
function open(suffix){
  var I=ids(suffix),d=document.getElementById(I.drop),b=document.getElementById(I.btn);
  if(!d||!b)return;
  var rect=b.getBoundingClientRect(),dropW=210,margin=8,left=rect.right-dropW;
  if(left<margin)left=margin;
  if(left+dropW>window.innerWidth-margin)left=window.innerWidth-dropW-margin;
  d.style.top=(rect.bottom+margin)+"px";d.style.left=left+"px";d.style.right="auto";
  d.classList.add("is-open");b.setAttribute("aria-expanded","true");d.setAttribute("aria-hidden","false");
  var first=d.querySelector(".ksw-country-btn,.ksw-lang-btn");if(first)first.focus();
}
function close(suffix){
  var I=ids(suffix),d=document.getElementById(I.drop),b=document.getElementById(I.btn);
  if(!d||!b)return;
  d.classList.remove("is-open");b.setAttribute("aria-expanded","false");d.setAttribute("aria-hidden","true");
}
function toggle(suffix){var I=ids(suffix),d=document.getElementById(I.drop);if(d)d.classList.contains("is-open")?close(suffix):open(suffix);}
function initInstance(suffix){
  var I=ids(suffix),btn=document.getElementById(I.btn),drop=document.getElementById(I.drop);
  if(!btn||!drop)return;
  var st=detect();updateUI(st.country,st.lang,suffix);
  btn.addEventListener("click",function(e){e.stopPropagation();toggle(suffix);});
}
function init(){
  document.querySelectorAll(".kayan-switcher-wrap").forEach(function(w){
    var suffix=w.id.replace("kayanSwitcher","");
    initInstance(suffix);
  });
  document.querySelectorAll(".ksw-country-btn").forEach(function(b){
    b.addEventListener("click",function(){
      var suffix=b.dataset.instance||"",s=detect(),saved={};
      try{saved=JSON.parse(localStorage.getItem(CFG.storageKey)||"{}");}catch(e){}
      navigate(b.dataset.country,saved.lang||s.lang);
    });
  });
  document.querySelectorAll(".ksw-lang-btn").forEach(function(b){
    b.addEventListener("click",function(){navigate(detect().country,b.dataset.lang);});
  });
  document.addEventListener("click",function(e){
    document.querySelectorAll(".kayan-switcher-wrap").forEach(function(w){
      if(!w.contains(e.target))close(w.id.replace("kayanSwitcher",""));
    });
  });
}
if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",init);else init();
})();
</script>
		<?php
	}
}

if ( ! function_exists( 'kayan_i18n_get_switcher_html' ) ) {
	function kayan_i18n_get_switcher_html( $args = array() ) {
		ob_start();
		kayan_i18n_render_switcher( $args );
		return ob_get_clean();
	}
}
