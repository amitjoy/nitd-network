<?
$page = "article_album_file";
include "header.php";

// MAKE SURE MEDIA VARS ARE SET IN URL
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }
if(isset($_POST['articlemedia_id'])) { $articlemedia_id = $_POST['articlemedia_id']; } elseif(isset($_GET['articlemedia_id'])) { $articlemedia_id = $_GET['articlemedia_id']; } else { $articlemedia_id = 0; }
if(isset($_POST['article_id'])) { $article_id = $_POST['article_id']; } elseif(isset($_GET['article_id'])) { $article_id = $_GET['article_id']; } else { $article_id = 0; }

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if($user->user_exists == 0 & $setting[setting_permission_article] == 0) {
  $smarty->assign('error_header', 11150915);
  $smarty->assign('error_message', 11150917);
  $smarty->assign('error_submit', 11150925);
  $smarty->display("error.tpl");
  exit();
}


// INITIALIZE ARTICLE OBJECT
$article = new rc_article($user->user_info[user_id], $article_id);
if($article->article_exists == 0) { header("Location: home.php"); exit(); }

if(!$article->is_article_active()) { 
 // header("Location: article.php?article_id=".$article->article_info[article_id]); exit();
}

// GET ARTICLE ALBUM INFO
$articlealbum_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_articlealbums WHERE articlealbum_article_id='".$article->article_info[article_id]."' LIMIT 1"));


// MAKE SURE MEDIA EXISTS
$articlemedia_query = $database->database_query("SELECT * FROM se_articlemedia WHERE articlemedia_id='$articlemedia_id' AND articlemedia_articlealbum_id='$articlealbum_info[articlealbum_id]' LIMIT 1");
if($database->database_num_rows($articlemedia_query) != 1) { header("Location: article.php?article_id=".$article->article_info[article_id]); exit(); }
$articlemedia_info = $database->database_fetch_assoc($articlemedia_query);

$article->article_owner();
$owner = $article->article_owner;

// CHECK PRIVACY
$privacy_max = $owner->user_privacy_max($user);
if(!($article->article_info[article_privacy] & $privacy_max)) {
  header("Location: article.php?article_id=".$article->article_info[article_id]); exit();
}


// UPDATE ALBUM VIEWS
$articlealbum_views_new = $articlealbum_info[articlealbum_views] + 1;
$database->database_query("UPDATE se_articlealbums SET articlealbum_views='$articlealbum_views_new' WHERE articlealbum_id='$articlealbum_info[articlealbum_id]' LIMIT 1");

// CHECK IF USER IS ALLOWED TO COMMENT
$allowed_to_comment = 1;
if(!($privacy_max & $article->article_info[article_comments])) { $allowed_to_comment = 0; }


// IF A COMMENT IS BEING POSTED
if($task == "dopost" & $allowed_to_comment != 0) {

  $comment_date = time();
  $comment_body = $_POST['comment_body'];

  // RETRIEVE AND CHECK SECURITY CODE IF NECESSARY
  if($setting[setting_comment_code] != 0) {
    session_start();
    $code = $_SESSION['code'];
    if($code == "") { $code = randomcode(); }
    $comment_secure = $_POST['comment_secure'];

    if($comment_secure != $code) { $is_error = 1; }
  }

  // MAKE SURE COMMENT BODY IS NOT EMPTY
  $comment_body = censor(str_replace("\r\n", "<br>", $comment_body));
  $comment_body = preg_replace('/(<br>){3,}/is', '<br><br>', $comment_body);
  $comment_body = ChopText($comment_body);
  if(str_replace(" ", "", $comment_body) == "") { $is_error = 1; $comment_body = ""; }

  // ADD COMMENT IF NO ERROR
  if($is_error == 0) {
    $database->database_query("INSERT INTO se_articlemediacomments (articlemediacomment_articlemedia_id, articlemediacomment_authoruser_id, articlemediacomment_date, articlemediacomment_body) VALUES ('$articlemedia_info[articlemedia_id]', '".$user->user_info[user_id]."', '$comment_date', '$comment_body')");

    // INSERT ACTION IF USER EXISTS
    if($user->user_exists != 0) {
      $commenter = $user->user_info[user_username];
      $comment_body_encoded = $comment_body;
      if(strlen($comment_body_encoded) > 250) { 
        $comment_body_encoded = substr($comment_body_encoded, 0, 240);
        $comment_body_encoded .= "...";
      }
      $comment_body_encoded = htmlspecialchars(str_replace("<br>", " ", $comment_body_encoded));
      
      $actions->actions_add($user, "articlemediacomment", Array($user->user_info[user_username], $user->user_displayname, $article_id, $article->article_info[article_title], $comment_body_encoded, $articlemedia_info[articlemedia_id]), Array(), 0, FALSE, "user", $user->user_info[user_id], $article->article_info[article_privacy]);
    } else { 
      $commenter = 11150914;
    }

    // SEND COMMENT NOTIFICATION IF NECESSARY
    $articleowner_info = $database->database_fetch_assoc($database->database_query("SELECT se_users.user_id, se_users.user_username, se_users.user_email, se_usersettings.usersetting_notify_articlemediacomment FROM se_users LEFT JOIN se_usersettings ON se_users.user_id=se_usersettings.usersetting_user_id WHERE se_users.user_id='".$article->article_info[article_user_id]."'"));
    if($articleowner_info[usersetting_notify_articlemediacomment] == 1 & $articleowner_info[user_id] != $user->user_info[user_id]) { 
      send_generic($articleowner_info[user_email], "$setting[setting_email_fromname] <$setting[setting_email_fromemail]>", $setting[setting_email_articlemediacomment_subject], $setting[setting_email_articlemediacomment_message], Array('[username]', '[commenter]', '[articlename]', '[link]'), Array($articleowner_info[user_username], $commenter, $article->article_info[article_title], "<a href=\"".$url->url_base."article_album_file.php?article_id=".$article->article_info[article_id]."&articlemedia_id=$articlemedia_info[articlemedia_id]\">".$url->url_base."article_album_file.php?article_id=".$article->article_info[article_id]."&articlemedia_id=$articlemedia_info[articlemedia_id]</a>")); 
    }
  }

  echo "<html><head><script type=\"text/javascript\">";
  echo "window.parent.addComment('$is_error', '$comment_body', '$comment_date');";
  echo "</script></head><body></body></html>";
  exit();
}



// GET ARTICLE MEDIA COMMENTS
$comment = new se_comment('articlemedia', 'articlemedia_id', $articlemedia_info[articlemedia_id]);
$total_comments = $comment->comment_total();
$comments = $comment->comment_list(0, $total_comments);




// CREATE BACK MENU LINK
$back = $database->database_query("SELECT articlemedia_id FROM se_articlemedia WHERE articlemedia_articlealbum_id='$articlemedia_info[articlemedia_articlealbum_id]' AND articlemedia_id<'$articlemedia_info[articlemedia_id]' ORDER BY articlemedia_id DESC LIMIT 1");
if($database->database_num_rows($back) == 1) {
  $back_info = $database->database_fetch_assoc($back);
  $link_back = $url->base."article_album_file.php?article_id=".$article->article_info[article_id]."&articlemedia_id=$back_info[articlemedia_id]";
} else {
  $link_back = "#";
}

// CREATE FIRST MENU LINK
$first = $database->database_query("SELECT articlemedia_id FROM se_articlemedia WHERE articlemedia_articlealbum_id='$articlemedia_info[articlemedia_articlealbum_id]' ORDER BY articlemedia_id ASC LIMIT 1");
if($database->database_num_rows($first) == 1 AND $link_back != "#") {
  $first_info = $database->database_fetch_assoc($first);
  $link_first = $url->base."article_album_file.php?article_id=".$article->article_info[article_id]."&articlemedia_id=$first_info[articlemedia_id]";
} else {
  $link_first = "#";
}

// CREATE NEXT MENU LINK
$next = $database->database_query("SELECT articlemedia_id FROM se_articlemedia WHERE articlemedia_articlealbum_id='$articlemedia_info[articlemedia_articlealbum_id]' AND articlemedia_id>'$articlemedia_info[articlemedia_id]' ORDER BY articlemedia_id ASC LIMIT 1");
if($database->database_num_rows($next) == 1) {
  $next_info = $database->database_fetch_assoc($next);
  $link_next = $url->base."article_album_file.php?article_id=".$article->article_info[article_id]."&articlemedia_id=$next_info[articlemedia_id]";
} else {
  $link_next = "#";
}

// CREATE END MENU LINK
$end = $database->database_query("SELECT articlemedia_id FROM se_articlemedia WHERE articlemedia_articlealbum_id='$articlemedia_info[articlemedia_articlealbum_id]' ORDER BY articlemedia_id DESC LIMIT 1");
if($database->database_num_rows($end) == 1 AND $link_next != "#") {
  $end_info = $database->database_fetch_assoc($end);
  $link_end = $url->base."article_album_file.php?article_id=".$article->article_info[article_id]."&articlemedia_id=$end_info[articlemedia_id]";
} else {
  $link_end = "#";
}



// GET CUSTOM ARTICLE STYLE IF ALLOWED
if($article->articleowner_level_info[level_article_style] != 0 & $is_article_private == 0) { 
  $articlestyle_info = $database->database_fetch_assoc($database->database_query("SELECT articlestyle_css FROM se_articlestyles WHERE articlestyle_article_id='".$article->article_info[article_id]."' LIMIT 1")); 
  $global_css = $articlestyle_info[articlestyle_css];
}





// ASSIGN VARIABLES AND DISPLAY ALBUM FILE PAGE
$smarty->assign('article', $article);
$smarty->assign('articlemedia_info', $articlemedia_info);
$smarty->assign('comments', $comments);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('allowed_to_comment', $allowed_to_comment);
$smarty->assign('link_first', $link_first);
$smarty->assign('link_back', $link_back);
$smarty->assign('link_next', $link_next);
$smarty->assign('link_end', $link_end);
include "footer.php";
?>