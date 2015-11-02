<?php

defined('SE_PAGE') or exit();


//
//  THIS FILE CONTAINS ALBUM-RELATED FUNCTIONS
//
//  FUNCTIONS IN THIS CLASS:
//
//    search_album()
//    mediatag_album()
//    deleteuser_album()
//    site_statistics_album()
//






// THIS FUNCTION IS RUN DURING THE SEARCH PROCESS TO SEARCH THROUGH ALBUMS AND MEDIA
// INPUT: 
// OUTPUT: 
function search_album() {
	global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results;

	// CONSTRUCT QUERY
	$album_query = "
	(
	SELECT
          '1' AS sub_type,
	  se_media.media_album_id AS album_id,
	  se_media.media_title AS title,
	  se_media.media_desc AS description,
	  se_media.media_id AS media_id,
	  se_media.media_ext AS media_ext,
	  se_users.user_id,
	  se_users.user_username,
	  se_users.user_photo,
	  se_users.user_fname,
	  se_users.user_lname
	FROM
	  se_media,
	  se_albums,
	  se_users,
	  se_levels
	WHERE
	  se_media.media_album_id=se_albums.album_id AND
	  se_albums.album_user_id=se_users.user_id AND
	  se_users.user_level_id=se_levels.level_id AND
	  (
	    se_albums.album_search='1' OR
	    se_levels.level_album_search='0'
	  )
	  AND
	  (
	    se_media.media_title LIKE '%{$search_text}%' OR
	    se_media.media_desc LIKE '%{$search_text}%'
	  )
	ORDER BY media_id DESC
	)
	UNION ALL
	(
	SELECT
	  '2' AS sub_type,
	  se_albums.album_id AS album_id,
	  se_albums.album_title AS title,
	  se_albums.album_desc AS description,
	  se_albums.album_cover AS media_id,
	  se_media.media_ext AS media_ext,
	  se_users.user_id,
	  se_users.user_username,
	  se_users.user_photo,
	  se_users.user_fname,
	  se_users.user_lname
	FROM
	  se_albums,
	  se_users,
	  se_levels,
	  se_media
	WHERE
	  se_albums.album_user_id=se_users.user_id AND
	  se_users.user_level_id=se_levels.level_id AND
	  se_albums.album_cover=se_media.media_id AND 
	  (
	    se_albums.album_search='1' OR
	    se_levels.level_album_search='0'
	  )
	  AND
	  (
	    se_albums.album_title LIKE '%{$search_text}%' OR
	    se_albums.album_desc LIKE '%{$search_text}%'
	  )
	ORDER BY album_id DESC
	)"; 

	// GET TOTAL RESULTS
	$total_albums = $database->database_num_rows($database->database_query($album_query." LIMIT 201"));

	// IF NOT TOTAL ONLY
	if($t == "album")
  {
	  // MAKE ALBUM PAGES
	  $start = ($p - 1) * $results_per_page;
	  $limit = $results_per_page+1;

	  // SEARCH ALBUMS
	  $albums = $database->database_query($album_query." ORDER BY album_id DESC LIMIT $start, $limit");
	  while($album_info = $database->database_fetch_assoc($albums)) {

	    // CREATE AN OBJECT FOR USER
	    $profile = new se_user();
	    $profile->user_info['user_id'] = $album_info['user_id'];
	    $profile->user_info['user_username'] = $album_info['user_username'];
	    $profile->user_info['user_fname'] = $album_info['user_fname'];
	    $profile->user_info['user_lname'] = $album_info['user_lname'];
	    $profile->user_info['user_photo'] = $album_info['user_photo'];
	    $profile->user_displayname();

	    // RESULT IS A MEDIA
	    if($album_info[sub_type] == 1) {
	      $result_url = $url->url_create('album_file', $album_info['user_username'], $album_info['album_id'], $album_info['media_id']);
	      $result_name = 1000119;
	      $result_desc = 1000121;

	    // RESULT IS AN ALBUM
	    } else {
	      $result_url = $url->url_create('album', $album_info['user_username'], $album_info['album_id']);
	      $result_name = 1000120;
	      $result_desc = 1000122;
	    }

	    // SET THUMBNAIL, IF AVAILABLE
	    switch($album_info['media_ext']) {
		case "jpeg": case "jpg": case "gif": case "png": case "bmp":
		  $thumb_path = $url->url_userdir($album_info['user_id']).$album_info['media_id']."_thumb.jpg";
		  break;
		case "mp3": case "mp4": case "wav":
		  $thumb_path = "./images/icons/audio_big.gif";
		  break;
		case "mpeg": case "mpg": case "mpa": case "avi": case "swf": case "mov": case "ram": case "rm":
		  $thumb_path = "./images/icons/video_big.gif";
		  break;
		default:
		  $thumb_path = "./images/icons/file_big.gif";
	    }

	    if(!file_exists($thumb_path)) { $thumb_path = "./images/icons/file_big.gif"; }

	    // IF NO TITLE
	    if($album_info['title'] == "") { SE_Language::_preload(589); SE_Language::load(); $album_info['title'] = SE_Language::_get(589); }

	    // IF DESCRIPTION IS LONG
	    if(strlen($album_info['description']) > 150) { $album_info['description'] = substr($album_info['description'], 0, 147)."..."; }

	    $results[] = Array('result_url' => $result_url,
				'result_icon' => $thumb_path,
				'result_name' => $result_name,
				'result_name_1' => $album_info['title'],
				'result_desc' => $result_desc,
				'result_desc_1' => $url->url_create('profile', $album_info['user_username']),
				'result_desc_2' => $profile->user_displayname,
				'result_desc_3' => $album_info['description']);
	  }

	  // SET TOTAL RESULTS
	  $total_results = $total_albums;

	}

	// SET ARRAY VALUES
	SE_Language::_preload_multi(1000118, 1000119, 1000120, 1000121, 1000122);
	if($total_albums > 200) { $total_albums = "200+"; }
	$search_objects[] = Array('search_type' => 'album',
				'search_lang' => 1000118,
				'search_total' => $total_albums);

} // END search_album() FUNCTION









// THIS FUNCTION IS RUN WHEN RETRIEVING PHOTOS OF A USER
// INPUT: 
// OUTPUT: 
function mediatag_album() {
	global $photo_query, $tag_query, $owner, $user;

	if($photo_query != "") { $photo_query .= " UNION ALL "; }

	$photo_query .= "(SELECT 'media' 		AS type,
				'media_id'		AS type_id,
				''			AS type_prefix,
				mediatag_media_id 	AS media_id,
				mediatag_date		AS mediatag_date,
				CONCAT('album.php?user=', user_username, '&album_id=[media_parent_id]')	AS media_parent_url,
				media_album_id		AS media_parent_id,
				album_title		AS media_parent_title,
				album_user_id		AS owner_user_id,
				user_id			AS user_id,
				user_username		AS user_username,
				user_fname		AS user_fname,
				user_lname		AS user_lname,
				CONCAT('./uploads_user/', user_id+999-((user_id-1)%1000), '/', user_id, '/') AS media_dir,
				media_date		AS media_date,
				media_title		AS media_title,
				media_desc		AS media_desc,
				media_ext		AS media_ext,
				media_filesize		AS media_filesize,
				CASE
				  WHEN ((se_albums.album_tag & @SE_PRIVACY_REGISTERED) AND '{$user->user_exists}'<>0)
				    THEN TRUE
				  WHEN ((se_albums.album_tag & @SE_PRIVACY_ANONYMOUS) AND '{$user->user_exists}'=0)
				    THEN TRUE
				  WHEN ((se_albums.album_tag & @SE_PRIVACY_SELF) AND se_albums.album_user_id='{$user->user_info['user_id']}')
				    THEN TRUE
				  WHEN ((se_albums.album_tag & @SE_PRIVACY_FRIEND) AND (SELECT TRUE FROM se_friends WHERE friend_user_id1=se_albums.album_user_id AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_albums.album_tag & @SE_PRIVACY_SUBNET) AND se_users.user_subnet_id='{$user->user_info['user_subnet_id']}')
				    THEN TRUE
				  WHEN ((se_albums.album_tag & @SE_PRIVACY_FRIEND2) AND se_users.user_subnet_id='{$user->user_info['user_subnet_id']}' AND (SELECT TRUE FROM se_friends AS friends_primary LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE friends_primary.friend_user_id1=se_albums.album_user_id AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' LIMIT 1))
				    THEN TRUE
				  ELSE FALSE
				END
				AS allowed_to_tag,
				CASE
				  WHEN ((se_albums.album_comments & @SE_PRIVACY_REGISTERED) AND '{$user->user_exists}'<>0)
				    THEN TRUE
				  WHEN ((se_albums.album_comments & @SE_PRIVACY_ANONYMOUS) AND '{$user->user_exists}'=0)
				    THEN TRUE
				  WHEN ((se_albums.album_comments & @SE_PRIVACY_SELF) AND se_albums.album_user_id='{$user->user_info['user_id']}')
				    THEN TRUE
				  WHEN ((se_albums.album_comments & @SE_PRIVACY_FRIEND) AND (SELECT TRUE FROM se_friends WHERE friend_user_id1=se_albums.album_user_id AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_albums.album_comments & @SE_PRIVACY_SUBNET) AND se_users.user_subnet_id='{$user->user_info['user_subnet_id']}')
				    THEN TRUE
				  WHEN ((se_albums.album_comments & @SE_PRIVACY_FRIEND2) AND se_users.user_subnet_id='{$user->user_info['user_subnet_id']}' AND (SELECT TRUE FROM se_friends AS friends_primary LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE friends_primary.friend_user_id1=se_albums.album_user_id AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' LIMIT 1))
				    THEN TRUE
				  ELSE FALSE
				END
				AS allowed_to_comment
			FROM se_mediatags
			LEFT JOIN se_media
				ON se_mediatags.mediatag_media_id=se_media.media_id
			LEFT JOIN se_albums
				ON se_media.media_album_id=se_albums.album_id
			LEFT JOIN se_users
				ON se_albums.album_user_id=se_users.user_id
			WHERE se_mediatags.mediatag_user_id='{$owner->user_info['user_id']}'
			)";

	$tag_query['media'] = "SELECT se_mediatags.*, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_mediatags LEFT JOIN se_users ON se_mediatags.mediatag_user_id=se_users.user_id WHERE mediatag_media_id='[media_id]' ORDER BY mediatag_id ASC";

} // END mediatag_album() FUNCTION









// THIS FUNCTION IS RUN WHEN A USER IS DELETED
// INPUT: $user_id REPRESENTING THE USER ID OF THE USER BEING DELETED
// OUTPUT: 
function deleteuser_album($user_id) {
	global $database;

	// DELETE ALBUMS, MEDIA, AND COMMENTS
	$database->database_query("DELETE FROM se_albums, se_media, se_mediacomments, se_mediatags USING se_albums LEFT JOIN se_media ON se_albums.album_id=se_media.media_album_id LEFT JOIN se_mediacomments ON se_media.media_id=se_mediacomments.mediacomment_media_id LEFT JOIN se_mediatags ON se_media.media_id=se_mediatags.mediatag_media_id WHERE se_albums.album_user_id='{$user_id}'");

	// DELETE TAGS OF USER
	$database->database_query("UPDATE se_mediatags SET mediatag_user_id='0' WHERE mediatag_user_id='{$user_id}'");

	// DELETE STYLE
	$database->database_query("DELETE FROM se_albumstyles WHERE albumstyle_user_id='{$user_id}'");
}

// END deleteuser_album() FUNCTION









// THIS FUNCTION IS RUN WHEN GENERATING SITE STATISTICS
// INPUT: 
// OUTPUT: 
function site_statistics_album(&$args)
{
  global $database;
  
  $statistics =& $args['statistics'];
  
  // NOTE: CACHING WILL BE HANDLED BY THE FUNCTION THAT CALLS THIS
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(album_id) AS total FROM se_albums"));
  $statistics['albums'] = array(
    'title' => 1000174,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  /*
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(media_id) AS total FROM se_media"));
  $statistics['media'] = array(
    'title' => 1000175,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(mediacomment_id) AS total FROM se_mediacomments"));
  $statistics['mediacomments'] = array(
    'title' => 1000176,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(mediatag_id) AS total FROM se_mediatags"));
  $statistics['mediatags'] = array(
    'title' => 1000177,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  */
}

// END site_statistics_album() FUNCTION

?>