<?php

$page = "group_discussion_view";
include "header.php";

if(isset($_POST['p'])) { $p = $_POST['p']; } elseif(isset($_GET['p'])) { $p = $_GET['p']; } else { $p = 1; }
if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = ""; }
if(isset($_POST['group_id'])) { $group_id = $_POST['group_id']; } elseif(isset($_GET['group_id'])) { $group_id = $_GET['group_id']; } else { $group_id = 0; }
if(isset($_POST['grouptopic_id'])) { $grouptopic_id = $_POST['grouptopic_id']; } elseif(isset($_GET['grouptopic_id'])) { $grouptopic_id = $_GET['grouptopic_id']; } else { $grouptopic_id = 0; }
if(isset($_POST['grouppost_id'])) { $grouppost_id = $_POST['grouppost_id']; } elseif(isset($_GET['grouppost_id'])) { $grouppost_id = $_GET['grouppost_id']; } else { $grouppost_id = 0; }

// DISPLAY ERROR PAGE IF USER IS NOT LOGGED IN AND ADMIN SETTING REQUIRES REGISTRATION
if( (!$user->user_exists && !$setting['setting_permission_group']) || ($user->user_exists && (~(int)$user->level_info['level_group_allow'] & 1)) )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 656);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}

// DISPLAY ERROR PAGE IF NO OWNER
$group = new se_group($user->user_info['user_id'], $group_id);
if( !$group->group_exists )
{
  $page = "error";
  $smarty->assign('error_header', 639);
  $smarty->assign('error_message', 2000219);
  $smarty->assign('error_submit', 641);
  include "footer.php";
}



// CHECK THAT TOPIC EXISTS AND GET TOPIC INFO
$topic_query = $database->database_query("SELECT * FROM se_grouptopics WHERE grouptopic_id='{$grouptopic_id}' AND grouptopic_group_id='{$group->group_info['group_id']}' LIMIT 1");
if( !$database->database_num_rows($topic_query) )
{
  header("Location: ".$url->url_create("group", NULL, $group->group_info['group_id'])."&v=discussions");
  exit();
}

$topic_info = $database->database_fetch_assoc($topic_query);


// CHECK IF USER IS ADMIN OR OFFICER
if($group->user_rank == 2 || $group->user_rank == 1)
{
  // STICKY TOPIC
  if($task == "sticky")
  {
    $database->database_query("UPDATE se_grouptopics SET grouptopic_sticky=1 WHERE grouptopic_id='{$topic_info['grouptopic_id']}' LIMIT 1");
    $topic_info['grouptopic_sticky'] = 1;
  }
  
  // UNSTICKY TOPIC
  elseif($task == "unsticky")
  {
    $database->database_query("UPDATE se_grouptopics SET grouptopic_sticky=0 WHERE grouptopic_id='{$topic_info['grouptopic_id']}' LIMIT 1");
    $topic_info['grouptopic_sticky'] = 0;
  }
  
  // CLOSE TOPIC
  elseif($task == "close")
  {
    $database->database_query("UPDATE se_grouptopics SET grouptopic_closed=1 WHERE grouptopic_id='{$topic_info['grouptopic_id']}' LIMIT 1");
    $topic_info['grouptopic_closed'] = 1;
  }
  
  // OPEN TOPIC
  elseif($task == "open")
  {
    $database->database_query("UPDATE se_grouptopics SET grouptopic_closed=0 WHERE grouptopic_id='{$topic_info['grouptopic_id']}' LIMIT 1");
    $topic_info['grouptopic_closed'] = 0;
  }
  
  // EDIT TOPIC
  elseif($task == "topic_edit")
  {
    $topic_subject = $_POST['topic_subject'];
    
    if( trim($topic_subject) )
    {
      $database->database_query("UPDATE se_grouptopics SET grouptopic_subject='{$topic_subject}' WHERE grouptopic_id='{$topic_info['grouptopic_id']}' LIMIT 1");
      $topic_info['grouptopic_subject'] = $topic_subject;
    }
  }
}


// EDIT POST
if($task == "post_edit")
{
  $post_query = $database->database_query("SELECT grouppost_id, grouppost_authoruser_id FROM se_groupposts WHERE grouppost_id='{$grouppost_id}' AND grouppost_grouptopic_id='{$topic_info['grouptopic_id']}'");
  if( $database->database_num_rows($post_query) )
  {
    $post_info = $database->database_fetch_assoc($post_query);
    
    // ADD BREAKS AND GROUP POST BODY
    $grouppost_body = $_POST['grouppost_body'];
    $grouppost_body = $group->group_post_bbcode_parse_clean($grouppost_body);
    $grouppost_body = addslashes(stripslashes($grouppost_body));
    $grouppost_date = time();
    
    if( $user->user_exists && $post_info['grouppost_authoruser_id'] == $user->user_info['user_id'] && trim($grouppost_body) )
    {
      $database->database_query("UPDATE se_groupposts SET grouppost_lastedit_date='{$grouppost_date}', grouppost_lastedit_user_id='{$user->user_info['user_id']}', grouppost_body='{$grouppost_body}' WHERE grouppost_id='{$grouppost_id}' LIMIT 1");
      $post_info['grouppost_body'] = $grouppost_body;
      $post_info['grouppost_body_formatted'] = $group->group_post_bbcode_parse_view($post_info['grouppost_body']);
    }
    
    // RUN JAVASCRIPT FUNCTION
    $post_info['grouppost_body'] = addslashes(stripslashes($post_info['grouppost_body']));
    $post_info['grouppost_body_formatted'] = addslashes(stripslashes($post_info['grouppost_body_formatted']));
    echo "<html>\n<head>\n<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>\n<script type=\"text/javascript\">\n";
    echo "window.parent.document.getElementById('post_div_{$post_info['grouppost_id']}').innerHTML = '{$post_info['grouppost_body_formatted']}';\n";
    echo "window.parent.document.getElementById('post_body_{$post_info['grouppost_id']}').innerHTML = '{$post_info['grouppost_body']}';\n";
    echo "</script>\n</head>\n<body>\n</body>\n</html>";
    exit();
  }
}


// DELETE POST
elseif($task == "post_delete")
{
  $post_query = $database->database_query("SELECT grouppost_id, grouppost_authoruser_id FROM se_groupposts WHERE grouppost_id='{$grouppost_id}' AND grouppost_grouptopic_id='{$topic_info['grouptopic_id']}' LIMIT 1");
  if( $database->database_num_rows($post_query) )
  {
    $post_info = $database->database_fetch_assoc($post_query);
    
    if( ($user->user_exists && $post_info['grouppost_authoruser_id'] == $user->user_info['user_id']) || $group->user_rank == 2 || $group->user_rank == 1 )
    {
      $database->database_query("UPDATE se_groupposts SET grouppost_deleted=1 WHERE grouppost_id='{$grouppost_id}' LIMIT 1");
      // Whoops we're not supposed to permanently delete them
      //$database->database_query("UPDATE se_grouptopics SET grouptopic_totalposts=grouptopic_totalposts-1 WHERE grouptopic_id='{$topic_info['grouptopic_id']}' LIMIT 1");
    }
  }
}



// GET PRIVACY LEVEL
$privacy_max = $group->group_privacy_max($user);
if( !($privacy_max & $group->group_info['group_privacy']) )
{
  header("Location: ".$url->url_create("group", NULL, $group->group_info['group_id']));
  exit();
}

// CHECK IF USER IS ALLOWED TO POST IN DISCUSSION
$allowed_to_discuss = ( ($privacy_max & $group->group_info['group_discussion']) && !$topic_info['grouptopic_closed'] );


// INCREMENT VIEWS FOR THIS TOPIC
$database->database_query("UPDATE se_grouptopics SET grouptopic_views=grouptopic_views+1 WHERE grouptopic_id='{$topic_info['grouptopic_id']}' LIMIT 1");


// SET POSTS PER PAGE
$posts_per_page = 10;


// IF GROUPPOST ID IS SET, RESET PAGE
if( $grouppost_id )
{
  $previous_posts = $database->database_num_rows($database->database_query("SELECT NULL FROM se_groupposts WHERE grouppost_id<='{$grouppost_id}' AND grouppost_grouptopic_id='{$topic_info['grouptopic_id']}'"));
  if( $previous_posts ) { $p = ceil($previous_posts/$posts_per_page); }
}


// GET TOTAL POSTS
$total_posts = $group->group_post_total(NULL, $topic_info['grouptopic_id']);

// MAKE POST PAGES
$page_vars = make_page($total_posts, $posts_per_page, $p);

// GET GROUP POSTS
$posts = $group->group_post_list($page_vars[0], $posts_per_page, "se_groupposts.grouppost_date ASC", "(se_groupposts.grouppost_grouptopic_id='{$topic_info['grouptopic_id']}')");

// GET CUSTOM GROUP STYLE IF ALLOWED
if( $group->groupowner_level_info['level_group_style'] )
{ 
  $groupstyle_info = $database->database_fetch_assoc($database->database_query("SELECT groupstyle_css FROM se_groupstyles WHERE groupstyle_group_id='{$group->group_info['group_id']}' LIMIT 1")); 
  $global_css = $groupstyle_info['groupstyle_css'];
}

// SET GLOBAL PAGE TITLE
$global_page_title[0] = 2000314; 
$global_page_title[1] = $group->group_info['group_title'];
$global_page_title[2] = $topic_info['grouptopic_subject'];
$global_page_description[0] = 2000313;
$global_page_description[1] = $group->group_info['group_desc'];


// ASSIGN VARIABLES AND INCLUDE FOOTER
$smarty->assign('grouppost_id', $grouppost_id);
$smarty->assign('group', $group);
$smarty->assign('posts', $posts);
$smarty->assign('topic_info', $topic_info);
$smarty->assign('allowed_to_discuss', $allowed_to_discuss);
$smarty->assign('p', $page_vars[1]);
$smarty->assign('total_posts', $total_posts);
$smarty->assign('maxpage', $page_vars[2]);
$smarty->assign('p_start', $page_vars[0]+1);
$smarty->assign('p_end', $page_vars[0]+count($posts));
include "footer.php";
?>