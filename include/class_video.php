<?php

//  THIS CLASS CONTAINS VIDEO-RELATED METHODS 
//  METHODS IN THIS CLASS:
//    se_video()
//    video_total()
//    video_list()
//    video_dir()
//    video_edit()
//    video_upload()
//    video_delete()
//    video_delete_selected()
//    video_update_youtube_thumb()

defined('SE_PAGE') or exit();




class se_video
{
  // INITIALIZE VARIABLES
  var $is_error = NULL;            // DETERMINES WHETHER THERE IS AN ERROR OR NOT

  var $user_id = NULL;            // CONTAINS THE USER ID OF THE USER WHOSE VIDEO WE ARE EDITING
  
  var $video_exists = FALSE;      // CONTAINS TRUE IF THE VIDEO EXISTS
  
  var $video_info = array();      // CONTAINS THE INFO ABOUT THE VIDEO

  var $debug = FALSE;              // DO DEBUGGING?








  //
  // THIS METHOD SETS INITIAL VARS
  //
  // INPUT:
  //    $user_id (OPTIONAL) REPRESENTING THE USER ID OF THE USER WHOSE VIDEOS WE ARE CONCERNED WITH
  //    $video_id (OPTIONAL) REPRESENTING THE ID OF THE VIDEO WE ARE CONCERNED WITH
  //
  // OUTPUT: 
  //    void
  //
  
  function se_video($user_id=NULL, $video_id=NULL)
  {
    global $database, $user;
    
    $this->user_id = $user_id;
    
    if( $video_id )
    {
      $sql = "SELECT * FROM se_videos WHERE video_id='{$video_id}' LIMIT 1";
      $resource = $database->database_query($sql);
      
      if( $resource && $database->database_num_rows($resource) )
      {
        $this->video_exists = TRUE;
        $this->video_info = $database->database_fetch_assoc($resource);
      }
    }
  }
  
  // END se_video() METHOD








  //
  // THIS METHOD RETURNS THE TOTAL NUMBER OF VIDEOS
  //
  // INPUT:
  //    $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
  //
  // OUTPUT:
  //    AN INTEGER REPRESENTING THE NUMBER OF VIDEOS
  //
  
  function video_total($where = "")
  {
    global $database;

    // BEGIN VIDEO QUERY
    $video_query = "SELECT video_id FROM se_videos";
    
    // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
    if($this->user_id == 0) { $video_query .= " LEFT JOIN se_users ON se_videos.video_user_id=se_users.user_id"; }
    
    // ADD WHERE IF NECESSARY
    if($where != "" || $this->user_id != 0) { $video_query .= " WHERE"; }
    
    // ENSURE USER ID IS NOT EMPTY
    if($this->user_id != 0) { $video_query .= " video_user_id='{$this->user_id}'"; }
    
    // INSERT AND IF NECESSARY
    if($this->user_id != 0 && $where != "") { $video_query .= " AND"; }
    
    // ADD WHERE CLAUSE, IF NECESSARY
    if($where != "") { $video_query .= " $where"; }
    
    // GET AND RETURN TOTAL VIDEOS
    $video_total = $database->database_num_rows($database->database_query($video_query));
    return $video_total;
  }
  
  // END video_total() METHOD








  //
  // THIS METHOD RETURNS AN ARRAY OF VIDEOS
  //
  // INPUT:
  //    $start REPRESENTING THE VIDEO TO START WITH
  //    $limit REPRESENTING THE NUMBER OF VIDEOS TO RETURN
  //    $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
  //    $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
  //    $details (OPTIONAL) REPRESENTING WHETHER TO GET DETAILS LIKE TOTAL COMMENTS
  //
  // OUTPUT:
  //    AN ARRAY OF VIDEOS
  //
  
  function video_list($start, $limit, $sort_by = "video_id DESC", $where = "", $details = 0)
  {
    global $database, $user, $owner;
    
    // BEGIN QUERY
    $video_query = "SELECT se_videos.*";
    
    // GET DETAILS
    if($details) { $video_query .= ", count(se_videocomments.videocomment_id) AS total_comments"; }
    
    // IF NO USER ID SPECIFIED, RETRIEVE USER INFORMATION
    if($this->user_id == 0) { $video_query .= ", se_users.user_id, se_users.user_username, se_users.user_photo, se_users.user_fname, se_users.user_lname"; }
    
    // CONTINUE QUERY
    $video_query .= " FROM se_videos";
    
    // GET DETAILS
    if($details) { $video_query .= " LEFT JOIN se_videocomments ON se_videos.video_id=se_videocomments.videocomment_video_id"; }
    
    // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
    if($this->user_id == 0) { $video_query .= " LEFT JOIN se_users ON se_videos.video_user_id=se_users.user_id"; }
    
    // ADD WHERE IF NECESSARY
    if($where != "" || $this->user_id != 0) { $video_query .= " WHERE"; }
    
    // ENSURE USER ID IS NOT EMPTY
    if($this->user_id != 0) { $video_query .= " video_user_id='{$this->user_id}'"; }
    
    // INSERT AND IF NECESSARY
    if($this->user_id != 0 && $where != "") { $video_query .= " AND"; }
    
    // ADD WHERE CLAUSE, IF NECESSARY
    if($where != "") { $video_query .= " $where"; }
    
    // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
    $video_query .= " GROUP BY video_id ORDER BY $sort_by LIMIT $start, $limit";
    
    // RUN QUERY
    $videos = $database->database_query($video_query);
    
    // GET VIDEOS INTO AN ARRAY
    $video_array = Array();
    while($video_info = $database->database_fetch_assoc($videos))
    {
      // IF NO USER ID SPECIFIED, CREATE OBJECT FOR AUTHOR
      if($this->user_id == 0)
      {
        $author = new se_user();
        $author->user_exists = 1;
        $author->user_info['user_id'] = $video_info['user_id'];
        $author->user_info['user_username'] = $video_info['user_username'];
        $author->user_info['user_fname'] = $video_info['user_fname'];
        $author->user_info['user_lname'] = $video_info['user_lname'];
        $author->user_info['user_photo'] = $video_info['user_photo'];
        $author->user_displayname();
      }
      
      // OTHERWISE, SET AUTHOR TO OWNER/LOGGED-IN USER
      elseif($owner->user_exists != 0 && $owner->user_info['user_id'] == $video_info['video_user_id'])
      {
        $author = $owner;
      }
      elseif($user->user_exists != 0 && $user->user_info['user_id'] == $video_info['video_user_id'])
      {
        $author = $user;
      }
      
      // SET AUTHOR AND DIRECTORY
      $video_info['video_author'] = $author;
      $video_info['video_dir'] = $this->video_dir($video_info['video_user_id'], TRUE);
      
      // CHECK FOR THUMBNAIL
      $video_info['video_thumb'] = file_exists($video_info['video_dir'].$video_info['video_id']."_thumb.jpg");
      
      // SET UP DURATION
      $minutes = floor($video_info['video_duration_in_sec']/60);
      $seconds = $video_info['video_duration_in_sec']-60*$minutes;
      $video_info['video_duration_in_min'] = sprintf("%02d:%02d", $minutes, $seconds);
      
      // SET UP RATING
      $video_info['video_rating_full'] = floor($video_info['video_cache_rating']);
      $video_info['video_rating_part'] = (($video_info['video_cache_rating']-$video_info['video_rating_full']) == 0) ? 0 : 1;
      $video_info['video_rating_none'] = 5-$video_info['video_rating_full']-$video_info['video_rating_part'];
      
      // CREATE ARRAY OF VIDEO DATA
      $video_array[] = $video_info;
    }
    
    // RETURN ARRAY
    return $video_array;
  }
  
  // END video_list() METHOD








  //
  // THIS METHOD RETURNS THE PATH TO THE GIVEN USER'S VIDEO DIRECTORY
  //
  // INPUT:
  //    $user_id (OPTIONAL) REPRESENTING A USER'S USER_ID
  //
  // OUTPUT:
  //    A STRING REPRESENTING THE RELATIVE PATH TO THE USER'S VIDEO DIRECTORY
  //
  
  function video_dir($user_id = 0, $create_if_missing = FALSE)
  {
    if( !$user_id && $this->user_id )
      $user_id = $this->user_id;
    
    if( !$user_id && !is_numeric($user_id) )
      return FALSE;
    
    $subdir = $user_id + 999 - ( ( $user_id - 1 ) % 1000 );
    $videodir = "./uploads_video/{$subdir}/{$user_id}/";
    
    if( $create_if_missing )
    {
      // IN ADMIN DIRECTORY, ABORT
      if( !is_dir('./uploads_video') )
        return FALSE;
      
      $video_path_array = explode("/", $videodir);
      array_pop($video_path_array);
      array_pop($video_path_array);
      $subdir = implode("/", $video_path_array)."/";
      if( !is_dir($subdir) )
      { 
        @mkdir($subdir, 0777); 
        @chmod($subdir, 0777); 
        $handle = @fopen($subdir."index.php", 'x+');
        @fclose($handle);
      }
      if( !is_dir($videodir) )
      {
        @mkdir($videodir, 0777);
        @chmod($videodir, 0777);
        $handle = @fopen($videodir."/index.php", 'x+');
        @fclose($handle);
      }
    }
    
    return $videodir;
  }
  
  // END video_dir() METHOD








  //
  // THIS METHOD CREATES/EDITS A VIDEO
  //
  // INPUT:
  //    $video_title REPRESENTING THE TITLE OF THE VIDEO
  //    $video_desc REPRESENTING THE DESCRIPTION OF THE VIDEO
  //    $video_search REPRESENTING THE SEARCH PRIVACY
  //    $video_privacy REPRESENTING THE VIDEO PRIVACY
  //    $video_comments REPRESENTING THE COMMENT PRIVACY
  //
  // OUTPUT:
  //    The ID of the video or FALSE
  //
  
  function video_edit($video_title, $video_desc, $video_search, $video_privacy, $video_comments, $video_type, $video_youtube_code=NULL)
  {
    global $database, $url, $setting, $user, $actions, $misc;
    
    // INSERT ROW INTO VIDEO TABLE
    if( empty($video_title) )
    {
      $this->is_error = 5500200;
      return FALSE;      
    }
    
    $this->video_info['video_title'] = $video_title;
    $this->video_info['video_desc'] = $video_desc;
    $this->video_info['video_search'] = $video_search;
    $this->video_info['video_privacy'] = $video_privacy;
    $this->video_info['video_comments'] = $video_comments;
    $this->video_info['video_type'] = $video_type;
    $this->video_info['video_youtube_code'] = $video_youtube_code;
    $time = time();
    
    if( ($video_type == 1) && (!$video_youtube_code) )
    {
      $this->is_error = 5500189;
      return FALSE;
    }    
    
    if( !$this->video_exists )
    {
      $sql = "
        INSERT INTO se_videos
        (
          video_user_id,
          video_datecreated,
          video_title,
          video_desc,
          video_search,
          video_privacy,
          video_comments, 
          video_type,
          video_dateupdated
      ";
      
      if( $video_type == 1 ) $sql .= ",
          video_youtube_code,
          video_uploaded,
          video_is_converted
      ";
      
      $sql .=  "
        ) VALUES (
          '{$user->user_info['user_id']}',
          '{$time}',
          '{$video_title}',
          '{$video_desc}',
          '{$video_search}',
          '{$video_privacy}',
          '{$video_comments}',
          '{$video_type}',
          '{$time}'
      ";
      
      if( $video_type == 1 ) $sql .= ",
          '{$this->video_info['video_youtube_code']}',
          '1',
          '1'
      ";
      
      $sql .= "
        )
      ";
      
      $resource = $database->database_query($sql) or die(mysql_error());
      $this->video_info['video_id'] = $database->database_insert_id();
      if( !$database->database_affected_rows() || !$this->video_info['video_id'] ) {
        return FALSE;
      }
      $this->video_exists = TRUE;
      
      // INSERT ACTION NOW IF YOUTUBE
      if( $this->video_info['video_type'] == 1 ) 
      {
        // INSERT ACTION
        if(strlen($this->video_info['video_title']) > 100) 
        {
          $video_info['video_title'] = substr($video_info['video_title'], 0, 97); 
          $video_info['video_title'] .= "..."; 
        }
        $action_media = array($this->video_update_youtube_thumb());
        $actions->actions_add($user, "newyoutubevideo", array($user->user_info['user_username'], $user->user_displayname, $this->video_info['video_id'], $this->video_info['video_title']), $action_media, 0, false, "user", $user->user_info['user_id'], $user->user_info['user_privacy']);
      }
    }
    
    // UPDATE VIDEO
    else
    {
      // Check owner
      if( $this->video_info['video_user_id']!=$this->user_id )
      {
        return FALSE;
      }
      
      $sql = "
        UPDATE
          se_videos
        SET
          video_title='{$video_title}',
          video_desc='{$video_desc}',
          video_search='{$video_search}',
          video_privacy='{$video_privacy}',
          video_comments='{$video_comments}'
        WHERE
          video_id='{$this->video_info['video_id']}'
        LIMIT
          1
      ";
      
      $resource = $database->database_query($sql);
      
      if( !$resource )
        return FALSE;
    }
    
    // GET UPDATED VIDEO INFO
    $sql = "SELECT * FROM se_videos WHERE video_id='{$this->video_info['video_id']}' LIMIT 1";
    $resource = $database->database_query($sql);
    
    if( !$resource || !$database->database_num_rows($resource) )
      return FALSE;
    $this->video_info = $database->database_fetch_assoc($resource);
    return $this->video_info;
  }
  
  // END video_edit() METHOD









  //
  // THIS METHOD UPLOADS A VIDEO
  //
  // INPUT:
  //    $file_name REPRESENTING THE NAME OF THE FILE INPUT
  //
  // OUTPUT:
  //    TRUE/FALSE
  //
  
  function video_upload($file_name)
  {
    global $database, $url, $setting, $user;
    
    // Check exists and owner
    if( !$this->video_exists || $this->video_info['video_user_id']!=$this->user_id )
    {
      return FALSE;
    }
   
    // SET KEY VARIABLES
    $file_maxsize = $user->level_info['level_video_maxsize'];
    $file_exts = explode(",", str_replace(" ", "", strtolower($setting['setting_video_exts'])));
    $file_types = explode(",", str_replace(" ", "", strtolower($setting['setting_video_mime'])));
    
    // IF FILE EXTS AND MIMES ARE EMPTY, FILL IN WITH VIDEO'S EXT/TYPE
    if(trim($setting['setting_video_exts']) == "") { $file_exts[] = strtolower(str_replace(".", "", strrchr($_FILES[$file_name]['name'], "."))); }
    if(trim($setting['setting_video_mime']) == "") { $file_types[] = strtolower($_FILES[$file_name]['type']); }
    
    $video_ext = strtolower(str_replace(".", "", strrchr($_FILES[$file_name]['name'], ".")));
    
    // CHECK THAT UPLOAD DIRECTORY EXISTS, IF NOT THEN CREATE
    $video_directory = $this->video_dir($this->user_id, TRUE);
    
    // CHECK FOR ERRORS
    $new_video = new se_upload();
    $new_video->new_upload($file_name, $file_maxsize, $file_exts, $file_types);

    // UPLOAD VIDEO IF NO ERROR
    if( !$new_video->is_error )
    {
      // SET FILE DESTINATION
      $video_id = $this->video_info['video_id'];
      $new_filename = $video_id;
      $file_dest = $this->video_dir($this->user_id).$new_filename.".original.".$video_ext;
      
      // MOVE FILE
      $new_video->upload_file($file_dest);
      
      // (DON'T) DELETE FROM DATABASE IF ERROR
      if( $new_video->is_error )
      {
        /*
        $database->database_query("DELETE FROM se_videos WHERE video_id='{$video_id}' AND video_user_id='{$user->user_info['user_id']}'");
        @unlink($file_dest);
        */
      }
      
      // ALERT FFMPEG IF SUCCESSFUL
      else
      {
        // SET UPLOADED STATUS IN DATABASE
        $sql = "UPDATE se_videos SET video_uploaded=1 WHERE video_id='{$video_id}' LIMIT 1";
        $database->database_query($sql);
        
        // PREPARE SHELL SCRIPT INFO
        $linebreak = "\n";
        $directory = getcwd().substr($this->video_dir($this->user_id), 1);
        
        // ffmpeg doc
        // http://ffmpeg.mplayerhq.hu/ffmpeg-doc.html
        
        // WRITE SHELL SCRIPT
        $duration_session = rand(1, 9999);
        $cwd = getcwd();
        
        $shell_script  = "touch {$cwd}/uploads_video/encoding/queue/{$new_filename}".$linebreak;
        $shell_script .= "FULLOUTPUT{$duration_session}=$({$setting['setting_video_ffmpeg_path']} -i {$directory}{$new_filename}.original.{$video_ext} -ab 64k -ar 44100 -qscale 5 -vcodec flv -f flv -r 25 -s {$setting['setting_video_width']}x{$setting['setting_video_height']} {$directory}{$new_filename}.flv  2>&1)".$linebreak;
        
        if( $this->debug )
        {
          $shell_script .= "echo \$FULLOUTPUT{$duration_session} > {$cwd}/uploads_video/encoding/debug/{$new_filename}.ffmpeg.log".$linebreak;
        }
        
        $shell_script .= "DURATION{$duration_session}=$(echo \$FULLOUTPUT{$duration_session} | grep -o --perl-regexp '[Dd]uration.{1,3}([0-9][0-9]:[0-9][0-9]:[0-9][0-9])' | grep -o [0-9][0-9]:[0-9][0-9]:[0-9][0-9])".$linebreak;
        //$shell_script .= "DURATION{$duration_session}=$(echo \$FULLOUTPUT{$duration_session} | grep Duration | grep -o [0-9][0-9]:[0-9][0-9]:[0-9][0-9])".$linebreak;
        //$shell_script .= "DURATION{$duration_session}=$({$setting['setting_video_ffmpeg_path']} -i {$directory}{$new_filename} -ab 64k -vcodec flv -f flv -r 25 -s {$setting['setting_video_width']}x{$setting['setting_video_height']} {$directory}{$new_filename}.flv  2>&1 | grep Duration | grep -o [0-9][0-9]:[0-9][0-9]:[0-9][0-9])".$linebreak;
        
        $shell_script .= "{$setting['setting_video_ffmpeg_path']} -i {$directory}{$new_filename}.flv -s {$setting['setting_video_thumb_width']}x{$setting['setting_video_thumb_height']} -f image2 -ss 4.00 -vframes 1 {$directory}{$new_filename}_thumb.jpg".$linebreak;
        //$shell_script .= "{$setting['setting_video_ffmpeg_path']} -i {$directory}{$new_filename}.flv -s {$setting['setting_video_thumb_width']}x{$setting['setting_video_thumb_height']} -f image2 -ss 4.00 -vframes 1 -pix_fmt jpeg {$directory}{$new_filename}_thumb.jpg".$linebreak;
        $shell_script .= "rm --force {$directory}{$new_filename}.original.{$video_ext}".$linebreak;
        $shell_script .= "mv {$cwd}/uploads_video/encoding/queue/{$new_filename} \"{$cwd}/uploads_video/encoding/queue/{$new_filename}_complete_{$video_id}_\$DURATION{$duration_session}\"".$linebreak;
        
        // PUT SHELL SCRIPT IN DIR
        if(!($fh=fopen(getcwd().'/uploads_video/encoding/jobs/'.$new_filename, 'w')))
        {
          $database->database_query("DELETE FROM se_videos WHERE video_id='{$video_id}' AND video_user_id='{$user->user_info['user_id']}'");
          @unlink($directory.$new_filename);
          @unlink($file_dest);
          $new_video->is_error = 5500077;
        }
        else
        {
          fwrite($fh, $shell_script);
          fclose($fh);
          chmod(getcwd().'/uploads_video/encoding/jobs/'.$new_filename, 0777);
        }
      }
    }
    
    // RETURN FILE STATS
    $file_result = Array('is_error' => $new_video->is_error);
    
    return $file_result;
  }
  
  // END video_upload() METHOD








  //
  // THIS METHOD DELETES A SPECIFIED VIDEO
  //
  // INPUT:
  //    $video_id REPRESENTING THE ID OF THE VIDEO TO DELETE
  //
  // OUTPUT: 
  //    void
  //
  
  function video_delete($video_id)
  {
    global $database, $url;
    
    $video_query = $database->database_query("SELECT video_id, video_user_id FROM se_videos WHERE video_id='{$video_id}'");
    
    if($database->database_num_rows($video_query) != 1) { return; }
    
    $video_info = $database->database_fetch_assoc($video_query);
    if($this->user_id != 0 && $video_info['video_user_id'] != $this->user_id) { return; }
    
    // DELETE VIDEO AND THUMBNAIL
    $video_path = $this->video_dir($video_info['video_user_id']).$video_info['video_id'].".flv";
    if(file_exists($video_path)) { @unlink($video_path); }
    $thumb_path = $this->video_dir($video_info['video_user_id']).$video_info['video_id']."_thumb.jpg";
    if(file_exists($thumb_path)) { @unlink($thumb_path); }
    $queue_path = getcwd().'/uploads_video/encoding/queue/'.$video_info['video_id'];
    if(file_exists($queue_path)) { @unlink($queue_path); }
    
    $database->database_query("DELETE FROM se_videos, se_videocomments, se_videoratings USING se_videos LEFT JOIN se_videocomments ON se_videos.video_id=se_videocomments.videocomment_video_id LEFT JOIN se_videoratings ON se_videos.video_id=se_videoratings.videorating_video_id WHERE se_videos.video_id='{$video_id}'");
  }
  
  // END video_delete() METHOD








  //
  // THIS METHOD DELETES SELECTED VIDEOS
  //
  // INPUT:
  //    $start REPRESENTING THE VIDEO TO START WITH
  //    $limit REPRESENTING THE NUMBER OF VIDEOS TO RETURN
  //    $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
  //    $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
  //
  // OUTPUT: 
  //    void
  //
  
  function video_delete_selected($start, $limit, $sort_by = "video_id DESC", $where = "")
  {
    global $database;
    
    // BEGIN QUERY
    $video_query = "SELECT se_videos.video_id";
    
    // CONTINUE QUERY
    $video_query .= " FROM se_videos";
    
    // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
    if($this->user_id == 0) { $video_query .= " LEFT JOIN se_users ON se_videos.video_user_id=se_users.user_id"; }
    
    // ADD WHERE IF NECESSARY
    if($where != "" || $this->user_id != 0) { $video_query .= " WHERE"; }
    
    // ENSURE USER ID IS NOT EMPTY
    if($this->user_id != 0) { $video_query .= " video_user_id='{$this->user_id}'"; }
    
    // INSERT AND IF NECESSARY
    if($this->user_id != 0 && $where != "") { $video_query .= " AND"; }
    
    // ADD WHERE CLAUSE, IF NECESSARY
    if($where != "") { $video_query .= " $where"; }
    
    // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
    $video_query .= " GROUP BY video_id ORDER BY $sort_by LIMIT $start, $limit";
    
    // RUN QUERY
    $videos = $database->database_query($video_query);
    
    // DELETE VIDEOS
    while($video_info = $database->database_fetch_assoc($videos))
    {
      $var = "delete_video_".$video_info['video_id'];
      if($_POST[$var] == 1) $this->video_delete($video_info['video_id']);
    }
  }
  
  // END video_delete_selected() METHOD
  
  
  
  
  
  
  function video_update_youtube_thumb()
  {
    global $setting, $url, $user;
    
    $width = $setting['setting_video_thumb_width'];
    $height = $setting['setting_video_thumb_height'];
    $video_youtube_code = $this->video_info['video_youtube_code'];
    $thumb_source_path = "http://img.youtube.com/vi/{$video_youtube_code}/default.jpg";
    
    $video_directory = $this->video_dir($video_info['video_user_id'], TRUE);
    
    $thumb_dimensions = @getimagesize($thumb_source_path);
    
    $thumb_width = $thumb_dimensions[0];
    $thumb_height = $thumb_dimensions[1];
    
    $destination = $video_directory . $this->video_info['video_id'] . '_thumb.jpg';
    $file = imagecreatetruecolor($width, $height);
    $new = imagecreatefromjpeg($thumb_source_path);
    for($i=0; $i<256; $i++) { imagecolorallocate($file, $i, $i, $i); } 
    imagecopyresampled($file, $new, 0, 0, 0, 0, $width, $height, $thumb_width, $thumb_height); 
    @imagejpeg($file, $destination, 100);
    ImageDestroy($new);
    $media_link = str_replace($url->url_base, '', $url->url_create('video', $user->user_info['user_username'], $this->video_info['video_id']));
    ImageDestroy($file);
    return array(
      'media_link' => $media_link,
      'media_path' => $destination,
      'media_width' => $thumb_width,
      'media_height' => $thumb_height
    );
  }
}


?>