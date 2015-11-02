<?php

$page = "admin_viewvideos";
include "admin_header.php";

if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['f_title'])) { $f_title = $_POST['f_title']; } elseif(isset($_GET['f_title'])) { $f_title = $_GET['f_title']; } else { $f_title = ""; }
if(isset($_POST['f_owner'])) { $f_owner = $_POST['f_owner']; } elseif(isset($_GET['f_owner'])) { $f_owner = $_GET['f_owner']; } else { $f_owner = ""; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['video_id'])) { $video_id = $_POST['video_id']; } elseif(isset($_GET['video_id'])) { $video_id = $_GET['video_id']; } else { $video_id = 0; }

// CREATE VIDEO OBJECT
$videos_per_page = 100;
$video = new se_video();


// DELETE VIDEO
if($task == "deletevideo") {
  if($database->database_num_rows($database->database_query("SELECT video_id FROM se_videos WHERE video_id='$video_id'")) == 1) { 
    $video->video_delete($video_id);
  }
}


// SET VIDEO SORT-BY VARIABLES FOR HEADING LINKS
$i = "id";   // VIDEO_ID
$t = "t";    // VIDEO_TITLE
$u = "u";    // OWNER OF VIDEO

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "i") {
  $sort = "se_videos.video_id";
  $i = "id";
} elseif($s == "id") {
  $sort = "se_videos.video_id DESC";
  $i = "i";
} elseif($s == "t") {
  $sort = "se_videos.video_title";
  $t = "td";
} elseif($s == "td") {
  $sort = "se_videos.video_title DESC";
  $t = "t";
} elseif($s == "u") {
  $sort = "user_username";
  $u = "ud";
} elseif($s == "ud") {
  $sort = "user_username DESC";
  $u = "u";
} else {
  $sort = "se_videos.video_id DESC";
  $i = "i";
}


// ADD CRITERIA FOR FILTER
$where_clause = Array();
if($f_owner != "") { $where_clause[] = "(se_users.user_username LIKE '%$f_owner%' OR CONCAT(se_users.user_fname, ' ', se_users.user_lname) LIKE '%$f_owner%')"; }
if($f_title != "") { $where_clause[] = " se_videos.video_title LIKE '%$f_title%'"; }
if(count($where_clause) != 0) { $where = "(".implode(" AND ", $where_clause).")"; }


// DELETE NECESSARY VIDEOS
$start = ($p - 1) * $videos_per_page;
if($task == "delete") { $video->video_delete_selected($start, $videos_per_page, $sort, $where); }

// GET TOTAL VIDEOS
$total_videos = $video->video_total($where);

// MAKE VIDEO PAGES
$page_vars = make_page($total_videos, $videos_per_page, $p);
$page_array = Array();
for($x=0;$x<=$page_vars[2]-1;$x++) {
  if($x+1 == $page_vars[1]) { $link = "1"; } else { $link = "0"; }
  $page_array[$x] = Array('page' => $x+1,
			  'link' => $link);
}

// GET VIDEO ARRAY
$videos = $video->video_list($page_vars[0], $videos_per_page, $sort, $where);







// ASSIGN VARIABLES AND SHOW VIEW VIDEOS PAGE
$smarty->assign('total_videos', $total_videos);
$smarty->assign('pages', $page_array);
$smarty->assign('videos', $videos);
$smarty->assign('f_title', $f_title);
$smarty->assign('f_owner', $f_owner);
$smarty->assign('i', $i);
$smarty->assign('t', $t);
$smarty->assign('u', $u);
$smarty->assign('f', $f);
$smarty->assign('su', $su);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('s', $s);
include "admin_footer.php";
?>