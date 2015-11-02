<?php

$page = "admin_blog";
include "admin_header.php";

$task                         = ( !empty($_POST['task'])                ? $_POST['task']                : NULL );
$blogentrycat_id              = ( !empty($_POST['blogentrycat_id'])     ? $_POST['blogentrycat_id']     : NULL );
$blogentrycat_title           = ( !empty($_POST['blogentrycat_title'])  ? $_POST['blogentrycat_title']  : NULL );
$blogentrycat_showusercreated = !empty($_POST['blogentrycat_showusercreated']);

// SET RESULT VARIABLE
$result = 0;



// DELETE CATEGORY
if( $task=="deleteblogentrycat" )
{
  $sql = "DELETE FROM se_blogentrycats WHERE blogentrycat_id='{$blogentrycat_id}' LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_affected_rows($resource) )
    echo '{"result" : "success"}';
  else
    echo '{"result" : "failure"}';
  
  exit();
}


// CREATE CATEGORY
else if( $task=="createblogentrycat" )
{
  $lvar_id = SE_Language::edit(0, $blogentrycat_title, NULL, LANGUAGE_INDEX_SUBNETS);
  $sql = "INSERT INTO se_blogentrycats (blogentrycat_languagevar_id,blogentrycat_title) VALUES ('{$lvar_id}','{$blogentrycat_title}')";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_affected_rows($resource) )
    echo '{"result" : "success", "blogentrycat_id" : '.$database->database_insert_id().', "blogentrycat_languagevar_id" : '.$lvar_id.'}';
  else
    echo '{"result" : "failure"}';
  
  exit();
}


// EDIT CATEGORY
else if( $task=="editblogentrycat" )
{
  // Get langvar id
  $sql = "SELECT * FROM se_blogentrycats WHERE blogentrycat_id='{$blogentrycat_id}' LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( !$database->database_num_rows($resource) )
  {
    echo '{"result" : "failure"}';
    exit();
  }
  
  $result = $database->database_fetch_assoc($resource);
  $lvar_id = $result['blogentrycat_languagevar_id'];
  
  
  SE_Language::edit($lvar_id, $blogentrycat_title);
  $sql = "UPDATE se_blogentrycats SET blogentrycat_title='{$blogentrycat_title}' WHERE blogentrycat_id='{$blogentrycat_id}' LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  if( $database->database_affected_rows($resource) || $resource )
    echo '{"result" : "success"}';
  else
    echo '{"result" : "failure"}';
  
  exit();
}


// SAVE CHANGES
elseif($task == "dosave")
{
  $setting_permission_blog = $_POST['setting_permission_blog'];
  
  $sql = "UPDATE se_settings SET setting_permission_blog='$setting_permission_blog'";
  $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
  
  
  $setting = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_settings LIMIT 1"));
  $result = 1;
}


// GET BLOG ENTRY CATEGORIES
$categories_array = se_blog::blog_category_list($blogentrycat_showusercreated);

// ASSIGN VARIABLES AND SHOW GENERAL SETTINGS PAGE
$smarty->assign('result', $result);
$smarty->assign('blogentrycats', $categories_array);
include "admin_footer.php";
?>