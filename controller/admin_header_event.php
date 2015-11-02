<?php
// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
defined('SE_PAGE') or exit();

// INCLUDE EVENTS CLASS FILE
include "../include/class_event.php";

// INCLUDE EVENTS FUNCTION FILE
include "../include/functions_event.php";


// SET HOOKS
SE_Hook::register("se_user_delete", 'deleteuser_event');

SE_Hook::register("se_site_statistics", 'site_statistics_event');

?>