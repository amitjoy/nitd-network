<?php


$page = "browse_blogs";
include "header.php";


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && !$setting['setting_permission_blog'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


// PARSE GET/POST
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "blogentry_date DESC"; }
if(isset($_POST['v'])) { $v = $_POST['v']; } elseif(isset($_GET['v'])) { $v = $_GET['v']; } else { $v = 0; }
if(isset($_POST['c'])) { $c = $_POST['c']; } elseif(isset($_GET['c'])) { $c = $_GET['c']; } else { $c = -1; }
if(isset($_POST['blog_search'])) { $blog_search = $_POST['blog_search']; } elseif(isset($_GET['c'])) { $blog_search = $_GET['blog_search']; } else { $c = NULL; }

// ENSURE SORT/VIEW ARE VALID
if($s != "blogentry_date DESC" && $s != "blogentry_views DESC" && $s != "blogentry_totalcomments DESC") { $s = "blogentry_date DESC"; }
if($v != "0" && $v != "1") { $v = 0; }


// SET WHERE CLAUSE
$where = "CASE
	    WHEN se_blogentries.blogentry_user_id='{$user->user_info['user_id']}'
	      THEN TRUE
	    WHEN ((se_blogentries.blogentry_privacy & @SE_PRIVACY_REGISTERED) AND '{$user->user_exists}'<>0)
	      THEN TRUE
	    WHEN ((se_blogentries.blogentry_privacy & @SE_PRIVACY_ANONYMOUS) AND '{$user->user_exists}'=0)
	      THEN TRUE
	    WHEN ((se_blogentries.blogentry_privacy & @SE_PRIVACY_FRIEND) AND (SELECT TRUE FROM se_friends WHERE friend_user_id1=se_blogentries.blogentry_user_id AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
	      THEN TRUE
	    WHEN ((se_blogentries.blogentry_privacy & @SE_PRIVACY_SUBNET) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_users WHERE user_id=se_blogentries.blogentry_user_id AND user_subnet_id='{$user->user_info['user_subnet_id']}' LIMIT 1))
	      THEN TRUE
	    WHEN ((se_blogentries.blogentry_privacy & @SE_PRIVACY_FRIEND2) AND (SELECT TRUE FROM se_friends AS friends_primary LEFT JOIN se_users ON friends_primary.friend_user_id1=se_users.user_id LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE friends_primary.friend_user_id1=se_blogentries.blogentry_user_id AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' AND se_users.user_subnet_id='{$user->user_info['user_subnet_id']}' LIMIT 1))
	      THEN TRUE
	    ELSE FALSE
	END";


// ONLY MY FRIENDS' BLOGS
if( $v=="1" && $user->user_exists )
{
  // SET WHERE CLAUSE
  $where .= " AND (SELECT TRUE FROM se_friends WHERE friend_user_id1='{$user->user_info['user_id']}' AND friend_user_id2=se_blogentries.blogentry_user_id AND friend_status=1)";
}


// CATEGORIES
if( isset($c) && $c!=-1 )
{
  if( $c==0 || !is_numeric($c) ) $c = '0';
  $where .= " AND blogentry_blogentrycat_id='{$c}'";
}


// SEARCH
if( !empty($blog_search) )
{
  $where .= " && MATCH (`blogentry_title`, `blogentry_body`) AGAINST ('{$blog_search}' IN BOOLEAN MODE)";
}



// CREATE blog OBJECT
$blog = new se_blog();


// GET TOTAL blogs
$total_blogentries = $blog->blog_entries_total($where);


// MAKE ENTRY PAGES
$blogentries_per_page = 10;
$page_vars = make_page($total_blogentries, $blogentries_per_page, $p);


// GET blog ARRAY
$blogentry_array = $blog->blog_entries_list($page_vars[0], $blogentries_per_page, $s, $where);


// GET BLOG ENTRY CATEGORIES
$blogentrycats_query = $database->database_query("SELECT * FROM se_blogentrycats WHERE blogentrycat_user_id=0 ORDER BY blogentrycat_id ASC");
$blogentrycats_array = array();
while( $blogentrycat=$database->database_fetch_assoc($blogentrycats_query) )
{
  $blogentrycats_array[] = array(
    'blogentrycat_id' => $blogentrycat['blogentrycat_id'],
    'blogentrycat_title' => $blogentrycat['blogentrycat_title']
  );
}



// ASSIGN SMARTY VARIABLES AND DISPLAY blogs PAGE
$smarty->assign('total_blogentries', $total_blogentries);
$smarty->assign_by_ref('blogentries', $blogentry_array);
$smarty->assign_by_ref('blogentrycats', $blogentrycats_array);

$smarty->assign('blog_search', $blog_search);

$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($blogentry_array));
$smarty->assign('s', $s);
$smarty->assign('v', $v);
$smarty->assign('c', $c);
include "footer.php";
?>