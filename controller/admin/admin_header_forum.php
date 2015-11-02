<?php

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

// INCLUDE FORUMS CLASS FILE
include "../include/class_forum.php";

// INCLUDE FORUMS FUNCTION FILE
include "../include/functions_forum.php";


// SET USER DELETION HOOK
SE_Hook::register("se_user_delete", 'deleteuser_forum');

SE_Hook::register("se_site_statistics", 'site_statistics_forum');

?>