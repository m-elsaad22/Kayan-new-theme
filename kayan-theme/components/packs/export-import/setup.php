<?php class get_YourColorTheme__XML{
	function __construct($args=array()) {
		# Var
			$this->ThemeStatic = new ThemeStatic;

			# DIRECTORY PATH
				$this->CurrentPath = trailingslashit( dirname( __FILE__ ) );

			# DIRECTORY URL
				$this->CurrentURL = explode(get_template_directory(), trailingslashit( dirname( __FILE__ ) ))[1];
				$this->CurrentURL = get_template_directory_uri().$this->CurrentURL;

			# STYLE URL	
				$this->StyleURL = $this->CurrentURL.'/css/';

			# FIELDS EXTRACT.	
				$this->ConfigrationActions = $this->CurrentPath.'Configration-Actions/';
				$this->ConfigrationActions__Packages = glob( $this->ConfigrationActions.'*.php' );

		# PAGE SETUP
			$this->PagesSetup = array(
				'get_YourColorTheme__XML'=>array(
					'title'=>'استيراد وتصدير',
					'pageTitle'=>'استيراد وتصدير',
					'menuicon'=>'dashicons-editor-ul',
					'disc'=>'اداة استيراد وتصدير ورشة لونك',
					'sluged'=>'get_YourColorTheme__XML',
					'url'=> 'admin.php?page=get_YourColorTheme__XML',
					'LoardIcon'=>'uukerzzv',
					'template'=>'export',
					'SubPages'=>array(
						'export'=>array(
							'title'=>'تصدير',
							'sluged'=>'export',
							'url'=> 'admin.php?page=export',
							'LoardIcon'=>'puvaffet',
							'template'=>'export',
							'HeaderMenu'=>true,
							'color'=>'#ff5722',
						),
						'import'=>array(
							'title'=>'إستيراد',
							'sluged'=>'import',
							'url'=> 'admin.php?page=import',
							'LoardIcon'=>'puvaffet',
							'template'=>'import',
							'HeaderMenu'=>true,
							'color'=>'#ff5722',
						)
					),
				),
			);

			$this->SubMenusMap = array();
			foreach ($this->PagesSetup as $s => $v) {
				if(isset($v['SubPages'])){
					$v['SubPages'] = (is_array($v['SubPages'])) ? $v['SubPages'] : array();
					foreach ($v['SubPages'] as $e => $k) {
						$this->SubMenusMap[$k['sluged']] = $v['sluged'];
					}
				}
			}

		$this->Styles = array("{$this->StyleURL}export-import-style.css");
		$this->Scripts = array();
	}	

	# CLASS TOOLS

		public function insertArray($dom, $element, $key, $value){
		    if (is_array($value)) {
    	        if (is_numeric($key)) {
		          $key = 'item';
		        }
		        $child = $dom->createElement(htmlspecialchars($key));
		        $element->appendChild($child);
		        foreach ($value as $subKey => $subValue) {
		            $this->insertArray($dom, $child, $subKey, $subValue);
		        }
		    } else {
    	        if (is_numeric($key)) {
		          $key = 'item';
		        }
		        $child = $dom->createElement(htmlspecialchars($key));
		        $child->appendChild($dom->createTextNode(htmlspecialchars($value)));
		        $element->appendChild($child);
		    }
		}

		public function Require($path, $vars=array()) {
			extract($vars);
			if( file_exists($path) ) {
				require($path);
			}else {
				echo '<p><strong>هذا المسار غير موجود :</strong>'.$path.'</p>';
			}
		}

		public function TitlePermaLink($data){
			$Retuner['title'] = $this->YcPageTitles($data);
			$Retuner['url'] = $this->YcPageUrl($data);
			return $Retuner;
		}
		public function YcPageTitles($data){
			$Title = '<em>'.$data['PageSetup']['title'].'</em><span>'.((isset($data['PageSetup']['disc'])) ? $data['PageSetup']['disc'] : '').'</span>';
			return $Title;
		}
		public function YcPageUrl($data){
			$Permalink = admin_url($data['PageSetup']['url']);
			return $Permalink; 
		}
		public function FormsPagesContext() { 
			$SendData = array();
			$SendData['PagesTaps'] = $this->PagesSetup;
			# PAGE TYPE
			$PageType = $_GET['page'];
			$SendData['PageType'] = $PageType;
			// # RESUME PAGE 
			$ResumePage = false;
			if(isset($this->PagesSetup[$PageType])){
				$ResumePage = true;
				$ActivePage = $this->PagesSetup[$PageType];
				$SendData['PagesTaps'] = $this->PagesSetup[$PageType];
			}else if(isset($this->SubMenusMap[$PageType])){
				if(isset($this->PagesSetup[$this->SubMenusMap[$PageType]])){
					$ResumePage = true;
					$ActivePage = $this->PagesSetup[$this->SubMenusMap[$PageType]]['SubPages'][$PageType];
					$SendData['PagesTaps'] = $this->PagesSetup[$this->SubMenusMap[$PageType]];
				}
			}

			if($ResumePage == true){
				$permalinkPage = admin_url($ActivePage['url']);
				$SendData['PageSetup'] = $ActivePage;
				$SendData['PageData'] = $this->TitlePermaLink($SendData);
				$SendData['Styles'] = $this->Styles;
				$SendData['Scripts'] = $this->Scripts;
				# Option Page
				$BladeName = $ActivePage['template'];
				//
				$SendData['BladeName'] = $PageType;
				echo '<yourcolorapi--conatiner>';
					$this->Require($this->CurrentPath.'Parts/'.$BladeName.'.php', $SendData);
				echo '</yourcolorapi--conatiner>';
			}else{
				wp_redirect($this->ThemeStatic->GetCurrentURL());
			}
			include( ABSPATH . 'wp-admin/admin-footer.php' );
			exit();
		}

	# SETUP FUNCTION .
		public function FormsSetupPage() {
			foreach ($this->PagesSetup as $pagename => $pagedata) {
				add_menu_page($pagedata['title'],$pagedata['title'], 'administrator',$pagedata['sluged'], array($this, 'FormsPagesContext'), $pagedata['menuicon']);
				if(isset($pagedata['SubPages'])){
					foreach ($pagedata['SubPages'] as $subname => $subdata) {
						add_submenu_page($pagedata['sluged'],$subdata['title'],$subdata['title'], 'administrator',$subdata['sluged'], array($this,'FormsPagesContext'));
					}
				}
			}
		}

		public function QueryEndpoint() {
			if(!empty($this->PagesSetup)){
				foreach ($this->PagesSetup as $var => $data) {
					add_rewrite_endpoint( $var , EP_ROOT );
				}
			}
			//
			if(!empty($this->SubMenusMap)){
				foreach ($this->SubMenusMap as $var => $data) {
					add_rewrite_endpoint( $var , EP_ROOT );
				}
			}

		}


	public function Setup() {
		add_action('admin_menu', array($this, 'FormsSetupPage'));
		add_action( 'init', array( $this, 'QueryEndpoint'));

		# CUSTOM OPTIONS			
			foreach ( is_array( $this->ConfigrationActions__Packages ) ? $this->ConfigrationActions__Packages : array() as $files ) {
				$this->Require( $files, array( 'CurrentDir'=> $files ) );
			}	

	}
}
(new get_YourColorTheme__XML)->Setup();