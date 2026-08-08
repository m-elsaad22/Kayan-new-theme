<?php # YOURCOLOR CUSTOM FIELDS MACHINE.
class YC__CFM_Enqueues {
	function __construct($arguments=array()) {
		$this->YC__CFM = new YC__CFM;
		// 
		$this->UI__URL = $this->YC__CFM->YC__CFM_URL.'/UI/';
		$this->JS__URL = $this->YC__CFM->YC__CFM_URL.'UI/js/';
		$this->Style__URL = $this->YC__CFM->YC__CFM_URL.'UI/css/';

		// مسارات الملفات (وليس الروابط) — تُستخدم لقراءة المحتوى وحقنه inline
		// كنسخة احتياطية إذا تعطّل تحميل الملف الخارجي (انظر enqueue_style_with_fallback).
		$this->UI__Path    = $this->YC__CFM->YC__CFM_Path.'UI/';
		$this->JS__Path    = $this->YC__CFM->YC__CFM_Path.'UI/js/';
		$this->Style__Path = $this->YC__CFM->YC__CFM_Path.'UI/css/';
	}

	/**
	 * يحمّل ملف CSS عبر wp_enqueue_style() العادي + يحقن نفس المحتوى inline
	 * على نفس الـ handle كنسخة احتياطية (نفس أسلوب
	 * Kayan_Admin_Platform::enqueue_assets()) — إذا تعطّل تحميل الملف
	 * الخارجي (حجب من الاستضافة/الحماية/محسّن أصول)، التنسيق يبقى يعمل لأنه
	 * جزء من استجابة الصفحة نفسها ولا يحتاج طلب شبكة منفصل.
	 *
	 * @param string $handle Style handle.
	 * @param string $url    External URL.
	 * @param string $path   Filesystem path (لقراءة المحتوى للنسخة inline).
	 * @return void
	 */
	private function enqueue_style_with_fallback( $handle, $url, $path ) {
		$ver = file_exists( $path ) ? (string) filemtime( $path ) : '2.4.1';
		wp_enqueue_style( $handle, $url, array(), $ver );
		if ( file_exists( $path ) ) {
			$contents = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( is_string( $contents ) && '' !== $contents ) {
				wp_add_inline_style( $handle, $contents );
			}
		}
	}

	/**
	 * نفس فكرة enqueue_style_with_fallback() لكن لملفات JS.
	 *
	 * @param string $handle    Script handle.
	 * @param string $url       External URL.
	 * @param string $path      Filesystem path.
	 * @param bool   $in_footer In footer?
	 * @return void
	 */
	private function enqueue_script_with_fallback( $handle, $url, $path, $in_footer = true ) {
		$ver = file_exists( $path ) ? (string) filemtime( $path ) : '2.4.1';
		wp_enqueue_script( $handle, $url, array(), $ver, $in_footer );
		if ( file_exists( $path ) ) {
			$contents = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( is_string( $contents ) && '' !== $contents ) {
				wp_add_inline_script( $handle, $contents );
			}
		}
	}

	/**
	 * v2.4.1 كانت تُقيّد تحميل هذه الأصول بصفحات YTS/yts-* فقط (تحسين مأخوذ من
	 * خط v1.4.10) — أُلغي هذا التقييد بعد ثبوت أنه يكسر لوحة "إعدادات القالب"
	 * نفسها على مواقع حقيقية (بعض تبويبات/مسارات الصفحة لا تُطابق الفحص كما
	 * هو متوقع). التحميل الآن غير مشروط دائماً — كما كان قبل v1.4.10 تماماً
	 * (المرجع: KAYAN v1.4.9 الذي يعمل بلا أي مشكلة). فقدان تحسين بسيط في وزن
	 * الصفحة أفضل بكثير من كسر لوحة تحكم القالب.
	 */
	private function should_load_admin_assets( $hook = '' ) {
		unset( $hook );
		return true;
	}

	public function YC__CFM_AdminFooter(){
		# echo '<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>';
		echo '<script type="text/javascript" src="'.$this->JS__URL.'bootstrap.bundle.min.js"></script>';

		# colorpicker
			echo '<script type="text/javascript" src="'.$this->JS__URL.'bootstrap-colorpicker.min.js"></script>';

		# codemirror
			echo '<script type="text/javascript" src="'.$this->JS__URL.'codemirror.js"></script>';

		# datepicker	
			echo '<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>';

		# jquery-ui (CDN كاحتياطي — ووردبريس sortable يُحمَّل أيضاً عبر wp_enqueue)
			echo '<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';
			echo '<script>window.kayanInitSortables=function($){if(!$)return;if(!$.fn||!$.fn.sortable){return;}$(".apbsortable").each(function(){var $list=$(this);try{if($list.data("ui-sortable")){$list.sortable("destroy");}}catch(e){}var handle=$list.attr("data-connect-with");if(handle==="sortbyme"){handle="sortbyme, .-widget-item-title-";}if(!handle){handle=".-widget-item-title-, sortbyme, .Title-MoreForms-Dublicate, .move-btn";}$list.sortable({items:"> *",handle:handle,cancel:"input,textarea,button,select,option,a,.-widget-open,.-widget-remove",cursor:"grabbing",tolerance:"pointer",placeholder:"kayan-sort-placeholder",forcePlaceholderSize:true,opacity:0.95,delay:50,distance:4,scroll:true,scrollSensitivity:60,axis:"y"});});};jQuery(function($){window.kayanInitSortables($);setTimeout(function(){window.kayanInitSortables($);},400);setTimeout(function(){window.kayanInitSortables($);},1200);});</script>';

		# owl carousel
			echo '<script src="'.$this->JS__URL.'owl.carousel.min.js"></script>';

		# INSERT PIN JQUERY .	
			do_action('YC__CFM__pin_jquery');

		# YC__CFM__before_main_js
			do_action('YC__CFM__before_main_js');	

		# YC__CFM__after_main_js
			do_action('YC__CFM__after_main_js');	

		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();

		}
	 	wp_register_script('mediaelement', plugins_url('wp-mediaelement.min.js', __FILE__), array('jquery'), '4.8.2', true);
		wp_enqueue_script('mediaelement');

		wp_print_media_templates();

		# CUSTOM JS — نفس أسلوب النسخة الاحتياطية inline المستخدم للـ CSS أعلاه
			$this->enqueue_script_with_fallback( 'kayan-cfm-custom-setup', $this->UI__URL.'Custom-Setup.js', $this->UI__Path.'Custom-Setup.js', true );

	}

	public function YC__CFM_Admin_Enqueue( $hook = '' ){
		if ( ! $this->should_load_admin_assets( $hook ) ) {
			return;
		}
		// FontAwesome الكامل (~100 كيلوبايت) يبقى بلا نسخة inline احتياطية عمداً —
		// فقدانه يعني أيقونات مفقودة فقط، لا كسر تخطيط الصفحة، ولا يستحق مضاعفة
		// وزن كل تحميل بهذا الحجم. باقي الملفات أدناه هي المسؤولة فعلياً عن شكل
		// لوحة "إعدادات القالب" (بطاقات/مفاتيح تبديل/تباعد) فتحصل على نسخة inline.
		echo '<link rel="stylesheet" href="'.esc_url( get_template_directory_uri().'/components/styles/FontAwesome/css/all.min.css' ).'">';

		$this->enqueue_style_with_fallback( 'kayan-cfm-codemirror', $this->Style__URL.'codemirror.css', $this->Style__Path.'codemirror.css' );
		$this->enqueue_style_with_fallback( 'kayan-cfm-richtext', $this->Style__URL.'richtext.min.css', $this->Style__Path.'richtext.min.css' );
		$this->enqueue_style_with_fallback( 'kayan-cfm-colorpicker', $this->Style__URL.'bootstrap-colorpicker.css', $this->Style__Path.'bootstrap-colorpicker.css' );
		$this->enqueue_style_with_fallback( 'kayan-cfm-custom', $this->UI__URL.'Custom-Style.css', $this->UI__Path.'Custom-Style.css' );
		$this->enqueue_style_with_fallback( 'kayan-cfm-admin-mobile', $this->Style__URL.'admin-mobile.css', $this->Style__Path.'admin-mobile.css' );
		$this->enqueue_style_with_fallback( 'kayan-cfm-admin-ui-fixes', $this->Style__URL.'admin-ui-fixes.css', $this->Style__Path.'admin-ui-fixes.css' );
		$this->enqueue_style_with_fallback( 'kayan-cfm-fa-free-fixes', get_template_directory_uri().'/components/styles/fa-free-fixes.css', get_template_directory().'/components/styles/fa-free-fixes.css' );
		$this->enqueue_style_with_fallback( 'kayan-cfm-flatpickr', $this->Style__URL.'flatpickr.min.css', $this->Style__Path.'flatpickr.min.css' );

		# تأكيد تحميل jQuery UI Sortable من ووردبريس (لترتيب عناصر الرئيسية)
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
	}

	public function Setup(){
		add_action( 'admin_enqueue_scripts', array($this, 'YC__CFM_Admin_Enqueue') );
		add_action('admin_footer', array($this, 'YC__CFM_AdminFooter'));

	}
	
}
(new YC__CFM_Enqueues)->Setup();