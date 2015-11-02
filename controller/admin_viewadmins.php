<?php

$page = "admin_viewadmins";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// DELETE ADMIN
if($task == "delete")
{
  $admin_id = $_GET['admin_id'];
  $diff_admin = new se_admin($admin_id);
  if($diff_admin->admin_exists == 0 || $diff_admin->admin_super == 1) { header("Location: admin_viewadmins.php"); exit(); }
  $diff_admin->admin_delete();
}

// CREATE ADMIN
elseif($task == "create")
{
  // GET POST VARIABLES
  $admin_username = strtolower($_POST['admin_username']);
  $admin_password = $_POST['admin_password'];
  $admin_password_confirm = $_POST['admin_password_confirm'];
  $admin_name = $_POST['admin_name'];
  $admin_email = $_POST['admin_email'];

  $diff_admin = new se_admin();
  $diff_admin->admin_account($admin_username, "", $admin_password, $admin_password_confirm, $admin_name, $admin_email);
  $is_error = $diff_admin->is_error;
  if( $is_error )
  {
    SE_Language::_preload_multi($is_error);
    SE_Language::load();
    $error_message = SE_Language::_get($is_error);
  }

  // ADD NEW ADMIN TO DATABASE IF NO ERROR
  if( !$is_error )
  {
    $diff_admin->admin_create($admin_username, $admin_password, $admin_name, $admin_email);
  }

  // RUN JAVASCRIPT TO UPDATE MAIN PAGE
  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
  echo "window.parent.createResult('$is_error', '".str_replace("'", "&#039;", $error_message)."');";
  echo "</script></head><body></body></html>";
  exit();
}

// EDIT ADMIN
elseif($task == "edit")
{
  $admin_id = $_POST['admin_id'];
  $admin_username = strtolower($_POST['admin_username']);
  $admin_name = $_POST['admin_name'];
  $admin_email = $_POST['admin_email'];
  $admin_old_password = $_POST['admin_old_password'];
  $admin_password = $_POST['admin_password'];
  $admin_password_confirm = $_POST['admin_password_confirm'];

  $diff_admin = new se_admin($admin_id);
  if( !$diff_admin->admin_exists ) exit('whoops');
  if( !$admin->admin_super && $admin->admin_info['admin_id'] != $diff_admin->admin_info['admin_id'] ) exit('whoops');

  $diff_admin->admin_account($admin_username, $admin_old_password, $admin_password, $admin_password_confirm, $admin_name, $admin_email);
  $is_error = $diff_admin->is_error;
  if( $is_error )
  {
    SE_Language::_preload_multi($is_error);
    SE_Language::load();
    $error_message = SE_Language::_get($is_error);
  }

  // EDIT ADMIN IN DATABASE
  if( !$is_error )
  {
    $diff_admin->admin_edit($admin_username, $admin_password, $admin_name, $admin_email);
  }

  // RUN JAVASCRIPT TO UPDATE MAIN PAGE
  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
  echo "window.parent.createResult('$is_error', '".str_replace("'", "&#039;", $error_message)."');";
  echo "</script></head><body></body></html>";
  exit();

}



// SELECT AND LOOP THROUGH ADMINS
$admin_array = array();
$admins = $database->database_query("SELECT * FROM se_admins ORDER BY admin_id");
while($admin_info = $database->database_fetch_assoc($admins))
{
  $admin_array[] = array(
    'admin_id' => $admin_info['admin_id'],
    'admin_username' => $admin_info['admin_username'],
    'admin_name' => $admin_info['admin_name'],
    'admin_email' => $admin_info['admin_email'],
    'admin_status' => !empty($admin_array)
  );
}








// ASSIGN VARIABLES AND SHOW VIEW ADMINS PAGE
$smarty->assign('admins', $admin_array);
include "admin_footer.php";
?>