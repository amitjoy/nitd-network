<?
$page = "user_article_edit_files_comments";
include "header.php";
if($user->level_info[level_article_allow] == 0) { header("Location: user_home.php"); exit(); }

if(isset($_GET['article_id'])) { $article_id = $_GET['article_id']; } elseif(isset($_POST['article_id'])) { $article_id = $_POST['article_id']; } else { $article_id = 0; }
if(isset($_POST['articlemedia_id'])) { $articlemedia_id = $_POST['articlemedia_id']; } elseif(isset($_GET['articlemedia_id'])) { $articlemedia_id = $_GET['articlemedia_id']; } else { $articlemedia_id = 0; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }

// INITIALIZE ARTICLE OBJECT
$article = new rc_article($user->user_info[user_id], $article_id);

if($article->article_exists == 0) { header("Location: user_article.php"); exit(); }
if($article->article_info[article_user_id] != $user->user_info[user_id]) { header("Location: user_article.php"); exit(); }

// GET ARTICLE ALBUM INFO
$articlealbum_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_articlealbums WHERE articlealbum_article_id='".$article->article_info[article_id]."' LIMIT 1"));

// MAKE SURE MEDIA EXISTS
$articlemedia_query = $database->database_query("SELECT * FROM se_articlemedia WHERE articlemedia_id='$articlemedia_id' AND articlemedia_articlealbum_id='$articlealbum_info[articlealbum_id]' LIMIT 1");
if($database->database_num_rows($articlemedia_query) != 1) { header("Location: user_article_edit_files.php?article_id=".$article->article_info[article_id]); exit(); }
$articlemedia_info = $database->database_fetch_assoc($articlemedia_query);




// CREATE MEDIA COMMENT OBJECT
$comments_per_page = 10;
$comment = new se_comment('articlemedia', 'articlemedia_id', $articlemedia_info[articlemedia_id]);


// DELETE NECESSARY COMMENTS
$start = ($p - 1) * $comments_per_page;
if($task == "delete") { $comment->comment_delete_selected($start, $comments_per_page); }


// GET TOTAL COMMENTS
$total_comments = $comment->comment_total();

// MAKE COMMENT PAGES
$page_vars = make_page($total_comments, $comments_per_page, $p);

// GET COMMENT ARRAY
$comments = $comment->comment_list($page_vars[0], $comments_per_page);



// ASSIGN VARIABLES AND SHOW ALBUM COMMENTS PAGE
$smarty->assign('articlemedia_id', $articlemedia_info[articlemedia_id]);
$smarty->assign('article', $article);
$smarty->assign('comments', $comments);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($comments));
include "footer.php";
?>