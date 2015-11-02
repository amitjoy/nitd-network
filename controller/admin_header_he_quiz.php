<?php

defined('SE_PAGE') or exit();

include "../include/class_he_database.php";
include "../include/class_he_quiz.php";

include "../include/functions_he_quiz.php";


// SET HOOKS
SE_Hook::register("se_user_delete", 'he_quiz_delete_user');
?>