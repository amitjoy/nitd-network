<?php

defined('SE_PAGE') or exit();

// INCLUDE VIDEO CLASS FILE
include "../include/class_video.php";

// INCLUDE VIDEO FUNCTION FILE
include "../include/functions_video.php";


// SET USER DELETION HOOK
SE_Hook::register("se_user_delete", 'deleteuser_video');
  
SE_Hook::register("se_site_statistics", 'site_statistics_video');

?>