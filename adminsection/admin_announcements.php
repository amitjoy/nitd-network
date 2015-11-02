<?php

$page = "admin_announcements";
include "admin_header.php";

if(isset($_GET['task'])) { $task = $_GET['task']; } elseif(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_GET['announcement_id'])) { $announcement_id = $_GET['announcement_id']; } elseif(isset($_POST['announcement_id'])) { $announcement_id = $_POST['announcement_id']; } else { $announcement_id = 0; }

// CHECK THAT ANNOUNCEMENT EXISTS IF ID IS GIVEN
if($announcement_id != 0) {
  $announcement = $database->database_query("SELECT * FROM se_announcements WHERE announcement_id='$announcement_id' LIMIT 1");
  if($database->database_num_rows($announcement) == 0) { header("Location: admin_announcements.php"); exit; }
  $item_info = $database->database_fetch_assoc($announcement);
}





// SEND EMAIL ANNOUNCEMENT
if($task == "sendemail") {
  $from = htmlspecialchars_decode($_POST['from'], ENT_QUOTES);
  $subject = $_POST['subject'];
  $message = $_POST['message'];
  $emails_at_a_time = $_POST['emails_at_a_time'];
  $levels = $_POST['levels'];
  $subnets = $_POST['subnets'];
  $start = $_POST['start'];

  $levels   = ( !empty($levels)  ? ( is_string($levels)   ? explode(",", $levels)   : (array) $levels  ) : NULL );
  $subnets  = ( !empty($subnets) ? ( is_string($subnets)  ? explode(",", $subnets)  : (array) $subnets ) : NULL ); 

  if($message != "" && ($levels != NULL || $subnets != NULL)) {

    // START BUILDING QUERY
    $emailquery = "SELECT user_email FROM se_users ";

    if( !empty($levels) || !empty($subnets) ) $emailquery .= 'WHERE ';

    // SET USER LEVELS AUDIENCE
    if(!empty($levels)) { $emailquery .= "user_level_id IN('".join("', '", $levels)."')"; }

    // SET SUBNETS AUDIENCE
    if(!empty($subnets)) {
      if(!empty($levels)) $emailquery .= " OR ";
      $emailquery .= "user_subnet_id IN('".join("', '", $subnets)."')";
    } 

    // GET TOTAL USERS
    $total_users = $database->database_num_rows($database->database_query($emailquery));

    $finish = $start + $emails_at_a_time;
    $limit = "$start, $emails_at_a_time";

    // ADD LIMITS
    $emailquery .= " ORDER BY user_id LIMIT $limit";

    $users = $database->database_query($emailquery);
    while($user = $database->database_fetch_assoc($users)) {
      send_generic($user[user_email], $from, $subject, $message, Array(), Array());
    }

    // IMPLODE LEVELS AND SUBNETWORKS
    if(is_array($levels)) { $levels = implode(",", $levels); }
    if(is_array($subnets)) { $subnets = implode(",", $subnets); }

    echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head>
          <body onload=\"parent.sendEmail('$start', '$total_users');\">
	  <form action='admin_announcements.php' method='post' name='emailform'>
          <input type='text' name='from' maxlength='200' value='$from'>
          <input type='text' name='subject' maxlength='200' value='$subject'>
          <textarea name='message' rows='7' cols='80'>$message</textarea>
	  <select name='emails_at_a_time'><option value='$emails_at_a_time'>$emails_at_a_time</option></select>
	  <input type='hidden' name='levels' value='$levels'>
          <input type='hidden' name='subnets' value='$subnets'>
          <input type='hidden' name='start' value='".($start+$emails_at_a_time)."'>
          <input type='hidden' name='task' value='sendemail'>
          </form></body></html>";
    exit();
  }

// POST NEWS ITEM
} elseif($task == "postnews") {
  $date = $_POST['date'];
  $subject = $_POST['subject'];
  $body = $_POST['body'];

  // IF NEW ITEM
  if($announcement_id == 0) {

    // DETERMINE ORDER ID
    $order_id = $database->database_fetch_assoc($database->database_query("SELECT MAX(announcement_order) AS announcement_order FROM se_announcements"));
    $order_id[announcement_order]++;
    $database->database_query("INSERT INTO se_announcements (announcement_order, announcement_date, announcement_subject, announcement_body) VALUES ('$order_id[announcement_order]', '$date', '$subject', '$body')");

  // IF EDITING OLD ITEM
  } else {
    $database->database_query("UPDATE se_announcements SET announcement_date='$date', announcement_subject='$subject', announcement_body='$body' WHERE announcement_id='$announcement_id' LIMIT 1");
  }




// IF JSON REQUEST, SEND BACK DATA
} elseif($task == "getnews") {
  $json = "{'date':'".str_replace("'", "\'", $item_info[announcement_date])."', 'subject':'".str_replace("'", "\'", $item_info[announcement_subject])."', 'body':'".str_replace("\n", "\\n", str_replace("\r\n", "\n", str_replace("'", "\'", $item_info[announcement_body])))."'}";
  echo $json;
  exit();



// MOVE ITEM IN ITEM ORDER
} elseif($task == "moveup") {

  // MOVE ITEM ABOVE DOWN
  $order_up = $item_info[announcement_order] - 1;
  $database->database_query("UPDATE se_announcements SET announcement_order='$item_info[announcement_order]' WHERE announcement_order='$order_up' LIMIT 1");

  // MOVE THIS ITEM UP
  $database->database_query("UPDATE se_announcements SET announcement_order='$order_up' WHERE announcement_id='$item_info[announcement_id]' LIMIT 1");



// DELETE ITEM
} elseif($task == "deletenews") {
  $database->database_query("DELETE FROM se_announcements WHERE announcement_id='$announcement_id'");
}






// GET ARRAY OF PAST NEWS ITEMS
$news = $database->database_query("SELECT * FROM se_announcements ORDER BY announcement_order DESC");
while($item = $database->database_fetch_assoc($news)) {

  // ADD TO ARRAY OF NEWS ITEMS
  $news_array[] = $item;

}



// GET USER LEVELS
$levels = $database->database_query("SELECT level_id, level_name, level_default FROM se_levels");
$level_array = Array();
while($level_info = $database->database_fetch_assoc($levels)) {
  $level_array[] = $level_info;
}

// GET SUBNETS
$subnets = $database->database_query("SELECT subnet_id, subnet_name FROM se_subnets");
$subnet_array = Array();
while($subnet_info = $database->database_fetch_assoc($subnets)) {
  SE_Language::_preload($subnet_info[subnet_name]);
  $subnet_array[] = $subnet_info;
}



// ASSIGN VARIABLES AND SHOW ADMIN ANNOUNCEMENTS PAGE
$smarty->assign('news', $news_array);
$smarty->assign("levels", $level_array);
$smarty->assign("subnets", $subnet_array);










$smarty->assign('totalinset', $totalinset);
$smarty->assign('emails_at_a_time', $emails_at_a_time);
$smarty->assign('is_error', $is_error);
$smarty->assign('error_msg', $error_msg);
$smarty->assign('start', $start);
$smarty->assign('finish', $finish);
$smarty->assign('start1', $start1);
$smarty->assign('finish1', $finish1);
$smarty->assign('total', $total);
include "admin_footer.php";
?>