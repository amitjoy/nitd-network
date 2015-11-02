<?php

$page = "admin_fields";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['type'])) { $type = $_POST['type']; } elseif(isset($_GET['type'])) { $type = $_GET['type']; } else { exit(); }
if(isset($_POST['hideSearch'])) { $hideSearch = $_POST['hideSearch']; } elseif(isset($_GET['hideSearch'])) { $hideSearch = $_GET['hideSearch']; }
if(isset($_POST['hideDisplay'])) { $hideDisplay = $_POST['hideDisplay']; } elseif(isset($_GET['hideDisplay'])) { $hideDisplay = $_GET['hideDisplay']; }
if(isset($_POST['hideSpecial'])) { $hideSpecial = $_POST['hideSpecial']; } elseif(isset($_GET['hideSpecial'])) { $hideSpecial = $_GET['hideSpecial']; }


// INITIALIZE FIELD OBJECT
$field = new se_field($type);



// Flush cached stuff
if( !empty($_GET['cat_id']) && is_object($cache_object) )
{
  $cache_object->remove('site_profile_categories_'.$_GET['cat_id']);
}




// SAVE CATEGORY
if($task == "savecat")
{
  $cat_id = $_GET['cat_id'];
  $cat_title = $_GET['cat_title'];
  $cat_dependency = $_GET['cat_dependency'];

  // IF CAT TITLE IS BLANK, DELETE
  if($cat_title == "") {

    if($cat_id != "new") { $field->cat_delete($cat_id); }

    // SEND AJAX CONFIRMATION
    echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
    echo "window.parent.removecat('$cat_id');";
    echo "</script></head><body></body></html>";
    exit();

  // SAVE CHANGES
  } else {

    $newcat_id = $field->cat_modify($cat_id, $cat_title, $cat_dependency);

    // SEND AJAX CONFIRMATION
    echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
    echo "window.parent.savecat_result('$cat_id', '$newcat_id', '$cat_title', '$cat_dependency');";
    echo "</script></head><body></body></html>";
    exit();

  }



// CHANGE ORDER OF CATS/FIELDS
} elseif($task == "changeorder") {
  $listorder = $_GET['listorder'];
  $divId = $_GET['divId'];

  $list = explode(",", $listorder);

  // RESORT CATEGORIES
  if($divId == "categories") {
    for($i=0;$i<count($list);$i++) {
      $cat_id = substr($list[$i], 4);
      $database->database_query("UPDATE se_".$type."cats SET ".$type."cat_order='".($i+1)."' WHERE ".$type."cat_id='$cat_id'");
    }

  // RESORT SUBCATEGORIES
  } elseif(substr($divId, 0, 7) == "subcats") {
    for($i=0;$i<count($list);$i++) {
      $cat_id = substr($list[$i], 4);
      $database->database_query("UPDATE se_".$type."cats SET ".$type."cat_order='".($i+1)."' WHERE ".$type."cat_id='$cat_id'");
    }

  // RESORT FIELDS
  } elseif(substr($divId, 0, 6) == "fields") {
    for($i=0;$i<count($list);$i++) {
      $field_id = substr($list[$i], 6);
      $database->database_query("UPDATE se_".$type."fields SET ".$type."field_order='".($i+1)."' WHERE ".$type."field_id='$field_id'");
    }

  }



// GET FIELD
} elseif($task == "getfield") {
  $field_id = $_GET['field_id'];
  
  $field_info = $field->field_get($field_id);

  // PULL OPTIONS INTO STRING
  $field_options_detailed = Array();
  for($i=0;$i<count($field_info[field_options_detailed]);$i++) {
    $field_options_detailed[] = $field_info[field_options_detailed][$i][value]."<!>".$field_info[field_options_detailed][$i][label]."<!>".$field_info[field_options_detailed][$i][dependency]."<!>".$field_info[field_options_detailed][$i][dependent_label]."<!>".$field_info[field_options_detailed][$i][dependent_id];
  }
  $field_options_detailed = implode("<~!~>", $field_options_detailed);

  $field->cat_list();
  $cat_array = $field->cats;
  $smarty->assign('hideSearch', $hideSearch);
  $smarty->assign('hideDisplay', $hideDisplay);
  $smarty->assign('hideSpecial', $hideSpecial);
  $smarty->assign('cats', $cat_array);
  $smarty->assign('cat_type', $type);
  $smarty->assign('function', "editfield('$field_info[field_id]', '$field_info[field_cat_id]', '".str_replace("'", "\'", htmlspecialchars_decode($field_info[field_title], ENT_QUOTES))."', '".str_replace("'", "\'", htmlspecialchars_decode($field_info[field_desc], ENT_QUOTES))."', '".str_replace("'", "\'", htmlspecialchars_decode($field_info[field_error], ENT_QUOTES))."', '$field_info[field_type]', '$field_info[field_style]', '$field_info[field_maxlength]', '$field_info[field_link]', '$field_options_detailed', '$field_info[field_required]', '$field_info[field_regex]', '$field_info[field_html]', '$field_info[field_search]', '$field_info[field_display]', '$field_info[field_special]');");
  $smarty->display("$page.tpl");
  exit();
  



// GET DEPENDENT FIELD
} elseif($task == "getdepfield") {
  $field_id = $_GET['field_id'];
  
  $field_info = $field->field_get($field_id);

  // PULL OPTIONS INTO STRING
  $field_options_detailed = Array();
  for($i=0;$i<count($field_info[field_options_detailed]);$i++) {
    $field_options_detailed[] = $field_info[field_options_detailed][$i][value]."<!>".$field_info[field_options_detailed][$i][label]."<!>".$field_info[field_options_detailed][$i][dependency]."<!>".$field_info[field_options_detailed][$i][dependent_label]."<!>".$field_info[field_options_detailed][$i][dependent_id];
  }
  $field_options_detailed = implode("<~!~>", $field_options_detailed);

  $field->cat_list();
  $cat_array = $field->cats;
  $smarty->assign('hideDisplay', $hideDisplay);
  $smarty->assign('cats', $cat_array);
  $smarty->assign('cat_type', $type);
  $smarty->assign('function', "editdepfield('$field_info[field_id]', '$field_info[field_cat_id]', '$field_info[field_type]', '".str_replace("'", "\'", htmlspecialchars_decode($field_info[field_title], ENT_QUOTES))."', '$field_info[field_style]', '$field_info[field_maxlength]', '$field_info[field_link]', '$field_info[field_required]', '$field_info[field_regex]', '$field_info[field_display]', '$field_options_detailed');");
  $smarty->display("$page.tpl");
  exit();




// REMOVE FIELD
} elseif($task == "removefield") {
  $field_id = $_GET['field_id'];

  $field->field_delete($field_id);

  // SEND AJAX CONFIRMATION
  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
  echo "window.parent.removefield_result('$field_id');";
  echo "</script></head><body></body></html>";
  exit();


// SAVE DEP FIELD
} elseif($task == "savedepfield") {
  $field_info[field_id] = $_POST['field_id'];
  $field_info[field_title] = $_POST['field_title'];
  if($_POST['field_subcat_id'] != "") { $field_info[field_cat_id] = $_POST['field_subcat_id']; } else { $field_info[field_cat_id] = $_POST['field_cat_id']; }
  $field_info[field_type] = $_POST['field_type'];
  $field_info[field_style] = $_POST['field_style'];
  $field_info[field_required] = $_POST['field_required'];
  $field_info[field_maxlength] = $_POST['field_maxlength'];
  $field_info[field_link] = $_POST['field_link'];
  $field_info[field_regex] = $_POST['field_regex'];
  $field_info[field_display] = $_POST['field_display'];
  $field_info[field_dependency] = $_POST['field_dependency'];
  $field_info[field_options] = $_POST['field_options'];

  $field_info = $field->field_save($field_info);

  // SEND AJAX CONFIRMATION
  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
  echo "window.parent.savedepfield_result('$field_info[field_id]', '$field_info[field_title]');";
  echo "</script></head><body></body></html>";
  exit();


// SAVE FIELD
} elseif($task == "savefield") {
  $old_field_id = $_POST['field_id'];
  $field_info[field_id] = $old_field_id;
  $field_info[field_title] = $_POST['field_title'];
  if($_POST['field_subcat_id'] != "") { $field_info[field_cat_id] = $_POST['field_subcat_id']; } else { $field_info[field_cat_id] = $_POST['field_cat_id']; }
  $field_info[field_type] = $_POST['field_type'];
  $field_info[field_style] = $_POST['field_style'];
  $field_info[field_desc] = $_POST['field_desc'];
  $field_info[field_error] = $_POST['field_error'];
  $field_info[field_required] = $_POST['field_required'];
  $field_info[field_html] = $_POST['field_html'];
  $field_info[field_search] = $_POST['field_search'];
  $field_info[field_display] = $_POST['field_display'];
  $field_info[field_special] = $_POST['field_special'];
  $field_info[field_maxlength] = $_POST['field_maxlength'];
  $field_info[field_link] = $_POST['field_link'];
  $field_info[field_regex] = $_POST['field_regex'];
  $field_info[field_options] = $_POST['field_options'];
  $field_info[field_suggestions] = $_POST['field_suggestions'];

  // SAVE FIELD
  $field_info = $field->field_save($field_info);

  // GET ERROR
  $is_error = $field->is_error;
  if($field->is_error != 0) {
    SE_Language::_preload_multi($field->is_error);
    SE_Language::load();
    $error_message = str_replace("'", "\'", SE_Language::_get($field->is_error));
  }

  // PULL OPTIONS INTO STRING
  $field_options_detailed = Array();
  for($i=0;$i<count($field_info[field_options]);$i++) {
    SE_Language::_preload_multi($field_info[field_options][$i][label]);
    SE_Language::load();
    $field_info[field_options][$i][label] = SE_Language::_get($field_info[field_options][$i][label]);
    $field_options_detailed[] = $field_info[field_options][$i][value]."<!>".$field_info[field_options][$i][label]."<!>".$field_info[field_options][$i][dependency]."<!>".$field_info[field_options][$i][dependent_label]."<!>".$field_info[field_options][$i][dependent_id];
  }
  $field_options_detailed = implode("<~!~>", $field_options_detailed);

  // SEND AJAX CONFIRMATION
  echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
  echo "window.parent.savefield_result('$is_error', '$error_message', '$old_field_id', '$field_info[field_id]', '$field_info[field_title]', '$field_info[field_cat_id]', '$field_options_detailed');";
  echo "</script></head><body></body></html>";
  exit();






// ADD A NEW FIELD BOX
} elseif($task == "addfield") {
  $field->cat_list();
  $cat_array = $field->cats;
  $smarty->assign('hideSearch', $hideSearch);
  $smarty->assign('hideDisplay', $hideDisplay);
  $smarty->assign('hideSpecial', $hideSpecial);
  $smarty->assign('cats', $cat_array);
  $smarty->assign('cat_type', $type);
  $smarty->assign('function', "addfield('".$_GET['cat_id']."');");
  $smarty->display("$page.tpl");
  exit();
}


?>