{include file='header.tpl'}
{* $Id: user_userconnection_contacts_3degree.tpl 8 2009-09-16 06:02:53Z SocialEngineAddOns $ *}
{* CODE FOR 3RD DEGREE CONTACTS *}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_friends.php'>{lang_print id=894}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_userconnection_contacts_2degree.php'>{lang_print id=650002050}</a></td>
<td class='tab0'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_userconnection_contacts_3degree.php'>{lang_print id=650002051}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_userconnection_setting.php'>{lang_print id=650002063}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<img src='./images/icons/userconnections-friends.png' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=650002065}<sup><span style="font-size:12px;">{lang_print id=650002066}</span></sup> {lang_print id=650002067}</div>
<div>{lang_print id=650002036}</div>

<br />
<br />
{if !empty($third_degree_contacts_users_information)}
{section name=third_degree_friend_loop loop=$third_degree_contacts_users_information}
  {* LOOP THROUGH FRIENDS *}
    <div class='friends_result' style='width: 398px; height: 100px; float: left; margin-left: 10px;'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
         <td class='friends_result0' style='width: 90px; text-align: center;'><a href='{$url->url_create('profile',$third_degree_contacts_users_information[third_degree_friend_loop]->user_info.user_username)}'><img src='{$third_degree_contacts_users_information[third_degree_friend_loop]->user_photo('./images/nophoto.gif')}' class='photo' width='{$misc->photo_size($third_degree_contacts_users_information[third_degree_friend_loop]->user_photo('./images/nophoto.gif'),'90','90','w')}' border='0' alt="{lang_sprintf id=509 1=$third_degree_contacts_users_information[third_degree_friend_loop]->user_displayname_short}"></a>
         </td>
         <td class='friends_result1' width='100%' valign='top'>
          <div class='friends_name'><a href='{$url->url_create('profile',$third_degree_contacts_users_information[third_degree_friend_loop]->user_info.user_username)}'></a><a href='{$url->url_create('profile',$third_degree_contacts_users_information[third_degree_friend_loop]->user_info.user_username)}'>{$third_degree_contacts_users_information[third_degree_friend_loop]->user_displayname|truncate:30:"...":true|chunk_split:12:"<wbr>&shy;"}</a>
          </div>
	  		  <div class='friends_stats'>
        		{if $third_degree_contacts_users_information[third_degree_friend_loop]->user_info.user_dateupdated != 0}<div>{lang_print id=849} {assign var='last_updated' value=$datetime->time_since($third_degree_contacts_users_information[third_degree_friend_loop]->user_info.user_dateupdated)}{lang_sprintf id=$last_updated[0] 1=$last_updated[1]}</div>{/if}
            {if $third_degree_contacts_users_information[third_degree_friend_loop]->user_info.user_lastlogindate != 0}<div>{lang_print id=906} {assign var='last_login' value=$datetime->time_since($third_degree_contacts_users_information[third_degree_friend_loop]->user_info.user_lastlogindate)}{lang_sprintf id=$last_login[0] 1=$last_login[1]}</div>{/if}
          </div>
         </td>
         <td class='friends_result2' valign='top' nowrap='nowrap'>
          <span id='addfriend_{$third_degree_contacts_users_information[third_degree_friend_loop]->user_info.user_id}'{if $is_friend == TRUE || $is_friend_pending != 0} align="left" style='display: none;'{/if}><a href="javascript:TB_show('{lang_print id=876}', 'user_friends_manage.php?user={$third_degree_contacts_users_information[third_degree_friend_loop]->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');" title="Add to Friend">{lang_print id=838}</a>
         	</span>
          
          <div><a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$third_degree_contacts_users_information[third_degree_friend_loop]->user_displayname}&to_id={$third_degree_contacts_users_information[third_degree_friend_loop]->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');">{lang_print id=839}</a></div>
          <div><a href='profile.php?user={$third_degree_contacts_users_information[third_degree_friend_loop]->user_info.user_username}&v=friends'>{assign var="user_displayname_short" value=$third_degree_contacts_users_information[third_degree_friend_loop]->user_displayname_short|truncate:15:"...":true}{lang_sprintf id=836 1=$user_displayname_short}</a></div>
         </td>
        </tr>
      </table>
    </div>
  {cycle values=",<div style='clear: both;'></div>"} 
{/section}
{else}
	
	
	    <table cellpadding='0' cellspacing='0' align='center'>
    <tr><td class='result'>
      <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id = 650002054}
    </td></tr>
    </table>
{/if}
{include file='footer.tpl'}