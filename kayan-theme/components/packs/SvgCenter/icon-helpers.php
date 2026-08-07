<?php
/**
 * kayan_icon_html — توحيد عرض الأيقونات (FA Free + SvgCenter + HTML)
 * يقبل: HTML جاهز، class FA، اسم أيقونة، أو slug من SvgCenter.
 */
if ( ! function_exists( 'kayan_icon_html' ) ) {

	function kayan_fa_icon_catalog() {
		static $catalog = null;
		if ( null !== $catalog ) {
			return $catalog;
		}
		# قائمة أيقونات Font Awesome 6 Free مناسبة للخدمات المنزلية ولوحة التحكم
		$catalog = array(
			'fa-solid fa-screwdriver-wrench' => 'أدوات / صيانة',
			'fa-solid fa-broom'              => 'تنظيف',
			'fa-solid fa-spray-can-sparkles' => 'تعقيم',
			'fa-solid fa-soap'               => 'غسيل',
			'fa-solid fa-water'              => 'مياه / سباكة',
			'fa-solid fa-faucet'             => 'حنفيات',
			'fa-solid fa-toilet'             => 'حمامات',
			'fa-solid fa-snowflake'          => 'تكييف / تبريد',
			'fa-solid fa-temperature-high'   => 'حرارة',
			'fa-solid fa-bolt'               => 'كهرباء',
			'fa-solid fa-plug'               => 'توصيلات',
			'fa-solid fa-lightbulb'          => 'إضاءة',
			'fa-solid fa-bug-slash'          => 'مكافحة حشرات',
			'fa-solid fa-shield-halved'      => 'حماية',
			'fa-solid fa-house'              => 'منزل',
			'fa-solid fa-house-chimney'      => 'منزل عائلي',
			'fa-solid fa-building'           => 'مبنى',
			'fa-solid fa-warehouse'          => 'مستودع',
			'fa-solid fa-city'               => 'مدينة',
			'fa-solid fa-map-location-dot'   => 'موقع',
			'fa-solid fa-location-dot'       => 'عنوان',
			'fa-solid fa-truck'              => 'نقل',
			'fa-solid fa-box'                => 'تغليف',
			'fa-solid fa-couch'              => 'أثاث',
			'fa-solid fa-paint-roller'       => 'دهانات',
			'fa-solid fa-brush'              => 'فرشاة',
			'fa-solid fa-hammer'             => 'مطرقة',
			'fa-solid fa-wrench'             => 'مفتاح',
			'fa-solid fa-gears'              => 'تروس',
			'fa-solid fa-fan'                => 'مروحة',
			'fa-solid fa-wind'               => 'تهوية',
			'fa-solid fa-droplet'            => 'قطرة',
			'fa-solid fa-fire-extinguisher'  => 'إطفاء',
			'fa-solid fa-kit-medical'        => 'طبي',
			'fa-solid fa-heart-pulse'        => 'صحة',
			'fa-solid fa-user-shield'        => 'حماية شخصية',
			'fa-solid fa-users'              => 'فريق',
			'fa-solid fa-user-tie'           => 'موظف',
			'fa-solid fa-headset'            => 'دعم',
			'fa-solid fa-phone'              => 'هاتف',
			'fa-brands fa-whatsapp'          => 'واتساب',
			'fa-solid fa-envelope'           => 'بريد',
			'fa-solid fa-calendar-check'     => 'موعد',
			'fa-solid fa-clock'              => 'وقت',
			'fa-solid fa-star'               => 'تقييم',
			'fa-solid fa-award'              => 'جائزة',
			'fa-solid fa-certificate'        => 'شهادة',
			'fa-solid fa-gift'               => 'عرض',
			'fa-solid fa-tags'               => 'أسعار',
			'fa-solid fa-hand-holding-dollar'=> 'دفع',
			'fa-solid fa-credit-card'        => 'بطاقة',
			'fa-solid fa-wallet'             => 'محفظة',
			'fa-solid fa-file-invoice'       => 'فاتورة',
			'fa-solid fa-clipboard-check'    => 'تأكيد',
			'fa-solid fa-circle-check'       => 'نجاح',
			'fa-solid fa-circle-info'        => 'معلومة',
			'fa-solid fa-triangle-exclamation'=> 'تنبيه',
			'fa-solid fa-ban'                => 'منع',
			'fa-solid fa-plus'               => 'إضافة',
			'fa-solid fa-xmark'              => 'إغلاق',
			'fa-solid fa-check'              => 'صح',
			'fa-solid fa-arrow-left'         => 'سهم يسار',
			'fa-solid fa-arrow-right'        => 'سهم يمين',
			'fa-solid fa-chevron-down'       => 'سهم لأسفل',
			'fa-solid fa-magnifying-glass'   => 'بحث',
			'fa-solid fa-layer-group'        => 'طبقات',
			'fa-solid fa-cube'               => 'مكعب',
			'fa-solid fa-boxes-stacked'      => 'صناديق',
			'fa-solid fa-recycle'            => 'إعادة تدوير',
			'fa-solid fa-leaf'               => 'بيئي',
			'fa-solid fa-seedling'           => 'زراعة',
			'fa-solid fa-tree'               => 'شجرة',
			'fa-solid fa-car'                => 'سيارة',
			'fa-solid fa-motorcycle'         => 'دراجة',
			'fa-solid fa-key'                => 'مفتاح باب',
			'fa-solid fa-lock'               => 'قفل',
			'fa-solid fa-camera'             => 'كاميرا',
			'fa-solid fa-image'              => 'صورة',
			'fa-solid fa-video'              => 'فيديو',
			'fa-solid fa-wifi'               => 'واي فاي',
			'fa-solid fa-satellite-dish'     => 'قمر صناعي',
			'fa-solid fa-tv'                 => 'تلفزيون',
			'fa-solid fa-laptop'             => 'لابتوب',
			'fa-solid fa-microchip'          => 'إلكترونيات',
			'fa-solid fa-helmet-safety'      => 'سلامة',
			'fa-solid fa-person-digging'     => 'أعمال',
			'fa-solid fa-ruler-combined'     => 'قياس',
			'fa-solid fa-tape'               => 'شريط',
			'fa-solid fa-stapler'            => 'دباسة',
			'fa-solid fa-briefcase'          => 'حقيبة',
			'fa-solid fa-store'              => 'متجر',
			'fa-solid fa-shop'               => 'محل',
			'fa-solid fa-basket-shopping'    => 'تسوق',
			'fa-solid fa-cart-shopping'      => 'عربة',
			'fa-solid fa-mug-hot'            => 'مشروبات',
			'fa-solid fa-utensils'           => 'مطابخ',
			'fa-solid fa-kitchen-set'        => 'أدوات مطبخ',
			'fa-solid fa-bath'               => 'استحمام',
			'fa-solid fa-shower'             => 'دش',
			'fa-solid fa-bed'                => 'غرف نوم',
			'fa-solid fa-baby'               => 'أطفال',
			'fa-solid fa-paw'                => 'حيوانات',
			'fa-solid fa-dog'                => 'كلاب',
			'fa-solid fa-cat'                => 'قطط',
			'fa-solid fa-mosquito'           => 'حشرات',
			'fa-solid fa-spider'             => 'عناكب',
			'fa-solid fa-worm'               => 'آفات',
			'fa-solid fa-dumpster'           => 'نفايات',
			'fa-solid fa-trash-can'          => 'سلة مهملات',
			'fa-solid fa-glasses'            => 'نظارات',
			'fa-solid fa-shirt'              => 'ملابس',
			'fa-solid fa-socks'              => 'غسيل ملابس',
			'fa-solid fa-wind'               => 'هواء',
			'fa-solid fa-sun'                => 'شمس',
			'fa-solid fa-moon'               => 'ليل',
			'fa-solid fa-cloud-sun-rain'     => 'طقس',
			'fa-solid fa-umbrella'           => 'مظلة',
			'fa-solid fa-face-smile'         => 'رضا',
			'fa-solid fa-thumbs-up'          => 'إعجاب',
			'fa-solid fa-handshake'          => 'شراكة',
			'fa-solid fa-file-signature'     => 'عقد',
			'fa-solid fa-file-contract'      => 'اتفاقية',
			'fa-solid fa-building-columns'   => 'مؤسسة',
			'fa-solid fa-landmark'           => 'معلم',
			'fa-solid fa-route'              => 'مسار',
			'fa-solid fa-compass'            => 'بوصلة',
			'fa-solid fa-sliders'            => 'إعدادات',
			'fa-solid fa-gear'               => 'إعداد',
			'fa-solid fa-pen-to-square'      => 'تعديل',
			'fa-solid fa-eye'                => 'عرض',
			'fa-solid fa-download'           => 'تحميل',
			'fa-solid fa-upload'             => 'رفع',
			'fa-solid fa-share-nodes'        => 'مشاركة',
			'fa-solid fa-link'               => 'رابط',
			'fa-solid fa-globe'              => 'ويب',
			'fa-brands fa-facebook-f'        => 'فيسبوك',
			'fa-brands fa-instagram'         => 'إنستغرام',
			'fa-brands fa-twitter'           => 'تويتر',
			'fa-brands fa-youtube'           => 'يوتيوب',
			'fa-brands fa-tiktok'            => 'تيك توك',
			'fa-brands fa-telegram'          => 'تيليجرام',
			'fa-brands fa-linkedin-in'       => 'لينكدإن',
		);
		return $catalog;
	}

	/**
	 * حوّل أي قيمة مخزّنة إلى HTML أيقونة آمن للعرض.
	 */
	function kayan_icon_html( $value, $fallback = 'fa-solid fa-screwdriver-wrench' ) {
		$value = is_string( $value ) ? trim( $value ) : '';
		if ( '' === $value ) {
			$value = $fallback;
		}

		# HTML جاهز (<i> أو <svg> أو SvgCenter سابق)
		if ( false !== strpos( $value, '<' ) ) {
			# سماح محدود لوسم الأيقونات فقط
			$allowed = array(
				'i'    => array( 'class' => true, 'aria-hidden' => true, 'style' => true ),
				'span' => array( 'class' => true, 'style' => true ),
				'svg'  => array(
					'xmlns' => true, 'viewbox' => true, 'viewBox' => true, 'width' => true, 'height' => true,
					'fill' => true, 'class' => true, 'aria-hidden' => true, 'role' => true, 'style' => true,
				),
				'path' => array( 'd' => true, 'fill' => true, 'stroke' => true, 'class' => true ),
				'g'    => array( 'fill' => true, 'class' => true, 'transform' => true ),
			);
			return wp_kses( $value, $allowed );
		}

		# slug SvgCenter قديم (مثل intro_v1)
		$svg_path = get_template_directory() . '/components/packs/SvgCenter/icons/' . sanitize_file_name( $value ) . '.php';
		if ( file_exists( $svg_path ) && function_exists( 'SVGIcon' ) ) {
			$svg = SVGIcon( $value );
			if ( is_string( $svg ) && '' !== trim( $svg ) ) {
				return $svg;
			}
		}

		# أسماء شائعة خاطئة / Pro → بدائل Free
		$aliases = array(
			'fa-light'              => 'fa-solid',
			'fal'                   => 'fa-solid',
			'fa-thin'               => 'fa-solid',
			'fat'                   => 'fa-solid',
			'fa-duotone'            => 'fa-solid',
			'fad'                   => 'fa-solid',
			'far fa-arrow-right'    => 'fa-solid fa-arrow-right',
			'far fa-arrow-left'     => 'fa-solid fa-arrow-left',
			'fa-regular fa-arrow-right' => 'fa-solid fa-arrow-right',
			'fa-regular fa-arrow-left'  => 'fa-solid fa-arrow-left',
		);
		foreach ( $aliases as $from => $to ) {
			if ( 0 === strpos( $value, $from ) || $value === $from ) {
				$value = str_replace( $from, $to, $value );
			}
		}

		# class FA كامل: "fa-solid fa-broom"
		if ( preg_match( '/^(fa[srlb]?|fa-solid|fa-regular|fa-brands)(\s+fa-[a-z0-9-]+)+$/i', $value ) ) {
			$value = preg_replace( '/\bfas\b/i', 'fa-solid', $value );
			$value = preg_replace( '/\bfar\b/i', 'fa-regular', $value );
			$value = preg_replace( '/\bfab\b/i', 'fa-brands', $value );
			$value = preg_replace( '/\bfal\b/i', 'fa-solid', $value );
			return '<i class="' . esc_attr( $value ) . '" aria-hidden="true"></i>';
		}

		# اسم أيقونة فقط: "broom" أو "fa-broom"
		$name = preg_replace( '/^fa-/', '', sanitize_title( $value ) );
		if ( '' !== $name ) {
			return '<i class="fa-solid fa-' . esc_attr( $name ) . '" aria-hidden="true"></i>';
		}

		return '<i class="' . esc_attr( $fallback ) . '" aria-hidden="true"></i>';
	}
}
