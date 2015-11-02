<?php

$page = "user_blog_entry";
include "header.php";

$task         = ( !empty($_POST['task'])          ? $_POST['task']          : ( !empty($_GET['task'])         ? $_GET['task']         : NULL ) );
$blogentry_id = ( !empty($_POST['blogentry_id'])  ? $_POST['blogentry_id']  : ( !empty($_GET['blogentry_id']) ? $_GET['blogentry_id'] : NULL ) );


// ENSURE BLOGS ARE ENABLED FOR THIS USER
if( !$user->level_info['level_blog_create'] )
{
  header("Location: user_home.php");
  exit();
}



// START BLOG METHOD 
$blog = new se_blog($user->user_info['user_id']);



// MAKE SURE THIS BLOG ENTRY BELONGS TO THIS USER AND IS NUMERIC
if( $blogentry_id )
{
  $blogentry_info = $blog->blog_entry_info($blogentry_id);
  
  if( !$blogentry_info )
  {
    header("Location: user_blog.php");
    exit();
  }
  
  // GET TOTAL COMMENTS POSTED ON THIS ENTRY
  $comments_total = $database->database_num_rows($database->database_query("SELECT blogcomment_id FROM se_blogcomments WHERE blogcomment_blogentry_id='{$blogentry_info[blogentry_id]}'"));
}


// DO SAVE
if( $task=="dosave" )
{
  $blogentry_title            = $_POST['blogentry_title'];
  $blogentry_body             = $_POST['blogentry_body'];
  $blogentry_blogentrycat_id  = $_POST['blogentry_blogentrycat_id'];
  $blogentry_search           = $_POST['blogentry_search'];
  $blogentry_privacy          = $_POST['blogentry_privacy'];
  $blogentry_comments         = $_POST['blogentry_comments'];
  $blogentry_trackbacks       = $_POST['blogentry_trackbacks'];
  $new_blogentrycat_title     = $_POST['new_blogentrycat_title'];
  
  // CATEGORY
  if( $blogentry_blogentrycat_id==-1 && !trim($new_blogentrycat_title) )
    $blogentry_blogentrycat_id = 0;
  
  if( $user->level_info['level_blog_category_create'] && $blogentry_blogentrycat_id==-1 )
    $blogentry_blogentrycat_id = $blog->blog_category_create($new_blogentrycat_title);
  
  // CREATE VS EDIT
  $is_edit = !empty($blogentry_id);
  
  // POST ENTRY
  $result_array = $blog->blog_entry_post(
    $blogentry_id,
    $blogentry_title,
    $blogentry_body,
    $blogentry_blogentrycat_id,
    $blogentry_search,
    $blogentry_privacy,
    $blogentry_comments,
    $blogentry_trackbacks
  );
  
  if( empty($blogentry_id) && !empty($result_array['blogentry_id']) )
    $blogentry_id = $result_array['blogentry_id'];
  
  // STUFF TO DO ON SUCCESS
  if( $result_array['result'] )
  {
    // UPDATE LAST UPDATE DATE (SAY THAT 10 TIMES FAST)
    $user->user_lastupdate();
    
    // INSERT ACTION
    if( !$is_edit )
    {
      if( strlen($blogentry_title)>100 )
        $blogentry_title = substr($blogentry_title, 0, 97); $blogentry_title .= "...";
      
      $actions->actions_add(
        $user,
        "postblog",
        array(
          $user->user_info['user_username'],
          $user->user_displayname,
          $blogentry_id,
          $blogentry_title
        ),
        array(),
        0,
        FALSE,
        "user",
        $user->user_info['user_id'],
        $blogentry_privacy
      );
    }
    
    // SEND USER BACK TO VIEW ENTRIES PAGE
    header("Location: user_blog.php");
    exit();
  }
  
  
  
  // AN ERROR OCCURED SEND THE DATA BACK
  $blogentry_info = array(
    'blogentry_id'              => $blogentry_id,
    'blogentry_title'           => $blogentry_title,
    'blogentry_body'            => $blogentry_body,
    'blogentry_blogentrycat_id' => $blogentry_blogentrycat_id,
    'blogentry_search'          => $blogentry_search,
    'blogentry_privacy'         => $blogentry_privacy,
    'blogentry_comments'        => $blogentry_comments,
    'blogentry_trackbacks'      => $blogentry_trackbacks
  );
}



// GET BLOG ENTRY CATEGORIES
$blogentrycats_array = $blog->blog_category_list($user->user_info['user_id']);



// GET PREVIOUS PRIVACY SETTINGS
$level_blog_privacy = unserialize($user->level_info['level_blog_privacy']);
rsort($level_blog_privacy);
for( $c=0; $c<count($level_blog_privacy); $c++ )
{
  $lvar = user_privacy_levels($level_blog_privacy[$c]);
  if( $lvar )
    SE_Language::_preload($privacy_options[$level_blog_privacy[$c]] = $lvar);
}

$level_blog_comments = unserialize($user->level_info['level_blog_comments']);
rsort($level_blog_comments);
for( $c=0; $c<count($level_blog_comments); $c++ )
{
  $lvar = user_privacy_levels($level_blog_comments[$c]);
  if( $lvar )
    SE_Language::_preload($comment_options[$level_blog_comments[$c]] = $lvar);
}


// CONVERT HTML CHARACTERS BACK
$blogentry_info['blogentry_body'] = str_replace("\r\n", "", htmlspecialchars_decode($blogentry_info['blogentry_body']));


// ASSIGN VARIABLES AND SHOW NEW BLOGENTRY PAGE
$smarty->assign('blogentry_info', $blogentry_info);
$smarty->assign('blogentrycats', $blogentrycats_array);
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('comment_options', $comment_options);
$smarty->assign('comments_total', $comments_total);
include "footer.php";
?>