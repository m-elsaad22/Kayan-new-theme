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
	 * وشاشات تحرير المنشورات/التصنيفات — لا تُحمَّل على صفحات Rank Math وغيرها.
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

	/**
	 * Stable asset version (filemtime when available).
	 *
	 * @param string $path Absolute path.
	 * @return string
	 */
	private function asset_ver( $path ) {
		if ( is_string( $path ) && file_exists( $path ) ) {
			return (string) filemtime( $path );
		}
		$theme = wp_get_theme( get_template() );
		$ver   = $theme ? $theme->get( 'Version' ) : '';
		return $ver ? (string) $ver : '2.4.3';
	}

	public function YC__CFM_AdminFooter(){
		if ( ! $this->should_load_admin_assets() ) {
			return;
		}

		$ver = $this->asset_ver( $this->YC__CFM->YC__CFM_Path . '/UI/Custom-Setup.js' );

		wp_enqueue_script( 'kayan-cfm-bootstrap', $this->JS__URL . 'bootstrap.bundle.min.js', array( 'jquery' ), $ver, true );
		wp_enqueue_script( 'kayan-cfm-colorpicker', $this->JS__URL . 'bootstrap-colorpicker.min.js', array( 'jquery', 'kayan-cfm-bootstrap' ), $ver, true );
		wp_enqueue_script( 'kayan-cfm-codemirror', $this->JS__URL . 'codemirror.js', array(), $ver, true );
		wp_enqueue_script( 'kayan-cfm-flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.13', true );
		wp_enqueue_script( 'kayan-cfm-owl', $this->JS__URL . 'owl.carousel.min.js', array( 'jquery' ), $ver, true );

		# INSERT PIN JQUERY .
			do_action('YC__CFM__pin_jquery');

		# YC__CFM__before_main_js
			do_action('YC__CFM__before_main_js');	

		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );

		$sortable_boot = 'window.kayanInitSortables=function($){if(!$)return;if(!$.fn||!$.fn.sortable){return;}$(".apbsortable").each(function(){var $list=$(this);try{if($list.data("ui-sortable")){$list.sortable("destroy");}}catch(e){}var handle=$list.attr("data-connect-with");if(handle==="sortbyme"){handle="sortbyme, .-widget-item-title-";}if(!handle){handle=".-widget-item-title-, sortbyme, .Title-MoreForms-Dublicate, .move-btn";}$list.sortable({items:"> *",handle:handle,cancel:"input,textarea,button,select,option,a,.-widget-open,.-widget-remove",cursor:"grabbing",tolerance:"pointer",placeholder:"kayan-sort-placeholder",forcePlaceholderSize:true,opacity:0.95,delay:50,distance:4,scroll:true,scrollSensitivity:60,axis:"y"});});};jQuery(function($){window.kayanInitSortables($);setTimeout(function(){window.kayanInitSortables($);},400);setTimeout(function(){window.kayanInitSortables($);},1200);});';
		wp_add_inline_script( 'jquery-ui-sortable', $sortable_boot );

		wp_enqueue_script( 'kayan-cfm-custom-setup', $this->UI__URL . 'Custom-Setup.js', array( 'jquery', 'jquery-ui-sortable' ), $ver, true );

		# YC__CFM__after_main_js
			do_action('YC__CFM__after_main_js');	

		wp_print_media_templates();
	}

	public function YC__CFM_Admin_Enqueue( $hook = '' ){
		if ( ! $this->should_load_admin_assets( $hook ) ) {
			return;
		}

		$dir = $this->YC__CFM->YC__CFM_Path;
		$ver = $this->asset_ver( $dir . '/UI/Custom-Style.css' );

		wp_enqueue_style( 'kayan-cfm-codemirror', $this->Style__URL . 'codemirror.css', array(), $this->asset_ver( $dir . '/UI/css/codemirror.css' ) );
		wp_enqueue_style( 'kayan-cfm-richtext', $this->Style__URL . 'richtext.min.css', array(), $this->asset_ver( $dir . '/UI/css/richtext.min.css' ) );
		wp_enqueue_style( 'kayan-cfm-fontawesome', get_template_directory_uri() . '/components/styles/FontAwesome/css/all.min.css', array(), $this->asset_ver( get_template_directory() . '/components/styles/FontAwesome/css/all.min.css' ) );
		wp_enqueue_style( 'kayan-cfm-colorpicker', $this->Style__URL . 'bootstrap-colorpicker.css', array(), $this->asset_ver( $dir . '/UI/css/bootstrap-colorpicker.css' ) );
		wp_enqueue_style( 'kayan-cfm-custom-style', $this->UI__URL . 'Custom-Style.css', array( 'kayan-cfm-fontawesome' ), $ver );
		wp_enqueue_style( 'kayan-cfm-admin-mobile', $this->Style__URL . 'admin-mobile.css', array( 'kayan-cfm-custom-style' ), $this->asset_ver( $dir . '/UI/css/admin-mobile.css' ) );
		wp_enqueue_style( 'kayan-cfm-admin-ui-fixes', $this->Style__URL . 'admin-ui-fixes.css', array( 'kayan-cfm-admin-mobile' ), $this->asset_ver( $dir . '/UI/css/admin-ui-fixes.css' ) );
		wp_enqueue_style( 'kayan-cfm-fa-free-fixes', get_template_directory_uri() . '/components/styles/fa-free-fixes.css', array( 'kayan-cfm-fontawesome' ), $this->asset_ver( get_template_directory() . '/components/styles/fa-free-fixes.css' ) );
		wp_enqueue_style( 'kayan-cfm-flatpickr', $this->Style__URL . 'flatpickr.min.css', array(), $this->asset_ver( $dir . '/UI/css/flatpickr.min.css' ) );

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
