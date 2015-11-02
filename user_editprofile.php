<?php

$page = "user_editprofile";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['cat_id'])) { $cat_id = $_POST['cat_id']; } elseif(isset($_GET['cat_id'])) { $cat_id = $_GET['cat_id']; } else { $cat_id = NULL; }



if( is_null($cat_id) )
{
  $cat_query = $database->database_query("SELECT t2.profilecat_id AS profilecat_id, COUNT(profilefield_id) AS total_fields FROM se_profilecats AS t1 LEFT JOIN se_profilecats AS t2 ON t1.profilecat_id=t2.profilecat_dependency LEFT JOIN se_profilefields ON t2.profilecat_id=se_profilefields.profilefield_profilecat_id WHERE profilefield_id IS NOT NULL AND t1.profilecat_id='{$user->user_info['user_profilecat_id']}' GROUP BY t2.profilecat_id ORDER BY t2.profilecat_order LIMIT 1"); 
  if($database->database_num_rows($cat_query) == 1)
  {
    $cat = $database->database_fetch_assoc($cat_query);
    $cat_id = $cat['profilecat_id'];
  }
  elseif($user->level_info['level_photo_allow'] != 0)
  {
    header("Location: user_editprofile_photo.php");
    exit();
  }
  else
  {
    header("Location: user_editprofile_settings.php");
    exit();
  }
}

// INITIALIZE VARIABLES
$result = 0;
$is_error = 0;


// VALIDATE CAT ID
if($task == "dosave") { $validate = 1; } else { $validate = 0; }
$field = new se_field("profile", $user->profile_info);
$field->cat_list($validate, 0, 0, "profilecat_id='{$user->user_info['user_profilecat_id']}'", "profilecat_id='{$cat_id}'");
$field_array = $field->fields;
if($validate == 1) { $is_error = $field->is_error; }
if(count($field_array) == 0) { header("Location: user_editprofile.php"); exit(); }

// SAVE PROFILE FIELDS
if($task == "dosave" && $is_error == 0)
{
  // SAVE PROFILE VALUES
  $profile_query = "UPDATE se_profilevalues SET {$field->field_query} WHERE profilevalue_user_id='{$user->user_info['user_id']}'";
  $database->database_query($profile_query);
  
  
  // Flush cached data
  $user->profile_info = NULL;
  $user->profile_info =& SEUser::getProfileValues($user->user_info['user_id']);
  
  $cache_object = SECache::getInstance();
  if( is_object($cache_object) )
  {
    $cache_object->remove('site_user_profiles_'.$user->user_info['user_id']);
  }
  
  /*
  $profilevalues_static =& SEUser::getProfileValues($user->user_info['user_id']);
  $profilevalues_static = NULL;
  
   = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_profilevalues WHERE profilevalue_user_id='".$user->user_info[user_id]."'"));
  //$user->profile_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_profilevalues WHERE profilevalue_user_id='".$user->user_info[user_id]."'"));
  */
  
  
  // SAVE FIRST/LAST NAME, IF RELEVANT
  if(isset($field->field_special[2])) { $flquery[] = "user_fname='".$field->field_special[2]."'"; }
  if(isset($field->field_special[3])) { $flquery[] = "user_lname='".$field->field_special[3]."'"; }
  if(count($flquery) != 0) { $database->database_query("UPDATE se_users SET ".implode(", ", $flquery)." WHERE user_id='{$user->user_info['user_id']}'"); }
  
  // UPDATE CACHED DISPLAYNAME
  $user->user_displayname_update($field->field_special[2], $field->field_special[3]);
  
  
  // SET SUBNETWORK
  $subnet = $user->user_subnet_select($user->user_info['user_email'], $user->user_info['user_profilecat_id'], $user->profile_info); 
  if($subnet[0] != $user->user_info['user_subnet_id'])
  {
    $database->database_query("UPDATE se_users SET user_subnet_id='{$subnet[0]}' WHERE user_id='{$user->user_info['user_id']}'");
    $user->user_info['user_subnet_id'] = $subnet[0];
    $user->subnet_info['subnet_id'] = $subnet[0];
    $user->subnet_info['subnet_name'] = $subnet[1];
    $result = 2;
  }
  else
  {
    $result = 1;
  }

  $user->user_lastupdate();

  // INSERT ACTION
  $actions->actions_add($user, "editprofile", Array($user->user_info['user_username'], $user->user_displayname), Array(), 1800, false, "user", $user->user_info['user_id'], $user->user_info['user_privacy']);
}





// GET TABS TO DISPLAY ON TOP MENU
$field->cat_list(0, 0, 0, "profilecat_id='{$user->user_info['user_profilecat_id']}'", "", "profilefield_id=0");
$cat_array = $field->subcats;


// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('cat_id', $cat_id);
$smarty->assign('cats', $cat_array);
$smarty->assign('fields', $field_array);
$smarty->assign('old_subnet_name', $subnet[2]);
$smarty->assign('new_subnet_name', $subnet[1]);
include "footer.php";
?>