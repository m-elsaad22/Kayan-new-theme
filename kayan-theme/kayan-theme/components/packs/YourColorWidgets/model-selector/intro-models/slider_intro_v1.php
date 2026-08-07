<?php
/**
 * RUKN v3 HERO — slider_intro_v1
 * إعادة بناء كاملة لمقدمة الصفحة الرئيسية بتصميم Rukn Eltatawer v3
 * كل المحتوى قابل للتعديل من لوحة التحكم (FieldsMachine)
 * مع قيم افتراضية مطابقة للتصميم في حال ترك الحقول فارغة
 */
class slider_intro_v1 extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'slider_intro_v1';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('dash_live_text','dash_services','dash_stats','dash_title','decimals','hero_buttons_settings','hero_chips_settings','hero_dash_settings','hero_main_settings','hero_warranty_settings','hide_call_button','hide_dashboard','hide_proof_chips','hide_quote_button','hide_warranty','hide_whatsapp_button','icon','label','number','proof_chips','quote_button_text','quote_button_url','sub_text','suffix','title','trust_items','url','warranty_icon','warranty_sub','warranty_title') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ العنوان الرئيسي — صيغة {% %} بتتحول للكلمة المتدرجة ═══════════
		if( !isset( $title ) || empty( $title ) ) $title = esc_html( get_bloginfo('name') ).' — منصة {%الخدمات المنزلية المتكاملة%} الأولى في الإمارات';
		$title = str_replace('{%','<em>',$title);
		$title = str_replace('%}','</em>',$title);

		# ═══════════ الوصف ═══════════
		if( !isset( $sub_text ) || empty( $sub_text ) ) $sub_text = 'من عزل الأسطح وكشف التسربات إلى صيانة التكييف والتنظيف الاحترافي — فريق معتمد، أجهزة حديثة، وضمان مكتوب يصل إلى 10 سنوات.';

		# ═══════════ أرقام التواصل من إعدادات القالب ═══════════
		$phonenumber     = get_option('phonenumber');
		$whatsapp_number = get_option('whatsapp_number');

		# ═══════════ زرار عرض السعر ═══════════
		if( !isset( $quote_button_text ) || empty( $quote_button_text ) ) $quote_button_text = 'طلب عرض سعر';
		if( !isset( $quote_button_url ) || empty( $quote_button_url ) )   $quote_button_url  = home_url('/contact-us/');

		# ═══════════ شارات الثقة (Chips) — افتراضية لو الحقل فاضي ═══════════
		if( !isset( $proof_chips ) || empty( $proof_chips ) || !is_array( $proof_chips ) ){
			$proof_chips = array(
				array( 'icon'=>'<i class="fas fa-star star"></i>',        'title'=>'4.9/5 (1,247+ تقييم Google)' ),
				array( 'icon'=>'<i class="fas fa-users"></i>',            'title'=>'15,000+ عميل راضٍ' ),
				array( 'icon'=>'<i class="fas fa-award"></i>',            'title'=>'12+ سنة خبرة' ),
				array( 'icon'=>'<i class="fas fa-shield-halved"></i>',    'title'=>'ضمان 10 سنوات مكتوب' ),
				array( 'icon'=>'<i class="fas fa-headset"></i>',          'title'=>'طوارئ 24/7' ),
				array( 'icon'=>'<i class="fas fa-map-location-dot"></i>', 'title'=>'جميع الإمارات' ),
			);
		}

		# ═══════════ لوحة الخدمات (Dashboard) ═══════════
		if( !isset( $dash_title ) || empty( $dash_title ) ) $dash_title = 'لوحة خدمات '.esc_html( get_bloginfo('name') );
		if( !isset( $dash_live_text ) || empty( $dash_live_text ) ) $dash_live_text = 'مباشر';

		if( !isset( $dash_services ) || empty( $dash_services ) || !is_array( $dash_services ) ){
			$dash_services = array(
				array( 'icon'=>'<i class="fas fa-droplet"></i>',           'title'=>'كشف تسربات',   'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-layer-group"></i>',       'title'=>'عزل أسطح',     'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-snowflake"></i>',         'title'=>'صيانة تكييف',  'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-spray-can-sparkles"></i>','title'=>'تنظيف وتعقيم', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-wrench"></i>',            'title'=>'سباكة',        'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-bug-slash"></i>',         'title'=>'مكافحة حشرات', 'url'=>'' ),
			);
		}

		if( !isset( $dash_stats ) || empty( $dash_stats ) || !is_array( $dash_stats ) ){
			$dash_stats = array(
				array( 'number'=>'15000', 'suffix'=>'+', 'decimals'=>'',  'label'=>'عميل' ),
				array( 'number'=>'30000', 'suffix'=>'+', 'decimals'=>'',  'label'=>'خدمة' ),
				array( 'number'=>'4.9',   'suffix'=>'',  'decimals'=>'1', 'label'=>'تقييم' ),
			);
		}

		if( !isset( $warranty_title ) || empty( $warranty_title ) ) $warranty_title = 'ضمان مكتوب يصل إلى 10 سنوات';
		if( !isset( $warranty_sub ) || empty( $warranty_sub ) )     $warranty_sub   = 'على أعمال العزل المائي والحراري';
		if( !isset( $warranty_icon ) || empty( $warranty_icon ) )   $warranty_icon  = '<i class="fas fa-shield-halved"></i>';

		if( !isset( $trust_items ) || empty( $trust_items ) || !is_array( $trust_items ) ){
			$trust_items = array(
				array( 'title'=>'معتمد من بلدية دبي' ),
				array( 'title'=>'فنيون معتمدون' ),
			);
		}

		# ════════════════════════════════════════════════════════
		# OUTPUT — نفس بنية التصميم الجديد بالملي
		# ════════════════════════════════════════════════════════
		echo '<section class="hero" id="home">';
			echo '<div class="hero-grid-bg"></div>';
			echo '<div id="particles"></div>';
			echo '<div class="wrap">';

				# ═══════════ النص الرئيسي ═══════════
				echo '<div class="hero-copy">';
					echo '<h1>'.$title.'</h1>';
					echo '<p class="sub">'.$sub_text.'</p>';

					echo '<div class="hero-ctas">';
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

					# شارات الثقة
					if( !isset( $hide_proof_chips ) || empty( $hide_proof_chips ) ){
						echo '<div class="hero-proof">';
							foreach ( $proof_chips as $chip ) {
								if( !isset( $chip['title'] ) || empty( $chip['title'] ) ) continue;
								$chip_icon = ( isset( $chip['icon'] ) && !empty( $chip['icon'] ) ) ? $chip['icon'] : '<i class="fas fa-circle-check"></i>';
								echo '<span class="chip">'.$chip_icon.' '.$chip['title'].'</span>';
							}
						echo '</div>';
					}
				echo '</div>';

				# ═══════════ لوحة الخدمات التفاعلية ═══════════
				if( !isset( $hide_dashboard ) || empty( $hide_dashboard ) ){
					echo '<div class="dash rv-l">';

						echo '<div class="dash-top">';
							echo '<span class="ttl"><i class="fas fa-chart-line" style="color:var(--aqua)"></i> '.$dash_title.'</span>';
							echo '<span class="live"><b></b> '.$dash_live_text.'</span>';
						echo '</div>';

						echo '<div class="dash-mini">';
							foreach ( $dash_services as $srv ) {
								if( !isset( $srv['title'] ) || empty( $srv['title'] ) ) continue;
								$srv_icon = ( isset( $srv['icon'] ) && !empty( $srv['icon'] ) ) ? $srv['icon'] : '<i class="fas fa-circle-check"></i>';
								if( isset( $srv['url'] ) && !empty( $srv['url'] ) ){
									echo '<a class="mini" href="'.$srv['url'].'" title="'.$srv['title'].'">'.$srv_icon.'<span>'.$srv['title'].'</span></a>';
								}else{
									echo '<div class="mini">'.$srv_icon.'<span>'.$srv['title'].'</span></div>';
								}
							}
						echo '</div>';

						echo '<div class="dash-stats">';
							foreach ( $dash_stats as $stat ) {
								if( !isset( $stat['number'] ) || $stat['number'] === '' ) continue;
								$stat_suffix   = ( isset( $stat['suffix'] ) ) ? $stat['suffix'] : '';
								$stat_decimals = ( isset( $stat['decimals'] ) && is_numeric( $stat['decimals'] ) ) ? $stat['decimals'] : '';
								echo '<div class="dstat">';
									echo '<b data-count="'.$stat['number'].'"'.( ( $stat_suffix !== '' ) ? ' data-suffix="'.$stat_suffix.'"' : '' ).( ( $stat_decimals !== '' ) ? ' data-dec="'.$stat_decimals.'"' : '' ).'>0</b>';
									echo '<small>'.( ( isset( $stat['label'] ) ) ? $stat['label'] : '' ).'</small>';
								echo '</div>';
							}
						echo '</div>';

						if( !isset( $hide_warranty ) || empty( $hide_warranty ) ){
							echo '<div class="warranty">';
								echo $warranty_icon;
								echo '<div><b>'.$warranty_title.'</b><small>'.$warranty_sub.'</small></div>';
							echo '</div>';
						}

						echo '<div class="dash-trust">';
							foreach ( $trust_items as $trust ) {
								if( !isset( $trust['title'] ) || empty( $trust['title'] ) ) continue;
								echo '<span class="dt"><i class="fas fa-circle-check"></i> '.$trust['title'].'</span>';
							}
						echo '</div>';

					echo '</div>';
				}

			echo '</div>';
		echo '</section>';

		# ════════════════════════════════════════════════════════
		# INLINE JS — الجزيئات العائمة + العدادات المتحركة
		# ════════════════════════════════════════════════════════
		echo '<script type="text/javascript">';
			# Hero particles
			echo '(function(){';
				echo 'var box=document.getElementById("particles");if(!box||box.children.length)return;';
				echo 'for(var i=0;i<22;i++){';
					echo 'var p=document.createElement("span");p.className="particle";';
					echo 'var s=Math.random()*4+2;';
					echo 'p.style.width=p.style.height=s+"px";';
					echo 'p.style.left=Math.random()*100+"%";';
					echo 'p.style.top=Math.random()*100+"%";';
					echo 'p.style.opacity=Math.random()*.5+.2;';
					echo 'p.style.animationDelay=(Math.random()*8)+"s";';
					echo 'p.style.animationDuration=(Math.random()*8+8)+"s";';
					echo 'box.appendChild(p);';
				echo '}';
			echo '})();';
			# Animated counters
			echo 'if(typeof window.ruknCounted==="undefined"){';
				echo 'window.ruknCounted=new Set();';
				echo 'window.ruknAnimateCount=function(el){';
					echo 'if(window.ruknCounted.has(el))return;window.ruknCounted.add(el);';
					echo 'var target=parseFloat(el.dataset.count);';
					echo 'var dec=parseInt(el.dataset.dec||"0");';
					echo 'var suffix=el.dataset.suffix||"";';
					echo 'var dur=1600,start=performance.now();';
					echo 'function step(now){';
						echo 'var t=Math.min((now-start)/dur,1);';
						echo 'var eased=1-Math.pow(1-t,3);';
						echo 'var val=target*eased;';
						echo 'el.textContent=(dec?val.toFixed(dec):Math.floor(val).toLocaleString("en-US"))+suffix;';
						echo 'if(t<1)requestAnimationFrame(step);else el.textContent=(dec?target.toFixed(dec):Math.floor(target).toLocaleString("en-US"))+suffix;';
					echo '}';
					echo 'requestAnimationFrame(step);';
				echo '};';
				echo 'window.ruknCio=new IntersectionObserver(function(entries){';
					echo 'entries.forEach(function(e){if(e.isIntersecting){window.ruknAnimateCount(e.target);window.ruknCio.unobserve(e.target)}});';
				echo '},{threshold:.4});';
			echo '}';
			echo 'document.querySelectorAll("[data-count]").forEach(function(el){window.ruknCio.observe(el)});';
		echo '</script>';

	}


	public function widget__setup(){
		global $yc__widgets__selector;

		$yc__widgets__selector[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'title'=>'RUKN v3 HERO — المقدمة الرئيسية',
			'id'=>$this->widget__name,
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'hero_main_settings',
					'title'=>'المحتوى الرئيسي',
				),
				array(
					'type'=>'Text',
					'id'=>'title',
					'title'=>'العنوان الرئيسي — استخدم {% و %} حول الكلمات المتدرجة',
				),
				array(
					'type'=>'TextArea',
					'id'=>'sub_text',
					'title'=>'الوصف تحت العنوان',
				),

				array(
					'type'=>'Title',
					'id'=>'hero_buttons_settings',
					'title'=>'الأزرار — أرقام الاتصال والواتساب بتتسحب من إعدادات القالب',
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
					'type'=>'Title',
					'id'=>'hero_chips_settings',
					'title'=>'شارات الثقة (تحت الأزرار)',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_proof_chips',
					'title'=>'إخفاء شارات الثقة',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'proof_chips',
					'title'=>'شارات الثقة',
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
					'id'=>'hero_dash_settings',
					'title'=>'لوحة الخدمات التفاعلية (يمين الهيرو)',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_dashboard',
					'title'=>'إخفاء اللوحة بالكامل',
				),
				array(
					'type'=>'Text',
					'id'=>'dash_title',
					'title'=>'عنوان اللوحة',
				),
				array(
					'type'=>'Text',
					'id'=>'dash_live_text',
					'title'=>'نص شارة "مباشر"',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'dash_services',
					'title'=>'الخدمات المصغرة (6 عناصر)',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML)',
						),
						array(
							'type'=>'Text',
							'id'=>'title',
							'title'=>'اسم الخدمة',
						),
						array(
							'type'=>'Text',
							'id'=>'url',
							'title'=>'الرابط (اختياري)',
						),
					)
				),
				array(
					'type'=>'GroupsField',
					'id'=>'dash_stats',
					'title'=>'الأرقام المتحركة (3 عناصر)',
					'fields'=> array(
						array(
							'type'=>'Text',
							'id'=>'number',
							'title'=>'الرقم (مثال: 15000 أو 4.9)',
						),
						array(
							'type'=>'Text',
							'id'=>'suffix',
							'title'=>'اللاحقة (مثال: +)',
						),
						array(
							'type'=>'Text',
							'id'=>'decimals',
							'title'=>'عدد الكسور العشرية (مثال: 1 للتقييم)',
						),
						array(
							'type'=>'Text',
							'id'=>'label',
							'title'=>'التسمية (عميل / خدمة / تقييم)',
						),
					)
				),

				array(
					'type'=>'Title',
					'id'=>'hero_warranty_settings',
					'title'=>'شريط الضمان الذهبي',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_warranty',
					'title'=>'إخفاء شريط الضمان',
				),
				array(
					'type'=>'TextArea_Code',
					'id'=>'warranty_icon',
					'title'=>'أيقونة الضمان (HTML)',
				),
				array(
					'type'=>'Text',
					'id'=>'warranty_title',
					'title'=>'عنوان الضمان',
				),
				array(
					'type'=>'Text',
					'id'=>'warranty_sub',
					'title'=>'النص الفرعي للضمان',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'trust_items',
					'title'=>'عناصر الاعتماد (أسفل اللوحة)',
					'fields'=> array(
						array(
							'type'=>'Text',
							'id'=>'title',
							'title'=>'النص',
						),
					)
				),

			)
		);

	}

	public function Setup(){
		add_action('yc__widgets__selector',array($this,'widget__setup'));
	}

}
(new slider_intro_v1)->Setup();
