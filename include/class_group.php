<?php


//  THIS CLASS CONTAINS GROUP-RELATED METHODS 
//  TODO!: group_media_delete 
//
//  METHODS IN THIS CLASS:
//    se_group()
//    group_total()
//    group_list()
//    group_create()
//    group_delete()
//    group_delete_selected()
//    group_lastupdate()
//    group_privacy_max()
//    group_member_total()
//    group_member_list()
//    group_dir()
//    group_photo()
//    group_photo_upload()
//    group_photo_delete()
//    group_media_upload()
//    group_media_space()
//    group_media_total()
//    group_media_list()
//    group_topic_list()
//    group_topic_total()
//    group_post_list()
//    group_post_total()
//    group_post_bbcode_parse_clean()
//    group_post_bbcode_parse_view()


defined('SE_PAGE') or exit();





class se_group
{
	// INITIALIZE VARIABLES
	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT
	var $error_message;		// CONTAINS RELEVANT ERROR MESSAGE

	var $user_id;			// CONTAINS THE USER ID OF THE USER WHOSE GROUPS WE ARE EDITING
	var $user_rank;			// CONTAINS THE USER'S ASSOCIATION TO THE GROUP (LEADER - 2, OFFICER - 1, MEMBER - 0, NOT AFFILIATED - -1)

	var $group_exists;		// DETERMINES WHETHER THE GROUP HAS BEEN SET AND EXISTS OR NOT

	var $group_info;		// CONTAINS THE GROUP INFO OF THE GROUP WE ARE EDITING
	var $groupvalue_info;		// CONTAINS THE GROUPVALUE INFO OF THE GROUP WE ARE EDITING
	var $groupowner_level_info;	// CONTAINS THE GROUP OWNER'S LEVEL INFO FOR THE GROUP WE ARE EDITING
	var $groupmember_info;		// CONTAINS THE USER'S ASSOCIATION TO THE GROUP FROM GROUPMEMBER TABLE

	var $moderation_privacy;	// CONTAINS THE PRIVACY LEVEL THAT IS ALLOWED TO MODERATE FOR THIS USER








	// THIS METHOD SETS INITIAL VARS
	// INPUT: $user_id (OPTIONAL) REPRESENTING THE USER ID OF THE USER WHOSE GROUPS WE ARE CONCERNED WITH
	//	  $group_id (OPTIONAL) REPRESENTING THE GROUP ID OF THE GROUP WE ARE CONCERNED WITH
	// OUTPUT: 
	function se_group($user_id = 0, $group_id = 0)
  {
	  global $database;
    
	  $this->user_id = $user_id;
	  $this->group_exists = 0;
	  $this->user_rank = -1;
	  $this->moderation_privacy = 3;
    
	  if( $group_id )
    {
	    $group = $database->database_query("SELECT * FROM se_groups WHERE group_id='{$group_id}' LIMIT 1");
	    if($database->database_num_rows($group) == 1)
      {
	      $this->group_exists = 1;
	      $this->group_info = $database->database_fetch_assoc($group);
	      $this->groupvalue_info = $database->database_fetch_assoc($database->database_query("SELECT * FROM se_groupvalues WHERE groupvalue_group_id='{$group_id}'"));
	      $this->groupowner_level_info = $database->database_fetch_assoc($database->database_query("SELECT se_levels.* FROM se_users LEFT JOIN se_levels ON se_users.user_level_id=se_levels.level_id WHERE se_users.user_id='{$this->group_info['group_user_id']}'"));
        
	      if( $this->user_id )
        {
	        $groupmember = $database->database_query("SELECT groupmember_id, groupmember_rank, groupmember_status, groupmember_approved FROM se_groupmembers WHERE groupmember_user_id='{$this->user_id}' AND groupmember_group_id='{$group_id}'");
	        if($database->database_num_rows($groupmember) == 1)
          {
	          $this->groupmember_info = $database->database_fetch_assoc($groupmember);
            if($this->groupmember_info['groupmember_status'] == 1)
            {
              $this->user_rank = $this->groupmember_info['groupmember_rank'];
            }
	        }
	      }
	    }
	  }

	} // END se_group() METHOD








	// THIS METHOD RETURNS THE TOTAL NUMBER OF GROUPS
	// INPUT: $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $group_details (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER TO RETRIEVE TOTAL MEMBERS, GROUP LEADER, ETC
	// OUTPUT: AN INTEGER REPRESENTING THE NUMBER OF GROUPS
	function group_total($where = "", $group_details = 0)
  {
	  global $database;
    
	  // BEGIN QUERY
	  $group_query = "
      SELECT
        NULL
    ";
    
	  // SELECT RELEVANT GROUP DETAILS IF NECESSARY
	  if( $group_details ) $group_query .= ",
        se_groups.group_totalmembers AS num_members,
        se_users.user_id,
        se_users.user_username
    ";
    
	  // IF USER ID NOT EMPTY, GET USER AS MEMBER
	  if( $this->user_id ) $group_query .= ",
        se_groupmembers.groupmember_rank
    ";
    
	  // CONTINUE QUERY
	  $group_query .= "
      FROM
        se_groups
    ";
    
	  // IF USER ID NOT EMPTY, SELECT FROM GROUPMEMBER TABLE
	  if( $this->user_id ) $group_query .= "
      LEFT JOIN
        se_groupmembers
        ON se_groupmembers.groupmember_group_id=se_groups.group_id
    ";
    
	  // CONTINUE QUERY IF NECESSARY
	  if( $group_details ) $group_query .= "
    /*
      LEFT JOIN
        se_groupmembers AS member_table
        ON se_groups.group_id=member_table.groupmember_group_id AND member_table.groupmember_status='1' AND member_table.groupmember_approved='1'
      */
      LEFT JOIN
        se_users
        ON se_groups.group_user_id=se_users.user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if($where != "" | $this->user_id != 0) { $group_query .= " WHERE"; }
    
	  // IF USER ID NOT EMPTY, MAKE SURE USER IS A MEMBER
	  if($this->user_id != 0) { $group_query .= " se_groupmembers.groupmember_user_id='{$this->user_id}'"; }
    
	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 & $where != "") { $group_query .= " AND"; }
    
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if($where != "") { $group_query .= " $where"; }
    
	  // ADD GROUP BY
	  //$group_query .= " GROUP BY group_id";
    
	  // RUN QUERY
	  $group_total = $database->database_num_rows($database->database_query($group_query));
	  return $group_total;
	}
  
  // END group_total() METHOD








	// THIS METHOD RETURNS AN ARRAY OF GROUPS
	// INPUT: $start REPRESENTING THE GROUP TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF GROUPS TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $group_details (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER TO RETRIEVE TOTAL MEMBERS, GROUP LEADER, ETC
	// OUTPUT: AN ARRAY OF GROUPS
  
	function group_list($start, $limit, $sort_by = "group_id DESC", $where = "", $group_details = 0)
  {
	  global $database, $user;
    
	  // BEGIN QUERY
	  $group_query = "
      SELECT
        se_groups.*
    ";
    
	  // SELECT RELEVANT GROUP DETAILS IF NECESSARY
	  if( $group_details ) $group_query .= ",
        se_groups.group_totalmembers AS num_members,
        se_users.user_id,
        se_users.user_username,
        se_users.user_fname,
        se_users.user_lname,
        se_users.user_photo
    ";
    
	  // IF USER ID NOT EMPTY, GET USER AS MEMBER
	  if( $this->user_id ) $group_query .= ",
        se_groupmembers.groupmember_rank
    ";

	  // CONTINUE QUERY
	  $group_query .= "
      FROM
        se_groups
    ";

	  // IF USER ID NOT EMPTY, SELECT FROM GROUPMEMBER TABLE
	  if( $this->user_id ) $group_query .= "
      LEFT JOIN
        se_groupmembers
        ON se_groupmembers.groupmember_group_id=se_groups.group_id
    ";
    
	  // CONTINUE QUERY IF NECESSARY
	  if( $group_details ) $group_query .= "
      LEFT JOIN
        se_users
        ON se_groups.group_user_id=se_users.user_id
    ";
    
	  // ADD WHERE IF NECESSARY
	  if($where != "" || $this->user_id != 0) { $group_query .= " WHERE"; }
    
	  // IF USER ID NOT EMPTY, MAKE SURE USER IS A MEMBER
	  if($this->user_id != 0) { $group_query .= " se_groupmembers.groupmember_user_id='{$this->user_id}'"; }

	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 && $where != "") { $group_query .= " AND"; }

	  // ADD WHERE CLAUSE, IF NECESSARY
	  if($where != "") { $group_query .= " $where"; }

	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $group_query .= "
      /*
      GROUP BY
        group_id
      */
      ORDER BY
        {$sort_by}
      LIMIT
        {$start}, {$limit}
    ";
    
	  // RUN QUERY
	  $groups = $database->database_query($group_query);
    
	  // GET GROUPS INTO AN ARRAY
	  $group_array = Array();
	  while($group_info = $database->database_fetch_assoc($groups))
    {
	    // CREATE OBJECT FOR LEADER
	    if($user->user_info['user_id'] == $group_info['group_user_id'])
      {
	      $leader = $user;
	    }
      else
      {
	      $leader = new se_user();
	      $leader->user_exists = 1;
	      $leader->user_info['user_id'] = $group_info['user_id'];
	      $leader->user_info['user_username'] = $group_info['user_username'];
	      $leader->user_info['user_photo'] = $group_info['user_photo'];
	      $leader->user_info['user_fname'] = $group_info['user_fname'];
	      $leader->user_info['user_lname'] = $group_info['user_lname'];
	      $leader->user_displayname();
	    }
      
	    // CREATE OBJECT FOR GROUP
	    $group = new se_group($group_info['user_id']);
	    $group->group_exists = TRUE;
	    $group->group_info= $group_info;
      
	    // SET GROUP ARRAY
	    $group_array[] = Array(
        'group' => $group,
				'group_leader' => $leader,
				'group_rank' => $group_info['groupmember_rank'],
				'group_members' => $group_info['num_members']
      );
	  }
    
	  // RETURN ARRAY
	  return $group_array;
	}
  
  // END group_list() METHOD








	// THIS METHOD CREATES A NEW GROUP
	// INPUT: $group_title REPRESENTING THE GROUP TITLE
	//	  $group_desc REPRESENTING THE GROUP DESCRIPTION
	//	  $group_groupcat_id REPRESENTING THE GROUP CATEGORY ID
	//	  $group_approval REPRESENTING WHETHER THE LEADER MUST APPROVE MEMBERSHIP REQUESTS
	//	  $group_invite REPRESENTING WHETHER MEMBERS CAN INVITE FRIENDS TO JOIN
	//	  $group_search REPRESENTING WHETHER THE GROUP SHOULD BE SEARCHABLE
	//	  $group_privacy REPRESENTING THE PRIVACY OF THE GROUP
	//	  $group_comments REPRESENTING WHO CAN POST COMMENTS ON THE GROUP
	//	  $group_discussion REPRESENTING WHO CAN CREATE AND POST IN DISCUSSION TOPICS IN THIS GROUP
	//	  $group_upload REPRESENTING WHO CAN UPLOAD PHOTOS IN THIS GROUP
	//	  $groupalbum_tag REPRESENTING WHO CAN TAG PHOTOS IN THIS GROUP
	//	  $group_field_query REPRESENTING THE PARTIAL QUERY TO SAVE IN THE GROUP'S VALUE TABLE
	// OUTPUT: THE NEWLY CREATED GROUP'S GROUP ID
	function group_create($group_title, $group_desc, $group_groupcat_id, $group_approval, $group_invite, $group_search, $group_privacy, $group_comments, $group_discussion, $group_upload, $groupalbum_tag, $group_field_query) {
	  global $database;

	  $datecreated = time();

	  // ADD ROW TO GROUPS TABLE
	  $database->database_query("
      INSERT INTO se_groups (
        group_user_id,
        group_groupcat_id,
        group_datecreated,
        group_dateupdated,
        group_title,
        group_desc,
        group_search,
        group_privacy,
        group_comments,
        group_discussion,
        group_upload,
        group_approval,
        group_invite,
        group_totalmembers
      ) VALUES (
        '{$this->user_id}',
        '{$group_groupcat_id}',
        '{$datecreated}',
        '{$datecreated}',
        '{$group_title}',
        '{$group_desc}',
        '{$group_search}',
        '{$group_privacy}',
        '{$group_comments}',
        '{$group_discussion}',
        '{$group_upload}',
        '{$group_approval}',
        '{$group_invite}',
        '1'               
      )
    ");
    
	  $group_id = $database->database_insert_id();
    
	  // ADD GROUP FIELD VALUES
	  $database->database_query("INSERT INTO se_groupvalues (groupvalue_group_id) VALUES ('$group_id')");
	  if($group_field_query != "") {
	    $database->database_query("UPDATE se_groupvalues SET $group_field_query WHERE groupvalue_group_id='$group_id'");
	  }

	  // MAKE OWNER A MEMBER
	  $database->database_query("INSERT INTO se_groupmembers (groupmember_user_id, groupmember_group_id, groupmember_status, groupmember_approved, groupmember_rank) VALUES ('".$this->user_id."', '$group_id', '1', '1', '2')");

	  // ADD GROUP STYLES ROW
	  $database->database_query("INSERT INTO se_groupstyles (groupstyle_group_id) VALUES ('$group_id')");

	  // ADD GROUP ALBUM
	  $database->database_query("INSERT INTO se_groupalbums (groupalbum_group_id, groupalbum_datecreated, groupalbum_dateupdated, groupalbum_title, groupalbum_desc, groupalbum_search, groupalbum_privacy, groupalbum_comments, groupalbum_tag) VALUES ('$group_id', '$datecreated', '$datecreated', '', '', '$group_search', '$group_privacy', '$group_comments', '$groupalbum_tag')");

	  // ADD GROUP DIRECTORY
	  $group_directory = $this->group_dir($group_id);
	  $group_path_array = explode("/", $group_directory);
	  array_pop($group_path_array);
	  array_pop($group_path_array);
	  $subdir = implode("/", $group_path_array)."/";
	  if(!is_dir($subdir)) { 
	    mkdir($subdir, 0777); 
	    chmod($subdir, 0777); 
	    $handle = fopen($subdir."index.php", 'x+');
	    fclose($handle);
	  }
	  mkdir($group_directory, 0777);
	  chmod($group_directory, 0777);
	  $handle = fopen($group_directory."/index.php", 'x+');
	  fclose($handle);

	  return $group_id;

	} // END group_create() METHOD









	// THIS METHOD DELETES A GROUP
	// INPUT: $group_id (OPTIONAL) REPRESENTING THE ID OF THE GROUP TO DELETE
	// OUTPUT:
	function group_delete($group_id = 0) {
	  global $database;

	  if($group_id == 0) { $group_id = $this->group_info['group_id']; }

	  // DELETE GROUP ALBUM, MEDIA, MEDIA COMMENTS
	  $database->database_query("DELETE FROM se_groupalbums, se_groupmedia, se_groupmediacomments, se_groupmedia_tags USING se_groupalbums LEFT JOIN se_groupmedia ON se_groupalbums.groupalbum_id=se_groupmedia.groupmedia_groupalbum_id LEFT JOIN se_groupmediacomments ON se_groupmedia.groupmedia_id=se_groupmediacomments.groupmediacomment_groupmedia_id LEFT JOIN se_groupmediatags ON se_groupmedia.groupmedia_id=se_groupmediatags.groupmediatag_groupmedia_id WHERE se_groupalbums.groupalbum_group_id='$group_id'");

	  // DELETE ALL MEMBERS
	  $database->database_query("DELETE FROM se_groupmembers WHERE se_groupmembers.groupmember_group_id='$group_id'");

	  // DELETE ALL SUBSCRIPTIONS
	  $database->database_query("DELETE FROM se_groupsubscribes WHERE se_groupsubscribes.groupsubscribe_group_id='$group_id'");

	  // DELETE GROUP VALUES
	  $database->database_query("DELETE FROM se_groupvalues WHERE se_groupvalues.groupvalue_group_id='$group_id'");

	  // DELETE GROUP STYLE
	  $database->database_query("DELETE FROM se_groupstyles WHERE se_groupstyles_group_id='$group_id'");

	  // DELETE GROUP ROW
	  $database->database_query("DELETE FROM se_groups WHERE se_groups.group_id='$group_id'");

	  // DELETE GROUP COMMENTS
	  $database->database_query("DELETE FROM se_groupcomments WHERE se_groupcomments.groupcomment_group_id='$group_id'");


	  // DELETE GROUP DISCUSSION POSTS
	  $database->database_query("DELETE FROM se_groupposts WHERE grouppost_grouptopic_id IN (SELECT grouptopic_id from se_grouptopics WHERE grouptopic_group_id='$group_id')");

	  // DELETE GROUP DISCUSSION TOPICS
	  $database->database_query("DELETE FROM se_grouptopics WHERE grouptopic_group_id='$group_id'");


	  // DELETE GROUP'S FILES
	  if(is_dir($this->group_dir($group_id))) {
	    $dir = $this->group_dir($group_id);
	  } else {
	    $dir = ".".$this->group_dir($group_id);
	  }
	  if($dh = @opendir($dir)) {
	    while(($file = @readdir($dh)) !== false) {
	      if($file != "." & $file != "..") {
	        @unlink($dir.$file);
	      }
	    }
	    @closedir($dh);
	  }
	  @rmdir($dir);
	}
  
  // END group_delete() METHOD








	// THIS METHOD DELETES SELECTED GROUPS
	// INPUT: $start REPRESENTING THE GROUP TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF GROUPS TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $group_details (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER TO RETRIEVE TOTAL MEMBERS, GROUP LEADER, ETC
	// OUTPUT: AN ARRAY OF GROUPS
	function group_delete_selected($start, $limit, $sort_by = "group_id DESC", $where = "", $group_details = 0) {
	  global $database, $user;

	  // BEGIN QUERY
	  $group_query = "SELECT se_groups.group_id";

	  // SELECT RELEVANT GROUP DETAILS IF NECESSARY
	  if($group_details == 1) { $group_query .= ", count(member_table.groupmember_id) AS num_members, se_users.user_id, se_users.user_username, se_users.user_photo"; }

	  // IF USER ID NOT EMPTY, GET USER AS MEMBER
	  if($this->user_id != 0) { $group_query .= ", se_groupmembers.groupmember_rank"; }

	  // CONTINUE QUERY
	  $group_query .= " FROM";

	  // IF USER ID NOT EMPTY, SELECT FROM GROUPMEMBER TABLE
	  if($this->user_id != 0) { 
	    $group_query .= " se_groupmembers LEFT JOIN se_groups ON se_groupmembers.groupmember_group_id=se_groups.group_id "; 
	  } else {
	    $group_query .= " se_groups";
	  }

	  // CONTINUE QUERY IF NECESSARY
	  if($group_details == 1) { $group_query .= " LEFT JOIN se_groupmembers AS member_table ON se_groups.group_id=member_table.groupmember_group_id AND member_table.groupmember_status='1' AND member_table.groupmember_approved='1' LEFT JOIN se_users ON se_groups.group_user_id=se_users.user_id"; }

	  // ADD WHERE IF NECESSARY
	  if($where != "" || $this->user_id != 0) { $group_query .= " WHERE"; }

	  // IF USER ID NOT EMPTY, MAKE SURE USER IS A MEMBER
	  if($this->user_id != 0) { $group_query .= " se_groupmembers.groupmember_user_id='{$this->user_id}'"; }

	  // INSERT AND IF NECESSARY
	  if($this->user_id != 0 && $where != "") { $group_query .= " AND"; }

	  // ADD WHERE CLAUSE, IF NECESSARY
	  if($where != "") { $group_query .= " $where"; }

	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $group_query .= " GROUP BY group_id ORDER BY $sort_by LIMIT $start, $limit";

	  // RUN QUERY
	  $groups = $database->database_query($group_query);

	  // GET GROUPS INTO AN ARRAY
	  while($group_info = $database->database_fetch_assoc($groups))
    {
      $var = "delete_group_".$group_info['group_id'];
	    if($_POST[$var] == 1)
      {
        $this->group_delete($group_info['group_id']);
      }
	  }

	} // END group_delete_selected() METHOD








	// THIS METHOD UPDATES THE GROUP'S LAST UPDATE DATE
	// INPUT: 
	// OUTPUT: 
	function group_lastupdate() {
	  global $database;

	  $database->database_query("UPDATE se_groups SET group_dateupdated='".time()."' WHERE group_id='{$this->group_info['group_id']}' LIMIT 1");
	  
	} // END group_lastupdate() METHOD








	// THIS METHOD RETURNS MAXIMUM PRIVACY LEVEL VIEWABLE BY A USER WITH REGARD TO THE CURRENT GROUP
	// INPUT: $other_user REPRESENTING A USER OBJECT
	// OUTPUT: RETURNS PRIVACY LEVEL OF GIVEN USER WITH RESPECT TO CURRENT GROUP
	function group_privacy_max($user) {
	  global $database;
	
	  switch(TRUE) {

	    // GROUP LEADER
	    case($this->group_info['group_user_id'] == $user->user_info['user_id']):
	      return 1;
	      break;

	    // GROUP OFFICER
	    case($database->database_num_rows($database->database_query("SELECT groupmember_id FROM se_groupmembers WHERE groupmember_user_id='{$user->user_info['user_id']}' AND groupmember_group_id='{$this->group_info['group_id']}' AND groupmember_status='1' AND groupmember_rank='1'")) != 0):
	      return 2;
	      break;

	    // GROUP MEMBER
	    case($database->database_num_rows($database->database_query("SELECT groupmember_id FROM se_groupmembers WHERE groupmember_user_id='{$user->user_info['user_id']}' AND groupmember_group_id='{$this->group_info['group_id']}' AND groupmember_status='1'")) != 0):
	      return 4;
	      break;

	    // GROUP LEADER'S FRIEND
	    case($database->database_num_rows($database->database_query("SELECT friend_id FROM se_friends WHERE friend_status='1' AND friend_user_id1='{$this->group_info['group_user_id']}' AND friend_user_id2='{$user->user_info['user_id']}'")) != 0):
	      return 8;
	      break;

	    // GROUP MEMBER'S FRIEND
	    case($database->database_num_rows($database->database_query("SELECT se_friends.friend_id FROM se_groupmembers LEFT JOIN se_friends ON se_groupmembers.groupmember_user_id=se_friends.friend_user_id1 WHERE se_groupmembers.groupmember_status='1' AND se_groupmembers.groupmember_group_id='{$this->group_info['group_id']}' AND se_friends.friend_status='1' AND se_friends.friend_user_id2='{$user->user_info['user_id']}'")) != 0):
	      return 16;
	      break;
	
	    // FRIEND OF GROUP MEMBER'S FRIENDS
	    case($database->database_num_rows($database->database_query("SELECT t2.friend_user_id2 FROM se_groupmembers LEFT JOIN se_friends AS t1 ON se_groupmembers.groupmember_user_id=t1.friend_user_id1 LEFT JOIN se_friends AS t2 ON t1.friend_user_id2=t2.friend_user_id1 WHERE se_groupmembers.groupmember_status='1' AND se_groupmembers.groupmember_group_id='{$group->group_info['group_id']}' AND t1.friend_status='1' AND t2.friend_status='1' AND t2.friend_user_id2='{$user->user_info['user_id']}'")) != 0):
	      return 32;
	      break;

	    // REGISTERED USER
	    case($user->user_exists == 1):
	      return 64;
	      break;

	    // DEFAULT EVERYONE
	    default:
	      return 128;

	  }

	} // END group_privacy_max() METHOD








	// THIS METHOD RETURNS THE TOTAL NUMBER OF MEMBERS IN A GROUP
	// INPUT: $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	//	  $member_details (OPTIONAL) REPRESENTING WHETHER TO JOIN TO THE USER TABLE FOR SEARCH PURPOSES
	// OUTPUT: AN INTEGER REPRESENTING THE NUMBER OF GROUP MEMBERS
	function group_member_total($where = "", $member_details = 0)
  {
	  global $database;
    
    // See if it has been cached in parent table
    if( !$where && isset($this->group_info['group_totalmembers']) )
      return $this->group_info['group_totalmembers'];
    
	  // BEGIN QUERY
	  $groupmember_query = "SELECT NULL FROM se_groupmembers";

	  // JOIN TO USER TABLE IF NECESSARY
	  if($member_details == 1) { $groupmember_query .= " LEFT JOIN se_users ON se_groupmembers.groupmember_user_id=se_users.user_id"; }

	  // ADD WHERE IF NECESSARY
	  if($this->group_exists != 0 || $where != "") { $groupmember_query .= " WHERE"; }

	  // IF GROUP ID IS SET
	  if($this->group_exists != 0) { $groupmember_query .= " se_groupmembers.groupmember_group_id='{$this->group_info['group_id']}'"; }

	  // ADD AND IF NECESSARY
	  if($this->group_exists != 0 && $where != "") { $groupmember_query .= " AND"; }  

	  // ADD REST OF WHERE CLAUSE
	  if($where != "") { $groupmember_query .= " $where"; }

	  // RUN QUERY
	  $groupmember_total = $database->database_num_rows($database->database_query($groupmember_query));
	  return $groupmember_total;

	} // END group_member_total() METHOD








	// THIS METHOD RETURNS AN ARRAY OF GROUP MEMBERS
	// INPUT: $start REPRESENTING THE GROUP MEMBER TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF GROUP MEMBERS TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: AN ARRAY OF GROUP MEMBERS
	function group_member_list($start, $limit, $sort_by = "groupmember_id DESC", $where = "") {
	  global $database, $user;

	  // BEGIN QUERY
	  $groupmember_query = "SELECT 
				se_groupmembers.*, 
				se_users.user_id, 
				se_users.user_username, 
				se_users.user_fname, 
				se_users.user_lname, 
				se_users.user_photo, 
				se_users.user_dateupdated, 
				se_users.user_lastlogindate, 
				se_users.user_signupdate,
			CASE
			  WHEN (SELECT TRUE FROM se_friends WHERE friend_user_id1='{$user->user_info['user_id']}' AND friend_user_id2=se_users.user_id AND friend_status='1' LIMIT 1)
			    THEN 2
			  WHEN (SELECT TRUE FROM se_friends WHERE friend_user_id1='{$user->user_info['user_id']}' AND friend_user_id2=se_users.user_id AND friend_status='0' LIMIT 1)
			    THEN 1
			  ELSE 0
			END
			AS is_viewers_friend,

			CASE
			  WHEN (SELECT TRUE FROM se_users AS se_users2 WHERE se_users2.user_id=se_users.user_id AND (user_blocklist LIKE '{$user->user_info['user_id']},%' OR user_blocklist LIKE '%,{$user->user_info['user_id']}' OR user_blocklist LIKE '%,{$user->user_info['user_id']},%') LIMIT 1)
			    THEN TRUE
			  ELSE FALSE
			END
			AS is_viewer_blocklisted
			FROM se_groupmembers 
			LEFT JOIN se_users 
			ON se_groupmembers.groupmember_user_id=se_users.user_id";

	  // ADD WHERE IF NECESSARY
	  if($this->group_exists != 0 || $where != "") { $groupmember_query .= " WHERE"; }

	  // IF GROUP ID IS SET
	  if($this->group_exists != 0) { $groupmember_query .= " se_groupmembers.groupmember_group_id='{$this->group_info['group_id']}'"; }

	  // ADD AND IF NECESSARY
	  if($this->group_exists != 0 && $where != "") { $groupmember_query .= " AND"; }  

	  // ADD REST OF WHERE CLAUSE
	  if($where != "") { $groupmember_query .= " $where"; }

	  // ADD ORDER, AND LIMIT CLAUSE
	  $groupmember_query .= " ORDER BY $sort_by LIMIT $start, $limit";

	  // RUN QUERY
	  $groupmembers = $database->database_query($groupmember_query);

	  // GET GROUP MEMBERS INTO AN ARRAY
	  $groupmember_array = Array();
	  while($groupmember_info = $database->database_fetch_assoc($groupmembers))
    {
	    // CREATE OBJECT FOR MEMBER
	    $member = new se_user();
	    $member->user_exists = 1;
	    $member->user_info['user_id'] = $groupmember_info['user_id'];
	    $member->user_info['user_username'] = $groupmember_info['user_username'];
	    $member->user_info['user_photo'] = $groupmember_info['user_photo'];
	    $member->user_info['user_dateupdated'] = $groupmember_info['user_dateupdated'];
	    $member->user_info['user_lastlogindate'] = $groupmember_info['user_lastlogindate'];
	    $member->user_info['user_signupdate'] = $groupmember_info['user_signupdate'];
	    $member->user_info['user_fname'] = $groupmember_info['user_fname'];
	    $member->user_info['user_lname'] = $groupmember_info['user_lname'];
	    $member->is_viewers_friend = $groupmember_info['is_viewers_friend'];
	    $member->is_viewers_blocklist = $groupmember_info['is_viewers_blocklist'];
	    $member->user_displayname();
      
	    // SET GROUP ARRAY
	    $groupmember_array[] = Array(
        'groupmember_id' => $groupmember_info['groupmember_id'],
        'groupmember_rank' => $groupmember_info['groupmember_rank'],
        'groupmember_title' => $groupmember_info['groupmember_title'],
        'groupmember_approved' => $groupmember_info['groupmember_approved'],
        'groupmember_status' => $groupmember_info['groupmember_status'],
        'member' => $member
      );
	  }

	  // RETURN ARRAY
	  return $groupmember_array;

	} // END group_member_list() METHOD








	// THIS METHOD RETURNS THE PATH TO THE GIVEN GROUP'S DIRECTORY
	// INPUT: $group_id (OPTIONAL) REPRESENTING A GROUP'S GROUP_ID
	// OUTPUT: A STRING REPRESENTING THE RELATIVE PATH TO THE GROUP'S DIRECTORY
	function group_dir($group_id = 0) {

	  if($group_id == 0 & $this->group_exists) { $group_id = $this->group_info['group_id']; }

	  $subdir = $group_id+999-(($group_id-1)%1000);
	  $groupdir = "./uploads_group/{$subdir}/{$group_id}/";
	  return $groupdir;

	} // END group_dir() METHOD








	// THIS METHOD OUTPUTS THE PATH TO THE GROUP'S PHOTO OR THE GIVEN NOPHOTO IMAGE
	// INPUT: $nophoto_image (OPTIONAL) REPRESENTING THE PATH TO AN IMAGE TO OUTPUT IF NO PHOTO EXISTS
	//	  $thumb (OPTIONAL) REPRESENTING WHETHER TO RETRIEVE THE SQUARE THUMBNAIL OR NOT
	// OUTPUT: A STRING CONTAINING THE PATH TO THE GROUP'S PHOTO
	function group_photo($nophoto_image = "", $thumb = FALSE) {

	  $group_photo = $this->group_dir($this->group_info['group_id']).$this->group_info['group_photo'];
	  if($thumb)
    { 
	    $group_thumb = substr($group_photo, 0, strrpos($group_photo, "."))."_thumb".substr($group_photo, strrpos($group_photo, ".")); 
	    if(file_exists($group_thumb)) { $group_photo = $group_thumb; }
	  }
	  if(!file_exists($group_photo) || $this->group_info['group_photo'] == "") { $group_photo = $nophoto_image; }
	  return $group_photo;
	  
	} // END group_photo() METHOD








	// THIS METHOD UPLOADS A GROUP PHOTO ACCORDING TO SPECIFICATIONS AND RETURNS GROUP PHOTO
	// INPUT: $photo_name REPRESENTING THE NAME OF THE FILE INPUT
	// OUTPUT: 
	function group_photo_upload($photo_name)
  {
	  global $database, $url;
    
	  // SET KEY VARIABLES
	  $file_maxsize = "4194304";
	  $file_exts = explode(",", str_replace(" ", "", strtolower($this->groupowner_level_info['level_group_photo_exts'])));
	  $file_types = explode(",", str_replace(" ", "", strtolower("image/jpeg, image/jpg, image/jpe, image/pjpeg, image/pjpg, image/x-jpeg, x-jpg, image/gif, image/x-gif, image/png, image/x-png")));
	  $file_maxwidth = $this->groupowner_level_info['level_group_photo_width'];
	  $file_maxheight = $this->groupowner_level_info['level_group_photo_height'];
	  $photo_newname = "0_".rand(1000, 9999).".jpg";
	  $file_dest = $this->group_dir($this->group_info['group_id']).$photo_newname;
	  $thumb_dest = substr($file_dest, 0, strrpos($file_dest, "."))."_thumb".substr($file_dest, strrpos($file_dest, "."));
    
	  $new_photo = new se_upload();
	  $new_photo->new_upload($photo_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);
    
	  // UPLOAD AND RESIZE PHOTO IF NO ERROR
	  if($new_photo->is_error == 0)
    {
	    // DELETE OLD AVATAR IF EXISTS
	    $this->group_photo_delete();
      
	    // UPLOAD THUMB
	    $new_photo->upload_thumb($thumb_dest);
      
	    // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
	    if($new_photo->is_image == 1)
      {
	      $new_photo->upload_photo($file_dest);
	    }
      else
      {
	      $new_photo->upload_file($file_dest);
	    }
      
	    // UPDATE GROUP INFO WITH IMAGE IF STILL NO ERROR
	    if( !$new_photo->is_error )
      {
	      $database->database_query("UPDATE se_groups SET group_photo='{$photo_newname}' WHERE group_id='{$this->group_info['group_id']}'");
	      $this->group_info['group_photo'] = $photo_newname;
      }
	  }
    
	  $this->is_error = $new_photo->is_error;
	}
  
  // END group_photo_upload() METHOD








	// THIS METHOD DELETES A GROUP PHOTO
	// INPUT: 
	// OUTPUT: 
	function group_photo_delete()
  {
	  global $database;
	  $group_photo = $this->group_photo();
	  if($group_photo != "")
    {
	    @unlink($group_photo);
	    $database->database_query("UPDATE se_groups SET group_photo='' WHERE group_id='{$this->group_info['group_id']}'");
	    $this->group_info['group_photo'] = "";
	  }
	}
  
  // END group_photo_delete() METHOD








	// THIS METHOD UPLOADS MEDIA TO A GROUP ALBUM
	// INPUT: $file_name REPRESENTING THE NAME OF THE FILE INPUT
	//	  $groupalbum_id REPRESENTING THE ID OF THE GROUP ALBUM TO UPLOAD THE MEDIA TO
	//	  $space_left REPRESENTING THE AMOUNT OF SPACE LEFT
	// OUTPUT:
	function group_media_upload($file_name, $groupalbum_id, &$space_left)
  {
	  global $database, $url, $user;
    
	  // SET KEY VARIABLES
	  $file_maxsize = $this->groupowner_level_info['level_group_album_maxsize'];
	  $file_exts = explode(",", str_replace(" ", "", strtolower($this->groupowner_level_info['level_group_album_exts'])));
	  $file_types = explode(",", str_replace(" ", "", strtolower($this->groupowner_level_info['level_group_album_mimes'])));
	  $file_maxwidth = $this->groupowner_level_info['level_group_album_width'];
	  $file_maxheight = $this->groupowner_level_info['level_group_album_height'];
    
	  $new_media = new se_upload();
	  $new_media->new_upload($file_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);
    
	  // UPLOAD AND RESIZE PHOTO IF NO ERROR
	  if($new_media->is_error == 0)
    {
	    // INSERT ROW INTO MEDIA TABLE
	    $database->database_query("
        INSERT INTO se_groupmedia (
          groupmedia_groupalbum_id,
          groupmedia_user_id,
          groupmedia_date
        ) VALUES (
          '{$groupalbum_id}',
          '{$user->user_info['user_id']}',
          '".time()."'
        )
      ");
	    $groupmedia_id = $database->database_insert_id();
      
	    // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
	    if($new_media->is_image == 1)
      {
	      $file_dest = $this->group_dir($this->group_info['group_id']).$groupmedia_id.".jpg";
	      $thumb_dest = $this->group_dir($this->group_info['group_id']).$groupmedia_id."_thumb.jpg";
        
	      // UPLOAD THUMB
	      $new_media->upload_thumb($thumb_dest, 200);
        
	      // UPLOAD FILE
	      $new_media->upload_photo($file_dest);
	      $file_ext = "jpg";
	      $file_filesize = filesize($file_dest);
	    }
      else
      {
	      $file_dest = $this->group_dir($this->group_info['group_id']).$groupmedia_id.".".$new_media->file_ext;
	      $thumb_dest = $this->group_dir($this->group_info['group_id']).$groupmedia_id."_thumb.jpg";
        
	      // UPLOAD THUMB IF NECESSARY
	      if($new_media->file_ext == 'gif')
        {
	        $thumb_dest = $this->group_dir($this->group_info['group_id']).$groupmedia_id."_thumb.jpg";
	        $new_media->upload_thumb($thumb_dest, 200);
	      }
        
	      $new_media->upload_file($file_dest);
	      $file_ext = $new_media->file_ext;
	      $file_filesize = filesize($file_dest);
	    }
      
	    // CHECK SPACE LEFT
	    if($file_filesize > $space_left) {
	      $new_media->is_error = 2000250;
	    } else {
	      $space_left = $space_left-$file_filesize;
	    }
      
	    // DELETE FROM DATABASE IF ERROR
	    if( $new_media->is_error )
      {
	      $database->database_query("DELETE FROM se_groupmedia WHERE groupmedia_id='$groupmedia_id' AND groupmedia_groupalbum_id='$groupalbum_id'");
	      @unlink($file_dest);
      }
      
	    // UPDATE ROW IF NO ERROR
      else
      {
	      $sql = "UPDATE se_groupmedia SET groupmedia_ext='$file_ext', groupmedia_filesize='$file_filesize' WHERE groupmedia_id='$groupmedia_id' AND groupmedia_groupalbum_id='$groupalbum_id'";
        $resource = $database->database_query($sql);
        
        // UPDATE PARENT TABLE ROW
        if( !is_numeric($file_filesize) ) $file_filesize = 0;
        $sql = "UPDATE se_groupalbums SET groupalbum_totalfiles=groupalbum_totalfiles+1, groupalbum_totalspace=groupalbum_totalspace+'{$file_filesize}' WHERE groupalbum_id='{$groupalbum_id}' LIMIT 1";
        $resource = $database->database_query($sql);
      }
	  }
    
	  // RETURN FILE STATS
	  $file_result = Array(
      'is_error'            => $new_media->is_error,
			'file_name'           => $_FILES[$file_name]['name'],
			'groupmedia_id'       => $groupmedia_id,
			'groupmedia_ext'      => $file_ext,
			'groupmedia_filesize' => $file_filesize
    );
    
	  return $file_result;
	}
  
  // END group_media_upload() METHOD








	// THIS METHOD RETURNS THE SPACE USED
	// INPUT: $groupalbum_id (OPTIONAL) REPRESENTING THE ID OF THE ALBUM TO CALCULATE
	// OUTPUT: AN INTEGER REPRESENTING THE SPACE USED
	function group_media_space($groupalbum_id = 0)
  {
	  global $database;
    
	  // BEGIN QUERY
	  $groupmedia_query = "SELECT sum(se_groupmedia.groupmedia_filesize) AS total_space";
    
	  // CONTINUE QUERY
	  $groupmedia_query .= " FROM se_groupalbums LEFT JOIN se_groupmedia ON se_groupalbums.groupalbum_id=se_groupmedia.groupmedia_groupalbum_id";
    
	  // ADD WHERE IF NECESSARY
	  if($this->group_exists != 0 || $groupalbum_id != 0) { $groupmedia_query .= " WHERE"; }
    
	  // IF GROUP EXISTS, SPECIFY GROUP ID
	  if($this->group_exists != 0) { $groupmedia_query .= " se_groupalbums.groupalbum_group_id='{$this->group_info['group_id']}'"; }
    
	  // ADD AND IF NECESSARY
	  if($this->group_exists != 0 && $groupalbum_id != 0) { $groupmedia_query .= " AND"; }
    
	  // SPECIFY ALBUM ID IF NECESSARY
	  if($groupalbum_id != 0) { $groupmedia_query .= " se_groupalbums.groupalbum_id='{$groupalbum_id}'"; }
    
	  // GET AND RETURN TOTAL SPACE USED
	  $space_info = $database->database_fetch_assoc($database->database_query($groupmedia_query));
	  return $space_info[total_space];
	}
  
  // END group_media_space() METHOD








	// THIS METHOD RETURNS THE NUMBER OF GROUP MEDIA
	// INPUT: $groupalbum_id (OPTIONAL) REPRESENTING THE ID OF THE GROUP ALBUM TO CALCULATE
	// OUTPUT: AN INTEGER REPRESENTING THE NUMBER OF FILES
	function group_media_total($groupalbum_id = 0)
  {
	  global $database;
    
    if( !$this->group_exists && !$groupalbum_id ) return FALSE;
    
    
    // NEW HANDLING
    $sql = "
      SELECT
        groupalbum_totalfiles
      FROM
        se_groupalbums
      WHERE
    ";
    
    if( $this->group_exists ) $sql .= "
      groupalbum_group_id='{$this->group_info['group_id']}'
    ";
    
    if( $this->group_exists && $groupalbum_id ) $sql .= " && ";
    
    
    if( $groupalbum_id ) $sql .= "
      groupalbum_id='{$groupalbum_id}'
    ";
    
    $resource = $database->database_query($sql);
    
    if( !$resource )
      return FALSE;
    
    $total_files = $database->database_fetch_assoc($resource);
    return (int) $total_files['groupalbum_totalfiles'];
	}
  
  // END group_media_total() METHOD








	// THIS METHOD RETURNS AN ARRAY OF GROUP MEDIA
	// INPUT: $start REPRESENTING THE GROUP MEDIA TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF GROUP MEDIA TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: AN ARRAY OF GROUP MEDIA
	function group_media_list($start, $limit, $sort_by = "groupmedia_id DESC", $where = "")
  {
	  global $database;
    
	  // BEGIN QUERY
	  $groupmedia_query = "SELECT se_groupmedia.*, se_groupalbums.groupalbum_id, se_groupalbums.groupalbum_group_id, se_groupalbums.groupalbum_title, count(se_groupmediacomments.groupmediacomment_id) AS total_comments";
    
	  // CONTINUE QUERY
	  $groupmedia_query .= " FROM se_groupmedia LEFT JOIN se_groupmediacomments ON se_groupmediacomments.groupmediacomment_groupmedia_id=se_groupmedia.groupmedia_id LEFT JOIN se_groupalbums ON se_groupalbums.groupalbum_id=se_groupmedia.groupmedia_groupalbum_id";
    
	  // ADD WHERE IF NECESSARY
	  if($this->group_exists != 0 || $where != "") { $groupmedia_query .= " WHERE"; }
    
	  // IF GROUP EXISTS, SPECIFY GROUP ID
	  if($this->group_exists != 0) { $groupmedia_query .= " se_groupalbums.groupalbum_group_id='{$this->group_info['group_id']}'"; }
    
	  // ADD AND IF NECESSARY
	  if($this->group_exists != 0 && $where != "") { $groupmedia_query .= " AND"; }
    
	  // ADD ADDITIONAL WHERE CLAUSE
	  if($where != "") { $groupmedia_query .= " $where"; }
    
	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $groupmedia_query .= " GROUP BY groupmedia_id ORDER BY $sort_by LIMIT $start, $limit";
    
	  // RUN QUERY
	  $groupmedia = $database->database_query($groupmedia_query);
    
	  // GET GROUP MEDIA INTO AN ARRAY
	  $groupmedia_array = Array();
	  while($groupmedia_info = $database->database_fetch_assoc($groupmedia))
    {
	    // CREATE ARRAY OF MEDIA DATA
	    $groupmedia_array[] = Array(
        'groupmedia_id' => $groupmedia_info['groupmedia_id'],
        'groupmedia_groupalbum_id' => $groupmedia_info['groupmedia_groupalbum_id'],
        'groupmedia_date' => $groupmedia_info['groupmedia_date'],
        'groupmedia_title' => $groupmedia_info['groupmedia_title'],
        'groupmedia_desc' => str_replace("<br>", "\r\n", $groupmedia_info['groupmedia_desc']),
        'groupmedia_ext' => $groupmedia_info['groupmedia_ext'],
        'groupmedia_filesize' => $groupmedia_info['groupmedia_filesize'],
        'groupmedia_comments_total' => $groupmedia_info['total_comments']
      );
	  }
    
	  // RETURN ARRAY
	  return $groupmedia_array;
	}
  
  // END group_media_list() METHOD








	// THIS METHOD RETURNS AN ARRAY OF GROUP TOPICS
	// INPUT: $start REPRESENTING THE GROUP TOPIC TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF GROUP TOPICS TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: AN ARRAY OF GROUP TOPICS
	function group_topic_list($start, $limit, $sort_by = "grouptopic_date DESC", $where = "")
  {
	  global $database;

	  // BEGIN QUERY
	  $grouptopic_query = "
      SELECT
        se_grouptopics.*,
        se_groupposts.*,
        se_grouptopics.grouptopic_totalposts AS total_posts,
        creator.user_id AS creator_user_id,
        creator.user_username AS creator_user_username,
        creator.user_fname AS creator_user_fname,
        creator.user_lname AS creator_user_lname,
        lastposter.user_id AS lastposter_user_id,
        lastposter.user_username AS lastposter_user_username,
        lastposter.user_fname AS lastposter_user_fname,
        lastposter.user_lname AS lastposter_user_lname,
        lastposter.user_photo AS lastposter_user_photo
    ";
    
	  // CONTINUE QUERY
	  $grouptopic_query .= "
      FROM
        se_grouptopics
      LEFT JOIN
        se_groupposts
        ON se_grouptopics.grouptopic_id=se_groupposts.grouppost_grouptopic_id
    ";

	  // JOIN TO USER TABLE (CREATOR)
	  $grouptopic_query .= " LEFT JOIN se_users AS creator ON se_grouptopics.grouptopic_creatoruser_id=creator.user_id";

	  // JOIN TO USER TABLE (LAST POSTER)
	  $grouptopic_query .= " LEFT JOIN se_users AS lastposter ON se_groupposts.grouppost_authoruser_id=lastposter.user_id";

	  // ADD WHERE IF NECESSARY
	  $grouptopic_query .= " WHERE se_groupposts.grouppost_id = (SELECT MAX(grouppost_id) FROM se_groupposts WHERE grouppost_grouptopic_id=se_grouptopics.grouptopic_id)";

	  // IF GROUP EXISTS, SPECIFY GROUP ID
	  if($this->group_exists != 0) { $grouptopic_query .= " AND se_grouptopics.grouptopic_group_id='{$this->group_info['group_id']}'"; }

	  // ADD ADDITIONAL WHERE CLAUSE
	  if($where != "") { $grouptopic_query .= " AND $where"; }

	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $grouptopic_query .= " GROUP BY grouptopic_id ORDER BY $sort_by LIMIT $start, $limit";

	  // RUN QUERY
	  $grouptopics = $database->database_query($grouptopic_query);

	  // GET GROUP TOPICS INTO AN ARRAY
	  $grouptopic_array = Array();
	  while($grouptopic_info = $database->database_fetch_assoc($grouptopics))
    {
	    $creator = new se_user();
	    if($grouptopic_info['creator_user_id'] != $grouptopic_info['grouptopic_creatoruser_id'])
      {
	      $creator->user_exists = 0;
	    }
      else
      {
	      $creator->user_exists = 1;
	      $creator->user_info['user_id'] = $grouptopic_info['creator_user_id'];
	      $creator->user_info['user_username'] = $grouptopic_info['creator_user_username'];
	      $creator->user_info['user_fname'] = $grouptopic_info['creator_user_fname'];
	      $creator->user_info['user_lname'] = $grouptopic_info['creator_user_lname'];
	      $creator->user_displayname();
	    }

	    $lastposter = new se_user();
	    if($grouptopic_info['lastposter_user_id'] != $grouptopic_info['grouppost_authoruser_id'])
      {
	      $lastposter->user_exists = 0;
	    }
      else
      {
	      $lastposter->user_exists = 1;
	      $lastposter->user_info['user_id'] = $grouptopic_info['lastposter_user_id'];
	      $lastposter->user_info['user_username'] = $grouptopic_info['lastposter_user_username'];
	      $lastposter->user_info['user_fname'] = $grouptopic_info['lastposter_user_fname'];
	      $lastposter->user_info['user_lname'] = $grouptopic_info['lastposter_user_lname'];
	      $lastposter->user_info['user_photo'] = $grouptopic_info['lastposter_user_photo'];
	      $lastposter->user_displayname();
	    }

	    // SET PART OF ARRAY
	    $grouptopic_info['creator'] = $creator;
	    $grouptopic_info['lastposter'] = $lastposter;

	    // CREATE ARRAY OF TOPIC DATA
	    $grouptopic_array[] = $grouptopic_info;

	  }

	  // RETURN ARRAY
	  return $grouptopic_array;

	} // END group_topic_list() METHOD








	// THIS METHOD RETURNS THE NUMBER OF GROUP TOPICS
	// INPUT: $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: AN INTEGER REPRESENTING THE NUMBER OF TOPICS
	function group_topic_total($where = "")
  {
	  global $database;
    
    // NEW SHORT HANDLING
    if( !$where && isset($this->group_info['group_totaltopics']) )
    {
      return (int) $this->group_info['group_totaltopics'];
    }
    
	  // BEGIN QUERY
	  $grouptopic_query = "SELECT se_grouptopics.*, count(se_groupposts.grouppost_id) AS total_posts";
	
	  // CONTINUE QUERY
	  $grouptopic_query .= " FROM se_grouptopics LEFT JOIN se_groupposts ON se_groupposts.grouppost_grouptopic_id=se_grouptopics.grouptopic_id";

	  // ADD WHERE IF NECESSARY
	  if($this->group_exists != 0 || $where != "") { $grouptopic_query .= " WHERE"; }

	  // IF GROUP EXISTS, SPECIFY GROUP ID
	  if($this->group_exists != 0) { $grouptopic_query .= " se_grouptopics.grouptopic_group_id='{$this->group_info['group_id']}'"; }

	  // ADD AND IF NECESSARY
	  if($this->group_exists != 0 && $where != "") { $grouptopic_query .= " AND"; }

	  // ADD ADDITIONAL WHERE CLAUSE
	  if($where != "") { $grouptopic_query .= " $where"; }

	  // ADD GROUP BY, ORDER, AND LIMIT CLAUSE
	  $grouptopic_query .= " GROUP BY grouptopic_id";

	  // RUN QUERY
	  $total_topics = $database->database_num_rows($database->database_query($grouptopic_query));

	  // RETURN TOTAL TOPICS
	  return $total_topics;

	} // END group_topic_total() METHOD








	// THIS METHOD RETURNS AN ARRAY OF GROUP POSTS
	// INPUT: $start REPRESENTING THE GROUP POST TO START WITH
	//	  $limit REPRESENTING THE NUMBER OF GROUP POSTS TO RETURN
	//	  $sort_by (OPTIONAL) REPRESENTING THE ORDER BY CLAUSE
	//	  $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: AN ARRAY OF GROUP POSTS
	function group_post_list($start, $limit, $sort_by = "grouppost_date DESC", $where = "") {
	  global $database;

	  // BEGIN QUERY
	  $grouppost_query = "SELECT se_groupposts.*, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname, se_users.user_photo";
	
	  // CONTINUE QUERY
	  $grouppost_query .= " FROM se_groupposts LEFT JOIN se_users ON se_groupposts.grouppost_authoruser_id=se_users.user_id";

	  // ADD WHERE IF NECESSARY
	  if($where != "") { $grouppost_query .= " WHERE $where"; }

	  // ADD ORDER, AND LIMIT CLAUSE
	  $grouppost_query .= " ORDER BY $sort_by LIMIT $start, $limit";

	  // RUN QUERY
	  $groupposts = $database->database_query($grouppost_query);

	  // GET GROUP POSTS INTO AN ARRAY
	  $grouppost_array = Array();
	  while($grouppost_info = $database->database_fetch_assoc($groupposts))
    {
      // Get post author
	    $author = new se_user();
	    if($grouppost_info['grouppost_authoruser_id'] != $grouppost_info['user_id'])
      {
	      $author->user_exists = 0;
	    }
      else
      {
	      $author->user_exists = 1;
	      $author->user_info['user_id'] = $grouppost_info['user_id'];
	      $author->user_info['user_username'] = $grouppost_info['user_username'];
	      $author->user_info['user_fname'] = $grouppost_info['user_fname'];
	      $author->user_info['user_lname'] = $grouppost_info['user_lname'];
	      $author->user_info['user_photo'] = $grouppost_info['user_photo'];
	      $author->user_displayname();
	    }
      
	    $grouppost_info['grouppost_author'] =& $author;
      
      // Get last post editor
      if( !empty($grouppost_info['grouppost_lastedit_user_id']) )
      {
        if( $grouppost_info['grouppost_lastedit_user_id']==$grouppost_info['grouppost_authoruser_id'] )
        {
          $grouppost_info['grouppost_lastedit_user_object'] =& $grouppost_info['grouppost_author'];
        }
        else
        {
          $grouppost_info['grouppost_lastedit_user_object'] = new se_user(array($grouppost_info['grouppost_lastedit_user_id']));
        }
      }
      
      // Format post body
      $grouppost_info['grouppost_body_formatted'] = $this->group_post_bbcode_parse_view($grouppost_info['grouppost_body']);
      $grouppost_info['grouppost_body_formatted'] = stripslashes($grouppost_info['grouppost_body_formatted']);
      
	    // CREATE ARRAY OF POST DATA
	    $grouppost_array[] = $grouppost_info;
      
      unset($author);
	  }
    
	  // RETURN ARRAY
	  return $grouppost_array;
	}
  
  // END group_post_list() METHOD








	// THIS METHOD RETURNS THE NUMBER OF GROUP POSTS
	// INPUT: $where (OPTIONAL) REPRESENTING ADDITIONAL THINGS TO INCLUDE IN THE WHERE CLAUSE
	// OUTPUT: AN INTEGER REPRESENTING THE NUMBER OF POSTS
	function group_post_total($where = "", $grouptopic_id=NULL) {
	  global $database;
    
    // TRY CACHED
    if( $grouptopic_id && !$where )
    {
      $sql = "SELECT grouptopic_totalposts FROM se_grouptopics WHERE grouptopic_id='{$grouptopic_id}' LIMIT 1";
      
      $resource = $database->database_query($sql);
      if( $resource )
      {
        $total_posts = $database->database_fetch_assoc($resource);
        return (int) $total_posts['grouptopic_totalposts'];
      }
    }
    
	  // BEGIN QUERY
	  $grouppost_query = "SELECT se_groupposts.grouppost_id, se_users.user_id";
    
	  // CONTINUE QUERY
	  $grouppost_query .= " FROM se_groupposts LEFT JOIN se_users ON se_groupposts.grouppost_authoruser_id=se_users.user_id";
    
	  // ADD WHERE IF NECESSARY
	  if($where != "") { $grouppost_query .= " WHERE $where"; }
    
	  // RUN QUERY
	  $total_posts = $database->database_num_rows($database->database_query($grouppost_query));
    
	  // RETURN TOTAL POSTS
	  return $total_posts;
    
	} // END group_post_total() METHOD





	function group_post_bbcode_parse_clean($string)
  {
    global $setting;
    
    // Fix line breaks
    $string = htmlspecialchars_decode($string, ENT_QUOTES);
    $string = censor($string);
    $string = preg_replace(array("/\\r\\n/", "/\\r/", "/\\n/"), array("[br]", "[br]", "[br]"), $string);
    
    // DO [code]
    $open_code = preg_match_all('/\[code\=?(.*?)\]/i', $string, $matches);
    $close_code = preg_match_all('/\[\/code\]/i', $string, $matches);
    $total_tags = ( ($open_code > $close_code) ? $close_code : $open_code );
    
    if( $total_tags )
    {
      $string = preg_replace_callback(
        '/(\[code\=?.*?\])(.*?)(\[\/code\])/i',
        create_function(
          '$matches',
          'return $matches[1].str_replace("&", "[AMP]", htmlspecialchars(htmlspecialchars_decode($matches[2], ENT_QUOTES), ENT_QUOTES)).$matches[3];'
        ),
        $string,
        $total_tags
      );
    }
    
    // Clean HTML
    //$string = str_replace('&', '[AMP]', $string);
    $string = cleanHTML($string, $setting['setting_group_discussion_html'], Array("style"));
    $string = str_replace('[AMP]', '&', $string);
    
    // Fix line breaks
    $string = str_replace("[br]", "<br>", $string);
    $string = preg_replace('/\s+<br>\s+/i', '<br>', $string);
    $string = preg_replace('/(<br>){3,}/is', '<br><br>', $string);
    
    return $string;
  }





	function group_post_bbcode_parse_view($string)
  {
    // DO [quote]
    $open_quote = preg_match_all('/\[quote\=(.*?)\]/i', $string, $matches);
    $close_quote = preg_match_all('/\[\/quote\]/i', $string, $matches);
    $total_tags = ( $open_quote>$close_quote ? $close_quote : $open_quote );
    
    if( $total_tags )
    {
      $string = preg_replace('/\[quote\=(.*?)\]/i', "<div class='group_discussion_quote'><div>".SE_Language::get(2000323, Array('$1'))."</div>", $string, $total_tags);
      $string = strrev(preg_replace('/\]etouq\/\[/i', ">vid/<", strrev($string), $total_tags));
    }
    
    // DO [code]
    $open_code = preg_match_all('/\[code\=?(.*?)\]/i', $string, $matches);
    $close_code = preg_match_all('/\[\/code\]/i', $string, $matches);
    $total_tags = ( ($open_code > $close_code) ? $close_code : $open_code );
    
    if( $total_tags )
    {
      $string = preg_replace('/\[code\=?(.*?)\](.*?)\[\/code\]/ie', "'<div class=\'group_discussion_code\'>'.( '\\1'!='' ? '<div class=\'group_discussion_code_title\'>'.'\\1'.'</div>' : '').'\\2'.'</div>'", $string, $total_tags);
    }
    
    return $string;
  }
}


?>