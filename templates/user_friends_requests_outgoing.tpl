{include file='header.tpl'}

{* $Id: user_friends_requests_outgoing.tpl 8 2009-01-11 06:02:53Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_friends.php'>{lang_print id=894}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_friends_requests.php'>{lang_print id=895}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_friends_requests_outgoing.php'>{lang_print id=896}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>


<img src='./images/icons/friends48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=896}</div>
<div>{lang_print id=915}</div>
<br />
<br />

{* DISPLAY MESSAGE IF NO FRIEND REQUESTS *}
{if $total_friends == 0}

  <table cellpadding='0' cellspacing='0' align='center'>
  <tr><td class='result'><img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=916}</td></tr>
  </table>

{* DISPLAY FRIEND REQUESTS *}
{else}

  {* JAVASCRIPT FOR CHANGING FRIEND MENU OPTION *}
  {literal}
  <script type="text/javascript">
  <!-- 
  function friend_update(status) {
    {/literal}
    window.location = 'user_friends_requests_outgoing.php?p={$p}';
    {literal}
  }
  //-->
  </script>
  {/literal}

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <br>
    <div class='center'>
    {if $p != 1}<a href='user_friends_requests_outgoing.php?p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
    {if $p_start == $p_end}
      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_friends} &nbsp;|&nbsp; 
    {else}
      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_friends} &nbsp;|&nbsp; 
    {/if}
    {if $p != $maxpage}<a href='user_friends_requests_outgoing.php?p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </div>
  {/if}

  {section name=friend_loop loop=$friends}
  {* LOOP THROUGH FRIENDS *}
    <div class='friends_result'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td class='friends_result0'><a href='{$url->url_create('profile', $friends[friend_loop]->user_info.user_username)}'><img src='{$friends[friend_loop]->user_photo('./images/nophoto.gif')}' class='photo' width='{$misc->photo_size($friends[friend_loop]->user_photo('./images/nophoto.gif'),'90','90','w')}' border='0' alt="{lang_sprintf id=509 1=$friends[friend_loop]->user_displayname_short}"></a></td>
    <td class='friends_result1' width='100%'>
      <div><font class='big'><a href='{$url->url_create('profile', $friends[friend_loop]->user_info.user_username)}'><img src='./images/icons/user16.gif' border='0' class='icon'>{$friends[friend_loop]->user_displayname}</a></div></font><br>
      <table cellpadding='0' cellspacing='0'>
      {if $friends[friend_loop]->user_info.user_dateupdated != 0}<tr><td>{lang_print id=849} &nbsp;</td><td>{assign var='last_updated' value=$datetime->time_since($friends[friend_loop]->user_info.user_dateupdated)}{lang_sprintf id=$last_updated[0] 1=$last_updated[1]}</td></tr>{/if}
      {if $friends[friend_loop]->user_info.user_lastlogindate != 0}<tr><td>{lang_print id=906} &nbsp;</td><td>{assign var='last_login' value=$datetime->time_since($friends[friend_loop]->user_info.user_lastlogindate)}{lang_sprintf id=$last_login[0] 1=$last_login[1]}</td></tr>{/if}
      {if $friends[friend_loop]->friend_type != ""}<tr><td>{lang_print id=882} &nbsp;</td><td>{$friends[friend_loop]->friend_type}</td></tr>{/if}
      {if $friends[friend_loop]->friend_explain != ""}<tr><td>{lang_print id=907} &nbsp;</td><td>{$friends[friend_loop]->friend_explain}</td></tr>{/if}
      </table>
    </td>
    <td class='friends_result2' NOWRAP>
      <a href="javascript:TB_show('{lang_print id=887}', 'user_friends_manage.php?task=cancel&user={$friends[friend_loop]->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');">{lang_print id=917}</a><br>
      {if $user->level_info.level_message_allow != 0}<a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$friends[friend_loop]->user_displayname}&to_id={$friends[friend_loop]->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');">{lang_print id=839}</a><br>{/if}
    </td>
    </tr>
    </table>
    </div>
  {/section}

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <br>
    <div class='center'>
    {if $p != 1}<a href='user_friends_requests_outgoing.php?p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
    {if $p_start == $p_end}
      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_friends} &nbsp;|&nbsp; 
    {else}
      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_friends} &nbsp;|&nbsp; 
    {/if}
    {if $p != $maxpage}<a href='user_friends_requests_outgoing.php?p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </div>
  {/if}
  
{/if}  

{include file='footer.tpl'}