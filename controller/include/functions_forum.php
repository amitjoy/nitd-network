<?php

//
//  THIS FILE CONTAINS FORUM-RELATED FUNCTIONS
//
//  FUNCTIONS IN THIS CLASS:
//    action_privacy_forum()
//    search_forum()
//    deleteuser_forum()
//    site_statistics_forum()
//


defined('SE_PAGE') or exit();









// THIS FUNCTION ADDS A CLAUSE IN ACTION QUERY TO ACCOUNT FOR FORUM PRIVACY
// INPUT: 
// OUTPUT: 
function action_privacy_forum($args) {
	global $user, $database;

	// SET LEVEL ID
	if($user->user_exists) { $level_id = $user->level_info[level_id]; } else { $level_id = 0; }

	// RETRIEVE A LIST OF FORUMS USER IS ALLOWED TO VIEW
	$forum_ids = Array();
	$forums = $database->database_query("SELECT se_forums.forum_id FROM se_forums LEFT JOIN se_forumlevels ON se_forums.forum_id=se_forumlevels.forumlevel_forum_id AND se_forumlevels.forumlevel_level_id='$level_id' LEFT JOIN se_forummoderators ON se_forums.forum_id=se_forummoderators.forummoderator_forum_id AND se_forummoderators.forummoderator_user_id='{$user->user_info[user_id]}' WHERE se_forumlevels.forumlevel_forum_id IS NOT NULL OR se_forummoderators.forummoderator_forum_id IS NOT NULL");
	while($forum_info = $database->database_fetch_assoc($forums)) {
	  $forum_ids[] = $forum_info[forum_id];
	}

	$args['actions_query'] .= " WHEN se_actions.action_object_owner='forum' THEN
				CASE
				  WHEN (se_actions.action_object_owner_id IN ('".implode("', '", $forum_ids)."'))
				    THEN TRUE
				  ELSE FALSE
				END";

} // END action_privacy_forum() FUNCTION









// THIS FUNCTION IS RUN DURING THE SEARCH PROCESS TO SEARCH THROUGH FORUM TOPICS
// INPUT: 
// OUTPUT: 
function search_forum() {
	global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results;


	// SET LEVEL ID
	if($user->user_exists) { $level_id = $user->level_info[level_id]; } else { $level_id = 0; }

	// RETRIEVE A LIST OF FORUMS USER IS ALLOWED TO VIEW
	$forum_ids = Array();
	$forums = $database->database_query("SELECT se_forums.forum_id FROM se_forums LEFT JOIN se_forumlevels ON se_forums.forum_id=se_forumlevels.forumlevel_forum_id AND se_forumlevels.forumlevel_level_id='$level_id' LEFT JOIN se_forummoderators ON se_forums.forum_id=se_forummoderators.forummoderator_forum_id AND se_forummoderators.forummoderator_user_id='{$user->user_info[user_id]}' WHERE se_forumlevels.forumlevel_forum_id IS NOT NULL OR se_forummoderators.forummoderator_forum_id IS NOT NULL");
	while($forum_info = $database->database_fetch_assoc($forums)) {
	  $forum_ids[] = $forum_info[forum_id];
	}


	// CONSTRUCT QUERY
	$forum_query = "
	(
	SELECT
	  '1' AS sub_type,
	  se_forums.forum_id AS forum_id,
	  se_forumtopics.forumtopic_id AS forumtopic_id,
	  '0' AS forumpost_id,
	  se_forums.forum_title AS forum_title,
	  se_forumtopics.forumtopic_subject AS forumtopic_subject,
	  se_forumtopics.forumtopic_excerpt AS excerpt
	FROM
	  se_forumtopics
	LEFT JOIN
	  se_forums
	ON
	  se_forumtopics.forumtopic_forum_id=se_forums.forum_id
	WHERE
	  se_forums.forum_id IN ('".implode("', '", $forum_ids)."')
	  AND
	  se_forumtopics.forumtopic_subject LIKE '%$search_text%'
	ORDER BY se_forumtopics.forumtopic_id DESC
	)
	UNION ALL
	(
	SELECT
	  '2' AS sub_type,
	  se_forumtopics.forumtopic_forum_id AS forum_id,
	  se_forumtopics.forumtopic_id AS forumtopic_id,
	  se_forumposts.forumpost_id AS forumpost_id,
	  '' AS forum_title,
	  se_forumtopics.forumtopic_subject AS forumtopic_subject,
	  se_forumposts.forumpost_excerpt AS excerpt
	FROM
	  se_forumposts
	LEFT JOIN
	  se_forumtopics
	ON
	  se_forumposts.forumpost_forumtopic_id=se_forumtopics.forumtopic_id
	WHERE
	  se_forumtopics.forumtopic_forum_id IN ('".implode("', '", $forum_ids)."')
	  AND
	  se_forumposts.forumpost_deleted='0'
	  AND
	  se_forumposts.forumpost_body LIKE '%$search_text%'
	  AND 
	  se_forumtopics.forumtopic_subject NOT LIKE '%$search_text%'
	ORDER BY se_forumposts.forumpost_id DESC
	)";

	// GET TOTAL FORUM RESULTS
	$total_forums = $database->database_num_rows($database->database_query($forum_query." LIMIT 201"));

	// IF NOT TOTAL ONLY
	if($t == "forum") {

	  // MAKE FORUM PAGES
	  $start = ($p - 1) * $results_per_page;
	  $limit = $results_per_page+1;

	  // SEARCH FORUMS
	  $posts = $database->database_query($forum_query." LIMIT $start, $limit") or die(mysql_query());
	  while($post_info = $database->database_fetch_assoc($posts)) {
	
	    $post_info['excerpt'] = $post_info['excerpt']."...";

	    // RESULT IS A TOPIC
	    if($post_info[sub_type] == 1) {
	      $result_url = "forum_topic.php?forum_id={$post_info[forum_id]}&topic_id={$post_info[forumtopic_id]}";
	      $result_name = 6000131;
	      $result_name_1 = $post_info['forumtopic_subject'];
	      $result_desc = 6000132;
	      $result_desc_1 = $post_info['forum_id'];
	      $result_desc_2 = SE_Language::get($post_info['forum_title']);
	      $result_desc_3 = ((strlen($post_info['excerpt'])>50)?substr($post_info['excerpt'], 0, 47)."...":$post_info['excerpt']);
      
	    // RESULT IS A POST
	    } elseif($post_info['sub_type'] == 2) {

	      $result_url = "forum_topic.php?forum_id={$post_info[forum_id]}&topic_id={$post_info[forumtopic_id]}&post_id={$post_info[forumpost_id]}#post_{$post_info[forumpost_id]}";
	      $result_name = 6000133;
	      $result_name_1 = $post_info['forumtopic_subject'];
	      $result_desc = 6000134;
	      $result_desc_1 = $post_info['excerpt'];

	    }
      
	    $results[] = Array(
			        'result_url' => $result_url,
				'result_icon' => 'images/icons/file_big.gif',
				'result_name' => $result_name,
				'result_name_1' => $result_name_1,
				'result_desc' => $result_desc,
				'result_desc_1' => $result_desc_1,
				'result_desc_2' => $result_desc_2,
				'result_desc_3' => $result_desc_3
	    );
	  }

	  // SET TOTAL RESULTS
	  $total_results = $total_forums;

	}

	// SET ARRAY VALUES
	SE_Language::_preload_multi(6000131, 6000132, 6000133, 6000134, 6000135);
	if($total_forums > 200) { $total_forums = "200+"; }
	$search_objects[] = Array(
   			 'search_type' => 'forum',
 			 'search_lang' => 6000135,
   			 'search_total' => $total_forums
	);

} // END search_forum() FUNCTION









// THIS FUNCTION IS RUN WHEN A USER IS DELETED
// INPUT: $user_id REPRESENTING THE USER ID OF THE USER BEING DELETED
// OUTPUT: 
function deleteuser_forum($user_id) {
	global $database;

	// DELETE FROM FORUMUSERS
	$database->database_query("DELETE FROM se_forumusers WHERE forumuser_user_id='{$user_id}'");

	// DELETE FROM FORUMMODERATORS
	$database->database_query("DELETE FROM se_forummoderators WHERE forummoderator_user_id='{$user_id}'");

	// DELETE FROM FORUM LOG
	$database->database_query("DELETE FROM se_forumlogs WHERE forumlog_user_id='{$user_id}'");

} // END deleteuser_forum() FUNCTION









// THIS FUNCTION IS RUN WHEN GENERATING SITE STATISTICS
// INPUT: 
// OUTPUT: 
function site_statistics_forum(&$args) {
	global $database;
  
	$statistics =& $args['statistics'];
  
	// NOTE: CACHING WILL BE HANDLED BY THE FUNCTION THAT CALLS THIS
  
	$total = $database->database_fetch_assoc($database->database_query("SELECT SUM(forum_totaltopics+forum_totalreplies) AS total FROM se_forums"));
	$statistics['forumtopics'] = array(
	  'title' => 6000122,
	  'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
	);

} // END site_statistics_forum() FUNCTION

?>