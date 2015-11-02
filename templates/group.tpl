{include file='header.tpl'}

{* $Id: group.tpl 247 2009-11-14 03:30:43Z phil $ *}

<div class='page_header'>{$group->group_info.group_title}</div>

<table width='100%' cellpadding='0' cellspacing='0'>
<tr>
<td class='profile_leftside' width='200'>
{* BEGIN LEFT COLUMN *}

  {* JAVASCRIPT FOR SWITCHING TABS *}
  {if $group->user_rank != -1}
    {literal}
    <script type='text/javascript'>
    <!--
      function subscribe_update(is_subscribed)
      {
        if(is_subscribed == '1')
        {
          $('is_subscribed').style.display = 'none';
          $('is_unsubscribed').style.display = 'block';
        }
        else
        {
          $('is_subscribed').style.display = 'block';
          $('is_unsubscribed').style.display = 'none';
        }
      }
    //-->
    </script>
    {/literal}
  {/if}

  {* SHOW PHOTO *}
  <table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
  <tr>
  <td class='profile_photo' width='182'><img class='photo' src='{$group->group_photo("./images/nophoto.gif")}' border='0' /></td>
  </tr>
  </table>

  <table class='profile_menu' cellpadding='0' cellspacing='0' width='100%'>

  {* SHOW EDIT GROUP BUTTON IF ALLOWED *}
  {if $group->user_rank>=2}
      <tr>
        <td class='profile_menu1'>
          <a href="user_group_edit.php?group_id={$group->group_info.group_id}"><img src='./images/icons/group_settings16.gif' border='0' class='button' />{lang_print id=2000159}</a>
        </td>
      </tr>
  {/if}
  
  {* SHOW JOIN GROUP BUTTON IF NOT ALREADY A MEMBER *}
  {if $group->user_rank == -1 && $user->user_exists == 1 && (int)$user->level_info.level_group_allow & 2}
    {if $group->groupmember_info.groupmember_id != 0 && $group->groupmember_info.groupmember_approved != 1}
      <tr>
        <td class='profile_menu1'>
          <div class='nolink'><img src='./images/icons/group_join16.gif' border='0' class='icon' />{lang_print id=2000223}</div>
        </td>
      </tr>
    {elseif $group->groupmember_info.groupmember_id != 0 && $group->groupmember_info.groupmember_approved == 1}
      <tr>
        <td class='profile_menu1'>
          <a href="javascript:TB_show('{lang_print id=2000203}', 'user_group_manage.php?group_id={$group->group_info.group_id}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/group_join16.gif' border='0' class='icon' />{lang_print id=2000203}</a>
        </td>
      </tr>
    {else}
      <tr>
        <td class='profile_menu1'>
          <a href="javascript:TB_show('{lang_print id=2000165}', 'user_group_manage.php?group_id={$group->group_info.group_id}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/group_join16.gif' border='0' class='icon' />{lang_print id=2000165}</a>
        </td>
      </tr>
    {/if}
  {/if}

  {* SHOW LEAVE GROUP BUTTON IF ALREADY A MEMBER *}
  {if $group->user_rank != -1}
    <tr>
      <td class='profile_menu1'>
        <a href="javascript:TB_show('{lang_print id=2000160}', 'user_group_manage.php?group_id={$group->group_info.group_id}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/group_leave16.gif' border='0' class='icon' />{lang_print id=2000160}</a></td></tr>
  {/if}

  {* SHOW SUBSCRIBE TO THIS GROUP ITEM *}
  {if $group->user_rank != -1}
    <tr><td class='profile_menu1'>
      <div id='is_subscribed'{if !$is_subscribed} style='display: none;'{/if}><a href="javascript:TB_show('{lang_print id=2000225}', 'user_group_subscribe.php?group_id={$group->group_info.group_id}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/group_unsubscribe16.gif' border='0' class='icon' />{lang_print id=2000225}</a></div>
      <div id='is_unsubscribed'{if $is_subscribed} style='display: none;'{/if}><a href="javascript:TB_show('{lang_print id=2000224}', 'user_group_subscribe.php?group_id={$group->group_info.group_id}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/group_subscribe16.gif' border='0' class='icon' />{lang_print id=2000224}</a></div>
    </td></tr>
  {/if}

  {* SHOW INVITE USERS MENU ITEM *}
  {if $allowed_to_invite}
    <tr><td class='profile_menu1'><a href='javascript:void(0)' onClick="TB_show('{lang_print id=2000174}', 'user_group_invite.php?group_id={$group->group_info.group_id}&TB_iframe=true&height=300&width=450', '', '../images/trans.gif');"><img src='./images/icons/group_invite16.gif' border='0' class='icon' />{lang_print id=2000174}</a></td></tr>
  {/if}

  {* SHOW REPORT THIS GROUP MENU ITEM *}
  <tr>
  <td class='profile_menu1'><a href="javascript:TB_show('{lang_print id=2000226}', 'user_report.php?return_url={$url->url_current()|escape:url}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/report16.gif' class='icon' border='0' />{lang_print id=2000226}</a></td>
  </tr>

  </table>


  {* DISPLAY IF GROUP IS PRIVATE TO VIEWING USER *}
  {if $is_group_private != 0}

    {* END LEFT COLUMN *}
    </td>
    <td class='profile_rightside'>
    {* BEGIN RIGHT COLUMN *}

      <img src='./images/icons/error48.gif' border='0' class='icon_big' />
      <div class='page_header'>{lang_print id=2000227}</div>
      {lang_print id=2000228}

  {* DISPLAY ONLY IF GROUP IS NOT PRIVATE TO VIEWING USER *}
  {else}

    {* SHOW OFFICERS *}
    <table cellpadding='0' cellspacing='0' width='100%' style='margin-top: 10px;'>
    <tr>
    <td class='header'>{lang_print id=2000229}</td>
    </tr>
    <tr>
    <td class='profile'>
      {section name=officer_loop loop=$officers}
        <div>
          <a href='{$url->url_create("profile", $officers[officer_loop].member->user_info.user_username)}'>{$officers[officer_loop].member->user_displayname}</a>{if $officers[officer_loop].groupmember_rank == 2} {lang_print id=2000230}{/if}
            {if $officers[officer_loop].groupmember_title != "" && $group->groupowner_level_info.level_group_titles == 1}
            <div class='group_officer_title'>{$officers[officer_loop].groupmember_title}</div>
          {/if}
          {if !$smarty.section.officer_loop.last}<div style='height: 4px;'></div>{/if}
        </div>
      {/section}
    </td>
    </tr>
    </table>
    {* END OFFICERS *}


  {* END LEFT COLUMN *}
  </td>
  <td class='profile_rightside'>
  {* BEGIN RIGHT COLUMN *}

    {* JAVASCRIPT FOR SWITCHING TABS *}
    {literal}
    <script type='text/javascript'>
    <!--
      var visible_tab = '{/literal}{$v}{literal}';
      function loadGroupTab(tabId)
      {
        if(tabId == visible_tab)
        {
          return false;
        }
        if( $('group_'+tabId) )
        {
          $('group_tabs_'+tabId).className='group_tab2';
          $('group_'+tabId).style.display = "block";
          if($('group_tabs_'+visible_tab))
          {
            $('group_tabs_'+visible_tab).className='group_tab';
            $('group_'+visible_tab).style.display = "none";
          }
          visible_tab = tabId;
        }
      }
    //-->
    </script>
    {/literal}
    
    {* SHOW GROUP TAB BUTTONS *}
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td valign='bottom'><table cellpadding='0' cellspacing='0'><tr><td class='group_tab{if $v == 'group'}2{/if}' id='group_tabs_group' onMouseUp="this.blur()" nowrap='nowrap'><a href='javascript:void(0);' onMouseDown="loadGroupTab('group')" onMouseUp="this.blur()">{lang_print id=2000231}</a></td></tr></table></td>
    {if $total_members_all != 0}<td valign='bottom'><table cellpadding='0' cellspacing='0'><td class='group_tab{if $v == 'members'}2{/if}' id='group_tabs_members' onMouseUp="this.blur()"><a href='javascript:void(0);' onMouseDown="loadGroupTab('members');" onMouseUp="this.blur()">{lang_print id=2000118}</a></td></tr></table></td>{/if}
    {if $allowed_to_upload != 0 || $total_files != 0}<td valign='bottom'><table cellpadding='0' cellspacing='0'><td class='group_tab{if $v == 'photos'}2{/if}' id='group_tabs_photos' onMouseUp="this.blur()"><a href='javascript:void(0);' onMouseDown="loadGroupTab('photos');" onMouseUp="this.blur()">{lang_print id=2000232}</a></td></tr></table></td>{/if}
    {if $allowed_to_discuss != 0 || $total_topics != 0}<td valign='bottom'><table cellpadding='0' cellspacing='0'><td class='group_tab{if $v == 'discussions'}2{/if}' id='group_tabs_discussions' onMouseUp="this.blur()"><a href='javascript:void(0);' onMouseDown="loadGroupTab('discussions');" onMouseUp="this.blur()">{lang_print id=2000233}</a></td></tr></table></td>{/if}
    {if $allowed_to_comment == 1 || $total_comments != 0}<td valign='bottom'><table cellpadding='0' cellspacing='0'><td class='group_tab{if $v == 'comments'}2{/if}' id='group_tabs_comments' onMouseUp="this.blur()"><a href='javascript:void(0);' onMouseDown="loadGroupTab('comments');" onMouseUp="this.blur()">{lang_print id=854}</a></td></tr></table></td>{/if}
    <td width='100%' class='group_tab_end'>&nbsp;</td>
    </tr>
    </table>
    
    <div class='group_content'>
    
    
    {* GROUP TAB *}
    <div id='group_group'{if $v != 'group'} style='display: none;'{/if}>
    
      {* SHOW GROUP INFORMATION *}
      <div style='margin-bottom: 10px;'>
        <div class='group_headline'>{lang_print id=2000254}</div>
        <table cellpadding='0' cellspacing='0'>
          <tr><td width='100' valign='top' nowrap='nowrap'>{lang_print id=2000094}</td><td>{$group->group_info.group_title}</td></tr>
          {if $group->group_info.group_desc != ""}<tr><td valign='top' nowrap='nowrap'>{lang_print id=2000255}</td><td>{$group->group_info.group_desc}</td></tr>{/if}
          <tr>
            <td valign='top' nowrap='nowrap'>{lang_print id=2000256}</td>
            <td>{if $groupcat_info.subcat_dependency == 0}<a href='browse_groups.php?groupcat_id={$groupcat_info.subcat_id}'>{lang_print id=$groupcat_info.subcat_title}</a>{else}<a href='browse_groups.php?groupcat_id={$groupcat_info.cat_id}'>{lang_print id=$groupcat_info.cat_title}</a> - <a href='browse_groups.php?groupcat_id={$groupcat_info.subcat_id}'>{lang_print id=$groupcat_info.subcat_title}</a>{/if}</td>
          </tr>
          {section name=cat_loop loop=$cats}
            {section name=field_loop loop=$cats[cat_loop].fields}
              <tr>
                <td valign='top' nowrap='nowrap'>{lang_print id=$cats[cat_loop].fields[field_loop].field_title}:</td>
                <td><div class='profile_field_value'>{$cats[cat_loop].fields[field_loop].field_value_formatted}</div></td>
              </tr>
            {/section}
          {/section}
        </table>
      </div>

      {* SHOW RECENT ACTIVITY *}
      {if $actions|@count > 0}
        <div style='margin-bottom: 10px;'>
          <div class='group_headline'>{lang_print id=2000253}</div>
          {section name=actions_loop loop=$actions max=20}
            <div id='action_{$actions[actions_loop].action_id}' class='profile_action'>
              <table cellpadding='0' cellspacing='0'>
                <tr>
                  <td valign='top'><img src='./images/icons/{$actions[actions_loop].action_icon}' border='0' class='icon'></td>
                  <td valign='top' width='100%'>
                    <div class='profile_action_date'>
                      {assign var='action_date' value=$datetime->time_since($actions[actions_loop].action_date)}
                      {lang_sprintf id=$action_date[0] 1=$action_date[1]}
                    </div>
                    {assign var='action_media' value=''}
                    {if $actions[actions_loop].action_media !== FALSE}{capture assign='action_media'}{section name=action_media_loop loop=$actions[actions_loop].action_media}<a href='{$actions[actions_loop].action_media[action_media_loop].actionmedia_link}'><img src='{$actions[actions_loop].action_media[action_media_loop].actionmedia_path}' border='0' width='{$actions[actions_loop].action_media[action_media_loop].actionmedia_width}' class='recentaction_media'></a>{/section}{/capture}{/if}
                    {lang_sprintf assign='action_text' id=$actions[actions_loop].action_text args=$actions[actions_loop].action_vars}
                    {$action_text|replace:"[media]":$action_media|choptext:50:"<br>"}
                  </td>
                </tr>
              </table>
            </div>
          {/section}
        </div>
      {/if}
      {* END RECENT ACTIVITY *}

    </div>
    {* END GROUP TAB *}






    {* MEMBERS TAB *}
    {if $total_members_all != 0}
      <div id='group_members'{if $v != 'members'} style='display: none;'{/if}>

        {* JAVASCRIPT FOR CHANGING FRIEND MENU OPTION *}
        {literal}
        <script type="text/javascript">
        <!-- 
	  function friend_update(status, id) {
	    if(status == 'pending') {
	      if($('addfriend_'+id))
	        $('addfriend_'+id).style.display = 'none';
	    } else if(status == 'remove') {
	      if($('addfriend_'+id))
	        $('addfriend_'+id).style.display = 'none';
	    }
	  }
        //-->
        </script>
        {/literal}

	<table cellpadding='0' cellspacing='0' width='100%'>
	<tr>
	<td valign='top'>
          <div class='group_headline'>{lang_print id=2000220} ({$total_members})</div>
	</td>
	<td valign='top' align='right'>
	  {if $search == ""}
	    <div id='group_members_searchbox_link'>
	      <a href='javascript:void(0);' onClick="$('group_members_searchbox_link').style.display='none';$('group_members_searchbox').style.display='block';$('group_members_searchbox_input').focus();">{lang_print id=2000221}</a>
	    </div>
	  {/if}
	  <div id='group_members_searchbox' style='text-align: right;{if $search == ""} display: none;{/if}'>
	    <form action='group.php' method='post'>
	    <input type='text' maxlength='100' size='30' class='text' name='search' value='{$search}' id='group_members_searchbox_input' />
	    <input type='submit' class='button' value='{lang_print id=646}' />
	    <input type='hidden' name='p' value='{$p_members}' />
	    <input type='hidden' name='v' value='members' />
	    <input type='hidden' name='group_id' value='{$group->group_info.group_id}' />
	    </form>
	  </div>
	</td>
	</tr>
	</table>

	{* DISPLAY NO RESULTS MESSAGE *}
	{if $search != "" && $total_members == 0}
	  <br>
	  <table cellpadding='0' cellspacing='0'>
	  <tr><td class='result'>
	    <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=2000222}
	  </td></tr>
	  </table>
	{/if}

        {* DISPLAY PAGINATION MENU IF APPLICABLE *}
	{if $maxpage_members > 1}
	  <div style='text-align: center;'>
	    {if $p_members != 1}<a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}&v=members&search={$search}&p={math equation="p-1" p=$p_members}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
	    {if $p_start_members == $p_end_members}
	      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start_members 2=$total_members} &nbsp;|&nbsp; 
	    {else}
	      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start_members 2=$p_end_members 3=$total_members} &nbsp;|&nbsp; 
	    {/if}
	    {if $p_members != $maxpage_members}<a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}&v=members&search={$search}&p={math equation="p+1" p=$p_members}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
	  </div>
	{/if}

        {* LOOP THROUGH MEMBERS *}
        {section name=member_loop loop=$members}
          <div class='group_members_result' style='overflow: hidden;'>
            <div class='group_members_photo'>
	      <a href='{$url->url_create("profile",$members[member_loop].member->user_info.user_username)}'><img src='{$members[member_loop].member->user_photo('./images/nophoto.gif')}' width='{$misc->photo_size($members[member_loop].member->user_photo('./images/nophoto.gif'),'90','90','w')}' border='0' alt="{lang_sprintf id=509 1=$members[member_loop].member->user_displayname_short}" class='photo'></a>
            </div>
            <div class='profile_friend_info'>
              <div class='profile_friend_name'><a href='{$url->url_create("profile",$members[member_loop].member->user_info.user_username)}'>{$members[member_loop].member->user_displayname}</a></div>
	      <div class='profile_friend_details'>
	        {if $members[member_loop].member->user_info.user_dateupdated != 0}<div>{lang_print id=849} {assign var='last_updated' value=$datetime->time_since($members[member_loop].member->user_info.user_dateupdated)}{lang_sprintf id=$last_updated[0] 1=$last_updated[1]}</div>{/if}
		{capture assign='member_rank'}{if $members[member_loop].groupmember_rank == 2}{lang_print id=2000179}{elseif $members[member_loop].groupmember_rank == 1 && $group->groupowner_level_info.level_group_officers == 1}{lang_print id=2000180}{/if}{/capture}
	        <div>{if $members[member_loop].groupmember_title != "" && $group->groupowner_level_info.level_group_titles == 1}{lang_sprintf id=2000182 1=$member_rank 2=$members[member_loop].groupmember_title}{elseif $member_rank != ""}{lang_sprintf id=2000178 1=$member_rank}{/if}</div>
	      </div>
            </div>
	    <div class='profile_friend_options'>
              {if !$members[member_loop].member->is_viewers_friend && !$members[member_loop].member->is_viewer_blocklisted && $members[member_loop].member->user_info.user_id != $user->user_info.user_id && $user->user_exists != 0}<div id='addfriend_{$members[member_loop].member->user_info.user_id}'><a href="javascript:TB_show('{lang_print id=876}', 'user_friends_manage.php?user={$members[member_loop].member->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');">{lang_print id=838}</a></div>{/if}
              {if !$members[member_loop].member->is_viewer_blocklisted && ($user->level_info.level_message_allow == 2 || ($user->level_info.level_message_allow == 1 && $members[member_loop].member->is_viewers_friend)) && $members[member_loop].member->user_info.user_id != $user->user_info.user_id}<a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$members[member_loop].member->user_displayname}&to_id={$members[member_loop].member->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');">{lang_print id=839}</a>{/if}
            </div>
	    <div style='clear: both;'></div>
          </div>
	  {if !$smarty.section.member_loop.last}<div style='clear: both; height: 8px;'></div>{/if}
        {/section}


        {* DISPLAY PAGINATION MENU IF APPLICABLE *}
	{if $maxpage_members > 1}
	  <div style='text-align: center;'>
	    {if $p_members != 1}<a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}&v=members&search={$search}&p={math equation="p-1" p=$p_members}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
	    {if $p_start_members == $p_end_members}
	      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start_members 2=$total_members} &nbsp;|&nbsp; 
	    {else}
	      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start_members 2=$p_end_members 3=$total_members} &nbsp;|&nbsp; 
	    {/if}
	    {if $p_members != $maxpage_members}<a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}&v=members&search={$search}&p={math equation="p+1" p=$p_members}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
	  </div>
	{/if}

      </div>
    {/if}
    {* END MEMBERS TAB *}




    {* BEGIN PHOTOS TAB *}
    {if $allowed_to_upload != 0 || $total_files != 0}

      {* PHOTOS TAB *}
      <div id='group_photos'{if $v != 'photos'} style='display: none;'{/if}>

        <div>
         <div class='group_headline' style='float: left;'>{lang_print id=2000232} (<span id='group_{$group->group_info.group_id}_totalfiles'>{$total_files}</span>)</div>
          {if $allowed_to_upload}
            <div style='float: right; padding-left: 10px;'>
              <a href="javascript:TB_show('{lang_print id=2000251}', 'user_group_upload.php?group_id={$group->group_info.group_id}&TB_iframe=true&height=300&width=500', '', './images/trans.gif');"><img src='./images/icons/group_addimages16.gif' border='0' class='button' style='float: left;'>{lang_print id=2000251}</a>
              <div style='clear: both; height: 0px;'></div>
            </div>
	  {/if}
          <div style='clear: both; height: 0px;'></div>
        </div>

        {* FILES *}
        <div id="group_{$group->group_info.group_id}_nofiles" style='display: none;'><img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=2000252}</div>
        <div id="group_{$group->group_info.group_id}_files" style='margin-left: auto; margin-right: auto;'></div>
      
        {lang_javascript ids=182,183,184,185}


        <script type="text/javascript" src="./include/js/class_group_files.js"></script>      

        <script type="text/javascript">
        
          SocialEngine.GroupFiles = new SocialEngineAPI.GroupFiles({ldelim}
	    'paginate' : true,
	    'cpp' : 18,

	    'group_id' : {$group->group_info.group_id},
	    'group_dir' : '{$group->group_dir($group->group_info.group_id)}'
          {rdelim});
        
          SocialEngine.RegisterModule(SocialEngine.GroupFiles);

          function getFiles(direction)
          {ldelim}
            SocialEngine.GroupFiles.getFiles(direction);
          {rdelim}

        </script>

      </div>

    {/if}
    {* END PHOTOS TAB *}







    {* DISCUSSION TAB *}
    {if $allowed_to_discuss != 0 || $total_topics != 0}

      <div id='group_discussions'{if $v != 'discussions'} style='display: none;'{/if}>

        <div>
          <div class='group_headline' style='float: left;'>{lang_print id=2000257} ({$total_topics})</div>
          {if $allowed_to_discuss}
            <div style='float: right;'>
              <a href='group_discussion_post.php?group_id={$group->group_info.group_id}'><img src='./images/icons/group_discussion_post16.gif' border='0' class='button' style='float: left;'>{lang_print id=2000258}</a>
              <div style='clear: both; height: 0px;'></div>
            </div>
          {/if}
          <div style='clear: both; height: 0px;'></div>
        </div>

        {* DISPLAY NO RESULTS MESSAGE *}
        {if $total_topics == 0}
          <br>
          <table cellpadding='0' cellspacing='0'>
          <tr><td class='result'>
            <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=2000259}
          </td></tr>
          </table>
        {/if}

        {* DISPLAY PAGINATION MENU IF APPLICABLE *}
        {if $maxpage_topics > 1}
          <div style='text-align: center;'>
            {if $p_topics != 1}<a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}&v=discussions&p={math equation="p-1" p=$p_topics}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
            {if $p_start_topics == $p_end_topics}
              &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start_topics 2=$total_topics} &nbsp;|&nbsp; 
            {else}
              &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start_topics 2=$p_end_topics 3=$total_topics} &nbsp;|&nbsp; 
            {/if}
            {if $p_topics != $maxpage_topics}<a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}&v=discussions&p={math equation="p+1" p=$p_topics}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
          </div>
        {/if}


        <table cellpadding='0' cellspacing='0' width='100%' class='group_discussion_table' style='margin-top: 5px; margin-bottom: 5px;'>
        {section name=topic_loop loop=$topics}
          <tr>
          <td class='group_discussion_topic{cycle values="1,1,1,2,2,2"}' nowrap='nowrap' style='text-align: center;' width='40'>
            {lang_sprintf id=2000260 1=$topics[topic_loop].total_posts-1}
          </td>
          <td class='group_discussion_topic{cycle values="1,1,1,2,2,2"}'>
            <table cellpadding='0' cellspacing='0'>
            <tr>
            <td style='vertical-align: top;'>
              {if !$topics[topic_loop].grouptopic_closed}
                <div><img src='./images/icons/group_discussion16.gif' border='0' class='icon'></div>
              {else}
                <div><img src='./images/icons/group_discussion_closed16.gif' border='0' class='icon'></div>
              {/if}
              {if $topics[topic_loop].grouptopic_sticky}
                <div><img src='./images/icons/group_discussion_stickied16.gif' border='0' class='icon' /></div>
              {/if}
            </td>
            <td style='vertical-align: top;'>
              <div style='font-weight: bold;'>
                <a href='{$url->url_create("group_discussion", $smarty.const.NULL, $group->group_info.group_id, $topics[topic_loop].grouptopic_id)}'>
                  {$topics[topic_loop].grouptopic_subject}
                </a>
              </div>
              <div style='color: #777777; font-size: 9px;'>
                {assign var='datecreated_vars' value=$datetime->time_since($topics[topic_loop].grouptopic_date)}
                {capture assign='datecreated'}{lang_sprintf id=$datecreated_vars[0] 1=$datecreated_vars[1]}{/capture}
                {if $topics[topic_loop].creator->user_exists}
                  {lang_sprintf id=2000261 1=$datecreated 2=$url->url_create('profile', $topics[topic_loop].creator->user_info.user_username) 3=$topics[topic_loop].creator->user_displayname}
                {else}
                  {if $topics[topic_loop].grouptopic_creatoruser_id != 0}
                    {capture assign='creator'}{lang_print id=1071}{/capture}
                  {else}
                    {capture assign='creator'}{lang_print id=835}{/capture}
                  {/if}
                  {lang_sprintf id=2000261 1=$datecreated 2=$creator}
                {/if}
                - {lang_sprintf id=2000262 1=$topics[topic_loop].grouptopic_views}
                {if $group->user_rank == 2 || $group->user_rank == 1}
                 - [ <a href='javascript:void(0);' onClick="confirmDelete('{$topics[topic_loop].grouptopic_id}')">{lang_print id=155}</a> ]
                {/if}
              </div>
            </td>
            </tr>
            </table>
          </td>
          <td class='group_discussion_topic{cycle values="1,1,1,2,2,2"}_end' nowrap='nowrap'>
            <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
            <td width='1'>
              <img src='{if $topics[topic_loop].lastposter->user_exists}{$topics[topic_loop].lastposter->user_photo("./images/nophoto.gif", TRUE)}{else}./images/nophoto.gif{/if}' class='photo' width='35' height='35' />
            </td>
            <td style='padding-left: 8px;'>
              <div>
                {if $topics[topic_loop].lastposter->user_exists}
                  {lang_sprintf id=2000263 1=$url->url_create("group_discussion_post", $smarty.const.NULL, $group->group_info.group_id, $topics[topic_loop].grouptopic_id, $topics[topic_loop].grouppost_id) 2=$url->url_create('profile', $topics[topic_loop].lastposter->user_info.user_username) 3=$topics[topic_loop].lastposter->user_displayname}
                {else}
                  {if $topics[topic_loop].grouppost_authoruser_id != 0}
                    {capture assign='lastposter'}{lang_print id=1071}{/capture}
                  {else}
                    {capture assign='lastposter'}{lang_print id=835}{/capture}
                  {/if}
                  {lang_sprintf id=2000265 1=$url->url_create("group_discussion_post", $smarty.const.NULL, $group->group_info.group_id, $topics[topic_loop].grouptopic_id, $topics[topic_loop].grouppost_id) 2=$lastposter}
                {/if}
              </div>
              <div>
                {assign var="grouppost_date" value=$datetime->time_since($topics[topic_loop].grouppost_date)}
                {lang_sprintf id=$grouppost_date[0] 1=$grouppost_date[1]}
              </div>
            </td>
            </tr>
            </table>
          </td>
          </tr>
        {/section}
        </table>


        {* DISPLAY PAGINATION MENU IF APPLICABLE *}
        {if $maxpage_topics > 1}
          <div style='text-align: center;'>
            {if $p_topics != 1}<a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}&v=discussions&p={math equation="p-1" p=$p_topics}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
            {if $p_start_topics == $p_end_topics}
              &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start_topics 2=$total_topics} &nbsp;|&nbsp; 
            {else}
              &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start_topics 2=$p_end_topics 3=$total_topics} &nbsp;|&nbsp; 
            {/if}
            {if $p_topics != $maxpage_topics}<a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}&v=discussions&p={math equation="p+1" p=$p_topics}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
          </div>
        {/if}

      </div>


      {if $group->user_rank == 2 || $group->user_rank == 1}
        {* JAVASCRIPT FOR CONFIRMING DELETION *}
        {literal}
        <script type="text/javascript">
        <!-- 
        var topic_id = 0;
        function confirmDelete(id) {
          topic_id = id;
          TB_show('{/literal}{lang_print id=2000266}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
        }
  
        function deleteTopic() {
          window.location = '{/literal}{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}{literal}&v=discussions&p={/literal}{$p_topics}{literal}&task=topic_delete&grouptopic_id='+topic_id;
        }
        //-->
        </script>
        {/literal}


        {* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
        <div style='display: none;' id='confirmdelete'>
          <div style='margin-top: 10px;'>
            {lang_print id=2000267}
          </div>
          <br>
          <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteTopic();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
        </div>
      {/if}

    {/if}
    {* END DISCUSSION *}






    {* BEGIN COMMENTS TAB *}
    {if $allowed_to_comment != 0 || $total_comments != 0}


      {* SHOW COMMENT TAB *}
      <div id='group_comments'{if $v != 'comments'} style='display: none;'{/if}>

      {* COMMENTS *}
      <div id="group_{$group->group_info.group_id}_postcomment"></div>
      <div id="group_{$group->group_info.group_id}_comments" style='margin-left: auto; margin-right: auto;'></div>
      
      {lang_javascript ids=39,155,175,182,183,184,185,187,784,787,829,830,831,832,833,834,835,854,856,891,1025,1026,1032,1034,1071}
      
      {literal}
      <style type='text/css'>
	div.comment_headline {
	  font-size: 13px; 
	  margin-bottom: 7px;
	  font-weight: bold;
	  padding: 0px;
	  border: none;
	  background: none;
	  color: #555555;
	}
      </style>
      {/literal}

      <script type="text/javascript">
        
        SocialEngine.GroupComments = new SocialEngineAPI.Comments({ldelim}
            'canComment' : {if $allowed_to_comment}true{else}false{/if},
            'commentHTML' : '{$setting.setting_comment_html|replace:",":", "}',
            'commentCode' : {if $setting.setting_comment_code}true{else}false{/if},
            
            'type' : 'group',
            'typeIdentifier' : 'group_id',
            'typeID' : {$group->group_info.group_id},
            
            'typeTab' : 'groups',
            'typeCol' : 'group',
            
            'initialTotal' : {$total_comments|default:0},
            
            'paginate' : true,
            'cpp' : 10,
            
            'object_owner' : 'group',
            'object_owner_id' : {$group->group_info.group_id}
        {rdelim});
        
        SocialEngine.RegisterModule(SocialEngine.GroupComments);
       
        // Backwards
        function addComment(is_error, comment_body, comment_date)
        {ldelim}
          SocialEngine.GroupComments.addComment(is_error, comment_body, comment_date);
        {rdelim}
        
        function getComments(direction)
        {ldelim}
          SocialEngine.GroupComments.getComments(direction);
        {rdelim}

      </script>


      </div>

    {/if}
    {* END COMMENTS *}



  {/if}
  {* END PRIVACY IF STATEMENT *}
    </div>


  </div>


{* END RIGHT COLUMN *}
</td>
</tr>
</table>

{include file='footer.tpl'}