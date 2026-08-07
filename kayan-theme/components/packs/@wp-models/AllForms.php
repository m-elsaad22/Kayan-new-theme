<?php 
$TaxonomyesObject = TaxonomyesObject();
$TaxonomyList  = array();
foreach ($TaxonomyesObject as $s => $v) {
	$TaxonomyList[$s] = $v->name;		
}

/*echo '<link rel="stylesheet" media="all" type="text/css" data-loader-href="'.get_template_directory_uri().'/components/styles/FontAwesome/css/all.min.css" />';
if(isset($Styles)){
	foreach ($Styles as $skey => $meky) {
		echo '<link rel="stylesheet" type="text/css" href="'.$meky.'?v='.rand().'" />';
	}
}*/

$per = 5;
$PostsArgums = array(
	'post_type'=>'yc-froms',
	'posts_per_page'=>$per,
);

if( isset( $_GET['s'] ) ){
	$PostsArgums['s'] = $_GET['s'];
}

if( isset( $_GET['paged'] ) ) {
	$PostsArgums['paged']	 = $_GET['paged'];
}
#
$Founder = new WP_Query($PostsArgums);
$CountQuery = $Founder->found_posts;

$UniqId = uniqid();
$LoadMoreAjax = true;
if( $CountQuery < $per ) {
	$LoadMoreAjax = false;
}

$LoaMoreAttr = (($LoadMoreAjax != false)) ? 'data-loadmore="'.base64_encode(json_encode($PostsArgums)).'" data-finish="false"' : 'data-finish="true"';
$currentURL = $this->GetCurrentURL();

echo '<Inseder--Appender>';	
	echo '<header class="YS_Header">';
		echo '<div class="PluginName">';
			echo '<div class="Head--Text">';
				echo '<h2>'.$PageSetup['title'].'</h2><p>لديك الان <spf-count>'.$CountQuery.'</spf-count> نماذج تم نشرهم </p>';
			echo '</div>';
			echo '<div class="Header Icon">'.LoardIcons($PageSetup['LoardIcon'],'250px','250px').'</div>';
		echo '</div>';
	echo '</header>';
	echo '<div class="PagesModelConainer -PageIs--CreateForms">';

		echo '<div class="-Page-Actions-Roots">';
			echo '<ul class="-Navs-Actions">';
				echo '<li data-navs-actions="SelectAll" data-uniqid="'.$UniqId.'"><i class="fa-solid fa-circle-plus"></i><span>تحديد الكل </span></li>';
				echo '<li data-navs-actions="RemoveSelectAll" data-uniqid="'.$UniqId.'" style="pointer-events: none; opacity: 0.5;"><i class="fas fa-circle-xmark"></i><span>ألغاء التحديد</span></li>';
				echo '<li data-navs-actions="RemoveAllSelected" data-uniqid="'.$UniqId.'" style="pointer-events: none; opacity: 0.5;"><i class="fas fa-circle-minus"></i><span>حذف المحدد</span></li></li>';
			echo '</ul>';

			echo '<searchingform>';
				echo '<form action="'.$currentURL.'" method="GET">';
					echo '<input type="hidden" value="FormsBuilding" id="page" name="page" placeholder="البحث في النماذج ">';
					echo '<input type="text" value="" id="YcContentSerching" name="s" placeholder="البحث في النماذج ">';
				echo '</form>';
			echo '</searchingform>';

			echo '<PagnationsCustom>';
				$CounterAll = $CountQuery / $per;

				if( strpos($CounterAll, '.') !== FALSE ){
					$CounterAll = explode('.',$CounterAll)[0];
					$CounterAll = $CounterAll + 1;

				}
				$Paged = ((!isset($_GET['paged']))) ? 1 : $_GET['paged'];
				$BackPaged = $Paged - 1;
				$NextPaged = $Paged + 1;

				$PagenateURL = $currentURL;
				$PagenateURL = explode('page=FormsBuilding', $PagenateURL)[0];
				$PagenateURL = $PagenateURL.'page=FormsBuilding';
				

				echo '<PagnationsNavs id="PagnationsNavs" data-counter="'.$CountQuery.'" data-pagedcounter="'.$CounterAll.'" data-permalink="'.$PagenateURL.'">';
					echo '<ul>';
						echo '<li '.(($Paged > 1) ? '' : 'style="pointer-events:none;opacity:0.5"').'><a href="'.$PagenateURL.'"><i class="fas fa-angles-right"></i><span>الصفحة الاولى </span></a></li>';
						echo '<li '.(($Paged > 1) ? '' : 'style="pointer-events:none;opacity:0.5"').'><a href="'.$PagenateURL.'&paged='.$BackPaged.'"><i class="fas fa-chevron-right"></i><span>الصفحة السابقة</span></a></li>';
					echo '</ul>';
					echo '<PagnationControll>';
						echo '<form action="'.$PagenateURL.'" method="GET">';
							echo '<input type="hidden" value="FormsBuilding" id="page" name="page" placeholder="البحث في النماذج ">';
							if(isset($_GET['s'])){
								echo '<input type="hidden" value="'.esc_attr( get_search_query() ).'" name="s">';
							}
							echo '<input type="text" value="'.$Paged.'" id="pagedValues" name="paged">';
						echo '</form>';
					echo '</PagnationControll>';
					echo '<ul>';
						echo '<li '.(($Paged >= $CounterAll) ? 'style="pointer-events:none;opacity:0.5"' : '').'><a href="'.$PagenateURL.'&paged='.$NextPaged.'"><i class="fas fa-chevron-left"></i><span>الصفحة التالية</span></a></li>';				
						echo '<li '.(($Paged >= $CounterAll) ? 'style="pointer-events:none;opacity:0.5"' : '').'><a href="'.$PagenateURL.'&paged='.$CounterAll.'"><coun-tt>'.$CounterAll.'</coun-tt> <i class="fas fa-angles-left"></i><span>الصفحة الاخيرة</span></a></li>';
					echo '</ul>';
				echo '</PagnationsNavs>';			
			echo '</PagnationsCustom>';

		echo '</div>';

		echo '<div class="YT-MiniBox -ScrollerCenter" '.$LoaMoreAttr.' data-uniqid="'.$UniqId.'" data-part="'.base64_encode( json_encode( array( 'template'=>'yc-froms' ) ) ).'">';
			foreach (get_posts($PostsArgums) as $post) {
				$AdminUrl = admin_url('admin.php?page=edit-forms&id='.$post->ID.'&action=edit');
				echo '<div class="-contain-MiniBox" data-post-id="'.$post->ID.'">';
					echo '<div class="-checkBox-post-fixed" data-selected-postactions="'.$post->ID.'" data-uniqid="'.$UniqId.'"></div>';
					echo '<div class="-mini-bx-info">';
						echo '<div class="-fix-selected-btn" data-selected-postactions="'.$post->ID.'" data-uniqid="'.$UniqId.'"></div>';
						echo '<h2><a href="'.$AdminUrl.'" target="_blank">'.wp_trim_words($post->post_title,10,'..').'</a></h2>';
						echo '<div class="-mini-Actions">';
							echo '<a href="'.$AdminUrl.'" target="_blank"><i class="fa-solid fa-pen-to-square"></i><span>تعديل النموذج </span></a>';
							echo '<div class="RemovePost" data-remove-post-id="'.$post->ID.'"><i class="fa-solid fa-trash-can"></i><span>حذف النموذج </span></div>';
						echo '</div>';
					echo '</div>';

				echo '</div>';
			}
		echo '</div>';
		echo '<LoadMore--InpuArea><PostsScrollLoader data-more-click="'.$UniqId.'" class="PostsScrollLoader LoadMorePostsBTN" '.(($LoadMoreAjax != false) ? '' : 'style="display:none"').'><i class="fa-solid fa-square-plus"></i><span>تحميل المزيد</span></PostsScrollLoader></LoadMore--InpuArea>';


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