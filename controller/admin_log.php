<?php

$page = "admin_log";
include "admin_header.php";


// SELECT AND LOOP THROUGH LAST 500 LOGINS
$login_array = Array();
$logins = $database->database_query("SELECT * FROM se_logins ORDER BY login_id DESC LIMIT 300");
while($login_info = $database->database_fetch_assoc($logins)) {

  // SET LOGIN ARRAY
  $login_array[] = Array('login_id' => $login_info[login_id],
			'login_email' => $login_info[login_email],
			'login_date' => $login_info[login_date],
			'login_ip' => $login_info[login_ip],
			'login_result' => $login_info[login_result]);
}


// ASSIGN VARIABLES AND SHOW LOG PAGE
$smarty->assign('logins', $login_array);
include "admin_footer.php";
?>