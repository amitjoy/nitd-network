<?php

$page = "admin_login";
include "admin_header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : "main" );


// SET DEFAULT
$is_error = 0;


// TRY TO LOGIN
if($task == "dologin") {
  // pasting username/password sometimes contains linebreak character
  // so gracefully trim before attempting to log in.
  $_POST['username'] = trim($_POST['username']);
  $_POST['password'] = trim($_POST['password']);
  
  $admin->admin_login();

  // IF ADMIN IS LOGGED IN SUCCESSFULLY, FORWARD THEM TO HOMEPAGE
  if($admin->is_error == 0) {
    cheader("admin_announcements.php");
    exit();

  // IF THERE WAS AN ERROR, SET ERROR MESSAGE
  } else {
    $is_error = $admin->is_error;
  }

}



// INCLUDE FOOTER
$smarty->assign('is_error', $is_error);
include "admin_footer.php";
?>