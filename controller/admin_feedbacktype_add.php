<?
$page = "admin_feedbacktype_add";
include "admin_header.php";

if(isset ($_POST['new_name'])) {
 $new_name=$_POST['new_name'];
 $database->database_query("INSERT INTO " . $feedbackObj->table_feedbacks_types . " (name) VALUES(\"$new_name\")");
 header("Location: " . $_SERVER['PHP_SELF']);
}

include "admin_footer.php";
?>
