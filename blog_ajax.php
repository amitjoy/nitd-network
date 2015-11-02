<?php


$page = "blog_ajax";
include "header.php";

// PROCESS INPUT
$task         = ( !empty($_POST['task'])          ? $_POST['task']          : ( !empty($_GET['task'])         ? $_GET['task']         : NULL ) );
$blogentry_id = ( !empty($_POST['blogentry_id'])  ? $_POST['blogentry_id']  : ( !empty($_GET['blogentry_id']) ? $_GET['blogentry_id'] : NULL ) );


// TRACKBACK COMPATIBILITY
if( empty($_POST['e_id']) && !empty($blogentry_id) )
  $_POST['e_id'] = $blogentry_id;


// CREATE BLOG OBJECT
$blog = new se_blog($user->user_exists ? $user->user_info['user_id'] : NULL);




// TRACKBACKS
if( $task=="trackback" )
{
  // Redirect if no data 
  if( !empty($blogentry_id) && empty($_POST['url']) && empty($_GET['url']) )
  {
    $blogentry_info = $blog->blog_entry_info($blogentry_id);
    header('Location: ' . $url->url_create('blog_entry', $blogentry_info['user_username'], $blogentry_id));
    exit();
  }
  
  echo $blog->blog_trackback_receive();
  exit();
}




/* ***** ACTIONS BELOW THIS LINE REQUIRE THE USER TO BE LOGGED IN ***** */
if( !$user->user_exists )
{
  echo json_encode(array('result' => FALSE));
  exit();
}




// DELETE
if( $task=="deleteblog" )
{
  $result = $blog->blog_entry_delete($blogentry_id);
  echo json_encode(array('result' => ( $result ? 'success' : 'failure' )));
  exit();
}


// PREVIEW
elseif( $task=="previewblog" )
{
  $page = "blog";
  
  $owner =& $user;
  $blog->user_id = $user->user_info['user_id'];
  
  $blogentry_title            = $_POST['blogentry_title'];
  $blogentry_body             = $_POST['blogentry_body'];
  $blogentry_blogentrycat_id  = $_POST['blogentry_blogentrycat_id'];
  
  $blogentry_body = str_replace("\r\n", "", htmlspecialchars_decode($blogentry_body));
  
  // GET CUSTOM BLOG STYLE IF ALLOWED
  if( $user->level_info['level_blog_style'] )
  {
    $blogstyle_info = $database->database_fetch_assoc($database->database_query("SELECT blogstyle_css FROM se_blogstyles WHERE blogstyle_user_id='{$owner->user_info['user_id']}' LIMIT 1"));
    $global_css = $blogstyle_info['blogstyle_css'];
  }

  // GET ARCHIVE AND CATEGORIES
  $archive_list = $blog->blog_archive_generate();
  $category_list = $blog->blog_categories_generate();
  
  // ASSIGN VARIABLES AND DISPLAY BLOG PAGE
  $smarty->assign('total_blogentries', 1);
  $smarty->assign('entries', array(array(
    'blogentry_id'              => $blogentry_id,
    'blogentry_title'           => $blogentry_title,
    'blogentry_body'            => $blogentry_body,
    'blogentry_blogentrycat_id' => $blogentry_blogentrycat_id
  )));
  
  $smarty->assign_by_ref('archive_list', $archive_list);
  $smarty->assign_by_ref('category_list', $category_list);
  $smarty->assign('p', 1);
  $smarty->assign('maxpage', 1);
  $smarty->assign('p_start', 1);
  $smarty->assign('p_end', 1);
  
  ob_end_clean();
  
  include "footer.php";
  exit();
}


// SUBSCRIBE
elseif( $task=="subscribeblog" )
{
  $result = $blog->blog_subscription_create($owner->user_info['user_id']);
  echo json_encode(array('result' => ( $result ? 'success' : 'failure' )));
  exit();
}


// UNSUBSCRIBE
elseif( $task=="unsubscribeblog" )
{
  $result = $blog->blog_subscription_delete($owner->user_info['user_id']);
  echo json_encode(array('result' => ( $result ? 'success' : 'failure' )));
  exit();
}

?>