<?php

$page = "help";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = ""; }



// INCREMENT VIEW
if($task == "view")
{
  $faq_id = $_GET['faq_id'];
  $database->database_query("UPDATE se_faqs SET faq_views=faq_views+1 WHERE faq_id='$faq_id'");
  exit();
}


$faqcats = $database->database_query("SELECT * FROM se_faqcats ORDER BY faqcat_order");
while($faqcat_info = $database->database_fetch_assoc($faqcats))
{
  $faq_array = Array();
  $faqs = $database->database_query("SELECT * FROM se_faqs WHERE faq_faqcat_id='{$faqcat_info['faqcat_id']}' ORDER BY faq_order");
  while($faq_info = $database->database_fetch_assoc($faqs)) {
    SE_Language::_preload_multi($faq_info[faq_subject], $faq_info['faq_content']);
    $faq_info['faq_content'] = htmlspecialchars_decode($faq_info['faq_content'], ENT_QUOTES);
    $faq_array[] = $faq_info;
  }

  SE_Language::_preload($faqcat_info['faqcat_title']);
  $faqcat_info[faqs] = $faq_array;
  $faqcat_array[] = $faqcat_info;
}

// SET GLOBAL PAGE TITLE/DESCRIPTION
$global_page_title[0] = 957;
$global_page_description[0] = 958;

// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
$smarty->assign('faqcats', $faqcat_array);
include "footer.php";
?>
