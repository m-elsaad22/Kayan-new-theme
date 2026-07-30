<?php
/**
 * منتقي أيقونات Font Awesome 6 Free (بدل قائمة SvgCenter شبه الفارغة)
 * يخزّن قيمة مثل: fa-solid fa-broom
 */
if ( ! isset( $value ) ) {
	$value = '';
}
if ( ! isset( $vars ) || ! is_array( $vars ) ) {
	$vars = array();
}

if ( ! function_exists( 'kayan_fa_icon_catalog' ) ) {
	require_once get_template_directory() . '/components/packs/SvgCenter/icon-helpers.php';
}

$Uni = uniqid( 'faico_', false );
if ( isset( $InsertElements ) ) {
	unset( $vars['InsertElements'] );
	$InputName = 'Insert_' . $id;
} elseif ( isset( $parent_id ) ) {
	$InputName = $parent_id . '[' . $id . ']';
} else {
	$InputName = $id;
}

$catalog = kayan_fa_icon_catalog();
# دعم القيم القديمة (SVG slug / HTML / class)
$current_label = 'بدون تحديد';
$current_preview = '<i class="fa-solid fa-icons" aria-hidden="true"></i>';
if ( '' !== $value ) {
	if ( isset( $catalog[ $value ] ) ) {
		$current_label = $catalog[ $value ];
		$current_preview = '<i class="' . esc_attr( $value ) . '" aria-hidden="true"></i>';
	} elseif ( false !== strpos( $value, '<' ) ) {
		$current_label = 'أيقونة مخصصة';
		$current_preview = kayan_icon_html( $value );
	} elseif ( file_exists( get_template_directory() . '/components/packs/SvgCenter/icons/' . sanitize_file_name( $value ) . '.php' ) ) {
		$current_label = 'SVG: ' . $value;
		$current_preview = kayan_icon_html( $value );
	} else {
		$current_label = $value;
		$current_preview = kayan_icon_html( $value );
	}
}

$vars['options'] = array( '' => 'بدون تحديد' ) + $catalog;
$vars['vars']    = base64_encode( wp_json_encode( $vars ) );

echo '<div class="-Text-inputs-area kayan-fa-icon-picker" ' . ( isset( $parent_id ) ? 'data-field-argums="' . esc_attr( base64_encode( wp_json_encode( $vars ) ) ) . '" ' : 'data-vars="' . esc_attr( base64_encode( wp_json_encode( $vars ) ) ) . '"' ) . '>';
	echo '<div class="-Text-forms-field-title"><h3>' . esc_html( $title ) . '</h3></div>';
	echo '<div class="Select-Options-Items kayan-fa-select">';
		echo isset( $AjaxHTML_Cut ) ? '<AjaxHTML_Cut>' : '';
			echo '<input type="text" name="' . esc_attr( $InputName ) . '" id="' . esc_attr( $InputName ) . '" value="' . esc_attr( $value ) . '" style="display:none" class="Selected-Value">';
			echo '<h2 data-select-open="' . esc_attr( $InputName ) . '">';
				echo '<span class="kayan-fa-preview">' . $current_preview . '</span>';
				echo '<span class="kayan-fa-label">' . esc_html( $current_label ) . '</span>';
				echo '<i class="fa-solid fa-angle-down" aria-hidden="true"></i>';
			echo '</h2>';
			echo '<div class="-Select-DropDown kayan-fa-dropdown">';
				echo '<div class="kayan-fa-search-wrap"><input type="search" class="kayan-fa-search" placeholder="ابحث عن أيقونة..." autocomplete="off"></div>';
				echo '<ul class="Lists-Select-Items kayan-fa-list">';
					echo '<li data-title="بدون تحديد" data-selected="" ' . ( '' === $value ? 'class="active"' : '' ) . '><i class="fa-solid fa-ban" aria-hidden="true"></i> بدون تحديد</li>';
					foreach ( $catalog as $class => $label ) {
						$active = ( $class === $value ) ? ' class="active"' : '';
						echo '<li data-title="' . esc_attr( $label . ' ' . $class ) . '" data-selected="' . esc_attr( $class ) . '"' . $active . '>';
							echo '<i class="' . esc_attr( $class ) . '" aria-hidden="true"></i>';
							echo '<span>' . esc_html( $label ) . '</span>';
						echo '</li>';
					}
				echo '</ul>';
			echo '</div>';
		echo isset( $AjaxHTML_Cut ) ? '</AjaxHTML_Cut>' : '';
	echo '</div>';
	if ( isset( $disc ) ) {
		echo '<descor>' . esc_html( $disc ) . '</descor>';
	}
echo '</div>';
?>
<style>
.kayan-fa-icon-picker .kayan-fa-select > h2 {
	display: flex; align-items: center; gap: 10px; cursor: pointer;
}
.kayan-fa-icon-picker .kayan-fa-preview {
	width: 34px; height: 34px; border-radius: 8px; background: #f3f4f6;
	display: inline-flex; align-items: center; justify-content: center; font-size: 16px; color: #2563eb;
}
.kayan-fa-icon-picker .kayan-fa-dropdown { max-height: 320px; overflow: auto; }
.kayan-fa-icon-picker .kayan-fa-search-wrap { position: sticky; top: 0; background: #fff; padding: 8px; z-index: 2; }
.kayan-fa-icon-picker .kayan-fa-search { width: 100%; padding: 8px 10px; border: 1px solid #e5e7eb; border-radius: 8px; }
.kayan-fa-icon-picker .kayan-fa-list li {
	display: flex; align-items: center; gap: 10px; padding: 8px 12px; cursor: pointer;
}
.kayan-fa-icon-picker .kayan-fa-list li i { width: 22px; text-align: center; color: #2563eb; }
.kayan-fa-icon-picker .kayan-fa-list li.is-hidden { display: none !important; }
</style>
<script>
(function(){
	var root = document.currentScript && document.currentScript.previousElementSibling;
	/* fallback: bind on all pickers once */
	function bindPickers(){
		document.querySelectorAll('.kayan-fa-icon-picker').forEach(function(box){
			if (box.dataset.faBound) return;
			box.dataset.faBound = '1';
			var search = box.querySelector('.kayan-fa-search');
			var list = box.querySelector('.kayan-fa-list');
			var input = box.querySelector('.Selected-Value');
			var preview = box.querySelector('.kayan-fa-preview');
			var label = box.querySelector('.kayan-fa-label');
			if (search) {
				search.addEventListener('input', function(){
					var q = (search.value || '').toLowerCase().trim();
					list.querySelectorAll('li').forEach(function(li){
						var t = (li.getAttribute('data-title') || '').toLowerCase();
						li.classList.toggle('is-hidden', q && t.indexOf(q) === -1);
					});
				});
			}
			if (list) {
				list.addEventListener('click', function(e){
					var li = e.target.closest('li[data-selected]');
					if (!li) return;
					var val = li.getAttribute('data-selected') || '';
					var title = li.getAttribute('data-title') || 'بدون تحديد';
					input.value = val;
					list.querySelectorAll('li').forEach(function(x){ x.classList.remove('active'); });
					li.classList.add('active');
					if (preview) {
						preview.innerHTML = val ? '<i class="'+val+'" aria-hidden="true"></i>' : '<i class="fa-solid fa-icons" aria-hidden="true"></i>';
					}
					if (label) {
						label.textContent = val ? (li.querySelector('span') ? li.querySelector('span').textContent : title) : 'بدون تحديد';
					}
				});
			}
		});
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', bindPickers);
	} else {
		bindPickers();
	}
	document.addEventListener('click', function(){ setTimeout(bindPickers, 50); });
})();
</script>
