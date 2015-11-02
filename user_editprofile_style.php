<?php

$page = "user_editprofile_style";
include "header.php";

$task = ( isset($_POST['task']) ? $_POST['task'] : NULL );

// CHECK FOR ADMIN ALLOWANCE OF STYLE
if( !$user->level_info['level_profile_style'] && !$user->level_info['level_profile_style_sample'] )
{
  header("Location: user_home.php");
  exit();
}

// SET VARS
$result = 0;

// GET SAMPLE PROFILE CSS
if( $user->level_info['level_profile_style_sample'] )
{
  $sample_css = Array();
  $styles = $database->database_query("SELECT * FROM se_stylesamples WHERE stylesample_type='profile'");
  while($sample_info = $database->database_fetch_assoc($styles))
  {
    $sample_css[] = $sample_info;
    $sample[$sample_info['stylesample_id']] = $sample_info['stylesample_css'];
  }
}


// SAVE NEW SETTINGS
if( $task == "dosave" )
{
  $style_profile = addslashes(str_replace("-moz-binding", "", strip_tags(htmlspecialchars_decode($_POST['style_profile'], ENT_QUOTES))));
  $style_profile_sample = $_POST['style_profile_sample'];

  // SET STYLE TO NOTHING IF NOT ALLOWED
  if( !$user->level_info['level_profile_style'] && !$user->level_info['level_profile_style_sample'] )
  {
    $style_profile = ""; 
    $style_profile_sample = 0;
  }
  
  // SET STYLE TO SAMPLE IF ALLOWED
  elseif( !$user->level_info['level_profile_style'] && $user->level_info['level_profile_style_sample'] )
  {
    $style_profile = addslashes(str_replace("-moz-binding", "", strip_tags($sample[$style_profile_sample])));
  }
 

  // UPDATE DATABASE
  $database->database_query("UPDATE se_profilestyles SET profilestyle_css='$style_profile', profilestyle_stylesample_id='$style_profile_sample' WHERE profilestyle_user_id='{$user->user_info['user_id']}' LIMIT 1");
  $user->user_lastupdate();
  $result = 1;
}


// GET THIS USER'S PROFILE CSS
$style_query = $database->database_query("SELECT profilestyle_css, profilestyle_stylesample_id FROM se_profilestyles WHERE profilestyle_user_id='{$user->user_info['user_id']}' LIMIT 1");
if($database->database_num_rows($style_query) == 1)
{ 
  $style_info = $database->database_fetch_assoc($style_query); 
}
else
{
  $database->database_query("INSERT INTO se_profilestyles (profilestyle_user_id, profilestyle_css, profilestyle_stylesample_id) VALUES ('{$user->user_info['user_id']}', '', '0')");
  $style_info['profilestyle_css'] = "";
  $style_info['profilestyle_stylesample_id'] = 0;
}


// GET TABS TO DISPLAY ON TOP MENU
$field = new se_field("profile", $user->profile_info);
$field->cat_list(0, 0, 0, "profilecat_id='{$user->user_info['user_profilecat_id']}'");
$cat_array = $field->subcats;


// ASSIGN SMARTY VARIABLES AND INCLUDE FOOTER
$smarty->assign('result', $result);
$smarty->assign('cats', $cat_array);
$smarty->assign('style_info', $style_info);
$smarty->assign('sample_css', $sample_css);
$smarty->assign('style_profile', htmlspecialchars($style_info['profilestyle_css'], ENT_QUOTES, 'UTF-8'));
include "footer.php";
?>