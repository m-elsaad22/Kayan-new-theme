<?php
/**
 * kayan-price-pay — حجز تفاعلي + تحويل لبوابة الدفع الخارجية (v1.4.7)
 * يعمل مع shortcode [post_prices] وودجت الأسعار وكروت #PriceBoxes
 */

if ( ! class_exists( 'Kayan_Price_Pay' ) ) {

	class Kayan_Price_Pay {

		const PAY_BASE = 'https://rukn-eltatawer-pay.tanceq.com/';

		public function Setup() {
			add_action( 'wp_enqueue_scripts', array( $this, 'Assets' ) );
		}

		public static function extract_amount( $raw ) {
			$raw = (string) $raw;
			if ( preg_match( '/(\d+(?:[.,]\d+)?)/u', $raw, $m ) ) {
				return str_replace( ',', '', $m[1] );
			}
			return preg_replace( '/[^\d.]/', '', $raw );
		}

		public static function service_title() {
			if ( is_singular() ) {
				return get_the_title();
			}
			return get_bloginfo( 'name' );
		}

		public function Assets() {
			if ( is_admin() ) {
				return;
			}

			$dir_path = get_template_directory() . '/components/packs/kayan-price-pay/assets/';
			$dir_uri  = get_template_directory_uri() . '/components/packs/kayan-price-pay/assets/';

			$css = $dir_path . 'css/kayan-price-pay.css';
			$js  = $dir_path . 'js/kayan-price-pay.js';

			wp_enqueue_style(
				'kayan-price-pay',
				$dir_uri . 'css/kayan-price-pay.css',
				array(),
				file_exists( $css ) ? (string) filemtime( $css ) : '1.4.7'
			);
			wp_enqueue_script(
				'kayan-price-pay',
				$dir_uri . 'js/kayan-price-pay.js',
				array(),
				file_exists( $js ) ? (string) filemtime( $js ) : '1.4.7',
				true
			);

			wp_localize_script(
				'kayan-price-pay',
				'KayanPricePay',
				array(
					'payBase'     => self::PAY_BASE,
					'service'     => self::service_title(),
					'currency'    => (string) get_option( 'currency', 'AED' ),
					'scrollRatio' => 0,
					'i18n'        => array(
						'selectPackage' => 'اختر باقة أولاً',
						'required'      => 'يرجى تعبئة الحقول المطلوبة',
						'payNow'        => 'ادفع الآن',
						'selected'      => 'الباقة المختارة',
						'amount'        => 'المبلغ',
					),
				)
			);
		}

		/**
		 * نموذج الحجز المدمج (HTML مشترك)
		 */
		public static function render_form( $args = array() ) {
			$service = isset( $args['service'] ) ? $args['service'] : self::service_title();
			$uid     = isset( $args['uid'] ) ? $args['uid'] : uniqid( 'kpp_' );
			?>
			<form class="kpp-form" id="<?php echo esc_attr( $uid ); ?>-form" novalidate>
				<input type="hidden" name="service" value="<?php echo esc_attr( $service ); ?>" />
				<input type="hidden" name="package" value="" class="kpp-field-package" />
				<input type="hidden" name="amount" value="" class="kpp-field-amount" />

				<div class="kpp-form-grid">
					<label class="kpp-field">
						<span>الاسم <em>*</em></span>
						<input type="text" name="name" required autocomplete="name" placeholder="الاسم الكامل" />
					</label>
					<label class="kpp-field">
						<span>الجوال <em>*</em></span>
						<input type="tel" name="phone" required autocomplete="tel" placeholder="05xxxxxxxx" inputmode="tel" />
					</label>
					<label class="kpp-field kpp-field-full">
						<span>العنوان <em>*</em></span>
						<input type="text" name="address" required autocomplete="street-address" placeholder="المدينة / المنطقة / الشارع" />
					</label>
					<label class="kpp-field">
						<span>التاريخ <em>*</em></span>
						<input type="date" name="date" required />
					</label>
					<label class="kpp-field">
						<span>الوقت <em>*</em></span>
						<input type="time" name="time" required />
					</label>
					<label class="kpp-field kpp-field-full">
						<span>ملاحظات</span>
						<textarea name="notes" rows="3" placeholder="تفاصيل إضافية (اختياري)"></textarea>
					</label>
				</div>

				<div class="kpp-summary" aria-live="polite">
					<div class="kpp-summary-row">
						<span><?php echo esc_html( 'الباقة المختارة' ); ?></span>
						<strong class="kpp-summary-package">—</strong>
					</div>
					<div class="kpp-summary-row">
						<span><?php echo esc_html( 'المبلغ' ); ?></span>
						<strong class="kpp-summary-amount">—</strong>
					</div>
				</div>

				<button type="submit" class="kpp-pay-btn">
					<i class="fas fa-lock" aria-hidden="true"></i>
					<span>ادفع الآن</span>
				</button>
				<p class="kpp-form-hint">بعد الضغط سيتم تحويلك لصفحة الدفع الآمنة لإتمام الطلب.</p>
			</form>
			<?php
		}
	}

	( new Kayan_Price_Pay() )->Setup();
}
