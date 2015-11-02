<?php

$page = "admin_levels_blogsettings";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_POST['level_id'])) { $level_id = $_POST['level_id']; } elseif(isset($_GET['level_id'])) { $level_id = $_GET['level_id']; } else { $level_id = 0; }


// VALIDATE LEVEL ID
$sql = "SELECT * FROM se_levels WHERE level_id='{$level_id}' LIMIT 1";
$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");

if( !$database->database_num_rows($resource) )
{ 
  header("Location: admin_levels.php");
  exit();
}

$level_info = $database->database_fetch_assoc($resource);


// SET RESULT AND ERROR VARS
$result = FALSE;
$is_error = FALSE;



// SAVE CHANGES
if($task == "dosave")
{
  $level_blog_view              = $_POST['level_blog_view'];
  $level_blog_create            = $_POST['level_blog_create'];
  $level_blog_category_create   = !empty($_POST['level_blog_category_create']);
  $level_blog_entries           = $_POST['level_blog_entries'];
  $level_blog_style             = $_POST['level_blog_style'];
  $level_blog_search            = $_POST['level_blog_search'];
  $level_blog_trackbacks_allow  = $_POST['level_blog_trackbacks_allow'];
  $level_blog_trackbacks_detect = $_POST['level_blog_trackbacks_detect'];
  $level_blog_html              = $_POST['level_blog_html'];
  $level_blog_privacy           = is_array($_POST['level_blog_privacy']) ? $_POST['level_blog_privacy'] : array();
  $level_blog_comments          = is_array($_POST['level_blog_comments']) ? $_POST['level_blog_comments'] : array();
  
  // FORMAT HTML CORRECTLY
  $level_blog_html = preg_replace('/[,\s]+/', ',', $level_blog_html);
  
  // CHECK THAT A NUMBER BETWEEN 1 AND 999 WAS ENTERED FOR BLOG ENTRIES
  if( !is_numeric($level_blog_entries) || $level_blog_entries<1 || $level_blog_entries>999 )
  {
    $is_error = 1500110;
  }
  
  else
  {
    // GET PRIVACY AND PRIVACY DIFFERENCES
    if( empty($level_blog_privacy) || !is_array($level_blog_privacy) ) $level_blog_privacy = array(63);
    rsort($level_blog_privacy);
    $new_privacy_options = $level_blog_privacy;
    $level_blog_privacy = serialize($level_blog_privacy);
    
    // GET COMMENT AND COMMENT DIFFERENCES
    if( empty($level_blog_comments) || !is_array($level_blog_comments) ) $level_blog_privacy = array(63);
    rsort($level_blog_comments);
    $new_comments_options = $level_blog_comments;
    $level_blog_comments = serialize($level_blog_comments);
    
    // SAVE SETTINGS
    $sql = "
      UPDATE
        se_levels
      SET 
        level_blog_view='$level_blog_view',
        level_blog_create='$level_blog_create',
        level_blog_category_create='$level_blog_category_create',
        level_blog_entries='$level_blog_entries',
        level_blog_search='$level_blog_search',
        level_blog_privacy='$level_blog_privacy',
        level_blog_comments='$level_blog_comments',
        level_blog_style='$level_blog_style',
        level_blog_trackbacks_allow='$level_blog_trackbacks_allow',
        level_blog_trackbacks_detect='$level_blog_trackbacks_detect',
        level_blog_html='$level_blog_html'
      WHERE
        level_id='{$level_info['level_id']}'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
    if( !$level_blog_search )
      $database->database_query("UPDATE se_blogentries INNER JOIN se_users ON se_users.user_id=se_blogentries.blogentry_user_id SET se_blogentries.blogentry_search='1' WHERE se_users.user_level_id='{$level_info['level_id']}'") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $database->database_query("UPDATE se_blogentries INNER JOIN se_users ON se_users.user_id=se_blogentries.blogentry_user_id SET se_blogentries.blogentry_privacy='{$new_privacy_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_blogentries.blogentry_privacy NOT IN('".join("','", $new_privacy_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    $database->database_query("UPDATE se_blogentries INNER JOIN se_users ON se_users.user_id=se_blogentries.blogentry_user_id SET se_blogentries.blogentry_comments='{$new_comments_options[0]}' WHERE se_users.user_level_id='{$level_info['level_id']}' && se_blogentries.blogentry_comments NOT IN('".join("','", $new_comments_options)."')") or die("<b>Error: </b>".$database->database_error()."<br /><b>File: </b>".__FILE__."<br /><b>Line: </b>".__LINE__."<br /><b>Query: </b>".$sql);
    
    $level_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_levels WHERE level_id='{$level_info['level_id']}'"));
    $result = TRUE;
  }

}


// GET PREVIOUS PRIVACY SETTINGS
for( $c=6; $c>0; $c-- )
{
  $priv = pow(2, $c)-1;
  $upl = user_privacy_levels($priv);
  if( !$upl ) continue;
  
  SE_Language::_preload($upl);
  $privacy_options[$priv] = $upl;
}

for( $c=6; $c>=0; $c-- )
{
  $priv = pow(2, $c)-1;
  $upl = user_privacy_levels($priv);
  if( !$upl ) continue;
  
  SE_Language::_preload($upl);
  $comment_options[$priv] = $upl;
}




// ASSIGN VARIABLES AND SHOW BLOG SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('is_error', $is_error);

$smarty->assign_by_ref('level_info', $level_info);

$smarty->assign('level_blog_privacy', unserialize($level_info['level_blog_privacy']));
$smarty->assign('level_blog_comments', unserialize($level_info['level_blog_comments']));
$smarty->assign('level_blog_html', str_replace(',', ', ', $level_info['level_blog_html']));

$smarty->assign('blog_privacy', $privacy_options);
$smarty->assign('blog_comments', $comment_options);

include "admin_footer.php";
?>