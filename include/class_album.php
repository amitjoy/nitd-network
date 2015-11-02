<?php


defined('SE_PAGE') or exit();


//
//  THIS CLASS CONTAINS ALBUM ENTRY-RELATED METHODS 
//
//  METHODS IN THIS CLASS:
//
//    se_album()
//    album_total()
//    album_list()
//    album_space()
//    album_delete()
//    album_delete_selected()
//    album_files()
//    album_media_upload()
//    album_media_list()
//    album_media_update()
//    album_media_delete()
//    album_media_rotate()
//




class se_album
{
	// INITIALIZE VARIABLES
	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT

	var $user_id;			// CONTAINS THE USER ID OF THE USER WHOSE ALBUM WE ARE EDITING








	// THIS METHOD SETS INITIAL VARS
	// INPUT: $user_id (OPTIONAL) REPRESENTING THE USER ID OF THE USER WHOSE ALBUMS WE ARE CONCERNED WITH
	// OUTPUT: 
  
	function se_album($user_id = 0)
  {
	  $this->user_id = $user_id;
	}
  
  // END se_album() METHOD








	// THIS METHOD RETURNS THE TOTAL NUMBER OF ALBUMS
	// INPUT: $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: AN INTEGER REPRESENTING THE NUMBER OF ALBUMS
	function album_total($where = "")
  {
	  global $database;
    
	  // BEGIN ALBUM QUERY
	  $sql = "
      SELECT
        NULL
      FROM
        se_albums
    ";
    
	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if( !$this->user_id ) $sql .= "
      LEFT JOIN
        se_users
      ON
        se_albums.album_user_id=se_users.user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( !empty($where) || $this->user_id ) $sql .= "
      WHERE
    ";
    
	  // ENSURE USER ID IS NOT EMPTY
	  if( $this->user_id ) $sql .= "
        album_user_id='{$this->user_id}'
    ";
    
	  // INSERT AND IF NECESSARY
	  if( $this->user_id && !empty($where) ) $sql .= " AND";
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( !empty($where) ) $sql .= "
        {$where}
    ";
    
	  // GET AND RETURN TOTAL PHOTO ALBUMS
	  $album_total = $database->database_num_rows($database->database_query($sql));
    
	  return (int) $album_total;
	}
  
  // END album_total() METHOD








	// THIS METHOD RETURNS AN ARRAY OF ALBUMS
	// INPUT: $start REPRESENTING THE ALBUM TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF ALBUMS TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: AN ARRAY OF ALBUMS
	function album_list($start, $limit, $sort_by = "album_id DESC", $where = "")
  {
	  global $database, $user, $owner;
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        se_albums.*,
        se_albums.album_totalfiles AS total_files,
        se_albums.album_totalspace AS total_space
    ";
    
	  // IF NO USER ID SPECIFIED, RETRIEVE USER INFORMATION
	  if( !$this->user_id ) $sql .= ",
        se_users.user_id,
        se_users.user_username,
        se_users.user_photo,
        se_users.user_fname,
        se_users.user_lname
    ";
    
	  // CONTINUE QUERY
	  $sql .= "
      FROM
        se_albums
    ";
    
	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if( !$this->user_id ) $sql .= "
      LEFT JOIN
        se_users
        ON se_albums.album_user_id=se_users.user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( !empty($where) || $this->user_id ) $sql .= "
      WHERE
    ";
    
	  // ENSURE USER ID IS NOT EMPTY
	  if( $this->user_id ) $sql .= "
        album_user_id='{$this->user_id}'
    ";
    
	  // INSERT AND IF NECESSARY
	  if( $this->user_id && !empty($where) ) $sql .= " AND";

	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( !empty($where) ) $sql .= "
        {$where}
    ";
    
	  // ADD ORDER, AND LIMIT CLAUSE
	  $sql .= "
      ORDER BY
        {$sort_by}
      LIMIT
        {$start}, {$limit}
    ";
    
	  // RUN QUERY
	  $resource = $database->database_query($sql);
    
	  // GET ALBUMS INTO AN ARRAY
	  $album_array = Array();
	  while( $album_info = $database->database_fetch_assoc($resource) )
    {
	    // IF NO USER ID SPECIFIED, CREATE OBJECT FOR AUTHOR
	    if( !$this->user_id )
      {
	      $author = new se_user();
	      $author->user_exists = TRUE;
	      $author->user_info['user_id']       = $album_info['user_id'];
	      $author->user_info['user_username'] = $album_info['user_username'];
	      $author->user_info['user_fname']    = $album_info['user_fname'];
	      $author->user_info['user_lname']    = $album_info['user_lname'];
	      $author->user_info['user_photo']    = $album_info['user_photo'];
	      $author->user_displayname();
      }
      
	    // OTHERWISE, SET AUTHOR TO OWNER/LOGGED-IN USER
	    elseif( $owner->user_exists && $owner->user_info['user_id']==$album_info['album_user_id'] )
      {
	      $author =& $owner;
	    }
      elseif( $user->user_exists && $user->user_info['user_id'] == $album_info['album_user_id'] )
      {
	      $author =& $user;
	    }
      
	    // CONVERT SPACE TO MB
	    $album_space_mb = ($album_info['total_space'] / 1024) / 1024;
	    $album_space_mb = round($album_space_mb, 2);
      
	    // GET PATH OF ALBUM COVER
	    $album_cover_id = 0;
	    $album_cover_ext = "";
	    if( $album_info['album_cover'] )
      {
	      $album_cover_query = $database->database_query("SELECT media_id, media_ext FROM se_media WHERE media_id='{$album_info['album_cover']}' AND media_album_id='{$album_info['album_id']}' LIMIT 1");
	      if( $database->database_num_rows($album_cover_query) )
        {
	        $album_cover_array = $database->database_fetch_assoc($album_cover_query);
	        $album_cover_id = $album_cover_array['media_id'];
	        $album_cover_ext = $album_cover_array['media_ext'];
	      }
	    }
      
	    // CREATE ARRAY OF ALBUM DATA
	    SE_Language::_preload(user_privacy_levels($album_info['album_privacy']));
      
      // SET OTHER INFO
      $album_info['album_author'] =& $author;
      $album_info['album_space'] = $album_space_mb;
      $album_info['album_privacy'] = user_privacy_levels($album_info['album_privacy']);
      $album_info['album_cover_id'] = $album_cover_id;
      $album_info['album_cover_ext'] = $album_cover_ext;
      $album_info['album_files'] = $album_info['total_files'];
      
	    $album_array[] = $album_info;
      
      unset($author, $album_info);
	  }
    
	  // RETURN ARRAY
	  return $album_array;
	}
  
  // END album_list() METHOD








	// THIS METHOD RETURNS THE SPACE USED
	// INPUT: $album_id (OPTIONAL) REPRESENTING THE ID OF THE ALBUM TO CALCULATE
	// OUTPUT: AN INTEGER REPRESENTING THE SPACE USED
	function album_space($album_id = 0)
  {
	  global $database;
    
	  // BEGIN QUERY
	  $album_query = "SELECT sum(se_media.media_filesize) AS total_space";
    
	  // CONTINUE QUERY
	  $album_query .= " FROM se_albums LEFT JOIN se_media ON se_albums.album_id=se_media.media_album_id";

	  // ADD WHERE IF NECESSARY
	  if($this->user_id != 0 || $album_id != 0) { $album_query .= " WHERE"; }

	  // ENSURE USER ID IS NOT EMPTY
	  if($this->user_id != 0) { $album_query .= " album_user_id='".$this->user_id."'"; }

	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 && $album_id != 0) { $album_query .= " AND"; }

	  // SPECIFY ALBUM IF NECESSARY
	  if($album_id != 0) { $album_query .= " album_id='$album_id'"; }

	  // GET AND RETURN TOTAL SPACE USED
	  $space_info = $database->database_fetch_assoc($database->database_query($album_query));
	  return $space_info[total_space];

	} // END album_space() METHOD








	// THIS METHOD DELETES A SPECIFIED ALBUM
	// INPUT: $album_id REPRESENTING THE ID OF THE ALBUM TO DELETE
	// OUTPUT: 
	function album_delete($album_id) {
	  global $database, $url;

	  $media = $database->database_query("SELECT media_id, media_ext FROM se_media WHERE media_album_id='$album_id'");

	  // LOOP OVER MEDIA
	  while($media_info = $database->database_fetch_assoc($media)) {
	    $media_path = $url->url_userdir($this->user_id).$media_info[media_id].".".$media_info[media_ext];
	    if(file_exists($media_path)) { unlink($media_path); }
	    $thumb_path = $url->url_userdir($this->user_id).$media_info[media_id]."_thumb.".$media_info[media_ext];
	    if(file_exists($thumb_path)) { unlink($thumb_path); }
	  }

	  $database->database_query("DELETE FROM se_albums, se_media, se_mediacomments, se_mediatags USING se_albums LEFT JOIN se_media ON se_albums.album_id=se_media.media_album_id LEFT JOIN se_mediacomments ON se_media.media_id=se_mediacomments.mediacomment_media_id LEFT JOIN se_mediatags ON se_media.media_id=se_mediatags.mediatag_media_id WHERE se_albums.album_id='$album_id'");

	  // CALL ALBUM CREATION HOOK
	  ($hook = SE_Hook::exists('se_album_delete')) ? SE_Hook::call($hook, array()) : NULL;


	} // END album_delete() METHOD








	// THIS METHOD DELETES SELECTED ALBUMS
	// INPUT: $start REPRESENTING THE ALBUM TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF ALBUMS TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: 
	function album_delete_selected($start, $limit, $sort_by = "album_id DESC", $where = "") {
	  global $database;

	  // BEGIN QUERY
	  $album_query = "SELECT se_albums.*, count(se_media.media_id) AS total_files, sum(se_media.media_filesize) AS total_space";
	
	  // IF NO USER ID SPECIFIED, RETRIEVE USER INFORMATION
	  if($this->user_id == 0) { $album_query .= ", se_users.user_id, se_users.user_username, se_users.user_photo"; }

	  // CONTINUE QUERY
	  $album_query .= " FROM se_albums LEFT JOIN se_media ON se_albums.album_id=se_media.media_album_id";

	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if($this->user_id == 0) { $album_query .= " LEFT JOIN se_users ON se_albums.album_user_id=se_users.user_id"; }

	  // ADD WHERE IF NECESSARY
	  if($where != "" || $this->user_id != 0) { $album_query .= " WHERE"; }

	  // ENSURE USER ID IS NOT EMPTY
	  if($this->user_id != 0) { $album_query .= " album_user_id='".$this->user_id."'"; }

	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 && $where != "") { $album_query .= " AND"; }

	  // ADD WHERE CLAUSE, IF NECESSARY
	  if($where != "") { $album_query .= " $where"; }

	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $album_query .= " GROUP BY album_id ORDER BY $sort_by LIMIT $start, $limit";

	  // RUN QUERY
	  $albums = $database->database_query($album_query);

	  // GET ALBUMS INTO AN ARRAY
	  $album_array = Array();
	  while($album_info = $database->database_fetch_assoc($albums)) {
    	    $var = "delete_album_".$album_info[album_id];
	    if($_POST[$var] == 1) { $this->album_delete($album_info[album_id]); }
	  }

	} // END album_delete_selected() METHOD








	// THIS METHOD RETURNS THE NUMBER OF FILES
	// INPUT: $album_id (OPTIONAL) REPRESENTING THE ID OF THE ALBUM TO CALCULATE
	//	  $where (OPTIONAL) REPRESENTING A WHERE CLAUSE
	// OUTPUT: AN INTEGER REPRESENTING THE NUMBER OF FILES
	function album_files($album_id = 0, $where = "")
  {
	  global $database;
    
    // NEW HANDLING
    if( empty($where) )
    {
      $sql = "
        SELECT
          album_totalfiles
        FROM
          se_albums
        WHERE 
      ";
      
      if( $this->user_id ) $sql .= "
          album_user_id='{$this->user_id}'
      ";
      
      if( $this->user_id && $album_id ) $sql .= " && ";
      
      if( $album_id ) $sql .= "
          album_id='{$album_id}'
      ";
      
      $resource = $database->database_query($sql);
      
      if( $resource )
      {
        $file_info = $database->database_fetch_assoc($resource);
        return $file_info['album_totalfiles'];
      }
    }
    
	  // BEGIN QUERY
	  $album_query = "SELECT count(se_media.media_id) AS total_files";
    
	  // CONTINUE QUERY
	  $album_query .= " FROM se_albums LEFT JOIN se_media ON se_albums.album_id=se_media.media_album_id";
    
	  // ADD WHERE IF NECESSARY
	  if($this->user_id != 0 || $album_id != 0 || $where != "") { $album_query .= " WHERE"; }
    
	  // ENSURE USER ID IS NOT EMPTY
	  if($this->user_id != 0) { $album_query .= " album_user_id='".$this->user_id."'"; }
    
	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 && ($album_id != 0 || $where != "")) { $album_query .= " AND"; }
    
	  // SPECIFY ALBUM IF NECESSARY
	  if($album_id != 0) { $album_query .= " album_id='$album_id'"; }
    
	  // INSERT AND IF NECESSARY
	  if($album_id != 0 && $where != "") { $album_query .= " AND"; }
    
	  // ADD WHERE CLAUSE
	  if($where != "") { $album_query .= " $where"; }
    
	  // GET AND RETURN TOTAL FILES
	  $file_info = $database->database_fetch_assoc($database->database_query($album_query));
	  return $file_info[total_files];
	}
  
  // END album_files() METHOD








	// THIS METHOD UPLOADS MEDIA TO AN ALBUM
	// INPUT: $file_name REPRESENTING THE NAME OF THE FILE INPUT
	//	  $album_id REPRESENTING THE ID OF THE ALBUM TO UPLOAD THE MEDIA TO
	//	  $space_left REPRESENTING THE AMOUNT OF SPACE LEFT
	// OUTPUT:
  
	function album_media_upload($file_name, $album_id, &$space_left)
  {
	  global $database, $url, $user;
    
	  // SET KEY VARIABLES
	  $file_maxsize = $user->level_info[level_album_maxsize];
	  $file_exts = explode(",", str_replace(" ", "", strtolower($user->level_info[level_album_exts])));
	  $file_types = explode(",", str_replace(" ", "", strtolower($user->level_info[level_album_mimes])));
	  $file_maxwidth = $user->level_info[level_album_width];
	  $file_maxheight = $user->level_info[level_album_height];
    
	  $new_media = new se_upload();
	  $new_media->new_upload($file_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);
    
	  // UPLOAD AND RESIZE PHOTO IF NO ERROR
	  if($new_media->is_error == 0)
    {
	    // GET MAX ORDER
	    $max = $database->database_fetch_assoc($database->database_query("SELECT max(media_order) AS max FROM se_media LEFT JOIN se_albums ON se_media.media_album_id=se_albums.album_id WHERE se_albums.album_user_id='".$user->user_info[user_id]."'"));
	    $media_order = $max[max]+1;
      
	    // INSERT ROW INTO MEDIA TABLE
	    $database->database_query("
        INSERT INTO se_media (
          media_album_id,
          media_date,
          media_order
        ) VALUES (
          '$album_id',
          '".time()."',
          '$media_order'
        )
      ");
      
	    $media_id = $database->database_insert_id();
      
	    // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
	    if($new_media->is_image == 1)
      {
	      $file_dest = $url->url_userdir($user->user_info[user_id]).$media_id.".jpg";
	      $thumb_dest = $url->url_userdir($user->user_info[user_id]).$media_id."_thumb.jpg";
        
	      // UPLOAD THUMB
	      $new_media->upload_thumb($thumb_dest, 200);
        
	      // UPLOAD FILE
	      $new_media->upload_photo($file_dest);
        
	      $file_ext = "jpg";
	      $file_filesize = filesize($file_dest);
	    }
      else
      {
	      $file_dest = $url->url_userdir($user->user_info[user_id]).$media_id.".".$new_media->file_ext;
        
	      // UPLOAD THUMB IF NECESSARY
	      if($new_media->file_ext == 'gif')
        {
	        $thumb_dest = $url->url_userdir($user->user_info[user_id]).$media_id."_thumb.jpg";
	        $new_media->upload_thumb($thumb_dest, 200);
	      }
        
	      // MOVE FILE
	      $new_media->upload_file($file_dest);
	      $file_ext = $new_media->file_ext;
	      $file_filesize = filesize($file_dest);
	    }
      
      if( !is_numeric($file_filesize) ) $file_filesize = 0;
      
	    // CHECK SPACE LEFT
	    if($file_filesize > $space_left) {
	      $new_media->is_error = 1000085;
	    } else {
	      $space_left = $space_left-$file_filesize;
	    }
      
	    // DELETE FROM DATABASE IF ERROR
	    if($new_media->is_error != 0)
      {
	      $database->database_query("DELETE FROM se_media WHERE media_id='$media_id' AND media_album_id='$album_id'");
	      @unlink($file_dest);
      }
      
	    // UPDATE ROW IF NO ERROR
	    else
      {
	      $database->database_query("UPDATE se_media SET media_ext='{$file_ext}', media_filesize='{$file_filesize}' WHERE media_id='{$media_id}' AND media_album_id='{$album_id}' LIMIT 1");
	      $database->database_query("UPDATE se_albums SET album_totalfiles=album_totalfiles+1, album_totalspace=album_totalspace+'{$file_filesize}' WHERE album_id='{$album_id}' LIMIT 1");
	    }
	  }
    
	  // RETURN FILE STATS
	  $file_result = array(
      'is_error'        => $new_media->is_error,
			'file_name'       => $_FILES[$file_name]['name'],
			'media_id'        => $media_id,
			'media_ext'       => $file_ext,
			'media_filesize'  => $file_filesize
    );
    
	  return $file_result;
	}
  
  // END album_media_upload() METHOD








	// THIS METHOD RETURNS AN ARRAY OF MEDIA
	// INPUT: $start REPRESENTING THE MEDIA TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF MEDIA TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $select (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO SELECT
	// OUTPUT: AN ARRAY OF MEDIA
  
	function album_media_list($start, $limit, $sort_by = "media_id DESC", $where = "", $select = "")
  {
	  global $database, $user, $owner;
    
	  // BEGIN QUERY
	  $sql = "
      SELECT
        se_media.*,
        se_albums.album_id,
        se_albums.album_user_id,
        se_albums.album_title,
        se_media.media_totalcomments AS total_comments
    ";
    
	  // IF NO USER ID SPECIFIED, RETRIEVE USER INFORMATION
	  if( !$this->user_id ) $sql .= ",
        se_users.user_id,
        se_users.user_username,
        se_users.user_photo,
        se_users.user_fname,
        se_users.user_lname
    ";
    
	  // ADD ADDITIONAL SELECTS
	  if( !empty($select) ) $sql .= $select;
    
	  // CONTINUE QUERY
	  $sql .= "
      FROM
        se_media
      LEFT JOIN
        se_albums
        ON se_albums.album_id=se_media.media_album_id
    ";
    
	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if( !$this->user_id ) $sql .= "
      LEFT JOIN
        se_users
        ON se_albums.album_user_id=se_users.user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if( !empty($where) || $this->user_id ) $sql .= "
      WHERE
    ";
    
	  // ENSURE USER ID IS NOT EMPTY
	  if( $this->user_id ) $sql .= "
        album_user_id='{$this->user_id}'
    ";
    
	  // INSERT AND IF NECESSARY
	  if( $this->user_id && !empty($where) ) $sql .= " AND";
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( !empty($where) ) $sql .= "
      {$where}
    ";
    
	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $sql .= "
      ORDER BY
        {$sort_by}
      LIMIT
        {$start}, {$limit}
    ";
    
	  // RUN QUERY
	  $resource = $database->database_query($sql);
    
	  // GET MEDIA INTO AN ARRAY
	  $media_array = array();
	  while($media_info = $database->database_fetch_assoc($resource))
    {
	    // IF NO USER ID SPECIFIED, CREATE OBJECT FOR AUTHOR
	    if( !$this->user_id )
      {
	      $author = new se_user();
	      $author->user_exists = TRUE;
	      $author->user_info['user_id']       = $media_info['user_id'];
	      $author->user_info['user_username'] = $media_info['user_username'];
	      $author->user_info['user_photo']    = $media_info['user_photo'];
	      $author->user_info['user_fname']    = $media_info['user_fname'];
	      $author->user_info['user_lname']    = $media_info['user_lname'];
	      $author->user_displayname();
      }
      
	    // OTHERWISE, SET AUTHOR TO OWNER/LOGGED-IN USER
	    elseif( $owner->user_exists && $owner->user_info['user_id']==$media_info['album_user_id'] )
      {
	      $author =& $owner;
	    }
      elseif( $user->user_exists && $user->user_info['user_id']==$media_info['album_user_id'] )
      {
	      $author =& $user;
	    }
      
      $media_info['media_author'] =& $author;
      
      // BACKWARDS COMPAT
      $media_info['media_comments_total'] = $media_info['total_comments'];
      
      $media_info['media_desc'] = str_replace("<br>", "\r\n", $media_info['media_desc']);
      
	    // CREATE ARRAY OF MEDIA DATA
	    $media_array[] = $media_info;
      
      unset($media_info, $author);
	  }
    
	  // RETURN ARRAY
	  return $media_array;
	}
  
  // END album_media_list() METHOD








	// THIS METHOD UPDATES MEDIA INFORMATION
	// INPUT: $start REPRESENTING THE MEDIA TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF MEDIA TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: AN ARRAY CONTAINING ALL THE MEDIA ID
  
	function album_media_update($start, $limit, $sort_by = "media_id DESC", $where = "")
  {
	  global $database;
    
	  // BEGIN QUERY
	  $media_query = "SELECT se_media.*, se_albums.album_id, se_albums.album_user_id, se_albums.album_title, count(se_mediacomments.mediacomment_id) AS total_comments";
    
	  // IF NO USER ID SPECIFIED, RETRIEVE USER INFORMATION
	  if($this->user_id == 0) { $media_query .= ", se_users.user_id, se_users.user_username, se_users.user_photo"; }
    
	  // CONTINUE QUERY
	  $media_query .= " FROM se_media LEFT JOIN se_mediacomments ON se_mediacomments.mediacomment_media_id=se_media.media_id LEFT JOIN se_albums ON se_albums.album_id=se_media.media_album_id";
    
	  // IF NO USER ID SPECIFIED, JOIN TO USER TABLE
	  if($this->user_id == 0) { $media_query .= " LEFT JOIN se_users ON se_albums.album_user_id=se_users.user_id"; }
    
	  // ADD WHERE IF NECESSARY
	  if($where != "" || $this->user_id != 0) { $media_query .= " WHERE"; }
    
	  // ENSURE USER ID IS NOT EMPTY
	  if($this->user_id != 0) { $media_query .= " album_user_id='".$this->user_id."'"; }
    
	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 && $where != "") { $media_query .= " AND"; }
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if($where != "") { $media_query .= " $where"; }
    
	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $media_query .= " GROUP BY media_id ORDER BY $sort_by LIMIT $start, $limit";
    
	  // RUN QUERY
	  $media = $database->database_query($media_query);
    
	  // GET MEDIA INTO AN ARRAY
	  $media_array = Array();
	  while($media_info = $database->database_fetch_assoc($media))
    {
	    $var1 = "media_title_".$media_info[media_id];
	    $var2 = "media_desc_".$media_info[media_id];
	    $var3 = "media_album_id_".$media_info[media_id];
	    $media_title = censor($_POST[$var1]);
	    $media_desc = censor(str_replace("\r\n", "<br>", $_POST[$var2]));
	    $media_album_id = $_POST[$var3];
	    if($media_title != $media_info[media_title] || $media_desc != $media_info[media_desc] || $media_album_id != $media_info[media_album_id])
      {
	      if($media_album_id != $media_info[media_album_id])
        {
          if($database->database_num_rows($database->database_query("SELECT album_id FROM se_albums WHERE album_id='$media_album_id' AND album_user_id='".$this->user_id."' LIMIT 1")) != 1)
          {
            $media_album_id = $media_info[media_album_id];
          }
	      }
	      $database->database_query("UPDATE se_media SET media_title='$media_title', media_desc='$media_desc', media_album_id='$media_album_id' WHERE media_id='$media_info[media_id]'");
	    }
	    
	    if($media_album_id == $media_info[media_album_id])
      {
	      $media_array[] = $media_info[media_id];
	    }
	  }
    
	  return $media_array;
	}
  
  // END album_media_update() METHOD








	// THIS METHOD DELETES SELECTED MEDIA
	// INPUT: $start REPRESENTING THE MEDIA TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF MEDIA TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT:
  
	function album_media_delete($start, $limit, $sort_by = "media_id DESC", $where = "")
  {
	  global $database, $url;
    
	  $delete = $_POST['delete'];
	  if(count($delete) == 0) { return; }
    
	  // BEGIN QUERY
	  $media_query = "
      SELECT
        se_media.media_id,
        se_media.media_ext,
        se_media.media_album_id,
        se_media.media_filesize
      FROM
        se_media
      LEFT JOIN
        se_mediacomments
        ON se_mediacomments.mediacomment_media_id=se_media.media_id
      LEFT JOIN
        se_albums ON se_albums.album_id=se_media.media_album_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  $media_query .= "
      WHERE
        se_media.media_id IN(".implode(", ", $delete).") &&
        se_albums.album_user_id='{$this->user_id}'
    ";
    
	  // ADD WHERE CLAUSE IF NECESSARY
	  if($where != "") { $media_query .= " AND $where"; }
    
	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $media_query .= " GROUP BY media_id ORDER BY $sort_by LIMIT $start, $limit";
    
	  // RUN QUERY
	  $media = $database->database_query($media_query);
    
	  // LOOP OVER MEDIA
	  $thumbs = Array();
	  while($media_info = $database->database_fetch_assoc($media))
    {
	    $media_path = $url->url_userdir($this->user_id).$media_info[media_id].".".$media_info[media_ext];
	    if(file_exists($media_path)) { unlink($media_path); }
	    $thumb_path = $url->url_userdir($this->user_id).$media_info[media_id]."_thumb.jpg";
	    if(file_exists($thumb_path)) { unlink($thumb_path); }
	    $thumbs[] = $url->url_base.substr($url->url_userdir($this->user_id), 2).$media_info[media_id]."_thumb.jpg";
      
      // DECREMENT ALBUM FILES AND SPACE COUNT
      if( !is_numeric($media_info['media_filesize']) ) $media_info['media_filesize'] = 0;
      $database->database_query("UPDATE se_albums SET album_totalfiles=album_totalfiles-1, album_totalspace=album_totalspace-'{$media_info['media_filesize']}' WHERE album_id='{$media_info['media_album_id']}' LIMIT 1");
    }
    
	  // DELETE ACTION MEDIA IF NECESSARY
	  $database->database_query("DELETE FROM se_actionmedia WHERE actionmedia_path IN ('".implode("', '", $thumbs)."')");
    
	  // DELETE MEDIA FROM DATABASE
	  $database->database_query("DELETE FROM se_media, se_mediacomments, se_mediatags USING se_media LEFT JOIN se_mediacomments ON se_media.media_id=se_mediacomments.mediacomment_media_id LEFT JOIN se_mediatags ON se_media.media_id=se_mediatags.mediatag_media_id WHERE se_media.media_id IN(".implode(", ", $delete).")");
    
	  return TRUE;
	}
  
  // END album_media_delete() METHOD








	// THIS METHOD ROTATES SELECTED MEDIA
	// INPUT: $media_id REPRESENTING THE ID OF THE MEDIA TO ROTATE
	//	  $angle REPRESENTING THE NUMBER OF DEGREES TO ROTATE THE IMAGE
	// OUTPUT:
  
	function album_media_rotate($media_id, $dir)
  {
	  global $database, $url;
    
	  // ENSURE MEDIA BELONGS TO USER
	  $media = $database->database_query("SELECT se_media.* FROM se_media LEFT JOIN se_albums ON se_media.media_album_id=se_albums.album_id WHERE media_id='$media_id' AND album_user_id='".$this->user_id."'");
	  if($database->database_num_rows($media) != 1) { return; }
	  $media_info = $database->database_fetch_assoc($media);
    
	  // GET IMAGE INFORMATION
	  $media_path = $url->url_userdir($this->user_id).$media_info[media_id].".".$media_info[media_ext];
	  $media_dimensions = @getimagesize($media_path);
	  $media_width = $media_dimensions[0];
	  $media_height = $media_dimensions[1];
    
	  // ROTATE IMAGE
	  switch($media_info[media_ext]) {
	    case "gif":
	      $old = imagecreatefromgif($media_path);
	      $rotate = imagerotate($old, $dir, 0);
	      imagejpeg($rotate, $media_path, 100);
	      ImageDestroy($old);
	      ImageDestroy($rotate);
	      break;
	    case "bmp":
	      $old = imagecreatefrombmp($media_path);
	      $rotate = imagerotate($old, $dir, 0);
	      imagejpeg($rotate, $media_path, 100);
	      ImageDestroy($old);
	      ImageDestroy($rotate);
	      break;
	    case "jpeg":
	    case "jpg":
	      $old = imagecreatefromjpeg($media_path);
	      $rotate = imagerotate($old, $dir, 0);
	      imagejpeg($rotate, $media_path, 100);
	      ImageDestroy($old);
	      ImageDestroy($rotate);
	      break;
	    case "png":
	      $old = imagecreatefrompng($media_path);
	      $rotate = imagerotate($old, $dir, 0);
	      imagejpeg($rotate, $media_path, 100);
	      ImageDestroy($old);
	      ImageDestroy($rotate);
	      break;
	  } 
    
	  // GET THUMB INFO
	  $thumb_path = $url->url_userdir($this->user_id).$media_info[media_id]."_thumb.jpg";
	  $thumb_dimensions = @getimagesize($thumb_path);
	  $thumb_width = $thumb_dimensions[0];
	  $thumb_height = $thumb_dimensions[1];
    
	  // ROTATE THUMB
	  $old = imagecreatefromjpeg($thumb_path);
	  $rotate = imagerotate($old, $dir, 0);
	  imagejpeg($rotate, $thumb_path, 100);
	  ImageDestroy($old);
	  ImageDestroy($rotate);
	}
  
  // END album_media_rotate() METHOD
  
}

?>