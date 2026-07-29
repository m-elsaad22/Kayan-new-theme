<?php 
$TaxonomyesObject = TaxonomyesObject();
$TaxonomyList  = array();
foreach ($TaxonomyesObject as $s => $v) {
	$TaxonomyList[$s] = $v->name;		
}
if( !isset( $_GET['action'] ) || isset( $_GET['action'] ) && $_GET['action'] == 'new' ){
	$post_id = wp_insert_post(
		array(
			'post_type'=>'yc-froms',
			'post_status'=>'auto-draft',
			'post_content'=>'',
			'post_title'=>'مسودة نماذج تلقائية ',
		)
	);
	$post = get_post($post_id);
	$action = 'new';
}else if( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && isset( $_GET['id'] ) ){
	$post = get_post($_GET['id']);
	$action = 'edit';
}


$ActionOptions = array(
	'defult'=>'بدون ',
	'contact_page'=>'صفحة اتصل بنا ',
	'mail'=>'رسالة للبريد الاليكتروني ',
	'support_pannel'=>'استقبال الرسائل على لوحة التحكم ',
);

if(!isset($ExclodedMeta)) $ExclodedMeta = array();
/*echo '<link rel="stylesheet" media="all" type="text/css" data-loader-href="'.get_template_directory_uri().'/components/styles/FontAwesome/css/all.min.css" />';
if(isset($Styles)){
	foreach ($Styles as $skey => $meky) {
		echo '<link rel="stylesheet" type="text/css" href="'.$meky.'?v='.rand().'" />';
	}
}
*/
if( !empty( $_POST ) ){
	if( isset( $_POST['submitForm'] ) ){
		$PostUpdated = array(	
			'ID'=>$post->ID,		
			'post_status'=>'publish',
		);

		if( isset( $_POST['post_title'] ) ){
			$PostUpdated['post_title'] = $_POST['post_title'];
		}
		wp_update_post($PostUpdated);
		#
		$PostMetaList = array('actionType','post_formate_mail','TempForms');
		foreach ($PostMetaList as $metavalue) {
			if( isset( $_POST[$metavalue] ) && !empty( $_POST[$metavalue] ) && $_POST[$metavalue] != '' ){
				update_post_meta($post->ID,$metavalue,$_POST[$metavalue]);
			}else{
				delete_post_meta($post->ID,$metavalue);
			}
		}


		if( isset( $_POST['services'] ) ){
			$SL = get_term_by('id',$_POST['services'],'services');
			if( isset( $SL->slug ) ){
				wp_set_object_terms( $post->ID,array($SL->slug) , 'services' );
			}
		}
	}
	$post = get_post($post->ID);
}
echo '<Inseder--Appender>';	
	echo '<header class="YS_Header">';
		echo '<div class="PluginName">';
			echo '<div class="Head--Text">';
				echo (( $action == 'new' ) ? '<h2>'.$PageSetup['title'].'</h2><p>إنشاء نموذج جديد </p>' : '<h2>تحرير الـFORM</h2><p>تعديل النموذج '.$post->post_title.'</p>');
			echo '</div>';
			echo '<div class="Header Icon">'.LoardIcons($PageSetup['LoardIcon'],'250px','250px').'</div>';
		echo '</div>';
	echo '</header>';
	echo '<div class="PagesModelConainer -PageIs--CreateForms">';
		$AdminUrl = admin_url('admin.php?page=edit-forms&id='.$post->ID.'&action=edit');
		echo '<form action="'.$AdminUrl.'" method="POST" enctype="multipart/form-data">';
			echo '<input type="hidden" name="submitForm" value="">';
			echo '<div class="-FormsBuilding-Main">';
				echo '<div class="-Page-FormsBox">';
					echo '<div class="-FormsPostTitle">';
						echo '<input value="'.(($post->post_status == 'auto-draft') ? '' : $post->post_title).'" type="text" placeholder="عنوان النموذج " name="post_title" id="post_title">';
					echo '</div>';

					echo '<div class="-Next-Actions-Selected">';
						$ActionType = get_post_meta($post->ID,'actionType',true);
						$ActionType = ( ( !empty( $ActionType ) ) ) ? $ActionType :  'defult';
						#
						$SelectTaxName = array(
							'type'=>'Select',
							'id' => 'actionType',
							'title' =>'تحديد الاجراء ',
							'value'=>$ActionType,
							'options'=> $ActionOptions,
						);
						$this->Blade('EditFields',$SelectTaxName,$SelectTaxName['type']);
					echo '</div>';
				echo '</div>';
				#

				$post_formate_mail = get_post_meta($post->ID,'post_formate_mail',true);
				$post_formate_mail = ( ( !empty($post_formate_mail) ) ) ? $post_formate_mail : array();

				echo '<div class="-fix-post-box -fix-field-Mail-Formate" '.( ( $ActionType == 'mail' ) ? '' : 'style="display:none;"' ).' data-show-postbox="mail">';
					echo '<div class="Title-MoreForms"><i class="fa-solid fa-envelopes-bulk"></i><h2>تحديد محتوي الرسالة الموجهة للعميل </h2></div>';
					$formate_post_select = array(
						'id'=>'post_formate_mail',
						'type'=>'Models-Selector',
						'ModelCenter'=>'Mail-Models',
						'value'=>$post_formate_mail,
						'create_fields'=>true,
						'select_field'=>array(
							'type'=>'Select',
							'id' => 'SelectedModel',
							'parent_id'=>'post_formate_mail',
							'title' =>'محتوي الرسالة ',
							'selected_shows'=>true,
						)
					);
					$this->Blade('EditFields',$formate_post_select,$formate_post_select['type']);
				echo '</div>';



				echo '<div class="-fix-post-box">';
					echo '<div class="Title-MoreForms"><i class="fa-solid fa-envelopes-bulk"></i><h2>تحديد الى خدمة مخصصة</h2></div>';
					$terms_services = ( is_array( get_the_terms( $post->ID,'services',true ) ) ) ? get_the_terms( $post->ID,'services',true ) : array();
					$services = '';
					if( !empty( $terms_services ) ){
						$services = $terms_services[0]->term_id;
					}

					$services_post_select = array(
						'id'=>'services',
						'type'=>'Taxonomy-Select',
						'value'=>$services,
						'taxonomy_name'=>'services',
						'title'=>'اختيار الخدمة ',
						'per'=>100
					);
					$this->Blade('EditFields',$services_post_select,$services_post_select['type']);
				echo '</div>';

				$UniqStep = uniqid();
				# ADD MORE BOXES.
				$UniqTest = uniqid();
				$FieldUniqTest = uniqid();
				$EmptyFormElement = array(
					$UniqTest=>array(
						'title'=>'',
						'fields'=>array(
							/*$FieldUniqTest=>array(
								'title'=>'كانها واحده معمولة لوحدهخا',
								'desctiption'=>'ودة كانه الوصف بتاع الواحدة الى معمولة لوحدها او جلاش لوحدها ',
								'FieldType'=>'Select',
								'Require'=>'on',
								'taxonomy_field'=>'',
								'options'=>'',
								'taxonomy_name'=>'',
								'id'=>'',
							)*/
						),
					)
				);
				$TempForms = ( is_array( get_post_meta($post->ID,'TempForms',true) ) ) ? get_post_meta($post->ID,'TempForms',true) : $EmptyFormElement;
				echo '<div class="-fix-inner-post-boxes -fix-post-box">';
					echo '<div class="Title-MoreForms"><i class="fa-solid fa-layer-group"></i><h2>اداة إنشاء النماذج </h2></div>';
					echo '<div class="Inner-selected-fields" data-uniq-step="'.$UniqStep.'">';
						#
						$TempKeyes = array();
						foreach ($TempForms as $ker => $ver) {
							$TempKeyes[] = $ker;
						}
						#
						$i = 0;
						foreach ($TempForms as $key => $formdata) {$i++;
							echo '<div class="InputsAppender--Fields-BoxArea">';
								echo '<div class="Title-MoreForms" data-elem-finded="'.$key.'"><i class="fa-solid fa-file-circle-plus"></i><h2>نموذج <em>['.$key.']</em> </h2><div class="Remove-GroupField" data-remove-setp="step-item" data-tooltip="حذف كل العناصر "><i class="fa-solid fa-trash-can"></i></div></div>';							

								$ArgsTitle = array(
									'type'=>'Text',
									'id' => 'title',
									'title'=>'عنوان النموذج ',
									'require'=>true,
									'parent_id'=>'TempForms['.$key.']',
									'value'=>$formdata['title'],
								);
								$this->Blade('EditFields',$ArgsTitle,$ArgsTitle['type']);

								$ArgsTitle = array(
									'type'=>'Text',
									'id' => 'button_title',
									'title'=>'عنوان زرار الSUBMIT',
									'require'=>true,
									'parent_id'=>'TempForms['.$key.']',
									'value'=>( ( isset( $formdata['button_title'] ) ) ) ? $formdata['button_title'] : '',
								);
								$this->Blade('EditFields',$ArgsTitle,$ArgsTitle['type']);

								$Field_button_icon = array(
									'type'=>'TextArea_Code',
									'id' => 'button_icon',
									'title'=>'ايقونة زرار الSUBMIT',
									'require'=>true,
									'parent_id'=>'TempForms['.$key.']',
									'value'=>( ( isset( $formdata['button_icon'] ) ) ) ? $formdata['button_icon'] : '',
								);
								$this->Blade('EditFields',$Field_button_icon,$Field_button_icon['type']);


								$FieldsSetup = array(
									'type'=>'GroupsField',
									'id'=>'fields',
									'title'=>'اداة إنشاء النماذج',
									'parent_id'=>'TempForms['.$key.']',
									'value'=>( ( !isset($formdata['fields']) ) ) ? array() : $formdata['fields'],
									'fields'=>array(
										array(
											'type'=>'SwitchBox',
											'id'=>'First_Field',
											'title'=>'الحقل الاول ؟',
											'disc'=>'هل تريد بدء العمليه بهذا الحقل  ?',
										),										
										array(
											'type'=>'Text',
											'id' => 'title',
											'title'=>'عنوان العنصر',
											'require'=>true,
										),
										array(
											'type'=>'Text',
											'id' => 'id',
											'title'=>'معرف الفيلد ',
										),
										array(
											'type'=>'Text',
											'id' => 'disc',
											'title'=>'وصف مختصر ',
										),
										array(
											'type'=>'Radio',
											'id'=>'FieldType',
											'title'=>'تحديد نوع الادخال ',
											'value'=>'Text',
											'options'=>array(
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
												'CuntrySelect'=>'Cuntry Select',
												'Phone-Number'=>'Phone Number',
											),
											'require'=>true,
											'Custom_says' => true,
											'shows_selected' => true,
											'show_create_fields'=>array(
												array(
													'type'=>'TextArea',
													'id' => 'options',
													'title'=>'إضافة إختيارات',
													'disc' =>'إضافة اختيارات  .. قم بكتابة الاختيار ثم اضغط ENTER واضف خيارً اخر وهكذا ',
													'shows_At'=>array('Select','CheckBox','Radio'),
												),

												array(
													'type'=>'Select',
													'id' => 'taxonomy_name',
													'title' =>'تحديد نوع الـ TAXONOMY',
													'options'=>$TaxonomyList,
													'shows_At'=>array('Taxonomy-Select','Taxonomy-CheckBox','Taxonomy-Radio'),
												),

												array(
													'type'=>'Select',
													'id' => 'taxonomy_field',
													'title' =>'إضافة اختيارات  .. قم بكتابة الاختيار ثم اضغط ENTER واضف خيارً اخر وهكذا ',
													'options'=>array(
														'term_id'=>'term_id',
														'slug'=>'slug',
														'name'=>'name',
													),
													'shows_At'=>array('Taxonomy-Select','Taxonomy-CheckBox','Taxonomy-Radio')
												)

											),
										),
										array(
											'type'=>'SwitchBox',
											'id'=>'Require',
											'title'=>'إجباري  ?',
											'disc'=>'هل تريد  تعيين هذا العنصر اجباريا لاستكمال الخطوة التالية ؟',
										),

										array(
											'type'=>'Radio',
											'id'=>'showsin',
											'title'=>'هل هذا الفيلد هو ؟',
											'value'=>'Text',
											'options'=>array(
												'mail' => 'البريد الالكتروني',
												'name'=>'الاسم ',
											),
										),

									)
								);
								$this->Blade('EditFields',$FieldsSetup,$FieldsSetup['type']);
							echo '</div>';
						}
						#
					echo '</div>';
					echo '<div class="-row-create-button">';
						echo '<div class="Insert-FormStep" data-insert-step="'.$post->ID.'" data-step="'.$UniqStep.'" data-insert-argums="'.base64_encode(json_encode($TempKeyes)).'" data-meta-name="TempForms">';
							echo '<i class="fa-solid fa-file-circle-plus"></i>';
							echo '<span>إنشاء نموذج </span>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			echo '<div class="-row-create-button"><button type="submit"><i class="fa-solid fa-diagram-project"></i><span>'.(($action == 'new') ? 'نشر النموذج ' : 'تحديث النموذج ').'</span></button></div>';
		echo '</form>';
	echo '</div>';

echo '</Inseder--Appender>';
/*echo '<script>';
	echo 'var $ = jQuery;';
	echo "var WPAdminAjax = '".admin_url('admin-ajax.php')."';";
	echo "var AjaxURL = '".home_url('EditAjaxCenter')."';";
	echo "var NewHomeURL = '".home_url()."';";
	echo "var MyAdminURL = '".admin_url()."';";
	echo "var TaxonomyesOption = '".json_encode($TaxonomyList)."';";

echo '</script>';
if(isset($Scripts)){
	foreach ($Scripts as $skey => $meky) {
		echo '<script type="text/javascript" src="'.$meky.'?v='.rand().'"></script>';
	}
}*/