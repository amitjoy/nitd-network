<?php

$page = "help_tos";
include "header.php";

$terms_of_service = SE_Language::get(1210);

// SET GLOBAL PAGE TITLE/DESCRIPTION
$global_page_title[0] = 753;
$global_page_description[0] = 1157;
$global_page_description[1] = substr($terms_of_service, 0, 150);

// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('terms_of_service', htmlspecialchars_decode($terms_of_service, ENT_QUOTES));
include "footer.php";
?>