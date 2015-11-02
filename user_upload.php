<?php

$page = "upload"; //"user_upload";

define('SE_PAGE_AJAX', TRUE);
define('SE_SESSION_RESUME', TRUE);

// RETRIEVE SESSION ID/UPLOAD TOKEN
if(isset($_POST['session_id'])) { $session_id = $_POST['session_id']; } elseif(isset($_GET['session_id'])) { $session_id = $_GET['session_id']; } else { $session_id = NULL; }
if(isset($_POST['upload_token'])) { $upload_token = $_POST['upload_token']; } elseif(isset($_GET['upload_token'])) { $upload_token = $_GET['upload_token']; } else { $upload_token = NULL; }

include "header.php";

//$action = $session->get('action');
//$sess_token = $session->get('upload_token');

if( $user->user_exists && !empty($_SESSION['action']) && $_SESSION['upload_token']==$upload_token )
{
  include $_SESSION['action'];
  exit();
}

// BELOW SHOULD ONLY EXECUTE IF SOMEONE IS TRYING TO HIJACK A SESSION ID
$json = json_encode(array('result'=>'failure', 'error' => 'Invalid user session', 'size' => ''));
header('Content-type: application/json');
echo $json;
exit();
?>