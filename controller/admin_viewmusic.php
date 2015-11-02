<?php
$page = "admin_viewmusic";
include "admin_header.php";

if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "id"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['f_title'])) { $f_title = $_POST['f_title']; } elseif(isset($_GET['f_title'])) { $f_title = $_GET['f_title']; } else { $f_title = ""; }
if(isset($_POST['f_owner'])) { $f_owner = $_POST['f_owner']; } elseif(isset($_GET['f_owner'])) { $f_owner = $_GET['f_owner']; } else { $f_owner = ""; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['music_id'])) { $music_id = $_POST['music_id']; } elseif(isset($_GET['music_id'])) { $music_id = $_GET['music_id']; }
if(isset($_POST['owner'])) { $owner = $_POST['owner']; } elseif(isset($_GET['owner'])) { $owner = $_GET['owner']; }



// CREATE MUSIC OBJECT
$entries_per_page = 100;
$music = new se_music();



// DELETE SONG
if( $task=="deletesong" )
{
  // OUTPUT
  if( $music->music_delete($music_id) )
    echo '{"result":"success"}';
  else
    echo '{"result":"failure"}';
  
  exit();
}


// DELETE MULTTPLE SONGS
elseif( $task=="delete_selected" && !empty($_POST['delete_entry']) && is_array($_POST['delete_entry']) )
{
  foreach( $_POST['delete_entry'] as $delete_music_id )
    $music->music_delete($delete_music_id);
}




// SET MUSIC SORT-BY VARIABLES FOR HEADING LINKS
$i = "id";   // MUSIC_ID
$t = "t";    // MUSIC_TITLE
$o = "o";    // OWNER OF MUSIC
$d = "d";    // DATE OF MUSIC

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "i") {
  $sort = "se_music.music_id";
  $i = "id";
} elseif($s == "id") {
  $sort = "se_music.music_id DESC";
  $i = "i";
} elseif($s == "t") {
  $sort = "se_music.music_title";
  $t = "td";
} elseif($s == "td") {
  $sort = "se_music.music_title DESC";
  $t = "t";
} elseif($s == "o") {
  $sort = "se_users.user_username";
  $o = "od";
} elseif($s == "od") {
  $sort = "se_users.user_username DESC";
  $o = "o";
} elseif($s == "d") {
  $sort = "se_music.music_date";
  $d = "dd";
} elseif($s == "dd") {
  $sort = "se_music.music_date DESC";
  $d = "d";
} else {
  $sort = "se_music.music_id DESC";
  $i = "i";
}




// ADD CRITERIA FOR FILTER
$where = "";
if($f_owner != "") { $where .= "se_users.user_id LIKE '%$f_owner%'"; }
if($f_owner != "" & $f_title != "") { $where .= " AND"; }
if($f_title != "") { $where .= " se_music.music_title LIKE '%$f_title%'"; }
if($where != "") { $where = "(".$where.")"; }

// GET TOTAL ENTRIES
$total_music = $music->music_list_total(NULL, NULL, $where);

// MAKE ENTRY PAGES
$page_vars = make_page($total_music, $entries_per_page, $p);
$page_array = Array();
for($x=0;$x<=$page_vars[2]-1;$x++) {
  if($x+1 == $page_vars[1]) { $link = "1"; } else { $link = "0"; }
  $page_array[$x] = Array('page' => $x+1,
			  'link' => $link);
}

// GET SONG LIST ARRAY
$music_list = $music->music_list($page_vars[0], $entries_per_page, $sort, $where);


// ASSIGN VARIABLES AND SHOW VIEW ENTRIES PAGE
$smarty->assign('total_music', $total_music);
$smarty->assign('pages', $page_array);
$smarty->assign('entries', $music_list);
$smarty->assign('f_title', $f_title);
$smarty->assign('f_owner', $f_owner);
$smarty->assign('i', $i);
$smarty->assign('t', $t);
$smarty->assign('o', $o);
$smarty->assign('v', $v);
$smarty->assign('d', $d);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('s', $s);
include "admin_footer.php";
?>