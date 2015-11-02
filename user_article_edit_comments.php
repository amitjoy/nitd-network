<?
$page = "user_article_edit_comments";
include "header.php";
if($user->level_info[level_article_allow] == 0) { header("Location: user_home.php"); exit(); }
if(isset($_POST['article_id'])) { $article_id = $_POST['article_id']; } elseif(isset($_GET['article_id'])) { $article_id = $_GET['article_id']; } else { $article_id = 0; }
if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

// INITIALIZE ARTICLE OBJECT
$article = new rc_article($user->user_info[user_id], $article_id);

if($article->article_exists == 0) { header("Location: user_article.php"); exit(); }
if($article->article_info[article_user_id] != $user->user_info[user_id]) { header("Location: user_article.php"); exit(); }


// CREATE PROFILE COMMENT OBJECT
$comments_per_page = 10;
$comment = new se_comment('article', 'article_id', $article->article_info[article_id]);


// DELETE NECESSARY COMMENTS
$start = ($p - 1) * $comments_per_page;
if($task == "delete") { $comment->comment_delete_selected($start, $comments_per_page); }


// GET TOTAL COMMENTS
$total_comments = $comment->comment_total();

// MAKE COMMENT PAGES
$page_vars = make_page($total_comments, $comments_per_page, $p);

// GET COMMENT ARRAY
$comments = $comment->comment_list($page_vars[0], $comments_per_page);







// ASSIGN VARIABLES AND DISPLAY MODERATE COMMENTS PAGE
$smarty->assign('article', $article);
$smarty->assign('comments', $comments);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($comments));
include "footer.php";
?>