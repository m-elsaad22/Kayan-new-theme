<?php function NumberOfWords($string) {

	$string = strip_tags($string);

	$number = 0;

	#

	foreach( explode(' ', $string) as $word ) {

		if( mb_strlen($word) >= 10 ) {
			$numberofwords__in = mb_strlen($word) / 4;
			$numberofwords__in = ( ( strpos( $numberofwords__in,'.' ) !== FALSE ) ) ? explode('.', $numberofwords__in)[0] : $numberofwords__in;

			$number = $number + $numberofwords__in;
		}else if( mb_strlen($word) > 1 ) {

			$number++;

		}

	}

	#

	return $number;

}