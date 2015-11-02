<?php

$page = "wall_action";
include "header.php";

if (isset($_POST['id'])) $action_id = $_POST['id']; elseif (isset($_GET['id'])) $action_id = $_GET['id'];

$where_clause = he_database::placeholder("se_actions.action_id=?", $action_id);
$wall_action = he_wall::actions_display(0, $setting['setting_actions_actionsonprofile'], $where_clause);
$wall_action = $wall_action[0];

if ( !$setting['setting_he_wall_guest_view'] && $user->level_info['level_wall_allowed'] == 0 )
{
    $page = "error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 690706102);
    $smarty->assign('error_submit', 641);
    include "footer.php";
}

$action_info = he_wall::get_action_info($action_id);

if (!$action_info) {
    he_wall::delete_action_notify($action_id);
}

if ( !$wall_action )
{
    $page = "error";
    $smarty->assign('error_header', 639);
    $smarty->assign('error_message', 690706081);
    $smarty->assign('error_submit', 641);
    include "footer.php";
}

$action_wall_owner = he_wall::get_wall_owner($action_info['action_object_owner'], $action_info['action_object_owner_id']);

if ($user->user_exists 
    && ($user->user_info['user_id']==$wall_action['action_user_id'] 
        || $user->user_info['user_id'] == $action_wall_owner->user_info['user_id'])) {
    he_wall::delete_action_notify($action_id);
}

$wall_action['owner'] = new se_user(array($wall_action['action_user_id']));

$total_comments = he_wall::total_comments( $action_id );
$per_click = $setting['setting_he_wall_comments_per_page'];

$result = he_wall::get_paging_comments($action_id, ($total_comments-$per_click), $per_click );

$action_likes = he_wall::get_likes(array($action_id));

$count = $result['count'];
$action_comments = $result['action_comments'];
$per_click = $result['per_click'];


$actiontype = he_wall::get_actiontype($action_id);

$filename = he_wall::get_action_filename($action_id);

if ( $actiontype == 'wallpostphoto' && !$filename && $action_info['action_object_owner'] == 'group' )
{
    $media_ext = he_wall::get_action_media_ext($action_id, 'group');
    
    $wall_action['action_media'][0]['actionmedia_path'] = str_replace('_thumb.jpg', ".$media_ext", $wall_action['action_media'][0]['actionmedia_path']);    
    
    $group_info = he_wall::get_group_info($action_info['action_object_owner_id']);
    $group_info['media_id'] = he_wall::group_action_media_id($action_id);
    
    if ( $global_plugins['group'] )
    {
        $smarty->assign('group_info', $group_info);
    }
}
elseif ( $actiontype == 'wallpostphoto' && !$filename && $action_info['action_object_owner'] == 'pages' )
{
    $wall_action['action_media'][0]['actionmedia_path'] = str_replace('_thumb', '', $wall_action['action_media'][0]['actionmedia_path']);    
}
elseif ( $actiontype == 'wallpostphoto' && !$filename )
{
    $media_ext = he_wall::get_action_media_ext($action_id);
    
    $wall_action['action_media'][0]['actionmedia_path'] = str_replace('_thumb.jpg', ".$media_ext", $wall_action['action_media'][0]['actionmedia_path']);    
    
    $album_id = he_wall::get_wall_album($wall_action['action_user_id']);
    $album_info = he_wall::get_wall_album_info($album_id);
    
    $global_page_title[0] = 1000155;
    $global_page_title[1] = $wall_action['owner']->user_displayname;
    $global_page_title[2] = $album_info['album_title'];
    $global_page_description[0] = 1000156;
    $global_page_description[1] = $album_info['album_desc'];
    
    
    if ( $global_plugins['album'] )
    {
        $smarty->assign('album_info', $album_info);
    }
}

$smarty->assign('referrer', $_SERVER['HTTP_REFERER']);
$smarty->assign('total_comments', $total_comments);
$smarty->assign('count', $count);
$smarty->assign('actiontype', $actiontype);
$smarty->assign('filename', $filename);
$smarty->assign('action_id', $action_id);
$smarty->assign('limit', $per_click);
$smarty->assign('wall_uid', uniqid());
$smarty->assign('action', $wall_action);
$smarty->assign('js_action_ids', json_encode(array($action_id)));
$smarty->assign('action_comments', $action_comments);
$smarty->assign('action_likes', $action_likes);
$smarty->assign('wall_object', $action_info['action_object_owner']);
$smarty->assign('wall_object_id', (int)$action_info['action_object_owner_id']);

include "footer.php";
?>