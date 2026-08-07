<?php  /* TEST SINGLE PAGE SETUP .
	
	# DO ACTION 'before_insert_actions__list' -> INSERT OR EDIT PAGES 'before_insert_post_type' ACTION 

	# TEMPLATE DO ACTIONS .
		- # do_action( {<$post_type_name>}__before__insert_fields )  -> APPEND YOUR OUTPUT BEFORE INSERT PAGE FIELDS .
		- # do_action( {<$post_type_name>}__after__insert_fields ) -> APPEND YOUR OUTPUT AFTER INSERT PAGE FIELDS .
		- # do_action( {<$post_type_name>}__after__insert_save_fields ,$post_id ) -> DURING SUBMIT FORM DATA .

	# ARGUMENTS -> ARRAY()

		- # 'path' -> OPEN FILE IN CUSTOM PATH , ( STRING )
		- # 'object__name' -> POST TYPE NAME , ( STRING )
		
		- # 'page__icon' -> SEND DEFULT PAGE ICON .. DEFULT '$this->defult__page__icon' ,( STRING )
		- # 'page__title' -> SEND CUSTOM PAGE TITLE ,( STRING )
		- # 'page__sub_title' -> SEND CUSTOM PAGE SUB TITLE ,( STRING )

		- # 'set__post_status' -> AFTER SUBMUT SET 'post_status' .. DEFULT '$this->defult__post_status' ,( STRING )
		- # 'show__post_title__field' -> SHOW 'post_title' FIELD IN PAGE [ 'true' || 'false' ] || DEFULT '$this->show__post_title__field',
		
		- # 'setup__fields' -> ARRAY()
			-- # '<wiget_name>' -> ARRAY()
				--- # icon -> WIDGET ICON ,
				--- # title -> WIDGET TITLE ,
				--- # fields -> ARRAY()
					---- # ARRAY() SINGLE FIELD ITEM,
					---- # ARRAY() SINGLE FIELD ITEM,

		- # 'setup__taxonomy' -> ARRAY()
			-- # '<taxonomy_name>' -> ARRAY()
				--- # 'title' -> SET THIS FIELD <TAXONOMY TITLE>,
				--- # 'desctiption' -> SET THIS FIELD <TAXONOMY DESCRIPTION>,
				--- # 'require' -> REQUIRE SELECT VALUE TO SUBMIT POST ? [ 'true' || 'false' ]
*/
class before_insert_post_page {
	function __construct() {
		$this->YC__CFM = new YC__CFM;
		$this->BeforeInsert__path = $this->YC__CFM->YC__CFM_Path.'BeforeInsert/*';
		$this->BeforeInsert__Packages = glob( $this->BeforeInsert__path );

		# It controls the method of adding, if 'true' is appointed, the files in directory "BeforeInsert" will be relied upon, and if 'false' is appointed, the "do_action('before_insert_post_page')" will be relied upon.
		$this->AutomaticInsertPages = true;

		# SET DEFULT PAGE TEMPLATE NAME .
			$this->defult__template = 'defult__template__page';

		# DEFULT POST_STATUS .	
			$this->defult__post_status = 'draft';

		# DEFULT LORD ICON 	
			$this->defult__page__icon = 'xvlmqqih';

		# SHOW TITLE FIELD .	
			$this->show__post_title__field = true;
	}

	public function get_before_insert_pages(){
		$List = array();
		$Pack__items = ( is_array( $this->BeforeInsert__Packages )) ? $this->BeforeInsert__Packages : array();
		foreach ( $Pack__items as $k => $v ) {
			$file_name = basename($v);
			$file_name = explode('.php', $file_name)[0];
			$List[$file_name] = array(
				'path'=>$v,
				'object__name'=>$file_name,
				'set__post_status'=>$this->defult__post_status,
				'page__icon'=>$this->defult__page__icon,
				'show__post_title__field'=>$this->show__post_title__field,
			);
		}
		return $List;
	}

	public function PostsInsertPage(){

		global $before_insert_actions__list,$post;

		$before_insert_actions__list = array();
		if( $this->AutomaticInsertPages != false ){
			$before_insert_actions__list = $this->get_before_insert_pages();
		}
		do_action('before_insert_actions__list');

		# EXTRACT POST TYPE NOW .
			$post_type = (isset( $_GET['post_type'] )) ? $_GET['post_type'] : 'post';


		# NOT FOUND THIS POST TYPE IN INSERT LIST '$before_insert_actions__list'
			if( !isset( $before_insert_actions__list[ $post_type ] ) ) return ;

		# ADD BODY CUSTOM CLASS .
			add_filter( 'admin_body_class', function( $classes ) {
				$post_type = (isset( $_GET['post_type'] )) ? $_GET['post_type'] : 'post';
				$classes .= ' YC-before_insert_post_page';
				$classes .= ' YC-before_insert_objectType_'.$post_type;
				return $classes; 
			});

		# SET DEFULT IMPORTANT METHODS
			if( !isset( $before_insert_actions__list[ $post_type ]['path'] ) ) $before_insert_actions__list[ $post_type ]['path'] = $before_insert_actions__list[ $this->defult__template ]['path'];
			if( !isset( $before_insert_actions__list[ $post_type ]['set__post_status'] ) ) $before_insert_actions__list[ $post_type ]['set__post_status'] = $this->defult__post_status;
			if( !isset( $before_insert_actions__list[ $post_type ]['page__icon'] ) ) $before_insert_actions__list[ $post_type ]['page__icon'] = $this->defult__page__icon;
			if( !isset( $before_insert_actions__list[ $post_type ]['show__post_title__field'] ) ) $before_insert_actions__list[ $post_type ]['show__post_title__field'] = $this->show__post_title__field;
			if( isset( $setup__fields ) ) $before_insert_actions__list[ $post_type ]['setup__fields'] = ( ( is_array( $before_insert_actions__list[ $post_type ]['setup__fields'] ) ) ) ? $before_insert_actions__list[ $post_type ]['setup__fields'] : array();


		$post = get_post_type_object( $post_type );
		$post->post_type = $post_type;
		$labels = $post->labels;

		$BladeArgums = array('object__type'=>'posts','object__name'=>$post_type);
		//
		include( ABSPATH . 'wp-admin/admin-header.php' );

		$this->YC__CFM->Require( $before_insert_actions__list[ $post_type ]['path'] , array_merge( $BladeArgums,$before_insert_actions__list[ $post_type ]) );

		include( ABSPATH . 'wp-admin/admin-footer.php' );
		exit();
	}

	public function Setup() {
		add_filter('load-post-new.php', array( $this, 'PostsInsertPage') );
		
	}
}
(new before_insert_post_page)->Setup();