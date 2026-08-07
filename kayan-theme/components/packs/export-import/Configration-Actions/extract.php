<?php  class Extract__data extends get_YourColorTheme__XML{
 	
 	function __construct($argums=array()){
 		
 	}

 	# CLASS TOOLS .

		public function CheckSerialize($str){
			$data = @unserialize($str);
			if ($str === 'b:0;' || $data !== false) {
			    return true;
			}
			return false;	
		}

		public function API_ExtractValues($data){
		  	$TaxonomyesObject = array();
		  	foreach ($this->API_TaxonomyesObject() as $tkey => $tmeky) {
		    	$TaxonomyesObject[$tkey] = $tmeky->label;
		  	}
		  	return $TaxonomyesObject;
		}

		public function API_TaxonomyesObject($data=array(),$values=false){
		  	$args = array(
		    	'public'   => true,  
		  	); 
		  	if(isset($data['output'])){
		    	$output = $data['output']; // or objects
		  	}else{
		    	$output = 'objects'; // or objects
		  	}
		  	if(isset($data['operator'])){
		    	$operator = $data['operator']; // or objects
		  	}else{
		    	$operator = 'and'; // or objects
		  	}
		  	$Ret = array();
		  	if(!isset($data['excloded'])) $data['excloded'] = array('post_format');

		  	$taxonomies = get_taxonomies( $args, $output, $operator ); 
		  	if ( $taxonomies ) {
		    	foreach ( $taxonomies  as $taxonomy ) {
			      	if(!in_array($taxonomy->name,$data['excloded'])){
			        	$Ret[$taxonomy->name] = $taxonomy;
			      	}
		    	}
		  	}
		  	if($values != false){
		    	$Ret = $this->API_ExtractValues($Ret);
		  	}
		  	return $Ret;
		}

	# USER FETCH .

		# GET TOTAL USER META .
			public function FeatchUserMeta( $user ){

				$Return = array();
				$TermMeta = get_user_meta($user->ID);
				foreach ($TermMeta as $metakey => $metavalue) {
					$metavalue[0] = ( ( $this->CheckSerialize($metavalue[0]) != false) ) ? maybe_unserialize($metavalue[0]) : $metavalue[0];
					//
					if(!empty($metavalue[0])){
						$Return[$metakey] = $metavalue[0];			
					}
				}
				return $Return;
			}	

		# FEATCH USER .		
			public function FeatchUsers( $vars ){
				/*
					$user => your $user , (OBJECT)
					$user_id => your $user->ID , (OBJECT)
					$get__single__fields => send your custom fields, (ARRAY) 
					$exclode__fields => exclode get your fields, (ARRAY)

				*/				
				extract( $vars );

				# FOUND TERM OBJECT .
					if( isset( $user_id ) && !isset( $user ) ) $user = get_userdata( $user_id );

					if( !isset( $user->ID ) ) return false;


				if( !isset( $get__single__fields ) ) $get__single__fields = array();
				$get__single__fields = ( ( is_array( $get__single__fields ) ) ) ? $get__single__fields : array();
				if( empty( $get__single__fields ) ) $get__single__fields[] = 'all';


				if( !isset( $exclode__fields ) ) $exclode__fields = array();
				$exclode__fields = ( ( is_array( $exclode__fields ) ) ) ? $exclode__fields : array();
				

				$__return_json = array(
					'search'=>$user->user_email,
					'metakey_search'=>'user__parentpage',
				);


					# USER OBJECT FIELDS .

		    		# USER FIELD 'user_login'
		    			if( !in_array( 'user_login',$exclode__fields ) && ( in_array( 'user_login',$get__single__fields) || in_array('all', $get__single__fields) ) ) $__return_json['Arguments']['user_login'] = $user->user_login;
		    		# USER FIELD 'user_pass' .
		    			if( !in_array( 'user_pass',$exclode__fields ) && ( in_array( 'user_pass',$get__single__fields) || in_array('all', $get__single__fields) ) ) $__return_json['Arguments']['user_pass'] = 'masrmix100**900**';
		    		# USER FIELD 'user_nicename' .
		    			if( !in_array( 'user_nicename',$exclode__fields ) && ( in_array( 'user_nicename',$get__single__fields) || in_array('all', $get__single__fields) ) ) $__return_json['Arguments']['user_nicename'] = $user->user_nicename;
		    		# USER FIELD 'user_email' .
		    			if( !in_array( 'user_email',$exclode__fields ) && ( in_array( 'user_email',$get__single__fields) || in_array('all', $get__single__fields) ) ) $__return_json['Arguments']['user_email'] = $user->user_email;
		    		# USER FIELD 'user_url' .
		    			if( !in_array( 'user_url',$exclode__fields ) && ( in_array( 'user_url',$get__single__fields) || in_array('all', $get__single__fields) ) ) $__return_json['Arguments']['user_url'] = $user->user_url;
		    		# USER FIELD 'user_status' .
		    			if( !in_array( 'user_status',$exclode__fields ) && ( in_array( 'user_status',$get__single__fields) || in_array('all', $get__single__fields) ) ) $__return_json['Arguments']['user_status'] = $user->user_status;
		    		# USER FIELD 'display_name' .
		    			if( !in_array( 'display_name',$exclode__fields ) && ( in_array( 'display_name',$get__single__fields) || in_array('all', $get__single__fields) ) ) $__return_json['Arguments']['display_name'] = $user->display_name;

		  		# USER LINK .
						if( !in_array( 'author__url',$exclode__fields ) && ( in_array( 'author__url',$get__single__fields) || in_array('all', $get__single__fields) ) ) $__return_json['last__author__url'] = get_author_posts_url($user->ID);

				# USER_META .
					if( in_array('all', $get__single__fields) ){
						$fix__user__meta = array( 
							'nickname',
							'first_name',
							'last_name',
							'rich_editing',
							'syntax_highlighting',
							'comment_shortcuts',
							'admin_color',
							'show_admin_bar_front',
							'adsense',
							'ganalytics',
							'meta-box-order_post',
							'screen_layout_post',
							'closedpostboxes_post',
							'metaboxhidden_post'
						);
						$user_meta = $this->FeatchUserMeta($user);
						foreach ($user_meta as $s => $v) {
							if( in_array( $s, $fix__user__meta ) ) $__return_json['user_meta'][$s] = $v;			
						}

					}else if( isset( $get__single__fields['user_meta'] ) ){

						$get__single__fields['user_meta'] = ( ( is_array( $get__single__fields['user_meta'] ) ) ) ? $get__single__fields['user_meta'] : array();

						foreach ( $get__single__fields['user_meta'] as $meta__key ) {
							$user__meta = get_user_meta( $user->ID, $meta__key, true);
							if( !empty( $user__meta ) ){
								$__return_json['user_meta'][ $meta__key ] = $v;
							}
						}
					}
				
				return $__return_json;
			}

	# TERMS .

		# TERM META .
			public function FeatchTermMeta($term){
				$Return = array();
				$TermMeta = get_term_meta($term->term_id);
				foreach ($TermMeta as $metakey => $metavalue) {
					$metavalue[0] = (($this->CheckSerialize($metavalue[0]) != false)) ? maybe_unserialize($metavalue[0]) : $metavalue[0];
					//
					if(!empty($metavalue[0])){
						$Return[$metakey] = $metavalue[0];			
					}
				}
				return $Return;
			}

		# FEATCH SINGLE TERM .	
			public function FeatchTaxonomy($term){
				$Return = array(
					'taxonomy'=>$term->taxonomy,
					'search'=> $term->term_id,
					'metakey_search'=>'taxonmoy__'.$term->taxonomy.'_parentpage',
					'name'=>$term->name,
				);
				$Return['term_meta']['last_term_link'] = get_term_link($term);
				// ## TERM META ..
					$term_meta = $this->FeatchTermMeta($term);
					foreach ($term_meta as $s => $v) {
						if( $s != 'taxonmoy__'.$term->taxonomy.'_parentpage' ){
							$Return['term_meta'][$s] = $v;
						}
					}
				return $Return;
			}

	# POSTS .

		# POST META .
			public function FeatchPostMeta($post){
				$Return = array();
				$PostMeta = get_post_meta($post->ID);
				$ExclodMetakeys = array('_thumbnail_id','_edit_lock','_edit_last','lastfieldsopts','_wp_old_date');
				foreach ($PostMeta as $metakey => $metavalue) {
					$metavalue[0] = (($this->CheckSerialize($metavalue[0]) != false)) ? maybe_unserialize($metavalue[0]) : $metavalue[0];
					//
					if(!empty($metavalue[0]) && !in_array($metakey, $ExclodMetakeys) ){
						$Return[$metakey] = $metavalue[0];
					}
				}
				return $Return;
			}

		# FEATCH POST .	
			public function FeatchPost($post,$shows__in=array()){

				$Return = array();
				$Return['search'] = $post->ID;
				$Return['metakey_search'] = 'posts__secrape__parentid';
				$Return['postArguments']['post_title'] = $post->post_title;
				$Return['postArguments']['post_content'] = $post->post_content;
				$Return['postArguments']['post_date'] = $post->post_date;
				$Return['postArguments']['post_type'] = $post->post_type;
				$Return['post_meta']['last_post_url'] = get_the_permalink( $post->ID );

				# POST AUTHOR .
					if( empty( $shows__in ) || ( !empty( $shows__in ) && in_array( 'author', $shows__in ) ) ){
						$Return['InsertAuthor'] = $this->FeatchUsers(array('user_id'=>$post->post_author));
					}

				# THUMBNAIL .
					if( empty( $shows__in ) || ( !empty( $shows__in ) && in_array( 'poster', $shows__in ) ) ){
						$poster = get_the_post_thumbnail_url($post->ID);
						if( !empty( $poster ) ){
							$Return['poster'] = get_the_post_thumbnail_url($post->ID);
						}
					}


				# POST_META .
					if( empty( $shows__in ) || ( !empty( $shows__in ) && in_array( 'post_meta', $shows__in ) ) ){
						$post_meta = $this->FeatchPostMeta($post);
						foreach ($post_meta as $s => $v) {
							if( $s != 'posts__secrape__parentid' ){
								$Return['post_meta'][$s] = $v;
							}
						}
					}

				# TAXONOMY .
					if( empty( $shows__in ) || ( !empty( $shows__in ) && in_array( 'taxonomies', $shows__in ) ) ){
						$TaxonomyesObject = $this->API_TaxonomyesObject();
						foreach ( $TaxonomyesObject as $taxonomy => $ob_tax ) {
							$Terms = (is_array(get_the_terms($post->ID,$taxonomy,true))) ? get_the_terms($post->ID,$taxonomy,true) : array();
							if(!empty($Terms)){
								foreach ($Terms as $termo) {
									$Return['InsertTaxonomy'][$taxonomy][$termo->term_id] = $this->FeatchTaxonomy($termo);
								}
							}
						}
					}

				return $Return;	
			}

	# 		
 	public function ExtractXML($data){
 		global $YC__CFM__global_setup_fields;
 		extract( $data );

 		$Return = array();

		if( isset( $Theme__options__pages ) ){
			foreach ( $Theme__options__pages as $pages ) {
				if( isset( $YC__CFM__global_setup_fields['ThemeOptions'] ) && isset( $YC__CFM__global_setup_fields['ThemeOptions'][ $pages ] ) && isset( $YC__CFM__global_setup_fields['ThemeOptions'][ $pages ]['fields'] ) ){

					foreach ( $YC__CFM__global_setup_fields['ThemeOptions'][ $pages ]['fields'] as $fieldArgument ) {
						$Values = yc_get_option( $fieldArgument['id'] );
						if( !empty( $Values ) ){
							$Return['ThemeOptions'][ $fieldArgument['id'] ] = array('FieldType'=>$fieldArgument['type'],'Value'=>$Values);
							if( $fieldArgument['type'] == 'Widgets' ){
								foreach ( $Values as $widget_post__id) {
									$widgetPost = get_post( $widget_post__id['widget_post__id'] );
									if( isset( $widgetPost->ID ) ){
										$Return['ThemeOptions'][ $fieldArgument['id'] ]['widgets__posts'][ $widgetPost->ID ] = $this->FeatchPost( $widgetPost,array( 'post_meta' ) );
									}
								}
							}

						}

					}
				}
			}
		}

		if( isset( $taxonomies__types ) ){
			foreach( $taxonomies__types as $taxonomy ){
				$argument__taxonomy = get_terms( array( 'taxonomy'=>$taxonomy,'hide_empty'=>0 ) );
				foreach ( $argument__taxonomy as $term ) {
					$Return['Taxonomies'][$taxonomy][] = $this->FeatchTaxonomy($term);
				}


			}
		}

		if( isset( $post__types ) ){
			foreach( $post__types as $post_type ){
				$argument__posts = get_posts( array( 'post_type'=>$post_type,'posts_per_page'=>-1 ) );
				foreach ( $argument__posts as $post_object ) {
					$Return['PostTypes'][$post_type][] = $this->FeatchPost($post_object);
				}
			}
		}


		return $Return;

 	}
}