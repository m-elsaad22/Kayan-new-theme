<?php 
function NumberReader($num) {
    $western_arabic = array('0','1','2','3','4','5','6','7','8','9');
    $eastern_arabic = array('0','1','2','3','4','5','6','7','8','9');

    if($num>1000) {
        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array(' ألف', ' مليون', ' مليار', ' تريليون');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '٫' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        $x_display = str_replace($western_arabic, $eastern_arabic, $x_display);
        return $x_display;
    }

    $num = str_replace($western_arabic, $eastern_arabic, $num);
    return $num;
}
