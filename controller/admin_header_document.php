<?php


defined('SE_PAGE') or exit();

// INCLUDE DOCUMENT CLASS FILE
include "../include/class_document.php";

// INCLUDE DOCUMENT FUNCTION FILE
include "../include/functions_document.php";


// SET HOOKS
SE_Hook::register("se_user_delete", "deleteuser_document");

SE_Hook::register("se_site_statistics", "site_statistics_document");


?>