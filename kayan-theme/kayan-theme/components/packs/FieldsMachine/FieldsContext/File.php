<?php
if( !isset( $value ) || !isset( $value ) && ( is_string($value) || $value == '' ) ) $value = array();

$value = ( ( is_array( $value ) ) ) ? $value : array();
if( isset( $InsertElements ) ){
	unset($vars['InsertElements']);
	$InputName = 'Insert_'.$id;
}else if( isset( $parent_id ) ){
	$InputName = $parent_id.'['.$id.']';
}else{
	$InputName = $id;
}

$d_button = ((is_rtl()) ? 'رفع ملف' : 'Upload file');
if( !isset( $button ) ) $button = $d_button;
$vars['button'] = $button;
#
if( !isset( $mime ) ) $mime = 'image';
$vars['mime'] = $mime;

if(!isset($multiple)) $multiple = false;
$vars['multiple'] = $multiple;
// ## IMAGE ID GET // 
$UniqID = uniqid();
$style='';
$vars['vars'] = base64_encode(json_encode($vars));
echo '<div class="-fix-inputs-area" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).'>';
	echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3></div>';
  //
	echo '<div class="FieldUpload--FilesBox FieldUpload--'.$id.'">';
    //
    if($multiple == true){
      if(!isset($value)) $value = array();

      if( isset( $value['url'] ) || isset( $value['id'] ) ) $value = array();

			if( empty($value) ) {$style='style="display:none;"';}	
			#
    	echo '<a data-custom-uploader="true" href="javascript:void(0);" data-id="'.$id.'" data-multiple="'.(($multiple == false) ? 'false' : 'true').'" data-type="'.$mime.'" data-field="'.$id.'" data-name="'.$InputName.'" data-rlname="'.$title.'" class="CustomUploadYC YC--Uploads--'.$id.'"><i class="fa-solid fa-arrow-up"></i><span>'.$button.'</span></a>';
      echo '<a '.$style.' href="javascript:void(0);" class="CustomImage--RemoveButton" data-multiple="true" id="'.$id.'_remove"><i class="fa-solid fa-trash"></i><span>حذف الكل</span></a>';
      echo '<div class="previewList for--multiple-files '.$id.'_previewLists" id="'.$id.'_previewLists">';
        foreach ((is_array($value)) ? $value : array() as $k => $url) {
          echo '<div class="CustomImage--Boxed">';
            echo '<input type="hidden" name="'.$InputName.'['.$k.']" value="'.$url.'" data-exvalue="'.$k.'" />';
            echo '<div class="CustomImage--Preview">';
              if($mime == 'image'){
                echo '<img src="'.$url.'" />';
              }else if($mime == 'video/mp4'){
                echo '<iframe src="'.$url.'" autoplay="0" width="270" height="180"></iframe>';
              }else if($mime == 'audio/mpeg'){
                echo '<audio controls>';
                  echo '<source src="horse.ogg" type="audio/ogg">';
                  echo '<source src="'.$url.'" type="audio/mpeg">';
                echo '</audio>';
              }
            echo '</div>';
            echo '<em onClick="$(this).parent().remove();"><span></span><span></span></em>';
          echo '</div>';
        }
      echo '</div>';
      //
    }else{
      if(!isset($value['url'])) $value = array('url'=>'','id'=>'');
			if( $value['url'] == '' || $value['id'] == '' ){ $style='style="display:none;"'; }
      //$Id_InputName = str_replace($id.']',$id.'_id]', $InputName);
			$Id_InputName = str_replace($id,$id.'_id', $InputName);
			#
      echo '<input type="hidden" id="CustomImage_'.$id.'_id" class="CustomImage_'.$id.'_id" name="'.$Id_InputName.'"  value="'.$value['id'].'" data-exvalue="'.$value['id'].'"  />';
      echo '<input type="text" value="'.$value['url'].'" name="'.$InputName.'" id="CustomImage_'.$id.'" class="CustomImage_'.$id.'"  data-exvalue="'.$value['url'].'" />';
      echo '<a href="javascript:void(0);"  data-custom-uploader="true" data-id="'.$id.'" data-multiple="'.(($multiple == false) ? 'false' : 'true').'" data-type="'.$mime.'" data-field="'.$id.'" data-name="'.$InputName.'" data-rlname="'.$title.'" class="CustomUploadYC YC--Uploads--'.$id.'"><i class="fa-solid fa-arrow-up"></i><span>'.$button.'</span></a>';
      echo '<a '.$style.' href="javascript:void(0);" class="CustomImage--RemoveButton" data-multiple="false" id="CustomImage_'.$id.'_remove"><i class="fa-solid fa-xmark"></i><span>حذف</span></a>';
      #
      echo '<div class="CustomImage--Preview CustomImage_'.$id.'_preview" id="CustomImage_'.$id.'_preview" '.$style.'>';
    		echo '<div class="CustomImage--Boxed">';
          if($mime == 'image'){
            echo '<img '.$style.' src="'.$value['url'].'" />';
          }else if($mime == 'video/mp4'){
            echo '<iframe src="'.$value['url'].'" '.$style.' autoplay="0" width="270" height="180"></iframe>';
            if( !isset($value['duration']) ) $value['duration'] = '';
            $duration_InputName = str_replace($id.']',$id.'_duration]', $InputName);
            echo '<input type="hidden" id="CustomImage_'.$id.'_duration" class="CustomImage_'.$id.'_duration" name="'.$duration_InputName.'"  value="'.$value['duration'].'" data-exvalue="'.$value['duration'].'"  />';
          }else if($mime == 'audio/mpeg'){
            if(!empty($value['url'])){
              echo '<audio controls>';
                echo '<source src="horse.ogg" type="audio/ogg">';
                echo '<source src="'.$value['url'].'" type="audio/mpeg">';
              echo '</audio>';
            }
          }
        echo '</div>';
    	echo '</div>';
    }
  echo '</div>';
  echo ( ( isset( $disc ) ) ) ? '<descor>'.$disc.'</descor>' : '';
echo '</div>';