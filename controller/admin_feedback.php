<?
$page = "admin_feedback";
include "admin_header.php";



if(isset ($_POST['task_to_delete'])) {
 $close_id=$_POST['task_to_delete'];
 $database->database_query("DELETE FROM " . $feedbackObj->table_wh_feedbacks . " WHERE id=$close_id");
}


if(isset ($_POST['task_to_close'])) {
 $close_id=$_POST['task_to_close'];
 $database->database_query("UPDATE " . $feedbackObj->table_wh_feedbacks . " SET status=2 WHERE id=$close_id");
}

if(isset ($_POST['task_to_reopen'])) {
 $open_id=$_POST['task_to_reopen'];
 $database->database_query("UPDATE " . $feedbackObj->table_wh_feedbacks . " SET status=1 WHERE id=$open_id");
}


//---------------------------- pagination -----------------------------------------------
$pagenum = 0;

if (isset($_POST['pagenum'])) {
 $pagenum = $_POST['pagenum'];
}

if (isset($_GET['pagenum'])) {
 $pagenum = $_GET['pagenum'];
}


$records_per_page=25;

//---------------------------- filtering -----------------------------------------------
$query_add=$feedbackObj->getFilterQuery($smarty);
$query="SELECT * FROM " . $feedbackObj->table_wh_feedbacks . " AS t1 ";
$query.=$query_add . " ORDER BY t1.id DESC LIMIT " . $pagenum*$records_per_page . ",$records_per_page";
$result = $database->database_query($query);

//------------------------------------------------------------------------------------

$counter = 0;
$feedback=array();
while(($row = mysql_fetch_assoc($result)) && ($counter<$records_per_page)) {
 $res = $database->database_query("SELECT user_email FROM se_users WHERE user_id=" . $row['user_id']);
 $r = mysql_fetch_assoc($res);
 $row['user_mail']=$r['user_email'];

 $feedback[]=$row;
 $feedback[$counter]['typename']=$feedbackObj->getTypenameByTypeid($feedback[$counter]['feedback_type_id']);
 $feedback[$counter]['text']=htmlspecialchars_decode($feedback[$counter]['text'], ENT_QUOTES);
 $counter++;
}

//------------------------------------------------------------------------------------

$user_array=array();

foreach ($feedback as $row) {
 $result = $database->database_query("SELECT user_displayname, user_email, user_username FROM se_users WHERE user_id=\"" . $row['user_id'] . "\"");

 $r = mysql_fetch_assoc($result);
 $user_array[$r['user_email']]['user_displayname']=$r['user_displayname'];
 $user_array[$r['user_email']]['user_email']=$r['user_email'];
 $user_array[$r['user_email']]['user_username']=$r['user_username'];



}


$res=mysql_query("SELECT COUNT(*) AS rowsN FROM " . $feedbackObj->table_wh_feedbacks . " AS t1 " . $query_add) or die(mysql_error());
$res=mysql_fetch_assoc($res);
$total_feeds=$res['rowsN'];
$linkrows_per_page=ceil($total_feeds/$records_per_page);



$smarty->assign("date_format",$feedbackObj->feedback_date_format);
$smarty->assign("feedback",$feedback);
$smarty->assign("feedbacks_types",$feedbackObj->feedbacks_types_array);
$smarty->assign("user_array",$user_array);
$smarty->assign("pages",$linkrows_per_page);
$smarty->assign("page_num",$pagenum);
$smarty->assign("filter_string",$filter_string);
$smarty->assign("total_feeds",$total_feeds);
$smarty->assign("query_errors",$feedbackObj->flagarray);



include "admin_footer.php";
?>
