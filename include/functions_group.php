<?php


//
//  THIS FILE CONTAINS GROUP-RELATED FUNCTIONS
//
//  FUNCTIONS IN THIS CLASS:
//    action_privacy_group()
//    search_group()
//    mediatag_group()
//    deleteuser_group()
//    group_privacy_levels()
//    site_statistics_group()
//


defined('SE_PAGE') or exit();









// THIS FUNCTION ADDS A CLAUSE IN ACTION QUERY TO ACCOUNT FOR GROUP PRIVACY
// INPUT: 
// OUTPUT: 
function action_privacy_group($args)
{
	global $user;

	$args['actions_query'] .= " WHEN se_actions.action_object_owner='group' THEN
				CASE
				  WHEN ((se_actions.action_object_privacy & 64) AND '{$user->user_exists}'<>0)
				    THEN TRUE
				  WHEN ((se_actions.action_object_privacy & 128) AND '{$user->user_exists}'=0)
				    THEN TRUE
				  WHEN ((se_actions.action_object_privacy & 4) AND (SELECT TRUE FROM se_groupmembers WHERE groupmember_group_id=se_actions.action_object_owner_id AND groupmember_user_id='{$user->user_info['user_id']}' AND groupmember_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_actions.action_object_privacy & 8) AND (SELECT TRUE FROM se_friends JOIN se_groups ON se_friends.friend_user_id1=se_groups.group_user_id WHERE se_groups.group_id=se_actions.action_object_owner_id AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_actions.action_object_privacy & 16) AND (SELECT TRUE FROM se_friends JOIN se_groupmembers ON se_friends.friend_user_id1=se_groupmembers.groupmember_user_id WHERE se_groupmembers.groupmember_group_id=se_actions.action_object_owner_id AND groupmember_status='1' AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_actions.action_object_privacy & 32) AND (SELECT TRUE FROM se_groupmembers LEFT JOIN se_friends AS friends_primary ON se_groupmembers.groupmember_user_id=friends_primary.friend_user_id1 LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE groupmember_status='1' AND groupmember_group_id=se_actions.action_object_owner_id AND friends_primary.friend_status='1' AND friends_secondary.friend_status='1' AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' LIMIT 1))
				    THEN TRUE
				  ELSE FALSE
				END";

} // END action_privacy_group() FUNCTION









// THIS FUNCTION IS RUN DURING THE SEARCH PROCESS TO SEARCH THROUGH GROUPS AND GROUP MEDIA
// INPUT: 
// OUTPUT: 
function search_group()
{
	global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results;


	// GET GROUP FIELDS
	$fields = $database->database_query("SELECT groupfield_id AS field_id, groupfield_type AS field_type, groupfield_options AS field_options FROM se_groupfields WHERE groupfield_type<>'5' AND (groupfield_dependency<>'0' OR (groupfield_dependency='0' AND groupfield_display<>'0'))");
	$group_query = "se_groups.group_title LIKE '%$search_text%' OR se_groups.group_desc LIKE '%$search_text%'";
    
	// LOOP OVER FIELDS
	while($field_info = $database->database_fetch_assoc($fields)) {
    
	  // TEXT FIELD OR TEXTAREA
	  if($field_info['field_type'] == 1 || $field_info['field_type'] == 2) {
	    if($group_query != "") { $group_query .= " OR "; }
	    $group_query .= "se_groupvalues.groupvalue_".$field_info['field_id']." LIKE '%$search_text%'";

	  // RADIO OR SELECT BOX
	  } elseif($field_info['field_type'] == 3 || $field_info['field_type'] == 4) {
	    $options = unserialize($field_info['field_options']);
 	    $langids = Array();
	    $cases = Array();
	    for($i=0,$max=count($options);$i<$max;$i++) { 
	      $cases[] = "WHEN languagevar_id={$options[$i]['label']} THEN {$options[$i]['value']}";
	      $langids[] = $options[$i]['label']; 
	    }
	    if(count($cases) != 0) {
	      if($group_query != "") { $group_query .= " OR "; }
	      $group_query .= "se_groupvalues.groupvalue_".$field_info['field_id']." IN (SELECT CASE ".implode(" ", $cases)." END AS value FROM se_languagevars WHERE languagevar_id IN (".implode(", ", $langids).") AND languagevar_value LIKE '%$search_text%')";
	    }

	  // CHECKBOX
	  } elseif($field_info['field_type'] == 6) {
	    $options = unserialize($field_info['field_options']);
 	    $langids = Array();
	    $cases = Array();
	    for($i=0,$max=count($options);$i<$max;$i++) { 
	      $cases[] = "WHEN languagevar_id={$options[$i]['label']} THEN ".(pow(2, $i));
	      $langids[] = $options[$i][label]; 
	    }
	    if(count($cases) != 0) {
	      if($group_query != "") { $group_query .= " OR "; }
	      $group_query .= "se_groupvalues.groupvalue_".$field_info['field_id']." & (SELECT sum(CASE ".implode(" ", $cases)." END) AS value FROM se_languagevars WHERE languagevar_id IN (".implode(", ", $langids).") AND languagevar_value LIKE '%$search_text%')";
	    }
	  }
	}

	// CONSTRUCT QUERY
	$group_query = "
	(
	SELECT 
	  '1' AS sub_type,
	  se_groups.group_id AS group_id, 
	  se_groups.group_title AS group_title, 
	  se_groups.group_photo AS group_photo,
	  '' AS title,
	  se_groups.group_desc AS description,
	  '' AS id,
	  '' AS extra
	FROM 
	  se_groupvalues 
	LEFT JOIN 
	  se_groups 
	ON 
	  se_groupvalues.groupvalue_group_id=se_groups.group_id 
	WHERE 
	  se_groups.group_search='1' 
	  AND 
	  ($group_query)
	ORDER BY group_id DESC
	)
	UNION ALL
	(
	SELECT
          '2' AS sub_type,
	  se_groups.group_id AS group_id, 
	  se_groups.group_title AS group_title, 
	  se_groups.group_photo AS group_photo,
	  se_groupmedia.groupmedia_title AS title,
	  se_groupmedia.groupmedia_desc AS description,
	  se_groupmedia.groupmedia_id AS id,
	  se_groupmedia.groupmedia_ext AS extra
	FROM
	  se_groupmedia,
	  se_groupalbums,
	  se_groups
	WHERE
	  se_groupmedia.groupmedia_groupalbum_id=se_groupalbums.groupalbum_id AND
	  se_groupalbums.groupalbum_group_id=se_groups.group_id AND
	  se_groups.group_search='1'
	  AND
	  (
	    se_groupmedia.groupmedia_title LIKE '%$search_text%' OR
	    se_groupmedia.groupmedia_desc LIKE '%$search_text%'
	  )
	ORDER BY groupmedia_id DESC
	)
	UNION ALL
	(
	SELECT
          '3' AS sub_type,
	  se_groups.group_id AS group_id, 
	  se_groups.group_title AS group_title, 
	  se_groups.group_photo AS group_photo,
	  se_grouptopics.grouptopic_subject AS title,
	  se_groupposts.grouppost_body AS description,
	  se_grouptopics.grouptopic_id AS id,
	  se_groupposts.grouppost_id AS extra
	FROM
	  se_groupposts,
	  se_grouptopics,
	  se_groups
	WHERE
	  se_groupposts.grouppost_grouptopic_id=se_grouptopics.grouptopic_id AND
	  se_grouptopics.grouptopic_group_id=se_groups.group_id AND
	  se_groups.group_search='1'
	  AND
	  (
	    se_groupposts.grouppost_body LIKE '%$search_text%'
	  )
	ORDER BY grouppost_id DESC
	)";

	// GET TOTAL GROUP RESULTS
	$total_groups = $database->database_num_rows($database->database_query($group_query." LIMIT 201"));

	// IF NOT TOTAL ONLY
	if($t == "group") {

	  // MAKE GROUP PAGES
	  $start = ($p - 1) * $results_per_page;
	  $limit = $results_per_page+1;

	  // SEARCH GROUPS
	  $groups = $database->database_query($group_query." LIMIT $start, $limit");
	  while($group_info = $database->database_fetch_assoc($groups)) {

	    // SET UP GROUP
	    $group = new se_group();
	    $group->group_info['group_id'] = $group_info['group_id'];
	    $group->group_info['group_photo'] = $group_info['group_photo'];
	    $thumb_path = $group->group_photo('./images/nophoto.gif', TRUE);

	    // IF DESCRIPTION IS LONG
	    if(strlen($group_info['description']) > 150) { $group_info['description'] = substr($group_info['description'], 0, 147)."..."; }
	    if(strlen($group_info['group_desc']) > 150) { $group_info['group_desc'] = substr($group_info['group_desc'], 0, 147)."..."; }

	    // RESULT IS A GROUP
	    if($group_info[sub_type] == 1)
      {
	      $result_url = $url->url_create('group', NULL, $group_info['group_id']);
	      $result_name = 2000292;
	      $result_name_1 = $group_info['group_title'];
	      $result_desc = 2000295;
	      $result_desc_1 = $group_info['description'];
      }
      
	    // RESULT IS A PHOTO
	    elseif($group_info['sub_type'] == 2)
      {
	      $result_url = $url->url_create('group_media', NULL, $group_info['group_id'], $group_info['id']);
	      $result_name = 2000293;
	      $result_name_1 = $group_info['title'];
	      $result_desc = 2000296;
	      $result_desc_1 = $url->url_create('group', NULL, $group_info['group_id']);
	      $result_desc_2 = $group_info['group_title'];
	      $result_desc_3 = $group_info['description'];

	      // SET THUMBNAIL, IF AVAILABLE
	      switch($group_info['extra'])
        {
          case "jpeg": case "jpg": case "gif": case "png": case "bmp":
            $thumb_path = $group->group_dir($group->group_info['group_id']).$group_info['id']."_thumb.jpg";
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
      
	    // RESULT IS A DISCUSSION POST
	    else
      {
	      $result_url = $url->url_create('group_discussion_post', NULL, $group_info['group_id'], $group_info['id'], $group_info['extra']);
	      $result_name = 2000294;
	      $result_name_1 = $group_info['title'];
	      $result_desc = 2000297;
	      $result_desc_1 = $url->url_create('group', NULL, $group_info['group_id']);
	      $result_desc_2 = $group_info['group_title'];
	      $result_desc_3 = $group_info['description'];
	    }
      
	    $results[] = Array(
        'result_url' => $result_url,
				'result_icon' => $thumb_path,
				'result_name' => $result_name,
				'result_name_1' => $result_name_1,
				'result_desc' => $result_desc,
				'result_desc_1' => $result_desc_1,
				'result_desc_2' => $result_desc_2,
				'result_desc_3' => $result_desc_3
      );
	  }

	  // SET TOTAL RESULTS
	  $total_results = $total_groups;

	}

	// SET ARRAY VALUES
	SE_Language::_preload_multi(2000291, 2000292, 2000293, 2000294, 2000295, 2000296, 2000297);
	if($total_groups > 200) { $total_groups = "200+"; }
	$search_objects[] = Array(
    'search_type' => 'group',
    'search_lang' => 2000291,
    'search_total' => $total_groups
  );
}

// END search_group() FUNCTION









// THIS FUNCTION IS RUN WHEN RETRIEVING PHOTOS OF A USER
// INPUT: 
// OUTPUT: 
function mediatag_group()
{
	global $photo_query, $tag_query, $owner, $user;

	if($photo_query != "") { $photo_query .= " UNION ALL "; }

	$photo_query .= "(SELECT 'groupmedia'			AS type,
				'groupmedia_id'			AS type_id,
				'group'				AS type_prefix,
				groupmediatag_groupmedia_id 	AS media_id,
				groupmediatag_date		AS mediatag_date,
				'group.php?group_id=[media_parent_id]&v=photos' AS media_parent_url,
				group_id			AS media_parent_id,
				group_title			AS media_parent_title,
				group_user_id			AS owner_user_id,
				0				AS user_id,
				''				AS user_username,
				''				AS user_fname,
				''				AS user_lname,
				CONCAT('./uploads_group/', group_id+999-((group_id-1)%1000), '/', group_id, '/') AS media_dir,
				groupmedia_date			AS media_date,
				groupmedia_title		AS media_title,
				groupmedia_desc			AS media_desc,
				groupmedia_ext			AS media_ext,
				groupmedia_filesize		AS media_filesize,
				CASE
				  WHEN ((se_groupalbums.groupalbum_tag & 64) AND '{$user->user_exists}'<>0)
				    THEN TRUE
				  WHEN ((se_groupalbums.groupalbum_tag & 128) AND '{$user->user_exists}'=0)
				    THEN TRUE
				  WHEN ((se_groupalbums.groupalbum_tag & 1) AND '{$user->user_exists}'<>0 AND group_user_id='{$user->user_info['user_id']}')
				    THEN TRUE
				  WHEN ((se_groupalbums.groupalbum_tag & 2) AND (SELECT TRUE FROM se_groupmembers WHERE groupmember_group_id=se_groupalbums.groupalbum_group_id AND groupmember_user_id='{$user->user_info['user_id']}' AND groupmember_status='1' AND groupmember_rank='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_groupalbums.groupalbum_tag & 4) AND (SELECT TRUE FROM se_groupmembers WHERE groupmember_group_id=se_groupalbums.groupalbum_group_id AND groupmember_user_id='{$user->user_info['user_id']}' AND groupmember_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_groupalbums.groupalbum_tag & 8) AND (SELECT TRUE FROM se_friends JOIN se_groups AS t1 ON se_friends.friend_user_id1=t1.group_user_id WHERE t1.group_id=se_groupalbums.groupalbum_group_id && friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_groupalbums.groupalbum_tag & 16) AND (SELECT TRUE FROM se_friends JOIN se_groupmembers ON se_friends.friend_user_id1=se_groupmembers.groupmember_user_id WHERE se_groupmembers.groupmember_group_id=se_groupalbums.groupalbum_group_id && groupmember_status='1' AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_groupalbums.groupalbum_tag & 32) AND (SELECT TRUE FROM se_groupmembers LEFT JOIN se_friends AS friends_primary ON se_groupmembers.groupmember_user_id=friends_primary.friend_user_id1 LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE groupmember_status='1' AND groupmember_group_id=se_groupalbums.groupalbum_group_id AND friends_primary.friend_status='1' AND friends_secondary.friend_status='1' AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' LIMIT 1))
				    THEN TRUE
				  ELSE FALSE
				END
				AS allowed_to_tag,
				CASE
				  WHEN ((se_groups.group_comments & 64) AND '{$user->user_exists}'<>0)
				    THEN TRUE
				  WHEN ((se_groups.group_comments & 128) AND '{$user->user_exists}'=0)
				    THEN TRUE
				  WHEN ((se_groups.group_comments & 1) AND '{$user->user_exists}'<>0 AND group_user_id='{$user->user_info['user_id']}')
				    THEN TRUE
				  WHEN ((se_groups.group_comments & 2) AND (SELECT TRUE FROM se_groupmembers WHERE groupmember_group_id=se_groups.group_id AND groupmember_user_id='{$user->user_info['user_id']}' AND groupmember_status='1' AND groupmember_rank='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_groups.group_comments & 4) AND (SELECT TRUE FROM se_groupmembers WHERE groupmember_group_id=se_groups.group_id AND groupmember_user_id='{$user->user_info['user_id']}' AND groupmember_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_groups.group_comments & 8) AND (SELECT TRUE FROM se_friends JOIN se_groups AS t1 ON se_friends.friend_user_id1=t1.group_user_id WHERE t1.group_id=se_groups.group_id && friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_groups.group_comments & 16) AND (SELECT TRUE FROM se_friends JOIN se_groupmembers ON se_friends.friend_user_id1=se_groupmembers.groupmember_user_id WHERE se_groupmembers.groupmember_group_id=se_groups.group_id && groupmember_status='1' AND friend_user_id2='{$user->user_info['user_id']}' AND friend_status='1' LIMIT 1))
				    THEN TRUE
				  WHEN ((se_groups.group_comments & 32) AND (SELECT TRUE FROM se_groupmembers LEFT JOIN se_friends AS friends_primary ON se_groupmembers.groupmember_user_id=friends_primary.friend_user_id1 LEFT JOIN se_friends AS friends_secondary ON friends_primary.friend_user_id2=friends_secondary.friend_user_id1 WHERE groupmember_status='1' AND groupmember_group_id=se_groups.group_id AND friends_primary.friend_status='1' AND friends_secondary.friend_status='1' AND friends_secondary.friend_user_id2='{$user->user_info['user_id']}' LIMIT 1))
				    THEN TRUE
				  ELSE FALSE
				END
				AS allowed_to_comment
			FROM se_groupmediatags
			LEFT JOIN se_groupmedia
				ON se_groupmediatags.groupmediatag_groupmedia_id=se_groupmedia.groupmedia_id
			LEFT JOIN se_groupalbums
				ON se_groupmedia.groupmedia_groupalbum_id=se_groupalbums.groupalbum_id
			LEFT JOIN se_groups
				ON se_groupalbums.groupalbum_group_id=se_groups.group_id
			WHERE se_groupmediatags.groupmediatag_user_id='{$owner->user_info['user_id']}'
			)";

	$tag_query['groupmedia'] = "SELECT groupmediatag_id AS mediatag_id, groupmediatag_groupmedia_id AS mediatag_media_id, groupmediatag_user_id AS mediatag_user_id, groupmediatag_x AS mediatag_x, groupmediatag_y AS mediatag_y, groupmediatag_height AS mediatag_height, groupmediatag_width AS mediatag_width, groupmediatag_text AS mediatag_text, groupmediatag_date AS mediatag_date, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_groupmediatags LEFT JOIN se_users ON se_groupmediatags.groupmediatag_user_id=se_users.user_id WHERE groupmediatag_groupmedia_id='[media_id]' ORDER BY groupmediatag_id ASC";

} // END mediatag_group() FUNCTION









// THIS FUNCTION IS RUN WHEN A USER IS DELETED
// INPUT: $user_id REPRESENTING THE USER ID OF THE USER BEING DELETED
// OUTPUT: 
function deleteuser_group($user_id)
{
	global $database;

	// INITATE GROUP OBJECT
	$group = new se_group($user_id);

	// LOOP OVER GROUPS AND DELETE THEM
	$groups = $database->database_query("SELECT group_id FROM se_groups WHERE group_user_id='{$user_id}'");
	while($group_info = $database->database_fetch_assoc($groups)) {
	  $group->group_delete($group_info['group_id']);
	}

	// DELETE USER FROM ALL GROUPS
	$database->database_query("DELETE FROM se_groupmembers WHERE groupmember_user_id='{$user_id}'");
	$database->database_query("DELETE FROM se_groupsubscribes WHERE groupsubscribe_user_id='{$user_id}'");
	$database->database_query("UPDATE se_groupmediatags SET groupmediatag_user_id='0' WHERE groupmediatag_user_id='{$user_id}'");
}

// END deleteuser_group() FUNCTION









// THIS FUNCTION RETURNS TEXT CORRESPONDING TO THE GIVEN GROUP PRIVACY LEVEL
// INPUT: $privacy_level REPRESENTING THE LEVEL OF GROUP PRIVACY
// OUTPUT: A STRING EXPLAINING THE GIVEN PRIVACY SETTING
function group_privacy_levels($privacy_level)
{
	global $functions_group;

	switch($privacy_level)
  {
	  case 255: $privacy = 323; break;
	  case 127: $privacy = 324; break;
	  case 63: $privacy = 2000014; break;
	  case 31: $privacy = 2000015; break;
	  case 15: $privacy = 2000016; break;
	  case 7: $privacy = 2000017; break;
	  case 3: $privacy = 2000152; break;
	  case 1: $privacy = 2000018; break;
	  case 0: $privacy = 329; break;
	  default: $privacy = ""; break;
	}

	return $privacy;
}

// END group_privacy_levels() FUNCTION









// THIS FUNCTION IS RUN WHEN GENERATING SITE STATISTICS
// INPUT: 
// OUTPUT: 
function site_statistics_group(&$args)
{
  global $database;
  
  $statistics =& $args['statistics'];
  
  // NOTE: CACHING WILL BE HANDLED BY THE FUNCTION THAT CALLS THIS
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(group_id) AS total FROM se_groups"));
  $statistics['groups'] = array(
    'title' => 2000338,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  /*
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(groupcomment_id) AS total FROM se_groupcomments"));
  $statistics['groupcomments'] = array(
    'title' => 2000339,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(grouptopic_id) AS total FROM se_grouptopics"));
  $statistics['grouptopics'] = array(
    'title' => 2000340,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(grouppost_id) AS total FROM se_groupposts"));
  $statistics['groupposts'] = array(
    'title' => 2000341,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(groupmedia_id) AS total FROM se_groupmedia"));
  $statistics['groupmedia'] = array(
    'title' => 2000342,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(groupmember_id) AS total FROM se_groupmembers WHERE groupmember_approved=1 && groupmember_status=1"));
  $statistics['groupmembers'] = array(
    'title' => 2000343,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  */
}

// END site_statistics_group() FUNCTION

?>