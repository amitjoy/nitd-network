<?php
$page = "admin_viewusers";
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


// DELETE USER
$user = new se_user(Array($user_id));
if($task == "delete" && $user->user_exists != 0) { $user->user_delete(); }







// SET USER SORT-BY VARIABLES FOR HEADING LINKS
$i = "id";   // USER_ID
$u = "u";    // USER_USERNAME
$em = "em";  // USER_EMAIL
$v = "v";    // USER_VERIFIED
$sd = "sd";  // USER_SIGNUPDATE

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "i") {
  $sort = "user_id";
  $i = "id";
} elseif($s == "id") {
  $sort = "user_id DESC";
  $i = "i";
} elseif($s == "u") {
  $sort = "user_username";
  $u = "ud";
} elseif($s == "ud") {
  $sort = "user_username DESC";
  $u = "u";
} elseif($s == "em") {
  $sort = "user_email";
  $em = "emd";
} elseif($s == "emd") {
  $sort = "user_email DESC";
  $em = "em";
} elseif($s == "v") {
  $sort = "user_verified, user_email";
  $v = "vd";
} elseif($s == "vd") {
  $sort = "user_verified DESC, user_email";
  $v = "v";
} elseif($s == "sd") {
  $sort = "user_signupdate";
  $sd = "sdd";
} elseif($s == "sdd") {
  $sort = "user_signupdate DESC";
  $sd = "sd";
} else {
  $sort = "user_id DESC";
  $i = "i";
}







// CONSTRUCT QUERY USING FILTERS
$user_query = "SELECT user_id, user_username, user_fname, user_lname, user_email, user_enabled, user_verified, user_signupdate, user_level_id, user_subnet_id FROM se_users";
$where_clause = Array();
if($f_user != "" && $setting[setting_username]) { $where_clause[] = "user_username LIKE '%$f_user%'"; } elseif($f_user != "" && !$setting[setting_username]) { $where_clause[] = "(user_fname LIKE '%$f_user%' OR user_lname LIKE '%$f_user%')"; }
if($f_email != "") { $where_clause[] = "user_email LIKE '%$f_email%'"; }
if($f_level != "") { $where_clause[] = "user_level_id='$f_level'"; }
if($f_subnet != "") { $where_clause[] = "user_subnet_id='$f_subnet'"; }
if($f_enabled != "") { $where_clause[] = "user_enabled='$f_enabled'"; }
if(count($where_clause) != 0) { $user_query .= " WHERE ".implode(" AND ", $where_clause); }




// GET TOTAL USERS
$total_users = $database->database_num_rows($database->database_query($user_query));

// MAKE USER PAGES
$users_per_page = 100;
$page_vars = make_page($total_users, $users_per_page, $p);

$page_array = Array();
for($x=0;$x<=$page_vars[2]-1;$x++) {
  if($x+1 == $page_vars[1]) { $link = "1"; } else { $link = "0"; }
  $page_array[$x] = Array('page' => $x+1,
			  'link' => $link);
}

$user_query .= " ORDER BY $sort LIMIT $page_vars[0], $users_per_page";




// DELETE MULTIPLE USERS
if($task == "dodelete") {
  $deleted_users = $_POST['delete'];
  for($d=0;$d<count($deleted_users);$d++) {
    $user = new se_user(Array($deleted_users[$d]), Array('user_id'));
    if($user->user_exists != 0) { 
      $user->user_delete(); 
      $total_users = $total_users - 1;
    }
  }
}


// LOOP OVER USER LEVELS
$levels = $database->database_query("SELECT level_id, level_name FROM se_levels ORDER BY level_name");
while($level_info = $database->database_fetch_assoc($levels)) { $level_array[$level_info[level_id]] = $level_info; }


// LOOP OVER SUBNETWORKS
$subnets = $database->database_query("SELECT subnet_id, subnet_name FROM se_subnets ORDER BY subnet_name");
$subnet_array[0] = Array('subnet_id' => 0, 'subnet_name' => 152);
SE_Language::_preload(152);
while($subnet_info = $database->database_fetch_assoc($subnets)) { SE_Language::_preload($subnet_info[subnet_name]); $subnet_array[$subnet_info[subnet_id]] = $subnet_info; }

// PULL USERS INTO AN ARRAY
$users = $database->database_query($user_query);
while($user_info = $database->database_fetch_assoc($users)) {

  $user = new se_user();
  $user->user_info[user_id] = $user_info[user_id];
  $user->user_info[user_username] = $user_info[user_username];
  $user->user_info[user_fname] = $user_info[user_fname];
  $user->user_info[user_lname] = $user_info[user_lname];
  $user->user_displayname();
  $user_info[user_displayname] = $user->user_displayname;

  $user_info[user_level] = $level_array[$user_info[user_level_id]][level_name];
  $user_info[user_subnet] = $subnet_array[$user_info[user_subnet_id]][subnet_name];
  $user_array[] = $user_info;
}



// ASSIGN VARIABLES AND SHOW VIEW USERS PAGE
$smarty->assign('total_users', $total_users);
$smarty->assign('pages', $page_array);
$smarty->assign('users', $user_array);
$smarty->assign('i', $i);
$smarty->assign('u', $u);
$smarty->assign('em', $em);
$smarty->assign('v', $v);
$smarty->assign('sd', $sd);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('s', $s);
$smarty->assign('f_user', $f_user);
$smarty->assign('f_email', $f_email);
$smarty->assign('f_level', $f_level);
$smarty->assign('f_subnet', $f_subnet);
$smarty->assign('f_enabled', $f_enabled);
$smarty->assign('levels', array_values($level_array));
$smarty->assign('subnets', array_values($subnet_array));
include "admin_footer.php";
?>