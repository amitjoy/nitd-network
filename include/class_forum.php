<?php


//  THIS CLASS CONTAINS FORUM-RELATED METHODS 
//
//  METHODS IN THIS CLASS:
//    se_forum()
//    forum_is_moderator()
//    forum_permission()
//    forum_list()
//    forum_topic_list()
//    forum_topic_new()
//    forum_post_new()
//    forum_excerpt()
//    forum_bbcode_parse_clean()
//    forum_bbcode_parse_view()


defined('SE_PAGE') or exit();





class se_forum
{
	// INITIALIZE VARIABLES
	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT
	var $error_message;		// CONTAINS RELEVANT ERROR MESSAGE










	// THIS METHOD SETS INITIAL VARS
	// OUTPUT: 
	function se_forum() {
	  global $database;

	} // END se_forum() METHOD








	// THIS METHOD CHECKS TO SEE IF A USER IS A MODERATOR
	// INPUT: $user_id REPRESENTING THE USER'S ID TO CHECK ABOUT MODERATOR STATUS
	//	  $forum_id (OPTIONAL) REPRESENTING THE FORUM ID TO CHECK FOR MODERATOR STATUS IN
	// OUTPUT: TRUE/FALSE
	function forum_is_moderator($user_id, $forum_id = 0) {
	  global $database;

	  if($user_id == 0) { return false; }

	  if($forum_id == 0) {
	    if($database->database_num_rows($database->database_query("SELECT NULL FROM se_forummoderators WHERE forummoderator_user_id='$user_id'")) == 0) {
	      return false;
	    } else {
	      return true;
	    }
	  } else {
	    if($database->database_num_rows($database->database_query("SELECT NULL FROM se_forummoderators WHERE forummoderator_user_id='$user_id' AND forummoderator_forum_id='$forum_id'")) == 0) {
	      return false;
	    } else {
	      return true;
	    }
	  }

	} // END forum_is_moderator() METHOD








	// THIS METHOD RETURNS AN ARRAY OF PERMISSIONS SETTINGS FOR A USER IN A GIVEN FORUM
	// INPUT: $forum_id REPRESENTING THE FORUM ID OF THE FORUM TO RETRIEVE PERMISSIONS FOR
	// OUTPUT: AN ARRAY OF FORUM PERMISSIONS FOR THE USER
	function forum_permission($forum_id) {
	  global $database, $user, $setting;

	  // SET LEVEL ID
	  if($user->user_exists) { $level_id = $user->level_info[level_id]; } else { $level_id = 0; }

	  $permission['is_moderator'] = false;
	  $permission['allowed_to_view'] = false;
	  $permission['allowed_to_post'] = false;
	  $permission['allowed_to_editall'] = false;
	  $permission['allowed_to_deleteall'] = false;
	  $permission['allowed_to_move'] = false;
	  $permission['allowed_to_close'] = false;
	  $permission['allowed_to_stick'] = false;
	

	  // CHECK IF MODERATOR
	  if($user->user_exists && $this->forum_is_moderator($user->user_info[user_id], $forum_id)) {
	    $permission['is_moderator'] = true;
	    $permission['allowed_to_view'] = true;
	    $permission['allowed_to_post'] = true;
	    if(substr($setting['setting_forum_modprivs'], 0, 1) == 1) { $permission['allowed_to_editall'] = true; }
	    if(substr($setting['setting_forum_modprivs'], 1, 1) == 1) { $permission['allowed_to_deleteall'] = true; }
	    if(substr($setting['setting_forum_modprivs'], 2, 1) == 1) { $permission['allowed_to_move'] = true; }
	    if(substr($setting['setting_forum_modprivs'], 3, 1) == 1) { $permission['allowed_to_close'] = true; }
	    if(substr($setting['setting_forum_modprivs'], 4, 1) == 1) { $permission['allowed_to_stick'] = true; }

	  // IF NOT MODERATOR, CHECK ABOUT PERMISSIONS
	  } else {

	    $forumlevel_query = $database->database_query("SELECT forumlevel_post FROM se_forumlevels WHERE forumlevel_forum_id='$forum_id' AND forumlevel_level_id='$level_id'");
	    if($database->database_num_rows($forumlevel_query) == 1) {
	      $permission['allowed_to_view'] = true;
	      $forumlevel_info = $database->database_fetch_assoc($forumlevel_query);
	      if($forumlevel_info[forumlevel_post]) { $permission['allowed_to_post'] = true; }
	    }

	  }

	  return $permission;

	} // END forum_permission() METHOD









	// THIS METHOD LISTS ALL FORUM CATEGORIES AND FORUMS
	// INPUT: $cat_id (OPTIONAL) REPRESENTING THE FORUM CATEGORY TO RETRIEVE FORUMS FROM
	// OUTPUT: AN ARRAY OF CATEGORIES WITH FULL FORUM AND MODERATOR INFO
	function forum_list($cat_id = 0) {
	  global $database, $user;

	  // SET LEVEL ID
	  if($user->user_exists) { $level_id = $user->level_info[level_id]; } else { $level_id = 0; }

	  // GET FORUM CATEGORIES
	  if($cat_id == 0) { $sql = "SELECT * FROM se_forumcats ORDER BY forumcat_order"; } else { $sql = "SELECT * FROM se_forumcats WHERE cat_id='$cat_id'"; }
	  $forumcats = $database->database_query($sql);
	  while($forumcat_info = $database->database_fetch_assoc($forumcats)) {


	    // GET FORUMS
	    $forum_array = Array();
	    $forums = $database->database_query("SELECT * FROM se_forums WHERE forum_forumcat_id='$forumcat_info[forumcat_id]' ORDER BY forum_order");
	    while($forum_info = $database->database_fetch_assoc($forums)) {

	      $show_forum = false;
	      if($database->database_num_rows($database->database_query("SELECT NULL FROM se_forumlevels WHERE forumlevel_forum_id='$forum_info[forum_id]' AND forumlevel_level_id='$level_id'")) == 1) {
		$show_forum = true;
	      } elseif($user->user_exists && $this->forum_is_moderator($user->user_info[user_id], $forum_info[forum_id])) {
		$show_forum = true;
	      }

	      if($show_forum) {
	        SE_Language::_preload_multi($forum_info[forum_title], $forum_info[forum_desc]);

	        // GET MODERATORS
	        $mod_array = Array();
	        $mod_array_id = Array();
	        $mods = $database->database_query("SELECT se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname FROM se_forummoderators LEFT JOIN se_users ON se_forummoderators.forummoderator_user_id=se_users.user_id WHERE se_forummoderators.forummoderator_forum_id='$forum_info[forum_id]' AND se_users.user_id IS NOT NULL");
	        while($user_info = $database->database_fetch_assoc($mods)) {

	          $mod_user = new se_user();
	          $mod_user->user_info[user_id] = $user_info[user_id];
	          $mod_user->user_info[user_username] = $user_info[user_username];
	          $mod_user->user_info[user_fname] = $user_info[user_fname];
	          $mod_user->user_info[user_lname] = $user_info[user_lname];
	          $mod_user->user_displayname();

	          $mod_array[] = $mod_user;
	        }
	        $forum_info[forum_mods] = $mod_array;

	 	// GET LAST POST
		$lastpost = $database->database_query("SELECT se_forumposts.forumpost_id, se_forumposts.forumpost_date, se_forumposts.forumpost_authoruser_id, se_forumtopics.forumtopic_id, se_forumtopics.forumtopic_subject, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname, se_users.user_photo FROM se_forumposts LEFT JOIN se_forumtopics ON se_forumposts.forumpost_forumtopic_id=se_forumtopics.forumtopic_id LEFT JOIN se_users ON se_forumposts.forumpost_authoruser_id=se_users.user_id WHERE se_forumtopics.forumtopic_forum_id='{$forum_info[forum_id]}' AND se_forumposts.forumpost_deleted='0' ORDER BY se_forumposts.forumpost_id DESC LIMIT 1");
		if($database->database_num_rows($lastpost) == 1) {
		  $lastpost_info = $database->database_fetch_assoc($lastpost);

		  $forum_info[lastpost] = true;

		  // GET POST AUTHOR
		  $author = new se_user();
		  if($lastpost_info['forumpost_authoruser_id'] != $lastpost_info['user_id']) {
		    $author->user_exists = false;
		  } else {
		    $author->user_exists = true;
		    $author->user_info['user_id'] = $lastpost_info['user_id'];
		    $author->user_info['user_username'] = $lastpost_info['user_username'];
		    $author->user_info['user_fname'] = $lastpost_info['user_fname'];
		    $author->user_info['user_lname'] = $lastpost_info['user_lname'];
		    $author->user_info['user_photo'] = $lastpost_info['user_photo'];
		    $author->user_displayname();
		  }
		  $lastpost_info[author] = $author;

		  $forum_info[lastpost_info] = $lastpost_info;

		} else {
		  $forum_info[lastpost] = false;
		}
    
		if($forum_info[lastpost]) {
		  $forum_info[is_read] = false;
		  if(isset($_COOKIE["forum_{$user->user_info[user_id]}_$forum_info[forum_id]"])) {
		    if($_COOKIE["forum_{$user->user_info[user_id]}_$forum_info[forum_id]"] >= $forum_info[lastpost_info][forumpost_date]) {
		      $forum_info[is_read] = true;
		    }
		  }
		} else {
		  $forum_info[is_read] = true;
		}

	        $forum_array[] = $forum_info;
	      }
	    }

	    SE_Language::_preload($forumcat_info[forumcat_title]);
	    $forumcat_info[forums] = $forum_array;
	    $forumcat_array[] = $forumcat_info;

	  }

	  return $forumcat_array;

	} // END forum_list() METHOD








	// THIS METHOD LISTS ALL TOPICS IN A FORUM
	// INPUT: $forum_id REPRESENTING THE FORUM ID OF THE FORUM TO RETRIEVE TOPICS FROM
	//	  $start REPRESENTING THE TOPIC TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF TOPICS TO RETURN
	// OUTPUT: AN ARRAY OF TOPIC INFORMATION
	function forum_topic_list($forum_id, $start, $limit) {
	  global $database, $user;

	  // GET TOPICS
	  $sql = "SELECT se_forumlogs.forumlog_date, se_forumtopics.* FROM se_forumtopics LEFT JOIN se_forumlogs ON (se_forumtopics.forumtopic_id=se_forumlogs.forumlog_forumtopic_id AND se_forumlogs.forumlog_user_id='{$user->user_info[user_id]}') WHERE forumtopic_forum_id='$forum_id' ORDER BY forumtopic_sticky DESC, forumtopic_date DESC LIMIT $start, $limit";
	  $forumtopics = $database->database_query($sql);
	  $forumtopic_array = Array();
	  while($forumtopic_info = $database->database_fetch_assoc($forumtopics)) {

	    // GET LAST POST
	    $lastpost = $database->database_query("SELECT se_forumposts.forumpost_id, se_forumposts.forumpost_date, se_forumposts.forumpost_excerpt, se_forumposts.forumpost_authoruser_id, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname, se_users.user_photo FROM se_forumposts LEFT JOIN se_users ON se_forumposts.forumpost_authoruser_id=se_users.user_id WHERE se_forumposts.forumpost_forumtopic_id='{$forumtopic_info[forumtopic_id]}' AND se_forumposts.forumpost_deleted='0' ORDER BY se_forumposts.forumpost_id DESC LIMIT 1");
	    if($database->database_num_rows($lastpost) == 1) {
	      $lastpost_info = $database->database_fetch_assoc($lastpost);

	      $forumtopic_info[lastpost] = true;

	      // GET POST AUTHOR
	      $author = new se_user();
	      if($lastpost_info['forumpost_authoruser_id'] != $lastpost_info['user_id']) {
	        $author->user_exists = false;
	      } else {
	        $author->user_exists = true;
	        $author->user_info['user_id'] = $lastpost_info['user_id'];
	        $author->user_info['user_username'] = $lastpost_info['user_username'];
	        $author->user_info['user_fname'] = $lastpost_info['user_fname'];
	        $author->user_info['user_lname'] = $lastpost_info['user_lname'];
	        $author->user_info['user_photo'] = $lastpost_info['user_photo'];
	        $author->user_displayname();
	      }
	      $lastpost_info[author] = $author;

	      $forumtopic_info[lastpost_info] = $lastpost_info;

	    } else {
	      $forumtopic_info[lastpost] = false;
	    }

	    if($forumtopic_info[forumlog_date] == NULL || $forumtopic_info[forumlog_date] < $forumtopic_info[forumtopic_date]) {
	      $forumtopic_info[is_new] = true;
	    } else {
	      $forumtopic_info[is_new] = false;
	    }

	    $forumtopic_array[] = $forumtopic_info;
	  }

	  return $forumtopic_array;

	} // END forum_topic_list() METHOD








	// THIS METHOD CREATES A NEW TOPIC
	// INPUT: $forum_id REPRESENTING THE FORUM ID OF THE FORUM TO CREATE TOPIC IN
	//	  $forum_title REPRESENTING THE TITLE OF THE FORUM
	//	  $topic_title REPRESENTING THE TOPIC TITLE
	//	  $post_body REPRESENTING THE POST'S BODY
	// OUTPUT:
	function forum_topic_new($forum_id, $forum_title, $topic_title, $post_body) {
	  global $database, $user, $actions;

	  $is_error = 0;
	  $nowdate = time();

	  // SET ERRORS
	  if(trim($topic_title) == "") { $is_error = 6000066; }
	  if(trim(str_replace("&lt;p&gt;", "", str_replace("&lt;/p&gt;", "", $post_body))) == "") { $is_error = 6000067; }

	  // IF NO ERROR, CREATE TOPIC
	  if($is_error == 0) {

	    // CREATE EXCERPT
	    $excerpt = $this->forum_excerpt($this->forum_bbcode_parse_clean($post_body));

	    // INSERT INTO FORUM TOPIC TABLE
	    $database->database_query("INSERT INTO se_forumtopics (forumtopic_forum_id, forumtopic_creatoruser_id, forumtopic_date, forumtopic_subject, forumtopic_excerpt) VALUES ('$forum_id', '{$user->user_info[user_id]}', '$nowdate', '$topic_title', '$excerpt')");
 	    $forumtopic_id = $database->database_insert_id();

	    // ADD POST
	    $this->forum_post_new($forum_id, $forumtopic_id, $topic_title, $post_body, true);

	    // ADD ACTION
	    if($user->user_exists) {
	      $actions->actions_add($user, "forumtopic", Array($user->user_info['user_username'], $user->user_displayname, $forum_id, SE_Language::get($forum_title), $forumtopic_id, $topic_title, $excerpt), Array(), 0, false, 'forum', $forum_id, 0);
	    }

	  }

	  return Array('is_error' => $is_error, 'topic_id' => $forumtopic_id);

	} // END forum_topic_new() METHOD








	// THIS METHOD CREATES A NEW POST
	// INPUT: $forum_id REPRESENTING THE FORUM ID OF THE FORUM TO CREATE TOPIC IN
	//	  $topic_id REPRESENTING THE ID OF THE TOPIC TO ADD THE POST TO
	//	  $topic_title REPRESENTING THE TITLE OF THE TOPIC
	//	  $post_body REPRESENTING THE POST'S BODY
	//	  $new_topic (OPTIONAL) REPRESENTING WHETHER THIS IS THE FIRST POST IN THE NEW TOPIC
	// OUTPUT:
	function forum_post_new($forum_id, $topic_id, $topic_title, $post_body, $new_topic = false) {
	  global $database, $user, $actions, $notify, $url;

	  $is_error = 0;
	  $nowdate = time();
	
	  // SET ERRORS
	  if(trim(str_replace("&lt;p&gt;", "", str_replace("&lt;/p&gt;", "", $post_body))) == "") { $is_error = 6000067; }

	  // IF NO ERROR, ADD POST
	  if($is_error == 0) {

	    // UPLOAD FORUM MEDIA
	    $forummedia_id = $this->forum_media_new($topic_id);

	    // CLEAN, CENSOR, ETC
	    $post_body = $this->forum_bbcode_parse_clean($post_body);

	    // CREATE EXCERPT
	    $excerpt = $this->forum_excerpt($post_body);
   
	    // INSERT INTO FORUM POST TABLE
	    $database->database_query("INSERT INTO se_forumposts (forumpost_forumtopic_id, forumpost_authoruser_id, forumpost_date, forumpost_excerpt, forumpost_body, forumpost_forummedia_id) VALUES ('$topic_id', '{$user->user_info[user_id]}', '$nowdate', '$excerpt', '$post_body', '$forummedia_id')") or die(mysql_error());
	    $forumpost_id = $database->database_insert_id();

	    // IF NEW TOPIC, UPDATE FORUM TABLE
	    if($new_topic) {
	      $database->database_query("UPDATE se_forums SET forum_totaltopics=forum_totaltopics+1 WHERE forum_id='$forum_id'");
	    
	    // IF REPLY, UPDATE FORUM AND FORUMTOPIC TABLE
	    } else {
	      $database->database_query("UPDATE se_forums SET forum_totalreplies=forum_totalreplies+1 WHERE forum_id='$forum_id'");
	      $database->database_query("UPDATE se_forumtopics SET forumtopic_date='$nowdate', forumtopic_totalreplies=forumtopic_totalreplies+1 WHERE forumtopic_id='$topic_id' AND forumtopic_forum_id='$forum_id'");

	      // ADD ACTION
	      if($user->user_exists) {
	        $actions->actions_add($user, "forumpost", Array($user->user_info['user_username'], $user->user_displayname, $forum_id, $topic_id, $topic_title, $forumpost_id, $excerpt), Array(), 0, false, 'forum', $forum_id, 0);
	      }

	      // SEND NOTIFICATION
	      if($user->user_exists) { $poster = $user->user_displayname; } else { $poster = SE_Language::get(835); }

	      // SEND REPLY NOTIFICATION
	      $topic_starter = $database->database_fetch_assoc($database->database_query("SELECT se_forumposts.forumpost_authoruser_id FROM se_forumposts WHERE forumpost_forumtopic_id='{$topic_id}' ORDER BY forumpost_id ASC LIMIT 1"));
	      if($topic_starter['forumpost_authoruser_id'] != $user->user_info['user_id']) {
	        $starter = new se_user(Array($topic_starter['forumpost_authoruser_id']));
		if($starter->user_exists) {
	          $notifytype = $notify->notify_add($starter->user_info[user_id], 'forumreply', $topic_id, Array($forum_id, $topic_id, $forumpost_id), Array($topic_title));
	          $object_url = $url->url_base.vsprintf($notifytype[notifytype_url], Array($forum_id, $topic_id, $forumpost_id));
	          $starter->user_settings();
	          if($starter->usersetting_info['usersetting_notify_forumreply']) {
	            send_systememail("forumreply", $starter->user_info['user_email'], Array($starter->user_displayname, $poster, $topic_title, "<a href=\"$object_url\">$object_url</a>"));
	          }
	        }
	      }
	    }


	    // UPDATE USER'S TOTAL POSTS
	    $database->database_query("INSERT INTO se_forumusers (forumuser_user_id, forumuser_totalposts) VALUES ('{$user->user_info[user_id]}', 1) ON DUPLICATE KEY UPDATE forumuser_totalposts=forumuser_totalposts+1") or die(mysql_error());

	  }

	  return Array('is_error' => $is_error, 'post_id' => $forumpost_id);

	} // END forum_post_new() METHOD








	// THIS METHOD UPLOADS AN IMAGE FOR A POST
	// INPUT: $topic_id REPRESENTING THE ID OF THE TOPIC TO WHICH IMAGE IS BEING UPLOADED
	// OUTPUT: THE FORUMMEDIA ID OF UPLOADED FILE
	function forum_media_new($topic_id) {
	  global $database;

	  // SET KEY VARIABLES
	  $forummedia_id = 0;
	  $file_maxsize = 2048000;
	  $file_exts = Array('jpg', 'jpeg', 'gif', 'png', 'bmp');
	  $file_types = Array('image/jpeg', 'image/pjpeg', 'image/jpg', 'image/jpe', 'image/pjpg', 'image/x-jpeg', 'image/x-jpg', 'image/gif', 'image/x-gif', 'image/png', 'image/x-png', 'image/bmp');
	  $file_maxwidth = 650;
	  $file_maxheight = 1000;
    
	  // START NEW UPLOAD
	  $new_media = new se_upload();
	  $new_media->new_upload('post_media', $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);
    
	  // UPLOAD AND RESIZE PHOTO IF NO ERROR
	  if($new_media->is_error == 0) {
	  
	    // INSERT ROW INTO MEDIA TABLE
	    $database->database_query("INSERT INTO se_forummedia (forummedia_forumtopic_id) VALUES ('{$topic_id}')");
	    $forummedia_id = $database->database_insert_id();
      
	    // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
	    if($new_media->is_image == 1) {

	      // MAKE SURE SUBDIRECTORY EXISTS
	      $subdir = './uploads_forum/'.$topic_id.'/';
	      if(!is_dir($subdir)) { 
		mkdir($subdir, 0777);
		chmod($subdir, 0777);
	      }
	      $file_dest = $subdir.$forummedia_id.".jpg";
        
	      // UPLOAD FILE
	      $new_media->upload_photo($file_dest);
	      $file_ext = "jpg";
	      $file_filesize = filesize($file_dest);

	    } else {

	      // MAKE SURE SUBDIRECTORY EXISTS
	      $subdir = './uploads_forum/'.$topic_id.'/';
	      if(!is_dir($subdir)) { 
		mkdir($subdir, 0777);
		chmod($subdir, 0777);
	      }
	      $file_dest = $subdir.$forummedia_id.".".$new_media->file_ext;
        
	      $new_media->upload_file($file_dest);
	      $file_ext = $new_media->file_ext;
	      $file_filesize = filesize($file_dest);
	    }
      
	    // DELETE FROM DATABASE IF ERROR
	    if($new_media->is_error) {
	      $database->database_query("DELETE FROM se_forummedia WHERE forummedia_id='$forummedia_id' AND forummedia_forumtopic_id='$topic_id'");
	      @unlink($file_dest);
	      $forummedia_id = 0;

	    // UPDATE ROW IF NO ERROR
	    } else {
	      $database->database_query("UPDATE se_forummedia SET forummedia_ext='$file_ext', forummedia_filesize='$file_filesize' WHERE forummedia_id='$forummedia_id' AND forummedia_forumtopic_id='$topic_id'");
	    }

	  }

	  return $forummedia_id;

	} // END forum_media_new() METHOD




	// THIS METHOD CREATES AN EXCERPT FROM TEXT
	// INPUT: $body REPRESENTING THE TEXT TO RETRIEVE THE EXCERPT FROM
	//	  $chars (OPTIONAL) REPRESENTING THE NUMBER OF CHARACTERS FOR THE EXCERPT
	// OUTPUT: A STRING WITHOUT HTML REPRESENTING THE EXCERPT FOR THE BODY OF TEXT
	function forum_excerpt($body, $chars = 100) {
	  global $database;

	  // HTMLSPECIALCHARS_DECODE
	  $body = htmlspecialchars_decode($body, ENT_QUOTES);

	  // GET RID OF EXCESS WHITE SPACE
	  $body = preg_replace('/\s\s+/', ' ', $body);

	  // GET RID OF HTML
	  $body = strip_tags($body);

	  // GET RID OF BBCODE (QUOTES AND SUCH)
	  $body = $this->forum_quote_regex($body);

	  // TRUNCATE TO $chars
	  $body = substr($body, 0, $chars);

	  // HTMLSPECIALCHARS
	  $body = htmlspecialchars($body, ENT_QUOTES);

	  // RETURN EXCERPT
	  return $body;

	} // END forum_excerpt() METHOD








	// RECURSIVE REGEX ESCAPE
	// INPUT: 
	// OUTPUT:
	function forum_quote_regex($string) {
	  
	  $regex = '/\[quote\=[^\]]*?\](.*?\[\/quote\])/is';

	  while(preg_match($regex, $string, $matches)) {
	    if(preg_match($regex, $matches[1], $new_matches)) {
	      $string = str_replace($matches[1], $this->forum_quote_regex($matches[1]), $string);
	    } else {
	      $string = str_replace($matches[0], ' ', $string);
	    }

	  }

	  return $string;


	} // END forum_excerpt() METHOD








	// THIS METHOD CLEANS AND PARSES A STRING FOR BBCODE
	// INPUT: $string REPRESENTING THE STRING TO PARSE
	// OUTPUT: A PARSED STRING
	function forum_bbcode_parse_clean($string) {
    
	  // FIX LINE BREAKS
	  $string = htmlspecialchars_decode($string, ENT_QUOTES);
	  $string = censor($string);
	  if (!preg_match('/<[^>]+>/', $string))
		$string = preg_replace(array("/\\r\\n/", "/\\r/", "/\\n/"), array("[br]", "[br]", "[br]"), $string);
    
	  // CLEAN HTML
	  $allowed_html = "ol,ul,li,strong,em,u,strike,p,br,a,embed,img";
	  $string = cleanHTML($string, $allowed_html, Array("style"));
    
	  // FIX LINE BREAKS
	  $string = str_replace("[br]", "<br>", $string);
	  $string = preg_replace('/\s+<br>\s+/i', '<br>', $string);
	  $string = preg_replace('/(<br>){3,}/is', '<br><br>', $string);
   
	  // RE-ENCODE
	  $string = htmlspecialchars($string, ENT_QUOTES);

	  return $string;

	} // END forum_bbcode_parse_clean() METHOD








	// THIS METHOD CLEANS AND PARSES A STRING FOR BBCODE
	// INPUT: $string REPRESENTING THE STRING TO PARSE
	// OUTPUT: A PARSED STRING
	function forum_bbcode_parse_view($string) {

	  // DO [quote]
	  $open_quote = preg_match_all('/\[quote\=(.*?)\]/i', $string, $matches);
	  $close_quote = preg_match_all('/\[\/quote\]/i', $string, $matches);
	  $total_tags = ( $open_quote>$close_quote ? $close_quote : $open_quote );
    
	  if($total_tags) {
	    $string = preg_replace('/\[quote\=(.*?)\]/i', "<div class='forum_quote'><div>".SE_Language::get(6000117, Array('$1'))."</div>", $string, $total_tags);
	    $string = strrev(preg_replace('/\]etouq\/\[/i', ">vid/<", strrev($string), $total_tags));
	  }
    
	  return $string;

	} // END forum_bbcode_parse_view() METHOD


}


?>