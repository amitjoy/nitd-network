<?php

$page = "admin_profile";
include "admin_header.php";


// GET TABS AND FIELDS
$field = new se_field("profile");
$field->cat_list();
$cat_array = $field->cats;


// ASSIGN VARIABLES AND SHOW ADMIN PROFILE PAGE
$smarty->assign('cats', $cat_array);
include "admin_footer.php";
?>