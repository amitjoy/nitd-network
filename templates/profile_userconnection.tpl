{*  $Id: profile_userconnection.tpl 1 2009-09-07 09:36:11Z SocialEngineAddOns $ * }

{ *  LAYOUT FOR PROFILE TAB *}
{ if $userconnection_setting.userconnection_position eq '1'}
	{ if !empty($toatl_users) || !empty($userconnection_setting.is_message) }
	{if !empty($shortest_path)}
	<div class="profile_headline">{lang_print id=650002019} {$user_name}</div>
	<div> 
	
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td valign="top">
						<div class="userconnection-right-block" align="center">
							<div class="userconnection-right-block-inner">
					  		{if !empty($userconnection_setting)}
					    		{section name=path_loop loop=$shortest_path max=$userconnection_setting.level}
						    		
						    			{if !empty($shortest_path[path_loop])}
						    			{if $smarty.section.path_loop.last}
						    				<div class="userconnection-user-photo-block">
							    				<div class="userconnection-user-photo">
							    						<img src='{$shortest_path[path_loop]->user_photo("./images/nophoto.gif", TRUE)}' class='photo' width='60' height='60' border='0' alt='{$shortest_path[path_loop]->user_displayname}' title="{$shortest_path[path_loop]->user_displayname}" /></a>
							    						<br />
							      				{$shortest_path[path_loop]->user_displayname|regex_replace:"/&#039;/":"'"|truncate:15:"...":true}
							      			<div>
								      			<div class="fleft margin-top-10"> 
															<a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$shortest_path[path_loop]->user_displayname|escape:url}&to_id={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');" title="Send Message"><img src="./images/icons/sendmessage16.gif" alt="Send Message" title="Send Message to {$shortest_path[path_loop]->user_displayname_short}" border="0" /></a>&nbsp;
													 		{if $smarty.section.path_loop.index neq '1'}
															
																<span id='addfriend_{$shortest_path[path_loop]->user_info.user_id}'><a href="javascript:TB_show('{lang_print id=876}', 'user_friends_manage.php?user={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');" title="Add to Friend"><img src='./images/icons/user_add_16x16.png' class='icon' border='0' alt="Add to Friend" title="Add to Friend"></a></span>
					        						
															{/if}
														</div>
														{if $userconnection_setting.userconnection_degree eq '0'}
								      				<div class="user-connection-degree-green fleft userconnection-rb-degree" align="center" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div> 
												  	{elseif $userconnection_setting.userconnection_degree eq '1'}
												  		<div class="user-connection-degree-yellow fleft userconnection-rb-degree" align="center" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div> 
												  	{elseif $userconnection_setting.userconnection_degree eq '2'}
												  		<div class="user-connection-degree-orange fleft userconnection-rb-degree" align="center" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div>
												  	{else}
												  		<div class="user-connection-degree-blue fleft userconnection-rb-degree" align="center" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div> 
														{/if}
								    				</div>
							    				</div>
							    			</div>	
							    				
							    		{elseif $smarty.section.path_loop.first}
							    		<div class="userconnection-user-photo-block">
												<div class="userconnection-user-photo">
						    					<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}'><img src='{$shortest_path[path_loop]->user_photo("./images/nophoto.gif", TRUE)}' class='photo' width='60' height='60' border='0' alt='{$shortest_path[path_loop]->user_displayname}' title="{$shortest_path[path_loop]->user_displayname}" /></a>
						    			  	<br />
						      				<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}' title="{$shortest_path[path_loop]->user_displayname}">{$shortest_path[path_loop]->user_displayname|regex_replace:"/&#039;/":"'"|truncate:15:"...":true}</a>
						      			</div>
						      			<div>
						      				{if $userconnection_setting.userconnection_degree eq '0'}
								      				<div class="user-connection-degree-green userconnection-rb-degree clr" align="center" title="You">Y<span>ou</span></div> 
												  	{elseif $userconnection_setting.userconnection_degree eq '1'}
												  		<div class="user-connection-degree-yellow userconnection-rb-degree clr" align="center" title="You">Y<span>ou</span></div>
												  	{elseif $userconnection_setting.userconnection_degree eq '2'}
												  		<div class="user-connection-degree-orange userconnection-rb-degree clr" align="center" title="You">Y<span>ou</span></div>
												  	{else}
												  		<div class="user-connection-degree-blue userconnection-rb-degree clr" align="center" title="You">Y<span>ou</span></div> 
													{/if}
						      			</div>	
							    		</div>
					      			{else}
					      				<div class="userconnection-user-photo-block">
						    					<div class="userconnection-user-photo">
						    						<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}'><img src='{$shortest_path[path_loop]->user_photo("./images/nophoto.gif", TRUE)}' class='photo' width='60' height='60' border='0' alt='{$shortest_path[path_loop]->user_displayname}' title="{$shortest_path[path_loop]->user_displayname}" /></a>
						    						<br />
						      					<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}' title="{$shortest_path[path_loop]->user_displayname}">{$shortest_path[path_loop]->user_displayname|regex_replace:"/&#039;/":"'"|truncate:15:"...":true}</a> 
						      			
														<div class="margin-top-10">
							      					<a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$shortest_path[path_loop]->user_displayname|escape:url}&to_id={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');" title="Send Message"><img src="./images/icons/sendmessage16.gif" alt="Send Message" title="Send Message  to {$shortest_path[path_loop]->user_displayname_short}" border="0" /></a>&nbsp;
															{if $smarty.section.path_loop.index neq '1'}
																	<span id='addfriend_{$shortest_path[path_loop]->user_info.user_id}'><a href="javascript:TB_show('{lang_print id=876}', 'user_friends_manage.php?user={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');" title="Add to Friend"><img src='./images/icons/user_add_16x16.png' class='icon' border='0' alt="Add to Friend" title="Add to Friend"></a></span>
						        						
															{/if}
														</div>
				      						</div>
				      					</div>	
				      				{/if}
				      				
				      				{if !($smarty.section.path_loop.last)}
				      				
				      					<div class="userconnection-arrow-rightbox" align="center">
		      							{ * Start ToolTip * }
		      			  			{if !empty($userconnection_relation[path_loop])}
		    									<div class="tipper"  rel="{literal}{content:'userconnection_tooltip_{/literal}{$shortest_path[path_loop]->user_info.user_id}{literal}'}{/literal}">
		    					 					<div id="userconnection_tooltip_{$shortest_path[path_loop]->user_info.user_id}" style="display:none;">
		    					 						{$userconnection_relation[path_loop]}
		    					 					</div>
					    					{/if}
					    			{ * End ToolTip * }
						    					{if $userconnection_setting.userconnection_arrow eq '0'}
						    						<img src="./images/icons/userconnection-arrow-green.gif"><br/>
						    					{elseif $userconnection_setting.userconnection_arrow eq '1'} 
						    					  <img src="./images/icons/userconnection-arrow-yellow.gif" > <br/>
						    					{elseif $userconnection_setting.userconnection_arrow eq '2'}
						    					  <img src="./images/icons/userconnection-arrow-orange.gif"> <br/>
						    					{else}
						    					  <img src="./images/icons/userconnection-arrow-blue.gif"> <br/>
						    					{/if}	
						    					{$userconnection_relation[path_loop]|truncate:7:"..."}
				    								{ * Start For ToolTip Div* }
		    										{if !empty($userconnection_relation[path_loop])}
		    											</div>
		    										{/if}
		    									{ * End For ToolTip Div * }
						    					</div>
						    				{/if}
						    			{/if}
					    		{/section}
					    	{/if} 
			    	</div>
	    		</div>
	    	</td>
	   	</tr> 		
	   </table> 
	  {elseif $userconnection_setting.is_message }
	  {$userconnection_setting.message}
	  
	 </div>
	 {/if} 
	 <div class='portal_spacer'></div>
	 {/if}
	 
	 
	 
	 
	 
	 
{ *  LAYOUT FOR SIDEBAR HORIZONTAL TAB *}	 
	 
{ elseif $userconnection_setting.userconnection_position eq '2' }

{ if !empty($toatl_users) || !empty($userconnection_setting.is_message) }
<div class='header'>{lang_print id=650002019}{$user_name}</div>
<div class='portal_content'>
{if !empty($shortest_path)}
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top">
		  	{if !empty($userconnection_setting)}
					<div class="fleft" align="center">
		  		{section name=path_loop loop=$shortest_path max=$userconnection_setting.level}
		    			{if !empty($shortest_path[path_loop])}
		    				{if $smarty.section.path_loop.last}
		    					<div class="userconnection-user-photo">
		    						<img src='{$shortest_path[path_loop]->user_photo("./images/nophoto.gif", TRUE)}' class='photo' width='60' height='60' border='0' alt='{$shortest_path[path_loop]->user_displayname}' title="{$shortest_path[path_loop]->user_displayname}" /></a>
		    						<br />
		      					{$shortest_path[path_loop]->user_displayname|regex_replace:"/&#039;/":"'"|truncate:15:"...":true} 
		      					<div>
			      					<div class="margin-top-10 fleft" align="center">
												<a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$shortest_path[path_loop]->user_displayname|escape:url}&to_id={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');" title="Send Message"><img src="./images/icons/sendmessage16.gif" alt="Send Message" title="Send Message to {$shortest_path[path_loop]->user_displayname_short}" border="0" /></a>&nbsp;
									 				{if $smarty.section.path_loop.index neq '1'}
												
															<span id='addfriend_{$shortest_path[path_loop]->user_info.user_id}'><a href="javascript:TB_show('{lang_print id=876}', 'user_friends_manage.php?user={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');" title="Add to Friend"><img src='./images/icons/user_add_16x16.png' class='icon' border='0' alt="Add to Friend" title="Add to Friend"></a></span>
	        											
													
													{/if}
											
											</div>
											{if $userconnection_setting.userconnection_degree eq '0'}
				      					<div class="user-connection-degree-green fleft userconnection-rb-degree" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div> 
								  		{elseif $userconnection_setting.userconnection_degree eq '1'}
								  			<div class="user-connection-degree-yellow fleft userconnection-rb-degree" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div> 
								  		{elseif $userconnection_setting.userconnection_degree eq '2'}
								  			<div class="user-connection-degree-orange fleft userconnection-rb-degree" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div>
								  		{else}
								  			<div class="user-connection-degree-blue fleft userconnection-rb-degree" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div> 
											{/if}
										</div>
									</div>
								{elseif $smarty.section.path_loop.first}
									<div class="userconnection-user-photo">
			    					<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}'><img src='{$shortest_path[path_loop]->user_photo("./images/nophoto.gif", TRUE)}' class='photo' width='60' height='60' border='0' alt='{$shortest_path[path_loop]->user_displayname}' title="{$shortest_path[path_loop]->user_displayname}" /></a>
			    			  	<br />
			      				<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}' title="{$shortest_path[path_loop]->user_displayname}">{$shortest_path[path_loop]->user_displayname|regex_replace:"/&#039;/":"'"|truncate:15:"...":true}</a>
			      					<div>
						      				{if $userconnection_setting.userconnection_degree eq '0'}
								      				<div class="user-connection-degree-green userconnection-lb-degree clr" align="center" title="You">Y<span>ou</span></div> 
												  	{elseif $userconnection_setting.userconnection_degree eq '1'}
												  		<div class="user-connection-degree-yellow userconnection-lb-degree clr" align="center" title="You">Y<span>ou</span></div>
												  	{elseif $userconnection_setting.userconnection_degree eq '2'}
												  		<div class="user-connection-degree-orange userconnection-lb-degree clr" align="center" title="You">Y<span>ou</span></div>
												  	{else}
												  		<div class="user-connection-degree-blue userconnection-lb-degree clr" align="center" title="You">Y<span>ou</span></div> 
														{/if}
						      			</div>		
			      			</div>
			  
															
		    				{else}
		    					<div class="userconnection-user-photo" align="center">
		    						<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}'>
		    							<img src='{$shortest_path[path_loop]->user_photo("./images/nophoto.gif", TRUE)}' class='photo' width='60' height='60' border='0' alt='{$shortest_path[path_loop]->user_displayname}' title="{$shortest_path[path_loop]->user_displayname}" /></a>
		    						<br />
		    						
		      					<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}' title="{$shortest_path[path_loop]->user_displayname}">{$shortest_path[path_loop]->user_displayname|regex_replace:"/&#039;/":"'"|truncate:15:"...":true}</a> 
		      					<div class="margin-top-10">
		      					
										<a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$shortest_path[path_loop]->user_displayname|escape:url}&to_id={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');" title="Send Message"><img src="./images/icons/sendmessage16.gif" alt="Send Message" title="Send Message to {$shortest_path[path_loop]->user_displayname_short}" border="0" /></a>&nbsp;
										{if $smarty.section.path_loop.index neq '1'}
												<span id='addfriend_{$shortest_path[path_loop]->user_info.user_id}'><a href="javascript:TB_show('{lang_print id=876}', 'user_friends_manage.php?user={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');" title="Add to Friend"><img src='./images/icons/user_add_16x16.png' class='icon' border='0' alt="Add to Friend" title="Add to Friend"></a></span>
	        						
										{/if}
									</div>
		      				</div>
		      			{/if}	
		      			
		      			
		      			{if !($smarty.section.path_loop.last)}
		      			<div class="userconnection-arrow">
		      			{ * Start ToolTip * }
		      			  {if !empty($userconnection_relation[path_loop])}
		    						<div class="tipper"  rel="{literal}{content:'userconnection_tooltip_{/literal}{$shortest_path[path_loop]->user_info.user_id}{literal}'}{/literal}">
		    					 	<div id="userconnection_tooltip_{$shortest_path[path_loop]->user_info.user_id}" style="display:none;">{$userconnection_relation[path_loop]}</div>
		    					{/if}
		    			{ * End ToolTip * }
		    					<span title="{$userconnection_relation[path_loop]}">{$userconnection_relation[path_loop]|truncate:4:".."}</span><br />
		    						{if $userconnection_setting.userconnection_arrow eq '0'}
				    					<img src="./images/icons/userconnection-arrow-green.gif "> 
				    				{elseif $userconnection_setting.userconnection_arrow eq '1'}
				    				  <img src="./images/icons/userconnection-arrow-yellow.gif" > 
				    				{elseif $userconnection_setting.userconnection_arrow eq '2'}
				    				  <img src="./images/icons/userconnection-arrow-orange.gif"> 
				    				{else}
				    				  <img src="./images/icons/userconnection-arrow-blue.gif"> 
				    				{/if}	    						
                  	{ * Start For ToolTip Div* }
		    						{if !empty($userconnection_relation[path_loop])}
		    						</div>
		    					{/if}
		    					{ * End For ToolTip Div * }
		   				  </div>
		    				{/if}
		    			{/if}
		    		{/section}
		    	{/if} 
    		</div>
    	</td>
   	</tr> 		
   </table> 
  {elseif $userconnection_setting.is_message }
  {$userconnection_setting.message}
  {/if} 
</div>
<div class='portal_spacer'></div>
	{/if}

	
	
{ *  LAYOUT FOR SIDEBAR VERTICAL TAB *}	 	
	
	
	
{elseif $userconnection_setting.userconnection_position eq '0'}
	
{if !empty($toatl_users) || !empty($userconnection_setting.is_message) }
<div class='header'>{lang_print id=650002019}{$user_name}</div>
<div class='portal_content'> 
{if !empty($shortest_path)}
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top">
		  	{if !empty($userconnection_setting)}
			  	{section name=path_loop loop=$shortest_path max=$userconnection_setting.level}
			  		{if !empty($shortest_path[path_loop])}
					  <div class="fleft" align="center">
						 <div class="fleft">
			    		{if $smarty.section.path_loop.last}
			    		<div class="userconnection-left-block">
			    			<div class="userconnection-user-photo">
			    				<img src='{$shortest_path[path_loop]->user_photo("./images/nophoto.gif", TRUE)}' class='photo' width='60' height='60' border='0' alt='{$shortest_path[path_loop]->user_displayname}' title="{$shortest_path[path_loop]->user_displayname}" /></a>
			    				<br />
			      	   {$shortest_path[path_loop]->user_displayname|regex_replace:"/&#039;/":"'"|truncate:15:"...":true}
			      	   	</div>
			      	   	<div class="fleft">
									 <div class="friend-action-send" align="left">
											<a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$shortest_path[path_loop]->user_displayname|escape:url}&to_id={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');" title="Send Message"><img src="./images/icons/sendmessage16.gif" alt="Send Message"  class="icon"  title="Send Message to {$shortest_path[path_loop]->user_displayname_short}" border="0" /></a>&nbsp;
											<a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$shortest_path[path_loop]->user_displayname|escape:url}&to_id={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');" title="Send Message to {$shortest_path[path_loop]->user_displayname_short}"></a>
									 </div>
									{if $smarty.section.path_loop.index neq '1'}
									 <div class="friend-action-add" align="left">
											<span id='addfriend_{$shortest_path[path_loop]->user_info.user_id}'><a href="javascript:TB_show('{lang_print id=876}', 'user_friends_manage.php?user={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');" title="Add to Friend"><img src='./images/icons/user_add_16x16.png' class='icon' border='0' alt="Add to Friend" title="Add to Friend"></a>
											</span>					
        						</div>
        						{/if}
        						<div class="fleft" align="center">
						      		{if $userconnection_setting.userconnection_degree eq '0'}
						      			<div class="user-connection-degree-green left-degree" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div> 
										  {elseif $userconnection_setting.userconnection_degree eq '1'}
										  	<div class="user-connection-degree-yellow left-degree" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div> 
										  {elseif $userconnection_setting.userconnection_degree eq '2'}
										  	<div class="user-connection-degree-orange left-degree" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div>
										  {else}
										  	<div class="user-connection-degree-blue left-degree" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div> 
											{/if}
										</div>
									</div>
								</div>	
			      	   
							{elseif $smarty.section.path_loop.first}
							<div class="userconnection-left-block">
								<div class="userconnection-user-photo">
			    				<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}'><img src='{$shortest_path[path_loop]->user_photo("./images/nophoto.gif", TRUE)}' class='photo' width='60' height='60' border='0' alt='{$shortest_path[path_loop]->user_displayname}' title="{$shortest_path[path_loop]->user_displayname}" /></a>
			    			  <br />
			      			<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}' title="{$shortest_path[path_loop]->user_displayname}">{$shortest_path[path_loop]->user_displayname|regex_replace:"/&#039;/":"'"|truncate:15:"...":true}</a>
			      		</div>
      					<div class="left-you-button">
			      				{if $userconnection_setting.userconnection_degree eq '0'}
					      				<div class="user-connection-degree-green userconnection-lb-degree clr" align="center" title="You">Y<span>ou</span></div> 
									  	{elseif $userconnection_setting.userconnection_degree eq '1'}
									  		<div class="user-connection-degree-yellow userconnection-lb-degree clr" align="center" title="You">Y<span>ou</span></div>
									  	{elseif $userconnection_setting.userconnection_degree eq '2'}
									  		<div class="user-connection-degree-orange userconnection-lb-degree clr" align="center" title="You">Y<span>ou</span></div>
									  	{else}
									  		<div class="user-connection-degree-blue userconnection-lb-degree clr" align="center" title="You">Y<span>ou</span></div> 
											{/if}
			      			</div>	
			      	</div>	
			    		{else}
			    		<div class="userconnection-left-block">
			    			<div class="userconnection-user-photo">
			    				<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}'><img src='{$shortest_path[path_loop]->user_photo("./images/nophoto.gif", TRUE)}' class='photo' width='60' height='60' border='0' alt='{$shortest_path[path_loop]->user_displayname}' title="{$shortest_path[path_loop]->user_displayname}" /></a>
			    			  <br />
			      			<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}' title="{$shortest_path[path_loop]->user_displayname}">{$shortest_path[path_loop]->user_displayname|regex_replace:"/&#039;/":"'"|truncate:15:"...":true}</a>
			      		</div>
			      			<div class="fleft">
									 <div class="friend-action-send" align="left">
											<a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$shortest_path[path_loop]->user_displayname|escape:url}&to_id={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');" title="Send Message"><img src="./images/icons/sendmessage16.gif" alt="Send Message" title="Send Message to {$shortest_path[path_loop]->user_displayname_short}" border="0" /></a>&nbsp;
											<a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$shortest_path[path_loop]->user_displayname|escape:url}&to_id={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');" title="Send Message to {$shortest_path[path_loop]->user_displayname_short}"></a>
									 </div>
									 {if $smarty.section.path_loop.index neq '1'}
									  <div class="friend-action-add">
											<div id='addfriend_{$shortest_path[path_loop]->user_info.user_id}'><a href="javascript:TB_show('{lang_print id=876}', 'user_friends_manage.php?user={$shortest_path[path_loop]->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');" title="Add to Friend"><img src='./images/icons/user_add_16x16.png' class='icon' border='0' alt="Add to Friend" title="Add to Friend"></a></div>
        							
										</div>
									 {/if}
									</div>
						 	</div>
						 </div>
						 {/if}	
		      {if !($smarty.section.path_loop.last)}
		      	 <div class="userconnection-arrow-down fleft clr" align="left"> 
		    			{if $userconnection_setting.userconnection_arrow eq '0'}
				    		<img src="./images/icons/userconnection-arrow-down-green.gif" alt="arrow" class="icon"> 
				    	{elseif $userconnection_setting.userconnection_arrow eq '1'}
				    	  <img src="./images/icons/userconnection-arrow-down-yellow.gif" alt="arrow" class="icon"> 
				    	{elseif $userconnection_setting.userconnection_arrow eq '2'}
				    		<img src="./images/icons/userconnection-arrow-down-orange.gif" alt="arrow" class="icon"> 
				    	{else}
				    	  <img src="./images/icons/userconnection-arrow-down-blue.gif" alt="arrow" class="icon"> 
				    	{/if}	
				    	<i>{$userconnection_relation[path_loop]}</i>
             </div> {/if}                 						
		    		</div>
		    		
		    		{/if}
		    	{/section}
		    {/if} 
    		</div>
    	</td>
   	</tr> 		
   </table> 
  {elseif $userconnection_setting.is_message }
  {$userconnection_setting.message}
{/if} 
</div>
<div class='portal_spacer'></div>
{/if}



{ *  LAYOUT FOR SIDERBAR VERTICAL WITHOUT PHOTO *}

{else}


{if !empty($toatl_users) || !empty($userconnection_setting.is_message) }
<div class='header'>{lang_print id=650002019}{$user_name}</div>
<div class='portal_content'> 
{if !empty($shortest_path)}
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top">
		  	{if !empty($userconnection_setting)}
			  	{section name=path_loop loop=$shortest_path max=$userconnection_setting.level}
			  		{if !empty($shortest_path[path_loop])}
						  <div class="fleft" align="center" style="width:100%;">
								 <div align="center" style="width:100%;" class="fleft">
								 	<div class="fleft" style="width:97%;">
					    	 		{if $smarty.section.path_loop.last}
					    	 			<div class="userconnection-user-name">
							    		{$shortest_path[path_loop]->user_displayname|regex_replace:"/&#039;/":"'"|truncate:15:"...":true}
							    		 {if $userconnection_setting.userconnection_degree eq '0'}
							      			<div class="user-connection-degree-green userconnection-lb-degree clr" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div> 
											  {elseif $userconnection_setting.userconnection_degree eq '1'}
											  	<div class="user-connection-degree-yellow userconnection-lb-degree clr" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span>	</div> 
											  {elseif $userconnection_setting.userconnection_degree eq '2'}
											  	<div class="user-connection-degree-orange userconnection-lb-degree clr" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div>
											  {else}
											  	<div class="user-connection-degree-blue userconnection-lb-degree clr" title="{$userconnection_depth}{$userconnection_depth_extension} Level Connection">{$userconnection_depth}<span>{$userconnection_depth_extension}</span></div> 
												{/if}
												
												
												
										
										</div>
	 								{elseif $smarty.section.path_loop.first}
									<div class="userconnection-user-name" style="width:99%;">
										<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}' title="{$shortest_path[path_loop]->user_displayname}">{$shortest_path[path_loop]->user_displayname|regex_replace:"/&#039;/":"'"|truncate:15:"...":true}</a>
										</div>
												<div class="fleft" style="width:97%;" align="center">
			      				{if $userconnection_setting.userconnection_degree eq '0'}
					      				<div class="user-connection-degree-green userconnection-lb-degree clr" align="center" title="You">Y<span>ou</span></div> 
									  	{elseif $userconnection_setting.userconnection_degree eq '1'}
									  		<div class="user-connection-degree-yellow userconnection-lb-degree clr" align="center" title="You">Y<span>ou</span></div>
									  	{elseif $userconnection_setting.userconnection_degree eq '2'}
									  		<div class="user-connection-degree-orange userconnection-lb-degree clr" align="center" title="You">Y<span>ou</span></div>
									  	{else}
									  		<div class="user-connection-degree-blue userconnection-lb-degree clr" align="center" title="You">Y<span>ou</span></div> 
											{/if}
			      			</div>
					      	{else}
					      		<div class="userconnection-user-name">
					    			<a href='{$url->url_create("profile",$shortest_path[path_loop]->user_info.user_username)}' title="{$shortest_path[path_loop]->user_displayname}">{$shortest_path[path_loop]->user_displayname|regex_replace:"/&#039;/":"'"|truncate:15:"...":true}</a>
					      		</div>
					     		{/if}
								 </div>	
									{if !($smarty.section.path_loop.last)}
				      		<div class="fleft width-full" style="margin-top:5px;margin-bottom:5px;"> 
					      	 <div class="fleft" style="width:55%;" align="right"> 
					    			{if $userconnection_setting.userconnection_arrow eq '0'}
							    		<img src="./images/icons/userconnection-arrow-down-green.gif" alt="arrow" class="icon"> 
							    	{elseif $userconnection_setting.userconnection_arrow eq '1'}
							    	  <img src="./images/icons/userconnection-arrow-down-yellow.gif" alt="arrow" class="icon"> 
							    	{elseif $userconnection_setting.userconnection_arrow eq '2'}
							    		<img src="./images/icons/userconnection-arrow-down-orange.gif" alt="arrow" class="icon"> 
							    	{else}
							    	  <img src="./images/icons/userconnection-arrow-down-blue.gif" alt="arrow" class="icon"> 
							    	{/if}	
							    </div>
			             <div class="fleft" style="width:45%;" align="left"><i>{$userconnection_relation[path_loop]}</i></div>
			            </div> 
		          	{/if}                 						
				    		</div>
				    	{/if}
			    	{/section}
			    {/if} 
	    		
	    	</td>
	   	</tr> 		
	   </table> 
	  {elseif $userconnection_setting.is_message }
	  {$userconnection_setting.message}
	{/if} 
</div>
<div class='portal_spacer'></div>
{/if}


{/if}
{* JAVSCRIPT CODE FOR TOOLTIPS *}
{literal}
<script language="javascript" type="text/javascript">
	window.addEvent('load', function(){
		new MooTooltips({
			hovered:'.tipper',		// the element that when hovered shows the tip
			ToolTipClass:'ToolTips',	// tooltip display class
			toolTipPosition:-1, // -1 top; 1: bottom - set this as a default position value if none is set on the element
			sticky:false,		// remove tooltip if closed
			fromTop: 0,		// distance from mouse or object
			fromLeft: -40,	// distance from left
			duration: 100,		// fade effect transition duration
			fadeDistance: 10    // the distance the tooltip starts the morph
		});		
	});
</script>    
{/literal}