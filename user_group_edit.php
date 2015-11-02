<?php

$page = "user_group_edit";
include "header.php";

if(isset($_POST['group_id'])) { $group_id = $_POST['group_id']; } elseif(isset($_GET['group_id'])) { $group_id = $_GET['group_id']; } else { $group_id = 0; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['justadded'])) { $justadded = $_POST['justadded']; } elseif(isset($_GET['justadded'])) { $justadded = $_GET['justadded']; } else { $justadded = ""; }

// ENSURE GROUPS ARE ENABLED FOR THIS USER
if( ~(int)$user->level_info['level_group_allow'] & 2 )
{
  header("Location: user_home.php");
  exit();
}

// INITIALIZE GROUP OBJECT
$group = new se_group($user->user_info[user_id], $group_id);

if( !$group->group_exists || $group->user_rank<=0 )
{
  header("Location: user_group.php");
  exit();
}


// INITIALIZE VARIABLES
$is_error = 0;
$result = 0;


// DELETE GROUP
if($task == "delete_do" && $group->user_rank == 2) {
  $group->group_delete($group->group_info[group_id]);
  header("Location: user_group.php");
  exit();
}


// DELETE PHOTO
if($task == "remove" && $group->groupowner_level_info['level_group_photo'] != 0) { 
  $group->group_photo_delete(); 
  $group->group_lastupdate(); 
}

// UPLOAD PHOTO
if($task == "upload" && $group->groupowner_level_info['level_group_photo'] != 0) {
  $group->group_photo_upload("photo");
  $is_error = $group->is_error;
  if($is_error == 0) { $group->group_lastupdate(); }
}






if($task == "dosave") {
  // GET BASIC GROUP INFO
  $old_group_title = $group->group_info['group_title'];
  $group->group_info['group_title'] = censor($_POST['group_title']);
  $group->group_info['group_desc'] = censor(str_replace("\r\n", "<br>", $_POST['group_desc']));
  $group->group_info['group_groupcat_id'] = $_POST['group_groupcat_id'];
  $group->group_info['group_groupsubcat_id'] = $_POST['group_groupsubcat_id'];

  // GET FIELDS
  $field = new se_field("group", $group->groupvalue_info);
  $field->cat_list(1, 0, 0, "groupcat_id='{$group->group_info['group_groupcat_id']}'", "", "");
  $selected_fields = $field->fields_all;
  $is_error = $field->is_error; 
 
  // CHECK TO MAKE SURE TITLE HAS BEEN ENTERED
  if(str_replace(" ", "", $group->group_info['group_title']) == "") { $is_error = 2000115; $group->group_info['group_title'] = $old_group_title; }

  // CHECK TO MAKE SURE CATEGORY HAS BEEN SELECTED
  if($group->group_info['group_groupcat_id'] == 0) { $is_error = 2000117; }

  // SET GROUP CATEGORY ID
  if($group->group_info['group_groupsubcat_id'] != "" && $group->group_info['group_groupsubcat_id'] != 0) { $group->group_info['group_groupcat_id'] = $group->group_info['group_groupsubcat_id']; }

  // IF NO ERROR, SAVE GROUP
  if($is_error == 0) {

    // UPDATE GROUP VALUES
    $database->database_query("UPDATE se_groupvalues SET {$field->field_query} WHERE groupvalue_group_id='{$group->group_info['group_id']}'");

    // UPDATE GROUP
    $database->database_query("UPDATE se_groups SET group_title='{$group->group_info['group_title']}', group_groupcat_id='{$group->group_info['group_groupcat_id']}', group_desc='{$group->group_info['group_desc']}' WHERE group_id='{$group->group_info['group_id']}'");

    // RESET RESULTS
    $group->groupvalue_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_groupvalues WHERE groupvalue_group_id='{$group->group_info['group_id']}'"));

    // SET RESULT MESSAGE
    $result = 1;

    $group->group_lastupdate();
  }


}




// GET FIELDS
$field = new se_field("group", $group->groupvalue_info);
$field->cat_list(0, 0, 0, "", "", "");
$cat_array = $field->cats;
if($is_error != 0) {
  $selected_cat_array = array_filter($cat_array, create_function('$a', 'if($a[cat_id] == "'.$group->group_info['group_groupcat_id'].'") { return $a; }'));
  while(list($key, $val) = each($selected_cat_array)) {
    $cat_array[$key][fields] = $selected_fields;
  }
}


// GET SUBCAT IF NECESSARY
$thiscat = $database->database_fetch_assoc($database->database_query("SELECT groupcat_id, groupcat_dependency FROM se_groupcats WHERE groupcat_id='{$group->group_info['group_groupcat_id']}' LIMIT 1"));
if($thiscat['groupcat_dependency'] == 0) {
  $group->group_info['group_groupsubcat_id'] = 0;
} else {
  $group->group_info['group_groupsubcat_id'] = $group->group_info['group_groupcat_id'];
  $group->group_info['group_groupcat_id'] = $thiscat['groupcat_dependency'];
}

// REMOVE BREAKS
$group->group_info['group_desc'] = str_replace("<br>", "\r\n", $group->group_info['group_desc']);


// ASSIGN VARIABLES AND SHOW USER EDIT GROUP PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('justadded', $justadded);
$smarty->assign('group', $group);
$smarty->assign('cats', $cat_array);
include "footer.php";
?>