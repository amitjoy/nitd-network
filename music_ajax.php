<?php
ob_start();
include "header.php";

$task = ( !empty($_POST['task']) ? $_POST['task'] : ( !empty($_GET['task']) ? $_GET['task'] : NULL ) );
$user_id = ( !empty($_POST['user_id']) ? $_POST['user_id'] : ( !empty($_GET['user_id']) ? $_GET['user_id'] : NULL ) );
$music_id = ( !empty($_POST['music_id']) ? $_POST['music_id'] : ( !empty($_GET['music_id']) ? $_GET['music_id'] : NULL ) );
$music_order = ( !empty($_POST['music_order']) ? $_POST['music_order'] : ( !empty($_GET['music_order']) ? $_GET['music_order'] : NULL ) );
$music_title = ( !empty($_POST['music_title']) ? $_POST['music_title'] : ( !empty($_GET['music_title']) ? $_GET['music_title'] : NULL ) );

// Fix the question mark thingy for the flashplayer
if( $user_id && strpos($user_id, '?')!==FALSE )
{
  $tmp_user_id_arr = explode("?", $user_id);
  $user_id = $tmp_user_id_arr[0];
}

$is_error = FALSE;

// GENERATE PLAYLIST
if( $task=="playlist" || !$task )
{
  // GET MUSIC
  $music = new se_music($user_id);
  $musiclist = $music->music_list();
  $user_dir = $url->url_userdir($user_id);
  
  // GENERATE PLAYLIST
  $tracklist_xml = "";
  foreach($musiclist as $song) $tracklist_xml .= <<<EOF
     <track>
      <location>{$user_dir}{$song[music_id]}.{$song[music_ext]}</location>
      <annotation>{$song[music_title]}</annotation>
     </track>

EOF;
  
  // OUTPUT
  ob_end_clean();
  
  echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?><playlist version="0" xmlns = "http://xspf.org/ns/0/">
  <trackList>
{$tracklist_xml}  </trackList>
</playlist>
EOF;
  exit();
}

// DELETE SONG
elseif( $task=="deletesong" )
{
  if( empty($user) || !$user->user_exists || !$user->level_info['level_music_allow'] )
    $is_error = 1;
  
  $music = new se_music($user->user_info['user_id']);
  
  // OUTPUT
  ob_end_clean();
  
  if( !$is_error && $music->music_delete($music_id) )
    echo '{"result":"success"}';
  else
    echo '{"result":"failure"}';
  
  exit();
}

// EDIT SONG TITLE
elseif( $task=="editsongtitle" )
{
  if( empty($user) || !$user->user_exists || !$user->level_info['level_music_allow'] )
    $is_error = 1;
  
  if( !trim($music_title) )
  {
    SE_Language::_preload(4000086);
    SE_Language::load();
    $music_title = SE_Language::_get(4000086);
  }
  
  $music = new se_music($user->user_info['user_id']);
  
  // OUTPUT
  ob_end_clean();
  
  if( !$is_error && $music->music_track_update($music_id, $music_title) )
    echo '{"result":"success"}';
  else
    echo '{"result":"failure"}';
  
  exit();
}

// MOVE UP SONG
elseif( $task=="moveupsong" )
{
  if( empty($user) || !$user->user_exists || !$user->level_info['level_music_allow'] )
    $is_error = 1;
  
  $music = new se_music($user->user_info['user_id']);
  
  // OUTPUT
  ob_end_clean();
  
  if( !$is_error && $music->music_moveup($music_id) )
    echo '{"result":"success"}';
  else
    echo '{"result":"failure"}';
  
  exit();
}

// COMPLETE PLAYLIST REORDER
elseif( $task=="reordermusic" )
{
  if( empty($user) || !$user->user_exists || !$user->level_info['level_music_allow'] )
    $is_error = 1;
  
  $music = new se_music($user->user_info['user_id']);
  
  // OUTPUT
  ob_end_clean();
  
  if( !$is_error && $music->music_reorder($music_order) )
    echo '{"result":"success"}';
  else
    echo '{"result":"failure"}';
  
  exit();
}


?>