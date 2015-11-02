<?php

$page = "feedback";
include "header.php";


if (!isset($_POST['feedback_type'])) {
 $result = $database->database_query("SELECT * FROM wh_feedbacks_types");
 $feedback_categories = array();
 while ($row = mysql_fetch_assoc($result)) {
  $feedback_categories[] = $row;
 }
 $smarty->assign("feedback_categories", $feedback_categories);
} else {
 $feedback_type = $_POST['feedback_type'];
 $curr_link = $_POST['curr_link'];
 $browser = $_SERVER['HTTP_USER_AGENT'];
 $feedback = substr(htmlspecialchars($_POST['feedback'], ENT_QUOTES, 'UTF-8'), 0, 500);
 $curr_time = time();
 $query="INSERT INTO `wh_feedbacks` (id, time, feedback_type_id, user_id, text, link, browser, status) VALUES ('', $curr_time ,  $feedback_type, {$user->user_info['user_id']},\"$feedback\",\" $curr_link\",\"$browser\", 1)";
 $done=$database->database_query($query);

/*
 if(!$done) {
  //$feedbackObj->flogger->log($query);
  $err=mysql_error();
  $feedbackObj->debug_logger->debug("ERROR with query: $err. QUERY IS => " . $query);
 }
 else {
  $feedbackObj->debug_logger->debug("OK " . $query);
 }

*/

}

include "footer.php";
?>
