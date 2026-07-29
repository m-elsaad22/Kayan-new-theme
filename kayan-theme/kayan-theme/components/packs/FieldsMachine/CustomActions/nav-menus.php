<?php  /**
 * 
 */
class YC__Setup_NavMenus{
	
	function __construct(){
		
	}

	public function YC__InsertNavMenus(){
		global $YC__nav__menus;

		$YC__nav__menus['main-menu'] = __( 'قائمة الموقع', 'YourColor' );
	}

	public function YC__SetupNavMenus() {
		global $YC__nav__menus;

		register_nav_menus($YC__nav__menus);

	}

	public function Setup(){
		add_action('YC__nav__menus',array($this,'YC__InsertNavMenus'),1);
		add_action('Initialize', array( $this,'YC__SetupNavMenus') );
	}
}
(new YC__Setup_NavMenus)->Setup();