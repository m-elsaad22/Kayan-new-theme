<?php  /**
* 
* 
*/
class YC__WidgetsMachine {
	
	function __construct( $args=array() ){
		
		# PLUGIN PATH.
			$this->YC__WidgetsMachine_Path = trailingslashit( dirname( __FILE__ ) );

		# PLUGIN URL.
			$this->YC__WidgetsMachine_URL = explode(get_template_directory(), trailingslashit( dirname( __FILE__ ) ))[1];
			$this->YC__WidgetsMachine_URL = get_template_directory_uri().$this->YC__WidgetsMachine_URL;

		# FIELDS FOLDERS .	
			$this->widgets_folders__List_path = $this->YC__WidgetsMachine_Path.'model-widgets/*/';
			$this->widgets_folders__Packages = array_filter( glob( $this->widgets_folders__List_path ),'is_dir');

		# FIELDS EXTRACT.	
			$this->widgets_files__List_path = $this->YC__WidgetsMachine_Path.'model-widgets/*/*';
			$this->widgets_files__Packages = array_filter( glob( $this->widgets_files__List_path ));

		# SELECTOR EXTRACT .
			$this->selector_folders__List_path = $this->YC__WidgetsMachine_Path.'model-selector/*/';
			$this->widgets_folders__Packages = array_filter( glob( $this->selector_folders__List_path ),'is_dir');

		# SELECTOR FILES EXTRACT.	
			$this->selector_files__List_path = $this->YC__WidgetsMachine_Path.'model-selector/*/*';
			$this->selector_files__Packages = array_filter( glob( $this->selector_files__List_path ));

		# REQUIRE SETUP FILES .	
			$this->require__setup = array_merge($this->widgets_files__Packages,$this->selector_files__Packages);
	}

	# EXTRACT WIDGETS ENQUEUES
		public function widgets__Enqueues($Widgets_data){

			$Styles = array();
			if( !empty( $Widgets_data ) ){

				$Path_style__Widgets = (new ThemeTree)->StylesPath.'YourColor__Widgets';

				$StyleFields__Widgets = array();

				foreach( glob($Path_style__Widgets.'/*.css') as $cs__file ) {
					$current_file_name = explode('YourColor__Widgets/', $cs__file)[1];
					$current_file_name = explode('/', $current_file_name)[0];
					$current_file_name = explode('.css', $current_file_name)[0];
					$StyleFields__Widgets[] = $current_file_name;
				}
				
				# Style FILES.
				foreach ( $Widgets_data as $s => $v) {
					if( in_array( $v['widget_id'] ,$StyleFields__Widgets ) ){
						$Styles[$v['widget_id']] = 'YourColor__Widgets/'.$v['widget_id'].'.css';
					}			
				}
			}
			return $Styles;
		}

	# EXTRACT WIDGETS ENQUEUES
		public function get__widgets__list($Widgets_data){

			$Return = array();
			if( !empty( $Widgets_data ) ){
				foreach ( $Widgets_data as $s => $v) {
					$Return[$v['widget_id']] = $v['widget_id'];
				}
			}
			return $Return;
		}



	# WIDGETS UI TOOLS .

		public function widgets___UI($vars){
			extract($vars);
			/*
				# $Widgets_data -> CURERENT WIDGET VALUE . ARRAY() *REQUIRE*
				# $WidgetID -> CURRENT WIDGET SELECTED . STRING *REQUIRE*
				# $Parent__section__class -> SEND CUSTOM PARENT SECTIONS CLASS NAME
				# $Single__section__class -> SEND CUSTOM SINGLE WIDGET CLASS NAME
				# $section_InnerRow_class -> SEND CUSTOM SINGLE WIDGET INNER ROW CLASS NAME
				# $top_separator -> SEND TOP SEPARATOR FILE NAME .
				# $bottom_separator -> SEND BOTTOM SEPARATOR FILE NAME .
			*/

			if( !isset( $Parent__section__class ) ) $Parent__section__class = '-YourColor-Widgets-Sections';
			if( !isset( $Single__section__class ) ) $Single__section__class = '-YourColor-SingleWidget-Section';
			if( !isset( $section_InnerRow_class ) ) $section_InnerRow_class = '-YC-Widgets-Inner-Row';
			
			if( !isset( $top_separator ) ) $top_separator = 'defult__top_separator';
			if( !isset( $bottom_separator ) ) $bottom_separator = 'defult__bottom_separator';


			echo '<div class="'.$Parent__section__class.' -YC-WidgetID-'.$WidgetID.'">';
				
				# EACH WIDGETS .
					foreach ( $Widgets_data as $k => $single__widget ) {
						if( isset( $single__widget['widget_post__id'] ) ){
							$widget_post_meta = ( is_array( get_post_meta( $single__widget['widget_post__id'], 'widget_post_meta',true ) ) ) ? get_post_meta( $single__widget['widget_post__id'], 'widget_post_meta',true ) : array();
							if( !empty( $widget_post_meta ) ){
								$single__widget = array_merge($single__widget,$widget_post_meta);
								$P_clas = '';
								if( isset( $single__widget['show_top_separator'] ) && $single__widget['show_top_separator'] == 'on' ) $P_clas .= ' -Top-separator-shows__in';
								if( isset( $single__widget['show_bottom_separator'] ) && $single__widget['show_bottom_separator'] == 'on' ) $P_clas .= ' -bottom-separator-shows__in';

								$hide_switch = wp_is_mobile() ? 
								    (isset($single__widget['mobile_hide_section__switch']) ? $single__widget['mobile_hide_section__switch'] : null) :
								    (isset($single__widget['hide_section__switch']) ? $single__widget['hide_section__switch'] : null);
								if( !isset(  $hide_switch ) ){
									echo '<div class="'.$Single__section__class.' -YC-WidgetType-'.$single__widget['widget_id'].$P_clas.'">';
										echo ( ( isset( $single__widget['color_edits'] ) && !empty( $single__widget['color_edits'] ) ) ) ? '<div style="display:none" data-roots-loded="'.base64_encode( json_encode( $single__widget['color_edits'] ) ).'"></div>' : '';
										# TOP SEPARATOR
										if( isset( $single__widget['show_top_separator'] ) && $single__widget['show_top_separator'] == 'on' ){
											// echo '<div class="shape-divider custom-shape-divider-top">';
											// 	echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev/svgjs" width="1440" height="300" preserveAspectRatio="none" viewBox="0 0 1440 300">';
											// 	    echo'<g mask="url(&quot;#SvgjsMask1005&quot;)" fill="none">';
											// 	        echo'<path d="M 0,185 C 144,157 432,51.4 720,45 C 1008,38.6 1296,131.4 1440,153L1440 300L0 300z" fill="rgba(0, 0, 0, 1)"></path>';
											// 	    echo'</g>';
											// 	    echo'<defs>';
											// 	        echo'<mask id="SvgjsMask1005">';
											// 	            echo'<rect width="1440" height="300" fill="#ffffff"></rect>';
											// 	        echo'</mask>';
											// 	    echo'</defs>';
											// 	echo'</svg>';	
											// echo '</div>';
										}

									# WIDGET PART
										echo '<div class="'.$section_InnerRow_class.'">';
											(new $single__widget['widget_id'])->widget__ui($single__widget);
										echo '</div>';

									# TOP SEPARATOR
										if( isset( $single__widget['show_bottom_separator'] ) && $single__widget['show_bottom_separator'] == 'on' ){
											// echo '<div class="shape-divider custom-shape-divider-bottom">';
											// 	echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev/svgjs" width="1440" height="300" preserveAspectRatio="none" viewBox="0 0 1440 300">';
											// 	    echo'<g mask="url(&quot;#SvgjsMask1005&quot;)" fill="none">';
											// 	        echo'<path d="M 0,185 C 144,157 432,51.4 720,45 C 1008,38.6 1296,131.4 1440,153L1440 300L0 300z" fill="rgba(0, 0, 0, 1)"></path>';
											// 	    echo'</g>';
											// 	    echo'<defs>';
											// 	        echo'<mask id="SvgjsMask1005">';
											// 	            echo'<rect width="1440" height="300" fill="#ffffff"></rect>';
											// 	        echo'</mask>';
											// 	    echo'</defs>';
											// 	echo'</svg>';		
											// echo '</div>';
										}
									echo '</div>';	
								}
							}
						}
					}
			echo '</div>';

		}

	# SELECTOR UI TOOLS .	

		public function widgets__model__UI( $vars ){
			/*
				# $model__value -> CURERENT MODELS VALUES . ARRAY() *REQUIRE*
				# $active__model -> CURRENT MODEL SELECTED .
				# $current__data -> CURRENT ACTIVABLE VALUES .			
			*/
			extract($vars);


			if( !isset( $model__value ) ) return '';

			if( !isset( $active__model ) ) $active__model = $model__value['SelectedModel'];

			if( !isset( $current__data ) ) $current__data = ( ( isset( $model__value[ $active__model ] ) ) ) ? $model__value[ $active__model ] : array();

			if( isset( $current__data[ 'TextAreaColor' ] ) && strpos( $current__data[ 'TextAreaColor' ], PHP_EOL ) !== FALSE ){
				$current__data[ 'TextAreaColor' ] = explode(PHP_EOL, $current__data[ 'TextAreaColor' ]);
			}else{
				$current__data[ 'TextAreaColor' ] = array();	
			}
			$current__data['AttrStyle'] = 'style="'.implode('', $current__data[ 'TextAreaColor' ]).'"';

			(new $active__model)->widget__ui($current__data);

		}

	# SETUP HOOKS
		public function SetupHooks(){

			# SETUP WIDGETS CENTER .
				global $yc__widgets__center;
				$yc__widgets__center = array();
				foreach ( $this->widgets_folders__Packages as $folder ) {
					$folder__name = basename($folder);
					$yc__widgets__center[ $folder__name ]['path'] = $folder;
					$yc__widgets__center[ $folder__name ]['name'] = $folder__name;

					$get__files = array_filter( glob( "{$folder}*" ) );
					foreach ( $get__files as $file ) {
						$file__name = basename($file);
						$file__name = explode('.php', $file__name)[0];
						$yc__widgets__center[ $folder__name ]['Packs'][$file__name] = array(
							'title'=>ucfirst($file__name),
							'id'=>$file__name,
						);
					}
				}
				do_action( 'yc__widgets__center' );

			# SETUP WIDGETS SELECTOR .
				global $yc__widgets__selector;
				$yc__widgets__selector = array();
				foreach ( $this->widgets_folders__Packages as $folder ) {
					$folder__name = basename($folder);
					$yc__widgets__selector[ $folder__name ]['path'] = $folder;
					$yc__widgets__selector[ $folder__name ]['name'] = $folder__name;

					$get__files = array_filter( glob( "{$folder}*" ) );
					foreach ( $get__files as $file ) {
						$file__name = basename($file);
						$file__name = explode('.php', $file__name)[0];
						$yc__widgets__selector[ $folder__name ]['Packs'][$file__name] = array(
							'title'=>ucfirst($file__name),
							'id'=>$file__name,
						);
					}
				}
				do_action( 'yc__widgets__selector' );
		}

	# FIRST SETUP ACTION .

		public function Setup(){

			# INIT FIELDS HOOK 
				add_action('after_setup_theme',array($this,'SetupHooks'),1);
			
			# SETUP WIDGETS FILES .
				foreach ( $this->require__setup as $file ) {
					require($file);
				}
		}
}
(new YC__WidgetsMachine)->Setup();