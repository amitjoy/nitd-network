<?php
$page = "browse_music";
include "header.php";

if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "music_date DESC"; }
if(isset($_POST['v'])) { $v = $_POST['v']; } elseif(isset($_GET['v'])) { $v = $_GET['v']; } else { $v = 0; }

// ENSURE SORT/VIEW ARE VALID
if($s != "music_date DESC" && $s != "music_track_num ASC") { $s = "music_date DESC"; }
if($v != "0" && $v != "1") { $v = 0; }


// ONLY MY FRIENDS' MUSIC
if( $v=="1" && $user->user_exists )
{
  // SET WHERE CLAUSE
  $where = "(
    SELECT
      TRUE
    FROM
      se_friends
    WHERE
      friend_user_id1={$user->user_info[user_id]} &&
      friend_user_id2=se_music.music_user_id &&
      friend_status=1
    ) 
  ";
}


// CREATE ALBUM OBJECT
$music_object = new se_music();

// GET TOTAL ALBUMS
$browse_music_total = $music_object->music_list_total(NULL, NULL, $where);

// MAKE ENTRY PAGES
$music_per_page = 20;
$page_vars = make_page($browse_music_total, $music_per_page, $p);

// GET ALBUM ARRAY
$browse_music_list = $music_object->music_list($page_vars[0], $music_per_page, $s, $where);


// ASSIGN SMARTY VARIABLES AND DISPLAY MUSIC PAGE
$smarty->assign('browse_music_list', $browse_music_list);
$smarty->assign('browse_music_total', $browse_music_total);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($browse_music_list));
$smarty->assign('s', $s);
$smarty->assign('v', $v);
include "footer.php";
?>