<?php

$page = "user_friends_requests_outgoing";
include "header.php";

if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }

// ENSURE CONECTIONS ARE ALLOWED FOR THIS USER
if( !$setting['setting_connection_allow'] )
{
  header("Location: user_home.php");
  exit();
}

// GET TOTAL FRIENDS
$total_friends = $user->user_friend_total(0, 0);

// MAKE FRIEND PAGES
$friends_per_page = 10;
$page_vars = make_page($total_friends, $friends_per_page, $p);

// GET FRIEND ARRAY
$friends = $user->user_friend_list($page_vars[0], $friends_per_page, 0, 0);

// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('friends', $friends);
$smarty->assign('total_friends', $total_friends);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($friends));
include "footer.php";
?>