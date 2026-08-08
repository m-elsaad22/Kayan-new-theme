<?php
/**
 * kayan-booking — نظام الحجز متعدد الخطوات (v1.0.0 — Phase 1)
 * ════════════════════════════════════════════════════════════════
 * Pack مستقل بنفس نمط باقي الـ Packs (بدون Composer / Namespaces).
 * يعرض معالج حجز 5 خطوات تلقائياً داخل المقالات وصفحات الخدمة،
 * حسب تصنيف المقال (category) المرتبط بكل خدمة (services__metabox).
 *
 * الاعتماديات المستخدمة من القالب الحالي (بدون تكرارها):
 * - CPT: services (kayan-cpt)
 * - Meta: linked_categories, extra_fields, service_price, service_icon... (services.php)
 * - جداول: wp_kayan_bookings / wp_kayan_booking_items / wp_kayan_booking_activity (Database/DB)
 * - خيارات: whatsapp_number / phonenumber (Theme Options الحالية)
 * ════════════════════════════════════════════════════════════════
 */

if ( ! class_exists( 'Kayan_Booking' ) ) {

	class Kayan_Booking {

		public function Setup() {
			add_action( 'wp_enqueue_scripts', array( $this, 'Assets' ) );
			add_filter( 'the_content', array( $this, 'InjectWizard' ), 20 );
		}

		# ═══════════ الإعدادات العامة (Theme Options) ═══════════

		public static function option( $key, $default = '' ) {
			$val = get_option( $key );
			return ( $val === false || $val === '' ) ? $default : $val;
		}

		public static function currency() {
			return self::option( 'kayan_booking_currency', 'AED' );
		}

		public static function tax_rate() {
			return (float) self::option( 'kayan_booking_tax_rate', 5 );
		}

		public static function working_hours() {
			return array(
				'from'  => self::option( 'kayan_booking_work_from', '09:00' ),
				'to'    => self::option( 'kayan_booking_work_to', '21:00' ),
				'days'  => (array) get_option( 'kayan_booking_work_days', array( '0', '1', '2', '3', '4', '6' ) ),
				'lead'  => (int) self::option( 'kayan_booking_lead_hours', 2 ),
				'slot'  => (int) self::option( 'kayan_booking_slot_minutes', 60 ),
			);
		}

		# ═══════════ الخدمات المرتبطة بتصنيف مقال ═══════════

		public static function get_linked_services( $category_id ) {
			$category_id = absint( $category_id );
			if ( ! $category_id ) return array();

			$posts = get_posts( array(
				'post_type'      => 'services',
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			) );

			$out = array();
			foreach ( $posts as $p ) {
				$linked = (array) get_post_meta( $p->ID, 'linked_categories', true );
				$linked = array_map( 'absint', $linked );
				if ( in_array( $category_id, $linked, true ) ) {
					$out[] = self::format_service( $p );
				}
			}
			return $out;
		}

		public static function format_service( $post ) {
			return array(
				'id'         => $post->ID,
				'title'      => get_the_title( $post ),
				'icon'       => (string) get_post_meta( $post->ID, 'service_icon', true ),
				'color'      => (string) get_post_meta( $post->ID, 'service_color', true ),
				'price'      => (string) get_post_meta( $post->ID, 'service_price', true ),
				'price_from' => (bool) get_post_meta( $post->ID, 'service_price_from', true ),
				'duration'   => (string) get_post_meta( $post->ID, 'service_duration', true ),
				'desc'       => (string) get_post_meta( $post->ID, 'service_short_desc', true ),
			);
		}

		# ═══════════ حقول الخطوة الثانية الديناميكية لكل خدمة ═══════════

		public static function get_service_fields( $service_id ) {
			$service_id = absint( $service_id );
			if ( ! $service_id ) return array();

			$fields = get_post_meta( $service_id, 'extra_fields', true );
			$fields = is_array( $fields ) ? $fields : array();

			$out = array();
			foreach ( $fields as $f ) {
				if ( empty( $f['id'] ) || empty( $f['title'] ) ) continue;

				$item = array(
					'id'       => sanitize_key( $f['id'] ),
					'title'    => sanitize_text_field( $f['title'] ),
					'disc'     => isset( $f['disc'] ) ? sanitize_text_field( $f['disc'] ) : '',
					'type'     => isset( $f['type'] ) ? sanitize_text_field( $f['type'] ) : 'Text',
					'require'  => ( isset( $f['Require'] ) && 'on' === $f['Require'] ),
					'options'  => array(),
				);

				if ( ! empty( $f['options'] ) && in_array( $item['type'], array( 'Select', 'CheckBox', 'Radio' ), true ) ) {
					$opts = explode( PHP_EOL, str_replace( "\r\n", "\n", (string) $f['options'] ) );
					foreach ( $opts as $opt ) {
						$opt = trim( $opt );
						if ( '' !== $opt ) $item['options'][] = sanitize_text_field( $opt );
					}
				}
				$out[] = $item;
			}
			return $out;
		}

		# ═══════════ الأصول (CSS/JS) ═══════════

		public function Assets() {
			if ( ! is_singular() ) return;

			$dir_path = get_template_directory() . '/components/packs/kayan-booking/assets/';
			$dir_url  = get_template_directory_uri() . '/components/packs/kayan-booking/assets/';

			$css_ver = file_exists( $dir_path . 'css/kayan-booking.css' ) ? filemtime( $dir_path . 'css/kayan-booking.css' ) : '1.0.0';
			$js_ver  = file_exists( $dir_path . 'js/kayan-booking.js' ) ? filemtime( $dir_path . 'js/kayan-booking.js' ) : '1.0.0';

			wp_enqueue_style( 'kayan-booking', $dir_url . 'css/kayan-booking.css', array(), $css_ver );
			wp_enqueue_script( 'kayan-booking', $dir_url . 'js/kayan-booking.js', array(), $js_ver, true );

			wp_localize_script( 'kayan-booking', 'KayanBookingConfig', array(
				'ajaxBase'  => trailingslashit( home_url( '/AjaxCenter' ) ),
				'currency'  => self::currency(),
				'taxRate'   => self::tax_rate(),
				'hours'     => self::working_hours(),
				'payMethods'=> class_exists( 'Kayan_Payment' ) ? Kayan_Payment::enabled_methods() : array( 'card', 'wallet', 'cash' ),
				'i18n'      => array(
					'step'            => 'الخطوة',
					'chooseService'   => 'ما نوع الخدمة التي تحتاجها؟',
					'next'            => 'التالي',
					'back'            => 'السابق',
					'submit'          => 'ادفع الآن',
					'required'        => 'هذا الحقل مطلوب',
					'selectAtLeastOne'=> 'يرجى اختيار خدمة واحدة على الأقل',
					'sending'         => 'جاري الإرسال...',
					'success'         => self::option( 'kayan_booking_success_message', 'تم استلام طلب حجزك بنجاح.' ),
					'error'           => 'حدث خطأ، يرجى المحاولة مرة أخرى',
					'customerData'    => 'بياناتك وموقع الخدمة',
					'dateTime'        => 'التاريخ والوقت المناسب',
					'review'          => 'مراجعة الطلب',
					'detectLocation'  => 'حدد موقعي',
					'subtotal'        => 'الإجمالي الفرعي',
					'tax'             => 'الضريبة',
					'total'           => 'الإجمالي',
					'payChooseMethod' => 'اختر وسيلة الدفع',
					'payCard'         => 'بطاقة ائتمان / خصم',
					'payWallet'       => 'محفظة رقمية (Apple Pay / Google Pay)',
					'payCash'         => 'الدفع عند الاستلام',
					'payCardTitle'    => 'بيانات البطاقة',
					'payCardNumber'   => 'رقم البطاقة',
					'payCardName'     => 'اسم حامل البطاقة',
					'payCardExpiry'   => 'تاريخ الانتهاء (MM/YY)',
					'payCardCvv'      => 'CVV',
					'payProcessing'   => 'جاري معالجة الدفع...',
					'payOtpTitle'     => 'أدخل رمز التحقق المرسل إلى هاتفك',
					'payConfirm'      => 'تأكيد الدفع',
					'payDemoNotice'   => 'بوابة دفع تجريبية (Demo) — لا تُجرى أي عملية سحب مالي حقيقية',
					'payBack'         => 'رجوع لاختيار وسيلة أخرى',
					'invoiceBtn'      => 'عرض / طباعة الفاتورة',
				),
			) );
		}

		# ═══════════ الحقن التلقائي داخل محتوى المقال ═══════════

		public function InjectWizard( $content ) {
			// ThemeStatic::Locate → Blade('@single') calls the_content() outside the main WP loop,
			// so in_the_loop() is false on every singular view — keep is_singular / is_main_query only.
			if ( is_admin() || ! is_singular( array( 'post', 'services' ) ) || ! is_main_query() ) {
				return $content;
			}

			$post_id = get_the_ID();
			$hide    = get_post_meta( $post_id, 'hide__kayan_booking', true );
			if ( 'on' === $hide ) return $content;

			if ( is_singular( 'services' ) ) {
				$service   = self::format_service( get_post( $post_id ) );
				$services  = array( $service );
				$boot_mode = 'service';
				$boot_id   = $post_id;
			} else {
				$cats = wp_get_post_categories( $post_id );
				if ( empty( $cats ) ) return $content;

				$services = array();
				foreach ( $cats as $cat_id ) {
					$services = array_merge( $services, self::get_linked_services( $cat_id ) );
				}
				if ( empty( $services ) ) return $content;

				# إزالة التكرار حسب معرف الخدمة
				$seen = array();
				$unique = array();
				foreach ( $services as $s ) {
					if ( isset( $seen[ $s['id'] ] ) ) continue;
					$seen[ $s['id'] ] = true;
					$unique[] = $s;
				}
				$services  = $unique;
				$boot_mode = 'category';
				$boot_id   = $cats[0];
			}

			ob_start();
			$this->RenderWizard( $services, $boot_mode, $boot_id, $post_id );
			$wizard = ob_get_clean();

			return $content . $wizard;
		}

		public function RenderWizard( $services, $boot_mode, $boot_id, $post_id ) {
			$boot = array(
				'mode'      => $boot_mode,
				'id'        => $boot_id,
				'postId'    => $post_id,
				'services'  => $services,
			);
			?>
			<div class="kayan-booking-wizard" id="kayan-booking-wizard-<?php echo (int) $post_id; ?>"
				 data-boot="<?php echo esc_attr( base64_encode( wp_json_encode( $boot ) ) ); ?>">
				<div class="kbw-loading"><span class="kbw-spinner"></span></div>
			</div>
			<?php
		}
	}

	( new Kayan_Booking() )->Setup();

	if ( is_admin() ) {
		require_once __DIR__ . '/admin.php';
	}
}
