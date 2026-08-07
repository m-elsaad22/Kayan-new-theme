<?php class YC__CFM__AjaxCallBack{

	function __construct( $arguments=array() ) {
		$this->YC__CFM = new YC__CFM;
	}

	public function AjaxRequire(){
		if( isset( $_GET['action'] ) || isset( $_POST['action'] ) ){
			$Current__data = ( ( isset( $_GET['action'] ) ) ) ? $_GET : $_POST;


			foreach ( is_array( $this->YC__CFM->AjaxCallBack_Packages ) ? $this->YC__CFM->AjaxCallBack_Packages : array() as $k => $file__object ) {

				$object__name = basename($file__object);
				$object__name = explode('.php', $object__name)[0];
				#
				if( $Current__data['action'] == $object__name ){
					$this->YC__CFM->Require($file__object,array('Ajax__data'=>$Current__data,'CurrentDir'=>$file__object ));
				}

			}
		}
		die();
	}

	public function Setup(){
		foreach ( is_array( $this->YC__CFM->AjaxCallBack_Packages ) ? $this->YC__CFM->AjaxCallBack_Packages : array() as $k => $file__object ) {
			$object__name = basename($file__object);
			$object__name = explode('.php', $object__name)[0];
			add_action( 'wp_ajax_'.$object__name, array($this,'AjaxRequire') );
		}
	}
}
(new YC__CFM__AjaxCallBack)->Setup();