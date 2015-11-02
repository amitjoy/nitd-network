<?
$page = "admin_feedback_settings";
include "admin_header.php";

if(isset ($_POST['type_to_delete'])) {
 $id=$_POST['type_to_delete'];
 $database->database_query("DELETE FROM " . $feedbackObj->table_feedbacks_types . " WHERE id=$id");
 header("Location: " . $_SERVER['PHP_SELF']);
}


$smarty->assign("feedbacks_types",$feedbackObj->feedbacks_types_array);



include "admin_footer.php";
?>