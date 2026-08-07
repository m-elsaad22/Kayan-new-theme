<?php
function CustomPin__Query(){
	echo '<script>';
		echo 'var $ = jQuery;';
		echo "var HomeURL = '".home_url()."';";
		echo "var NewHomeURL = '".home_url()."';";

		$KeysValues = array();
		echo "var EmptyKeysValues = '".base64_encode( json_encode( $KeysValues ) )."';";

		echo 'var UploadButtonText = "'.((is_rtl()) ? 'إستخدام' : 'Use this').'";';
		echo 'var CodePreviewList = [];';

		echo 'function PinnedJQuery() {
			$(".ColorViewer").colorpicker();

			var CodesNumb = 0;	
			$(".CodePreview").each(function(els, el){
				if( !$(el).next().hasClass("CodeMirror") ) {CodesNumb++;
					$(el).attr("setup-code-id",CodesNumb);
					CodePreviewList[CodesNumb] = CodeMirror.fromTextArea(el, {
						mode: "text/html",
						styleActiveLine: true,
						lineNumbers: true,
						lineWrapping: true
					});
				}
			});
			$(".EditorPreview").each(function(els, el){
				$(el).removeClass("EditorPreview")
				$(el).richText();
			});


			$(".DatePreview").each(function(els, el){
				flatpickr($(el)[0], {
					enableTime: true,
					dateFormat: $(el).data("format"),

				});

			});

		}';
		echo 'PinnedJQuery();';
		# FIELDS VARS .
			$FildsOpts = array(
				'Text' => 'Text',
				'TextArea'=>'TextArea',
				'Select'=>'Select',
				'Taxonomy-Select'=> 'Taxonomy Select',
				'CheckBox'=>'CheckBox',
				'Taxonomy-CheckBox'=> 'Taxonomy CheckBox',
				'Radio'=>'Radio',
				'Taxonomy-Radio'=>'Taxonomy Radio',
				'Date'=>'Date',
				'SwitchBox'=>'SwitchBox',
			);
			$SayFieldsSetup = array();
			foreach ($FildsOpts as $skey => $meky) {
				$SayFieldsSetup[$skey] = array(
					'type' => $skey,
					'id' => 'test_'.$skey,
					'title' => 'تجربة نوع الادخال '.$meky,
					'value' => '',
				);
				
				if( $skey == 'Radio' || $skey == 'CheckBox' || $skey == 'Select' ){
					for ($i=1; $i < 11; $i++) { 
						$SayFieldsSetup[$skey]['options'][$i] = 'الاختيار رقم '.$i;
						if( $i == 1 ){
							if( $skey == 'CheckBox' ){
								$SayFieldsSetup[$skey]['value'] = array();
								$SayFieldsSetup[$skey]['value'][] = $i;
							}else{
								$SayFieldsSetup[$skey]['value'] = $i;
							}
						}
					}
				}else if( $skey == 'Taxonomy-Select' || $skey == 'Taxonomy-CheckBox' || $skey == 'Taxonomy-Radio' ){
					$SayFieldsSetup[$skey]['taxonomy_name'] = 'category';
					$SayFieldsSetup[$skey]['taxonomy_field'] = 'term_id';
					if( $skey == 'Taxonomy-CheckBox' ){
						$SayFieldsSetup[$skey]['value'] = array();
					}							
				}
			}

			echo "var SayFieldsSetup = '".base64_encode( json_encode( $SayFieldsSetup ) )."';";

			echo 'jQuery(function($){';
				echo "$('.YourColorEdits-Class-Style #adminmenu .wp-submenu-head, .YourColorEdits-Class-Style #adminmenu a.menu-top').addClass( 'hoverable activable' );";
				echo "$('#wpadminbar .quicklinks .menupop ul li:not(#wp-admin-bar-user-info) a').addClass('hoverable activable');";
				echo "$('.YourColorEdits-Class-Style #wpadminbar .ab-top-menu > li:not(#wp-admin-bar-wp-logo) > a.ab-item').addClass('hoverable activable');";
			echo '})';

	echo '</script>';
}
add_action( 'YC__CFM__pin_jquery', 'CustomPin__Query' );