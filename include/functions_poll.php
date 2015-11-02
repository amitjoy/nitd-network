<?php

//  THIS FILE CONTAINS POLL-RELATED FUNCTIONS
//
//  FUNCTIONS IN THIS CLASS:
//    search_poll()
//    deleteuser_poll()
//    site_statistics_poll()
//


defined('SE_PAGE') or exit();












// THIS FUNCTION IS RUN DURING THE SEARCH PROCESS TO SEARCH THROUGH POLLS
// INPUT: $search_text REPRESENTING THE STRING TO SEARCH FOR
//	  $total_only REPRESENTING 1/0 DEPENDING ON WHETHER OR NOT TO RETURN JUST THE TOTAL RESULTS
//	  $search_objects REPRESENTING AN ARRAY CONTAINING INFORMATION ABOUT THE POSSIBLE OBJECTS TO SEARCH
//	  $results REPRESENTING THE ARRAY OF SEARCH RESULTS
//	  $total_results REPRESENTING THE TOTAL SEARCH RESULTS
// OUTPUT: 
function search_poll()
{
	global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results;

	// CONSTRUCT QUERY
	$sql = "
    SELECT
      se_polls.poll_id,
      se_polls.poll_title,
      se_users.user_id,
      se_users.user_username,
      se_users.user_photo,
      se_users.user_fname,
      se_users.user_lname
    FROM
      se_polls,
      se_users,
      se_levels
    WHERE
      se_polls.poll_user_id=se_users.user_id &&
      se_users.user_level_id=se_levels.level_id &&
      (
        se_polls.poll_search='1' ||
        se_levels.level_poll_search='0'
      ) &&
      (
        poll_title LIKE '%$search_text%' ||
        poll_desc LIKE '%$search_text%' ||
        poll_options LIKE '%$search_text%'
      )
  ";
  
	// GET TOTAL ENTRIES
	$total_polls = $database->database_num_rows($database->database_query($sql." LIMIT 201"));

	// IF NOT TOTAL ONLY
	if( $t=="poll" )
  {
	  // MAKE POLL PAGES
	  $start = ($p - 1) * $results_per_page;
	  $limit = $results_per_page + 1;
    
	  // SEARCH POLLS
    $sql .= " ORDER BY se_polls.poll_id DESC LIMIT $start, $limit";
	  $resource = $database->database_query($sql) or die($database->database_error());
    
	  while( $poll_info=$database->database_fetch_assoc($resource) )
    {
	    // CREATE AN OBJECT FOR AUTHOR
	    $profile = new se_user();
	    $profile->user_info['user_id']        = $poll_info['user_id'];
	    $profile->user_info['user_username']  = $poll_info['user_username'];
	    $profile->user_info['user_fname']     = $poll_info['user_fname'];
	    $profile->user_info['user_lname']     = $poll_info['user_lname'];
	    $profile->user_info['user_photo']     = $poll_info['user_photo'];
	    $profile->user_displayname();
      
      $result_url = $url->url_create('poll', $poll_info['user_username'], $poll_info['poll_id']);
      $result_name = 2500112;
      $result_desc = 2500113;
      
	    // IF EMPTY TITLE
	    if( !trim($poll_info['poll_title']) ) { SE_Language::_preload(589); SE_Language::load(); $poll_info['poll_title'] = SE_Language::_get(589); }
      
	    $results[] = array(
        'result_url'    => $result_url,
				'result_icon'   => './images/icons/poll_poll48.gif',
				'result_name'   => $result_name,
				'result_name_1' => $poll_info['poll_title'],
				'result_desc'   => $result_desc,
				'result_desc_1' => $url->url_create('profile', $profile->user_info['user_username']),
				'result_desc_2' => $profile->user_displayname,
				'result_desc_3' => $poll_info['poll_desc'],
      );
	  }
    
	  // SET TOTAL RESULTS
	  $total_results = $total_polls;
	}

	// SET ARRAY VALUES
	SE_Language::_preload_multi(2500111, 2500112, 2500113);
	if($total_polls > 200) { $total_polls = "200+"; }
  
	$search_objects[] = array(
    'search_type' => 'poll',
    'search_lang' => 2500111,
    'search_total' => $total_polls
  );
}
// END search_poll() FUNCTION









// THIS FUNCTION IS RUN WHEN A USER IS DELETED
// INPUT: $user_id REPRESENTING THE USER ID OF THE USER BEING DELETED
// OUTPUT: 
function deleteuser_poll($user_id)
{
	global $database;

	// DELETE poll ENTRIES AND COMMENTS
	$database->database_query("DELETE FROM se_polls, se_pollcomments USING se_polls LEFT JOIN se_pollcomments ON se_polls.poll_id=se_pollcomments.pollcomment_poll_id WHERE se_polls.poll_user_id='$user_id'");

	// DELETE COMMENTS POSTED BY USER
	$database->database_query("DELETE FROM se_pollcomments WHERE pollcomment_authoruser_id='$user_id'");

}
// END deleteuser_poll() FUNCTION









// THIS FUNCTION IS RUN WHEN GENERATING SITE STATISTICS
// INPUT: 
// OUTPUT: 
function site_statistics_poll(&$args)
{
  global $database;
  
  $statistics =& $args['statistics'];
  
  // NOTE: CACHING WILL BE HANDLED BY THE FUNCTION THAT CALLS THIS
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(poll_id) AS total FROM se_polls"));
  $statistics['polls'] = array(
    'title' => 2500128,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  /*
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(pollcomment_id) AS total FROM se_pollcomments"));
  $statistics['pollcomments'] = array(
    'title' => 2500129,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT SUM(poll_totalvotes) AS total FROM se_polls"));
  $statistics['pollvotes'] = array(
    'title' => 2500130,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  */
}

// END site_statistics_poll() FUNCTION


?>