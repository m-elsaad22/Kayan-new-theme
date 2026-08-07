<?php
ob_start();
header("Content-Type: application/json");

$json = array();

$PartArguments = array('UniqID'=>$_POST['UniqKey']);


$_POST['filter_id'] = json_decode( base64_decode( $_POST['filter_id'] ) ,true);
#
$_POST['filter_arguments'] = json_decode( base64_decode( $_POST['filter_arguments'] ) ,true);

//$json['00__test'] = $_POST['filter_arguments'];
if( isset( $_POST['filter_arguments']['tax_query'] ) ){
	$Newfilter_arguments = $_POST['filter_arguments']['tax_query'];
	#
	foreach ($Newfilter_arguments as $k => $v ) {
		if( isset( $v['taxonomy'] ) && $v['taxonomy'] == $_POST['filter_id']['taxonomy'] ){
			unset($_POST['filter_arguments']['tax_query'][$k]);
		}
	}
}

if( isset( $_POST['filter_id']['id'] ) ){
	if( !isset( $_POST['filter_arguments']['tax_query']['relation'] ) ) $_POST['filter_arguments']['tax_query']['relation'] = 'AND';

	# TERM OPRIONS .
	$Field = 'term_id';
	$NewTerm = get_term_by($Field,$_POST['filter_id']['id'],$_POST['filter_id']['taxonomy']);
	if( isset( $NewTerm->term_id ) ){
		$_POST['filter_arguments']['tax_query'][] = array(
		    'taxonomy'  => $_POST['filter_id']['taxonomy'],
		    'field'   => $Field,
		    'terms'   => $_POST['filter_id']['id'],
		    'operator'  => 'IN'
		);
		# SEND PART.
		$PartArguments['CurrentFilters'][ $_POST['filter_id']['taxonomy'] ] = $NewTerm;
	}
}

//$json['01__test'] = $_POST['filter_arguments'];
#
if( isset( $_POST['filter_arguments']['meta_query'] ) ){
	$Newfilter_arguments = $_POST['filter_arguments']['meta_query'];
	#
	foreach ($Newfilter_arguments as $k => $v ) {
		if( isset( $v['key'] ) ){

			if( is_array( $_POST['filter_id']['meta_query'] ) ){
				foreach( $_POST['filter_id']['meta_query'] as $ars => $mtk ){
					if( $v['key'] == $mtk['key'] ){
						unset($_POST['filter_arguments']['meta_query'][$k]);
					}
				}
			}else if( $_POST['filter_id']['meta_query'] == $v['key'] ){
				unset($_POST['filter_arguments']['meta_query'][$k]);
			}
		}
	}
}
#
//$json['11__test'] = $_POST['filter_arguments'];

if( isset( $_POST['filter_id']['meta_query'] ) ){
	if( !isset( $_POST['filter_arguments']['meta_query']['relation'] ) ) $_POST['filter_arguments']['meta_query']['relation'] = 'AND';

	foreach ( $_POST['filter_id']['meta_query'] as $k => $metavalue) {
		if( is_array( $metavalue ) ){
			//$json[$k] = $_POST['filter_id'];
			$_POST['filter_arguments']['meta_query'][] = $metavalue;
		}
	}
}

#
if( isset( $_POST['filter_id']['filter'] ) ){
	$PartArguments['CurrentFilters'][ 'filter' ] = $_POST['filter_id']['filter'];
	if( $_POST['filter_id']['filter'] == 'latest' ) {
	    unset($_POST['filter_arguments']['meta_key']);
	    unset($_POST['filter_arguments']['orderby']);
	}else if( $_POST['filter_id']['filter'] == 'most_views' ) {
	    $_POST['filter_arguments']['meta_key'] = 'views';
	    $_POST['filter_arguments']['orderby'] = 'meta_value_num';

	}else if( $_POST['filter_id']['filter'] == 'most_rate' ) {
	    $_POST['filter_arguments']['meta_key'] = 'TotalRate';
	    $_POST['filter_arguments']['orderby'] = 'meta_value_num';

	}else if( $_POST['filter_id']['filter'] == 'best_seller' ) {
	    $_POST['filter_arguments']['meta_key'] = 'seller_conter';
	    $_POST['filter_arguments']['orderby'] = 'meta_value_num';

	}else if( $_POST['filter_id']['filter'] == 'pin' ) {
    	$_POST['filter_arguments']['meta_key'] = 'pin';
    	if( isset( $_POST['filter_arguments']['orderby'] ) ) unset( $_POST['filter_arguments']['orderby'] );

	}else if( $_POST['filter_id']['filter'] == 'rand' ) {
    	$_POST['filter_arguments']['orderby'] = 'rand';

	}else if( $_POST['filter_id']['filter'] == 'old' ) {
    	$_POST['filter_arguments']['order'] = 'ASC';
	}
}

//$json['test'] = $_POST['filter_arguments'];
$PartArguments['PostsArguments'] = $_POST['filter_arguments'];

if( isset( $_POST['filter_id']['workstap'] ) ){
	$PartArguments['Ajax_Cut'] = true;
	(new ThemeStatic)->Part('workstap', $PartArguments);

	$HTML = ob_get_clean();

	#
	$output = explode( '<Filter-Posts-Cut>' , $HTML) [1];
	$output = explode( '</Filter-Posts-Cut>' , $output) [0];
	if( !empty( $output ) && $output != ''){
		$json['output'] = $output;
	}else{
		$json['output'] = '<div class="Not-Found-posts"><p>لم يتم العثور على نتائج </p></div>';
	}

}else{
	foreach( get_posts($_POST['filter_arguments']) as $post ) {
	  (new ThemeStatic)->Part($_POST['filter_id']['BoxPart'], array("post"=>$post,'stop_lazyload'=>false));
	}	
	$HTML = ob_get_clean();
	if( empty( $HTML ) ){
		$HTML = '<div class="Not-Found-posts"><p>لم يتم العثور على نتائج </p></div>';
	}
	$json['output'] = $HTML;
}

$json['argums'] = base64_encode( json_encode( $_POST['filter_arguments'] ) );
echo json_encode($json);