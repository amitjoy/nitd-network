<?php

$page = "admin_lostpass";
include "admin_header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : "main" );

// SET ERROR VARS
$is_error = 0;
$submitted = 0;


if($task == "send_email") {

  $admin_email = $_POST['admin_email'];
  $admin_query = $database->database_query("SELECT admin_id FROM se_admins WHERE admin_email='$admin_email' LIMIT 1");
  $submitted = 1;

  if($database->database_num_rows($admin_query) != 1) {
    $is_error = 1;
  } else {
    $lostpassword_code = randomcode(15);
    $lostpassword_time = time();

    $admin_lost = $database->database_fetch_assoc($admin_query);
    $database->database_query("UPDATE se_admins SET admin_lostpassword_code='$lostpassword_code', admin_lostpassword_time='$lostpassword_time' WHERE admin_id='$admin_lost[admin_id]' LIMIT 1");

    $prefix = $url->url_base;
    $link = "<a href=\"$prefix"."admin/admin_lostpass_reset.php?admin_id=$admin_lost[admin_id]&r=$lostpassword_code\">$prefix"."admin/admin_lostpass_reset.php?admin_id=$admin_lost[admin_id]&r=$lostpassword_code</a>";
    SE_Language::_preload_multi(40, 41);
    SE_Language::load();
    send_generic($admin_email, $admin_email, SE_Language::_get(40), SE_Language::_get(41), Array("[link]"), Array($link));

  }
}



// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('is_error', $is_error);
$smarty->assign('submitted', $submitted);
include "admin_footer.php";
?>