<?php 
ob_start();
header("Content-Type: application/json");
$json = array();

if( $_POST['Type'] == 'taxonomy' ){

	$RatingValue_v1 = (INT) get_term_meta( $_POST['id'],'RatingValue_v1',true );
	$RateUserCount_v1 = (INT) get_term_meta($_POST['id'], 'RateUserCount_v1', true);

	$RatingData = ( is_array( get_term_meta( $_POST['id'], 'RateUsersData_v1', true) ) ) ? get_term_meta($_POST['id'], 'RateUsersData_v1', true) : array();

	for ($i=1; $i < 6; $i++) { 
		if( !isset( $RatingData[$i] ) ) $RatingData[$i] = 0;
	}

	if( isset( $_POST[ 'LastValueRate' ] ) ){
		if( $RatingData[ $_POST[ 'LastValueRate' ] ] > 0 ) $RatingData[ $_POST[ 'LastValueRate' ] ] = $RatingData[ $_POST[ 'LastValueRate' ] ] - 1;
	}

	$RatingData[ $_POST[ 'RateValue' ] ] = $RatingData[ $_POST[ 'RateValue' ] ] + 1;

	update_term_meta(  $_POST['id'], 'RateUsersData_v1',$RatingData );
	#

	if( !isset( $_POST[ 'LastValueRate' ] ) ){
		$RateUserCount_v1 = $RateUserCount_v1 + 1;
		update_term_meta($_POST['id'], 'RateUserCount_v1', $RateUserCount_v1);
	}
	$RatingValue_v1 = ( ( $RatingValue_v1 > $_POST[ 'LastValueRate' ] ) ) ? $RatingValue_v1 - $_POST[ 'LastValueRate' ] : 0;
	#
	$RatingValue_v1 = $RatingValue_v1 + $_POST[ 'RateValue' ];
	update_term_meta($_POST['id'], 'RatingValue_v1', $RatingValue_v1);


	# OUTPUT.
	for ($q=5; $q >= 1; $q--) { 
		if( isset( $RatingData[ $q ] ) ){
			$AverageCalc = $RatingData[ $q ] * 100 / $RateUserCount_v1;
			echo '<div class="-Rate-Average-element">';
				echo '<em>'.$q.'</em>';
				echo '<div class="-Rate-Average-Label"><div class="-Average--progress" data-progressload="'.$AverageCalc.'"></div></div>';
				echo '<span>'.$RatingData[ $q ].'</span>';
			echo '</div>';
		}
	}

	$HTML = ob_get_clean();
	$json['output'] = $HTML;	

	#
	$json['RateUserCount_v1'] = $RateUserCount_v1;
	$json['RatingValue_v1'] = $RatingValue_v1;


	$UsersTotalRate = $RateUserCount_v1 * 5;
	$TotalValue = $RatingValue_v1 * 5 / $UsersTotalRate;

	update_term_meta($_POST['id'],'TotalRate_v1',$TotalValue);

	$json['TotalValue'] = $TotalValue;

}else{
	$post = get_post($_POST['id']);

	if( $post->post_status == 'publish' ) {

		$RatingValue_v1 = (INT) get_post_meta( $_POST['id'],'RatingValue_v1',true );
		$RateUserCount_v1 = (INT) get_post_meta($_POST['id'], 'RateUserCount_v1', true);

		$RatingData = ( is_array( get_post_meta( $_POST['id'], 'RateUsersData_v1', true) ) ) ? get_post_meta($_POST['id'], 'RateUsersData_v1', true) : array();

		for ($i=1; $i < 6; $i++) { 
			if( !isset( $RatingData[$i] ) ) $RatingData[$i] = 0;
		}

		if( isset( $_POST[ 'LastValueRate' ] ) ){
			if( $RatingData[ $_POST[ 'LastValueRate' ] ] > 0 ) $RatingData[ $_POST[ 'LastValueRate' ] ] = $RatingData[ $_POST[ 'LastValueRate' ] ] - 1;
		}

		$RatingData[ $_POST[ 'RateValue' ] ] = $RatingData[ $_POST[ 'RateValue' ] ] + 1;

		update_post_meta(  $_POST['id'], 'RateUsersData_v1',$RatingData );
		#

		if( !isset( $_POST[ 'LastValueRate' ] ) ){
			$RateUserCount_v1 = $RateUserCount_v1 + 1;
			update_post_meta($_POST['id'], 'RateUserCount_v1', $RateUserCount_v1);
		}
		$RatingValue_v1 = ( ( $RatingValue_v1 > $_POST[ 'LastValueRate' ] ) ) ? $RatingValue_v1 - $_POST[ 'LastValueRate' ] : 0;
		#
		$RatingValue_v1 = $RatingValue_v1 + $_POST[ 'RateValue' ];
		update_post_meta($_POST['id'], 'RatingValue_v1', $RatingValue_v1 );

		# OUTPUT.
		for ($q=5; $q >= 1; $q--) { 
			if( isset( $RatingData[ $q ] ) ){
				$AverageCalc = $RatingData[ $q ] * 100 / $RateUserCount_v1;
				$AverageCalc = round($AverageCalc,1);
				echo '<div class="-Rate-Average-element">';
					echo '<em>'.$q.'</em>';
					echo '<div class="-Rate-Average-Label"><div class="-Average--progress" data-progressload="'.$AverageCalc.'"></div></div>';
					echo '<span>'.$AverageCalc.'%</span>';
				echo '</div>';
			}
		}

		$HTML = ob_get_clean();
		$json['output'] = $HTML;
		#
		$json['RateUserCount_v1'] = $RateUserCount_v1;
		$json['RatingValue_v1'] = $RatingValue_v1;


		$UsersTotalRate = $RateUserCount_v1 * 5;
		$TotalValue = $RatingValue_v1 * 5 / $UsersTotalRate;
		$TotalValue = round($TotalValue,1);
		update_post_meta($_POST['id'],'TotalRate_v1',$TotalValue);

		$json['TotalValue'] = $TotalValue;
	}
}
echo json_encode($json);
