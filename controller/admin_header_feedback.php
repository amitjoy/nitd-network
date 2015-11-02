<?

if (!defined('SE_PAGE')) {
 exit();
}


header("Content-Type: text/html; charset=utf-8");


class FeedbackAdminClass {

 public $table_feedbacks_types = "wh_feedbacks_types";
 public $table_wh_feedbacks = "wh_feedbacks";
 public $feedbacks_types_array = array();
 public $feedback_date_format = "%Y-%m-%d %H:%M:%S";
 public $flagarray=array();

 function FeedbackAdminClass() {
  $this->getFeedbackTypes();
  //$this->checkLogs();
 }

 private function getFeedbackTypes() {
  global $database;
  $result = $database->database_query("SELECT * FROM " . $this->table_feedbacks_types . " ORDER BY id ASC");
  while ($row = mysql_fetch_assoc($result)) {
   $this->feedbacks_types_array[] = $row;
  }
 }

 private function checkLogs() {
  global $database;
  $filename="../feedback_logger.log";
  $handle=fopen($filename, "r+b");
  flock($handle, LOCK_EX);


  $lines=explode("\n", fread($handle, filesize($filename)));
  for($i=0;$i<count($lines);++$i) {
   if(!empty ($lines[$i])) {
    $flag=$database->database_query($lines[$i]);
    if(!$flag) {
     $this->flagarray[]=$lines[$i];
    }
   }
  }

  ftruncate($handle, 0);
  fclose($handle);
 }

 //----------------------------------------------------------------------------

 public function getTypenameByTypeid($typeid) {

  for ($i = 0; $i < count($this->feedbacks_types_array); ++$i) {
   if ($this->feedbacks_types_array[$i]['id'] == $typeid) {
    return $this->feedbacks_types_array[$i]['name'];
   }
  }

  return "undefined type";
 }

//----------------------------------------------------------------------------
 public function getFilterQuery($smarty) {
  $query_add = "";

  $feedback_type_filter = 0;
  $feedback_status_filter = 0;
  $feedback_mail_filter = 0;
  $filter_string = "";


//*****************

  if (isset($_REQUEST['feedback_mail_filter'])) {
   if ($_REQUEST['feedback_mail_filter'] != "") {
    $feedback_mail_filter = trim($_REQUEST['feedback_mail_filter']);
    $filter_string.="&feedback_mail_filter=" . $feedback_mail_filter;
    $smarty->assign("feedback_mail_filter", $feedback_mail_filter);
   }
  }

  if ($feedback_mail_filter != "") {
   $smarty->assign("feedback_mail_filter", $feedback_mail_filter);
   $query_add.=" INNER JOIN se_users ON (t1.user = se_users.user_id) WHERE user_email LIKE ('%$feedback_mail_filter%') ";
   $smarty->assign("$feedback_mail_filter", $feedback_mail_filter);
  } else {
   $query_add.="WHERE 1=1 ";
  }


//*****************
  if (isset($_REQUEST['feedback_type_filter'])) {
   if ($_REQUEST['feedback_type_filter'] != "") {
    $feedback_type_filter = $_REQUEST['feedback_type_filter'];
    $filter_string.="&feedback_type_filter=" . $feedback_type_filter;
    $smarty->assign("feedback_type_filter", $_REQUEST['feedback_type_filter']);
   }
  }

  if ($feedback_type_filter != "all") {
   $smarty->assign("feedback_type_filter", $feedback_type_filter);
   $query_add.="AND t1.feedback_type_id=\"$feedback_type_filter\" ";
   $smarty->assign("$feedback_type_filter", $feedback_type_filter);
  }

//*****************
  if (isset($_REQUEST['feedback_status_filter'])) {
   if ($_REQUEST['feedback_status_filter'] != "") {
    $feedback_status_filter = $_REQUEST['feedback_status_filter'];
    $filter_string.="&feedback_status_filter=" . $feedback_status_filter;
    $smarty->assign("feedback_status_filter", $_REQUEST['feedback_status_filter']);
   }
  }

  if ($feedback_status_filter != "all") {
   $smarty->assign("feedback_status_filter", $feedback_status_filter);
   $query_add.="AND t1.status=\"$feedback_status_filter\" ";
  }

  return $query_add;
 }

 //----------------------------------------------------------------------------
}

$feedbackObj = new FeedbackAdminClass();
?>
