<?php

$page = "chat";
include "header.php";
include "include/class_chat.php";


// REDIRECT IF USER IS NOT LOGGED IN OR USER IS NOT ALLOWED TO CHAT
if( !$user->user_exists || !$user->level_info['level_chat_allow'] )
{
  cheader('home.php');
  exit();
}


include "footer.php";
?>