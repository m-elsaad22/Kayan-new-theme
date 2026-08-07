<?php
header("Content-Type: application/json");
ob_start();

$json = array();
$Ajax__data['Argums'] = json_decode( base64_decode( $Ajax__data['Argums'] ) );
$Ajax__data['Argums'][] = $Ajax__data['key'];
#
$TaxonomyesObject = TaxonomyesObject();
$TaxonomyList  = array();
foreach ($TaxonomyesObject as $s => $v) {
	$TaxonomyList[$s] = $v->name;		
}

$FieldUniqTest = uniqid();
$key = $Ajax__data['key'];

echo '<div class="InputsAppender--Fields-BoxArea">';
	echo '<div class="Title-MoreForms" data-elem-finded="'.$key.'"><i class="fa-solid fa-file-circle-plus"></i><h2>نموذج <em>['.$key.']</em> </h2><div class="Remove-GroupField" data-remove-setp="step-item" data-tooltip="حذف كل العناصر "><i class="fa-solid fa-trash-can"></i></div></div>';
		#
	$ArgsTitle = array(
		'type'=>'Text',
		'id' => 'title',
		'title'=>'عنوان النموذج ',
		'require'=>true,
		'parent_id'=>'TempForms['.$key.']',
	);
	$this->Fields__Part($ArgsTitle['type'],$ArgsTitle);
	#
	$FieldsSetup = array(
		'type'=>'GroupsField',
		'id'=>'fields',
		'title'=>'اداة إنشاء النماذج',
		'parent_id'=>'TempForms['.$key.']',
		'value'=>$formdata['fields'],
		'fields'=>array(
			array(
				'type'=>'Text',
				'id' => 'title',
				'title'=>'عنوان العنصر',
				'require'=>true,
			),
			array(
				'type'=>'Text',
				'id' => 'desctiption',
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
		)
	);
	$this->Fields__Part($FieldsSetup['type'],$FieldsSetup);
echo '</div>';

#
$query = ob_get_clean();
$json['output'] = $query;
$json['Argums'] = base64_encode( json_encode( $Ajax__data['Argums'] ) );
#
echo json_encode($json);