<?
$page = "user_article";
include "header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "dd"; }
if(isset($_POST['search'])) { $search = $_POST['search']; } elseif(isset($_GET['search'])) { $search = $_GET['search']; } else { $search = ""; }


// ENSURE BLOGS ARE ENABLED FOR THIS USER
if($user->level_info[level_article_allow] == 0) { header("Location: user_home.php"); exit(); }



// SET ENTRY SORT-BY VARIABLES FOR HEADING LINKS
$d = "dd";    // BLOGENTRY_DATE
$t = "t";     // BLOGENTRY_TITLE
$c = "cd";    // TOTAL_COMMENTS
$r = "r";
$a = "a";
$f = "f";
$g = "g";

// SET SORT VARIABLE FOR DATABASE QUERY
if($s == "d") {
  $sort = "article_date_start";
  $d = "dd";
} elseif($s == "dd") {
  $sort = "article_date_start DESC";
  $d = "d";
} elseif($s == "t") {
  $sort = "article_title";
  $t = "td";
} elseif($s == "td") {
  $sort = "article_title DESC";
  $t = "t";
} elseif($s == "c") {
  $sort = "article_views";
  $c = "cd";
} elseif($s == "cd") {
  $sort = "article_views DESC";
  $c = "c";
} elseif($s == "r") {
  $sort = "article_draft";
  $r = "cd";
} elseif($s == "rd") {
  $sort = "article_draft DESC";
  $r = "r";
} elseif($s == "a") {
  $sort = "article_approved";
  $a = "ad";
} elseif($s == "ad") {
  $sort = "article_approved DESC";
  $a = "a";
} elseif($s == "f") {
  $sort = "article_featured";
  $f = "fd";
} elseif($s == "fd") {
  $sort = "article_featured DESC";
  $f = "f";
} elseif($s == "g") {
  $sort = "articlecat_title";
  $g = "gd";
} elseif($s == "gd") {
  $sort = "articlecat_title DESC";
  $g = "g";
}
else {
  $sort = "article_date_start DESC";
  $d = "d";
}

// SET WHERE CLAUSE
if($search != "") { $where = "(article_title LIKE '%$search%' OR article_body LIKE '%$search%')"; } else { $where = ""; }


// CREATE BLOG OBJECT
$entries_per_page = 20;
$article = new rc_article($user->user_info[user_id]);

// DELETE NECESSARY ENTRIES
$start = ($p - 1) * $entries_per_page;

// GET TOTAL ENTRIES
$total_articleentries = $article->article_total($where);

//rc_toolkit::debug($total_articleentries,"total_articleentries");

// MAKE ENTRY PAGES
$page_vars = make_page($total_articleentries, $entries_per_page, $p);

// GET ENTRY ARRAY
$articleentries = $article->article_list($page_vars[0], $entries_per_page, $sort, $where, 1);

// ASSIGN VARIABLES AND SHOW VIEW ENTRIES PAGE
$smarty->assign('s', $s);
$smarty->assign('d', $d);
$smarty->assign('t', $t);
$smarty->assign('c', $c);
$smarty->assign('g', $g);
$smarty->assign('r', $r);
$smarty->assign('a', $a);
$smarty->assign('f', $f);

$smarty->assign('search', $search);
$smarty->assign('articleentries', $articleentries);
$smarty->assign('total_articleentries', $total_articleentries);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($articleentries));
include "footer.php";
?>