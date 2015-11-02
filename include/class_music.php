<?php

//THIS CLASS CONTAINS MUSIC-RELATED METHODS
//  METHODS IN THIS CLASS
//  se_music()
//  music_upload()
//  music_space()
//  music_list()
//  music_list_total()
//  music_total()
//  music_delete()
//  music_moveup()
//  music_reorder()
//  music_track_info()
//  music_track_update()
//  profile_settings()
//  music_skin_list()

class se_music
{
	// INITIALIZE VARIABLES
	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT
	var $error_message;		// CONTAINS RELEVANT ERROR MESSAGE

	var $user_id;			// CONTAINS THE USER ID OF THE USER WHOSE MUSIC WE ARE EDITING
	var $music_title;
	
  
  
	// THIS METHOD SETS INITIAL VARS
	// OUTPUT: 
	function se_music($user_id = 0)
  {
	  $this->user_id = $user_id;
	}
  // END se_music() METHOD	
  
  
  
	// THIS METHOD UPLOADS MUSIC
	// INPUT: $file_name REPRESENTING THE NAME OF THE FILE INPUT
	//	  $space_left REPRESENTING THE AMOUNT OF SPACE LEFT
	// OUTPUT:
	function music_upload($file_name, &$space_left)
  {
	  global $database, $url, $user;
		
	  // SET KEY VARIABLES
	  $file_maxsize = $user->level_info['level_music_maxsize'];
	  $file_exts    = explode(",", str_replace(" ", "", strtolower($user->level_info['level_music_exts'])));
	  $file_types   = explode(",", str_replace(" ", "", strtolower($user->level_info['level_music_mimes'])));
	  
	  $new_music = new se_upload();
	  $new_music->new_upload($file_name, $file_maxsize, $file_exts, $file_types);
	  
    // CHECK SPACE LEFT
    if( $new_music->file_size>$space_left )
      $new_music->is_error = 4000087;
    else
      $space_left = $space_left - $file_filesize;
    
    
	  if( !$new_music->is_error )
    {
      // GENERATE TITLE
      $myId3 = new ID3($file_dest);
      if( $myId3->getInfo() )
      {
        $id3_artist = trim($myId3->getArtist());
        $id3_title = trim($myId3->getTitle());
        
        if( $id3_artist && $id3_title )
          $music_title = $id3_artist .' - '.  $id3_title;
      }
      
      if( !trim($music_title) )
        $music_title = $new_music->file_name;
      
      $music_title = security(censor($music_title));
      
      
	    // INSERT ROW INTO MUSIC TABLE
      $time = time();
	    $track_num = $database->database_fetch_array($database->database_query("SELECT music_track_num as track FROM se_music WHERE music_user_id = '$this->user_id' ORDER BY music_track_num DESC LIMIT 1"));
	    $track_num = $track_num['track'] + 1;
	    $database->database_query("
        INSERT INTO se_music
        (
          music_user_id,
          music_track_num,
          music_date,
          music_title,
          music_ext,
          music_filesize
        )
        VALUES
        (
          '{$this->user_id}',
          '{$track_num}',
          '{$time}',
          '{$music_title}',
          '{$new_music->file_ext}',
          '{$new_music->file_size}'
        )
      ");
      
      $music_id = $database->database_insert_id();
	    
      // MOVE UPLOADED FILE
	    $file_dest = $url->url_userdir($user->user_info[user_id]).$music_id.".".$new_music->file_ext;
	    $new_music->upload_file($file_dest);
    }
    
    
    // RETURN AND DELETE FROM DATABASE IF ERROR
    if( $new_music->is_error )
    {
      if( $music_id ) $database->database_query("DELETE FROM se_music WHERE music_id='$music_id' && music_user_id='{$this->user_id}'");
      if( $file_dest ) @unlink($file_dest);
      return array(
        'is_error' => $new_music->is_error
      );
    }
	  
	  // RETURN FILE STATS
	  return array(
			'music_id'        => $music_id,
			'music_ext'       => $new_music->file_ext,
			'music_filesize'  => $new_music->file_size,
			'music_title'     => $music_title
    );
	}
  
  // END music_media_upload() METHOD
  
  
  
	// THIS METHOD RETURNS THE SPACE USED
	// INPUT: $user_id
	// OUTPUT: AN INTEGER REPRESENTING THE SPACE USED
	function music_space($user_id = 0)
  {
	  global $database;
    
	  // BEGIN QUERY
	  $music_query = "SELECT sum(se_music.music_filesize) AS total_space";
    
	  // CONTINUE QUERY
	  $music_query .= " FROM se_music";
    
	  // ADD WHERE IF NECESSARY
	  if($this->user_id != 0) { $music_query .= " WHERE music_user_id='".$this->user_id."'"; }
    
	  // GET AND RETURN TOTAL SPACE USED
	  $space_info = $database->database_fetch_assoc($database->database_query($music_query));
	  return $space_info[total_space];
	}
  
  // END music_space() METHOD
	
  
  
	// this method returns the music for a specific user
	function &music_list($start=NULL, $limit=NULL, $sort_by=NULL, $where=NULL, $select=NULL)
  {
		global $database;
		
    // GENERATE QUERY
    $sql = "
      SELECT
    ";
    
    // SELECT FIELDS
    if( $select )
      $sql .= " $select ";
    else
      $sql .= " se_music.* ";
    
    if( !$this->user_id && !$select ) $sql .= ",
      se_users.user_id,
      se_users.user_username,
      se_users.user_photo,
      se_users.user_fname,
      se_users.user_lname
    ";
    
    $sql .= "
      FROM
        se_music
    ";
    
    if( !$this->user_id ) $sql .= "
      LEFT JOIN
        se_users
        ON se_users.user_id=se_music.music_user_id
    ";
    
    if( $this->user_id ) $sql .= "
      WHERE
        se_music.music_user_id='{$this->user_id}'
    ";
    
    if( $this->user_id && $where )
      $sql .= " && ";
    elseif( $where )
      $sql .= " WHERE ";
    
    if( $where )
      $sql .= " $where ";
    
    // CUSTOM
    if( $sort_by )
      $sql .= " ORDER BY $sort_by ";
    // IF USER ID AND WHERE UNSET, ASSUME PROFILE
    elseif( !$sort_by && $this->user_id )
      $sql .= "  ORDER BY se_music.music_track_num ASC";
    // OTHERWISE ASSUME SEARCH
    else
      $sql .= "  ORDER BY se_music.music_id ASC";
    
    // START AND LIMIT
    if( $start || $limit ) $sql .= "
      LIMIT
    ";
    
    if( $start ) $sql .= " {$start}, ";
    
    if( $limit ) $sql .= " {$limit} ";
    
		$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
    
		$music_array = array();
		while( $musiclist=$database->database_fetch_assoc($resource) )
    {
      if( $musiclist['music_filesize'] )
        $musiclist['music_filesize'] = round(($musiclist['music_filesize'] / 1024) / 1024, 2);
      
      if( !$this->user_id )
      {
        $musiclist['music_uploader'] = new se_user();
        $musiclist['music_uploader']->user_info['user_id'] = $musiclist['user_id'];
        $musiclist['music_uploader']->user_info['user_username'] = $musiclist['user_username'];
        $musiclist['music_uploader']->user_info['user_photo'] = $musiclist['user_photo'];
        $musiclist['music_uploader']->user_info['user_fname'] = $musiclist['user_fname'];
        $musiclist['music_uploader']->user_info['user_lname'] = $musiclist['user_lname'];
        $musiclist['music_uploader']->user_displayname();
      }
      
			$music_array[] = $musiclist;
		}
		
		return $music_array;
	}
  
  
  
	//  this method returns all of the music for all users
	function music_list_total($start=NULL, $limit=NULL, $sort_by=NULL, $where=NULL)
  {
    $music_data = $this->music_list($start, $limit, $sort_by, $where, 'COUNT(*) AS music_total');
    return $music_data[0]['music_total'];
	}
	
  
  
	//  returns the count of total tracks, for the admin panel
	function music_total($where)
  {
		global $database;
		
		$music = $database->database_query("SELECT NULL FROM se_music");
		$music_count = $database->database_num_rows($music);
		
		return $music_count;
	}
	
  
  
	//  deletes the specified track from the users music
	//  INPUT: $file = name of the file
	//         $music_id = id number of the music track
	function music_delete($music_id)
  {
		global $database, $url, $user, $admin;
		
    if( $this->user_id )
    {
      $music = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_music WHERE music_user_id='{$this->user_id}' && music_id='{$music_id}'"));
    }
    
    elseif( $admin && $admin->admin_exists )
    {
      $music = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_music WHERE music_id='{$music_id}'"));
    }
    
    if( empty($music) )
      return FALSE;
    
    $userdir = $url->url_userdir($music['music_user_id']);
    $music_file = "{$userdir}{$music['music_id']}.{$music['music_ext']}";
    
		$database->database_query("DELETE FROM se_music WHERE music_id='{$music_id}' LIMIT 1");
		if( file_exists($music_file) ) { unlink($music_file); }
    
    return TRUE;
	}	
  
  
  
	//  moves the specified track up one level in the playlist
	// INPUT: music id number
	function music_moveup($music_id)
  {
		global $database;
		
		$old_track_id = $database->database_fetch_assoc($database->database_query("SELECT music_track_num FROM se_music WHERE music_id = '$music_id' AND music_user_id='$this->user_id'"));
		$new_track_id = $old_track_id[music_track_num] - 1;
		$previous_track_id = $database->database_fetch_assoc($database->database_query("SELECT music_track_num, music_id FROM se_music WHERE music_track_num = '$new_track_id' AND music_user_id='$this->user_id'"));
		
		$database->database_query("UPDATE se_music SET music_track_num ='$new_track_id' WHERE music_track_num = '$old_track_id[music_track_num]' AND music_user_id='$this->user_id' AND music_id = '$music_id' LIMIT 1");
		$database->database_query("UPDATE se_music SET music_track_num ='$old_track_id[music_track_num]' WHERE music_track_num = '$new_track_id' AND music_user_id='$this->user_id' AND music_id = '$previous_track_id[music_id]' LIMIT 1");
    
    return TRUE;
  }
  
  
  
	// Reorders the entire playlist
	// INPUT: music id number
	function music_reorder($music_ids)
  {
		global $database;
    
    if( is_string($music_ids) )
      $music_ids = explode(',', $music_ids);
    
    if( !is_array($music_ids) || empty($music_ids) )
      return FALSE;
    
    // MAKE ORDER
    $music_index = 1;
    foreach( $music_ids as $music_id )
    {
      $sql = "UPDATE se_music SET music_track_num='{$music_index}' WHERE music_user_id='{$this->user_id}' && music_id='{$music_id}' LIMIT 1";
      $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
      $music_index++;
    }
    
    return TRUE;
  }
	
  
  
	//  returns track title, track number, extension, and filesize information from the specified song number
	//  INPUT: music id number
	function music_track_info($music_id)
  {
		global $database;
		
		$music = $database->database_query("SELECT music_id, music_track_num, music_title, music_ext, music_filesize FROM se_music WHERE music_user_id = '$this->user_id' AND music_id = '$music_id'");
		$track_info = $database->database_fetch_assoc($music);
		return $track_info;
	}
  
  
  
	//  updates the specified track with the new title
	//  INPUT: music track id, new music title
	function music_track_update($music_id, $music_title)
  {
		global $database;
		
    $sql = "UPDATE se_music SET music_title='{$music_title}' WHERE music_id='{$music_id}' && music_user_id='{$this->user_id}' LIMIT 1";
		$resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
		
		return ( $database->database_affected_rows($resource) ? $music_title : FALSE );
	}
  
  
  
  // gets the skin info
	function skin_info($skin_id=NULL, $skin_title=NULL)
  {
		global $database;
    
    if( !$skin_id && !$skin_title )
      $skin_id = 1;
    
    $sql = "
      SELECT
        *
      FROM
        se_xspfskins
      WHERE
    ";
    
    if( $skin_id ) $sql .= "
      xspfskin_id='$skin_id'
    ";
    
    if( $skin_id && $skin_title ) $sql .= " || ";
    
    if( $skin_title ) $sql .= "
      xspfskin_title='$skin_title'
    ";
    
    $sql .= "
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    
    return ( $database->database_num_rows($resource) ? $database->database_fetch_assoc($resource) : FALSE );
  }
  
  
  
	//  returns the profile settings from the specified user
	function profile_settings()
  {
		global $database;
		
    // GET USER SETTINGS
    $sql = "
      SELECT
        usersetting_music_profile_autoplay,
        usersetting_music_site_autoplay,
        usersetting_xspfskin_id
      FROM
        se_usersettings
      WHERE
        usersetting_user_id='{$this->user_id}'
      LIMIT
        1
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
		$profile_info = $database->database_fetch_assoc($resource);
		
    // GET SKIN INFO
    $skin_id = ( !empty($profile_info['usersetting_xspfskin_id']) ? $profile_info['usersetting_xspfskin_id'] : 1 );
		
    $sql = "
      SELECT
        *
      FROM
        se_xspfskins
      WHERE
        xspfskin_id='$skin_id'
    ";
    
    $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
    $skin_info = $database->database_fetch_assoc($resource);
		
		return array_merge($profile_info, $skin_info);
	}
  
  
  
	//  returns a list of all the skins
	function music_skin_list()
  {
		global $database;
		
		$skins = $database->database_query("SELECT * FROM se_xspfskins");
		
		$skin_array = array();
		while( $skinlist=$database->database_fetch_assoc($skins) )
      $skin_array[] = $skinlist;
    
		return $skin_array;
	}
}







/***********************************************************
* Class:       ID3
* Version:     1.0
* Date:        Janeiro 2004
* Author:      Tadeu F. Oliveira
* Contact:     tadeu_fo@yahoo.com.br
* Use:         Extract ID3 Tag information from mp3 files
************************************************************/


class ID3
{
  // full path to the file the sugestion is that this path should be a
  // relative path
  var $file_name;
  
  // array with ID3 tags extracted from the file
  var $tags;
  
  // keep the number of the last error ocurred
  var $last_error_num = 0;
  
  // the number of elements at the tags array
  var $tags_count = 0;
  
  
  
  /*********************/
  /**private functions**/
  /*********************/
  function hex2bin($data)
  {
    $len = strlen($data);
    for($i=0;$i<$len;$i+=2)
      $newdata .= pack("C",hexdec(substr($data,$i,2)));
    return $newdata;
  }
  
  
  
  function get_frame_size($fourBytes)
  {
    $tamanho[0] = str_pad(base_convert(substr($fourBytes,0,2),16,2),7,0,STR_PAD_LEFT);
    $tamanho[1] = str_pad(base_convert(substr($fourBytes,2,2),16,2),7,0,STR_PAD_LEFT);
    $tamanho[2] = str_pad(base_convert(substr($fourBytes,4,2),16,2),7,0,STR_PAD_LEFT);
    $tamanho[3] = str_pad(base_convert(substr($fourBytes,6,2),16,2),7,0,STR_PAD_LEFT);
    $total      = $tamanho[0].$tamanho[1].$tamanho[2].$tamanho[3];
    $tamanho[0] = substr($total,0,8);
    $tamanho[1] = substr($total,8,8);
    $tamanho[2] = substr($total,16,8);
    $tamanho[3] = substr($total,24,8);
    $total      = $tamanho[0].$tamanho[1].$tamanho[2].$tamanho[3];
    $total      = base_convert($total,2,10);
    return $total;
  }
  
  
  
  function extractTags($text, &$tags)
  {
    $size = -1;
    while( strlen($text) && $size )
    {
      //while there are tags to read and they have a meaning
      //while existem tags a serem tratadas e essas tags tem conteudo
      $ID    = substr($text,0,4);
      $aux   = substr($text,4,4);
      $aux   = bin2hex($aux);
      $size  = $this->get_frame_size($aux);
      $flags = substr($text,8,2);
      $info  = substr($text,11,$size-1);
      if( $size )
      {
        $tags[$ID] = $info;
        $this->tags_count++;
      }
      $text = substr($text,10+$size,strlen($text));
    }
  }
  
  
  
  /********************/
  /**public functions**/
  /********************/
  /**Constructor**/

  function ID3($file_name)
  {
    $this->file_name = $file_name;
    $this->last_error_num = 0;
  }
  
  
  
  /**Read the file and put the TAGS
  content on $this->tags array**/
  function getInfo()
  {
    if( $this->file_name )
    {
      $mp3 = @fopen($this->file_name,"r");
      $header = @fread($mp3,10);
      if (!$header)
      {
        $this->last_error_num = 2;
        return false;
        die();
      }
      if( substr($header,0,3)!="ID3" )
      {
        $this->last_error_num = 3;
        return false;
        die();
      }
      $header   = bin2hex($header);
      $version  = base_convert(substr($header,6,2),16,10).".".base_convert(substr($header,8,2),16,10);
      $flags    = base_convert(substr($header,10,2),16,2);
      $flags    = str_pad($flags,8,0,STR_PAD_LEFT);
      if ($flags[7] == 1){
        //echo('with Unsynchronisation<br>');
      }
      if ($flags[6] == 1){
        //echo('with Extended header<br>');
      }
      if ($flags[5] == 1){
        //Esperimental tag
        $this->last_error_num = 4;
        return false;
        die();
      }
      $total = $this->get_frame_size(substr($header,12,8));
      $text = @fread($mp3,$total);
      fclose($mp3);
      $this->extractTags($text,$this->tags);
    }
    
    //file not set
    else
    {
      $this->last_error_num = 1;
      return false;
      die();
    }
    return true;
  }
  
  
  
  /*************
  *   PUBLIC
  * Functions to get information
  * from the ID3 tag
  **************/
  
  function getArtist()
  {
    if(array_key_exists('TPE1',$this->tags) )
    {
      return $this->tags['TPE1'];
    }
    else
    {
      $this->last_error_num = 5;
      return false;
    }
  }
  
  
  
  function getTrack()
  {
    if( array_key_exists('TRCK', $this->tags) )
    {
      return $this->tags['TRCK'];
    }
    else
    {
      $this->last_error_num = 5;
      return false;
    }
  }
  
  
  
  function getTitle()
  {
    if( array_key_exists('TIT2', $this->tags) )
    {
      return $this->tags['TIT2'];
    }
    else
    {
      $this->last_error_num = 5;
      return false;
    }
  }
  
  
  
  function getAlbum()
  {
    if( array_key_exists('TALB', $this->tags) )
    {
      return $this->tags['TALB'];
    }
    else
    {
      $this->last_error_num = 5;
      return false;
    }
  }
  
  
  
  function getYear()
  {
    if( array_key_exists('TYER', $this->tags) )
    {
      return $this->tags['TYER'];
    }
    else
    {
      $this->last_error_num = 5;
      return false;
    }
  }
  
  
  
  function getGender()
  {
    if( array_key_exists('TCON',$this->tags) )
    {
      return $this->tags['TCON'];
    }
    else
    {
      $this->last_error_num = 5;
      return false;
    }
  }
}

?>