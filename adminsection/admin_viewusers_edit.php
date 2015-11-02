<?php
$page = "admin_viewusers_edit";
include "admin_header.php";

if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['f_user'])) { $f_user = $_POST['f_user']; } elseif(isset($_GET['f_user'])) { $f_user = $_GET['f_user']; } else { $f_user = ""; }
if(isset($_POST['f_email'])) { $f_email = $_POST['f_email']; } elseif(isset($_GET['f_email'])) { $f_email = $_GET['f_email']; } else { $f_email = ""; }
if(isset($_POST['f_level'])) { $f_level = $_POST['f_level']; } elseif(isset($_GET['f_level'])) { $f_level = $_GET['f_level']; } else { $f_level = ""; }
if(isset($_POST['f_subnet'])) { $f_subnet = $_POST['f_subnet']; } elseif(isset($_GET['f_subnet'])) { $f_subnet = $_GET['f_subnet']; } else { $f_subnet = ""; }
if(isset($_POST['f_enabled'])) { $f_enabled = $_POST['f_enabled']; } elseif(isset($_GET['f_enabled'])) { $f_enabled = $_GET['f_enabled']; } else { $f_enabled = ""; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['user_id'])) { $user_id = $_POST['user_id']; } elseif(isset($_GET['user_id'])) { $user_id = $_GET['user_id']; } else { $user_id = 0; }

// VALIDATE USER ID OR RETURN TO VIEW USERS
$user = new se_user(Array($user_id));
if($user->user_exists == 0) { header("Location: admin_viewusers.php?s=$s&p=$p&f_user=$f_user&f_email=$f_email&f_level=$f_level&f_subnet=$f_subnet&f_enabled=$f_enabled"); exit(); }


// INITIALIZE ERROR VARS
$is_error = 0;
$result = 0;




// RESEND EMAIL VERIFICATION
if($task == "resend") {

  $verify_code = md5($user->user_info['user_code']);
  $time = time();
  $verify_link = $url->url_base."signup_verify.php?u=".$user->user_info['user_id']."&verify=$verify_code&d=$time";
  send_systememail('verification', $user->user_info[user_email], Array($user->user_displayname, $user->user_info[user_email], "<a href=\"$verify_link\">$verify_link</a>")); 
  $result = 1140;




// MANUALLY VERIFY USER
} elseif($task == "verify") {

  $database->database_query("UPDATE se_users SET user_verified='1' WHERE user_id='".$user->user_info[user_id]."'");
  $result = 1141;
  $user->user_info[user_verified] = 1;



// DELETE ACTION
} elseif($task == "action_delete") {
  if(isset($_GET['action_id'])) { $action_id = $_GET['action_id']; } else { $action_id = 0; }

  // DELETE ACTION
  $database->database_query("DELETE FROM se_actions, se_actionmedia USING se_actions LEFT JOIN se_actionmedia ON se_actions.action_id=se_actionmedia.actionmedia_action_id WHERE action_id='$action_id'");

  // SEND AJAX CONFIRMATION
  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
  echo "window.parent.action_delete('$action_id');";
  echo "</script></head><body></body></html>";
  exit();




// EDIT USER
} elseif($task == "edituser") {

  // GET POST VARIABLES
  $user_email = $_POST['user_email'];
  $user_username = $_POST['user_username'];
  $user_password = $_POST['user_password'];
  $user_enabled = $_POST['user_enabled'];
  $user_level_id = $_POST['user_level_id'];
  $user_profilecat_id = $_POST['user_profilecat_id'];
  $user_invitesleft = $_POST['user_invitesleft'];

  // VERIFY USER DETAILS
  $user->user_account($user_email, $user_username);
  $is_error = $user->is_error;

  // VERIFY USER PASSWORD (MUST CREATE NEW USER OBJECT SINCE NO OLD PASSWORD IS SPECIFIED)
  if($user_password == "") { $password = "temporary"; } else { $password = $user_password; }
  $new_user = new se_user();
  $new_user->user_password('', $password, $password, 0);
  if($is_error == 0) { $is_error = $new_user->is_error; }


  // CHECK THAT INVITES LEFT IS BETWEEN 0 AND 999
  if(!is_numeric($user_invitesleft) || $user_invitesleft > 999) { $is_error = 1142; }

  // SAVE CHANGES IF NO ERROR
  if($is_error == 0) {

    // SET RESULT
    $result = 191;

    // SET SUBNETWORK
    if(($user_email != $user->user_info[user_email] && ($setting[setting_subnet_field1_id] == 0 || $setting[setting_subnet_field2_id] == 0)) || ($user_profilecat_id != $user->user_info[user_profilecat_id] && ($setting[setting_subnet_field1_id] == -1 || $setting[setting_subnet_field2_id] == -1))) {
      $subnet = $user->user_subnet_select($user_email, $user_profilecat_id, $user->profile_info); 
      if($subnet[0] != $user->user_info[user_subnet_id]) {
        $user->user_info[user_subnet_id] = $subnet[0];
        $user->subnet_info[subnet_id] = $subnet[0];
        $user->subnet_info[subnet_name] = $subnet[1];
        $result = 1143;
      }
    }
    
    // ENCRYPT NEW PASSWORD IF CHANGED
    if( trim($user_password) )
      $user_password = $user->user_password_crypt($user_password);
    else
      $user_password = $user->user_info['user_password'];
    
    // SET USERNAME TO THE SAME IT WAS BEFORE IF NECESSARY
    if( !$setting[setting_username] )
      $user_username = $user->user_info['user_username'];
    
    // EDIT USER AND REFRESH USER DATA
    $database->database_query("UPDATE se_users SET user_level_id='$user_level_id', user_subnet_id='".$user->user_info[user_subnet_id]."', user_profilecat_id='$user_profilecat_id', user_email='$user_email', user_newemail='$user_email', user_username='$user_username', user_password='$user_password', user_enabled='$user_enabled', user_invitesleft='$user_invitesleft' WHERE user_id='".$user->user_info[user_id]."'");
    $user = new se_user(Array($user->user_info[user_id]));
  }
}





// SET USER STATS
$total_friends = $database->database_num_rows($database->database_query("SELECT friend_id FROM se_friends WHERE friend_user_id1='".$user->user_info[user_id]."'"));
$total_messages = $total_pms = $user->user_message_total(0, 0);

// LOOP THROUGH COMMENT TABLES TO GET TOTAL COMMENTS
$total_comments = 0;
$comment_tables = $database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_%comments'");
while($table_info = $database->database_fetch_array($comment_tables)) {
  $comment_type = strrev(substr(strrev(substr($table_info[0], 3)), 8));
  $table_comments = $database->database_fetch_assoc($database->database_query("SELECT count(*) AS total_comments FROM se_".$comment_type."comments WHERE ".$comment_type."comment_authoruser_id='".$user->user_info[user_id]."'"));
  $total_comments += $table_comments[total_comments];
}



// GET USER LEVEL ARRAY
$levels = $database->database_query("SELECT level_id, level_name FROM se_levels ORDER BY level_name");
while($level_info = $database->database_fetch_assoc($levels)) { $level_array[] = $level_info; }

// GET PROFILECAT ARRAY
$cats = $database->database_query("SELECT profilecat_id AS cat_id, profilecat_title AS cat_title FROM se_profilecats WHERE profilecat_dependency='0' ORDER BY profilecat_order");
while($cat_info = $database->database_fetch_assoc($cats)) { SE_Language::_preload($cat_info[cat_title]); $cat_array[] = $cat_info; }

// GET RECENT ACTIVITY (ACTIONS)
$owner = $user;
$actions = new se_actions();
$actions = $actions->actions_display(0, $setting[setting_actions_actionsonprofile], "se_actions.action_user_id='".$user->user_info[user_id]."'");


// ASSIGN VARIABLES AND SHOW EDIT USERS PAGE
$smarty->assign('is_error', $is_error);
$smarty->assign('result', $result);
$smarty->assign('user', $user);
$smarty->assign('levels', $level_array);
$smarty->assign('cats', $cat_array);
$smarty->assign('actions', $actions);
$smarty->assign('old_subnet_name', $subnet[2]);
$smarty->assign('new_subnet_name', $subnet[1]);
$smarty->assign('total_friends', $total_friends);
$smarty->assign('total_messages', $total_messages);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('s', $s);
$smarty->assign('p', $p);
$smarty->assign('f_user', $f_user);
$smarty->assign('f_email', $f_email);
$smarty->assign('f_level', $f_level);
$smarty->assign('f_subnet', $f_subnet);
$smarty->assign('f_enabled', $f_enabled);
include "admin_footer.php";
?>