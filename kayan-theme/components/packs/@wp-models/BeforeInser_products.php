<?php 
if( !empty( $_POST ) ){
	if( isset( $_POST['submitForm'] ) && isset( $_POST['post_id'] ) ){

		header("Content-Type: application/json");

		$json = array();
		$PostUpdated = array(
			'ID'=>$_POST['post_id'],		
			'post_status'=>'publish',
		);

		if( isset( $_POST['post_title'] ) ){
			$PostUpdated['post_title'] = $_POST['post_title'];
		}
		wp_update_post($PostUpdated);

		$J = array();
		$Sv_Cats = $_POST['taxonomy_category'];
		foreach ($_POST['taxonomy_category'] as $s => $v) {
			$termo = get_term_by('id',$v,'category');
			if( isset( $termo->slug ) ){

				if( $termo->parent > 0 && !in_array($termo->parent,$Sv_Cats) ){
					$parent_cats = get_term_by('id',$termo->parent,'category');
					$J[] = $parent_cats->slug;
				}

				$J[] = $termo->slug;
			}
		}


		wp_set_object_terms($_POST['post_id'], array_values($J),'category');

		$json = array(
			'type'=>'sucsses',
			'post_url'=>admin_url( 'post.php?post='.$_POST['post_id'].'&action=edit'),
			'alert'=>'جاري التحويل الى الخطوة التالية ',
		);
	}else{
		$json = array(
			'type'=>'error',
			'alert'=>'لقد حدث خطا .. يرجي اعادة تحميل الصفحة والمحاولة مرة اخري',
		);
	}
	$output = ob_get_clean();
	echo json_encode($json);
	die();
}else{
	$post_id = wp_insert_post(
		array(
			'post_type'=>'products',
			'post_status'=>'auto-draft',
			'post_content'=>'',
			'post_title'=>'مسودة نماذج تلقائية ',
		)
	);
	$post = get_post($post_id);
	$action = 'new';
}


/*echo '<link rel="stylesheet" media="all" type="text/css" data-loader-href="'.get_template_directory_uri().'/components/styles/FontAwesome/css/all.min.css" />';
if(isset($Styles)){
	foreach ($Styles as $skey => $meky) {
		echo '<link rel="stylesheet" type="text/css" href="'.$meky.'?v='.rand().'" />';
	}
}*/
#
$currentURL = $this->GetCurrentURL();
echo '<yourcolorapi--conatiner>';
	echo '<Inseder--Appender>';	
		echo '<header class="YS_Header">';
			echo '<div class="PluginName">';
				echo '<div class="Head--Text">';
					echo '<h2>أضافة منتج جديد</h2><p>برجاء تحديد عنوان المنتج والقسم والعلامة التجارية ثم أضغط أستمرار ومتابعة</p>';
				echo '</div>';
				echo '<div class="Header Icon">'.LoardIcons('slkvcfos','250px','250px').'</div>';
			echo '</div>';
		echo '</header>';
		echo '<div class="PagesModelConainer -PageIs--CreateForms">';
			$AdminUrl = admin_url('post.php?post='.$post->ID.'&action=edit');
			$uniqid = uniqid();
			echo '<form action="'.$currentURL.'" method="POST" enctype="multipart/form-data" data-form-ajax="true" data-uniq="'.$uniqid.'" data-form-result="beforeinsert_products">';

				echo '<input type="hidden" name="submitForm" value="">';
				echo '<input type="hidden" name="post_id" value="'.$post->ID.'">';

				echo '<div class="-FormsBuilding-Main">';
					echo '<div class="-Page-FormsBox">';
						echo '<div class="-FormsPostTitle">';
							$select_category = array(
								'type'=>'Text',
								'id' => 'post_title',
								'title' =>'ادخل عنواناََ مميزاََ لمنُتَجك ',
								'value'=>'',
								'require'=>true,
							);
							$this->Blade('EditFields',$select_category,$select_category['type']);
						echo '</div>';
					echo '</div>';
				echo '</div>';
				#
				echo '<div class="-fix-post-box">';
					echo '<div class="Title-MoreForms"><i class="fa-solid fa-envelopes-bulk"></i><h2>تحديد بيانات المنتج الاساسية </h2></div>';
					$select_category = array(
						'type'=>'Taxonomy-CheckBox',
						'id' => 'taxonomy_category',
						'title' =>'تحديد القسم الاساسي للمنتج ',
						'value'=>array(),
						'taxonomy_name' => 'category',
						'taxonomy_field' => 'term_id',
						'taxonomy_parent'=>0,
						'require'=>true,
					);
					$this->Blade('EditFields',$select_category,$select_category['type']);
				echo '</div>';
				echo '<div class="-row-create-button"><button type="submit"><i class="fa-solid fa-arrow-left"></i><span>متابعة واستمرار </span></button></div>';
			echo '</form>';
		echo '</div>';
	echo '</Inseder--Appender>';
echo '</yourcolorapi--conatiner>';
/*echo '<script>';
	echo 'var $ = jQuery;';
	echo "var WPAdminAjax = '".admin_url('admin-ajax.php')."';";
	echo "var AjaxURL = '".home_url('EditAjaxCenter')."';";
	echo "var NewHomeURL = '".home_url()."';";
	echo "var MyAdminURL = '".admin_url()."';";

echo '</script>';
if(isset($Scripts)){
	foreach ($Scripts as $skey => $meky) {
		echo '<script type="text/javascript" src="'.$meky.'?v='.rand().'"></script>';
	}
}*/