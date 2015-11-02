<?php

$page = "browse_polls";
include "header.php";


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( (!$user->user_exists && !$setting['setting_permission_poll']) || ($user->user_exists && (1 & ~(int)$user->level_info['level_poll_allow'])) )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}


// PARSE GET/POST
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "poll_datecreated DESC"; }
if(isset($_POST['v'])) { $v = $_POST['v']; } elseif(isset($_GET['v'])) { $v = $_GET['v']; } else { $v = 0; }


// ENSURE SORT/VIEW ARE VALID
if($s != "poll_datecreated DESC" && $s != "poll_totalvotes DESC" && $s != "poll_views DESC") { $s = "poll_datecreated DESC"; }
if($v != "0" && $v != "1") { $v = 0; }


// SET WHERE CLAUSE
$where = "CASE
	    WHEN se_polls.poll_user_id='{$user->user_info['user_id']}'
	      THEN TRUE
	    WHEN ((se_polls.poll_privacy & @SE_PRIVACY_REGISTERED) AND '{$user->user_exists}'<>0)
	      THEN TRUE
	    WHEN ((se_polls.poll_privacy & @SE_PRIVACY_ANONYMOUS) AND '{$user->user_exists}'=0)
	      THEN TRUE
	    WHEN ((se_polls.poll_privacy & @SE_PRIVACY_FRIEND) AND (SELECT TRUE FROM se_friends WHERE friend_user_id1=se_polls.poll_user_id AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
	      THEN TRUE
	    WHEN ((se_polls.poll_privacy & @SE_PRIVACY_SUBNET) AND '{$user->user_exists}'<>0 AND (SELECT TRUE FROM se_users WHERE user_id=se_polls.poll_user_id AND user_subnet_id='{$user->user_info['user_subnet_id']}' LIMIT 1))
	      THEN TRUE
	    WHEN ((se_polls.poll_privacy & @SE_PRIVACY_FRIEND2) AND (SELECT TRUE FROM se_friends AS friends_primary LEFT JOIN se_users ON friends_primary.friend_user_id1=se_users.user_id LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE friends_primary.friend_user_id1=se_polls.poll_user_id AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' AND se_users.user_subnet_id='{$user->user_info['user_subnet_id']}' LIMIT 1))
	      THEN TRUE
	    ELSE FALSE
	END";


// ONLY MY FRIENDS' POLLS
if( $v=="1" && $user->user_exists )
{
  // SET WHERE CLAUSE
  $where .= " && (
    SELECT
      TRUE
    FROM
      se_friends
    WHERE
      friend_user_id1='{$user->user_info['user_id']}' &&
      friend_user_id2=se_polls.poll_user_id &&
      friend_status=1
    )
  ";
}


// CREATE poll OBJECT
$poll = new se_poll();

// GET TOTAL pollS
$total_polls = $poll->poll_total($where);

// MAKE ENTRY PAGES
$polls_per_page = 10;
$page_vars = make_page($total_polls, $polls_per_page, $p);

// GET poll ARRAY
$poll_array = $poll->poll_list($page_vars[0], $polls_per_page, $s, $where, TRUE);



// ASSIGN SMARTY VARIABLES AND DISPLAY pollS PAGE
$smarty->assign('polls', $poll_array);
$smarty->assign('total_polls', $total_polls);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($poll_array));
$smarty->assign('s', $s);
$smarty->assign('v', $v);
include "footer.php";
?>