<?php


ob_start();

$send_debug_information = FALSE;
$benchmark_start = ( (list($usec, $sec) = explode(" ",microtime())) ? ((float)$usec + (float)$sec) : time());





// Error handlers
$errors = array();

function handleErrorInAjax($errno=NULL, $errstr=NULL, $errfile=NULL, $errline=NULL, $generate=FALSE)
{
  global $errors;
  
  // Ignore notices
  if( ($errno & E_USER_NOTICE) || ($errno & E_NOTICE) || ($errno & E_STRICT) ) return TRUE;
  
  // Append to list
  $errors[] = "[$errno] [$errfile:$errline] $errstr";
}

set_error_handler('handleErrorInAjax');

error_reporting(E_ALL);





// input
$task = ( !empty($_POST['task']) ? $_POST['task'] : 'failure' );





// Includes 
$page = 'chat_ajax';
include "header.php";
include "include/class_inputfilter.php";
include "include/class_chat.php";

// Json emulation
if( !function_exists('json_encode') )
{
  include_once "include/xmlrpc/xmlrpc.inc";
  include_once "include/xmlrpc/xmlrpcs.inc";
  include_once "include/xmlrpc/xmlrpc_wrappers.inc";
  include_once "include/jsonrpc/jsonrpc.inc";
  include_once "include/jsonrpc/jsonrpcs.inc";
  include_once "include/jsonrpc/json_extension_api.inc";
}

$benchmark_start = getmicrotime();
$responses = array();
$messages = array();
$seIM = new seIM();







/* ------------------------------------------------------------------------- *\
|                                                                             |
| Execute                                                                 [X] |
|                                                                             |
\* ------------------------------------------------------------------------- */

$responses = $seIM->execute($task);

$benchmark_end = ( (list($usec, $sec) = explode(" ",microtime())) ? ((float)$usec + (float)$sec) : time());
$benchmark_delta = round($benchmark_end - $benchmark_start, 5);

// Note that errors in stats section will be destroyed
$contents = ob_get_contents();
ob_end_clean();

if( $send_debug_information )
{
  if( !empty($errors) )
    $responses['errors'] = $errors;

  if( !empty($contents) )
    $responses['contents'] = $contents;

  if( !empty($benchmark_delta) )
    $responses['benchmark'] = $benchmark_delta;
}

$json_string = json_encode($responses);
$json_string_length = strlen($json_string);








/* ------------------------------------------------------------------------- *\
|                                                                             |
| Stats                                                                   [O] |
|                                                                             |
\* ------------------------------------------------------------------------- */

update_stats('chat_requests');

$sql = "
  INSERT INTO
    se_stats
  (
    stat_date,
    stat_chat_cpu_time,
    stat_chat_bandwidth
  )
  VALUES
  (
    UNIX_TIMESTAMP(CURDATE()),
    '$benchmark_delta',
    '$json_string_length'
  ) 
  ON DUPLICATE KEY UPDATE
    stat_chat_cpu_time=stat_chat_cpu_time+'$benchmark_delta',
    stat_chat_bandwidth=stat_chat_bandwidth+'$json_string_length'
";

$resource = $database->database_query($sql) or die($database->database_error());









/* ------------------------------------------------------------------------- *\
|                                                                             |
| Output                                                                  [O] |
|                                                                             |
\* ------------------------------------------------------------------------- */

//ob_end_clean();

header("Content-Type: text/x-json; charset=UTF-8");

echo $json_string;
exit();
?>