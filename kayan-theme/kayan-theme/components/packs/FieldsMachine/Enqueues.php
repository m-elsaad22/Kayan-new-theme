<?php # YOURCOLOR CUSTOM FIELDS MACHINE.
class YC__CFM_Enqueues {
	function __construct($arguments=array()) {
		$this->YC__CFM = new YC__CFM;
		// 
		$this->UI__URL = $this->YC__CFM->YC__CFM_URL.'/UI/';
		$this->JS__URL = $this->YC__CFM->YC__CFM_URL.'UI/js/';
		$this->Style__URL = $this->YC__CFM->YC__CFM_URL.'UI/css/';

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

		# jquery-ui (CDN كاحتياطي — ووردبريس sortable يُحمَّل أيضاً عبر wp_enqueue)
			echo '<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';
			echo '<script>window.kayanInitSortables=function($){if(!$)return;$(".apbsortable").each(function(){var $list=$(this);if($list.data("ui-sortable")){try{$list.sortable("destroy");}catch(e){}}var handle=$list.attr("data-connect-with")||$list.data("connectWith")||"sortbyme, .-widget-item-title-";if($.fn.sortable){$list.sortable({handle:handle,cursor:"grabbing",tolerance:"pointer",placeholder:"kayan-sort-placeholder",forcePlaceholderSize:true,opacity:0.92,axis:"y",update:function(){/* order preserved by DOM */}});}});};</script>';

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

	public function YC__CFM_Admin_Enqueue(){
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'codemirror.css" />';
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'richtext.min.css" />';
		echo '<link rel="stylesheet" href="'.get_template_directory_uri().'/components/styles/FontAwesome/css/all.min.css">';
		echo '<link href="'.$this->Style__URL.'bootstrap-colorpicker.css" rel="stylesheet">';

		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->UI__URL.'Custom-Style.css?'.rand().'" />';
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'admin-mobile.css?v=1.4.5" />';
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this->Style__URL.'admin-ui-fixes.css?v=1.4.5" />';
		echo '<link rel="stylesheet" href="'.get_template_directory_uri().'/components/styles/fa-free-fixes.css?v=1.4.5">';
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