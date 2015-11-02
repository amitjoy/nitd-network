{* $Id: user_home_userconnection.tpl 8 2009-09-16 06:02:53Z SocialEngineAddOns $ *}
{* CODE FOR SHOWING USERS MY NETWORK *}
<div class='spacer10'></div>
  <div class='header'>{lang_print id=650002039}</div>
	  <div class='network_content'>
		  <div class="user-network" align="center">
		  	<div class="usernetwork-1stdegree">
					{ if !empty($count_first_degree_contacts)}
					<a href="./user_friends.php">{$count_first_degree_contacts}</a>
					<div class="usernetwork-contacts-text">
						<a href="./user_friends.php">{lang_print id=650002040}</a>
					</div>
					{ else }
					{$count_first_degree_contacts}
					<div class="usernetwork-contacts-text">
						{lang_print id=650002040}
					</div>
					{ /if }					
				</div>
				<div class="usernetwork-2nddegree">
					{ if !empty($count_second_degree_contacts)}
					<a href="./user_userconnection_contacts_2degree.php">{$count_second_degree_contacts}</a>
					<div class="usernetwork-contacts-text">
						<a href="./user_userconnection_contacts_2degree.php">{lang_print id=650002041}</a>
					</div>
					{ else }
					{$count_second_degree_contacts}
					<div class="usernetwork-contacts-text">
						{lang_print id=650002041}
					</div>
					{ /if }				
				</div>
				
				<div class="usernetwork-3rddegree">
				{ if !empty($count_third_degree_contacts)}
					<a href="./user_userconnection_contacts_3degree.php">{$count_third_degree_contacts}</a>
					<div class="usernetwork-contacts-text">
						<a href="./user_userconnection_contacts_3degree.php">{lang_print id=650002042}</a>
					</div>
					{ else }
					{$count_third_degree_contacts}
					<div class="usernetwork-contacts-text">
						{lang_print id=650002042}
					</div>
					{ /if }					
				</div>
				<div class="width-full user-network-bottom-link">
					<a href="invite.php">Expand your network now! <b>&raquo;</b></a>
				</div>
			</div>	  
