<?php

$page = "user_account_delete";
include "header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : NULL );
$delete_token = ( isset($_POST['token']) ? $_POST['token'] : NULL );

if( !$user->level_info['level_profile_delete'] )
{
  header("Location: user_account.php");
  exit();
}

// DELETE THIS USER
if( $task == "dodelete" )
{
  if( empty($_SESSION['delete_token']) || $_SESSION['delete_token']!=$delete_token || time()>$_SESSION['delete_token_expires'] )
  {
    $result = FALSE;
  }
  else
  {
    $user->user_delete();
    $user->user_setcookies();
    $result = TRUE;
  }
  
  header("Content-Type: application/json");
  echo json_encode(array('result' => $result));
  exit();
}


// Nothing wrong with being extra safe, right?
$_SESSION['delete_token'] = md5(uniqid(rand(), TRUE));
$_SESSION['delete_token_expires'] = time() + 300;


// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
$smarty->assign('delete_token', $_SESSION['delete_token']);
include "footer.php";
?>