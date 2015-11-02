<?php
$page = "admin_language_export";
include "admin_header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : NULL ) );
$start = ( isset($_POST['start']) ? $_POST['start'] : ( isset($_GET['start']) ? $_GET['start'] : NULL ) );
$end = ( isset($_POST['end']) ? $_POST['end'] : ( isset($_GET['end']) ? $_GET['end'] : NULL ) );
$language_id = ( isset($_POST['language_id']) ? $_POST['language_id'] : ( isset($_GET['language_id']) ? $_GET['language_id'] : NULL ) );

$is_error = FALSE;
$result = NULL;

// VALIDATE LANGUAGE ID
$sql = "SELECT * FROM se_languages WHERE language_id='{$language_id}' LIMIT 1";
$resource = $database->database_query($sql) or die($database->database_error());

if( !$database->database_num_rows($resource) )
{
  header('Location: admin_language.php');
  exit();
}

$language_info = $database->database_fetch_assoc($resource);



$language_object_database = SELanguageStorage::getInstance('mysql');
$language_object_file = SELanguageStorage::getInstance('file');

if( $task=="doexport" )
{
  $sql = "SELECT * FROM se_languagevars WHERE languagevar_language_id='{$language_id}'";
  if( !empty($start) ) $sql .= " && languagevar_id>='{$start}'";
  if( !empty($end) ) $sql .= " && languagevar_id<='{$end}'";
  $sql .= "ORDER BY languagevar_id ASC";
  
  $resource = $database->database_query($sql) or die($database->database_error());
  
  $languagevars_export_array = array();
  while( $languagevar_info = $database->database_fetch_assoc($resource) )
  {
    $languagevars_export_array[$languagevar_info['languagevar_id']] = $languagevar_info['languagevar_value'];
  }
  
  $file_contents = $language_object_file->make_language_file(
    $language_info['language_id'],
    $languagevars_export_array
  );
  
  if( empty($start) ) $start = min(array_keys($languagevars_export_array));
  if( empty($end) ) $end =  max(array_keys($languagevars_export_array));;
  $output_filename = ( !empty($_POST['output_filename']) ? $_POST['output_filename'] : 'language_[code]' );
  $output_filename = str_replace('[id]', $language_info['language_id'], $output_filename);
  $output_filename = str_replace('[code]', $language_info['language_code'], $output_filename);
  $output_filename = str_replace('[range]', $start.'-'.$end, $output_filename);
  
  header("Content-type: application/x-download");
  header("Content-disposition: attachment; filename={$output_filename}.php");
  echo $file_contents;
  exit();
}


// ASSIGN VARIABLES AND SHOW PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);
$smarty->assign('task', $task);
$smarty->assign_by_ref('language_info', $language_info);
include "admin_footer.php";
?>