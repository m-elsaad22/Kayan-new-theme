<?php 
require_once __DIR__ . '/icon-helpers.php';

class SvgCenter {
	function __construct() {
		$this->ThemeStatic = new ThemeStatic;
	}
	public function QueryEndpoint() {
		add_rewrite_endpoint( 'SvgCenter', EP_ROOT );
	}
	public function SvgCenterPage() {
		if($SvgCenter = get_query_var('SvgCenter')){
			#
			$SvgCenterPath = get_template_directory().'/components/packs/SvgCenter/';
			$SvgCenterURL = get_template_directory_uri().'/components/packs/SvgCenter/';
			#
			header("Content-Type: application/json");
			ob_start();
			$safe = sanitize_file_name( (string) $SvgCenter );
	    	require(get_template_directory().'/components/packs/SvgCenter/icons/'.$safe.'.php');
	    	$HTML__output = ob_get_clean();
	    	echo json_encode($HTML__output);
	    	die();
	    }
	}


	public function Setup() {
		add_action( 'init', array( $this, 'QueryEndpoint' ) );
		add_action( 'BeforeHeader', array( $this, 'SvgCenterPage' ) );
	}
}
(new SvgCenter)->Setup();
#
function SVGIcon($icon) {
	$icon = sanitize_file_name( (string) $icon );
	$iconpath = get_template_directory().'/components/packs/SvgCenter/icons/'.$icon.'.php';
	ob_start();
	if( file_exists($iconpath) ) {
		require $iconpath;
	}
	$icon = ob_get_clean();
	return $icon;
}