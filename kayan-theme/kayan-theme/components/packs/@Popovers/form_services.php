<?php
$FormsUI = new FormsUI;
if( !isset( $current__action ) ) $current__action = 'form__services';
if( !isset( $current_step ) ) $current_step = 'first_forms_steps';


$form__title = get_option('form__title');

if(empty($form__title)){

	$form__title = 'ما نوع الخدمه التي تحتاجها؟';
}


$dis__title = get_option('dis__title');
if(empty($dis__title)){

	$dis__title = 'ما نوع الخدمه التي تحتاجها؟';
}

$catargey__title = get_option('catargey__title');
if(empty($catargey__title)){

	$catargey__title = 'تحديد التصينف';
}
$button__title = get_option('button__title');
if(empty($button__title)){

	$button__title = ' التالي ';
}

$button__icon = get_option('button__icon');
if(empty($button__icon)){

	$button__icon = ' <i class="fa-solid fa-arrow-left"></i> ';
}

$extract__fields = array(
	'current_step'=>$current_step,
	'forms__fields'=>array(
		'first_forms_steps' => array(
			'title'=>''.$form__title.'',
			'desc'=>''.$dis__title.'',
			'id'=>'first_forms_steps',
			'fields'=>array(
				array(
					'type'=>'Taxonomy-Radio',
					'id' => 'services__type',
					'taxonomy_name'=>'category',
					'parent'=>0,
					'per'=>100,
					'title' =>'',
				)
			),
			'button_title'=>''.$button__title.'',
			'button_icon'=>''.$button__icon.'',

		)
	)
);


$extract__fields = array(
	'current_step'=>$current_step,
	'forms__fields'=>array(
		'first_forms_steps' => array(
			'title'=>'ما نوع الخدمه التي تحتاجها؟',
			'desc'=>'ما نوع الخدمه التي تحتاجها؟',
			'id'=>'first_forms_steps',
			'fields'=>array(
				array(
					'type'=>'Taxonomy-Radio',
					'id' => 'services__type',
					'taxonomy_name'=>'category',
					'parent'=>0,
					'per'=>100,
					'title' =>'تحديد التصينف',
				)
			),
			'button_title'=>'التالي',
			'button_icon'=>'',
		)
	)
);

$current__category = false;
if( isset( $_POST['services__type'] ) ) $current__category = $_POST['services__type'];
if( isset( $_POST['HistoryFields']['services__type'] ) ) $current__category = $_POST['HistoryFields']['services__type'];

if( $current__category != false ){

	$object__category = get_term_by('id',$current__category,'category');
	if( isset( $object__category->term_id ) ){
		$taxonomy__fields = $FormsUI->Extract__fields(
			array(
				'object__type'=>'taxonomies',
				'current__object'=>$object__category,
				'meta__key'=>'service_steps'
			)
		);
		if( isset( $taxonomy__fields['forms__fields'] ) ){
			$extract__fields['forms__fields'] = array_merge( $extract__fields['forms__fields'],$taxonomy__fields['forms__fields']);
			if( isset( $_POST['services__type'] ) && isset( $taxonomy__fields['first_step'] ) && $taxonomy__fields['first_step'] != false ) {
				$current_step = $taxonomy__fields['first_step'];
			}
		}

	}
}
$get__fields = $FormsUI->Extract__fields($extract__fields);
$ThankYou = false;
if( isset( $_POST['submitForm'] ) ){

	$ActiveStep = false;

	$NextStep = false;
	$ChangeNext = false;

	$PrevStep = false;
	$ChangePrev = true;

	$HistoryFields = ( ( isset( $_POST['HistoryFields'] ) && is_array( $_POST['HistoryFields'] ) ) ) ? $_POST['HistoryFields'] : array();

	foreach ( $get__fields['forms__fields'] as $e => $steps) {
		
		if( $ChangeNext == true ){
			$NextStep = $steps;
			$ChangeNext = false;
		}

		if( $ChangePrev == true ){
			$PrevStep = $steps;
		}

		if( $_POST['current_step'] == $e ){
			$ChangeNext = true;
			$ChangePrev = false;
			$ActiveStep = $steps;

			foreach ( $steps['fields'] as $s => $fields) {
				if( isset( $_POST[ $fields['id'] ] ) && !empty( $_POST[ $fields['id'] ]  ) ){
					$HistoryFields[ $fields['id'] ] = $_POST[ $fields['id'] ];
				}
			}
		}
	}

	#print_r($FormData);die;
	if( isset( $_POST['forms__navs'] ) && $ActiveStep != false ){
		$current_step = $ActiveStep['id'];
		$current__action = $_POST['current__action'];
		$AjaxAction = true;
	}else if( $NextStep != false ){
		$current_step = $NextStep['id'];
		$current__action = $_POST['current__action'];
		$AjaxAction = true;
	}else{



		$taxonomy__fields['forms__fields'] = array_merge( $extract__fields['forms__fields'],$taxonomy__fields['forms__fields'] );

		$finalValues = array();
		foreach ( $HistoryFields as $metakey => $metavalue ) {
			
			foreach ( $taxonomy__fields['forms__fields'] as $__step ) {

				foreach ( $__step['fields'] as $__field ) {
					if( $__field['id'] == $metakey ){
						$__field['value'] = $metavalue;
						$finalValues[] = $__field;
					}
				}
			}
		}

		$whatsapp_number = get_option('whatsapp_number');
		if (!empty($whatsapp_number)) {
			$msg= '';
				foreach ($finalValues as $__metakey => $values) {

					if( $values['type'] == 'Taxonomy-Radio' || $values['type'] == 'Taxonomy-Select' ){
						$services__type = get_term_by('id',$values['value'],$values['taxonomy_name']);
						$msg .= "*{$values['title']}* : {$services__type->name}%0a ";

					}

					if( $values['type'] == 'Radio' || $values['type'] == 'Select' ){
						if( isset( $values['options'][ $values['value'] ] ) ){
							$msg .= "*{$values['title']}* : {$values['options'][ $values['value'] ]}%0a ";

						}
					}

					if( $values['type'] == 'CheckBox' ){
						$msg .= "*{$values['title']}* : ";
						foreach ( $values['value'] as $ik => $iv ) {
							if( isset( $values['options'][ $iv ] ) ){
								$msg .= "*{$values['options'][ $iv ]}* : ";
							}						
						}
						$msg .= " %0a ";
					}

					if( $values['type'] == 'TextArea' || $values['type'] == 'Text' || $values['type'] == 'Phone-Number' ){
						$msg .= "*{$values['title']}* : {$values['value']} %0a ";
					}
				}
			$msg = urlencode($msg); // ترميز النص لاستخدامه في عنوان URL
			#"My First"+"\r\n\r\n"+"Message for Test"
			$whatsapp_url = "https://api.whatsapp.com/send?phone=$whatsapp_number&text=$msg";
			
		}
		$alert_def = get_option('alert_def');
		if (empty($alert_def)) {
			$alert_def = 'شكرا لقد تم ارسال طلبك وسيتم الرد عليك في اقرب وقت ممكن ';
		}
		echo json_encode( array( 'alert_output'=> array('type'=>'','alert'=>'  
			<p>'.$alert_def.'</p>
			<a class="activable" target="_blank" href="'.$whatsapp_url.'">قوم بارسال البيانات</a>
			','msg'=>$whatsapp_url) ) );
				$FinalsValues = array();
		ob_start();


		$ThankYou = true;
		die;
	}
}

if( $ThankYou == false ){

	# CUSTOM OPTIONS .
		$CurrentStep = false;
		$EndStep = false;
		#
		$NextStep = false;
		$ChangeNext = false;
		#
		$PrevStep = false;
		$ChangePrev = true;

		foreach ($get__fields['forms__fields'] as $e => $w) {
			if( $ChangeNext == true ){
				$NextStep = $w;
				$ChangeNext = false;
			}

			if( $current_step == $e ){
				$ChangeNext = true;
				$ChangePrev = false;
			}

			if( $ChangePrev == false ){
				$PrevStep = $EndStep;
				$ChangePrev = true;
			}

			$EndStep = $w;
		}



	$form__fields = array(
		'form__services' => array(
			'title'=>'تحديد الخطمات المتاحة ',
			'id'=>'form__services',
			'button_title'=>'التالى',
			'button_icon'=>'',
			'steps'=>$get__fields['forms__fields'],
		)
	);

	# GET CURRENT ACTION .
		$current__form = ( ( isset( $form__fields[ $current__action ] ) ) ) ? $form__fields[ $current__action ] : $form__fields[ 'form__services' ];


	# FOUND STEPS .
		if( isset( $current__form['steps'] ) ){

			$current__parent = $current__form;
			$current__form = $current__parent['steps'][ $current_step ];
		}

	# EXTRACT CURRENT FIELDS .
		if( isset( $current__form['fields'] ) ) $current__fields = $current__form['fields'];

	# SET DEFUALT FIELDS VALUES IF FOUND IN HISTORY FIELDS .
		$ShowNextStep = true;
		foreach ( $current__fields as $r => $be_fields ) {

			if( isset( $be_fields['Require'] ) && $be_fields['Require'] == 'on' && !isset( $HistoryFields[ $be_fields['id'] ] ) ){
				$ShowNextStep = false;
			}
			#
			if( isset( $HistoryFields[ $be_fields['id'] ] ) && !empty( $HistoryFields[ $be_fields['id'] ] ) ){

				$current__fields[$r]['value'] = $HistoryFields[ $be_fields['id'] ];

				unset( $HistoryFields[ $be_fields['id'] ] );
			}		
		}



	echo '<div class="Parent-Boxed--Context---overlays --ActionType--Insert___terms">';
		echo '<div class="Boxed--Context---overlays">';

		    echo '<div class="title--Context---overlays">';
		      	echo '<strong>'.$current__form['title'].'</strong>';
				echo '<span class="Close--title---Context----overlays activable hoverable" data-tooltip="إغلاق" data-button="popover--tool" data-position="top" data-tool-type="Closse--Popover"><i class="fas fa-times"></i></span>';
		    echo '</div>';

	  		echo '<div class="inner--Context---overlays" customscroller>';

				echo ( ( isset( $AjaxAction ) ) && $AjaxAction == true ) ? '<Ex___Cut___Ajax>' : ''; 

					# FORM PLUGIN .

						/*
							# ACTION -> AJAX FILE ACTIONS .
							# DATA-FOR-ACTION -> IMPORTANT SUBMIT TO AJAXCENTER FILE.
							# METHOD -> POST || GET
							# DATA-FORM-ID -> FORM UINIQID .
							# DATA-TOTAL -> CASE FOUND FORM STEPS .	
						*/

					echo '<form action="forms__services" method="POST" enctype="multipart/form-data" data-for-action="true" data-form-ajax="true" data-form-id="'.$uniqid.'"'.( ( isset( $current__parent ) ) ? 'data-fields-arguments="'.base64_encode( json_encode( $current__parent['steps'] ) ).'" data-fetch-type="total" data-active-step="'.$current_step.'"' : ' data-fields-arguments="'.base64_encode( json_encode( $current__fields ) ).'"' ).'>';
						
						echo '<input type="hidden" name="submitForm" value="">';
						echo '<input type="hidden" name="blade" value="Popovers">';
						echo '<input type="hidden" name="shape" value="form_services">';

						if( isset( $current__parent ) ){
							echo '<input type="hidden" name="Parent__Action" value="'.$current__parent['id'].'">';
						}

						echo '<input type="hidden" name="current__action" value="'.$current__action.'">';
						echo '<input type="hidden" name="current_step" value="'.$current_step.'">';

						echo ( ( isset( $NextStep['id'] ) ) ) ? '<input type="hidden" name="NextStep" value="'.$NextStep['id'].'">' : '';
						echo ( ( isset( $PrevStep['id'] ) ) ) ? '<input type="hidden" name="PrevStep" value="'.$PrevStep['id'].'">' : '';

						if( isset( $HistoryFields ) && !empty( $HistoryFields ) ){

							foreach ( $HistoryFields as $skey => $meky ) {
								echo '<input type="hidden" name="HistoryFields['.$skey.']" value="'.$meky.'">';
							}
						}

						if( !empty( $current__fields ) ){
							foreach ($current__fields as $s => $field) {
								$this->Blade('UIFields',$field,$field['type']);
							}
							echo '<div class="-YC-Forms-button">';
								if( $PrevStep != false ){
									echo '<div class="YC-BTN-Forms-Plugin Next-Step-Action activable" data-form-navs="'.$PrevStep['id'].'"><i class="fa-solid fa-chevron-right"></i><span>السابق </span></div>';
								}

								echo '<button class="YC-BTN-Forms-Plugin SubmitButton activable" type="submit"><span>'.$current__form['button_title'].'</span>'.( ( $current__form['button_icon'] ) ? $current__form['button_icon'] : '' ).'</button>';

								/*if( $NextStep != false && $ShowNextStep == true ){
									echo '<div class="YC-BTN-Forms-Plugin Next-Step-Action activable" data-form-navs="'.$NextStep['id'].'"><span>التالى </span><i class="fa-solid fa-chevron-left"></i></div>';
								}*/

							echo '</div>';
						}

					echo '</from>';

				echo ( ( isset( $AjaxAction ) ) && $AjaxAction == true ) ? '</Ex___Cut___Ajax>' : ''; 

			echo '</div>';
		echo '</div>';
	echo '</div>';
}