<?php
header("Content-Type: application/json");
ob_start();

$aa = json_decode(base64_decode($Ajax__data['args']), true);
extract($aa);

$TaxonomyesObjects = TaxonomyesObject();
#
$json = array();
echo '<div class="Popver--CoursesAlert" data-popover-uniqs="'.$Ajax__data['uniq'].'">';
  	echo '<div class="PopverAlertOverlay" onClick="$(this).parent().remove();"></div>';
	echo '<div class="PopverInnerElemnt">';
	    echo '<div class="HeadAlert--Popvoer"><h2>'.$TaxonomyesObjects[$taxonomy]->labels->add_new_item.'</h2><span class="Remover--CoursesAlerts hoverable" onClick="$(this).parent().parent().parent().remove();"><i class="fa-solid fa-xmark"></i></span></div>';

		echo '<div class="Form-Popover">';
			echo '<form action="TermInsert" method="POST" enctype="multipart/form-data" data-form-ajax="true" data-uniq="'.$Ajax__data['uniq'].'" data-for-action="inset_term" data-form-result="inset_term" >';

				echo '<input type="hidden" name="insert_term_taxonomy" value="'.$taxonomy.'">';
				echo ( ( isset( $outputType ) ) ) ? '<input type="hidden" name="insert_term_output" value="'.$outputType.'">' : '';

				$Term_name = array(
					'type'=>'Text',
					'id' => 'inser_term_name',
					'title' => $TaxonomyesObjects[$taxonomy]->labels->new_item_name,
					'value'=>'',
					'require'=>true,
				);
				$this->Fields__Part($Term_name['type'],$Term_name);
				#
				$term_slug = array(
					'type'=>'Text',
					'id' => 'inser_term_slug',
					'title' =>'الاسم اللطيف ',
					'value'=>'',
					'require'=>true,
				);
				$this->Fields__Part($term_slug['type'],$term_slug);
				#
				$term_parent = array(
					'type'=>'Taxonomy-Select',
					'id' => 'insert_term_parent',
					'title' =>'التصنيف الأب',
					'value'=>( ( isset($parent) && $parent > 0 ) ) ? $parent : '',
					'taxonomy_name' => $taxonomy,
					'taxonomy_field' => 'term_id',
					'taxonomy_parent'=>0,
				);
				$this->Fields__Part($term_parent['type'],$term_parent);
				#
				$term_description = array(
					'type'=>'TextArea',
					'id' => 'inser_term_description',
					'title' =>'وصف الـ '.$TaxonomyesObjects[$taxonomy]->labels->singular_name,
					'value'=>'',
				);
				$this->Fields__Part($term_description['type'],$term_description);

				echo '<div class="-row-create-button"><button type="submit"><span>إضافة عنصر </span><i class="fa-solid fa-arrow-left"></i></button></div>';
			echo '</form>';
 		echo '</div>';
 	echo '</div>';
echo '</div>';
$output = ob_get_clean();
$json['output'] = $output;
echo json_encode($json);