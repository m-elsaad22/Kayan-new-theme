<?php  $YC__CFM = new YC__CFM;
$YourColor__ScrapePlugin = new YourColor__ScrapePlugin;
if( !empty( $_POST ) ){
	if( isset( $_POST['submitForm'] ) ){

		if (isset($_FILES['import__yourColor__json']) && $_FILES['import__yourColor__json']['error'] === UPLOAD_ERR_OK) {
            $jsonFile = $_FILES['import__yourColor__json']['tmp_name'];
            $jsonData = file_get_contents($jsonFile);
            $dataArray = json_decode($jsonData, true);

            if( $dataArray !== null ){

            	if( isset( $dataArray['Taxonomies'] ) ){
            		foreach ( $dataArray['Taxonomies'] as $taxonomy => $tax_value ) {
            			foreach ( $tax_value as $term_insert ) {
            				$YourColor__ScrapePlugin->InsertTerm( $term_insert );
            			}
            		}
            	}

				foreach ( $dataArray['PostTypes'] as $post__objects ) {
					foreach ( $post__objects as $scrape__post) {

						if( isset( $scrape__post['InsertTaxonomy'] ) && !empty( $scrape__post['InsertTaxonomy'] ) ){
							foreach ( $scrape__post['InsertTaxonomy'] as $items ) {
								foreach ( $items as $tax) {
									$termInsert = $YourColor__ScrapePlugin->InsertTerm($tax);
									$scrape__post['taxonomy'][ $termInsert->taxonomy ][] = $termInsert->term_id;
								}
							}
						}
						$PostInsert = $YourColor__ScrapePlugin->InsertPost($scrape__post);
					}
				}


        		if( isset( $dataArray['ThemeOptions'] ) ){

        			foreach ( $dataArray['ThemeOptions'] as $metakey => $metavalue ) {

        				if( $metavalue['FieldType'] == 'Widgets' ){
        					foreach ( $metavalue['Value'] as $w_key => $w_value ) {
        						if( isset( $w_value['widget_post__id'] ) && isset( $metavalue['widgets__posts'][ $w_value['widget_post__id'] ] ) ){
        							$PostInsert = $YourColor__ScrapePlugin->InsertPost( $metavalue['widgets__posts'][ $w_value['widget_post__id'] ] );
        							$metavalue['Value'][$w_key]['widget_post__id'] = $PostInsert->ID;
        						}
        					}
        					yc_update_option($metakey,$metavalue['Value']);

        				}else if( $metavalue['FieldType'] == 'File' ){
							$UploadPhoto = $YourColor__ScrapePlugin->UploadPhoto($metavalue['Value']);							
							yc_update_option($metakey,wp_get_attachment_url($UploadPhoto));
							yc_update_option("{$metakey}_id",$UploadPhoto);
        				}else{
        					yc_update_option($metakey,$metavalue['Value']);
        				}
        			}

        		}

            }else{
                echo '<p>تعذر تحويل الملف JSON إلى مصفوفة PHP.</p>';
            }
        }else{
            echo '<p>حدث خطأ أثناء استيراد الملف.</p>';
        }


	}
}

$GetCurrentURL = (new ThemeStatic)->GetCurrentURL();
echo '<Inseder--Appender>';	
	echo '<header class="YS_Header">';
		echo '<div class="PluginName">';
			echo '<div class="Head--Text">';
				echo '<h2>تصدير واستيراد </h2><p>تصدير</p>';
			echo '</div>';
			echo '<div class="Header Icon"><i class="fa-solid fa-cloud-arrow-down" style="font-size:120px;color:#ffffff;"></i></div>';
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

						echo '<div class="-fix-inputs-area">';
							echo '<div class="-fix-forms-field-title"><h3>رفع ملف .JSON</h3></div>';
							echo '<div class="FieldUpload--FilesBox">';
								echo '<input type="file" name="import__yourColor__json" class="CustomImage" accept=".json"/>';
						  	echo '</div>';
						echo '</div>';

					echo '</div>';

				echo '</div>';
				echo '<div class="-row-create-button"><button type="submit"><span>إستيراد</span><i class="fa-solid fa-arrow-left"></i></button></div>';
			echo '</form>';
		echo '</div>';
	echo '</div>';

echo '</Inseder--Appender>';