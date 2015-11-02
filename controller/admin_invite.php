<?php

$page = "admin_invite";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }


// SET RESULT VARIABLE
$result = 0;


// SAVE CHANGES
if($task == "doinvite") {
  $invite_emails = implode(",", array_slice(explode(",", $_POST['invite_emails']), 0, 10));

  // NO INVITE CODE REQUIRED
  if($setting[setting_signup_invite] == 0) {
    send_systememail('invite', $invite_emails, Array($setting[setting_email_fromname], $setting[setting_email_fromemail], "", "<a href=\"".$url->url_base."signup.php\">".$url->url_base."signup.php</a>"), TRUE);

  // INVITE CODE NECESSARY
  } else {

    // LOOP OVER EMAILS
    $invite_emails_array = explode(",", $invite_emails);
    for($e=0;$e<count($invite_emails_array);$e++) {
      $email = trim($invite_emails_array[$e]);
      if($email != "") {

        // CREATE CODE, INSERT INTO DATABASE, AND SEND EMAIL
	$invite_code = randomcode();
	$database->database_query("INSERT INTO se_invites (invite_user_id, invite_date, invite_email, invite_code) VALUES ('0', '".time()."', '$email', '$invite_code')");
        send_systememail('invitecode', $email, Array($setting[setting_email_fromname], $setting[setting_email_fromemail], "", $invite_code, "<a href=\"".$url->url_base."signup.php?signup_email=$email&signup_invite=$invite_code\">".$url->url_base."signup.php?signup_email=$email&signup_invite=$invite_code</a>"));
      }
    }
  }
  
  $result = 1;
}



// ASSIGN VARIABLES AND SHOW BANNING PAGE
$smarty->assign('result', $result);
include "admin_footer.php";
?>