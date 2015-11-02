{include file='header.tpl'}

{* $Id: profile.tpl 255 2009-11-18 02:21:01Z steve $ *}

<div class='page_header'>{lang_sprintf id=786 1=$owner->user_displayname}</div>

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td class='profile_leftside' width='200'>
{* BEGIN LEFT COLUMN *}

  {* SHOW USER PHOTO *}
  <table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
  <tr>
  <td class='profile_photo'><img class='photo' src='{$owner->user_photo("./images/nophoto.gif")}' border='0'></td>
  </tr>
  </table>

  <table class='profile_menu' cellpadding='0' cellspacing='0' width='100%'>

  {* SHOW PHOTOS OF THIS PERSON *}
  {if $total_photo_tags != 0}
    <tr><td class='profile_menu1' nowrap='nowrap'><a href='profile_photos.php?user={$owner->user_info.user_username}'><img src='./images/icons/photos16.gif' class='icon' border='0'>{lang_sprintf id=1204 1=$owner->user_displayname_short 2=$total_photo_tags}</a></td></tr>
    {assign var='showmenu' value='1'}
  {/if}


  {* SHOW BUTTONS IF LOGGED IN AND VIEWING SOMEONE ELSE *}
  {if $owner->user_info.user_id != $user->user_info.user_id}
 
    {* SHOW ADD OR REMOVE FRIEND MENU ITEM *}
    {if $friendship_allowed != 0 && $user->user_exists != 0}
      <tr><td class='profile_menu1' nowrap='nowrap'>
        {* JAVASCRIPT FOR CHANGING FRIEND MENU OPTION *}
        {literal}
        <script type="text/javascript">
        <!-- 
        function friend_update(status, id) {
          if(status == 'pending') {
            if($('addfriend_'+id))
              $('addfriend_'+id).style.display = 'none';
            if($('confirmfriend_'+id))
              $('confirmfriend_'+id).style.display = 'none';
            if($('pendingfriend_'+id))
              $('pendingfriend_'+id).style.display = 'block';
            if($('removefriend_'+id))
              $('removefriend_'+id).style.display = 'none';
          } else if(status == 'remove') {
            if($('addfriend_'+id))
              $('addfriend_'+id).style.display = 'none';
            if($('confirmfriend_'+id))
              $('confirmfriend_'+id).style.display = 'none';
            if($('pendingfriend_'+id))
              $('pendingfriend_'+id).style.display = 'none';
            if($('removefriend_'+id))
              $('removefriend_'+id).style.display = 'block';
          } else if(status == 'add') {
            if($('addfriend_'+id))
              $('addfriend_'+id).style.display = 'block';
            if($('confirmfriend_'+id))
              $('confirmfriend_'+id).style.display = 'none';
            if($('pendingfriend_'+id))
              $('pendingfriend_'+id).style.display = 'none';
            if($('removefriend_'+id))
              $('removefriend_'+id).style.display = 'none';
          }
        }
        //-->
        </script>
        {/literal}
        <div id='addfriend_{$owner->user_info.user_id}'{if $is_friend == TRUE || $is_friend_pending != 0} style='display: none;'{/if}><a href="javascript:TB_show('{lang_print id=876}', 'user_friends_manage.php?user={$owner->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/addfriend16.gif' class='icon' border='0'>{lang_print id=838}</a></div>
        <div id='confirmfriend_{$owner->user_info.user_id}'{if $is_friend_pending != 1} style='display: none;'{/if}><a href="javascript:TB_show('{lang_print id=887}', 'user_friends_manage.php?user={$owner->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/addfriend16.gif' class='icon' border='0'>{lang_print id=885}</a></div>
        <div id='pendingfriend_{$owner->user_info.user_id}'{if $is_friend_pending != 2} style='display: none;'{/if} class='nolink'><img src='./images/icons/addfriend16.gif' class='icon' border='0'>{lang_print id=875}</div>
        <div id='removefriend_{$owner->user_info.user_id}'{if $is_friend == FALSE || $is_friend_pending != 0} style='display: none;'{/if}><a href="javascript:TB_show('{lang_print id=837}', 'user_friends_manage.php?task=remove&user={$owner->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/remove_friend16.gif' class='icon' border='0'>{lang_print id=837}</a></div>
      </td></tr>
      {assign var='showmenu' value='1'}
    {/if}
    
    {* SHOW SEND MESSAGE MENU ITEM *}
    {if ($user->level_info.level_message_allow == 2 || ($user->level_info.level_message_allow == 1 && $is_friend)) && $owner->level_info.level_message_allow != 0}
      <tr><td class='profile_menu1' nowrap='nowrap'><a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$owner->user_displayname|escape:url}&to_id={$owner->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');"><img src='./images/icons/sendmessage16.gif' class='icon' border='0'>{lang_print id=839}</a></td></tr>
      {assign var='showmenu' value='1'}
    {/if}
    
    {* SHOW REPORT THIS PERSON MENU ITEM *}
    {if $user->user_exists != 0}
      <tr><td class='profile_menu1' nowrap='nowrap'><a href="javascript:TB_show('{lang_print id=857}', 'user_report.php?return_url={$url->url_current()|escape:url}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/report16.gif' class='icon' border='0'>{lang_print id=840}</a></td></tr>
      {assign var='showmenu' value='1'}
    {/if}
    
    {* SHOW BLOCK OR UNBLOCK THIS PERSON MENU ITEM *}
    {if $user->level_info.level_profile_block != 0}
      <tr><td class='profile_menu1' nowrap='nowrap'>
        <div id='unblock'{if $user->user_blocked($owner->user_info.user_id) == FALSE} style='display: none;'{/if}><a href="javascript:TB_show('{lang_print id=869}', 'user_friends_block.php?task=unblock&user={$owner->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/unblock16.gif' class='icon' border='0'>{lang_print id=841}</a></div>
        <div id='block'{if $user->user_blocked($owner->user_info.user_id) == TRUE} style='display: none;'{/if}><a href="javascript:TB_show('{lang_print id=868}', 'user_friends_block.php?task=block&user={$owner->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');"><img src='./images/icons/block16.gif' class='icon' border='0'>{lang_print id=842}</a></div>
      </td></tr>
      {assign var='showmenu' value='1'}
    {/if}

  {/if}


  {* PLUGIN RELATED PROFILE MENU ITEMS *}
  {hook_foreach name=profile_menu var=profile_menu_args}
    {assign var='showmenu' value='1'}
    <tr>
      <td class='profile_menu1' nowrap='nowrap'>
        <a href='{$profile_menu_args.file}'>
          <img src='./images/icons/{$profile_menu_args.icon}' class='icon' border='0' />
          {lang_sprintf id=$profile_menu_args.title 1=$profile_menu_args.title_1 2=$profile_menu_args.title_2}
        </a>
      </td>
    </tr>
  {/hook_foreach}
  
  </table>

  {if $showmenu == 1}
    <div style='height: 10px; font-size: 1pt;'>&nbsp;</div>
  {/if}


  {* DISPLAY IF PROFILE IS PRIVATE TO VIEWING USER *}
  {if $is_profile_private != 0}

    {* END LEFT COLUMN *}
    </td>
    <td class='profile_rightside'>
    {* BEGIN RIGHT COLUMN *}

      <img src='./images/icons/error48.gif' border='0' class='icon_big'>
      <div class='page_header'>{lang_print id=843}</div>
      {lang_print id=844}

  {* DISPLAY ONLY IF PROFILE IS NOT PRIVATE TO VIEWING USER *}
  {else}

    {* BEGIN STATUS *}
    {if ($owner->level_info.level_profile_status != 0 && ($owner->user_info.user_status != "" || $owner->user_info.user_id == $user->user_info.user_id)) || $is_online == 1}
      <table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
      <tr>
      <td class='header'>{lang_print id=768}</td>
      <tr>
      <td class='profile'>
        {if $is_online == 1}
          <table cellpadding='0' cellspacing='0'>
          <tr>
          <td valign='top'><img src='./images/icons/online16.gif' border='0' class='icon'></td>
          <td>{lang_sprintf id=845 1=$owner->user_displayname_short}</td>
          </tr>
          </table>
        {/if}
        
        {if $owner->level_info.level_profile_status != 0 && ($owner->user_info.user_status != "" || $owner->user_info.user_id == $user->user_info.user_id)}
          <table cellpadding='0' cellspacing='0'{if $is_online == 1} style='margin-top: 5px;'{/if}>
          <tr>
          <td valign='top'><img src='./images/icons/status16.gif' border='0' class='icon'></td>
          <td>
            {if $owner->user_info.user_id == $user->user_info.user_id}
              {* JAVASCRIPT FOR CHANGING STATUS - THIS IS ONLY SHOWN WHEN OWNER IS VIEWING OWN PROFILE, SO WE CAN USE VIEWER OBJECT *}
              {lang_javascript ids=773,1113 range=743-747}
              {literal}
              <script type="text/javascript">
              <!-- 
              SocialEngine.Viewer.user_status = '{/literal}{$user->user_info.user_status}{literal}';
              //-->
              </script>
              {/literal}
              
              <div id='ajax_status'>
              {if $owner->user_info.user_status != ""}
                {assign var='status_date' value=$datetime->time_since($user->user_info.user_status_date)}
                {$user->user_displayname_short} <span id='ajax_currentstatus_value'>{$user->user_info.user_status}</span>
                <div style='padding-top: 5px;'>
                  <div style='float: left; padding-right: 5px;'>[ <a href="javascript:void(0);" onClick="SocialEngine.Viewer.userStatusChange(); return false;">{lang_print id=745}</a> ]</div>
                  <div class='home_updated'>
                    {lang_print id=1113}
                    <span id='ajax_currentstatus_date'>{lang_sprintf id=$status_date[0] 1=$status_date[1]}</span>
                  </div>
                  <div style='clear: both; height: 0px;'></div>
                </div>
              {else}
                <a href="javascript:void(0);" onClick="SocialEngine.Viewer.userStatusChange(); return false;">{lang_print id=743}</a>
              {/if}
              </div>
            {else}
              {assign var='status_date' value=$datetime->time_since($owner->user_info.user_status_date)}
              {$owner->user_displayname_short} {$owner->user_info.user_status}
              <br>{lang_print id=1113} <span id='ajax_currentstatus_date'>{lang_sprintf id=$status_date[0] 1=$status_date[1]}</span>
            {/if}
          </td>
          </tr>
          </table>
        {/if}
      </td>
      </tr>
      </table>
    {/if}
    {* END STATUS *}
    
    {* BEGIN STATS *}
    <table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
    <tr><td class='header'>{lang_print id=24}</td></tr>
    <tr>
    <td class='profile'>
      <table cellpadding='0' cellspacing='0'>
      <tr><td width='80' valign='top'>{lang_print id=1120}</td><td><a href='search_advanced.php?cat_selected={$owner->profilecat_info.profilecat_id}'>{lang_print id=$owner->profilecat_info.profilecat_title}</a></td></tr>
      <tr><td valign='top'>{lang_print id=1119}</td><td>{lang_print id=$owner->subnet_info.subnet_name}</td></tr>
      <tr><td>{lang_print id=846}</td><td>{lang_sprintf id=740 1=$profile_views}</td></tr>
      {if $setting.setting_connection_allow != 0}<tr><td>{lang_print id=847}</td><td>{lang_sprintf id=848 1=$total_friends}</td></tr>{/if}
      {if $owner->user_info.user_dateupdated != ""}<tr><td>{lang_print id=1113}</td><td>{assign var='last_updated' value=$datetime->time_since($owner->user_info.user_dateupdated)}{lang_sprintf id=$last_updated[0] 1=$last_updated[1]}</td></tr>{/if}
      {if $owner->user_info.user_signupdate != ""}<tr><td>{lang_print id=850}</td><td>{$datetime->cdate("`$setting.setting_dateformat`", $datetime->timezone("`$owner->user_info.user_signupdate`", $global_timezone))}</td></tr>{/if}
      </table>
    </td>
    </tr>
    </table>
    {* END STATS *}
    
    
    {* PLUGIN RELATED PROFILE SIDEBAR *}
    {hook_foreach name=profile_side var=profile_side_args}
      {include file=$profile_side_args.file}
    {/hook_foreach}
    
    
  {* END LEFT COLUMN *}
  </td>
  <td class='profile_rightside'>
  {* BEGIN RIGHT COLUMN *}
    
    {* JAVASCRIPT FOR SWITCHING TABS *}
    {literal}
    <script type='text/javascript'>
    <!--
      var visible_tab = '{/literal}{$v}{literal}';
      function loadProfileTab(tabId)
      {
        if(tabId == visible_tab)
        {
          return false;
        }
        if($('profile_'+tabId))
        {
          $('profile_tabs_'+tabId).className='profile_tab2';
          $('profile_'+tabId).style.display = "block";
          if($('profile_tabs_'+visible_tab))
          {
            $('profile_tabs_'+visible_tab).className='profile_tab';
            $('profile_'+visible_tab).style.display = "none";
          }
          visible_tab = tabId;
        }
      }
    //-->
    </script>
    {/literal}
    
    {* SHOW PROFILE TAB BUTTONS *}
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td valign='bottom'><table cellpadding='0' cellspacing='0'><tr><td class='profile_tab{if $v == 'profile'}2{/if}' id='profile_tabs_profile' onMouseUp="this.blur()"><a href='javascript:void(0);' onMouseDown="loadProfileTab('profile')" onMouseUp="this.blur()">{lang_print id=652}</a></td></tr></table></td>
    {if $total_friends_all != 0}<td valign='bottom'><table cellpadding='0' cellspacing='0'><td class='profile_tab{if $v == 'friends'}2{/if}' id='profile_tabs_friends' onMouseUp="this.blur()"><a href='javascript:void(0);' onMouseDown="loadProfileTab('friends');" onMouseUp="this.blur()">{lang_print id=653}</a></td></tr></table></td>{/if}
    {if $allowed_to_comment != 0 || $total_comments != 0}<td valign='bottom'><table cellpadding='0' cellspacing='0'><td class='profile_tab{if $v == 'comments'}2{/if}' id='profile_tabs_comments' onMouseUp="this.blur()"><a href='javascript:void(0);' onMouseDown="loadProfileTab('comments');SocialEngine.ProfileComments.getComments(1)" onMouseUp="this.blur()">{lang_print id=854}</a></td></tr></table></td>{/if}
    
    {* PLUGIN RELATED PROFILE TABS *}
    {hook_foreach name=profile_tab var=profile_tab_args max=8 complete=profile_tab_complete}
      <td valign='bottom'>
        <table cellpadding='0' cellspacing='0' style='float: left;'>
          <tr>
            <td class='profile_tab{if $v == $profile_tab_args.name}2{/if}' id='profile_tabs_{$profile_tab_args.name}' onMouseUp="this.blur();">
              <a href='javascript:void(0);' onMouseDown="loadProfileTab('{$profile_tab_args.name}')" onMouseUp="this.blur();">
                {lang_print id=$profile_tab_args.title}
              </a>
            </td>
          </tr>
        </table>
      </td>
    {/hook_foreach}
    
    {if !$profile_tab_complete}
      <td valign='bottom'>
        <table cellpadding='0' cellspacing='0' style='float: left;'>
          <tr>
            <td class='profile_tab' onMouseUp="this.blur();" nowrap="nowrap">
              <a href="javascript:void(0);" onclick="$('profile_tab_dropdown').style.display = ( $('profile_tab_dropdown').style.display=='none' ? 'inline' : 'none' ); this.blur(); return false;" nowrap="nowrap">
                {lang_print id=1317}
              </a>
            </td>
          </tr>
        </table>
        <div class='menu_profile_dropdown' id='profile_tab_dropdown' style='display: none;'>
          <div>
            {* SHOW ANY PLUGIN MENU ITEMS *}
            {hook_foreach name=profile_tab var=profile_tab_args start=8}
            <div class='menu_profile_item_dropdown'>
              <div  id='profile_tabs_{$profile_tab_args.name}' onMouseUp="this.blur();">
              <a href='javascript:void(0);' onMouseDown="loadProfileTab('{$profile_tab_args.name}')" onMouseUp="this.blur();" class='menu_profile_item' style="text-align: left;">
                {lang_print id=$profile_tab_args.title}
              </a>
            </div></div>
            {/hook_foreach}
          </div>
        </div>
      </td>
    {/if}
    
    <td width='100%' class='profile_tab_end'>&nbsp;</td>
    </tr>
    </table>
    
    
    
    
    <div class='profile_content'>
    
    {* PROFILE TAB *}
    <div id='profile_profile'{if $v != 'profile'} style='display: none;'{/if}>
      
      {* SHOW PROFILE CATS AND FIELDS *}
      {section name=cat_loop loop=$cats}
        {section name=subcat_loop loop=$cats[cat_loop].subcats}
          <div class='profile_headline{if !$smarty.section.subcat_loop.first}2{/if}'><b>{lang_print id=$cats[cat_loop].subcats[subcat_loop].subcat_title}</b></div>
            
            <table cellpadding='0' cellspacing='0'>
            {* LOOP THROUGH FIELDS IN TAB, ONLY SHOW FIELDS THAT HAVE BEEN FILLED IN *}
            {section name=field_loop loop=$cats[cat_loop].subcats[subcat_loop].fields}
              <tr>
              <td valign='top' style='padding-right: 10px;' nowrap='nowrap'>
                {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_title}:
              </td>
              <td>
                <div class='profile_field_value'>{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value_formatted}</div>
                {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_special == 1 && $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value|substr:0:4 != "0000"} ({lang_sprintf id=852 1=$datetime->age($cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value)}){/if}
              </td>
              </tr>
            {/section}
            </table>
          
        {/section}
      {/section}
      {* END PROFILE TABS AND FIELDS *}
      
      {* SHOW RECENT ACTIVITY *}
      {if $actions|@count > 0}
        {literal}
        <script language="JavaScript">
        <!-- 
          Rollimage0 = new Image(10,12);
          Rollimage0.src = "./images/icons/action_delete1.gif";
          Rollimage1 = new Image(10,12);
          Rollimage1.src = "./images/icons/action_delete2.gif";
        //-->
        </script>
        {/literal}
        
        {* SHOW RECENT ACTIONS *}
        <div style='padding-bottom: 10px;' id='actions'>
          <div class='profile_headline2'><b>{lang_print id=851}</b></div>
          {section name=actions_loop loop=$actions}
            <div id='action_{$actions[actions_loop].action_id}' class='profile_action'>
              <table cellpadding='0' cellspacing='0'>
              <tr>
              <td valign='top'><img src='./images/icons/{$actions[actions_loop].action_icon}' border='0' class='icon'></td>
              <td valign='top' width='100%'>
                <div class='profile_action_date'>
                  {assign var='action_date' value=$datetime->time_since($actions[actions_loop].action_date)}
                  {lang_sprintf id=$action_date[0] 1=$action_date[1]}
                  {* DISPLAY DELETE LINK IF NECESSARY *}
                  {if $setting.setting_actions_selfdelete == 1 && $actions[actions_loop].action_user_id == $user->user_info.user_id}
                    <img src='./images/icons/action_delete1.gif' style='vertical-align: middle; margin-left: 3px; cursor: pointer; cursor: hand;' border='0' onmouseover="this.src=Rollimage1.src;" onmouseout="this.src=Rollimage0.src;" onClick="SocialEngine.Viewer.userActionDelete({$actions[actions_loop].action_id});" />
                  {/if}
                </div>
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
      {* END RECENT ACTIVITY *}
      
    </div>
    {* END PROFILE TAB *}
    
    
    
    
    
    {* FRIENDS TAB *}
    {if $total_friends_all != 0}
      <div id='profile_friends'{if $v != 'friends'} style='display: none;'{/if}>
        
        <div>
          <div style='float: left; width: 50%;'>
            <div class='profile_headline'>
              {if $m == 1}
                {lang_sprintf id=1024 1=$owner->user_displayname_short}
              {else}
                {lang_sprintf id=930 1=$owner->user_displayname_short}
              {/if} ({$total_friends})
            </div>
          </div>
          <div style='float: right; width: 50%; text-align: right;'>
            {if $search == ""}
              <div id='profile_friends_searchbox_link'>
                <a href='javascript:void(0);' onClick="$('profile_friends_searchbox_link').style.display='none';$('profile_friends_searchbox').style.display='block';$('profile_friends_searchbox_input').focus();">{lang_print id=1197}</a>
              </div>
            {/if}
            <div id='profile_friends_searchbox' style='text-align: right;{if $search == ""} display: none;{/if}'>
              <form action='profile.php' method='post'>
              <input type='text' maxlength='100' size='30' class='text' name='search' value='{$search}' id='profile_friends_searchbox_input'>
              <input type='submit' class='button' value='{lang_print id=646}'>
              <input type='hidden' name='p' value='{$p}'>
              <input type='hidden' name='v' value='friends'>
              <input type='hidden' name='user' value='{$owner->user_info.user_username}'>
              </form>
            </div>
          </div>
          <div style='clear: both;'></div>
        </div>
        
        {* IF MUTUAL FRIENDS EXIST, SHOW OPTION TO VIEW THEM *}
        {if $owner->user_info.user_id != $user->user_info.user_id && $total_friends_mut != 0}
          <div style='margin-bottom: 10px;'>
            {if $m != 1}
              {lang_print id=1022}
            {else}
              <a href='profile.php?user={$owner->user_info.user_username}&v=friends'>{lang_print id=1022}</a>
            {/if}
            &nbsp;|&nbsp; 
            {if $m == 1}
              {lang_print id=1020}
            {else}
              <a href='profile.php?user={$owner->user_info.user_username}&v=friends&m=1'>{lang_print id=1020}</a>
            {/if}
          </div>
        {/if}
        
        {* DISPLAY NO RESULTS MESSAGE *}
        {if $search != "" && $total_friends == 0}
          <br>
          <table cellpadding='0' cellspacing='0'>
          <tr><td class='result'>
            <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_sprintf id=934 1=$owner->user_displayname_short}
          </td></tr>
          </table>
        {elseif $m == 1 && $total_friends == 0}
          <br>
          <table cellpadding='0' cellspacing='0'>
          <tr><td class='result'>
            <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_sprintf id=1023 1=$owner->user_displayname_short}
          </td></tr>
          </table>
        {/if}
        
        
        {* DISPLAY PAGINATION MENU IF APPLICABLE *}
        {if $maxpage_friends > 1}
          <div style='text-align: center;'>
            {if $p_friends != 1}<a href='profile.php?user={$owner->user_info.user_username}&v=friends&search={$search}&m={$m}&p={math equation='p-1' p=$p_friends}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
            {if $p_start_friends == $p_end_friends}
              &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start_friends 2=$total_friends} &nbsp;|&nbsp; 
            {else}
              &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start_friends 2=$p_end_friends 3=$total_friends} &nbsp;|&nbsp; 
            {/if}
            {if $p_friends != $maxpage_friends}<a href='profile.php?user={$owner->user_info.user_username}&v=friends&search={$search}&m={$m}&p={math equation='p+1' p=$p_friends}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
          </div>
        {/if}
        
        {* LOOP THROUGH FRIENDS *}
        {section name=friend_loop loop=$friends}
          <div class='browse_friends_result' style='overflow: hidden;'>
            <div class='profile_friend_photo'>
              <a href='{$url->url_create("profile",$friends[friend_loop]->user_info.user_username)}'>
                <img src='{$friends[friend_loop]->user_photo("./images/nophoto.gif")}' width='{$misc->photo_size($friends[friend_loop]->user_photo("./images/nophoto.gif"),"90","90","w")}' border='0' alt="{lang_sprintf id=509 1=$friends[friend_loop]->user_displayname_short}">
              </a>
            </div>
            <div class='profile_friend_info'>
              <div class='profile_friend_name'><a href='{$url->url_create('profile',$friends[friend_loop]->user_info.user_username)}'>{$friends[friend_loop]->user_displayname}</a></div>
              <div class='profile_friend_details'>
                {if $friends[friend_loop]->user_info.user_dateupdated != 0}<div>{lang_print id=849} {assign var='last_updated' value=$datetime->time_since($friends[friend_loop]->user_info.user_dateupdated)}{lang_sprintf id=$last_updated[0] 1=$last_updated[1]}</div>{/if}
                {if $show_details != 0}
                  {if $friends[friend_loop]->friend_type != ""}<div>{lang_print id=882} {$friends[friend_loop]->friend_type}</div>{/if}
                  {if $friends[friend_loop]->friend_explain != ""}<div>{lang_print id=907} {$friends[friend_loop]->friend_explain}</div>{/if}
                {/if}
              </div>
            </div>
            <div class='profile_friend_options'>
              {if !$friends[friend_loop]->is_viewers_friend && !$friends[friend_loop]->is_viewers_blocklisted && $friends[friend_loop]->user_info.user_id != $user->user_info.user_id && $user->user_exists != 0}<div id='addfriend_{$friends[friend_loop]->user_info.user_id}'><a href="javascript:TB_show('{lang_print id=876}', 'user_friends_manage.php?user={$friends[friend_loop]->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');">{lang_print id=922}</a></div>{/if}
              {if !$members[member_loop].member->is_viewer_blocklisted && ($user->level_info.level_message_allow == 2 || ($user->level_info.level_message_allow == 1 && $friends[friend_loop]->is_viewers_friend == 2)) && $friends[friend_loop]->user_info.user_id != $user->user_info.user_id}<a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$friends[friend_loop]->user_displayname}&to_id={$friends[friend_loop]->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');">{lang_print id=839}</a>{/if}
            </div>
            <div style='clear: both;'></div>
          </div>
        {/section}
        
        
        {* DISPLAY PAGINATION MENU IF APPLICABLE *}
        {if $maxpage_friends > 1}
          <div style='text-align: center;'>
            {if $p_friends != 1}<a href='profile.php?user={$owner->user_info.user_username}&v=friends&search={$search}&m={$m}&p={math equation='p-1' p=$p_friends}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
            {if $p_start_friends == $p_end_friends}
              &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start_friends 2=$total_friends} &nbsp;|&nbsp; 
            {else}
              &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start_friends 2=$p_end_friends 3=$total_friends} &nbsp;|&nbsp; 
            {/if}
            {if $p_friends != $maxpage_friends}<a href='profile.php?user={$owner->user_info.user_username}&v=friends&search={$search}&m={$m}&p={math equation='p+1' p=$p_friends}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
          </div>
        {/if}
        
        
      </div>
    {/if}
    {* END FRIENDS TAB *}
    
    
    
    
    
    
    
    {* BEGIN COMMENTS TAB *}
    {if $allowed_to_comment != 0 || $total_comments != 0}
      
      {* SHOW COMMENT TAB *}
      <div id='profile_comments'{if $v != 'comments'} style='display: none;'{/if}>
        
        {* COMMENTS *}
        <div id="profile_{$owner->user_info.user_id}_postcomment"></div>
        <div id="profile_{$owner->user_info.user_id}_comments" style='margin-left: auto; margin-right: auto;'></div>
        
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
        
          SocialEngine.ProfileComments = new SocialEngineAPI.Comments({ldelim}
            'canComment' : {if $allowed_to_comment}true{else}false{/if},
            'commentHTML' : '{$setting.setting_comment_html|replace:",":", "}',
            'commentCode' : {if $setting.setting_comment_code}true{else}false{/if},
            
            'type' : 'profile',
            'typeIdentifier' : 'user_id',
            'typeID' : {$owner->user_info.user_id},
            
            'typeTab' : 'users',
            'typeCol' : 'user',
            
            'initialTotal' : {$total_comments|default:0},
            
            'paginate' : true,
            'cpp' : 10,
            
            'commentLinks' : {literal}{'reply' : true, 'walltowall' : true}{/literal}
          {rdelim});
        
          SocialEngine.RegisterModule(SocialEngine.ProfileComments);
       
          // Backwards
          function addComment(is_error, comment_body, comment_date)
          {ldelim}
            SocialEngine.ProfileComments.addComment(is_error, comment_body, comment_date);
          {rdelim}
        
          function getComments(direction)
          {ldelim}
            SocialEngine.ProfileComments.getComments(direction);
          {rdelim}

        </script>
        
        
      </div>
      
      
    {/if}
    {* END COMMENTS *}
    
    
    
    {* PLUGIN RELATED PROFILE TABS *}
    {hook_foreach name=profile_tab var=profile_tab_args}
      <div id='profile_{$profile_tab_args.name}'{if $v != $profile_tab_args.name} style='display: none;'{/if}>
        {include file=$profile_tab_args.file}
      </div>
    {/hook_foreach}
    
    
  {/if}
  {* END PRIVACY IF STATEMENT *}

  </div>

{* END RIGHT COLUMN *}
</td>
</tr>
</table>

{include file='footer.tpl'}
