<?php

//
//  THIS FILE CONTAINS EVENT-RELATED FUNCTIONS
//
//  FUNCTIONS IN THIS CLASS:
//
//    notification_event()
//    action_privacy_event()
//    mediatag_event()
//    search_event()
//    deleteuser_event()
//    event_privacy_levels()
//    site_statistics_event()
//


defined('SE_PAGE') or exit();








//
// THIS FUNCTION IS RUN ON THE USER HOME PAGE TO GENERATE NOTIFICATIONS
//
// INPUT: 
//    void
//
// OUTPUT: 
//    void
//

function notification_event(&$notifications)
{
	global $database, $user, $url, $functions_event;

	// SET VARIABLES AND INITIALIZE EVENT OBJECT
	$event = new se_event($user->user_info['user_id']);
	$where = "(se_eventmembers.eventmember_status='0')";

	// GET TOTAL EVENT INVITES
	$total_events = $event->event_total($where);

	// IF EVENT INVITES, CONTINUE
	if( $total_events>0 )
  {
	  // GET PLUGIN ICON
	  $plugin_info = $database->database_fetch_assoc($database->database_query("SELECT plugin_icon FROM se_plugins WHERE plugin_type='event'"));
    
	  // SET NOTIFICATION ARRAY
	  $notifications[] = array(
      'notify_url' => $url->url_base."user_event.php?show_notification=1",
      'notify_icon' => $plugin_info[plugin_icon],
      'notify_text' => $total_events.$functions_event[11]
    );
	}
}

// END notification_event() FUNCTION









//
// THIS FUNCTION ADDS A CLAUSE IN ACTION QUERY TO ACCOUNT FOR EVENT PRIVACY
//
// INPUT: 
//
// OUTPUT: 
//

function action_privacy_event($args)
{
	global $user;

	$args['actions_query'] .= "
    WHEN se_actions.action_object_owner='event' THEN
      CASE
        WHEN ((se_actions.action_object_privacy & 32) AND '{$user->user_exists}'<>0)
          THEN TRUE
        WHEN ((se_actions.action_object_privacy & 64) AND '{$user->user_exists}'=0)
          THEN TRUE
        WHEN ((se_actions.action_object_privacy & 2) AND (SELECT TRUE FROM se_eventmembers WHERE eventmember_event_id=se_actions.action_object_owner_id AND eventmember_user_id='{$user->user_info['user_id']}' AND eventmember_status='1' LIMIT 1))
          THEN TRUE
        WHEN ((se_actions.action_object_privacy & 4) AND (SELECT TRUE FROM se_friends JOIN se_events ON se_friends.friend_user_id1=se_events.event_user_id WHERE se_events.event_id=se_actions.action_object_owner_id AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
          THEN TRUE
        WHEN ((se_actions.action_object_privacy & 8) AND (SELECT TRUE FROM se_friends JOIN se_eventmembers ON se_friends.friend_user_id1=se_eventmembers.eventmember_user_id WHERE se_eventmembers.eventmember_event_id=se_actions.action_object_owner_id AND eventmember_status='1' AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
          THEN TRUE
        WHEN ((se_actions.action_object_privacy & 16) AND (SELECT TRUE FROM se_eventmembers LEFT JOIN se_friends AS friends_primary ON se_eventmembers.eventmember_user_id=friends_primary.friend_user_id1 LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE eventmember_status='1' AND eventmember_event_id=se_actions.action_object_owner_id AND friends_primary.friend_status='1' AND friends_secondary.friend_status='1' AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' LIMIT 1))
          THEN TRUE
        ELSE FALSE
      END
  ";				  
}

// END action_privacy_event() FUNCTION









//
// THIS FUNCTION IS RUN WHEN RETRIEVING PHOTOS OF A USER
//
// INPUT: 
//
// OUTPUT: 
//

function mediatag_event()
{
	global $photo_query, $tag_query, $owner, $user;

	if($photo_query != "") { $photo_query .= " UNION ALL "; }
  
	$photo_query .= "
    (
      SELECT
        'eventmedia'			                              AS type,
        'eventmedia_id'                                 AS type_id,
        'event'				                                  AS type_prefix,
        se_eventmediatags.eventmediatag_eventmedia_id   AS media_id,
        se_eventmediatags.eventmediatag_date            AS mediatag_date,
        'event.php?event_id=[media_parent_id]&v=photos' AS media_parent_url,
        se_events.event_id			                        AS media_parent_id,
        se_events.event_title			                      AS media_parent_title,
        se_events.event_user_id			                    AS owner_user_id,
        0				                                        AS user_id,
        ''				                                      AS user_username,
        ''				                                      AS user_fname,
        ''				                                      AS user_lname,
				CONCAT('./uploads_event/', se_events.event_id+999-((se_events.event_id-1)%1000), '/', se_events.event_id, '/') AS media_dir,
				se_eventmedia.eventmedia_date			              AS media_date,
				se_eventmedia.eventmedia_title		              AS media_title,
				se_eventmedia.eventmedia_desc			              AS media_desc,
				se_eventmedia.eventmedia_ext			              AS media_ext,
				se_eventmedia.eventmedia_filesize		            AS media_filesize,
        
				(CASE
				  WHEN ((se_eventalbums.eventalbum_tag & 32) AND '{$user->user_exists}'<>0)
				    THEN TRUE
				  WHEN ((se_eventalbums.eventalbum_tag & 64) AND '{$user->user_exists}'=0)
				    THEN TRUE
				  WHEN ((se_eventalbums.eventalbum_tag & 1) AND '{$user->user_exists}'<>0 AND event_user_id='{$user->user_info['user_id']}')
				    THEN TRUE
				  WHEN ((se_eventalbums.eventalbum_tag & 2) AND (SELECT TRUE FROM se_eventmembers WHERE se_eventmembers.eventmember_event_id=se_eventalbums.eventalbum_event_id AND se_eventmembers.eventmember_user_id='{$user->user_info['user_id']}' AND se_eventmembers.eventmember_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_eventalbums.eventalbum_tag & 4) AND (SELECT TRUE FROM se_friends JOIN se_events AS t1 ON se_friends.friend_user_id1=t1.event_user_id WHERE t1.event_id=se_eventalbums.eventalbum_event_id && friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_eventalbums.eventalbum_tag & 8) AND (SELECT TRUE FROM se_friends JOIN se_eventmembers ON se_friends.friend_user_id1=se_eventmembers.eventmember_user_id WHERE se_eventmembers.eventmember_status='1' && se_eventmembers.eventmember_event_id=se_eventalbums.eventalbum_event_id AND se_friends.friend_user_id2='{$user->user_info[user_id]}' AND se_friends.friend_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_eventalbums.eventalbum_tag & 16) AND (SELECT TRUE FROM se_eventmembers LEFT JOIN se_friends AS friends_primary ON se_eventmembers.eventmember_user_id=friends_primary.friend_user_id1 LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE se_eventmembers.eventmember_status='1' AND se_eventmembers.eventmember_event_id=se_eventalbums.eventalbum_event_id AND friends_primary.friend_status='1' AND friends_secondary.friend_status='1' AND friends_secondary.friend_user_id2='{$user->user_info[user_id]}' LIMIT 1))
				    THEN TRUE
				  ELSE FALSE
				END)
				AS allowed_to_tag,
				(CASE
				  WHEN ((se_events.event_comments & 32) AND '{$user->user_exists}'<>0)
				    THEN TRUE
				  WHEN ((se_events.event_comments & 64) AND '{$user->user_exists}'=0)
				    THEN TRUE
				  WHEN ((se_events.event_comments & 1) AND '{$user->user_exists}'<>0 AND event_user_id='{$user->user_info['user_id']}')
				    THEN TRUE
				  WHEN ((se_events.event_comments & 2) AND (SELECT TRUE FROM se_eventmembers WHERE se_eventmembers.eventmember_event_id=se_events.event_id AND se_eventmembers.eventmember_user_id='{$user->user_info['user_id']}' AND se_eventmembers.eventmember_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_events.event_comments & 4) AND (SELECT TRUE FROM se_friends JOIN se_events AS t1 ON se_friends.friend_user_id1=t1.event_user_id WHERE friend_user_id2='{$user->user_info['user_id']}' && t1.event_id=se_events.event_id AND se_friends.friend_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_events.event_comments & 8) AND (SELECT TRUE FROM se_friends JOIN se_eventmembers ON se_friends.friend_user_id1=se_eventmembers.eventmember_user_id WHERE se_eventmembers.eventmember_status='1' && se_eventmembers.eventmember_event_id=se_events.event_id AND se_friends.friend_user_id2='{$user->user_info['user_id']}' AND se_friends.friend_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_events.event_comments & 16) AND (SELECT TRUE FROM se_eventmembers LEFT JOIN se_friends AS friends_primary ON se_eventmembers.eventmember_user_id=friends_primary.friend_user_id1 LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE eventmember_status='1' AND eventmember_event_id=se_events.event_id AND friends_primary.friend_status='1' AND friends_secondary.friend_status='1' AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' LIMIT 1))
				    THEN TRUE
				  ELSE FALSE
				END)
				AS allowed_to_comment
    FROM
      se_eventmediatags
    LEFT JOIN
      se_eventmedia
      ON se_eventmediatags.eventmediatag_eventmedia_id=se_eventmedia.eventmedia_id
    LEFT JOIN
      se_eventalbums
      ON se_eventmedia.eventmedia_eventalbum_id=se_eventalbums.eventalbum_id
    LEFT JOIN
      se_events
      ON se_eventalbums.eventalbum_event_id=se_events.event_id
    WHERE
      se_eventmediatags.eventmediatag_user_id='{$owner->user_info['user_id']}'
    )
  ";

	$tag_query['eventmedia'] = "SELECT eventmediatag_id AS mediatag_id, eventmediatag_eventmedia_id AS mediatag_media_id, eventmediatag_user_id AS mediatag_user_id, eventmediatag_x AS mediatag_x, eventmediatag_y AS mediatag_y, eventmediatag_height AS mediatag_height, eventmediatag_width AS mediatag_width, eventmediatag_text AS mediatag_text, eventmediatag_date AS mediatag_date, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_eventmediatags LEFT JOIN se_users ON se_eventmediatags.eventmediatag_user_id=se_users.user_id WHERE eventmediatag_eventmedia_id='[media_id]' ORDER BY eventmediatag_id ASC";

}

// END mediatag_event() FUNCTION








//
// THIS FUNCTION IS RUN DURING THE SEARCH PROCESS TO SEARCH THROUGH EVENTS AND EVENT MEDIA
//
// INPUT:
//    void
//
// OUTPUT: 
//    void
//

function search_event()
{
	global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results;
  
	// GET EVENT FIELDS
	$fields = $database->database_query("SELECT eventfield_id AS field_id, eventfield_type AS field_type, eventfield_options AS field_options FROM se_eventfields WHERE eventfield_type<>'5' AND (eventfield_dependency<>'0' OR (eventfield_dependency='0' AND eventfield_display<>'0'))");
	$event_query = "se_events.event_title LIKE '%{$search_text}%' OR se_events.event_desc LIKE '%{$search_text}%'";
  
	// LOOP OVER FIELDS
	while( $field_info=$database->database_fetch_assoc($fields) )
  {
	  // TEXT FIELD OR TEXTAREA
	  if($field_info['field_type'] == 1 || $field_info['field_type'] == 2) {
	    if($event_query != "") { $event_query .= " OR "; }
	    $event_query .= "se_eventvalues.eventvalue_{$field_info['field_id']} LIKE '%{$search_text}%'";
    }
    
	  // RADIO OR SELECT BOX
	  elseif($field_info['field_type'] == 3 || $field_info['field_type'] == 4)
    {
	    $options = unserialize($field_info['field_options']);
 	    $langids = Array();
	    $cases = Array();
	    for($i=0,$max=count($options);$i<$max;$i++) { 
	      $cases[] = "WHEN languagevar_id='{$options[$i]['label']}' THEN {$options[$i][value]}";
	      $langids[] = $options[$i]['label']; 
	    }
	    if(count($cases) != 0) {
	      if($event_query != "") { $event_query .= " OR "; }
	      $event_query .= "se_eventvalues.eventvalue_{$field_info['field_id']} IN (SELECT CASE ".implode(" ", $cases)." END AS value FROM se_languagevars WHERE languagevar_id IN (".implode(", ", $langids).") AND languagevar_value LIKE '%{$search_text}%')";
	    }
    }
    
	  // CHECKBOX
	  elseif($field_info['field_type'] == 6)
    {
	    $options = unserialize($field_info['field_options']);
 	    $langids = Array();
	    $cases = Array();
	    for($i=0,$max=count($options);$i<$max;$i++) { 
	      $cases[] = "WHEN languagevar_id='{$options[$i]['label']}' THEN ".(pow(2, $i));
	      $langids[] = $options[$i]['label']; 
	    }
	    if(count($cases) != 0) {
	      if($event_query != "") { $event_query .= " OR "; }
	      $event_query .= "se_eventvalues.eventvalue_{$field_info['field_id']} & (SELECT sum(CASE ".implode(" ", $cases)." END) AS value FROM se_languagevars WHERE languagevar_id IN (".implode(", ", $langids).") AND languagevar_value LIKE '%{$search_text}%')";
	    }
	  }
	}

	// CONSTRUCT QUERY
	$event_query = "
    (
      SELECT 
        '1' AS sub_type,
        se_events.event_id AS event_id, 
        se_events.event_title AS event_title, 
        se_events.event_photo AS event_photo,
        '' AS title,
        se_events.event_desc AS description,
        '' AS id,
        '' AS extra
      FROM 
        se_eventvalues 
      LEFT JOIN 
        se_events 
      ON 
        se_eventvalues.eventvalue_event_id=se_events.event_id 
      WHERE 
        se_events.event_search='1' 
        AND 
        ($event_query)
      ORDER BY event_id DESC
    )
    UNION ALL
    (
      SELECT
        '2' AS sub_type,
        se_events.event_id AS event_id, 
        se_events.event_title AS event_title, 
        se_events.event_photo AS event_photo,
        se_eventmedia.eventmedia_title AS title,
        se_eventmedia.eventmedia_desc AS description,
        se_eventmedia.eventmedia_id AS id,
        se_eventmedia.eventmedia_ext AS extra
      FROM
        se_eventmedia,
        se_eventalbums,
        se_events
      WHERE
        se_eventmedia.eventmedia_eventalbum_id=se_eventalbums.eventalbum_id AND
        se_eventalbums.eventalbum_event_id=se_events.event_id AND
        se_events.event_search='1'
        AND
        (
          se_eventmedia.eventmedia_title LIKE '%{$search_text}%' OR
          se_eventmedia.eventmedia_desc LIKE '%{$search_text}%'
        )
      ORDER BY eventmedia_id DESC
    )
  ";

	// GET TOTAL EVENT RESULTS
	$total_events = $database->database_num_rows($database->database_query($event_query." LIMIT 201"));

	// IF NOT TOTAL ONLY
	if( $t=="event" )
  {
	  // MAKE EVENT PAGES
	  $start = ($p - 1) * $results_per_page;
	  $limit = $results_per_page + 1;
    
	  // SEARCH EVENTS
	  $resource = $database->database_query($event_query." LIMIT $start, $limit");
    
	  while( $event_info=$database->database_fetch_assoc($resource) )
    {
	    // SET UP EVENT
	    $event = new se_event();
	    $event->event_info['event_id']    = $event_info['event_id'];
	    $event->event_info['event_photo'] = $event_info['event_photo'];
	    $thumb_path = $event->event_photo('./images/nophoto.gif', TRUE);
      
	    // IF DESCRIPTION IS LONG
	    if( strlen($event_info['description']) > 150 ) $event_info['description'] = substr($event_info['description'], 0, 147)."...";
	    if( strlen($event_info['event_desc'])  > 150 ) $event_info['event_desc']  = substr($event_info['event_desc'],  0, 147)."...";
      
	    // RESULT IS A EVENT
	    if( $event_info[sub_type]==1 )
      {
	      $result_url = $url->url_create('event', NULL, $event_info['event_id']);
	      $result_name = 3000282;
	      $result_name_1 = $event_info['event_title'];
	      $result_desc = 3000284;
	      $result_desc_1 = $event_info['description'];
      }
      
	    // RESULT IS A PHOTO
	    elseif( $event_info[sub_type]==2 )
      {
	      $result_url = $url->url_create('event_media', NULL, $event_info['event_id'], $event_info['id']);
	      $result_name = 3000283;
	      $result_name_1 = $event_info['title'];
	      $result_desc = 3000285;
	      $result_desc_1 = "event.php?event_id=".$event_info[event_id];
	      $result_desc_2 = $event_info['event_title'];
	      $result_desc_3 = $event_info['description'];
        
	      // SET THUMBNAIL, IF AVAILABLE
	      switch( $event_info['extra'] )
        {
          case "jpeg": case "jpg": case "gif": case "png": case "bmp":
            $thumb_path = $event->event_dir($event->event_info['event_id']).$event_info['id']."_thumb.jpg";
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
      }
      
	    $results[] = array(
        'result_url'    => $result_url,
				'result_icon'   => $thumb_path,
				'result_name'   => $result_name,
				'result_name_1' => $result_name_1,
				'result_desc'   => $result_desc,
				'result_desc_1' => $result_desc_1,
				'result_desc_2' => $result_desc_2,
				'result_desc_3' => $result_desc_3
      );
	  }
    
	  // SET TOTAL RESULTS
	  $total_results = $total_events;
	}

	// SET ARRAY VALUES
	SE_Language::_preload_multi(3000281, 3000282, 3000283, 3000284, 3000285);
  
	if($total_events > 200) $total_events = "200+";
	
  $search_objects[] = array(
    'search_type'   => 'event',
    'search_lang'   => 3000281,
    'search_total'  => $total_events
  );
}

// END search_event() FUNCTION








//
// THIS FUNCTION IS RUN WHEN A USER IS DELETED
//
// INPUT:
//    $user_id REPRESENTING THE USER ID OF THE USER BEING DELETED
//
// OUTPUT: 
//  void
//

function deleteuser_event($user_id)
{
	global $database;

	// INITATE EVENT OBJECT
	$event = new se_event($user_id);

	// LOOP OVER EVENTS AND DELETE THEM
	$events = $database->database_query("SELECT event_id FROM se_events WHERE event_user_id='{$user_id}'");
	while($event_info = $database->database_fetch_assoc($events)) {
	  $event->event_delete($event_info['event_id']);
	}

	// DELETE USER FROM EVENT GUESTLISTS
	$database->database_query("DELETE FROM se_eventmembers WHERE eventmember_user_id='{$user_id}'");
	
	// DELETE USER'S COMMENTS
	$database->database_query("DELETE FROM se_eventcomments WHERE eventcomment_authoruser_id='{$user_id}'");
	$database->database_query("DELETE FROM se_eventmediacomments WHERE eventmediacomment_authoruser_id='{$user_id}'");

}

// END deleteuser_event() FUNCTION








//
// THIS FUNCTION RETURNS TEXT CORRESPONDING TO THE GIVEN EVENT PRIVACY LEVEL
//
// INPUT:
//    $privacy_level REPRESENTING THE LEVEL OF EVENT PRIVACY
//
// OUTPUT:
//    A STRING EXPLAINING THE GIVEN PRIVACY SETTING
//

function event_privacy_levels($privacy_level)
{
	switch($privacy_level)
  {
	  case 127: $privacy = 323;     break;
	  case 63:  $privacy = 324;     break;
	  case 31:  $privacy = 3000014; break;
	  case 15:  $privacy = 3000015; break;
	  case 7:   $privacy = 3000016; break;
	  case 3:   $privacy = 3000017; break;
	  case 1:   $privacy = 3000018; break;
	  case 0:   $privacy = 329;     break;
	  default:  $privacy = NULL;    break;
	}
  
  if( $privacy && class_exists('SE_Language') )
    SE_Language::_preload($privacy);

	return $privacy;
}

// END event_privacy_levels() FUNCTION








//
// GENERATES AN ERROR STRING FOR DATABASE QUERIES
//
// DEPRECATED AND REMOVED: 3.04
//

function event_error($err_file, $err_line, $err_msg, $err_query, $is_fatal=TRUE)
{
  return;
}

// END event_error() FUNCTION









// THIS FUNCTION IS RUN WHEN GENERATING SITE STATISTICS
// INPUT: 
// OUTPUT: 
function site_statistics_event(&$args)
{
  global $database;
  
  $statistics =& $args['statistics'];
  
  // NOTE: CACHING WILL BE HANDLED BY THE FUNCTION THAT CALLS THIS
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(event_id) AS total FROM se_events"));
  $statistics['events'] = array(
    'title' => 3000291,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  /*
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(eventcomment_id) AS total FROM se_eventcomments"));
  $statistics['eventcomments'] = array(
    'title' => 3000292,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(eventmedia_id) AS total FROM se_eventmedia"));
  $statistics['eventmedia'] = array(
    'title' => 3000293,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(eventmember_id) AS total FROM se_eventmembers WHERE eventmember_approved=1 && eventmember_status=1"));
  $statistics['eventmembers'] = array(
    'title' => 3000294,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  */
}

// END site_statistics_event() FUNCTION

?>