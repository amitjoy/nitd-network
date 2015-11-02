<?
$page = "admin_feedbacktype_edit";
include "admin_header.php";

if (!isset($_POST['new_name'])) {
 $type_id = $_GET['id'];
 $feedbacktype_name = $feedbackObj->feedbacks_types_array;
 $result = $database->database_query("SELECT name FROM " . $feedbackObj->table_feedbacks_types . " WHERE id=$type_id");
 $row = mysql_fetch_assoc($result);

 $smarty->assign("type_id", $type_id);
 $smarty->assign("type_name", $row['name']);print($_POST['id']);
} else {
 $new_name = $_POST['new_name'];
 $type_id = $_POST['type_id'];
 $database->database_query("UPDATE " . $feedbackObj->table_feedbacks_types . " SET name=\"$new_name\"  WHERE id=$type_id") or die(mysql_error());
}

include "admin_footer.php";
?>