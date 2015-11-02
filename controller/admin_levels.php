<?php

$page = "admin_levels";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }


// CREATE NEW USER LEVEL
if($task == "create") {
  $level_name = $_POST['level_name'];
  $level_desc = $_POST['level_desc'];

  // IF NAME EXISTS, CREAT LEVEL
  if(trim($level_name) != "") {

    // GET DEFAULT LEVEL INFO
    $sql = "SELECT * FROM se_levels WHERE level_default=1 LIMIT 1";
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    $new_level_defaults = $database->database_fetch_assoc($resource);
     
    // SET NEW LEVEL INFO
    unset($new_level_defaults['level_id']);
    $new_level_defaults['level_name'] = $level_name;
    $new_level_defaults['level_desc'] = $level_desc;
    $new_level_defaults['level_default'] = 0;
     
    // ESCAPE
    foreach( $new_level_defaults as $nld_key=>$nld_value )
      $new_level_defaults[$nld_key] = $database->database_real_escape_string($nld_value);
     
    // GENERATE QUERY
    $sql  = "INSERT INTO se_levels (";
    $sql .= join(",", array_keys($new_level_defaults));
    $sql .= ") VALUES ('";
    $sql .= join("','", array_values($new_level_defaults));
    $sql .= "')";
     
    $resource = $database->database_query($sql) or die($database->database_error()." SQL: ".$sql);
    $level_id = $database->database_insert_id();
     
    // REDIRECT
    if( $level_id ) header("Location: admin_levels_edit.php?level_id=$level_id");
    exit();
  }


// SET DEFAULT USER LEVEL
} elseif($task == "savechanges") {

  $default = $_GET['default'];
  if($database->database_num_rows($database->database_query("SELECT level_id FROM se_levels WHERE level_id='$default'")) == 1) {
    $default_level = $database->database_fetch_assoc($database->database_query("SELECT level_id FROM se_levels WHERE level_default='1' LIMIT 1"));
    $database->database_query("UPDATE se_levels SET level_default='0' WHERE level_id='$default_level[level_id]'");
    $database->database_query("UPDATE se_levels SET level_default='1' WHERE level_id='$default'");
  }


// DELETE USER LEVEL
} elseif($task == "delete") {
  $level_id = $_GET['level_id'];

  // DELETE USER LEVEL AND MOVE ALL USERS TO DEFAULT LEVEL IF NOT DEFAULT
  if($database->database_num_rows($database->database_query("SELECT level_id FROM se_levels WHERE level_id='$level_id' AND level_default<>'1'")) == 1) { 
    $default_level = $database->database_fetch_assoc($database->database_query("SELECT level_id FROM se_levels WHERE level_default='1' LIMIT 1"));
    $database->database_query("DELETE FROM se_levels WHERE level_id='$level_id'");
    $database->database_query("UPDATE se_users SET user_level_id='$default_level[level_id]' WHERE user_level_id='$level_id'");
  }
}




// SET USER LEVEL SORT-BY VARIABLES FOR HEADING LINKS
$i = "id";   // LEVEL_ID
$n = "n";    // LEVEL_NAME
$u = "ud";    // NUMBER OF USERS

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "i") {
  $sort = "level_id";
  $i = "id";
} elseif($s == "id") {
  $sort = "level_id DESC";
  $i = "i";
} elseif($s == "n") {
  $sort = "level_name";
  $n = "nd";
} elseif($s == "nd") {
  $sort = "level_name DESC";
  $n = "n";
} elseif($s == "u") {
  $sort = "users";
  $u = "ud";
} elseif($s == "ud") {
  $sort = "users DESC";
  $u = "u";
} else {
  $sort = "level_id DESC";
  $i = "i";
}



// GET USER LEVEL ARRAY
$levels = $database->database_query("SELECT se_levels.*, count(se_users.user_id) AS users FROM se_levels LEFT JOIN se_users ON se_levels.level_id=se_users.user_level_id GROUP BY se_levels.level_id ORDER BY $sort");
while($level_info = $database->database_fetch_assoc($levels)) { $level_array[] = $level_info; }




// ASSIGN VARIABLES AND SHOW ADMIN USER LEVELS PAGE
$smarty->assign('s', $s);
$smarty->assign('i', $i);
$smarty->assign('n', $n);
$smarty->assign('u', $u);
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('levels', $level_array);
include "admin_footer.php";
?>