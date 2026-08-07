<?php 
class AjaxCenter {
	function __construct() {
		$this->ThemeStatic = new ThemeStatic;
	}
	public function QueryEndpoint() {
		add_rewrite_endpoint( 'AjaxCenter', EP_ROOT );
	}
	public function AjaxCenterPage() {
		if($AjaxCenter = get_query_var('AjaxCenter')){
			$Action = explode('/', $AjaxCenter)[0];
			if( strpos( $AjaxCenter , $Action.'/') !== FALSE ){
				$Params = explode($Action.'/', $AjaxCenter)[1];
			}else{
				$Params = '';
			}
			$AjaxCenterPath = get_template_directory().'/components/packs/AjaxCenter/';
			$AjaxCenterURL = get_template_directory_uri().'/components/packs/AjaxCenter/';

			# ═══ حماية LFI: الاسم يُقيَّد بالأحرف الآمنة ويُطابق قائمة ملفات المجلد الفعلية فقط ═══
			$Action = sanitize_key( basename( (string) $Action ) );
			$Allowed = array();
			foreach ( (array) glob( $AjaxCenterPath.'*.php' ) as $Allowed_file ) {
				$Allowed[] = basename( $Allowed_file, '.php' );
			}
			if ( '' === $Action || 'setup' === $Action || ! in_array( $Action, $Allowed, true ) ) {
				status_header( 404 );
				die();
			}
	    	require( $AjaxCenterPath.$Action.'.php' );
	    	die();
	    }
	}
	public function Setup() {
		add_action( 'init', array( $this, 'QueryEndpoint' ) );
		add_action( 'BeforeHeader', array( $this, 'AjaxCenterPage' ) );
	}
}
(new AjaxCenter)->Setup();