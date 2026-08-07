<?php 
function TermsTransient($data,$force=false){

	$EncodedQuery =  QueryEncode($data);
	$CachTransit = $EncodedQuery;	
	$CachTransit = md5($CachTransit);
	#
	$NewExpTerms = get_transient($CachTransit);
	if( empty( $MatchesLoop ) || $force == true ) {
		$NewExpTerms = get_terms($data);
		set_transient($CachTransit,$NewExpTerms,3600);
	}
	return $NewExpTerms;
}



