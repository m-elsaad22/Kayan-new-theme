<?php 
/*
    Dev: YourColor Dev team;
    Function_Name: IsSpeed;
    Function_Role: Check IF User Agent IS Google Speed;
    Return: true (if the user agent is Google )/ false ;
    default : false ;
*/
function IsSpeed() {
    $IsSpeed = false;
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'moto g') !== false) {
        $IsSpeed =  true;
    }
    return $IsSpeed;
}