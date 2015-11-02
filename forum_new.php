<?php
$page = "forum_new";
include "header.php";

if(isset($_GET['task'])) { $task = $_GET['task']; } elseif(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_GET['forum_id'])) { $forum_id = (int) $_GET['forum_id']; } elseif(isset($_POST['forum_id'])) { $forum_id = (int) $_POST['forum_id']; } else { $forum_id = 0; }
if(isset($_GET['topic_id'])) { $topic_id = (int) $_GET['topic_id']; } elseif(isset($_POST['topic_id'])) { $topic_id = (int) $_POST['topic_id']; } else { $topic_id = 0; }
if(isset($_GET['quote_id'])) { $quote_id = (int) $_GET['quote_id']; } elseif(isset($_POST['quote_id'])) { $quote_id = (int) $_POST['quote_id']; } else { $quote_id = 0; }
if(isset($_GET['post_id'])) { $post_id = (int) $_GET['post_id']; } elseif(isset($_POST['post_id'])) { $post_id = (int) $_POST['post_id']; } else { $post_id = 0; }

// IF FORUMS ARE TURNED OFF, FORWARD TO HOME PAGE
if($setting[setting_forum_status] == 0) { header("Location: home.php"); exit(); }

// IF FORUMS ARE IN MAINTENANCE MOD, FORWARD TO MAIN FORUM
if($setting[setting_forum_status] == 2 && (!$user->user_exists || ($user->user_exists && !$forum->forum_is_moderator($user->user_info[user_id])))) { header("Location: forum.php"); exit(); }

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if((!$user->user_exists && !$setting['setting_permission_forum'])) {
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// VALIDATE FORUM ID AND GET INFO
$forum_query = $database->database_query("SELECT * FROM se_forums WHERE forum_id='$forum_id'");
if($database->database_num_rows($forum_query) != 1) {
  header("Location: forum.php");
  exit();
}
$forum_info = $database->database_fetch_assoc($forum_query);

// SET DEFAULT VARS
$is_reply = false;
$is_edit = false;
$is_error = 0;
$topic_title = "";
$post_body = "";


// VALIDATE TOPIC ID IF AVAILABLE AND GET INFO
if($topic_id != 0) {
  $topic_query = $database->database_query("SELECT * FROM se_forumtopics WHERE forumtopic_forum_id='{$forum_info[forum_id]}' AND forumtopic_id='$topic_id'");
  if($database->database_num_rows($topic_query) != 1) {
    header("Location: forum_view.php?forum_id={$forum_info[forum_id]}");
    exit();
  }
  $topic_info = $database->database_fetch_assoc($topic_query);
  $is_reply = true;

  // VALIDATE POST ID AND GET INFO
  if($post_id != 0) {
    $post_query = $database->database_query("SELECT * FROM se_forumposts WHERE forumpost_forumtopic_id='{$topic_info[forumtopic_id]}' AND forumpost_id='{$post_id}'");
    if($database->database_num_rows($post_query) == 1) {
      $post_info = $database->database_fetch_assoc($post_query);
      $postmedia = $database->database_query("SELECT * FROM se_forummedia WHERE forummedia_forumtopic_id='{$topic_info[forumtopic_id]}' AND forummedia_id='{$post_info[forumpost_forummedia_id]}'");
      $postmedia_info[is_media] = false;
      if($database->database_num_rows($postmedia) == 1) {
	$postmedia_info = $database->database_fetch_assoc($postmedia);
        $postmedia_info[is_media] = true;
	$postmedia_info[forummedia_path] = "./uploads_forum/{$topic_info[forumtopic_id]}/{$postmedia_info[forummedia_id]}.{$postmedia_info[forummedia_ext]}";
      }
      $first_in_topic = $database->database_fetch_assoc($database->database_query("SELECT forumpost_id FROM se_forumposts WHERE forumpost_forumtopic_id='{$topic_info[forumtopic_id]}' ORDER BY forumpost_id ASC LIMIT 1"));
      if($first_in_topic[forumpost_id] == $post_info[forumpost_id]) { $show_title = true; } else { $show_title = false; }
      $is_reply = false;
      $is_edit = true;
      $topic_title = $topic_info[forumtopic_subject];
      $post_body = $post_info[forumpost_body];
    }
  }
}



// DETERMINE THE USER'S PERMISSIONS FOR THIS FORUM (VIEW, POST, MODERATE, ETC)
$forum_permission = $forum->forum_permission($forum_info[forum_id]);

// SEND USER BACK IF NOT ALLOWED TO VIEW OR POST IN THIS FORUM
if(!$forum_permission[allowed_to_view]) { header("Location: forum.php"); exit(); }
if(!$forum_permission[allowed_to_post]) { header("Location: forum_view.php?forum_id=".$forum_info[forum_id]); exit(); }
if($is_reply && $topic_info[forumtopic_closed]) { header("Location: forum_topic.php?forum_id=".$forum_info[forum_id]."&topic_id=".$topic_info[forumtopic_id]); exit(); }
if($is_edit && !$forum_permission[allowed_to_editall] && $topic_info[forumtopic_closed]) { header("Location: forum_topic.php?forum_id=".$forum_info[forum_id]."&topic_id=".$topic_info[forumtopic_id]); exit(); }

// IF EDITING, ENSURE USER IS ALLOWED TO
if($is_edit && (!$user->user_exists || (!$forum_permission[allowed_to_editall] && $user->user_info[user_id] != $post_info[forumpost_authoruser_id]))) { header("Location: forum_topic.php?forum_id=".$forum_info[forum_id]."&topic_id=".$topic_info[forumtopic_id]."&post_id=".$post_info[forumpost_id]."#post_".$post_info[forumpost_id]); exit(); }


// CREATE NEW TOPIC
if($task == "create") {

  $topic_title = $_POST['topic_title'];
  $post_body = str_replace("&amp;#160;", " ", $_POST['post_body']);

  // RETRIEVE AND CHECK SECURITY CODE IF NECESSARY
  if($setting['setting_forum_code']) {
    if( !session_id() ) session_start();
    $code = $_SESSION['code'];
    if($code == "") { $code = randomcode(); }
    if($_POST['comment_secure'] != $code) { $is_error = 832; }
  }

  // IF SECURITY CODE IS FINE, CONTINUE
  if($is_error == 0) { 
    $results = $forum->forum_topic_new($forum_info[forum_id], $forum_info[forum_title], $topic_title, $post_body); 
    $is_error = $results['is_error'];
    $topic_id = $results['topic_id'];
  }

  // IF NO ERROR, FORWARD TO TOPIC
  if($is_error == 0) { header("Location: forum_topic.php?forum_id={$forum_info[forum_id]}&topic_id={$topic_id}"); exit(); }


// ADD A NEW REPLY
} elseif($task == "reply" && $is_reply) {

  $post_body = str_replace("&amp;#160;", " ", $_POST['post_body']);

  // RETRIEVE AND CHECK SECURITY CODE IF NECESSARY
  if($setting['setting_forum_code']) {
    if( !session_id() ) session_start();
    $code = $_SESSION['code'];
    if($code == "") { $code = randomcode(); }
    if($_POST['comment_secure'] != $code) { $is_error = 832; }
  }

  // IF SECURITY CODE IS FINE, CONTINUE
  if($is_error == 0) { 
    $results = $forum->forum_post_new($forum_info[forum_id], $topic_info[forumtopic_id], $topic_info[forumtopic_subject], $post_body);
    $is_error = $results['is_error'];
    $post_id = $results['post_id'];
  }

  // IF NO ERROR, FORWARD TO TOPIC
  if($is_error == 0) { header("Location: forum_topic.php?forum_id={$forum_info[forum_id]}&topic_id={$topic_info[forumtopic_id]}&post_id={$post_id}#post_{$post_id}"); exit(); }


// EDIT A POST
} elseif($task == "edit" && $is_edit) {

  $topic_title = $_POST['topic_title'];
  $post_body = str_replace("&amp;#160;", " ", $_POST['post_body']);
  $postmedia_keep = $_POST['postmedia_keep'];

  // SET ERRORS
  if(trim($topic_title) == "" && $show_title) { $is_error = 6000066; }
  if(trim(str_replace("&lt;p&gt;", "", str_replace("&lt;/p&gt;", "", $post_body))) == "") { $is_error = 6000067; }

  // IF NO ERRORS
  if($is_error == 0) {

    // IF MEDIA EXISTS BUT USER HAS REMOVED IT, UNLINK
    $new_postmedia_id = $postmedia_info[forummedia_id];
    if($postmedia_keep != 1 && $postmedia_info[is_media]) {
      @unlink($postmedia_info[forummedia_path]);
      $database->database_query("DELETE FROM se_forummedia WHERE forummedia_id='{$postmedia_info[forummedia_id]}' AND forummedia_forumtopic_id='{$topic_info[forumtopic_id]}'");
      $new_postmedia_id = 0;
      $postmedia_info[is_media] = false;
    }

    // CHECK TO SEE IF NEW MEDIA HAS BEEN UPLOADED
    if(!$postmedia_info[is_media]) {
      $new_postmedia_id = $forum->forum_media_new($topic_id);
    }

    // CLEAN, CENSOR, ETC
    $post_body = $forum->forum_bbcode_parse_clean($post_body);

    // CREATE NEW EXCERPT
    $excerpt = $forum->forum_excerpt($post_body);

    // UPDATE FORUMPOST TABLE
    $database->database_query("UPDATE se_forumposts SET forumpost_excerpt='$excerpt', forumpost_body='$post_body', forumpost_forummedia_id='$new_postmedia_id' WHERE forumpost_id='{$post_info[forumpost_id]}' AND forumpost_forumtopic_id='{$topic_info[forumtopic_id]}'");

    // IF FIRST POST, UPDATE TOPIC TITLE AND EXCERPT
    if($show_title) {
      $database->database_query("UPDATE se_forumtopics SET forumtopic_subject='$topic_title', forumtopic_excerpt='$excerpt' WHERE forumtopic_id='{$topic_info[forumtopic_id]}' AND forumtopic_forum_id='{$forum_info[forum_id]}'");
    }

    // SEND BACK TO POST
    header("Location: forum_topic.php?forum_id={$forum_info[forum_id]}&topic_id={$topic_info[forumtopic_id]}&post_id={$post_info[forumpost_id]}#post_{$post_info[forumpost_id]}");
    exit();

  }
}


// CHECK IF QUOTING FOR REPLY
if($is_reply && $quote_id != 0) {

  // VALIDATE POST ID
  $post = $database->database_query("SELECT se_forumposts.*, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_forumposts LEFT JOIN se_users ON se_forumposts.forumpost_authoruser_id=se_users.user_id WHERE forumpost_id='$quote_id' AND forumpost_forumtopic_id='{$topic_info[forumtopic_id]}' AND forumpost_deleted='0'");
  if($database->database_num_rows($post) == 1) {
    $post_info = $database->database_fetch_assoc($post);
    
    // GET POST AUTHOR
    $author = new se_user();
    if($post_info['forumpost_authoruser_id'] != $post_info['user_id']) {
      $author->user_exists = false;
      // AUTHOR DELETED
      if($post_info[forumpost_authoruser_id] != 0) {
        $author->user_displayname = SE_Language::get(6000119);
      // AUTHOR ANONYMOUS
      } else {
        $author->user_displayname = SE_Language::get(835);
      }
    } else {
      $author->user_exists = true;
      $author->user_info['user_id'] = $post_info['user_id'];
      $author->user_info['user_username'] = $post_info['user_username'];
      $author->user_info['user_fname'] = $post_info['user_fname'];
      $author->user_info['user_lname'] = $post_info['user_lname'];
      $author->user_displayname();
    }

    $post_body = "[quote={$author->user_displayname}]{$post_info[forumpost_body]}[/quote]";
  }
}



// DECODE POST BODY
$post_body = str_replace("\r\n", "", htmlspecialchars_decode($post_body));


// ASSIGN SMARTY VARS AND INCLUDE FOOTER
$smarty->assign('is_error', $is_error);
$smarty->assign('is_reply', $is_reply);
$smarty->assign('is_edit', $is_edit);
$smarty->assign('topic_title', $topic_title);
$smarty->assign('post_body', $post_body);
$smarty->assign('post_id', $post_id);
$smarty->assign('postmedia_info', $postmedia_info);
$smarty->assign('show_title', $show_title);
$smarty->assign('forum_info', $forum_info);
$smarty->assign('topic_info', $topic_info);
include "footer.php";
?>