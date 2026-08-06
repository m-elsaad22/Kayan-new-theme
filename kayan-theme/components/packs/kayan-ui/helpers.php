<?php 
if ( ! function_exists( 'kayan_ui_show_call_button' ) ) {
	/**
	 * أزرار الاتصال داخل المحتوى (بطاقة، ودجات، هيدر، بوب أب) — يتحكم بها kayan_show_call_buttons فقط.
	 */
	function kayan_ui_show_call_button() {
		return ! empty( yc_get_option( 'kayan_show_call_buttons' ) );
	}
}

if ( ! function_exists( 'kayan_ui_show_floating_call_button' ) ) {
	/**
	 * الزر العائم فقط — مستقل عن kayan_show_call_buttons.
	 *
	 * @param int  $post_id           معرّف المقال/الصفحة (0 خارج singular).
	 * @param bool $chat_mode_active  وضع نافذة واتساب يستبدل الأزرار العائمة.
	 */
	function kayan_ui_show_floating_call_button( $post_id = 0, $chat_mode_active = false ) {
		if ( $chat_mode_active ) {
			return false;
		}

		if ( ! empty( yc_get_option( 'hide__floating__call' ) ) ) {
			return false;
		}

		$post_id = (int) $post_id;
		if ( $post_id > 0 ) {
			if ( ! empty( get_post_meta( $post_id, 'hide__floating__call', true ) ) ) {
				return false;
			}

			$hide_categories = yc_get_option( 'hide__floating__call__categories' );
			if ( ! empty( $hide_categories ) && is_array( $hide_categories ) ) {
				$post_cats = wp_get_post_categories( $post_id, array( 'fields' => 'ids' ) );
				if ( ! empty( array_intersect( $post_cats, array_map( 'intval', $hide_categories ) ) ) ) {
					return false;
				}
			}
		}

		return true;
	}
}

if ( ! function_exists( 'kayan_wa_get_site_name' ) ) {
	function kayan_wa_get_site_name() {
		if ( function_exists( 'kayan_seo_get_site_name' ) ) {
			return kayan_seo_get_site_name();
		}
		$name = yc_get_option( 'sitename' );
		if ( empty( $name ) ) {
			$name = get_bloginfo( 'name' );
		}
		return trim( wp_strip_all_tags( (string) $name ) );
	}
}

if ( ! function_exists( 'kayan_wa_get_page_title' ) ) {
	function kayan_wa_get_page_title( $post_id = 0 ) {
		if ( $post_id ) {
			return trim( wp_strip_all_tags( get_the_title( $post_id ) ) );
		}
		if ( is_singular() ) {
			return trim( wp_strip_all_tags( get_the_title() ) );
		}
		if ( is_category() || is_tax() || is_tag() ) {
			$obj = get_queried_object();
			return isset( $obj->name ) ? trim( wp_strip_all_tags( $obj->name ) ) : '';
		}
		if ( is_search() ) {
			return 'نتائج البحث: ' . trim( get_search_query() );
		}
		return '';
	}
}

if ( ! function_exists( 'kayan_wa_default_message' ) ) {
	function kayan_wa_default_message( $page_title = null ) {
		if ( null === $page_title ) {
			$page_title = kayan_wa_get_page_title();
		}
		$page_title = trim( (string) $page_title );
		$site_name  = kayan_wa_get_site_name();
		if ( $page_title !== '' && $site_name !== '' ) {
			return 'مرحباً! ' . $page_title . '، ' . $site_name;
		}
		if ( $site_name !== '' ) {
			return 'مرحباً! ' . $site_name;
		}
		return 'مرحباً!';
	}
}

if ( ! function_exists( 'kayan_wa_resolve_title' ) ) {
	function kayan_wa_resolve_title( $custom = '' ) {
		$custom = trim( (string) $custom );
		if ( $custom === '' ) {
			return kayan_wa_get_site_name();
		}
		$replacements = array(
			'{site}'      => kayan_wa_get_site_name(),
			'{site_name}' => kayan_wa_get_site_name(),
		);
		return str_replace( array_keys( $replacements ), array_values( $replacements ), $custom );
	}
}

if ( ! function_exists( 'kayan_wa_resolve_message' ) ) {
	function kayan_wa_resolve_message( $custom = '', $page_title = null ) {
		$custom = trim( (string) $custom );
		if ( $custom === '' ) {
			return kayan_wa_default_message( $page_title );
		}
		if ( null === $page_title ) {
			$page_title = kayan_wa_get_page_title();
		}
		$replacements = array(
			'{title}'     => $page_title,
			'{page}'      => $page_title,
			'{site}'      => kayan_wa_get_site_name(),
			'{site_name}' => kayan_wa_get_site_name(),
		);
		return str_replace( array_keys( $replacements ), array_values( $replacements ), $custom );
	}
}

if ( ! function_exists( 'kayan_wa_sanitize_number' ) ) {
	function kayan_wa_sanitize_number( $number ) {
		return preg_replace( '/\D+/', '', (string) $number );
	}
}

if ( ! function_exists( 'kayan_wa_build_url' ) ) {
	function kayan_wa_build_url( $number, $message = null, $page_title = null ) {
		$digits = kayan_wa_sanitize_number( $number );
		if ( $digits === '' ) {
			return '';
		}
		if ( null === $message ) {
			$message = kayan_wa_default_message( $page_title );
		} else {
			$message = kayan_wa_resolve_message( $message, $page_title );
		}
		return 'https://wa.me/' . $digits . '?text=' . rawurlencode( $message );
	}
}
