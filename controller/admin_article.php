<?php
$page = "admin_article";
include "admin_header.php";

$task = rc_toolkit::get_request('task','main');

$rc_articlecats = new rc_articlecats();

$result = "";

$rc_validator = new rc_validator();

$keys = array('setting_article_license',
'setting_permission_article',
'setting_email_articlecomment_subject',
'setting_email_articlecomment_message',
'setting_email_articlemediacomment_subject',
'setting_email_articlemediacomment_message'
);

// SET RESULT VARIABLE

// SAVE CHANGES
if($task == "dosave") {
  
  foreach ($keys as $key) {
    $setting[$key] = $data[$key] = $_POST[$key];
  }
  
    
  if (!$rc_validator->has_errors()) {
    $rc_articlecats->save_categories($_POST['articlecat_title'],$_POST['articlecat_title_sub']);

    $database->database_query("UPDATE se_settings SET ".rc_toolkit::db_data_packer($data));
    $setting = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_settings LIMIT 1"));
    $result = 11150203;  
  }
  

  
}

foreach ($keys as $key) {
  $smarty->assign($key, $setting[$key]);
}

$categories = $rc_articlecats->get_categories(0);

$smarty->assign('is_error', $rc_validator->has_errors());
$smarty->assign('error_message', join(" ",$rc_validator->get_errors()));
$smarty->assign('result', $result);
$smarty->assign('permission_article', $setting['setting_permission_article']);
$smarty->assign('categories', $categories);
$smarty->assign('cat_max_id',$rc_articlecats->get_max_id());
include "admin_footer.php";
exit();
