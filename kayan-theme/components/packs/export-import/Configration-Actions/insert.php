<?php
/* 
# POST INSERT METHODS function( InsertPost ) .
	array(
		'search'=> PARENT PAGE INSERT VALUE ,
		'metakey_search'=> PARENT PAGE FIELD METAKEY,

		'postArguments'=>array(
			'post_type'=> INSERT POST TYPE,
			'post_title'=> INSERT POST TITLE,
			'post_content'=> INSERT POST CONTENT,

			... and other keys in WP_POST OBJECT .
		),
		'post_meta'=> array(
			'metakey'=>meta_value,
		),
		'taxonomy'=>array(
			'taxonomy_name'=> array(
				'slug' OR 'term_id'
			),
		),
		'poster'=> POST THUMBNAIL URL,	
		'ImportantUpdate'=> true  # IMPORTANT WP UPDATE POST .
	);
	

# INSERT TERM IN TAXONOMY function( InsertTerm ) .

	array(
		'search'=> PARENT PAGE INSERT VALUE ,
		'metakey_search'=> PARENT PAGE FIELD METAKEY,

		'taxonomy'=> TAXONOMY NAME ,
		'name'=> TERM NAME ,
		'Arguments'=>array(
			'slug'=> TERM SLUG ,
			'parent'=> PARNT term_id ,
			... and other keys in WP_TERM OBJECT .
		),
		'ImportantUpdate' => true , # IMPORTANT WP_UPDATE_TERM .

		'term_meta'=> array(
			'metakey'=>meta_value,
		),
		'poster'=> IMAGE URL # UPLOAD IMAGE AND SET term_meta 'poster' .

	)

*/	
class YourColor__import__insert{
	function __construct( $args=array() ){
		$this->TaxonomesObjects = array();
		$this->PostTypesObjects = array();
		$this->DBArguments = new DBArguments;
	}
	# file_get_contents
		public function file_get_contents($url, $find=false, $times=0) {
		    $ch = curl_init($url);
		    curl_setopt($ch, CURLOPT_ENCODING,"");
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_HEADER, true);
		    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		    curl_setopt($ch, CURLOPT_FILETIME, true);
		    curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 5.1; rv:32.0) Gecko/20100101 Firefox/32.0");
		    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
		    curl_setopt($ch, CURLOPT_VERBOSE, true);
		    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		    curl_setopt($ch, CURLOPT_TIMEOUT,100);
		    curl_setopt($ch, CURLOPT_FAILONERROR,true);
		    $data = curl_exec($ch);
		    $error = curl_error($ch);
		    if (curl_errno($ch)){
			    return false;
		    }else {
			    $skip = intval(curl_getinfo($ch, CURLINFO_HEADER_SIZE)); 
			    $responseHeader = substr($data,0,$skip);
			    $data= substr($data,$skip);
			    $info = curl_getinfo($ch);
			    if ($info['http_code'] != '200') $info = var_export($info,true);
		    }
		    if( $find == false ) return $data;
		    if( strpos($data, $find) !== false ) return $data;
		    if( $times == 6 ) return false;
		    $times++;
		    return $this->file_get_contents($url, $find, $times);
		}

	# ParentPageSearch
		public function ParentPageSearch($search,$table="postmeta",$metakey="parentpage",$prifix=true){
			global $wpdb;
			$table = ( ( $prifix == true ) ? $wpdb->prefix : '' ).$table;

			$Searching = $this->DBArguments->get(
				array(
					'table'=>$table,
					'where'=>array(
						'meta_key'=> $metakey,
						'meta_value'=> $search
					),
				)
			);
			if($Searching != false && isset($Searching[0]) ){
				return $Searching[0];
			}
			return false;
		}
	# UPLOADS STAFF .
		public function UploadImageCheck($url){
			$DBArguments = new DBArguments;
			$Searching = $this->ParentPageSearch($url,'postmeta','ImageParentPage');
			if($Searching != false && isset($Searching->post_id) ){
				return $Searching->post_id;
			}			
		}

		public function UploadPhoto($image_url,$check=true) {
			$attach_id = 0;
			if( !empty($image_url) ) {
				// ## CHECK IMAGE INSERT OR NO ?!!
				$UploadImageCheck = false;

				// ## USER SEND FALSE ==  INSERT IMAGE AND DONT CHECK 
				// ## USER SEND TRUE ==  FIND THIS URL IN WP_POST_META ..
				if($check == true){
					$UploadImageCheck = $this->UploadImageCheck($image_url);
					if($UploadImageCheck != false){
						$attach_id = $UploadImageCheck;
					}
				}

				if($UploadImageCheck == false){
			    $post_id = '';
			    $upload_dir = wp_upload_dir();
			    $image_data = $this->file_get_contents($image_url);
			    $filename = rand().'.jpg';
			    if(wp_mkdir_p($upload_dir['path']))
		        $file = $upload_dir['path'] . '/' . $filename;
			    else
		        $file = $upload_dir['basedir'] . '/' . $filename;
			    file_put_contents($file, $image_data);
			    $wp_filetype = wp_check_filetype($filename, null );
			    $attachment = array(
			        'post_mime_type' => $wp_filetype['type'],
			        'post_title' => sanitize_file_name($filename),
			        'post_content' => '',
			        'post_status' => 'inherit'
			    );
			    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
			    update_post_meta($attach_id,'ImageParentPage',$image_url);
			    require_once(ABSPATH . 'wp-admin/includes/image.php');
			    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			    wp_update_attachment_metadata( $attach_id, $attach_data );
				}
			}
			return $attach_id;
		}


		# INSERT USER .	
			public function insert__user( $user ){

				if( isset( $user['ID'] ) ){
					$return = get_userdata( $user['ID'] );
					if( !isset( $return->ID ) ) return false;
				}
				$InsertStatus = 'edit';
				$insert___exists = true;

				if( !isset( $user['insert__action_type'] ) ) $user['insert__action_type'] = 'update';

				if( !isset( $user['ID'] ) ){

					# Insert Options
						$ImportantInsert = true;
						$InsertStatus = 'new';

					# Check .
						if( isset( $user['search'] ) && isset( $user['metakey_search']  ) ){
							$user_exists = $this->ParentPageSearch($user['search'],'usermeta',$user['metakey_search']);
							if($user_exists != false){
								$return = get_userdata($user_exists->user_id);
								if( isset( $return->ID ) ) {
									$ImportantInsert = false;
									$InsertStatus = 'edit';
								}
							}
						}

					# SELECT ACTION RETURN 'exists'	|| INSERT .
						if( $user['insert__action_type'] == 'exists' && $InsertStatus == 'edit' ) $insert___exists = false;
						/*echo ( ( $insert___exists == false ) ) ? '<pre>FALSE</pre>' : '<pre>TRUE</pre>';
						echo ( ( $ImportantInsert == false ) ) ? '<pre>FALSE</pre>' : '<pre>TRUE</pre>';
						*/

					# Insert .
						if( $ImportantInsert == true && $insert___exists == true ){
							$user_insert = wp_insert_user( $user['Arguments'] );

							if(is_wp_error($user_insert)) return $user_insert;
							$return = get_userdata($user_insert);

							# Update SearchValue .
								if( isset( $user['search'] ) && isset( $user['metakey_search'] ) ){
									update_user_meta($return->ID,$user['metakey_search'],$user['search']);
								}
						}					
				}

				if( $insert___exists == true ){
					# Update Object .
						if(isset($user['ImportantUpdate'])){
							wp_update_user( $user['Arguments']  );
						}

					# Term Meta .
						if(isset($user['user_meta'])){
							foreach ($user['user_meta'] as $metakey => $metavalue) {
								update_user_meta($return->ID,$metakey,$metavalue);
							}
						}

						update_user_meta( $return->ID,'YourColor__last__update',time() );
				}
				$return->YourColor__updated__type = $InsertStatus;
				return $return;
			}

	# INSERT POST .
		public function InsertPost($data,$upload=true){
			if(!isset($data['postArguments']['post_type']) && !isset( $data['postArguments']['ID'] )){
				return false;
				//echo "<pre>"; print_r($data); echo "</pre>";die();
			}

			$data['postArguments']['post_status'] = 'publish';
			if( isset( $data['postArguments']['ID'] ) ) $return = get_post( $data['postArguments']['ID'] );

			if( !isset( $data['postArguments']['ID'] ) ){


				# Insert Options
					$ImportantInsert = false;
					$InsertStatus = 'edit';

				# Check .
					$post_exists = $this->ParentPageSearch($data['search'],'postmeta',$data['metakey_search']);
					if($post_exists != false){
						$return = get_post($post_exists->post_id);
						if( isset( $return->ID ) ) $ImportantInsert = true;
					}

				# Insert .
					if($ImportantInsert == false){
						$InsertStatus = 'new';
						$PostID = wp_insert_post($data['postArguments']);

						$return = get_post($PostID);

						# Update SearchValue .
						update_post_meta($return->ID,$data['metakey_search'],$data['search']);

						# JUST UPDATE IN NEW INSERT .
							if(isset($data['new_post__meta'])){
								foreach ($data['new_post__meta'] as $metakey => $metavalue) {
									update_post_meta($return->ID,$metakey,$metavalue);
								}
							}

					}

				# SAVE PostTypesObjects	
					$this->PostTypesObjects[$data['postArguments']['post_type']][$data['search']] = $return;
			}

			# Update Object .
				if(isset($data['ImportantUpdate'])){
					if( !isset( $data['postArguments']['ID'] ) ) $data['postArguments']['ID'] = $return->ID;
					wp_update_post( $data['postArguments'] );
				}

			# POST Meta .
				if(isset($data['post_meta'])){
					foreach ($data['post_meta'] as $metakey => $metavalue) {
						update_post_meta($return->ID,$metakey,$metavalue);
					}
				}

			# TAXONOMY .
				if( isset( $data['taxonomy'] ) ){
					foreach ( $data['taxonomy'] as $tax => $tax_values ) {
						wp_set_object_terms( $return->ID, array_values($tax_values) ,$tax);
					}
				}
			# Poster .
				if( isset( $data['poster'] ) ){
					$UploadPhoto = $this->UploadPhoto($data['poster']);
					set_post_thumbnail($return->ID,$UploadPhoto);
				}
			return $return;
		}
	
	# INSERT TERM .
		public function InsertTerm($term){
			if(!isset($term['taxonomy'])){
				return false;
				//echo "<pre>"; print_r($term); echo "</pre>";die();
			}

			# Insert Options
				$ImportantInsert = false;
				$InsertStatus = 'edit';

			# Check .
				$term_exists = $this->ParentPageSearch($term['search'],'termmeta',$term['metakey_search']);
				if($term_exists != false){
					$return = get_term_by('id',$term_exists->term_id,$term['taxonomy']);
					if( isset( $return->term_id ) ) $ImportantInsert = true;
				}

			# Insert .
				if($ImportantInsert == false){
					$InsertStatus = 'new';
					if( isset( $term['Arguments'] ) && !empty( $term['Arguments'] ) ){
						$insert = wp_insert_term($term['name'],$term['taxonomy'],$term['Arguments']);
					}else{
						$insert = wp_insert_term($term['name'],$term['taxonomy']);
					}

					if(is_wp_error($insert)){

						echo PHP_EOL;
							print_r($insert);	
						echo PHP_EOL;
							echo $insert->get_error_message();
						echo PHP_EOL;
							print_r($insert);
						echo PHP_EOL;
							print_r($term);	
						die();
						
					}

					$return = get_term_by('id',$insert['term_id'],$term['taxonomy']);

					# Update SearchValue .
					update_term_meta($return->term_id,$term['metakey_search'],$term['search']);
				}

			# SAVE TAXONOMY	
				$this->TaxonomesObjects[$term['taxonomy']][$term['search']] = $return;

			# Update Object .
				if(isset($term['ImportantUpdate'])){
					if( !isset( $term['Arguments'] ) ) $term['Arguments'] = array();

					$term['Arguments']['name'] = $term['name'];
					$update = wp_update_term( $return->term_id, $return->taxonomy , $term['Arguments'] );
				}

			# Term Meta .
				if(isset($term['term_meta'])){
					foreach ($term['term_meta'] as $metakey => $metavalue) {
						update_term_meta($return->term_id,$metakey,$metavalue);
					}
				}

			# Poster .
				if(isset($term['poster']) && empty(get_term_meta($return->term_id,'poster',true))){
					$UploadPhoto = $this->UploadPhoto($term['poster']);
					update_term_meta($return->term_id,'poster',wp_get_attachment_url($UploadPhoto));
					update_term_meta($return->term_id,'poster_id',$UploadPhoto);
				}
			return $return;
		}



	# ON POST TRASH OR POST DRAFT PARENT PAGE REMOVED .
		public function Ontrashpost($pid) {
			$Fields = array();
			foreach ($Fields as $metakey) {
				$postmeta = get_post_meta($pid, $metakey, true);
				if( !empty($postmeta) ) {
					update_post_meta($pid, $metakey.'__backup', $postmeta);
					delete_post_meta($pid, $metakey);
				}
			}
		}
		
		public function Onpublishpost($post_id) {
			$Fields = array();
			$postObject = get_post($post_id);
			//
			foreach ($Fields as $metakey) {
				$postmeta__backup = get_post_meta($post_id, $metakey.'__backup', true);
				if( !empty( $postmeta__backup ) and $postObject->post_status == 'publish' ) {
				  	update_post_meta($post_id, $metakey, $postmeta__backup);
					delete_post_meta($post_id, $metakey.'__backup');
				}
			}
		}

		public function Setup(){
			# Remove Searching Post Meta In Remove Action
				add_action( 'wp_trash_post', array($this,'Ontrashpost') );
				add_action('publish_post', array($this,'Onpublishpost') );

		}
}
(new YourColor__import__insert)->Setup();