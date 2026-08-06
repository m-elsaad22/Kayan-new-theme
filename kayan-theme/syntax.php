<?php 
class ThemeStatic extends ThemeTree {
	public function Locate() {
	
		if( is_feed() ) return ;
		if( !is_admin() ) {
			$currentURL = $this->GetCurrentURL();

			if( isset($_GET['page']) ) {
				$currentURL = str_replace(array('/?page=', '?page='), '/page/', $currentURL);
				wp_redirect($currentURL);
				die();
			}
			if( isset($_GET['s']) ) {
				$currentURL = str_replace(array('/?s=', '?s='), '/search/', $currentURL);
				wp_redirect($currentURL);
				die();
			}

		}
		wp_reset_query();
		global $post;
		if( is_home() ) {
			do_action('BeforeBlade_index');
			$this->Blade('index');
			do_action('AfterBlade_index');
		}else if( is_author() ) {
			do_action('BeforeBlade_author');
			$this->Blade('author');
			do_action('AfterBlade_author');
		}else if( is_archive() or is_tag() or is_category() or is_tax() ) {
			do_action('BeforeBlade_archive');
			$obj = get_queried_object();
			$this->Blade('archive', $obj);
			do_action('AfterBlade_archive');
		}else if( is_single() ) {
			if( get_post_type() == 'post' ) {
				$category = get_the_terms($post->ID, 'category', '');
				$catTerms = array();
				foreach( $category as $term ) {
					$termviws = (int)get_term_meta($term->term_id,'views',1);
					update_term_meta($term->term_id,'views',$termviws+1);
					$catTerms[] = $term->term_id;
				}


				$trending = (INT) get_post_meta($post->ID, 'trending', true);
				if( get_post_meta($post->ID, 'last_update', true) != date('d-m-Y') ) {
					$trending = 0;
				}
				update_post_meta($post->ID, "trending", (INT) $trending + 1);
				update_post_meta($post->ID, "last_update", date('d-m-Y'));

			}
			do_action('BeforeBlade_single');
			$this->Blade('single', $post);
			do_action('AfterBlade_single');
		}else if( is_search() ) {
			do_action('BeforeBlade_search');
			$this->Blade('search');
			do_action('AfterBlade_search');
		}else if( is_page() ) {
			do_action('BeforeBlade_page');
			$this->Blade('page');
			do_action('AfterBlade_page');
		}else if( is_404() ) {
			do_action('BeforeBlade_404');
			$this->Blade('404');
			do_action('AfterBlade_404');
		}
		die();
	}
	public function Part($part, $vars=array()) {
		$part_hook = ucfirst(strtolower($part));
		do_action('Before'.$part_hook.'');
		$packs = $this->Packages;
		foreach ($packs as $pack) {
			if( basename($pack) == '#'.$part ) {
				$path = $pack.'part.php';
				$CurrentURL = str_replace(get_template_directory(), get_template_directory_uri(), $pack);
				$CurrentURL = str_replace('#', urlencode('#'), $CurrentURL);
				$this->Require($path, array_merge(array('CurrentDir'=>$pack, 'CurrentURL'=>$CurrentURL), $vars));
			}
		}
		do_action('After'.$part_hook);
	}
	public function Blade($page, $obj=false, $fname='shape') {
		$packs = $this->Packages;
		$vars = array();
		if( $obj != false and isset($obj->taxonomy) ) {
			$pagecheck = 'archive-'.$obj->taxonomy;
			if( is_dir($this->packsPath.$pagecheck) ) {
				$page = $pagecheck;
			}
			$vars = array('obj'=>$obj);
		}else if( $obj != false and isset($obj->ID) ) {
			$vars = array('post'=>$obj);
		}else if( $obj != false ) {
			$vars = $obj;
		}
		foreach ($packs as $pack) {
			if( basename($pack) == '@'.$page ) {
				$filename = $fname;
				if( $obj != false and isset($obj->ID) ) {
					if( file_exists($pack.$obj->post_type.'.php') ) {
						$filename = $obj->post_type;
					}
				}
				$path = $pack.$filename.'.php';
				# ═══ v1.3.0: fallback لقالب post.php لأي نوع منشور بلا قالب مخصص داخل @single ═══
				# يمنع الصفحة الفارغة للأنواع الجديدة (reviews/faqs/pricing/portfolio/before_after)
				if( '@single' === basename($pack) && ! file_exists($path) && file_exists($pack.'post.php') ) {
					$path = $pack.'post.php';
				}
				$CurrentURL = str_replace(get_template_directory(), get_template_directory_uri(), $pack);
				$CurrentURL = str_replace('#', urlencode('#'), $CurrentURL);
				$this->Require($path, array_merge(array('CurrentDir'=>$pack, 'CurrentURL'=>$CurrentURL), $vars));
			}
		}
	}
	public function RequestVars($vars=[]) {
		foreach ($vars as $var => $val) {
			if(isset ( $vars[$var] ) && empty ( $vars[$var] )){
		        $vars[$var] = '';
		    }
		}
	    return $vars;
	}
	public function Tree() {
		add_action( 'template_redirect', array($this, 'Locate') );
		add_action( 'request', array($this, 'RequestVars') );
	}

	// A little bit Hooks
	public function CompareUSort($a, $b) {   
		if ($a == $b) {
			return 0;
		}   
		return ($a > $b) ? -1 : 1; 
	}
	public function TryToLogin($userlogin, $password, $remember=true) {
		$user_login     = esc_attr($userlogin);
		$user_password     = $password;

		$creds = array();
		$creds['user_login'] = $user_login;
		$creds['user_password'] = $user_password;
		$creds['remember'] = $remember;

		$user = wp_signon( $creds, false );
		if( !is_wp_error($user) ) {
			$userID = $user->ID;
			wp_set_current_user( $userID, $user_login );
			wp_set_auth_cookie( $userID, true, false );
		}

		return $user;
	}
	public function GetFileSize( $url ) {
	  // Assume failure.
	  $result = -1;

	  $curl = curl_init( $url );

	  // Issue a HEAD request and follow any redirects.
	  curl_setopt( $curl, CURLOPT_NOBODY, true );
	  curl_setopt( $curl, CURLOPT_HEADER, true );
	  curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	  curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
	  curl_setopt( $curl, CURLOPT_USERAGENT, get_user_agent_string() );

	  $data = curl_exec( $curl );
	  curl_close( $curl );

	  if( $data ) {
	    $content_length = "unknown";
	    $status = "unknown";

	    if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
	      $status = (int)$matches[1];
	    }

	    if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) {
	      $content_length = (int)$matches[1];
	    }

	    // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
	    if( $status == 200 || ($status > 300 && $status <= 308) ) {
	      $result = $content_length;
	    }
	  }

	  return $result;
	}
	public function get_terms($args) {
		$terms = get_terms($args);
		return $terms;
	}
	public function get_posts($args) {
		$posts = get_posts($args);
		return $posts;
	}
	public function premium_file_get_contents($url, $find=false, $times=0, $appendurl='', $removeappend='', $headers=array()) {
		# مفتاح scrapestack يُقرأ من خيار في لوحة التحكم — لا مفاتيح داخل الكود (v1.2.0)
		$access_key = get_option( 'yc_scrapestack_key' );
		if ( empty( $access_key ) ) {
			return false;
		}
		$queryString = http_build_query( array(
			'access_key' => $access_key,
			'url'        => $url.$appendurl,
		) );
		$response = wp_remote_get( 'https://api.scrapestack.com/scrape?'.$queryString, array( 'timeout' => 30, 'headers' => $headers ) );
		if ( is_wp_error( $response ) ) {
			return false;
		}
		$content = wp_remote_retrieve_body( $response );
		if ( '' === $content ) {
			return false;
		}
		if ( $find !== false && strpos( $content, $find ) === false && $times < 2 ) {
			$times++;
			return $this->premium_file_get_contents( $url, $find, $times, '', $appendurl );
		}
		return $content;
	}
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
	public function GetCurrentURL() {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		return $pageURL;
	}
	public function prepareFilename($str) {
		$str = strip_tags($str); 
	    $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
	    $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
	    $str = strtolower($str);
	    $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
	    $str = htmlentities($str, ENT_QUOTES, "utf-8");
	    $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
	    $str = str_replace(' ', '-', $str);
	    $str = rawurlencode($str);
	    $str = str_replace('%', '-', $str);
	    return $str;
	}
	public function get_home_path() {
	    $home    = set_url_scheme( get_option( 'home' ), 'http' );
	    $siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );
	    if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
	        $wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
	        $pos                 = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
	        $home_path           = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
	        $home_path           = trailingslashit( $home_path );
	    } else {
	        $home_path = ABSPATH;
	    }
	 
	    return str_replace( '\\', '/', $home_path );
	}
	public function Paged() {
		if ( get_query_var('paged') ) { $paged = get_query_var('paged'); } else if ( get_query_var('page') ) {$paged = get_query_var('page'); } else {$paged = 1; }
		return $paged;
	}
	public function cleanURL($url) {
		$url = preg_replace('/([^:])(\/{2,})/', '$1/', $url);
		return $url;
	}
	public function UploadImageCheck($url){
		$DBArguments = new DBArguments;
		$FindImage = $DBArguments->get(array('table'=>'wp_postmeta','where'=>array('meta_key'=>'ImageParentPage','meta_value'=>$url)),1);
		if($FindImage != false && isset($FindImage[0])){
			return $FindImage[0]->post_id;
		}
		return false;
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
		    $filename = str_replace('.jpg', rand().'.jpg', basename($image_url));
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
	public function Slugify($text) {
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
		return 'n-a';
		}

		return $text;
	}
	public function Pagination($wp_query, $wp_rewrite){
		echo '<div class="pagination">';
			$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;

			$pagination = array(
			    'base' => @add_query_arg('page','%#%'),
			    'format' => '',
			    'total' => $wp_query->max_num_pages,
			    'current' => $current,
			    'show_all' => false,
			    'type' => 'list',
			    'prev_text' => '<i class="fa-solid fa-arrow-right"></i>',
			    'next_text' => '<i class="fa-solid fa-arrow-left"></i>'
		    );

			if( $wp_rewrite->using_permalinks() )
			    $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 'page', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );

			if( !empty($wp_query->query_vars['s']) and !is_search() )
			    $pagination['add_args'] = array( 's' => get_query_var( 's' ) );

			echo paginate_links( $pagination );
		echo '</div>';
	}
}
(new ThemeStatic)->Tree();

add_action( 'request', function ($vars=[]){
	global $wp_rewrite;
	#if( isset( $vars['paged'] ) && !isset( $vars['page'] ) ) $vars['page'] = $vars['paged'];
	return $vars;
});