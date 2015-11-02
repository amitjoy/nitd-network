<?php

$page = "admin_language_import";
include "admin_header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : NULL ) );
$is_error = FALSE;
$result = NULL;


if( $task=="doimport" )
{
  $language_storage_mysql = SELanguageStorage::getInstance('mysql');
  $language_storage_file = SELanguageStorage::getInstance('file');
  $language_file_data = $language_storage_file->read_language_file($_FILES['language_import_file']['tmp_name']);
  $language_id = $_POST['language_id'];
  $language_overwrite = ( $_POST['language_import_mode']=='replace' );
  
  if( empty($language_file_data) )
    $is_error = 1308;
  
  elseif( $language_id=='-1' && empty($language_file_data['language_info']['language_code']) )
    $is_error = 1309;
  
  elseif( $language_id=='-1' && empty($language_file_data['language_info']['language_name']) )
    $is_error = 1310;
  
  // Create new language if necessary
  if( !$is_error && $language_id=='-1' )
  {
    $language_code = $language_file_data['language_info']['language_code'];
    $language_name = $language_file_data['language_info']['language_name'];
    $language_setlocale = $language_file_data['language_info']['language_locale'];
    $language_autodetect_regex = $language_file_data['language_info']['language_autodetect'];
    
    $sql = "
      INSERT INTO se_languages
        (language_code, language_name, language_setlocale, language_autodetect_regex)
      VALUES
        ('{$language_code}', '{$language_name}', '{$language_setlocale}', '{$language_autodetect_regex}')
    ";
    
    if( !$database->database_query($sql) )
      $is_error = 1311;
    else
      $language_id = $database->database_insert_id();
  }
  
  
  if( !$is_error && $language_id>0 )
  {
    $import_results = $language_storage_mysql->import($language_id, $language_file_data['languagevars'], $language_overwrite);
    $result = TRUE;
  }
}


// ASSIGN VARIABLES AND SHOW PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('task', $task);
$smarty->assign_by_ref('import_results', $import_results);
include "admin_footer.php";
?>