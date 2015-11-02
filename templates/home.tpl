{include file='header-home.tpl'}

{* HOME IMAGE *}
<img src='images/home.jpg' />


{* BEGIN MAIN COLUMN *}
<div style='float: left; width: 600px;'>

   {* RECENT ACTIVITY ADVERTISEMENT BANNERS *}
      {if $ads->ad_feed != ""}
        <div style='display: block; visibility: visible; padding-bottom: 10px;'>{$ads->ad_feed}</div>
      {/if}

<!-- Start Tabs -->
<div class="simpleTabs">
	<ul class="simpleTabsNavigation">
		<li><a href="#">{lang_print id=642}</a></li>
		
	</ul>
	<div class="simpleTabsContent">

  {* SHOW NEW CONTENT TABS AND TEASERS *}
  
  <div class="home-box">
      <div style='padding: 10px;'>
        <div class='page_header'>{lang_print id=850009}</div>
        {lang_print id=657}
      </div>
  </div>

  {* SHOW RECENT NEWS ANNOUNCEMENTS IF MORE THAN ZERO *}  
  {if $news|@count > 0}
  <div class="home-box">
    <div style='padding: 10px;'>
      <div class='page_header'>{lang_print id=664}</div>
      {section name=news_loop loop=$news max=3}
        <div style='margin-top: 3px;'><img src='./images/icons/news16.gif' border='0' class='icon'><b>{$news[news_loop].announcement_subject}</b> - {$news[news_loop].announcement_date}</div>
        <div style='margin-top: 3px;'>{$news[news_loop].announcement_body}</div>
      {/section}
    </div>
  </div>
  {/if}
  
	</div>  
	<div class="simpleTabsContent">

{* SHOW PUBLIC VERSION OF ACTIVITY LIST *}  
  {if $actions|@count > 0}
    <div class='portal_whatsnew'>


      {* SHOW ACTIONS *}
      {section name=actions_loop loop=$actions max=20}
        <div id='action_{$actions[actions_loop].action_id}' class='portal_action{if $smarty.section.actions_loop.first}_top{/if}'>
          <table cellpadding='0' cellspacing='0'>
          <tr>
          <td nowrap style='padding: 0 5px;' align='left' valign="top">
		<img src='{$user->user_photo2($actions[actions_loop].user_photo,$actions[actions_loop].action_user_id,'./images/nophoto.gif', TRUE)}' class='photo3' width='40' height='40' border='0' style="border: 3px solid #f2f2f2;">
          </td>
          <td valign='top'><img src='./images/icons/{$actions[actions_loop].action_icon}' border='0' class='icon' alt='' /></td>
          <td valign='top' width='100%'>
            {assign var='action_date' value=$datetime->time_since($actions[actions_loop].action_date)}
            <div class='portal_action_date'>{lang_sprintf id=$action_date[0] 1=$action_date[1]}</div>
            {assign var='action_media' value=''}
            {if $actions[actions_loop].action_media !== FALSE}{capture assign='action_media'}{section name=action_media_loop loop=$actions[actions_loop].action_media}<a href='{$actions[actions_loop].action_media[action_media_loop].actionmedia_link}'><img src='{$actions[actions_loop].action_media[action_media_loop].actionmedia_path}' border='0' width='{$actions[actions_loop].action_media[action_media_loop].actionmedia_width}' class='recentaction_media'></a>{/section}{/capture}{/if}
            {lang_sprintf assign=action_text id=$actions[actions_loop].action_text args=$actions[actions_loop].action_vars}
            {$action_text|replace:"[media]":$action_media|choptext:50:"<br>"}
                </td>
          </tr>
          </table>
        </div>
      {/section}
    </div>
  {/if}


  
	</div>
<!-- End Tabs -->
</div>
    
    
  
 	 <div class="simpleTabs">
        <ul class="simpleTabsNavigation">
            <li><a href="#">{lang_print id=671}</a></li>
            <li><a href="#">{lang_print id=666}</a></li>
            <li><a href="#">{lang_print id=668}</a></li>
        </ul>
        <div class="simpleTabsContent">
        
            {* SHOW LAST LOGINS *}

            {if !empty($logins)}
            <table cellpadding='0' cellspacing='0' align='center'>
              {section name=login_loop loop=$logins max=14}
              {cycle name="startrow3" values="<tr>,,,,,,"}
              <td class='portal_member' valign="bottom" style="padding:4px;">
                {if !empty($logins[login_loop])}
                <a href='{$url->url_create("profile",$logins[login_loop]->user_info.user_username)}' TITLE='{$logins[login_loop]->user_displayname|truncate:15:"...":true}'><img src='{$logins[login_loop]->user_photo("./images/nophoto.gif", TRUE)}' class='photo' width='60' height='60' border='0' style='border: 5px solid #f2f2f2;'></a>
                {/if}
              </td>
              {cycle name="endrow3" values=",,,,,,</tr>"}
              {/section}
              </table>
            {else}
              {lang_print id=672}
            {/if}
            
            
<div class='sublink'><a href="search_advanced.php">View All NITD Network Members</a></div>
        
        </div>
        <div class="simpleTabsContent">
        
        {* SHOW LAST SIGNUPS *}

            {if !empty($logins)}
            <table cellpadding='0' cellspacing='0' align='center'>
              {section name=signups_loop loop=$signups max=14}
              {cycle name="startrow" values="<tr>,,,,,,"}
              <td class='portal_member' valign="bottom" style="padding:4px;">
                {if !empty($signups[signups_loop])}
                  <a href='{$url->url_create("profile",$signups[signups_loop]->user_info.user_username)}' TITLE='{$signups[signups_loop]->user_displayname|truncate:15:"...":true}'><img src='{$signups[signups_loop]->user_photo("./images/nophoto.gif", TRUE)}' class='photo' width='60' height='60' border='0' style='border: 5px solid #f2f2f2;'></a>
                {/if}
              </td>
              {cycle name="endrow" values=",,,,,,</tr>"}
              {/section}
              </table>
            {else}
              {lang_print id=667}
            {/if}

<div class='sublink'><a href="search_advanced.php">View All NITD Network Members</a></div> 
        
        </div>
        <div class="simpleTabsContent">
        
        
        {* SHOW MOST POPULAR USERS (MOST FRIENDS) *}
          {if $setting.setting_connection_allow != 0}

            
            {if !empty($logins)}
            <table cellpadding='0' cellspacing='0' align='center'>
              {section name=friends_loop loop=$friends max=14}
              {cycle name="startrow2" values="<tr>,,,,,,"}
              <td class='portal_member' valign="bottom" style="padding:4px;">
                {if !empty($friends[friends_loop])}
                <a href='{$url->url_create("profile",$friends[friends_loop].friend->user_info.user_username)}' TITLE='{$friends[friends_loop].friend->user_displayname|truncate:15:"...":true} - {lang_sprintf id=669 1=$friends[friends_loop].total_friends}'><img src='{$friends[friends_loop].friend->user_photo("./images/nophoto.gif", TRUE)}' class='photo' width='60' height='60' border='0' style='border: 5px solid #f2f2f2;'></a><br />
                {/if}
              </td>
              {cycle name="endrow3" values=",,,,,,</tr>"}
              {/section}
              </table>
            {else}
              {lang_print id=670}
            {/if}
            
          {/if}  
    
        
<div class='sublink'><a href="search_advanced.php">View All NITD Network Members</a></div>        
        </div>
    </div>
    
    
    
</div>

















{* BEGIN RIGHT COLUMN CONTENT *}
<div style='float: left; width: 300px; padding-top: 10px;'>


{* SHOW LOGIN FORM IF USER IS NOT LOGGED IN *}
  {if !$user->user_exists}

<!-- Login/Signup (start) -->
<div class="home-hightlight">
	<ul class="home-hightlight-navigation">
		<li><a href="login.php" class="current">{lang_print id=659}</a></li>
		
	</ul>
	<div class="home-highlight-content">
        
      <form action='login.php' method='post'>
      <table cellpadding='0' cellspacing='0' align='center'>
      <tr>
        <td>
          <span>{lang_print id=89}:</span><br />
          <input type='text' class='text login-highlight' name='email' size='25' maxlength='100' value='{$prev_email}' />
        </td>
      </tr>
      <tr>
        <td style='padding-top: 6px;'>
          <span>{lang_print id=29}:</span><br />
          <input type='password' class='text login-highlight' name='password' size='25' maxlength='100' />
        </td>
      </tr>
      {if !empty($setting.setting_login_code)}
      <tr>
        <td style='padding-top: 6px;'>
          <table cellpadding='0' cellspacing='0'>
            <tr>
              <td><input type='text' name='login_secure' class='text' size='6' maxlength='10' />&nbsp;</td>
              <td>
                <table cellpadding='0' cellspacing='0'>
                  <tr>
                    <td align='center'>
                      <img src='./images/secure.php' id='secure_image' border='0' height='20' width='67' class='signup_code' alt='' /><br />
                      <a href="javascript:void(0);" onClick="$('secure_image').src = './images/secure.php?' + (new Date()).getTime();">{lang_print id=975}</a>
                    </td>
                    <td>{capture assign=tip}{lang_print id=691}{/capture}<img src='./images/icons/tip.gif' border='0' class='Tips1' title='{$tip|escape:quotes}' alt='' /></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      {/if}
      <tr>
        <td style='padding-top: 10px;' align="right">
          
          <input type='checkbox' class='checkbox' name='persistent' value='1' id='rememberme' />
          <label for='rememberme'>{lang_print id=660}</label> &nbsp;
          <input type='submit' class='button go-highlight' value='{lang_print id=30}' />
          
        </td>
      </tr>
      </table>
      
      <noscript><input type='hidden' name='javascript_disabled' value='1' /></noscript>
      <input type='hidden' name='task' value='dologin' />
      <input type='hidden' name='ip' value='{$ip}' />
      </form> 
    
    
    </div>
</div>
<!-- Login/Signup (end) -->

  {* SHOW HELLO MESSAGE IF USER IS LOGGED IN *}
  {else}
  
<!-- Welcome Message (start) -->
    <div class='home-module'>
		<div style="text-align: center; padding: 10px;">    

    	{lang_sprintf id=510 1=$user->user_displayname_short}
    
    
      <div style='padding-bottom: 5px;'><a href='{$url->url_create('profile',$user->user_info.user_username)}'><img src='{$user->user_photo("./images/nophoto.gif")}' width='{$misc->photo_size($user->user_photo("./images/nophoto.gif"),'90','90','w')}' border='0' class='photo' alt="{lang_sprintf id=509 1=$user->user_info.user_username}"></a></div>
      <div>[ <a href='user_logout.php?token={$token}'>{lang_print id=26}</a> ]</div>
      
      
    	</div>
    
    </div>
<!-- Welcome Message (end) -->
  {/if}



<!-- Network Statistics (start) -->
{* SHOW NETWORK STATISTICS *}
<div class='home-module'>
	<div class='home-module-header'>
    {lang_print id=511}
    </div>
    <div class='home-module-content'>
        <ul>
        {foreach from=$site_statistics key=stat_name item=stat_array}
          <li> {lang_sprintf id=$stat_array.title 1=$stat_array.stat}</li>
        {/foreach}
        </ul>
    </div>
</div>
<!-- Network Statistics (end) -->


<!-- Online Users (start) -->
{* SHOW ONLINE USERS IF MORE THAN ZERO *}
<div class='home-module'>
{math assign='total_online_users' equation="x+y" x=$online_users[0]|@count y=$online_users[1]}
{if $total_online_users > 0}
    
	<div class='home-module-header'>
	{lang_print id=665} ({$total_online_users})
    </div>
    <div class='home-module-content'>
  
      {if $online_users[0]|@count == 0}
        {lang_sprintf id=977 1=$online_users[1]}
      {else}
        {capture assign='online_users_registered'}{section name=online_loop loop=$online_users[0]}{if $smarty.section.online_loop.rownum != 1}, {/if}<a href='{$url->url_create("profile", $online_users[0][online_loop]->user_info.user_username)}'>{$online_users[0][online_loop]->user_displayname}</a>{/section}{/capture}
        {lang_sprintf id=976 1=$online_users_registered 2=$online_users[1]}
      {/if}

  {/if}
    
    </div>
</div>
<!-- Online Users (end) -->


<!-- 'right-wrapper' end -->
</div>

</div>











<div style='clear: both;'></div>



{include file='footer.tpl'}