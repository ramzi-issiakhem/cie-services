<?php

// connect to data base
// include 'connect.php';

$tpl = "includes/templates/";
$css = "layout/css/";
$js = "layout/js/";
$lang = "includes/languages/";
$func = "includes/functions/";

include $lang . 'french.php';
include $func . 'fucntions.php';


// header
include $tpl . "header.php";

// navbar on all pages except the one with $noNavbar variable
// include $tpl . "navbar.php";
