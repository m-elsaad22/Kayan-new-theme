<?php 
function DisplayDate($time) {
	if( date('Y', $time) != date('Y') ) {
		$displayed = date_i18n('l, d F Y', $time);
	}else {
		if( date('Y-m-d', $time) == date('Y-m-d') ) {
			$displayed = 'منذ '.human_time_diff( date('U', $time), current_time('timestamp') );
		}else {
			$displayed = date_i18n('l, d F', $time);
		}
	}
	return $displayed;
}