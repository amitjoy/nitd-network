<?php

//  THIS FILE CONTAINS USER CONNECTION PATH-RELETED FUNCTIONS
//  FUNCTIONS IN THIS FILE:
//    user_connection_path()
//		find_minimum()
//		userconnection_calculate_distance()
//		userconnection_users_information()
//

// THIS FUNCTION IS RUN DURING THE SEARCH PROCESS TO SEARCH SHORTEST PATH AND FOR FINDING OUT CONTACTS DEGREE 
// INPUT: LOGED USER ID AND VISITING PROFILE'S USER ID
// OUTPUT: RETURN A COMBIND ARRAY OF CONNECTING PATH AND CONTACTS DEGREE 
//
function user_connection_path ($userid, $to_user_id)
{
	global $database;
	global $user;
	global $userconnection_distance;
  global $userconnection_previous;
	global $userconnection_setting;
	$userconnection_output = array();
	
	 // CACHING
  $cache_object = SECache::getInstance('serial');
  if (is_object($cache_object) ) {
	  $userconnection_combind_path_contacts_array = $cache_object->get('userconnection_combind_path_contacts_array_cache');
  }
	if (!is_array($userconnection_combind_path_contacts_array)) {
		// longest is the steps ... By changint it you can change steps level  
		$longest = $userconnection_setting['level'];
		// IF $longest IS LESS THEN 4 THEN FOR FINDING OUT CONTACTS DEGREE WE ASSIGN '4' TO $longest BECAUSE WE WANT TO SHOW THREE DEGREE CONTACTS  
		if ($longest<4) {
			$longest = 4;
		}
		// Initialize the distance to all the user entities with the maximum value of distance.
  	// Initialize the previous connecting user entity for every user entity as -1. This means a no known path.
		$id =	 $user->user_info['user_id'];
		$result = $database->database_query ("SELECT su.user_id FROM se_users su INNER JOIN se_usersettings sus ON sus.usersetting_user_id = su.user_id WHERE (su.user_verified='1' AND su.user_enabled='1' AND su.user_search='1' AND sus.usersetting_userconnection = '0') OR su.user_id = '$id'");
		while ($row = $database->database_fetch_assoc($result)) {
			
			$userconnection_entity = $row['user_id'];
			$userconnection_entities_array[] = $userconnection_entity;
  	  $userconnection_distance[$userconnection_entity] = $longest;
  	  $userconnection_previous[$userconnection_entity] = -1;
		}
  	// The connection distance from the userid to itself is 0
  	$userconnection_distance[$userid] = 0;
  	// $userconnection_temp1_array keeps track of the entities we still need to work on 
  	$userconnection_temp1_array = $userconnection_entities_array;
  	
  	while (count ($userconnection_temp1_array) > 0) { // more elements in $userconnection_temp1_array
  	  $userconnection_userentity_id = find_minimum ($userconnection_temp1_array);
  		if ($userconnection_userentity_id == $to_user_id) {
  			$userconnection_previous_array = $userconnection_previous;
  	    // Can stop computing the distance if we have reached the to_user_id
  	  }
			
    	$userconnection_temp2_array = array_search ($userconnection_userentity_id, $userconnection_temp1_array);
    	$userconnection_temp1_array[$userconnection_temp2_array] = false;
    	$userconnection_temp1_array = array_filter ($userconnection_temp1_array); // filters away the false elements
    	// Find all friends linked to $userconnection_temp2_array
    	$invitees = $database->database_query("SELECT friend_user_id1 FROM se_friends WHERE friend_user_id2='$userconnection_userentity_id' AND friend_status = '1'");
    	while ($row = $database->database_fetch_assoc($invitees)) {
    		$link_id = $row['friend_user_id1'];
				userconnection_calculate_distance ($userconnection_userentity_id, $link_id);
    	}
    	$inviters = $database->database_query("SELECT friend_user_id2 FROM se_friends WHERE friend_user_id1='$userconnection_userentity_id' AND friend_status = '1'");
    	while ($row = $database->database_fetch_assoc($inviters)) {
    	
    		$link_id = $row['friend_user_id2'];
				userconnection_calculate_distance ($userconnection_userentity_id, $link_id);
    	}
  	}
		// The path is found in the $userconnection_previous values from $fromid to $to_user_id
  	$userconnection_temp = 0;
  	// If user visiting his/her profile then $to_user_id is 0 so for terminating this we assign -1 to $to_user_id
  	if (empty($to_user_id)) {
  		$to_user_id = -1;
  	}
  	$userconnection_currententity = $to_user_id;
		
  	while ($userconnection_currententity != $userid && $userconnection_currententity != -1) {
    	$userconnection_links_array[$userconnection_temp++] = $userconnection_currententity;
    	$userconnection_currententity = $userconnection_previous_array[$userconnection_currententity];
  	}
 
  	if ($userconnection_currententity != $userid) { 
  		 $empty =array();
  		 // HERE WE ARE ASSIGING TWO ARRAY ($empty ,$userconnection_distance) TO A NEW ARRAY 
  		$userconnection_combind_path_contacts_array = array($empty, $userconnection_distance);
  		return $userconnection_combind_path_contacts_array;
  	} 
  	else {
    	// Display the connection paths in the reverse order
    	$userconnection_preventity = $userid;
    	$userconnection_output[] = $user->user_info['user_id'];
			// Entering the values in ouput array
    	for ($i = $userconnection_temp - 1; $i >= 0; $i--) {
    	  $userconnection_temp1 = $userconnection_links_array[$i];
    	  $userconnection_output[] = $userconnection_temp1;
    	  $userconnection_preventity = $userconnection_temp1;
    	} 
  	}
		// HERE WE ARE COMPARING No. OF ELEMENT IN $USERCONNECTION_OUTPUT AND LEVEL BECAUSE WE ASSINGED $larget TO 4 IN CASE OF LEVEL LESS THEN 4 SO IF ADMIN ASSIGN LEVEL LESS THEN 4 THEN IT WILL ALWAYS RETURN A PATH OF 4 LEVEL AND WE DON'T WANT THIS  
  	if (count($userconnection_output) > $userconnection_setting['level']){
  		$empty	= array();
  		// HERE WE ARE ASSIGING TWO ARRAY ($empty ,$userconnection_distance) TO A NEW ARRAY 
  		$userconnection_combind_path_contacts_array = array($empty, $userconnection_distance);
  	}
  	else {
  	// HERE WE ARE ASSIGING TWO ARRAY ($userconnection_output ,$userconnection_distance) TO A NEW ARRAY  	
		 $userconnection_combind_path_contacts_array = array($userconnection_output, $userconnection_distance);
  	}
  	// CACHE
    if (is_object($cache_object)) {
	    $cache_object->store($userconnection_output, 'userconnection_combind_path_contacts_array_cache');
    }
	}
 return $userconnection_combind_path_contacts_array;
}
// END OF USER CONNECTION FUNCTION


//  FIND THE USER ID OF AN ENTITY THAT HAS MININMUN DISTANCE IN THE RESPECT OF ALL USERS.
// INPUT: $ENTITIES MUST HAVE AT LEAST ONE KEY 
// OUTPUR: RETURN THE USER ID OF AN ENTITY THAT HAS MININMUN DISTANCE IN THE RESPECT OF ALL USERS. THIS USERS ID IS A KEY OF $userconnection_distance ARRAY
function find_minimum($entities) {
  
	global $userconnection_distance;
  $keys = array_keys ($entities);
  $minid = $entities[$keys[0]];
  $mindist = $userconnection_distance[$minid];
  foreach ($keys as $key) {
    $tentity = $entities[$key];
    if ($userconnection_distance[$tentity] < $mindist) {
      $mindist = $userconnection_distance[$tentity];
      $minid = $tentity;
    }
  }
  return $minid;
}
// END OF THE FUNCTION


function userconnections_lsettings($key, $type) {
		return false;
}

// THIS FUNCTION JUST SET THE PREVIOUS ENTITY VALUE
// INPUT:  $userconnection_temp2_array : USER ID OF PARENT ENTITY, $v USER ID OF CHILD
// OUTPUT: CALCULATE THE DISTANCE BETWEEN CHILD AND PARENT OF AN ENTITY AND SET CALCULATED VALUE IN THE DISTANCE VECTOR OF THIS ENTITY(A USER ID)
function userconnection_calculate_distance($userconnection_temp2_array, $v) {
	 global $userconnection_distance;
	 global $userconnection_previous;	
   if ($userconnection_distance[$v] > $userconnection_distance[$userconnection_temp2_array] + 1) {
    $userconnection_distance[$v] = $userconnection_distance[$userconnection_temp2_array] + 1;
    $userconnection_previous[$v] = $userconnection_temp2_array;
  }
}

//
// THIS FUNCTION RETURNS AN ARRAY OF CONNECTED USER'S INFORMATION :  PHOTO , USER_UD, USERNAMES
// INPUT: AN ARRAY WHICH IS RETURN BY USER_CONNECTION_PATH FUNCTION
// OUTPUT: RETURN AN ARRAY OF CONNECTED USER'S INFORMATION
//
function userconnection_users_information($path_array) {
	global $setting, $database,$user;
  $path = NULL;
  
  // CACHING
  $cache_object = SECache::getInstance('serial');
  if (is_object($cache_object) ) {
    $path = $cache_object->get('shortest_path');
  }
  
  // RETRIEVAL
  if (!is_array($path)) {
  	// HERE WE WILL TAKE ALL USER ID'S IN A SINGLE VARIABLE $USERS_ID
  	$users_id = implode(",", $path_array);
  	$id =	 $user->user_info['user_id'];
  	$sql = "SELECT user_id, user_username, user_fname, user_lname, user_photo, user_lastlogindate, user_dateupdated FROM se_users WHERE ((user_verified='1' AND user_enabled='1' AND user_search='1') OR (user_id = '$id')) AND user_id IN ($users_id) ";
    $resource = $database->database_query($sql);
    $path = array();   
    while ($user_info = $database->database_fetch_assoc($resource)) {
    	
      $shortest_user = new se_user();
      $shortest_user->user_info['user_id'] = $user_info['user_id'];
      $shortest_user->user_info['user_username'] = $user_info['user_username'];
      $shortest_user->user_info['user_photo'] = $user_info['user_photo'];
      $shortest_user->user_info['user_fname'] = $user_info['user_fname'];
      $shortest_user->user_info['user_lname'] = $user_info['user_lname'];
      $shortest_user->user_info['user_lastlogindate'] = $user_info['user_lastlogindate'];
      $shortest_user->user_info['user_dateupdated'] = $user_info['user_dateupdated'];
      $shortest_user->user_displayname();
      $path[$user_info['user_id']] =& $shortest_user;
     
      unset($shortest_user);
    }
//    
    foreach ($path_array as $l) {
    	if (!empty($path[$l])) {
    	  $new_user_array[] = $path[$l];
    	}
    }
    // CACHE
    if (is_object($cache_object)) {
      $cache_object->store($path, 'shortest_path');
    }
  }
	
  return $new_user_array;
}
// END OF THE FUNCTION


//
// THIS FUNCTION RETURNS AN ARRAY OF ALL THE ADMIN DRIVEN USERCONNECTION PLUIN SETTINGS  
// INPUT: 
// OUTPUT: RETURN AN ARRAY OF USERCONNECTION_SETTINGS TABLE VALUE'S
//
function userconnection_get_site_settings() {
	global $database;
	$row = $database->database_query("SELECT * FROM userconnection_settings");
	$result = $database->database_fetch_assoc($row);
	return $result;
}
// END OF THE FUNCTION


// THIS FUNCTION RETURNS AN ARRAY OF REALATIONSHIP AMONG USERS
// INPUT: AN ARRAY WHICH IS RETURN BY USER_CONNECTION_PATH FUNCTION
// OUTPUT: RETURN AN ARRAY OF REALATIONSHIP AMONG USERS
//
function userconnection_frnd_relationship($path_array) {
	global $setting, $database;
	$temp =-1;
	foreach ($path_array as $key) {
		$query = $database->database_query("SELECT friend_type FROM se_friends WHERE friend_user_id1 = $temp AND friend_user_id2 = $key ");
		 $temp = $key;
		 while($ee = $database->database_fetch_assoc($query)) {
		 	$relation[] = $ee['friend_type'];
		 }
	}
 return $relation;
}
// END OF THE FUNCTION
?>
