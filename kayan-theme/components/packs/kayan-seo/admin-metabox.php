<?php
/**
 * kayan-seo / admin-metabox.php
 *
 * واجهة تحرير عنوان/وصف SEO تحت المقال — تكتب مباشرة في ميتا Rank Math:
 * rank_math_title / rank_math_description
 *
 * ليست محرك SEO بديلاً؛ تخزين Rank Math يبقى المصدر الوحيد.
 * تظهر فقط بينما KAYAN SEO مفعّل (kayan_seo_disable فارغ).
 */

if ( ! function_exists( 'kayan_seo_metabox_bootstrap' ) ) {

	/**
	 * Register admin hooks for the Rank Math–backed SEO metabox.
	 */
	function kayan_seo_metabox_bootstrap() {
		if ( ! is_admin() ) {
			return;
		}
		if ( function_exists( 'kayan_seo_is_disabled' ) && kayan_seo_is_disabled() ) {
			return;
		}

		add_action( 'add_meta_boxes', 'kayan_seo_register_metabox', 20 );
		add_action( 'save_post', 'kayan_seo_save_metabox', 10, 2 );
		add_action( 'add_meta_boxes', 'kayan_seo_hide_rank_math_metabox', 99 );
		add_filter( 'rank_math/metabox/add_seo_metabox', 'kayan_seo_filter_hide_rm_metabox', 99 );
	}
	add_action( 'admin_init', 'kayan_seo_metabox_bootstrap', 5 );

	/**
	 * Post types that get the KAYAN SEO metabox.
	 *
	 * @return string[]
	 */
	function kayan_seo_metabox_post_types() {
		$types = array( 'post', 'page' );
		foreach ( array( 'works', 'price', 'faq', 'kayan_pseo' ) as $cpt ) {
			if ( post_type_exists( $cpt ) ) {
				$types[] = $cpt;
			}
		}
		/**
		 * Filter post types for the KAYAN SEO (Rank Math storage) metabox.
		 *
		 * @param string[] $types Post type slugs.
		 */
		return apply_filters( 'kayan_seo_metabox_post_types', $types );
	}

	/**
	 * Register metabox under the editor.
	 */
	function kayan_seo_register_metabox() {
		foreach ( kayan_seo_metabox_post_types() as $post_type ) {
			add_meta_box(
				'kayan_seo_rank_math',
				'KAYAN SEO — عنوان ووصف (Rank Math)',
				'kayan_seo_render_metabox',
				$post_type,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Render title / description fields bound to Rank Math meta keys.
	 *
	 * @param WP_Post $post Post object.
	 */
	function kayan_seo_render_metabox( $post ) {
		wp_nonce_field( 'kayan_seo_metabox_save', 'kayan_seo_metabox_nonce' );

		$title = get_post_meta( $post->ID, 'rank_math_title', true );
		$desc  = get_post_meta( $post->ID, 'rank_math_description', true );
		if ( ! is_string( $title ) ) {
			$title = '';
		}
		if ( ! is_string( $desc ) ) {
			$desc = '';
		}

		$title_len = function_exists( 'mb_strlen' ) ? mb_strlen( $title ) : strlen( $title );
		$desc_len  = function_exists( 'mb_strlen' ) ? mb_strlen( $desc ) : strlen( $desc );

		echo '<style id="kayan-seo-metabox-css">';
		echo '.kayan-seo-box{font-family:inherit;direction:rtl;text-align:right}';
		echo '.kayan-seo-box .ks-note{background:#f0f6fc;border-inline-start:4px solid #2271b1;padding:10px 12px;margin:0 0 14px;border-radius:4px;font-size:12px;line-height:1.6;color:#1d2327}';
		echo '.kayan-seo-box .ks-field{margin:0 0 14px}';
		echo '.kayan-seo-box label{display:block;font-weight:600;margin:0 0 6px}';
		echo '.kayan-seo-box input[type=text],.kayan-seo-box textarea{width:100%;max-width:100%;box-sizing:border-box}';
		echo '.kayan-seo-box .ks-meta{display:flex;justify-content:space-between;gap:8px;margin-top:4px;font-size:11px;color:#646970}';
		echo '.kayan-seo-box .ks-preview{margin-top:8px;padding:12px;border:1px solid #dcdcde;border-radius:6px;background:#fff}';
		echo '.kayan-seo-box .ks-preview-title{color:#1a0dab;font-size:18px;line-height:1.3;margin:0 0 4px}';
		echo '.kayan-seo-box .ks-preview-url{color:#006621;font-size:13px;margin:0 0 4px;direction:ltr;text-align:left}';
		echo '.kayan-seo-box .ks-preview-desc{color:#545454;font-size:13px;line-height:1.5;margin:0}';
		echo '</style>';

		echo '<div class="kayan-seo-box">';
		echo '<p class="ks-note">هذه الواجهة تابعة لـ <strong>Rank Math</strong> — الحفظ في <code>rank_math_title</code> و <code>rank_math_description</code>. ليست محرك SEO بديلاً.</p>';

		echo '<div class="ks-field">';
		echo '<label for="kayan_seo_title">عنوان SEO</label>';
		echo '<input type="text" id="kayan_seo_title" name="kayan_seo_title" value="' . esc_attr( $title ) . '" placeholder="' . esc_attr( get_the_title( $post ) ) . '" maxlength="200" />';
		echo '<div class="ks-meta"><span>يُفضّل 50–60 حرفاً</span><span id="kayan_seo_title_count">' . esc_html( (string) $title_len ) . '</span></div>';
		echo '</div>';

		echo '<div class="ks-field">';
		echo '<label for="kayan_seo_description">وصف SEO (Meta Description)</label>';
		echo '<textarea id="kayan_seo_description" name="kayan_seo_description" rows="3" maxlength="320" placeholder="وصف يظهر في نتائج البحث…">' . esc_textarea( $desc ) . '</textarea>';
		echo '<div class="ks-meta"><span>يُفضّل 140–160 حرفاً</span><span id="kayan_seo_desc_count">' . esc_html( (string) $desc_len ) . '</span></div>';
		echo '</div>';

		$preview_title = $title !== '' ? $title : get_the_title( $post );
		$preview_desc  = $desc !== '' ? $desc : '';
		$preview_url   = get_permalink( $post );

		echo '<div class="ks-preview" aria-hidden="true">';
		echo '<p class="ks-preview-title" id="kayan_seo_preview_title">' . esc_html( $preview_title ) . '</p>';
		echo '<p class="ks-preview-url">' . esc_html( $preview_url ? (string) $preview_url : '' ) . '</p>';
		echo '<p class="ks-preview-desc" id="kayan_seo_preview_desc">' . esc_html( $preview_desc ) . '</p>';
		echo '</div>';

		echo '</div>';

		echo '<script>(function(){';
		echo 'var t=document.getElementById("kayan_seo_title"),d=document.getElementById("kayan_seo_description");';
		echo 'var tc=document.getElementById("kayan_seo_title_count"),dc=document.getElementById("kayan_seo_desc_count");';
		echo 'var pt=document.getElementById("kayan_seo_preview_title"),pd=document.getElementById("kayan_seo_preview_desc");';
		echo 'var fallback=' . wp_json_encode( get_the_title( $post ) ) . ';';
		echo 'function len(s){return (s||"").length;}';
		echo 'function sync(){if(t&&tc){tc.textContent=len(t.value);if(pt)pt.textContent=t.value||fallback;}if(d&&dc){dc.textContent=len(d.value);if(pd)pd.textContent=d.value||"";}}';
		echo 'if(t)t.addEventListener("input",sync);if(d)d.addEventListener("input",sync);';
		echo '})();</script>';
	}

	/**
	 * Persist metabox fields into Rank Math post meta.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	function kayan_seo_save_metabox( $post_id, $post ) {
		if ( ! isset( $_POST['kayan_seo_metabox_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kayan_seo_metabox_nonce'] ) ), 'kayan_seo_metabox_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( function_exists( 'kayan_seo_is_disabled' ) && kayan_seo_is_disabled() ) {
			return;
		}

		$types = kayan_seo_metabox_post_types();
		if ( $post instanceof WP_Post && ! in_array( $post->post_type, $types, true ) ) {
			return;
		}

		if ( isset( $_POST['kayan_seo_title'] ) ) {
			$title = sanitize_text_field( wp_unslash( $_POST['kayan_seo_title'] ) );
			if ( '' === $title ) {
				delete_post_meta( $post_id, 'rank_math_title' );
			} else {
				update_post_meta( $post_id, 'rank_math_title', $title );
			}
		}

		if ( isset( $_POST['kayan_seo_description'] ) ) {
			$desc = sanitize_textarea_field( wp_unslash( $_POST['kayan_seo_description'] ) );
			if ( '' === $desc ) {
				delete_post_meta( $post_id, 'rank_math_description' );
			} else {
				update_post_meta( $post_id, 'rank_math_description', $desc );
			}
		}
	}

	/**
	 * Hide Rank Math’s own metabox while KAYAN SEO UI is active (same data, one editor).
	 */
	function kayan_seo_hide_rank_math_metabox() {
		foreach ( kayan_seo_metabox_post_types() as $post_type ) {
			remove_meta_box( 'rank_math_metabox', $post_type, 'normal' );
			remove_meta_box( 'rank_math_metabox', $post_type, 'side' );
			remove_meta_box( 'rank_math_metabox', $post_type, 'advanced' );
		}
	}

	/**
	 * Rank Math filter: do not add its SEO metabox while KAYAN UI owns editing.
	 *
	 * @param bool $add Whether to add.
	 * @return bool
	 */
	function kayan_seo_filter_hide_rm_metabox( $add ) {
		if ( function_exists( 'kayan_seo_is_disabled' ) && kayan_seo_is_disabled() ) {
			return $add;
		}
		return false;
	}
}
