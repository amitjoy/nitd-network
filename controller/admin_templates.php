<?php

$page = "admin_templates";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['t'])) { $filename = $_POST['t']; } elseif(isset($_GET['t'])) { $filename = $_GET['t']; } else { $filename = ""; }

// REMOVE ANY BACK-PATHING FROM URL
$filename = str_replace("..", "", $filename);

// VALIDATE FILENAME
$path = "../templates/$filename";
$is_error = 0;

// MAKE SURE FILE IS A TEMPLATE OR CSS FILE
if(strpos(strtolower($filename), ".tpl") === FALSE && strpos(strtolower($filename), ".css") === FALSE) {
  $is_error = 473;
}
// MAKE SURE FILE EXISTS
elseif(!is_file($path) || strpos($filename, "..") !== FALSE) {
  $is_error = 474;
}
// MAKE SURE FILE IS READABLE
elseif(!is_readable($path)) {
  $is_error = 475;
}
// MAKE SURE FILE IS WRITABLE
elseif(!is_writable($path)) {
  $is_error = 476;
}


// IF JSON REQUEST, SEND BACK DATA
if($task == "gettemplate") {
  $template_code = file_get_contents($path);
  $template_code = str_replace("'", "\'", str_replace("\n", "\\n", str_replace("\\", "\\\\", str_replace("\r\n", "\n", $template_code))));

  if($is_error != 0) {
    SE_Language::_preload_multi($is_error);
    SE_Language::load();
    $error_message = str_replace("'", "\'", SE_Language::_get($is_error));
  }

  $json = "{'is_error':$is_error, 'error_message':'$error_message', 'template':'$template_code'}";
  echo $json;
  exit();


// SAVE TEMPLATE
} elseif($task == "save") {

  // WRITE CODE TO FILE
  if($is_error == 0) {
    $template_code = str_replace("{/php}", "", str_replace("{php}", "", htmlspecialchars_decode(str_replace("\\\\", "\\", $_POST['template_code']), ENT_QUOTES)));
    $handle = fopen($path, 'w+');
    fwrite($handle, $template_code);
    fclose($handle);
  }
  exit();
}





// GET ARRAYS OF TEMPLATES FROM TEMPLATE DIRECTORY
$user_files = Array();
$base_files = Array();
$head_files = Array();
if($handle = opendir('../templates')) { 
  while (false !== ($file = readdir($handle))) { 
    if($file != "." && $file != ".." && strpos(strtolower($file), "admin_") === FALSE) {
      // IF FILES ARE USER PAGES
      if(strpos(strtolower($file), "user_") !== FALSE) {
        $user_files[] = Array('filename' => $file);
      // IF FILES ARE HEADER/FOOTER/STYLE PAGES
      } elseif(preg_match("/^header|footer|styles.*\.tpl|\.css/", $file)) {
        $head_files[] = Array('filename' => $file);
      // IF FILES ARE BASE PAGES
      } else {
        $base_files[] = Array('filename' => $file);
      }
    }
  }
  closedir($handle); 
} 

sort($user_files);
sort($base_files);
sort($head_files);


// ASSIGN VARIABLES AND SHOW TEMPLATES PAGE
$smarty->assign('user_files', $user_files);
$smarty->assign('base_files', $base_files);
$smarty->assign('head_files', $head_files);
include "admin_footer.php";
?>