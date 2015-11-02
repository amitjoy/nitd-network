<?php


$page = "blog";
include "header.php";


// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( !$user->user_exists && !$setting['setting_permission_blog'] )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
if( !$owner->user_exists )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 828);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// ENSURE BLOGS ARE ENABLED FOR THIS USER
if( !$owner->level_info['level_blog_create'] )
{
  header("Location: ".$url->url_create('profile', $owner->user_info['user_username']));
  exit();
}


// PROCESS INPUT
$p            = ( !empty($_POST['p'])             ? $_POST['p']             : ( !empty($_GET['p'])            ? $_GET['p']            : NULL ) );
$blogentry_id = ( !empty($_POST['blogentry_id'])  ? $_POST['blogentry_id']  : ( !empty($_GET['blogentry_id']) ? $_GET['blogentry_id'] : NULL ) );
$category_id  = ( !empty($_POST['category_id'])   ? $_POST['category_id']   : ( !empty($_GET['category_id'])  ? $_GET['category_id']  : NULL ) );
$date_start   = ( !empty($_POST['date_start'])    ? $_POST['date_start']    : ( !empty($_GET['date_start'])   ? $_GET['date_start']   : NULL ) );
$date_end     = ( !empty($_POST['date_end'])      ? $_POST['date_end']      : ( !empty($_GET['date_end'])     ? $_GET['date_end']     : NULL ) );
$blog_search  = ( !empty($_POST['blog_search'])   ? $_POST['blog_search']   : ( !empty($_GET['blog_search'])  ? $_GET['blog_search']  : NULL ) );


// CREATE BLOG OBJECT
$blog = new se_blog($owner->user_info['user_id']);


// GENERATE WHERE CLAUSE
$privacy_max = $owner->user_privacy_max($user);
$where = "(blogentry_privacy & '{$privacy_max}')";

if( !empty($blogentry_id) && is_numeric($blogentry_id) )
{
  // SPECIFIC ENTRY SPECIFIED
  $where .= " && blogentry_id='{$blogentry_id}'";
}

else
{
  // SEARCH PARAMETERS
  if( !empty($date_start) && !empty($date_end) && is_numeric($date_start) && is_numeric($date_end) )
    $where .= " && blogentry_date>'{$date_start}' && blogentry_date<'{$date_end}'";

  if( !empty($category_id) && is_numeric($category_id) )
    $where .= " && blogentry_blogentrycat_id='{$category_id}'";
  
  if( !empty($blog_search) )
    $where .= " && MATCH (blogentry_title, blogentry_body) AGAINST ('{$blog_search}' IN BOOLEAN MODE)";
}


// GET TOTAL ENTRIES
$total_blogentries = $blog->blog_entries_total($where);

// MAKE ENTRY PAGES
$entries_per_page = (int) $owner->level_info['level_blog_entries'];
if( $entries_per_page<=0 || $entries_per_page>100 ) $entries_per_page = 10;
$page_vars = make_page($total_blogentries, $entries_per_page, $p);

// GET ENTRY ARRAY
$blogentries = $blog->blog_entries_list($page_vars[0], $entries_per_page, "blogentry_date DESC", $where);

// GET CUSTOM BLOG STYLE IF ALLOWED
if( $owner->level_info['level_blog_style'] )
{
  $blogstyle_info = $database->database_fetch_assoc($database->database_query("SELECT blogstyle_css FROM se_blogstyles WHERE blogstyle_user_id='{$owner->user_info['user_id']}' LIMIT 1"));
  $global_css = $blogstyle_info['blogstyle_css'];
}

// GET ARCHIVE AND CATEGORIES
$archive_list = $blog->blog_archive_generate("(se_blogentries.blogentry_privacy & '{$privacy_max}')");
$category_list = $blog->blog_categories_generate("(se_blogentries.blogentry_privacy & '{$privacy_max}')");

$is_subscribed = $blog->blog_subscription_exists($owner->user_info['user_id'], $user->user_info['user_id']);




// DO STUFF IF ONLY ONE ENTRY IS BEING DISPLAYED
if( $total_blogentries==1 && $blogentry_id )
{
  $blogentry_info =& $blogentries[0];
  

  // ENSURE OWNER OF BLOG ENTRY MATCHES OWNER OBJECT
  if( $owner->user_info['user_id']!=$blogentry_info['blogentry_user_id'] )
  {
    header("Location: home.php");
    exit();
  }


  // UPDATE ENTRY VIEWS
  if( $user->user_info['user_id']!=$owner->user_info['user_id'] )
  {
    $database->database_query("UPDATE se_blogentries SET blogentry_views=blogentry_views+1 WHERE blogentry_id='{$blogentry_info['blogentry_id']}'");
  }
  
  
  // GET ENTRY COMMENT PRIVACY
  $allowed_to_comment = TRUE;
  if( !($privacy_max & $blogentry_info['blogentry_comments']) ) 
    $allowed_to_comment = FALSE;
  
  
  // GET BLOG TRACKBACKS
  $tb_where = "blogtrackback_blogentry_id='{$blogentry_id}'";
  $trackback_total = $blog->blog_trackback_total($tb_where);
  $trackback_list  = $blog->blog_trackback_list(NULL, NULL, NULL, $tb_where);
  
  
  // MAKE TRACKBACK DISCOVERY
  $trackback_rdf = $blog->blog_trackback_generate($blogentry_info);
  
  
  // UPDATE NOTIFICATIONS
  if( $is_subscribed )
  {
    $database->database_query("
      DELETE FROM
        se_notifys
      USING
        se_notifys
      LEFT JOIN
        se_notifytypes
        ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id
      WHERE
        se_notifys.notify_user_id='{$user->user_info['user_id']}' AND
        se_notifytypes.notifytype_name='newblogsubscriptionentry' AND
        notify_object_id='{$blogentry_id}'
    ");
  }
  
  if( $user->user_info['user_id']==$owner->user_info['user_id'])
  {
    $database->database_query("
      DELETE FROM
        se_notifys
      USING
        se_notifys
      LEFT JOIN
        se_notifytypes
        ON se_notifys.notify_notifytype_id=se_notifytypes.notifytype_id
      WHERE
        se_notifys.notify_user_id='{$owner->user_info['user_id']}' AND
        se_notifytypes.notifytype_name='blogcomment' AND
        notify_object_id='{$blogentry_id}'
    ");
  }
  
  
  // SET SEO STUFF
  $global_page_content = $blogentry_info['blogentry_title'];
  $global_page_content = cleanHTML(str_replace('>', '> ', $global_page_content), NULL);
  if( strlen($global_page_content)>255 ) $global_page_content = substr($global_page_content, 0, 251).'...';
  $global_page_content = addslashes(trim(preg_replace('/\s+/', ' ',$global_page_content)));
  
  $global_page_title = array(
    1500125,
    $owner->user_displayname,
    $global_page_content
  );
  
  $global_page_content = $blogentry_info['blogentry_body'];
  $global_page_content = cleanHTML(str_replace('>', '> ', $global_page_content), NULL);
  if( strlen($global_page_content)>255 ) $global_page_content = substr($global_page_content, 0, 251).'...';
  $global_page_content = addslashes(trim(preg_replace('/\s+/', ' ',$global_page_content)));
  
  $global_page_description = array(
    1500125,
    $owner->user_displayname,
    $global_page_content
  );
  
  
  // ASSIGN
  $smarty->assign('total_comments', $total_comments);
  $smarty->assign('allowed_to_comment', $allowed_to_comment);
  $smarty->assign('trackback_rdf', $trackback_rdf);
  
  $smarty->assign('trackback_total', $trackback_total);
  $smarty->assign_by_ref('trackback_list', $trackback_list);
  $smarty->assign_by_ref('blogentry_info', $blogentry_info);
}


// DO STUFF IF MORE THAN ONE ENTRY IS BEING DISPLAYED
else
{
  // SET SEO STUFF
  $global_page_title = array(1500124, $owner->user_displayname);
  $global_page_description = array(1500124, $owner->user_displayname);
}




// ASSIGN VARIABLES AND DISPLAY BLOG PAGE
$smarty->assign('total_blogentries', $total_blogentries);
$smarty->assign_by_ref('entries', $blogentries);
$smarty->assign_by_ref('archive_list', $archive_list);
$smarty->assign_by_ref('category_list', $category_list);

$smarty->assign('is_subscribed', $is_subscribed);

$smarty->assign('blogentry_id', $blogentry_id);
$smarty->assign('category_id', $category_id);
$smarty->assign('date_start', $date_start);
$smarty->assign('date_end', $date_end);
$smarty->assign('blog_search', $blog_search);

$smarty->assign('p', $page_vars[1]);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($blogentries));

include "footer.php";
?>