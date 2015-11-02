<?
$page = "admin_viewarticles";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['s'])) { $s = $_POST['s']; } elseif(isset($_GET['s'])) { $s = $_GET['s']; } else { $s = "dd"; }
if(isset($_POST['search'])) { $search = $_POST['search']; } elseif(isset($_GET['search'])) { $search = $_GET['search']; } else { $search = ""; }

if(isset($_POST['article_id'])) { $article_id = $_POST['article_id']; } elseif(isset($_GET['article_id'])) { $article_id = $_GET['article_id']; } else { $article_id = ""; }
if(isset($_POST['value'])) { $value = $_POST['value']; } elseif(isset($_GET['value'])) { $value = $_GET['value']; } else { $value = ""; }
if ($task == 'approve') {
  $rc_article = new rc_article(null, $article_id);
  
  if ($rc_article->article_info[article_id]) {
    $value = $value > 0 ? 1 : 0;
    $database->database_query("UPDATE se_articles SET article_approved='$value' WHERE article_id='".$rc_article->article_info[article_id]."'");
    
    $rc_article->article_owner();
    
    $article_user = new se_user(array($rc_article->article_info[article_user_id],'',''));
    $article_date_start = $rc_article->article_info[article_date_start];
    if ($article_user->user_exists != 0 and $value == 1 and $article_date_start > 0) {
    	include_once('../include/class_actions.php');
    	$actions = new se_actions();
      $article_title_short = $rc_article->article_info[article_title];
      if(strlen($article_title_short) > 100) { $article_title_short = substr($article_title_short, 0, 97); $article_title_short .= "..."; }
      
      $actions->actions_add($rc_article->article_owner, "articleapprove", Array($rc_article->article_owner->user_info[user_username], $rc_article->article_owner->user_displayname, $article_id, $article_title_short, date("F j, Y, g:i a",$article_date_start)), Array(), 0, FALSE, "user", $rc_article->article_owner->user_info[user_id], $rc_article->article_info[article_privacy]);
    }

  }
  //rc_toolkit::redirect("admin_viewarticles.php");
}
elseif ($task == 'feature') {
  $rc_article = new rc_article(null, $article_id);
  if ($rc_article->article_info[article_id]) {
    $value = $value > 0 ? 1 : 0;
    $database->database_query("UPDATE se_articles SET article_featured='$value' WHERE article_id='".$rc_article->article_info[article_id]."'");
  }
  //rc_toolkit::redirect("admin_viewarticles.php");  
}
elseif ($task == 'delete') {
	$database->database_query("DELETE FROM se_articles WHERE article_id='$article_id'");	
}


// SET ENTRY SORT-BY VARIABLES FOR HEADING LINKS
$d = "dd"; 
$t = "t"; 
$c = "cd";
$r = "r";
$a = "a";
$f = "f";
$o = "o";
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
} elseif($s == "o") {
  $sort = "user_username";
  $o = "od";
} elseif($s == "od") {
  $sort = "user_username DESC";
  $o = "o";
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


// CREATE ARTICLE OBJECT
$entries_per_page = 20;
$article = new rc_article();

$start = ($p - 1) * $entries_per_page;

// GET TOTAL ENTRIES
$total_articleentries = $article->article_total($where);

//rc_toolkit::debug($total_articleentries,"total_articleentries");

// MAKE ENTRY PAGES
$page_vars = make_page($total_articleentries, $entries_per_page, $p);

if ($total_articleentries > 0)
{
// GET ENTRY ARRAY
$articleentries = $article->article_list($page_vars[0], $entries_per_page, $sort, $where, 1);
}

//rc_toolkit::debug($articleentries);

// ASSIGN VARIABLES AND SHOW VIEW ENTRIES PAGE
$smarty->assign('s', $s);
$smarty->assign('d', $d);
$smarty->assign('t', $t);
$smarty->assign('c', $c);
$smarty->assign('o', $o);
$smarty->assign('r', $r);
$smarty->assign('a', $a);
$smarty->assign('f', $f);
$smarty->assign('g', $g);

$smarty->assign('search', $search);
$smarty->assign('articleentries', $articleentries);
$smarty->assign('total_articleentries', $total_articleentries);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($articleentries));
include "admin_footer.php";
exit();
?>