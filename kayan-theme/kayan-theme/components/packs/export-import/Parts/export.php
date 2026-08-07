<?php  $YC__CFM = new YC__CFM;
global $YC__CFM__global_setup_fields;
if( !empty( $_POST ) ){
	if( isset( $_POST['submitForm'] ) ){
		
		ob_get_clean();

		$Extract__data = new Extract__data;
		$XML__Data = $Extract__data->ExtractXML($_POST);

		$filename = 'KAYAN-Export-'.date('Y-m-d').'.json';
		$content = json_encode( $XML__Data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );

		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Type: application/json');

		echo $content;
		exit;
	}
}

$GetCurrentURL = (new ThemeStatic)->GetCurrentURL();

$PostTypeArguments = PostTypeArguments();
$post__types__options = array();
foreach ( $PostTypeArguments as $ptname => $ptargums ) {
	$post__types__options[ $ptname ] = $ptargums->label;
}
#
$TaxonomyesObject = TaxonomyesObject();
$taxonomies__options = array();
foreach ( $TaxonomyesObject as $txname => $txargums ) {
	$taxonomies__options[ $txname ] = $txargums->label;
}

$ThemeOptions__pages = array();
if( isset( $YC__CFM__global_setup_fields['ThemeOptions'] ) ){
	foreach ( $YC__CFM__global_setup_fields['ThemeOptions'] as $pagename => $pageargums ) {
		$ThemeOptions__pages[ $pagename ] = $pageargums['title'];
	}
}

if( isset( $Styles ) ){
	foreach ($Styles as $skey => $meky) {
		echo '<link rel="stylesheet" data-style-ajax="'.$skey.'" type="text/css" href="'.$meky.'?v='.rand().'" />';
	}
}

echo '<Inseder--Appender>';	
	echo '<header class="YS_Header">';
		echo '<div class="PluginName">';
			echo '<div class="Head--Text">';
				echo '<h2>تصدير واستيراد </h2><p>تصدير</p>';
			echo '</div>';
			echo '<div class="Header Icon"><i class="fa-solid fa-cloud-arrow-up" style="font-size:120px;color:#ffffff;"></i></div>';
		echo '</div>';
	echo '</header>';
	echo '<div class="PagesModelConainer -PageIs--ThemeOptions --pageIS--Export-And-Import">';
		echo '<div class="-New-YTC-Pannel-Boxes">';
			echo '<form action="'.$GetCurrentURL.'" method="POST" enctype="multipart/form-data">';
				echo '<input type="hidden" name="submitForm" value="">';
				echo '<div class="-FormsBuilding-Main">';

					echo '<div class="----expor--title">';
						echo '<h2>تحديد العناصر المراد تصديرها </h2>';
						echo '<p>قٌم بتحديد العناصر المراد تصديرها من موقعك ثم إضغط على تصدير وسيتم تحميل ملف XML</p>';
					echo'</div>';

					echo '<div class="-customfields-menuFields --Export-And-Import">';
						$ArgsTitle = array(
							'type'=>'CheckBox',
							'id' => 'post__types',
							'title'=>'POST TYPES',
							'options'=>$post__types__options
						);
						$YC__CFM->Fields__Part($ArgsTitle['type'],$ArgsTitle);
					echo '</div>';

					echo '<div class="-customfields-menuFields --Export-And-Import">';
						$ArgsTitle = array(
							'type'=>'CheckBox',
							'id' => 'taxonomies__types',
							'title'=>'TAXONOMIES',
							'options'=>$taxonomies__options
						);
						$YC__CFM->Fields__Part($ArgsTitle['type'],$ArgsTitle);
					echo '</div>';

					echo '<div class="-customfields-menuFields --Export-And-Import">';
						$ArgsTitle = array(
							'type'=>'CheckBox',
							'id' => 'Theme__options__pages',
							'title'=>'صفحات اعدادات القالب',
							'options'=>$ThemeOptions__pages
						);
						$YC__CFM->Fields__Part($ArgsTitle['type'],$ArgsTitle);
					echo '</div>';

				echo '</div>';
				echo '<div class="-row-create-button"><button type="submit"><span>تصدير</span><i class="fa-solid fa-arrow-left"></i></button></div>';
			echo '</form>';
		echo '</div>';
	echo '</div>';

echo '</Inseder--Appender>';