<?
$page = "user_article_edit_delete";
include "header.php";
if($user->level_info[level_article_allow] == 0) { header("Location: user_home.php"); exit(); }
if(isset($_GET['task'])) { $task = $_GET['task']; } elseif(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_GET['article_id'])) { $article_id = $_GET['article_id']; } elseif(isset($_POST['article_id'])) { $article_id = $_POST['article_id']; } else { $article_id = 0; }

// INITIALIZE EVENT OBJECT
$article = new rc_article($user->user_info[user_id], $article_id);

if($article->article_exists == 0) { header("Location: user_article.php"); exit(); }
if($article->article_info[article_user_id] != $user->user_info[user_id]) { header("Location: user_article.php"); exit(); }




if($task == "dodelete") {
  $article->article_delete($article->article_info[article_id]);
  header("Location: user_article.php");
  exit();
}






// ASSIGN VARIABLES AND SHOW DELETE EVENTS PAGE
$smarty->assign('article', $article);
include "footer.php";
?>