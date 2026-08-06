<?php
/**
 * RUKN v3 REVIEWS — rukn_reviews (ودجت جديدة)
 * سلايدر "آراء عملائنا" — كروت تقييمات Google الموثقة
 * كل تقييم: شارة التوثيق + نجوم + نص + العميل (أفاتار حرفه الأول تلقائياً، اسم، مدينة)
 * سلايدر بأسهم تنقل + تشغيل تلقائي بيتوقف عند مرور الماوس
 */
class rukn_reviews extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'rukn_reviews';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('autoplay_seconds','before_title','content','location','name','reviews_items','rvw_display_settings','rvw_head_settings','rvw_items_settings','rvw_slider_settings','stars','text','title','verified_text') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'آراء العملاء';
		if( !isset( $title ) || empty( $title ) ) $title = 'آراء عملائنا — {%تقييم 4.9 من 5%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'تقييمات حقيقية موثقة من عملاء Google.';

		# ═══════════ إعدادات ═══════════
		if( !isset( $verified_text ) || empty( $verified_text ) ) $verified_text = 'موثّق عبر Google';
		if( !isset( $autoplay_seconds ) || empty( $autoplay_seconds ) || !is_numeric( $autoplay_seconds ) ) $autoplay_seconds = 5;

		# ═══════════ التقييمات ═══════════
		if( !isset( $reviews_items ) || empty( $reviews_items ) || !is_array( $reviews_items ) ){
			$reviews_items = array(
				array( 'name'=>'محمد الشمري',    'location'=>'دبي مارينا', 'stars'=>'5', 'text'=>'خدمة ممتازة جداً! الفريق جاء في الموعد تماماً وحدّد مكان التسرب بدقة بدون أي تكسير. احترافية عالية وأسعار عادلة.' ),
				array( 'name'=>'سارة البلوشي',   'location'=>'البرشاء',    'stars'=>'5', 'text'=>'عزلنا السطح معهم منذ 3 سنوات ولم نواجه أي مشكلة حتى الآن رغم حرارة الصيف. ضمان حقيقي وعمل متقن.' ),
				array( 'name'=>'عبدالله الكعبي', 'location'=>'جميرا',      'stars'=>'5', 'text'=>'اتصلت بهم مساءً بسبب تسرب طارئ ووصلوا خلال أقل من ساعة. سرعة استجابة مذهلة وخدمة 24 ساعة فعلاً.' ),
				array( 'name'=>'فاطمة المنصوري', 'location'=>'أبوظبي',     'stars'=>'5', 'text'=>'صيانة التكييف كانت دقيقة جداً وأصبح المنزل أبرد بكثير. فريق مؤدب ونظيف في عمله. أنصح بهم بشدة.' ),
				array( 'name'=>'خالد النعيمي',   'location'=>'الشارقة',    'stars'=>'5', 'text'=>'أفضل شركة تعاملت معها للتنظيف والتعقيم. النتيجة فاقت التوقعات والمواد آمنة على الأطفال.' ),
				array( 'name'=>'ريم الحمادي',    'location'=>'عجمان',      'stars'=>'5', 'text'=>'تعامل راقٍ من أول مكالمة. عرض السعر كان شفافاً بدون أي مفاجآت، والعمل سُلّم في الوقت المحدد.' ),
			);
		}

		$uniqid = uniqid('ruknreviews');

		# ════════════════════════════════════════════════════════
		# OUTPUT — نفس بنية التصميم الجديد
		# ════════════════════════════════════════════════════════
		echo '<div class="wrap" id="'.$uniqid.'">';

			# رأس القسم
			echo '<div class="shead rv">';
				if( !empty( $before_title ) ) echo '<span class="tag">'.$before_title.'</span>';
				echo '<h2>'.$title.'</h2>';
				if( !empty( $content ) ) echo '<p>'.$content.'</p>';
			echo '</div>';

			# السلايدر
			echo '<div class="rv-slider rv">';
				echo '<div class="rv-track" data-rv-track>';

					foreach ( $reviews_items as $review ) {
						if( !isset( $review['text'] ) || empty( $review['text'] ) ) continue;

						$stars_count = ( isset( $review['stars'] ) && is_numeric( $review['stars'] ) ) ? min( 5, max( 1, intval( $review['stars'] ) ) ) : 5;
						$stars_output = str_repeat( '★', $stars_count ).str_repeat( '☆', 5 - $stars_count );

						$client_name = ( isset( $review['name'] ) && !empty( $review['name'] ) ) ? $review['name'] : 'عميل';
						$avatar_letter = mb_substr( trim( $client_name ), 0, 1, 'UTF-8' );

						echo '<div class="rcard">';
							echo '<div class="gtop">';
								echo '<span class="gver"><i class="fab fa-google"></i> '.$verified_text.'</span>';
								echo '<span class="rstars">'.$stars_output.'</span>';
							echo '</div>';
							echo '<p class="txt">"'.$review['text'].'"</p>';
							echo '<div class="rclient">';
								echo '<span class="rav">'.$avatar_letter.'</span>';
								echo '<div>';
									echo '<b>'.$client_name.'</b>';
									if( isset( $review['location'] ) && !empty( $review['location'] ) ) echo '<small>'.$review['location'].'</small>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					}

				echo '</div>';
			echo '</div>';

			# أسهم التنقل
			echo '<div class="rv-nav">';
				echo '<button data-rv-prev aria-label="السابق"><i class="fas fa-chevron-right"></i></button>';
				echo '<button data-rv-next aria-label="التالي"><i class="fas fa-chevron-left"></i></button>';
			echo '</div>';

		echo '</div>';

		# ════════════════════════════════════════════════════════
		# INLINE JS — السلايدر بالتشغيل التلقائي
		# ════════════════════════════════════════════════════════
		echo '<script type="text/javascript">';
			echo '(function(){';
				echo 'var root=document.getElementById("'.$uniqid.'");if(!root)return;';
				echo 'var track=root.querySelector("[data-rv-track]");if(!track||!track.children.length)return;';
				echo 'var idx=0;';
				echo 'function per(){return window.innerWidth<=640?1:window.innerWidth<=1024?2:3}';
				echo 'function maxIdx(){return Math.max(0,track.children.length-per())}';
				echo 'function apply(){';
					echo 'idx=Math.min(Math.max(idx,0),maxIdx());';
					echo 'var card=track.children[0];';
					echo 'var step=card.getBoundingClientRect().width+16;';
					echo 'track.style.transform="translateX("+(idx*step)+"px)";';
				echo '}';
				echo 'function move(dir){idx+=dir;if(idx>maxIdx())idx=0;if(idx<0)idx=maxIdx();apply()}';
				echo 'root.querySelector("[data-rv-prev]").addEventListener("click",function(){move(-1)});';
				echo 'root.querySelector("[data-rv-next]").addEventListener("click",function(){move(1)});';
				echo 'window.addEventListener("resize",apply);apply();';
				echo 'var timer=setInterval(function(){move(1)},'.( intval( $autoplay_seconds ) * 1000 ).');';
				echo 'track.parentElement.addEventListener("mouseenter",function(){clearInterval(timer)});';
				echo 'track.parentElement.addEventListener("mouseleave",function(){timer=setInterval(function(){move(1)},'.( intval( $autoplay_seconds ) * 1000 ).')});';
			echo '})();';
		echo '</script>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — آراء العملاء (جديدة)',
			'description'=>'سلايدر تقييمات Google الموثقة',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'rvw_head_settings',
					'title'=>'رأس القسم',
				),
				array(
					'type'=>'Text',
					'id'=>'before_title',
					'title'=>'الشارة فوق العنوان (Tag)',
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
					'id'=>'rvw_slider_settings',
					'title'=>'إعدادات السلايدر',
				),
				array(
					'type'=>'Text',
					'id'=>'verified_text',
					'title'=>'نص شارة التوثيق (الافتراضي: موثّق عبر Google)',
				),
				array(
					'type'=>'Number',
					'id'=>'autoplay_seconds',
					'title'=>'ثواني التشغيل التلقائي (الافتراضي: 5)',
				),

				array(
					'type'=>'Title',
					'id'=>'rvw_items_settings',
					'title'=>'التقييمات',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'reviews_items',
					'title'=>'التقييمات',
					'fields'=> array(
						array(
							'type'=>'Text',
							'id'=>'name',
							'title'=>'اسم العميل',
						),
						array(
							'type'=>'Text',
							'id'=>'location',
							'title'=>'المدينة/المنطقة',
						),
						array(
							'type'=>'Text',
							'id'=>'stars',
							'title'=>'عدد النجوم (1-5)',
						),
						array(
							'type'=>'TextArea',
							'id'=>'text',
							'title'=>'نص التقييم',
						),
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'rvw_display_settings',
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
					'title' =>'خلفية بيضاء للشريحة',
					'disc'=>'القسم في التصميم الأصلي بخلفية بيضاء — فعّل السويتش ده للمطابقة',
				)

			),
		);

	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new rukn_reviews)->Setup();
