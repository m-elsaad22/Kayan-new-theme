<?php @ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '128M');
@ini_set( 'max_execution_time', '300' );
ob_start();

class ThemeTree {
	private $args;
	private $_GET;
	private $_POST;
	function __construct($args=array()) {
		$this->args = $args;
		$this->Method = array(
			'GETs'=>$_GET,
			'POSTs'=>$_POST,
		);
		$this->TempPath = get_template_directory();
		$this->TempURL = get_template_directory_uri();
		$this->StylesURL = get_template_directory_uri().'/components/styles/';
		$this->StylesPath = get_template_directory().'/components/styles/';
		$this->folderpath = $this->TempPath.'/components/packs/*/';
		$this->packsPath = $this->TempPath.'/components/packs/';
		$this->Packages = array_filter(glob($this->folderpath), 'is_dir');
		if( !class_exists('ThemeStatic') ) {
			require($this->TempPath.'/syntax.php');
		}
	}
	public function AddTaxonomy($id='', $ptypes=array(), $name='', $rewrite=false, $hierarchical=true) {
		$labels = array(
			'name' => __($name, 'yourcolor' , 'post type general name'),
			'all_items' => __('كل العناصر', 'yourcolor' , 'all items'),
			'add_new_item' => __('اضافة عنصر جديد', 'yourcolor' , 'adding a new item'),
			'new_item_name' => __('اسم عنصر جديد', 'yourcolor' , 'adding a new item'),
		);
		register_taxonomy( $id, $ptypes, 
			array( 
				'hierarchical' => $hierarchical,
				'rewrite' => $rewrite,
				'labels' => $labels,
			)
		);
	}
	public function AddPType($name, $singlename, $plus='', $id='', $public=true, $rewrite=false, $supports=array(), $position='') {
		$labels = array(
			'name'               => __( $name, 'post type general name', 'yourcolor' ),
			'singular_name'      => __( $name, 'post type singular name', 'yourcolor' ),
			'menu_name'          => __( $name, 'admin menu', 'yourcolor' ),
			'name_admin_bar'     => __( $name, 'add new on admin bar', 'yourcolor' ),
			'add_new'            => __( 'اضف جديد', 'search', 'yourcolor' ),
			'add_new_item'       => __( 'إضافة '.$singlename.' جديد'.$plus, 'yourcolor' ),
			'new_item'           => __( $singlename.' جديد'.$plus, 'yourcolor' ),
			'edit_item'          => __( 'تعديل '.$singlename, 'yourcolor' ),
			'all_items'          => __( 'كل '.$name, 'yourcolor' ),
			'search_items'       => __( 'بحث  في '.$name, 'yourcolor' ),
			'parent_item_colon'  => __( $singlename.' الرئيس', 'yourcolor' ),
			'not_found'          => __( 'لا يوجد عناصر.', 'yourcolor' ),
			'not_found_in_trash' => __( 'لا يوجد عناصر فى سلة المهملات.', 'yourcolor' )
		);
		$args = array(
			'labels'             => $labels,
			'public'             => $public,
			'rewrite'             => $rewrite,
			'supports'           => $supports,
		);
		if( is_numeric($position) ) {
			$args['menu_position'] = $position;
		}
		register_post_type( $id, $args );
	}
	public function Require($path, $vars=array()) {
		extract($vars);
		if( file_exists($path) ) {
			require($path);
		}else {
			echo '<p><strong>هذا المسار غير موجود :</strong>'.$path.'</p>';
		}
	}
	public function Initialize() {
		do_action('Initialize');
	}
}
// تحميل ملف الترجمة العربية لنصوص منصة KAYAN (text domain 'kayan') —
// languages/kayan-ar.mo. لا يمس Text Domain الأساسي للثيم ('yourcolor').
add_action( 'after_setup_theme', function() {
	load_theme_textdomain( 'kayan', get_template_directory() . '/languages' );
} );

$ThemeTree = new ThemeTree;
add_action('init', array($ThemeTree, 'Initialize'));
$ThemeStatic = new ThemeStatic();
$packs = $ThemeTree->Packages;
foreach ($packs as $pack) {
	if( substr(basename($pack), 0, 1) != '@' and substr(basename($pack), 0, 1) != '#' ) {
		$path = $pack.'setup.php';
		$ThemeTree->Require($path, array('CurrentDir'=>$pack));
	}
}
wp_reset_query();
remove_action( 'shutdown', 'wp_ob_end_flush_all',1);
// ============================================================
// YC Multisite Option Helpers
// بيضيف blog_id prefix تلقائياً في Multisite
// عشان كل موقع في الشبكة يحفظ إعداداته الخاصة
// ============================================================
function yc_option_key( $key ) {
    if ( is_multisite() ) {
        return 'site_' . get_current_blog_id() . '_' . $key;
    }
    return $key;
}
function yc_get_option( $key, $default = false ) {
    static $cache = array();
    $opt_key = yc_option_key( $key );
    if ( ! array_key_exists( $opt_key, $cache ) ) {
        $cache[ $opt_key ] = get_option( $opt_key, $default );
    }
    return $cache[ $opt_key ];
}
function yc_update_option( $key, $value ) {
    static $cache = array();
    $opt_key = yc_option_key( $key );
    $cache[ $opt_key ] = $value;
    return update_option( $opt_key, $value );
}
function yc_delete_option( $key ) {
    static $cache = array();
    $opt_key = yc_option_key( $key );
    unset( $cache[ $opt_key ] );
    return delete_option( $opt_key );
}
// ============================================================

// ============================================================
// 404 صحيحة — بديل عن تحويل 303 القديم للرئيسية
// (SEO عبر Rank Math — القالب يضمن فقط كود الحالة الصحيح)
// ============================================================
function kayan_send_404_status() {
	if ( is_404() ) {
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'wp', 'kayan_send_404_status', 1 );
// ============================================================
