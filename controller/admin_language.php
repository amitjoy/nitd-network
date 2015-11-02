<?php

$page = "admin_language";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }




// SET DEFAULT
if($task == "setdefault") {
  $language_id = $_GET['language_id'];
  $database->database_query("UPDATE se_languages SET language_default='1' WHERE language_id='$language_id'");
  $database->database_query("UPDATE se_languages SET language_default='0' WHERE language_id<>'$language_id'");
  SE_Language::_languages();



// CREATE LANGUAGE
} elseif($task == "create") {
  $language_id = $_POST['language_id'];
  $language_name = $_POST['language_name'];
  $language_code = $_POST['language_code'];
  $language_setlocale = $_POST['language_setlocale'];
  $language_autodetect_regex = $_POST['language_autodetect_regex'];

  if(trim($language_name) != "") {
    if($language_id != 0) {
      $database->database_query("UPDATE se_languages SET language_name='$language_name', language_code='$language_code', language_setlocale='$language_setlocale', language_autodetect_regex='$language_autodetect_regex' WHERE language_id='$language_id'");
    } else {
      $database->database_query("INSERT INTO se_languages (language_name, language_code, language_setlocale, language_autodetect_regex) VALUES ('$language_name', '$language_code', '$language_setlocale', '$language_autodetect_regex')");
    }
  }

  SE_Language::_languages();


// DELETE LANGUAGE
} elseif($task == "delete") {
  $language_id = $_GET['language_id'];
  $database->database_query("DELETE FROM se_languages, se_languagevars USING se_languages LEFT JOIN se_languagevars ON se_languages.language_id=se_languagevars.languagevar_language_id WHERE language_id='$language_id' AND language_default<>'1'");
  SE_Language::_languages();



// SAVE CHANGES
} elseif($task == "dosave") {
  $setting[setting_lang_allow] = $_POST['setting_lang_allow'];
  $setting[setting_lang_anonymous] = $_POST['setting_lang_anonymous'];
  $setting[setting_lang_autodetect] = $_POST['setting_lang_autodetect'];

  $database->database_query("UPDATE se_settings SET setting_lang_allow='$setting[setting_lang_allow]',
						setting_lang_anonymous='$setting[setting_lang_anonymous]',
						setting_lang_autodetect='$setting[setting_lang_autodetect]'");

  // RESET LANGUAGE
  SE_Language::select($admin);
  header("Content-Language: ".SE_Language::info('language_code'));
}


// GET LANGUAGE PACK LIST
$lang_packlist = SE_Language::list_packs();
ksort($lang_packlist);
$lang_packlist = array_values($lang_packlist);


// GET AVAILABLE LOCALE OPTIONS
$locales = array();
if( strtoupper(substr(PHP_OS, 0, 3))!=='WIN' )
{
  $result = NULL;
  @exec('locale -a', $result);
  if( is_array($result) )
  {
    $locales = array_map('trim', $result);
  }
}


// ASSIGN VARIABLES AND SHOW ADMIN USER LEVELS PAGE
$smarty->assign('locales', $locales);
$smarty->assign('lang_packlist', $lang_packlist);
$smarty->assign('HTTP_ACCEPT_LANGUAGE', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
$smarty->assign('HTTP_ACCEPT_LANGUAGE_CLEAN', join(', ', array_filter(preg_split('/[;,.q=\d]+/', $_SERVER['HTTP_ACCEPT_LANGUAGE']))) );
$smarty->assign('AUTODETECTED_LANGUAGE', SE_Language::_autodetect($_SERVER['HTTP_ACCEPT_LANGUAGE'], '[default]') );
include "admin_footer.php";
?>