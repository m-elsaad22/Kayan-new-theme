<?php 
function YC_stripslashes_deep($value) {
    $value = is_array($value) ? array_map('YC_stripslashes_deep', $value) : stripslashes($value);
    return $value;
}