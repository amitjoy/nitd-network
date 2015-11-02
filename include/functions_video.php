<?php

//
//  THIS FILE CONTAINS VIDEO-RELATED FUNCTIONS
//
//  FUNCTIONS IN THIS CLASS:
//    video_manage_jobs()
//    search_video()
//    deleteuser_video()
//    site_statistics_video()
//    extract_youtube_code()
//

defined('SE_PAGE') or exit();









// THIS FUNCTION TRIGGERS THE JOB QUEUE TO RUN
// INPUT: 
// OUTPUT: 
function video_manage_jobs()
{
	global $database, $setting, $actions, $misc, $url;

	// SET JOB COUNTER VAR
	$job_counter = 0;
	$video = new se_video();

	// GET COMPLETED JOBS AND LOOP OVER THEM
	$dir = getcwd().'/uploads_video/encoding/queue';
	if( !($handle = opendir($dir)) )
  {
    return FALSE;
  }
  
  while(($file = readdir($handle)) !== false)
  {
    if( $file=='.' || $file=='..' )
    {
      continue;
    }
      
    if( !strpos($file, '_complete') )
    {
      $job_counter++;
      continue;
    }
    
    
    $_file = explode('_', $file);
    $path = htmlspecialchars(strip_tags($_file[0]));
    $video_id = $_file[2];
    $duration = $_file[3];
    
    // CALCULATE DURATION OF VIDEO IN SECONDS
    $duration = explode(':', $duration);
    $hours = $duration[0] * 3600;
    $minutes = $duration[1] * 60;
    $seconds = $duration[2] * 1;
    $duration = ceil($seconds + $minutes + $hours);
    
    // SET MINIMUM ALLOWED DURATION
    $min_duration = 1;
    
    // DELETE VIDEO IF ENCODING WAS SCREWED UP
    if(empty($duration) || $duration <= $min_duration)
    {
      // GET USER ID
      $user_info = $database->database_fetch_assoc($database->database_query("SELECT video_user_id FROM se_videos WHERE video_id='".htmlspecialchars(strip_tags($video_id))."' LIMIT 1"));
      
      $database->database_query("UPDATE se_videos SET video_is_converted='-1' WHERE video_id='".htmlspecialchars(strip_tags($video_id))."'");
      
      $directory = getcwd().substr($video->video_dir($user_info[video_user_id]), 1);
      
      unlink($directory.$path.'.flv');
      unlink($directory.$path.'_thumb.jpg');
    }
    
    // VIDEO WAS ENCODED SUCCESSFULLY, UPDATE DATABASE
    else
    {
      $database->database_query("UPDATE se_videos SET video_is_converted=1, video_duration_in_sec=$duration WHERE video_id='".htmlspecialchars(strip_tags($video_id))."' LIMIT 1");
      
      $video_info = $database->database_fetch_assoc($database->database_query("SELECT user_id, user_username, user_fname, user_lname, user_privacy, video_id, video_title FROM se_videos LEFT JOIN se_users ON se_videos.video_user_id=se_users.user_id WHERE se_videos.video_id='".htmlspecialchars(strip_tags($video_id))."' LIMIT 1"));
      
      $author = new se_user();
      $author->user_exists = 1;
      $author->user_info['user_id'] = $video_info['user_id'];
      $author->user_info['user_username'] = $video_info['user_username'];
      $author->user_info['user_fname'] = $video_info['user_fname'];
      $author->user_info['user_lname'] = $video_info['user_lname'];
      $author->user_info['user_privacy'] = $video_info['user_privacy'];
      $author->user_displayname();
      
      $action_media = array();
      $thumb_path = str_replace('./', '', $video->video_dir($video_info['user_id']).$video_info['video_id']."_thumb.jpg");
      $media_link = str_replace($url->url_base, '', $url->url_create('video', $author->user_info['user_username'], $video_info['video_id']));
      
      if( file_exists($thumb_path) )
      {
        $media_width = $misc->photo_size($thumb_path, "100", "100", "w");
        $media_height = $misc->photo_size($thumb_path, "100", "100", "h");
        $action_media[] = array(
          'media_link' => $media_link,
          'media_path' => $thumb_path,
          'media_width' => $media_width,
          'media_height' => $media_height
        );
      }
      
      // INSERT ACTION
      if(strlen($video_info[video_title]) > 100) { $video_info[video_title] = substr($video_info['video_title'], 0, 97); $video_info['video_title'] .= "..."; }
      $actions->actions_add($author, "newvideo", array($author->user_info['user_username'], $author->user_displayname, $video_info['video_id'], $video_info['video_title']), $action_media, 0, false, "user", $author->user_info['user_id'], $author->user_info['user_privacy']);
    }
    
    // IF NOT DEBUG, REMOVE SHELL SCRIPT, OTHERWISE MOVE TO DEBUG FOLDER
    if( !$video->debug )
    {
      unlink(getcwd().'/uploads_video/encoding/queue/'.$file);
      unlink(getcwd().'/uploads_video/encoding/jobs/'.$path);
    }
    else
    {
      rename(getcwd().'/uploads_video/encoding/queue/'.$file, getcwd().'/uploads_video/encoding/debug/'.$file);
      rename(getcwd().'/uploads_video/encoding/jobs/'.$path, getcwd().'/uploads_video/encoding/debug/'.$path);
    }
    
  }
  closedir($handle);

	// START NEW JOBS
	if( $job_counter <= $setting['setting_video_max_jobs'] )
  {
	  $dir = getcwd().'/uploads_video/encoding/jobs';
	  if( !($handle = opendir($dir)) )
    {
      return FALSE;
    }
    
    while(($file = readdir($handle)) !== false && $job_counter <= $setting['setting_video_max_jobs'])
    {
      if( $file=='.' || $file=='..' )
      {
        continue;
      }
      
      $cwd = getcwd();
      if( file_exists("{$cwd}/uploads_video/encoding/queue/{$file}") )
      {
        continue;
      }
      
      // IF NOT DEBUG, START NORMALLY
      if( !$video->debug )
      {
        exec("{$cwd}/uploads_video/encoding/jobs/{$file} > /dev/null &");
      }
      
      // OTHERWISE SHUFFLE OUTPUT TO FILE
      else
      {
        exec("{$cwd}/uploads_video/encoding/jobs/{$file} 3>&1 2>&1 > {$cwd}/uploads_video/encoding/debug/{$file}.log &");
      }
      $job_counter++;
    }
    
    closedir($handle);
	}
}

// END video_manage_jobs() FUNCTION









// THIS FUNCTION IS RUN DURING THE SEARCH PROCESS TO SEARCH THROUGH VIDEOS
// INPUT: 
// OUTPUT: 
function search_video()
{
	global $database, $url, $results_per_page, $p, $search_text, $t, $search_objects, $results, $total_results;

	// CONSTRUCT QUERY
	$video_query = "SELECT 
			  se_videos.*, 
			  se_users.user_id, 
			  se_users.user_username,
			  se_users.user_photo,
			  se_users.user_fname,
			  se_users.user_lname
			FROM
			  se_videos,
			  se_users,
			  se_levels
			WHERE
			  se_videos.video_user_id=se_users.user_id AND
			  se_users.user_level_id=se_levels.level_id AND
			  (
			    se_videos.video_search='1' OR
			    se_levels.level_video_search='0'
			  )
			  AND
			  (
			    se_videos.video_title LIKE '%$search_text%' OR
			    se_videos.video_desc LIKE '%$search_text%'
			  )"; 

	// GET TOTAL RESULTS
	$total_videos = $database->database_num_rows($database->database_query($video_query." LIMIT 201"));

	// IF NOT TOTAL ONLY
	if($t == "video") {

	  // MAKE VIDEO PAGES
	  $start = ($p - 1) * $results_per_page;
	  $limit = $results_per_page+1;

	  // SEARCH VIDEOS
	  $video = new se_video();
	  $videos = $database->database_query($video_query." ORDER BY video_id DESC LIMIT $start, $limit");
	  while($video_info = $database->database_fetch_assoc($videos)) {

	    // CREATE AN OBJECT FOR USER
	    $profile = new se_user();
	    $profile->user_info[user_id] = $video_info[user_id];
	    $profile->user_info[user_username] = $video_info[user_username];
	    $profile->user_info[user_fname] = $video_info[user_fname];
	    $profile->user_info[user_lname] = $video_info[user_lname];
	    $profile->user_info[user_photo] = $video_info[user_photo];
	    $profile->user_displayname();

	    // SET RESULT VARS
	    $result_url = $url->url_create("video", $video_info[user_username], $video_info[video_id]);
	    $result_name = 5500141;
	    $result_desc = 5500142;

	    // SET DIRECTORY
	    $video_info[video_dir] = $video->video_dir($video_info[user_id]);
	
	    // CHECK FOR THUMBNAIL
	    $thumb_path = $video_info[video_dir].$video_info[video_id]."_thumb.jpg";
	    if(!file_exists($thumb_path)) { $video_info[video_thumb] = "./images/video_placeholder.gif"; }

	    // IF NO TITLE
	    if($video_info[video_title] == "") { $video_info[video_title] = SE_Language::get(589); }

	    // IF DESCRIPTION IS LONG
	    if(strlen($video_info[video_desc]) > 150) { $video_info[video_desc] = substr($video_info[video_desc], 0, 147)."..."; }

	    $results[] = Array('result_url' => $result_url,
				'result_icon' => $thumb_path,
				'result_name' => $result_name,
				'result_name_1' => $video_info[video_title],
				'result_desc' => $result_desc,
				'result_desc_1' => $url->url_create('profile', $video_info[user_username]),
				'result_desc_2' => $profile->user_displayname,
				'result_desc_3' => $video_info[video_desc]);
	  }

	  // SET TOTAL RESULTS
	  $total_results = $total_videos;

	}

	// SET ARRAY VALUES
	SE_Language::_preload_multi(5500141, 5500142, 5500143);
	if($total_videos > 200) { $total_videos = "200+"; }
	$search_objects[] = Array('search_type' => 'video',
				'search_lang' => 5500143,
				'search_total' => $total_videos);


} // END search_video() FUNCTION









// THIS FUNCTION IS RUN WHEN A USER IS DELETED
// INPUT: $user_id REPRESENTING THE USER ID OF THE USER BEING DELETED
// OUTPUT: 

function deleteuser_video($user_id)
{
	global $database;

	// GET VIDEO OBJECT
	$video = new se_video($user_id);

	// DELETE VIDEOS, COMMENTS, AND RATINGS
	$database->database_query("DELETE FROM se_videos, se_videocomments, se_videoratings USING se_videos LEFT JOIN se_videocomments ON se_videos.video_id=se_videocomments.videocomments_video_id LEFT JOIN se_videoratings ON se_videos.video_id=se_videoratings.videorating_video_id WHERE se_videos.video_user_id='$user_id'");

	// DELETE USER'S FILES
	if(is_dir($video->video_dir())) {
	  $dir = $video->video_dir();
	} else {
	  $dir = ".".$video->video_dir();
	}
  
	if($dh = @opendir($dir))
  {
	  while( ($file = @readdir($dh)) !== false )
    {
	    if( $file != "." && $file != ".." )
      {
	      @unlink($dir.$file);
	    }
	  }
	  @closedir($dh);
	}
	@rmdir($dir);
}

// END deleteuser_video() FUNCTION









// THIS FUNCTION IS RUN WHEN GENERATING SITE STATISTICS
// INPUT: 
// OUTPUT: 
function site_statistics_video(&$args)
{
  global $database;
  
  $statistics =& $args['statistics'];
  
  // NOTE: CACHING WILL BE HANDLED BY THE FUNCTION THAT CALLS THIS
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(video_id) AS total FROM se_videos WHERE video_is_converted=1"));
  $statistics['videos'] = array(
    'title' => 5500175,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  /*
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(videocomment_id) AS total FROM se_videocomments"));
  $statistics['videocomments'] = array(
    'title' => 5500176,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  
  $total = $database->database_fetch_assoc($database->database_query("SELECT COUNT(videorating_id) AS total FROM se_videoratings"));
  $statistics['videoratings'] = array(
    'title' => 5500177,
    'stat'  => (int) ( isset($total['total']) ? $total['total'] : 0 )
  );
  */
}

// END site_statistics_video() FUNCTION







function extract_youtube_code($video_url)
{
  $video_code_start = strpos($video_url, "v=");
  if ($video_code_start === FALSE) { 
    $is_error = 5500189;
    return FALSE;
  }
  $video_code = substr($video_url, $video_code_start + 2);
  if (empty($video_code)) 
  {
    return FALSE;
  }
  $video_code_end = strpos($video_code, '&');
  if ($video_code_end) {
    $video_code = substr($video_code, 0, $video_code_end);
  }

  return $video_code;
}

?>