<?php

if(!defined('SE_PAGE')) { exit(); }

include_once "./include/functions_userconnection.php";
global $userconnection_setting; 
$userconnection_setting = userconnection_get_site_settings();
$userconnection_depth = 0;

//print_r($owner); die;
if (!empty($user->user_exists)) {	
	if ($page == "profile" || $page == "user_home" || $page == "user_userconnection_contacts_2degree" || $page == "user_userconnection_contacts_3degree") {
		// CHECKING USER VISITING OTHERS PROFILE OR NOT	
		
		//START: CODE FOR PROFILE PAGE ONLY
		if ($page == "profile" && $userconnection_setting[profile_page]) {
			if ($owner->user_info['user_username'] != $user->user_info['user_username']) { 
			  if (($owner->user_info['user_enabled'] == 1 && $owner->user_info['user_search'] == 1 && $owner->user_info['user_verified'] == 1) && $owner->usersetting_info['usersetting_userconnection'] == 0 ) {
			  	$userconnection_combind_path_contacts_array = user_connection_path($user->user_info['user_id'], $owner->user_info['user_id']);
					$path_array = $userconnection_combind_path_contacts_array['0'];
					$count_path_array = count($path_array);
	   	  }
			  if (!empty($count_path_array)) {
			  	 $path_information = userconnection_users_information($path_array);
			  	 $frnd_relationship = userconnection_frnd_relationship($path_array);
			  	 $userconnection_depth	= $count_path_array-1;
				   if ($userconnection_depth == 1) {
				   	$userconnection_depth_extension = "st";
				   }
				   elseif ($userconnection_depth == 2) {
				   		$userconnection_depth_extension = "nd";
				   }
				   elseif ($userconnection_depth == 3) {
				   		$userconnection_depth_extension = "rd";
				   }
				   else {
				   		$userconnection_depth_extension = "th";
				   }
				}
				if (!empty($count_path_array) || $userconnection_setting['is_message'] )	{		
			    if ($userconnection_setting['userconnection_position'] == 1 ) {
				 	  $plugin_vars['menu_profile_tab'] = Array('file'=> 'profile_userconnection.tpl', 'title' => 650002002);
					  $plugin_vars['menu_profile_side'] = '';
			    }	else {
					 $plugin_vars['menu_profile_tab'] = '';
				   $plugin_vars['menu_profile_side'] = Array('file'=> 'profile_userconnection.tpl', 'title' => 650002019);
					}	
					$smarty->assign_by_ref('toatl_users', $count_path_array);
					$smarty->assign_by_ref('shortest_path', $path_information);
					$smarty->assign('userconnection_setting', $userconnection_setting);
					$smarty->assign('userconnection_relation', $frnd_relationship);
					$smarty->assign('userconnection_depth', $userconnection_depth);
					$smarty->assign('userconnection_depth_extension', $userconnection_depth_extension);
					$smarty->assign('user_name', $owner->user_displayname_short);
				}
			}
		}
		// End  : CODE FOR PROFILE PAGE END
		
		//Start: CODE FOR USER HOME , USER CONNECTION 2nd DEGREE AND USER CONNECTION 3rd DEGREE PAGE 
		elseif (($page == "user_home" || $page == "user_userconnection_contacts_2degree" || $page == "user_userconnection_contacts_3degree") && $userconnection_setting[user_home_page] ) {
			$userconnection_combind_path_contacts_array = user_connection_path($user->user_info['user_id'], $owner->user_info['user_id']);
			$first_degree_contacts_id  = array();
			$second_degree_contacts_id = array();
			$third_degree_contacts_id  = array();
			$count_first_degree_contacts = 0;
			$count_second_degree_contacts = 0;
			$count_third_degree_contacts = 0;
			// HERE WE ARE ECTRACTING CONTACTS DEGREE ARRAY FROM $userconnection_combind_path_contacts_array
			$user_contacts_degree = $userconnection_combind_path_contacts_array[1];
			// HERE WE ARE FINDING 1st ,2nd AND 3rd DEGREE CONTACTS ID AND TOTAL NO OF 1st ,2nd AND 3rd DEGREE USER 		
			for ($distance =1; $distance<4; $distance++) {
				$id = array_keys ($user_contacts_degree,$distance);
				switch ($distance) {
					case 1:
						$first_degree_contacts_id  = $id;
						$count_first_degree_contacts = count($first_degree_contacts_id);
						break;
					case 2:
						$second_degree_contacts_id = $id;
						$count_second_degree_contacts = count($second_degree_contacts_id);
						break;
					case 3:
						$third_degree_contacts_id  = $id;
						$count_third_degree_contacts = count($third_degree_contacts_id);
						break;
				}
			}
		
			$smarty->assign('count_first_degree_contacts', $count_first_degree_contacts);
			$smarty->assign('count_second_degree_contacts', $count_second_degree_contacts);
			$smarty->assign('count_third_degree_contacts', $count_third_degree_contacts);
			
			// HERE WE ARE CREATING A NEW RIGHT SIDE MENU OF MY NETWORK
			$plugin_vars['menu_userhome'] = Array('file'=> 'user_home_userconnection.tpl');
			if (!empty($plugin_vars['menu_user'])) {
			  $smarty->assign_hook('menu_user_apps', $plugin_vars['menu_user']);
			}
		}
		// End  : CODE FOR USER HOME , USER CONNECTION 2nd DEGREE AND USER CONNECTION 3rd DEGREE PAGE END
	}
	// HERE WE ARE CREATING A NEW APPLICATION IN MY APPLICATION MENU
	$plugin_vars['menu_user'] = Array('file' => 'user_userconnection_setting.php', 'icon' => 'userconnection_userconnection16.gif', 'title' => 650002055);
}
?>