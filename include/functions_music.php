<?php

//  THIS FILE CONTAINS ALBUM-RELATED FUNCTIONS
//  FUNCTIONS IN THIS CLASS:
//    search_music()
//    deleteuser_music()














// THIS FUNCTION IS RUN DURING THE SEARCH PROCESS TO SEARCH THROUGH ALBUMS AND MEDIA
// INPUT: 
// OUTPUT: 
function search_music()
{
	global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results, $user;

	// CONSTRUCT QUERY
	$sql = "
    SELECT
      se_music.*,
      se_users.user_id,
      se_users.user_username,
      se_users.user_photo,
      se_users.user_fname,
      se_users.user_lname
    FROM
      se_music
    LEFT JOIN
      se_users
      ON se_users.user_id=se_music.music_user_id
    WHERE
      se_music.music_title LIKE '%$search_text%'
  "; 

	// GET TOTAL RESULTS
	$total_music = $database->database_num_rows($database->database_query($sql." LIMIT 201"));

	// IF NOT TOTAL ONLY
	if( $t=="music" )
  {
	  // MAKE MUSIC PAGES
	  $start = ($p - 1) * $results_per_page;
	  $limit = $results_per_page+1;
    
    // Lang for download song
    SE_Language::_preload(4000095);
    SE_Language::load();
    
	  // SEARCH MUSIC
    $sql .= " ORDER BY se_music.music_id DESC LIMIT $start, $limit";
	  $resource = $database->database_query($sql) or die($database->database_error());
	  while( $music_info=$database->database_fetch_assoc($resource) )
    {
	    // CREATE AN OBJECT FOR USER
	    $profile = new se_user();
	    $profile->user_info['user_id']        = $music_info['user_id'];
	    $profile->user_info['user_username']  = $music_info['user_username'];
	    $profile->user_info['user_fname']     = $music_info['user_fname'];
	    $profile->user_info['user_lname']     = $music_info['user_lname'];
	    $profile->user_info['user_photo']     = $music_info['user_photo'];
	    $profile->user_displayname();
      
      $result_url = $url->url_create('profile', $music_info['user_username']);
      $result_name = 4000105;
      $result_desc = 4000106;
      
      $userdir = $url->url_userdir($music_info['user_id']);
      $music_path = "{$userdir}{$music_info['music_id']}.{$music_info['music_ext']}";
      
	    // IF NO TITLE
	    if( !trim($music_info['music_title']) ) { SE_Language::_preload(589); SE_Language::load(); $music_info['music_title'] = SE_Language::_get(589); }
	    
      $dl_lv = SE_Language::_get(4000095);
      
      $desc3 = <<<EOF
<br />
<table><tr><td>
  <object width="17" height="17" data="images/music_button.swf?song_url={$music_path}" type="application/x-shockwave-flash">
    <param value="images/music_button.swf?song_url={$music_path}" name="movie" />
    <img width="17" height="17" alt="" src="noflash.gif" />
  </object>
</td>
EOF;
      if( $user->user_exists && $user->level_info.level_music_allow_downloads ) $desc3 .= <<<EOF
<td style="padding-left:10px;vertical-align:middle;">
  <a type="application/force-download" href="{$music_path}">{$dl_lv}</a>
</td>
EOF;
      $desc3 .= "</tr></table>";
      
	    $results[] = array(
        'result_url'    => $result_url,
				'result_icon'   => './images/icons/music_music48.gif',
				'result_name'   => $result_name,
				'result_name_1' => $music_info['music_title'],
				'result_desc'   => $result_desc,
				'result_desc_1' => $url->url_create('profile', $profile->user_info['user_username']),
				'result_desc_2' => $profile->user_displayname,
				'result_desc_3' => $desc3
      );
	  }
    
	  // SET TOTAL RESULTS
	  $total_results = $total_music;
    
	}

	// SET ARRAY VALUES
	SE_Language::_preload_multi(4000104, 4000105, 4000106);
	if($total_music > 200) { $total_music = "200+"; }
	$search_objects[] = array(
    'search_type' => 'music',
    'search_lang' => 4000104,
    'search_total' => $total_music
  );

}
// END search_album() FUNCTION









// THIS FUNCTION IS RUN WHEN A USER IS DELETED
// INPUT: $user_id REPRESENTING THE USER ID OF THE USER BEING DELETED
// OUTPUT: 
function deleteuser_music($user_id)
{
	global $database;

	// DELETE ALBUMS, MEDIA, AND COMMENTS
	$database->database_query("DELETE FROM se_music WHERE se_music.music_user_id='$user_id'");
}
// END deleteuser_music() FUNCTION




?>