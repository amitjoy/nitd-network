<?php

//  THIS FILE CONTAINS BLOG-RELATED FUNCTIONS
//  FUNCTIONS IN THIS CLASS:
//
//    search_blog()
//    deleteuser_blog()
//    site_statistics_blog()
//


defined('SE_PAGE') or exit();










//
// THIS FUNCTION IS RUN DURING THE SEARCH PROCESS TO SEARCH THROUGH BLOG ENTRIES
//
// INPUT:
//
// OUTPUT: 
//

function search_blog()
{
	global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results;

	// CONSTRUCT QUERY
  $sql = "
    SELECT
      se_blogentries.blogentry_id,
      se_blogentries.blogentry_title,
      se_blogentries.blogentry_body,
      se_users.user_id,
      se_users.user_username,
      se_users.user_photo,
      se_users.user_fname,
      se_users.user_lname
    FROM
      se_blogentries,
      se_users,
      se_levels
    WHERE
      se_blogentries.blogentry_user_id=se_users.user_id &&
      se_users.user_level_id=se_levels.level_id &&
      (
        se_blogentries.blogentry_search='1' ||
        se_levels.level_blog_search='0'
      ) 
  ";
  
  $sql .= " && MATCH (`blogentry_title`, `blogentry_body`) AGAINST ('{$search_text}' IN BOOLEAN MODE)";
  
  /*
  $sql .= " && (
        blogentry_title LIKE '%$search_text%' ||
        blogentry_body LIKE '%$search_text%'
      )
  ";
  */
  
	// GET TOTAL ENTRIES
  $sql2 = $sql . " LIMIT 201";
  $resource = $database->database_query($sql2);
	$total_entries = $database->database_num_rows($resource);
  
	// IF NOT TOTAL ONLY
	if( $t=="blog" )
  {
	  // MAKE BLOG PAGES
	  $start = ($p - 1) * $results_per_page;
	  $limit = $results_per_page+1;
    
	  // SEARCH BLOGS
    $sql3 = $sql . " ORDER BY blogentry_id DESC LIMIT {$start}, {$limit}";
    $resource = $database->database_query($sql3);
    
	  while( $blogentry_info=$database->database_fetch_assoc($resource) )
    {
	    // CREATE AN OBJECT FOR AUTHOR
	    $profile = new se_user();
	    $profile->user_info['user_id']        = $blogentry_info['user_id'];
	    $profile->user_info['user_username']  = $blogentry_info['user_username'];
	    $profile->user_info['user_photo']     = $blogentry_info['user_photo'];
	    $profile->user_info['user_fname']     = $blogentry_info['user_fname'];
	    $profile->user_info['user_lname']     = $blogentry_info['user_lname'];
	    $profile->user_displayname();
      
	    // IF EMPTY TITLE
	    if( !trim($blogentry_info['blogentry_title']) )
        $blogentry_info['blogentry_title'] = SE_Language::get(589);
      
      $blogentry_info['blogentry_body'] = cleanHTML($blogentry_info['blogentry_body'], '');
      
	    // IF BODY IS LONG
	    if( strlen($blogentry_info['blogentry_body'])>150 )
        $blogentry_info['blogentry_body'] = substr($blogentry_info['blogentry_body'], 0, 147)."...";
      
      $result_url = $url->url_create('blog_entry', $blogentry_info['user_username'], $blogentry_info['blogentry_id']);
      $result_name = 1500118;
      $result_desc = 1500119;
      
	    $results[] = array(
        'result_url'    => $result_url,
				'result_icon'   => './images/icons/blog_blog48.gif',
				'result_name'   => $result_name,
				'result_name_1' => $blogentry_info['blogentry_title'],
				'result_desc'   => $result_desc,
				'result_desc_1' => $url->url_create('profile', $blogentry_info['user_username']),
				'result_desc_2' => $profile->user_displayname,
				'result_desc_3' => $blogentry_info['blogentry_body']
      );
	  }
    
	  // SET TOTAL RESULTS
	  $total_results = $total_entries;
	}

	// SET ARRAY VALUES
	SE_Language::_preload_multi(1500118, 1500119, 1500120);
	if( $total_albums>200 )
    $total_albums = "200+";
  
	$search_objects[] = array(
    'search_type'   => 'blog',
    'search_lang'   => 1500120,
    'search_total'  => $total_entries
  );
}

// END search_blog() FUNCTION












// THIS FUNCTION IS RUN WHEN A USER IS DELETED
// INPUT: $user_id REPRESENTING THE USER ID OF THE USER BEING DELETED
// OUTPUT: 
function deleteuser_blog($user_id) {
	global $database;

	// DELETE BLOG ENTRIES AND COMMENTS
	$database->database_query("DELETE FROM se_blogentries, se_blogcomments USING se_blogentries LEFT JOIN se_blogcomments ON se_blogentries.blogentry_id=se_blogcomments.blogcomment_blogentry_id WHERE se_blogentries.blogentry_user_id='$user_id'");

	// DELETE COMMENTS POSTED BY USER
	$database->database_query("DELETE FROM se_blogcomments WHERE blogcomment_authoruser_id='$user_id'");

	// DELETE STYLE
	$database->database_query("DELETE FROM se_blogstyles WHERE blogstyle_user_id='$user_id'");

} // END deleteuser_blog() FUNCTION









// THIS FUNCTION IS RUN WHEN GENERATING SITE STATISTICS
// INPUT: 
// OUTPUT: 
function site_statistics_blog(&$args)
{
  global $database;
  
  $statistics =& $args['statistics'];
  
  // NOTE: CACHING WILL BE HANDLED BY THE FUNCTION THAT CALLS THIS
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(blogentry_id) AS total FROM se_blogentries"));
  $statistics['blog'] = array(
    'title' => 1500150,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  /*
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(blogsubscription_id) AS total FROM se_blogsubscriptions"));
  $statistics['blogsubscriptions'] = array(
    'title' => 1500151,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  */
}

// END site_statistics_blog() FUNCTION

?>