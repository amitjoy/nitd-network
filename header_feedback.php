<?

// ENSURE THIS IS BEING INCLUDED IN AN SE SCRIPT
if(!defined('SE_PAGE')) {
 exit();
}

// PRELOAD LANGUAGE
SE_Language::_preload_multi(17001000, 17001035);

switch($page) {

 // CODE FOR USER HOME PAGE
 case "user_home":
 // your code goes here
  break;
}

header("Content-Type: text/html; charset=utf-8");

class FileLogger {

 private $filehandler;
 private $logname;
 public $buffer = array();

 public function __construct($logname, $filename) {
  $this->logname = $logname;
  $this->filehandler = fopen($filename, "a+");
 }

 public function __destruct() {
  /*fputs($this->filehandler, join("", $this->buffer));
  fclose($this->filehandler);*/
 }

 private function insertData() {
  fputs($this->filehandler, join("", $this->buffer));
  fclose($this->filehandler);
 }

 public function log($str) {
  //$prefix = "[" . date("Y-m-d_h:i:s") . "->" . $this->logname . "] ";
  $str = preg_replace("/^/m", $prefix, rtrim($str));
  $this->buffer[] = $str . "\n";

  $this->insertData();
 }

 public function debug($s, $level=0) {
  $stack = debug_backtrace();
  $file = basename($stack[$level]['file']);
  $line = $stack[$level]['line'];
  $this->log("[at $file line $line] $s");

  $this->insertData();
 }

}


class FeedbackClass {

 public $flogger;
 public $debug_logger;
 public $table_wh_feedbacks = "wh_feedbacks";
 public $table_feedbacks_types = "wh_feedbacks_types";

 function FeedbackClass() {
  $this->getFeedbackTypes();
  $this->flogger=new FileLogger("logger", "feedback_logger.log");
  $this->debug_logger=new FileLogger("debug", "feedback_logger_err.log");
 }

 private function getFeedbackTypes() {
  global $database;
  $result = $database->database_query("SELECT * FROM " . $this->table_feedbacks_types);
  while ($row = mysql_fetch_assoc($result)) {
   $this->feedbacks_types_array[] = $row;
  }
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

}






$feedbackObj=new FeedbackClass();

?>
