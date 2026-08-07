<?php
/**
 * RUKN v3 CONTACT / FINAL CTA — contact__form
 * إعادة بناء كاملة لودجت نموذج الاتصال بتصميم Rukn v3 — وضعين:
 *   - CTA (الرئيسية): الشريط الكحلي المتدرج "جاهزون لخدمتك 24/7" بأزرار
 *     الواتساب/الاتصال/عرض السعر + شارات الثقة
 *   - FORM (صفحة التواصل): عمود معلومات التواصل والسوشيال + فورم كامل
 *     بيرسل لنفس أكشن AjaxCenter/contact__form القديم بنفس أسماء الحقول
 *     (user__name / user_mail / phone__number / servies__category / description)
 *     فالباك إند والإيميلات شغالين من غير أي تعديل
 */
class contact__form extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'contact__form';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('before_title','cf_cta_settings','cf_display_settings','cf_form_settings','cf_head_settings','cf_mode_settings','contact_footer_list','content','form_note','form_title','hide_call_button','hide_quote_button','hide_services_select','hide_whatsapp_button','icon','quote_button_text','quote_button_url','social_list','submit_text','success_text','title','trust_badges','widget_mode') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ إعدادات عامة ═══════════
		if( !isset( $widget_mode ) || empty( $widget_mode ) ) $widget_mode = 'cta';
		$phonenumber     = get_option('phonenumber');
		$whatsapp_number = get_option('whatsapp_number');

		# ════════════════════════════════════════════════════════
		# MODE 1 — الشريط الختامي CTA (الرئيسية)
		# ════════════════════════════════════════════════════════
		if( $widget_mode == 'cta' ){

			if( !isset( $title ) || empty( $title ) ) $title = 'جاهزون لخدمتك في أي وقت — 24/7';
			$title = str_replace('{%','<span>',$title);
			$title = str_replace('%}','</span>',$title);
			if( !isset( $content ) || empty( $content ) ) $content = 'تواصل معنا الآن واحصل على معاينة مجانية وعرض سعر شفاف.';

			if( !isset( $quote_button_text ) || empty( $quote_button_text ) ) $quote_button_text = 'طلب عرض سعر';
			if( !isset( $quote_button_url ) || empty( $quote_button_url ) )   $quote_button_url  = home_url('/contact-us/');

			if( !isset( $trust_badges ) || empty( $trust_badges ) || !is_array( $trust_badges ) ){
				$trust_badges = array(
					array( 'icon'=>'<i class="fas fa-shield-halved"></i>',    'title'=>'ضمان 10 سنوات' ),
					array( 'icon'=>'<i class="fas fa-magnifying-glass"></i>', 'title'=>'معاينة مجانية' ),
					array( 'icon'=>'<i class="fas fa-headset"></i>',          'title'=>'خدمة طوارئ' ),
				);
			}

			echo '<div class="rukn-fcta">';
				echo '<div class="wrap">';
					echo '<h2 class="rv">'.$title.'</h2>';
					if( !empty( $content ) ) echo '<p class="rv">'.$content.'</p>';

					echo '<div class="fcta-btns rv">';
						if( !empty( $whatsapp_number ) && ( !isset( $hide_whatsapp_button ) || empty( $hide_whatsapp_button ) ) ){
							echo '<a href="https://wa.me/'.$whatsapp_number.'" target="_blank" rel="noopener" class="btn btn-wa"><i class="fab fa-whatsapp"></i> تواصل عبر واتساب</a>';
						}
						if( !empty( $phonenumber ) && ( !isset( $hide_call_button ) || empty( $hide_call_button ) ) ){
							echo '<a href="tel:'.$phonenumber.'" class="btn btn-call"><i class="fas fa-phone"></i> اتصل الآن</a>';
						}
						if( !isset( $hide_quote_button ) || empty( $hide_quote_button ) ){
							echo '<a href="'.$quote_button_url.'" class="btn btn-quote"><i class="fas fa-file-invoice-dollar"></i> '.$quote_button_text.'</a>';
						}
					echo '</div>';

					echo '<div class="fcta-trust rv">';
						foreach ( $trust_badges as $badge ) {
							if( !isset( $badge['title'] ) || empty( $badge['title'] ) ) continue;
							$badge_icon = ( isset( $badge['icon'] ) && !empty( $badge['icon'] ) ) ? $badge['icon'] : '<i class="fas fa-circle-check"></i>';
							echo '<span>'.$badge_icon.' '.$badge['title'].'</span>';
						}
					echo '</div>';

				echo '</div>';
			echo '</div>';

			return;
		}

		# ════════════════════════════════════════════════════════
		# MODE 2 — فورم التواصل الكامل (صفحة اتصل بنا)
		# ════════════════════════════════════════════════════════

		# رأس القسم
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'تواصل معنا';
		if( !isset( $title ) || empty( $title ) ) $title = 'تواصل {%معنا%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'فريقنا جاهز للرد على استفساراتك على مدار الساعة.';

		# عناصر التواصل — نفس حقل الودجت القديمة
		$ContactIcons = array(
			'phonenumber'     => array( 'icon'=>'<i class="fas fa-phone"></i>',            'title'=>'رقم الهاتف',        'url_type'=>'tel' ),
			'whatsapp_number' => array( 'icon'=>'<i class="fab fa-whatsapp"></i>',         'title'=>'واتساب',            'url_type'=>'whatsapp' ),
			'company__mail'   => array( 'icon'=>'<i class="fas fa-envelope"></i>',         'title'=>'البريد الإلكتروني', 'url_type'=>'mailto' ),
			'company__adress' => array( 'icon'=>'<i class="fas fa-map-location-dot"></i>', 'title'=>'العنوان',           'url_type'=>'' ),
		);

		# السوشيال — نفس حقل الودجت القديمة
		$SocialIcon = array(
			'facebook'=>'<i class="fab fa-facebook-f"></i>',
			'twitter'=>'<i class="fab fa-twitter"></i>',
			'telegram'=>'<i class="fa-brands fa-telegram"></i>',
			'youtube'=>'<i class="fab fa-youtube"></i>',
			'linkedin'=>'<i class="fab fa-linkedin-in"></i>',
			'instagram'=>'<i class="fab fa-instagram"></i>',
			'threads'=>'<i class="fa-brands fa-threads"></i>',
		);

		# خدمات القائمة المنسدلة — من تصنيفات ووردبريس (القيمة = term_id زي ما الباك إند متوقع)
		$service_terms = get_terms( array( 'taxonomy'=>'category', 'hide_empty'=>false ) );

		if( !isset( $form_title ) || empty( $form_title ) )     $form_title     = 'أرسل لنا رسالة';
		if( !isset( $submit_text ) || empty( $submit_text ) )   $submit_text    = 'إرسال الرسالة';
		if( !isset( $form_note ) || empty( $form_note ) )       $form_note      = 'بياناتك محفوظة ولن تُستخدم إلا للتواصل معك.';
		if( !isset( $success_text ) || empty( $success_text ) ) $success_text   = 'تم إرسال رسالتك بنجاح وسيتم التواصل معك قريباً.';
		$uniqid = uniqid('ruknform');

		echo '<div class="wrap">';

			# رأس القسم
			echo '<div class="shead rv">';
				if( !empty( $before_title ) ) echo '<span class="tag">'.$before_title.'</span>';
				echo '<h2>'.$title.'</h2>';
				if( !empty( $content ) ) echo '<p>'.$content.'</p>';
			echo '</div>';

			echo '<div class="rukn-contact-grid">';

				# ═══════════ عمود معلومات التواصل ═══════════
				echo '<div class="rukn-contact-info rv">';

					if( isset( $contact_footer_list ) && !empty( $contact_footer_list ) && is_array( $contact_footer_list ) ){
						foreach ( $contact_footer_list as $contact_item ) {
							$contact_value = get_option( $contact_item );
							if( empty( $contact_value ) || !isset( $ContactIcons[ $contact_item ] ) ) continue;
							$item_setup = $ContactIcons[ $contact_item ];

							$item_url = '';
							if( $item_setup['url_type'] == 'tel' )      $item_url = 'tel:'.$contact_value;
							if( $item_setup['url_type'] == 'whatsapp' ) $item_url = 'https://wa.me/'.$contact_value;
							if( $item_setup['url_type'] == 'mailto' )   $item_url = 'mailto:'.$contact_value;

							echo ( !empty( $item_url ) ) ? '<a href="'.$item_url.'" class="cinfo-item"'.( ( $item_setup['url_type'] == 'whatsapp' ) ? ' target="_blank" rel="noopener"' : '' ).'>' : '<div class="cinfo-item">';
								echo '<span class="cinfo-ic">'.$item_setup['icon'].'</span>';
								echo '<div><b>'.$item_setup['title'].'</b><small>'.$contact_value.'</small></div>';
							echo ( !empty( $item_url ) ) ? '</a>' : '</div>';
						}
					}

					if( isset( $social_list ) && !empty( $social_list ) && is_array( $social_list ) ){
						echo '<div class="cinfo-social">';
							foreach ( $social_list as $social__item ) {
								$social_value = get_option( $social__item );
								if( !empty( $social_value ) && isset( $SocialIcon[ $social__item ] ) ){
									echo '<a class="'.$social__item.'" title="'.$social__item.'" aria-label="'.$social__item.'" target="_blank" rel="noopener" href="'.$social_value.'">'.$SocialIcon[ $social__item ].'</a>';
								}
							}
						echo '</div>';
					}

				echo '</div>';

				# ═══════════ عمود الفورم ═══════════
				echo '<div class="rukn-contact-form rv">';
					echo '<h3>'.$form_title.'</h3>';
					echo '<form id="'.$uniqid.'" data-rukn-contact-form data-success-text="'.esc_attr( $success_text ).'">';
					# ═══ مصيدة سبام: حقل مخفي لا يراه البشر — أي بوت يملؤه يُرفض بصمت ═══
					echo '<input type="text" name="yc_website_url" value="" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;top:-9999px;height:1px;width:1px;opacity:0" aria-hidden="true">';
						echo '<div class="form-grid">';

							echo '<div class="fld"><label>الاسم الكامل</label><input type="text" name="user__name" placeholder="اسمك الكامل" required></div>';
							echo '<div class="fld"><label>رقم الهاتف</label><input type="tel" name="phone__number" placeholder="05xxxxxxxx" required></div>';
							echo '<div class="fld full"><label>البريد الإلكتروني</label><input type="email" name="user_mail" placeholder="example@email.com"></div>';

							if( ( !isset( $hide_services_select ) || empty( $hide_services_select ) ) && is_array( $service_terms ) && !empty( $service_terms ) ){
								echo '<div class="fld full"><label>الخدمة المطلوبة</label>';
									echo '<div class="sel"><i class="fas fa-toolbox"></i>';
										echo '<select name="servies__category">';
											foreach ( $service_terms as $service_term ) {
												echo '<option value="'.$service_term->term_id.'">'.$service_term->name.'</option>';
											}
										echo '</select>';
									echo '</div>';
								echo '</div>';
							}

							echo '<div class="fld full"><label>تفاصيل الطلب</label><textarea name="description" placeholder="اكتب تفاصيل طلبك هنا..." required></textarea></div>';

						echo '</div>';
						echo '<button type="submit" class="btn btn-quote rukn-form-submit"><i class="fas fa-paper-plane"></i> '.$submit_text.'</button>';
						echo '<div class="form-note"><i class="fas fa-lock"></i> '.$form_note.'</div>';
						echo '<div class="rukn-form-alert" style="display:none"></div>';
					echo '</form>';
				echo '</div>';

			echo '</div>';

		echo '</div>';

		# ════════════════════════════════════════════════════════
		# INLINE JS — إرسال الفورم لنفس أكشن AjaxCenter القديم
		# ════════════════════════════════════════════════════════
		echo '<script type="text/javascript">';
			echo '(function(){';
				echo 'var form=document.getElementById("'.$uniqid.'");if(!form)return;';
				echo 'form.addEventListener("submit",function(e){';
					echo 'e.preventDefault();';
					echo 'var btn=form.querySelector(".rukn-form-submit");';
					echo 'var alertBox=form.querySelector(".rukn-form-alert");';
					echo 'btn.disabled=true;btn.style.opacity=".6";';
					echo 'var data=new URLSearchParams(new FormData(form));';
					# ═══ جلب nonce طازج لحظة الإرسال (متوافق مع كاش الصفحات) ثم الإرسال ═══
					echo 'fetch(HomeURL+"/AjaxCenter/contact__nonce/",{cache:"no-store"}).then(function(r){return r.json()}).then(function(n){';
					echo 'data.append("yc_contact_nonce",n.nonce);';
					echo 'return fetch(HomeURL+"/AjaxCenter/contact__form/",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:data.toString()});})';
					echo '.then(function(r){return r.json()})';
					echo '.then(function(){';
						echo 'alertBox.style.display="block";';
						echo 'alertBox.className="rukn-form-alert rukn-form-success";';
						echo 'alertBox.innerHTML=\'<i class="fas fa-circle-check"></i> \'+form.getAttribute("data-success-text");';
						echo 'form.reset();btn.disabled=false;btn.style.opacity="1";';
					echo '})';
					echo '.catch(function(){';
						echo 'alertBox.style.display="block";';
						echo 'alertBox.className="rukn-form-alert rukn-form-error";';
						echo 'alertBox.innerHTML=\'<i class="fas fa-circle-xmark"></i> حدث خطأ، حاول مرة أخرى أو تواصل معنا مباشرة.\';';
						echo 'btn.disabled=false;btn.style.opacity="1";';
					echo '});';
				echo '});';
			echo '})();';
		echo '</script>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — التواصل / CTA النهائي',
			'description'=>'الشريط الختامي أو فورم التواصل الكامل بتصميم Rukn v3',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'cf_mode_settings',
					'title'=>'وضع العرض',
				),
				array(
					'type'=>'Radio',
					'id'=>'widget_mode',
					'title'=>'اختار شكل الشريحة',
					'options'=>array(
						'cta' =>'CTA — الشريط الكحلي الختامي (الرئيسية)',
						'form'=>'FORM — فورم التواصل الكامل (صفحة اتصل بنا)',
					)
				),

				array(
					'type'=>'Title',
					'id'=>'cf_head_settings',
					'title'=>'العناوين',
				),
				array(
					'type'=>'Text',
					'id'=>'before_title',
					'title'=>'الشارة فوق العنوان (وضع الفورم فقط)',
				),
				array(
					'type'=>'Text',
					'id'=>'title',
					'title'=>'عنوان الشريحة',
					'disc'=> "قَم بتمييز كلمات محددة في العنوان بتدرج لوني عن طريق إضافة ' {% ' قبل الكلمة و ' %} ' بعدها",
				),
				array(
					'type'=>'Editor',
					'id' => 'content',
					'title' =>'وصف الشريحة',
				),

				array(
					'type'=>'Title',
					'id'=>'cf_cta_settings',
					'title'=>'إعدادات وضع CTA — الأرقام من إعدادات القالب',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_whatsapp_button',
					'title'=>'إخفاء زرار الواتساب',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_call_button',
					'title'=>'إخفاء زرار الاتصال',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_quote_button',
					'title'=>'إخفاء زرار عرض السعر',
				),
				array(
					'type'=>'Text',
					'id'=>'quote_button_text',
					'title'=>'عنوان زرار عرض السعر',
				),
				array(
					'type'=>'Text',
					'id'=>'quote_button_url',
					'title'=>'رابط زرار عرض السعر',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'trust_badges',
					'title'=>'شارات الثقة أسفل الأزرار',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML)',
						),
						array(
							'type'=>'Text',
							'id'=>'title',
							'title'=>'النص',
						),
					)
				),

				array(
					'type'=>'Title',
					'id'=>'cf_form_settings',
					'title'=>'إعدادات وضع FORM',
				),
				array(
					'type'=>'Text',
					'id'=>'form_title',
					'title'=>'عنوان الفورم (الافتراضي: أرسل لنا رسالة)',
				),
				array(
					'type'=>'Text',
					'id'=>'submit_text',
					'title'=>'نص زرار الإرسال',
				),
				array(
					'type'=>'Text',
					'id'=>'form_note',
					'title'=>'ملاحظة الخصوصية أسفل الزرار',
				),
				array(
					'type'=>'Text',
					'id'=>'success_text',
					'title'=>'رسالة النجاح بعد الإرسال',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_services_select',
					'title'=>'إخفاء قائمة الخدمة المطلوبة',
				),
				array(
					'title'  => 'عناصر التواصل المعروضة بجانب الفورم',
					'en_title'=> 'contact_footer_list',
					'type'  => 'CheckBox',
					'id'    => 'contact_footer_list',
					'options'=>array(
						'company__mail'=>'البريد الالكتروني للشركة',
						'whatsapp_number'=>'رقم واتساب',
						'company__adress'=>'عنوان الشركة',
						'phonenumber'=>'رقم الهاتف',
					)
				),
				array(
					'title'  => 'أيقونات السوشيال بجانب الفورم',
					'en_title'=> 'social_header_list',
					'type'  => 'CheckBox',
					'id'    => 'social_list',
					'options'=>array(
						'facebook'=>'facebook',
						'twitter'=>'twitter',
						'telegram'=>'telegram',
						'youtube'=>'youtube',
						'linkedin'=>'linkedin',
						'instagram'=>'instagram',
						'threads'=>'threads'
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'cf_display_settings',
					'title' =>'إعدادات الظهور',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'hide_section__switch',
					'title' =>'هل تريد إخفاء هذه الشريحة مؤقتاً',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'mobile_hide_section__switch',
					'title' =>'هل تريد إخفاء هذه الشريحة مؤقتاً في الموبيل',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'show_top_separator',
					'title' =>'خلفية بيضاء للشريحة (وضع الفورم)',
					'disc'=>'التبديل بين الخلفية الفاتحة والبيضاء لعمل تناوب بين الأقسام',
				)

			),
		);

	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new contact__form)->Setup();
