<?php

$page = "user_account_pass";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// SET EMPTY VARS
$is_error = 0;
$result = 0;


// SAVE NEW PASSWORD
if($task == "dosave")
{
  $password_old = $_POST['password_old'];
  $password_new = $_POST['password_new'];
  $password_new2 = $_POST['password_new2'];

  $user->user_password($password_old, $password_new, $password_new2);
  $is_error = $user->is_error;

  // IF THERE WAS NO ERROR, SAVE CHANGES
  if($is_error == 0)
  {
    // ENCRYPT NEW PASSWORD WITH MD5
    $password_new_crypt = $user->user_password_crypt($password_new);
    
    // UPDATE DATABASE AND RESET USER INFO
    $database->database_query("UPDATE se_users SET user_password='$password_new_crypt' WHERE user_id='{$user->user_info['user_id']}' LIMIT 1");
    $user = new se_user(Array($user->user_info['user_id']));
    
    // UPDATE COOKIES
    $user->user_setcookies(); 
    
    // SET RESULT
    $result = 1;
  }
}


// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
include "footer.php";
?>