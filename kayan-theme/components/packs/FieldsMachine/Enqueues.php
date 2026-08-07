<?php # YOURCOLOR CUSTOM FIELDS MACHINE.
class YC__CFM_Enqueues {
	function __construct($arguments=array()) {
		$this->YC__CFM = new YC__CFM;
		// 
		$this->UI__URL = $this->YC__CFM->YC__CFM_URL.'/UI/';
		$this->JS__URL = $this->YC__CFM->YC__CFM_URL.'UI/js/';
		$this->Style__URL = $this->YC__CFM->YC__CFM_URL.'UI/css/';

	}

	/**
	 * أصول FieldsMachine فقط في صفحات إعدادات القالب (YTS / yts-*)
	 * وشاشات تحرير المنشورات/التصنيفات — لا تُحمَّل على صفحات Rank Math وغيرها.
	 */
	private function should_load_admin_assets( $hook = '' ) {
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		if ( 'YTS' === $page || 'yts' === $page || 0 === stripos( $page, 'yts-' ) ) {
			return true;
		}
		if ( is_string( $hook ) && '' !== $hook && false !== stripos( $hook, 'yts' ) ) {
			return true;
		}
		if ( in_array( $hook, array( 'post.php', 'post-new.php', 'term.php', 'edit-tags.php' ), true ) ) {
			return true;
		}
		# admin_footer لا يمرّر $hook — استخدم $pagenow / الشاشة الحالية
		global $pagenow;
		if ( in_array( (string) $pagenow, array( 'post.php', 'post-new.php', 'term.php', 'edit-tags.php' ), true ) ) {
			return true;
		}
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && ! empty( $screen->id ) && false !== stripos( (string) $screen->id, 'yts' ) ) {
				return true;
			}
		}
		return false;
	}

	public function YC__CFM_AdminFooter(){
		if ( ! $this->should_load_admin_assets() ) {
			return;
		}

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

		# CUSTOM JS
			echo '<script src="'.$this->UI__URL.'Custom-Setup.js?'.rand().'" type="text/javascript"></script>';

	}

	public function YC__CFM_Admin_Enqueue( $hook = '' ){
		if ( ! $this->should_load_admin_assets( $hook ) ) {
			return;
		}
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'codemirror.css" />';
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'richtext.min.css" />';
		echo '<link rel="stylesheet" href="'.get_template_directory_uri().'/components/styles/FontAwesome/css/all.min.css">';
		echo '<link href="'.$this->Style__URL.'bootstrap-colorpicker.css" rel="stylesheet">';

		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->UI__URL.'Custom-Style.css?'.rand().'" />';
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'admin-mobile.css?v=2.4.1" />';
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'admin-ui-fixes.css?v=2.4.1" />';
		echo '<link rel="stylesheet" href="'.get_template_directory_uri().'/components/styles/fa-free-fixes.css?v=2.4.1">';
		echo '<link href="'.$this->Style__URL.'flatpickr.min.css" rel="stylesheet">';
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