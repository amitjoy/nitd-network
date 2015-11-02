<?php
$page = "admin_levels_wallsettings";
include "admin_header.php";

if(isset($_POST['task'])) { $task = $_POST['task']; } else { $task = "main"; }
if(isset($_POST['level_id'])) { $level_id = $_POST['level_id']; } elseif(isset($_GET['level_id'])) { $level_id = $_GET['level_id']; } else { $level_id = 0; }

$query = he_database::placeholder( "SELECT * FROM `se_levels` WHERE `level_id`=?", $level_id );
$level_info = he_database::fetch_row($query);

if ( !$level_info )
{
	header("Location: admin_levels.php");
	exit();
}

// SET RESULT VARIABLE
$result = 0;

if ($task == "dosave")
{
	$level_info['level_wall_allowed'] = $_POST['level_wall_allowed'];
	$level_info['level_wall_action_privacy']  = is_array($_POST['level_wall_action_privacy']) ? $_POST['level_wall_action_privacy'] : array();
	
    //GET PRIVACY AND PRIVACY DIFFERENCES
    if( empty($level_info['level_wall_action_privacy']) || !is_array($level_info['level_wall_action_privacy']) )
    {
    	$level_info['level_wall_action_privacy'] = array( 63 );
    }

    rsort($level_info['level_wall_action_privacy']);
    $new_privacy_options = $level_info['level_wall_action_privacy'];
    $level_info['level_wall_action_privacy'] = serialize($level_info['level_wall_action_privacy']);
	
    $query = he_database::placeholder( "UPDATE `se_levels` SET `level_wall_allowed`=?, `level_wall_action_privacy`='?'
        WHERE `level_id`=?", $level_info['level_wall_allowed'], $level_info['level_wall_action_privacy'], $level_id );
	he_database::query($query);
	
	$result = 1;
}

// GET PREVIOUS PRIVACY SETTINGS
$privacy_options = array();
for( $c = 6; $c > 0; $c-- )
{
    $priv = pow(2, $c) - 1;
    
    if( user_privacy_levels($priv) != "" && $priv != 1 )
    {
        SE_Language::_preload(user_privacy_levels($priv));
        $privacy_options[$priv] = user_privacy_levels($priv);
    }
}

// ASSIGN VARIABLES AND SHOW ADMIN ADD USER LEVEL PAGE
$smarty->assign('result', $result);
$smarty->assign('level_wall_action_privacy', unserialize($level_info['level_wall_action_privacy']));
$smarty->assign('privacy_options', $privacy_options);
$smarty->assign('level_info', $level_info);
include "admin_footer.php";
?>