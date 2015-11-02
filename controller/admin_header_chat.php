<?php

defined('SE_PAGE') or exit();

include "../include/functions_chat.php";

SE_Hook::register("se_site_statistics", 'site_statistics_chat');

?>