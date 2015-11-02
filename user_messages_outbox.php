<?php

$page = "user_messages_outbox";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }

// CHECK FOR ADMIN ALLOWANCE OF MESSAGES
if( !$user->level_info['level_message_allow'] )
{
  header("Location: user_home.php");
  exit();
}

// SET VARS
$pms_per_page = 20;

// DELETE NECESSARY PMS
if($task == "deleteselected") { $user->user_message_delete_selected($_POST['delete_convos'], 1); }

// GET TOTAL MESSAGES
$total_pms = $user->user_message_total(1, 0);

// MAKE PM PAGES
$page_vars = make_page($total_pms, $pms_per_page, $p);

// GET ARRAY OF MESSAGES
$pms = $user->user_message_list($page_vars[0], $pms_per_page, 1);

// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('total_pms', $total_pms);
$smarty->assign_by_ref('pms', $pms);

$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($pms));
include "footer.php";
?>