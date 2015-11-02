{include file='header.tpl'}

{* $Id: event.tpl 162 2009-04-30 01:43:11Z john $ *}

{* JAVASCRIPT *}
{lang_javascript ids=861,3000080,3000081,3000082,3000083,3000084,3000085,3000093,3000097,3000153,3000154,3000170,3000219,3000223,3000225,3000229}
<script type="text/javascript" src="./include/js/class_event.js"></script>
<script type='text/javascript'>
<!--
  SocialEngine.Event = new SocialEngineAPI.Event({$event->event_generate_javascript_structure()}, {ldelim} 'defaultView' : '{$v}', 'ajaxURL' : SocialEngine.URL.url_base + 'event_ajax.php' {rdelim});
  SocialEngine.RegisterModule(SocialEngine.Event);
  
  // Delete redirect function
  function redirectOnDelete()
  {ldelim}
    window.location.href = SocialEngine.URL.url_base + 'user_event.php';
  {rdelim}
//-->
</script>


{* HIDDEN DIV TO DISPLAY CANCEL REQUEST CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmeventrequestcancel'>
  <div style='margin-top: 10px;'>
    {lang_print id=3000221}
  </div>
  <br />
  {lang_block id=175 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.SocialEngine.Event.cancelConfirm();' />{/lang_block}
  {lang_block id=39 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();' />{/lang_block}
</div>


{* HIDDEN DIV TO DISPLAY LEAVE CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmeventleave'>
  <div style='margin-top: 10px;'>
    {lang_print id=3000220}
  </div>
  <br />
  {lang_block id=3000219 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.SocialEngine.Event.leaveConfirm();' />{/lang_block}
  {lang_block id=39 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();' />{/lang_block}
</div>


{* HIDDEN DIV TO DISPLAY DELETE CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmeventdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=3000094}
  </div>
  <br />
  {lang_block id=175 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.SocialEngine.Event.deleteConfirm();' />{/lang_block}
  {lang_block id=39 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();' />{/lang_block}
</div>


{* HIDDEN DIV TO DISPLAY MEMBER INVITE *}
<div style='display: none;' id='eventmemberinvite'>
  {* NO FRIENDS MESSAGE *}
  <div style='text-align:center;margin:10px;font-weight:bold;border: 1px dashed #CCCCCC;background: #FFFFFF;padding: 7px 8px 7px 7px;' id='noFriends'>
    <img src='./images/icons/bulb16.gif' class='icon'>
    {lang_print id=3000227}
    <br />
    <br />
    {lang_block id=39 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();' />{/lang_block}
  </div>
  
  {* INVITE DIALOG *}
  <div style='display:none;text-align:left;padding:10px;' id='inviteForm'>
    <div>{lang_print id=3000226}</div>
    <br />
    
    <div><a href='javascript:void(0);' id="eventMemberInviteSelectAll" onClick="var checkboxes = document.getElementsByTagName('input'); for( var i=0, l=checkboxes.length; i<l; i++ ) if( checkboxes[i].type=='checkbox' && parseInt(checkboxes[i].value)>0 ) {ldelim} checkboxes[i].checked = true; parent.SocialEngine.Event.memberInviteUpdate(checkboxes[i].value, true); {rdelim}">{lang_print id=3000228}</a></div>
    <div id='invite_friendlist' class='invite_friendlist'></div>
    
    <div style='margin-top: 20px;'>
      {lang_block id=3000225 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.SocialEngine.Event.memberInviteSend();' />{/lang_block}
      {lang_block id=39 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();' />{/lang_block}
    </div>
  </div>
  
  {* RESULT DIALOG *}
  <div style='display:none;text-align:left;padding:10px;' id='inviteResults'></div>
</div>








<div class='page_header'>{$event->event_info.event_title}</div>

<table width='100%' cellpadding='0' cellspacing='0'><tr><td class='profile_leftside' width='200'>
{* BEGIN LEFT COLUMN *}

  {* SHOW PHOTO *}
  <table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
    <tr>
      <td class='profile_photo' width='182'>
        <img class='photo' src='{$event->event_photo("./images/nophoto.gif")}' border='0' />
      </td>
    </tr>
  </table>

  <table class='profile_menu' cellpadding='0' cellspacing='0' width='100%'>
    
    {if $allowed_to_view}
    
    {* SHOW EDIT IF ALLOWED *}
    {if $event->is_member && $event->user_rank==3}
    <tr>
      <td class='profile_menu1'>
        <a href='user_event_edit.php?event_id={$event->event_info.event_id}'>
          <img src='./images/icons/event_edit16.gif' border='0' class='icon' />
          {lang_print id=3000245}
        </a>
      </td>
    </tr>
    {/if}
    
    {* SHOW DELETE EVENT BUTTON IF OWNER *}
    {if $event->is_member && $event->user_rank==3}
    <tr>
      <td class='profile_menu1'>
        <a href='javascript:void(0);' onclick="SocialEngine.Event.deleteShow();">
          <img src='./images/icons/event_delete16.gif' border='0' class='icon' />
          {lang_print id=3000169}
        </a>
      </td>
    </tr>
    {/if}
    
    {* JOIN GROUP || ACCEPT INVITATION *}
    {*
    (!$event->is_member && !$event->is_member_waiting && !$event->event_info.event_inviteonly) ||
    ($event->eventmember_info.eventmember_approved && !$event->eventmember_info.eventmember_status)
    *}
    
    {*
    <tr id="eventProfileMenuJoin"{if !(!$event->is_member && !$event->is_member_waiting && !$event->event_info.event_inviteonly) && !($event->eventmember_info.eventmember_approved && !$event->eventmember_info.eventmember_status)} style="display:none;"{/if}>
      <td class='profile_menu1'>
        <a href='javascript:void(0);' onclick="SocialEngine.Event.join();">
          <img src='./images/icons/event_join16.gif' border='0' class='icon' />
          {lang_print id=3000168}
        </a>
      </td>
    </tr>
    *}
    
    {* REQUEST INVITATION *}
    <tr id="eventProfileMenuRequest"{if !(!$event->is_member && !$event->is_member_waiting && $event->event_info.event_inviteonly)} style="display:none;"{/if}>
      <td class='profile_menu1'>
        <a href='javascript:void(0);' onclick="SocialEngine.Event.request();">
          <img src='./images/icons/event_join16.gif' border='0' class='icon' />
          {lang_print id=3000167}
        </a>
      </td>
    </tr>
    
    {* CANCEL REQUEST INVITATION *}
    <tr id="eventProfileMenuCancel"{if !(!$event->eventmember_info.eventmember_approved && $event->eventmember_info.eventmember_status)} style="display:none;"{/if}>
      <td class='profile_menu1'>
        <a href='javascript:void(0);' onclick="SocialEngine.Event.cancelShow();">
          <img src='./images/icons/event_remove16.gif' border='0' class='icon' />
          {lang_print id=3000170}
        </a>
      </td>
    </tr>
    
    {* RSVP *}
    {*
    <tr id="eventProfileMenuRSVP"{if !$event->is_member} style="display:none;"{/if}>
      <td class='profile_menu1'>
        <a href='javascript:void(0);' onclick="SocialEngine.Event.rsvpShow();">
          <img src='./images/icons/event_rsvp16.gif' border='0' class='icon' />
          {lang_print id=3000097}
        </a>
      </td>
    </tr>
    *}
    
    {* LEAVE *}
    <tr id="eventProfileMenuLeave"{if $event->user_rank==3 || !$event->is_member} style="display:none;"{/if}>
      <td class='profile_menu1'>
        <a href='javascript:void(0);' onclick="SocialEngine.Event.leaveShow();">
          <img src='./images/icons/event_remove16.gif' border='0' class='icon' />
          {lang_print id=3000219}
        </a>
      </td>
    </tr>
    
    {* INVITE *}
    <tr id="eventProfileMenuInvite"{if $event->user_rank!=3 && !($event->is_member && $event->event_info.event_invite)} style="display:none;"{/if}>
      <td class='profile_menu1'>
        <a href='javascript:void(0);' onclick='SocialEngine.Event.memberInvitePopulate();'>
          <img src='./images/icons/event_invite16.gif' border='0' class='icon' />
          {lang_print id=3000145}
        </a>
      </td>
    </tr>
    
    
    {* PLUGIN RELATED MENU ITEMS *}
    {foreach from=$global_plugins key=plugin_k item=plugin_v}
      {if !empty($plugin_v.menu_event_actions)}
        {foreach from=$plugin_v.menu_event_actions key=plugin_mk item=plugin_mv}
        <tr>
          <td class='{$plugin_mv.className|default:"profile_menu1"}'>
            <a href='{$plugin_mv.link}'{if !empty($plugin_mv.onclick)} onclick="{$plugin_mv.onclick}"{/if}>
              <img src='{$plugin_mv.icon}' class='icon' border='0' />
              {lang_print id=$plugin_mk.title}
            </a>
          </td>
        </tr>
        {/foreach}
      {/if}
    {/foreach}
    
    {/if}
    
    
    
    {* SHOW REPORT THIS EVENT MENU ITEM *}
    <tr>
      <td class='profile_menu1'>
        <a href="javascript:TB_show('{lang_print id=3000172}', 'user_report.php?return_url={$url->url_current()|escape:url}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');">
          <img src='./images/icons/report16.gif' class='icon' border='0' />
          {lang_print id=3000172}
        </a>
      </td>
    </tr>
    
  </table>
  
  
  {if $allowed_to_view}
  
  {* SHOW OFFICERS *}
  <table cellpadding='0' cellspacing='0' width='100%' style='margin-top: 10px;'>
    <tr>
      <td class='header'>{lang_print id=3000269}</td>
    </tr>
    <tr>
      <td class='profile'>
        {section name=officer_loop loop=$officers}
        <div>
          <a href='{$url->url_create("profile", $officers[officer_loop].member->user_info.user_username)}'>{$officers[officer_loop].member->user_displayname}</a>{if $officers[officer_loop].eventmember_rank == 3} ({lang_print id=3000270}){/if}
          {*
          {if $officers[officer_loop].eventmember_title != "" && $event->eventowner_level_info.level_event_titles == 1}
          <div class='event_officer_title'>{$officers[officer_loop].eventmember_title}</div>
          {/if}
          *}
          {if !$smarty.section.officer_loop.last}<div style='height: 4px;'></div>{/if}
        </div>
        {/section}
      </td>
    </tr>
  </table>
  {* END OFFICERS *}
  
  
  
  {* SHOW RSVP OPTIONS *}
  <table cellpadding='0' cellspacing='0' width='100%' id="eventProfileMenuRSVP" style='margin-top: 10px;{if (!$event->is_member && $event->event_info.event_inviteonly && empty($event->eventmember_info.eventmember_approved) || !$user->user_exists)}display:none;{/if}'>
    <tr>
      <td class='header'>{lang_print id=3000278}</td>
    </tr>
    <tr>
      <td class='profile'>
        <div id="seEventProfileRSVPSuccess" style="display:none;margin-bottom: 4px;">{lang_print id=3000279}</div>
        
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td style="vertical-align:middle;padding:3px 3px 0px 0px;"><input class="seEventProfileRSVP" type="radio" id="seEventProfileRSVP_1" name="event_rsvp" onchange="SocialEngine.Event.rsvpConfirm(1, true);"{if $event->eventmember_info.eventmember_rsvp==1} checked{/if} /></td>
            <td style="vertical-align:middle;padding:3px 3px 0px 0px;"><label for="seEventProfileRSVP_1">{lang_print id=3000082}</label></td>
          </tr>
          <tr>
            <td style="vertical-align:middle;padding:3px 3px 0px 0px;"><input class="seEventProfileRSVP" type="radio" id="seEventProfileRSVP_2" name="event_rsvp" onchange="SocialEngine.Event.rsvpConfirm(2, true);"{if $event->eventmember_info.eventmember_rsvp==2} checked{/if} /></td>
            <td style="vertical-align:middle;padding:3px 3px 0px 0px;"><label for="seEventProfileRSVP_2">{lang_print id=3000083}</label></td>
          </tr>
          <tr>
            <td style="vertical-align:middle;padding:3px 3px 3px 0px;"><input class="seEventProfileRSVP" type="radio" id="seEventProfileRSVP_3" name="event_rsvp" onchange="SocialEngine.Event.rsvpConfirm(3, true);"{if $event->eventmember_info.eventmember_rsvp==3} checked{/if} /></td>
            <td style="vertical-align:middle;padding:3px 3px 3px 0px;"><label for="seEventProfileRSVP_3">{lang_print id=3000084}</label></td>
          </tr>
        </table>
        
      </td>
    </tr>
  </table>
  {* END RSVP OPTIONS *}
  
  
  
  {* PLUGIN RELATED PROFILE SIDEBAR *}
  {foreach from=$global_plugins key=plugin_k item=plugin_v}
    {if !empty($plugin_v.menu_event_side)}
      {include file=$plugin_v.menu_event_side.file}
    {/if} 
  {/foreach}
  
  {/if}
  
  
  
{* END LEFT COLUMN *}
</td><td class='profile_rightside'>
{* BEGIN RIGHT COLUMN *}
  
  
  
  {* DISPLAY IF EVENT IS PRIVATE TO VIEWING USER *}
  {if !$allowed_to_view}
    
    <img src='./images/icons/error48.gif' border='0' class='icon_big' />
    <div class='page_header'>{lang_print id=3000173}</div>
    You are not allowed to view this event.
    
  {* DISPLAY ONLY IF EVENT IS NOT PRIVATE TO VIEWING USER *}
  {else}
    
    
    {* SHOW PROFILE TAB BUTTONS *}
    <table cellpadding='0' cellspacing='0' id="event_tab_table">
      <tr>
        <td valign='bottom'>
          <table cellpadding='0' cellspacing='0'>
            <tr>
              <td class='event_tab event_tab_left event_tab_active' id='event_tabs_profile' onMouseUp="this.blur();" nowrap="nowrap">
                <a href='javascript:void(0);' onMouseDown="SocialEngine.Event.loadProfileTab('profile')" onMouseUp="this.blur();">{lang_print id=3000137}</a>
              </td>
            </tr>
          </table>
        </td>
        
        {*
        <td valign='bottom'>
          <table cellpadding='0' cellspacing='0'>
            <tr>
              <td class='event_tab' id='event_tabs_calendar' onMouseUp="this.blur();" nowrap="nowrap">
                <a href='javascript:void(0);' onMouseDown="SocialEngine.Event.loadProfileTab('calendar');" onMouseUp="this.blur();">{lang_print id=3000244}</a>
              </td>
            </tr>
          </table>
        </td>
        *}
        
        <td valign='bottom'>
          <table cellpadding='0' cellspacing='0'>
            <tr>
              <td class='event_tab' id='event_tabs_members' onMouseUp="this.blur();" nowrap="nowrap">
                <a href='javascript:void(0);' onMouseDown="SocialEngine.Event.loadProfileTab('members');" onMouseUp="this.blur();">{lang_print id=3000138}</a>
              </td>
            </tr>
          </table>
        </td>
        
        <td valign='bottom'>
          <table cellpadding='0' cellspacing='0'>
            <tr>
              <td class='event_tab' id='event_tabs_photos' onMouseUp="this.blur();" nowrap="nowrap">
                <a href='javascript:void(0);' onMouseDown="SocialEngine.Event.loadProfileTab('photos');" onMouseUp="this.blur();">{lang_print id=3000164}</a>
              </td>
            </tr>
          </table>
        </td>
        
        {if $allowed_to_comment || $event->event_info.event_totalcomments}
        <td valign='bottom'>
          <table cellpadding='0' cellspacing='0'>
            <tr>
              <td class='event_tab' id='event_tabs_comments' onMouseUp="this.blur();" nowrap="nowrap">
                <a href='javascript:void(0);' onMouseDown="SocialEngine.Event.loadProfileTab('comments');SocialEngine.EventComments.getComments(1);" onMouseUp="this.blur();">{lang_print id=854}</a>
              </td>
            </tr>
          </table>
        </td>
        {/if}
        
        {if !empty($plugin_v.menu_event_tab)}
        {foreach from=$global_plugins key=plugin_k item=plugin_v}
        {if !empty($plugin_v.menu_event_tab)}
        <td valign='bottom'>
          <table cellpadding='0' cellspacing='0'>
            <tr>
              <td class='event_tab' id='event_tabs_{$plugin_k}' onMouseUp="this.blur();" nowrap="nowrap">
                <a href='javascript:void(0);' onMouseDown="SocialEngine.Event.loadProfileTab('{$plugin_k}');" onMouseUp="this.blur();">{lang_print id=$plugin_v.menu_profile_tab.title}</a>
              </td>
            </tr>
          </table>
        </td>
        {/if} 
        {/foreach}
        {/if}
        
        <td width='100%' class='profile_tab_end'>&nbsp;</td>
      </tr>
    </table>
    
    
    
    <div class='profile_content'>
      
      {* PROFILE TAB - SHOW EVENT INFORMATION AND STATISTICS *}
      <div id='event_profile'>
      
        {* SHOW EVENT INFORMATION *}
        <div style='margin-bottom: 10px;'>
          <div class='event_headline'>{lang_print id=3000174}</div>
          <table cellpadding='0' cellspacing='0'>
            <tr>
              <td width='100' valign='top' nowrap='nowrap'>{lang_print id=3000110}</td>
              <td>{$event->event_info.event_title}</td>
            </tr>
            {if !empty($event->event_info.event_desc)}
            <tr>
              <td valign='top' nowrap='nowrap'>{lang_print id=3000111}</td>
              <td>{$event->event_info.event_desc}</td>
            </tr>
            {/if}
            <tr>
              <td valign='top' nowrap='nowrap'>{lang_print id=3000175}</td>
              <td>
                {assign var=event_date_start value=$datetime->timezone($event->event_info.event_date_start, $global_timezone)}
                {assign var=event_date_end value=$datetime->timezone($event->event_info.event_date_end, $global_timezone)}
                
                {* NO END DATE *}
                {if !$event->event_info.event_date_end}
                  {lang_sprintf id=3000203 1=$datetime->cdate($setting.setting_dateformat, $event_date_start) 2=$datetime->cdate($setting.setting_timeformat, $event_date_start)}
                
                {* SAME-DAY EVENT *}
                {elseif $datetime->cdate("F j, Y", $event_date_start)==$datetime->cdate("F j, Y", $event_date_end)}
                  {lang_sprintf id=3000202 1=$datetime->cdate($setting.setting_dateformat, $event_date_start) 2=$datetime->cdate($setting.setting_timeformat, $event_date_start) 3=$datetime->cdate($setting.setting_timeformat, $event_date_end)}
                
                {* MULTI-DAY EVENT *}
                {else}
                  {lang_sprintf id=3000204 1=$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $event_date_start) 2=$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $event_date_end)}
                {/if}
              </td>
            </tr>
            {if !empty($event->event_info.event_host)}
            <tr>
              <td valign='top' nowrap='nowrap'>{lang_print id=3000115}</td>
              <td>{$event->event_info.event_host}</td>
            </tr>
            {/if}
            {if !empty($event->event_info.event_location)}
            <tr>
              <td valign='top' nowrap='nowrap'>{lang_print id=3000116}</td>
              <td>{$event->event_info.event_location}</td>
            </tr>
            {/if}
            
            <tr>
              <td valign='top' nowrap='nowrap'>{lang_print id=3000280}</td>
              <td>
                {if $eventcat_info.subcat_dependency == 0}
                  <a href='browse_events.php?eventcat_id={$eventcat_info.subcat_id}'>{lang_print id=$eventcat_info.subcat_title}</a>
                {else}
                  <a href='browse_events.php?eventcat_id={$eventcat_info.cat_id}'>{lang_print id=$eventcat_info.cat_title}</a> -
                  <a href='browse_events.php?eventcat_id={$eventcat_info.subcat_id}'>{lang_print id=$eventcat_info.subcat_title}</a>
                {/if}
              </td>
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
          <div class='event_headline'>{lang_print id=3000201}</div>
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
                  {lang_sprintf assign=action_text id=$actions[actions_loop].action_text args=$actions[actions_loop].action_vars}
                  {$action_text|replace:"[media]":$action_media|choptext:50:"<br />"}
                </td>
              </tr>
            </table>
          </div>
          {/section}
        </div>
        {/if}
        {* END RECENT ACTIVITY *}
        
      </div>
      {* END PROFILE TAB - SHOW EVENT INFORMATION AND STATISTICS *}
      
      
      
      
      
      {* CALENDAR TAB - SHOW EVENT DATES *}
      {*
      <div id='event_calendar' style='display: none;'>
        
        <table><tr><td>
          {foreach name=calendar_year_loop from=$calendar_data.years key=year_time_value item=year_data}
            {foreach name=calendar_month_loop from=$year_data.months key=month_time_value item=month_data}
              
              {include file='event_calendar.tpl' calendar_view="month_small" show_next_last=0 month=$month_data.name year=$year_data.name days_in_month=$month_data.days_in_month first_day_of_month=$month_data.first_day_of_month last_day_of_month=$month_data.last_day_of_month total_cells=$month_data.total_cells event_days=$month_data.days}
              {if !$smarty.section.calendar_year_loop.last && !$smarty.section.calendar_month_loop.last}<div style='height: 8px;'></div>{/if}
              
            {/foreach}
          {/foreach}
        </td></tr></table>
        
      </div>
      *}
      {* END CALENDAR TAB - SHOW EVENT DATES *}
      
      
      
      
      
      {* MEMBERS TAB - SHOW EVENT MEMBERS AND INVITED USERS *}
      <div id='event_members' style='display: none;'>
        
        {* JAVASCRIPT FOR CHANGING FRIEND MENU OPTION *}
        {literal}
        <script type="text/javascript">
       <!-- 
          function friend_update(status, id)
          {
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
          <div class='event_headline'>{lang_print id=3000160} ({$event->event_info.event_totalmembers})</div>
        </td>
        <td valign='top' align='right'>

          <div id='event_members_searchbox' style='text-align: right;'>
            
            <form name="event_search_members_form" action='{$url->url_create("event", NULL, $event->event_info.event_id)}' method='post'>
            {strip}
            <table cellpadding='0' cellspacing='0' align="right">
            <tr>
            <td>{lang_print id=3000090}&nbsp;</td>
            <td>
              <select name="v_members" class="event_small" onchange="document.event_search_members_form.submit();">
                <option value=""{if !isset($v_members)} selected{/if}>{lang_print id=3000143} ({$event->event_info.event_totalmembers|default:0})</option>
                <option value="0"{if $v_members=="0"} selected{/if}>{lang_print id=3000081} ({$total_members_waiting|default:0})</option>
                <option value="1"{if $v_members=="1"} selected{/if}>{lang_print id=3000082} ({$total_members_attending|default:0})</option>
                <option value="2"{if $v_members=="2"} selected{/if}>{lang_print id=3000083} ({$total_members_maybeattending|default:0})</option>
                <option value="3"{if $v_members=="3"} selected{/if}>{lang_print id=3000084} ({$total_members_notattending|default:0})</option>
              </select>
            </td>
            </tr>
            </table>
            {/strip}
            
            <input type='hidden' name='p' value='{$p_members}' />
            <input type='hidden' name='v' value='members' />
            <input type='hidden' name='event_id_rem' value='{$event->event_info.event_id}' />
            </form>
            
          </div>
        </td>
        </tr>
        </table>
        
        {* DISPLAY NO RESULTS MESSAGE *}
        {if $search != "" && $total_members == 0}
          
          <table cellpadding='0' cellspacing='0' style="margin-top: 6px;">
            <tr>
              <td class='result'>
                <img src='./images/icons/bulb16.gif' border='0' class='icon' />
                {lang_print id=3000162}
              </td>
            </tr>
          </table>
          
        {/if}
        
        {* DISPLAY PAGINATION MENU IF APPLICABLE *}
        {if $maxpage_members > 1}
          <div style='text-align: center; margin-bottom: 5px;'>
            {if $p_members != 1}
              <a href='{$url->url_create("event", NULL, $event->event_info.event_id)}&v=members&search={$search}&p={math equation="p-1" p=$p_members}'>&#171; {lang_print id=182}</a>
            {else}
              <font class='disabled'>&#171; {lang_print id=182}</font>
            {/if}
            {if $p_start_members == $p_end_members}
              &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start_members 2=$total_members} &nbsp;|&nbsp; 
            {else}
              &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start_members 2=$p_end_members 3=$total_members} &nbsp;|&nbsp; 
            {/if}
            {if $p_members != $maxpage_members}
              <a href='{$url->url_create("event", NULL, $event->event_info.event_id)}&v=members&search={$search}&p={math equation="p+1" p=$p_members}'>{lang_print id=183} &#187;</a>
            {else}
              <font class='disabled'>{lang_print id=183} &#187;</font>
            {/if}
          </div>
        {/if}
        
        {* LOOP THROUGH MEMBERS *}
        {section name=member_loop loop=$members}
          <div class='event_members_result' style='overflow: hidden;'>
            <div class='event_members_photo'>
              <a href='{$url->url_create("profile",$members[member_loop].member->user_info.user_username)}'>
                <img src='{$members[member_loop].member->user_photo("./images/nophoto.gif")}' width='{$misc->photo_size($members[member_loop].member->user_photo("./images/nophoto.gif"),"90","90","w")}' border='0' alt="{lang_sprintf id=509 1=$members[member_loop].member->user_displayname_short}" class='photo' />
              </a>
            </div>
            <div class='profile_friend_info'>
              <div class='profile_friend_name'>
                <a href='{$url->url_create("profile",$members[member_loop].member->user_info.user_username)}'>
                  {$members[member_loop].member->user_displayname}
                </a>
              </div>
              <div class='profile_friend_details'>
                {if $members[member_loop].member->user_info.user_dateupdated != 0}
                <div>
                  {lang_print id=849}
                  {assign var='last_updated' value=$datetime->time_since($members[member_loop].member->user_info.user_dateupdated)}
                  {lang_sprintf id=$last_updated[0] 1=$last_updated[1]}
                </div>
                {/if}
                
                {* SHOW EVENT MEMBER RANK *}
                <div>
                  {if $members[member_loop].member->user_info.user_id==$event->event_info.event_user_id}
                    {lang_print id=3000152}
                  {else}
                    {lang_print id=3000163}
                  {/if}
                </div>
                
                {* SHOW EVENT MEMBER RSVP *}
                <div>
                  {lang_print id=$members[member_loop].eventmember_rsvp_lvid}
                </div>
              </div>
            </div>
            <div class='profile_friend_options'>
              {if !$members[member_loop].member->is_viewers_friend && !$members[member_loop].member->is_viewer_blocklisted && $members[member_loop].member->user_info.user_id!=$user->user_info.user_id && $user->user_exists}
              <div id='addfriend_{$members[member_loop].member->user_info.user_id}'>
                <a href="javascript:TB_show('{lang_print id=876}', 'user_friends_manage.php?user={$members[member_loop].member->user_info.user_username}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');">{lang_print id=838}</a>
              </div>
              {/if}
              {if !$members[member_loop].member->is_viewer_blocklisted && ($user->level_info.level_message_allow == 2 || ($user->level_info.level_message_allow == 1 && $members[member_loop].member->is_viewers_friend)) && $members[member_loop].member->user_info.user_id!=$user->user_info.user_id}
              <div id='messageuser_{$members[member_loop].member->user_info.user_id}'>
                <a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$members[member_loop].member->user_displayname}&to_id={$members[member_loop].member->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');">{lang_print id=839}</a>
              </div>
              {/if}
            </div>
            <div style='clear: both;'></div>
          </div>
          {if !$smarty.section.member_loop.last}<div style='clear: both; height: 8px;'></div>{/if}
        {/section}
        
        {* DISPLAY PAGINATION MENU IF APPLICABLE *}
        {if $maxpage_members > 1}
          <div style='text-align: center; margin-top: 5px;'>
            {if $p_members != 1}<a href='event.php?event_id={$event->event_info.event_id}&v=members&search={$search}&p={math equation='p-1' p=$p_members}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
            {if $p_start_members == $p_end_members}
              &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start_members 2=$total_members} &nbsp;|&nbsp; 
            {else}
              &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start_members 2=$p_end_members 3=$total_members} &nbsp;|&nbsp; 
            {/if}
            {if $p_members != $maxpage_members}<a href='event.php?event_id={$event->event_info.event_id}&v=members&search={$search}&p={math equation='p+1' p=$p_members}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
          </div>
        {/if}
        
      </div>
      {* END MEMBERS TAB *}
      
      
      {* PHOTOS TAB - SHOW EVENT PHOTOS *}
      <div id='event_photos' style='display: none;'>
        
        {lang_javascript ids=182,183,184,185,3000165}
        
        <div>
         <div class='event_headline' style='float: left;'>{lang_print id=3000164} (<span id='event_{$event->event_info.event_id}_totalfiles'>{$eventalbum_info.eventalbum_totalfiles|default:0}</span>)</div>
          {if $allowed_to_upload}
            <div style='float: right; padding-left: 10px;'>
              <a href="javascript:TB_show(SocialEngine.Language.Translate(3000165), 'user_event_upload.php?event_id={$event->event_info.event_id}&TB_iframe=true&height=300&width=500', '', './images/trans.gif');">
                <img src='./images/icons/event_addimages16.gif' border='0' class='button' style='float: left;' />
                {lang_print id=3000165}
              </a>
              <div style='clear: both; height: 0px;'></div>
            </div>
          {/if}
          <div style='clear: both; height: 0px;'></div>
        </div>
        
        {* FILES *}
        <div id="event_{$event->event_info.event_id}_nofiles" style='display: none;'>
          <img src='./images/icons/bulb16.gif' border='0' class='icon' />
          {lang_print id=3000166}
        </div>
        <div id="event_{$event->event_info.event_id}_files" style='margin-left: auto; margin-right: auto;'></div>
        
        
        <script type="text/javascript" src="./include/js/class_event_files.js"></script>      
        
        <script type="text/javascript">
          
          SocialEngine.EventFiles = new SocialEngineAPI.EventFiles({ldelim}
            'paginate' : true,
            'cpp' : 18,

            'event_id' : {$event->event_info.event_id},
            'event_dir' : '{$event->event_dir($event->event_info.event_id)}',
            
            'ajaxURL' : SocialEngine.URL.url_base + 'event_ajax.php'
          {rdelim});
          
          SocialEngine.RegisterModule(SocialEngine.EventFiles);
          
        </script>
        
      </div>
      {* END PHOTOS TAB *}
      
      
      {* COMMENTS TAB - SHOW EVENT COMMENTS *}
      <div id='event_comments' style='display: none;'>
        
        <div id="event_{$event->event_info.event_id}_postcomment"></div>
        <div id="event_{$event->event_info.event_id}_comments" style='margin-left: auto; margin-right: auto;'></div>
        
        {lang_javascript ids=39,155,175,182,183,184,185,187,784,787,829,830,831,832,833,834,835,854,856,891,1025,1026,1032,1034,1071}
        
        <script type="text/javascript">
          
          SocialEngine.EventComments = new SocialEngineAPI.Comments({ldelim}
            'canComment' : {if $allowed_to_comment}true{else}false{/if},
            'commentHTML' : '{$setting.setting_comment_html|replace:",":", "}',
            'commentCode' : {if $setting.setting_comment_code}true{else}false{/if},
            
            'type' : 'event',
            'typeIdentifier' : 'event_id',
            'typeID' : {$event->event_info.event_id},
            
            'typeTab' : 'events',
            'typeCol' : 'event',
            
            'initialTotal' : {$event->event_info.event_totalcomments|default:0},
            
            'paginate' : false,
            'cpp' : 5,
            
            'object_owner' : 'event',
            'object_owner_id' : '{$event->event_info.event_id}',
            
            'ajaxURL' : SocialEngine.URL.url_base + 'misc_js.php'
            
          {rdelim});
          
          SocialEngine.RegisterModule(SocialEngine.EventComments);
          
          // Backwards
          function addComment(is_error, comment_body, comment_date)
          {ldelim}
            SocialEngine.EventComments.addComment(is_error, comment_body, comment_date);
          {rdelim}
          
          function getComments(direction)
          {ldelim}
            SocialEngine.EventComments.getComments(direction);
          {rdelim}
          
        </script>
        
      </div>
      {* END COMMENTS TAB *}
      
      
      {* PLUGIN TABS *}
      {foreach from=$global_plugins key=plugin_k item=plugin_v}
        {if !empty($plugin_v.menu_event_tab)}
          <div id='event_{$plugin_k}' style='display: none;'>
            {include file=$plugin_v.menu_event_tab.file}
          </div>
        {/if} 
      {/foreach}
      {* END PLUGIN TABS *}
      
    </div>
    
  {/if}
  {* END PRIVACY IF STATEMENT *}


{* END RIGHT COLUMN *}
</td></tr></table>

{include file='footer.tpl'}