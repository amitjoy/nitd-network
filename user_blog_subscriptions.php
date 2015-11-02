<?php

$page = "user_blog_subscriptions";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "dd"; }

$sort = NULL; //"se_blogentries.blogentry_date DESC";
$where = NULL;


// ENSURE BLOGS ARE ENABLED FOR THIS USER
if( !$user->level_info['level_blog_view'] )
{
  header("Location: user_home.php");
  exit();
}


// CREATE BLOG OBJECT
$subscriptions_per_page = 10;
$blog_object = new se_blog($user->user_info['user_id']);


// GET TOTAL ENTRIES
$blog_subscriptions_total = $blog_object->blog_subscription_total($where);


// MAKE ENTRY PAGES
$page_vars = make_page($blog_subscriptions_total, $subscriptions_per_page, $p);


// GET ENTRY ARRAY
$blog_subscriptions_list = $blog_object->blog_subscription_list($page_vars[0], $subscriptions_per_page, $sort, $where, TRUE);

//echo mysql_get_server_info();
//print_r($blog_subscriptions_list);


// ASSIGN VARIABLES AND SHOW PAGE
$smarty->assign       ('blog_subscriptions_total', $blog_subscriptions_total);
$smarty->assign_by_ref('blog_subscriptions_list' , $blog_subscriptions_list );

$smarty->assign('s', $s);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($blog_subscriptions_list));

include "footer.php";
?>