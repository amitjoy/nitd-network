<?php

$page = "admin_viewalbums";
include "admin_header.php";

if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['f_title'])) { $f_title = $_POST['f_title']; } elseif(isset($_GET['f_title'])) { $f_title = $_GET['f_title']; } else { $f_title = ""; }
if(isset($_POST['f_owner'])) { $f_owner = $_POST['f_owner']; } elseif(isset($_GET['f_owner'])) { $f_owner = $_GET['f_owner']; } else { $f_owner = ""; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['album_id'])) { $album_id = $_POST['album_id']; } elseif(isset($_GET['album_id'])) { $album_id = $_GET['album_id']; } else { $album_id = 0; }

// CREATE ALBUM OBJECT
$albums_per_page = 100;
$album = new se_album();


// DELETE ALBUM
if($task == "deletealbum") {
  if($database->database_num_rows($database->database_query("SELECT album_id FROM se_albums WHERE album_id='$album_id'")) == 1) { 
    $album->album_delete($album_id);
  }
}


// SET ALBUM SORT-BY VARIABLES FOR HEADING LINKS
$i = "id";   // ALBUM_ID
$t = "t";    // ALBUM_TITLE
$u = "u";    // OWNER OF ALBUM
$f = "f";    // FILES IN ALBUM
$su = "su";  // TOTAL SPACE USED

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "i") {
  $sort = "se_albums.album_id";
  $i = "id";
} elseif($s == "id") {
  $sort = "se_albums.album_id DESC";
  $i = "i";
} elseif($s == "t") {
  $sort = "se_albums.album_title";
  $t = "td";
} elseif($s == "td") {
  $sort = "se_albums.album_title DESC";
  $t = "t";
} elseif($s == "u") {
  $sort = "user_username";
  $u = "ud";
} elseif($s == "ud") {
  $sort = "user_username DESC";
  $u = "u";
} elseif($s == "f") {
  $sort = "total_files";
  $f = "fd";
} elseif($s == "fd") {
  $sort = "total_files DESC";
  $f = "f";
} elseif($s == "su") {
  $sort = "total_space";
  $su = "sud";
} elseif($s == "sud") {
  $sort = "total_space DESC";
  $su = "su";
} else {
  $sort = "se_albums.album_id DESC";
  $i = "i";
}


// ADD CRITERIA FOR FILTER
$where_clause = Array();
if($f_owner != "") { $where_clause[] = "(se_users.user_username LIKE '%$f_owner%' OR CONCAT(se_users.user_fname, ' ', se_users.user_lname) LIKE '%$f_owner%')"; }
if($f_title != "") { $where_clause[] = " se_albums.album_title LIKE '%$f_title%'"; }
if(count($where_clause) != 0) { $where = "(".implode(" AND ", $where_clause).")"; }


// DELETE NECESSARY ALBUMS
$start = ($p - 1) * $albums_per_page;
if($task == "delete") { $album->album_delete_selected($start, $albums_per_page, $sort, $where); }

// GET TOTAL ALBUMS
$total_albums = $album->album_total($where);

// MAKE ALBUM PAGES
$page_vars = make_page($total_albums, $albums_per_page, $p);
$page_array = Array();
for($x=0;$x<=$page_vars[2]-1;$x++) {
  if($x+1 == $page_vars[1]) { $link = "1"; } else { $link = "0"; }
  $page_array[$x] = Array('page' => $x+1,
			  'link' => $link);
}

// GET ALBUM ARRAY
$albums = $album->album_list($page_vars[0], $albums_per_page, $sort, $where);







// ASSIGN VARIABLES AND SHOW VIEW ALBUMS PAGE
$smarty->assign('total_albums', $total_albums);
$smarty->assign('pages', $page_array);
$smarty->assign('albums', $albums);
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