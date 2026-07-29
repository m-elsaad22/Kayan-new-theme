<?php 
function Taxonomies() {
	global $ThemeTree;
	$ThemeTree->AddTaxonomy('category',array('works','post'), ' تصنيفات ', array('slug'=>'category'),true);
	$ThemeTree->AddTaxonomy('city', array("post"), 'المدن', array('slug'=>get_option('cities_url')), false);
	$ThemeTree->AddTaxonomy('questions', "bot", 'الاسئلة', false, true);
}
add_action('Initialize', 'Taxonomies', 10, 3);