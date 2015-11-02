<?php

$page = "admin_faq";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }


// CREATE CATEGORY
if($task == "addcategory") {

  $cat_title = $_POST['cat_title'];
  $max_order = $database->database_fetch_assoc($database->database_query("SELECT max(faqcat_order) AS max_order FROM se_faqcats"));
  $cat_order = $max_order[max_order]+1;

  $cat_title = SE_Language::edit(0, $cat_title, NULL, LANGUAGE_INDEX_FAQ);

  $database->database_query("INSERT INTO se_faqcats (faqcat_title, faqcat_order) VALUES ('$cat_title', '$cat_order')");






// EDIT CATEGORY
} elseif($task == "editcategory") {

  $faqcat_id = $_POST['faqcat_id'];
  $faqcat = $database->database_query("SELECT * FROM se_faqcats WHERE faqcat_id='$faqcat_id'");
  if($database->database_num_rows($faqcat) == 1) {
    $faqcat_info = $database->database_fetch_assoc($faqcat);
    $cat_title = $_POST['cat_title'];
    SE_Language::edit($faqcat_info[faqcat_title], $cat_title);
  }






// DELETE CATEGORY
} elseif($task == "deletecategory") {

  $faqcat_id = $_GET['faqcat_id'];
  $database->database_query("DELETE FROM se_languagevars, se_faqs USING se_faqs JOIN se_languagevars WHERE faq_faqcat_id='$faqcat_id' AND (faq_subject=languagevar_id OR faq_content=languagevar_id)");
  $database->database_query("DELETE FROM se_languagevars, se_faqcats USING se_faqcats LEFT JOIN se_languagevars ON se_faqcats.faqcat_title=se_languagevars.languagevar_id WHERE faqcat_id='$faqcat_id'");







// MOVE CATEGORY
} elseif($task == "movecategory") {

  $faqcat_id = $_GET['faqcat_id'];
  $cat_info = $database->database_fetch_assoc($database->database_query("SELECT faqcat_id, faqcat_order FROM se_faqcats WHERE faqcat_id='$faqcat_id'"));
  $prev_cat = $database->database_query("SELECT faqcat_id, faqcat_order FROM se_faqcats WHERE faqcat_order<'$cat_info[faqcat_order]' ORDER BY faqcat_order DESC LIMIT 1");
  if($database->database_num_rows($prev_cat) == 1) {
    $prev_cat_info = $database->database_fetch_assoc($prev_cat);
    $database->database_query("UPDATE se_faqcats SET faqcat_order='$cat_info[faqcat_order]' WHERE faqcat_id='$prev_cat_info[faqcat_id]'");
    $database->database_query("UPDATE se_faqcats SET faqcat_order='$prev_cat_info[faqcat_order]' WHERE faqcat_id='$cat_info[faqcat_id]'");
  }




// CREATE QUESTION
} elseif($task == "addquestion") {

  $faqcat_id = $_POST['faqcat_id'];
  $faq_subject = $_POST['faq_subject'];
  $faq_content = htmlspecialchars_decode($_POST['faq_content'], ENT_QUOTES);
  $max_order = $database->database_fetch_assoc($database->database_query("SELECT max(faq_order) AS max_order FROM se_faqs"));
  $faq_order = $max_order[max_order]+1;

  $faq_subject = SE_Language::edit(0, $faq_subject, NULL, LANGUAGE_INDEX_FAQ);
  $faq_content = SE_Language::edit(0, $faq_content, NULL, LANGUAGE_INDEX_FAQ);
  
  $faq_datecreated = time();

  $database->database_query("INSERT INTO se_faqs (faq_faqcat_id, faq_order, faq_subject, faq_content, faq_datecreated) VALUES ('$faqcat_id', '$faq_order', '$faq_subject', '$faq_content', '$faq_datecreated')");






// EDIT QUESTION
} elseif($task == "editquestion") {

  $faq_id = $_POST['faq_id'];
  $faqcat_id = $_POST['faqcat_id'];
  $faq_subject = $_POST['faq_subject'];
  $faq_content = htmlspecialchars_decode($_POST['faq_content'], ENT_QUOTES);
  $faq = $database->database_query("SELECT * FROM se_faqs WHERE faq_id='$faq_id'");
  if($database->database_num_rows($faq) == 1) {
    $faq_info = $database->database_fetch_assoc($faq);

    SE_Language::edit($faq_info[faq_subject], $faq_subject);
    SE_Language::edit($faq_info[faq_content], $faq_content);

    $faq_dateupdated = time();

    $database->database_query("UPDATE se_faqs SET faq_faqcat_id='$faqcat_id', faq_dateupdated='$faq_dateupdated' WHERE faq_id='$faq_id'");
  }






// DELETE QUESTION
} elseif($task == "deletequestion") {

  $faq_id = $_GET['faq_id'];
  $database->database_query("DELETE FROM se_languagevars, se_faqs USING se_faqs JOIN se_languagevars WHERE faq_id='$faq_id' AND (faq_subject=languagevar_id OR faq_content=languagevar_id)");







// MOVE QUESTION
} elseif($task == "movequestion") {

  $faq_id = $_GET['faq_id'];
  $faq_info = $database->database_fetch_assoc($database->database_query("SELECT faq_id, faq_order FROM se_faqs WHERE faq_id='$faq_id'"));
  $prev_faq = $database->database_query("SELECT faq_id, faq_order FROM se_faqs WHERE faq_order<'$faq_info[faq_order]' ORDER BY faq_order DESC LIMIT 1");
  if($database->database_num_rows($prev_faq) == 1) {
    $prev_faq_info = $database->database_fetch_assoc($prev_faq);
    $database->database_query("UPDATE se_faqs SET faq_order='$faq_info[faq_order]' WHERE faq_id='$prev_faq_info[faq_id]'");
    $database->database_query("UPDATE se_faqs SET faq_order='$prev_faq_info[faq_order]' WHERE faq_id='$faq_info[faq_id]'");
  }






// RESET VIEWS
} elseif($task == "resetviews") {

  $faq_id = $_GET['faq_id'];

  $database->database_query("UPDATE se_faqs SET faq_views='0' WHERE faq_id='$faq_id'");

}









$nowdate = time()+1;
$faqcats = $database->database_query("SELECT * FROM se_faqcats ORDER BY faqcat_order");
while($faqcat_info = $database->database_fetch_assoc($faqcats)) {

  $faq_array = Array();
  $faqs = $database->database_query("SELECT * FROM se_faqs WHERE faq_faqcat_id='$faqcat_info[faqcat_id]' ORDER BY faq_order");
  while($faq_info = $database->database_fetch_assoc($faqs)) {

    SE_Language::_preload_multi($faq_info[faq_subject], $faq_info[faq_content]);
    $faq_info[faq_views_average] = round( $faq_info[faq_views] / ceil(($nowdate - $faq_info[faq_datecreated]) / 86400) );
    $faq_array[] = $faq_info;
  }

  SE_Language::_preload($faqcat_info[faqcat_title]);
  $faqcat_info[faqs] = $faq_array;
  $faqcat_array[] = $faqcat_info;
}


// ASSIGN VARIABLES AND SHOW ADMIN FAQ PAGE
$smarty->assign('is_error', $is_error);
$smarty->assign('faqcats', $faqcat_array);
include "admin_footer.php";
?>