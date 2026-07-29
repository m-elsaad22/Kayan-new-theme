<?php function StaticThemeDate($date,$formate="j F <p>G:i</p>",$justmonth=false){
	$months = array(
		"January"=> "يناير",
		"February"=> "فبراير",
		"March"=>"مارس",
		"April"=>"أبريل",
		"May"=> "مايو",
		"June"=> "يونيو",
		"July"=> "يوليو",
		"August"=>"أغسطس",
		"September"=>"سبتمبر",
		"October"=>"أكتوبر",
		"November"=> "نوفمبر",
		"December"=> "ديسمبر"
	);
	$en_month = date("F", strtotime($date));	
	$ar_month = $months[$en_month];
	if($justmonth != false){
		return $ar_month;
	}
	$ReturnDated = date($formate, strtotime($date));
	$ReturnDated = str_replace($en_month,$ar_month, $ReturnDated);
	return $ReturnDated;
}
function StaticNameDay($day){
	$months = array(
		"Friday"=> "الجمعة",
		"Saturday"=> "السبت",
		"Sunday"=>"الاحد",
		"Monday"=>"الاثنين",
		"Tuesday"=> "الثلاثاء",
		"Wednesday"=> "الاربعاء",
		"Thursday"=> "الخميس",
	);
	$day = trim($day);
	if(isset($months[$day])){
		return $months[$day];
	}
	return false;
}