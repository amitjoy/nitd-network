
{* $Id: event_calendar.tpl 9 2009-01-11 06:03:21Z john $ *}

{if $calendar_view=="month_small"}
  
  <div style='text-align: center; padding-bottom: 5px;font-weight: bold;'>
    {if !isset($show_next_last) || $show_next_last}<a href='profile_event_calendar.php?user={$owner->user_info.user_username}&date={$date_last}'>&#171;</a>{/if}
    {$month}, {$year}
    {if !isset($show_next_last) || $show_next_last}<a href='profile_event_calendar.php?user={$owner->user_info.user_username}&date={$date_next}'>&#187;</a>{/if}
  </div>
  
  
  <table cellpadding='0' cellspacing='0' class='profile_events'>
    <tr>
      <td class='profile_events_cellheader'>{lang_print id=3000237}</td>
      <td class='profile_events_cellheader'>{lang_print id=3000238}</td>
      <td class='profile_events_cellheader'>{lang_print id=3000239}</td>
      <td class='profile_events_cellheader'>{lang_print id=3000240}</td>
      <td class='profile_events_cellheader'>{lang_print id=3000241}</td>
      <td class='profile_events_cellheader'>{lang_print id=3000242}</td>
      <td class='profile_events_cellheader'>{lang_print id=3000243}</td>
    </tr>
    
    {* SHOW DAYS OF MONTH *}
    {assign var='daycount' value=1}
    {section name=calendar loop=$total_cells}
    
    {* START A NEW ROW *}
    {cycle name="startrow" values="<tr>,,,,,,"}
    
    {* SHOW EMPTY CELLS BEFORE THE MONTH STARTS *}
    {if $smarty.section.calendar.index+1 < $first_day_of_month OR $smarty.section.calendar.index+1 > $last_day_of_month}
      <td class='profile_events_cellblank'>&nbsp;</td>
    
    {* SHOW A DAY *}
    {else}
      {assign value=$events.$daycount var=day_events}
      {if is_array($event_days)}{assign value=$daycount|in_array:$event_days var=is_event_day}{else}{assign value=0 var=is_event_day}{/if}
      <td class='profile_events_cell{if $is_event_day}4{elseif $today_day == $daycount AND $today_month == $date_current}3{elseif $day_events|@count != 0}2{else}1{/if}' align='center'>
        {if $day_events|@count == 0}
	  {$daycount}
	{else}
	  <a href='javascript:void(0)' onClick="parent.se_popup('day{$daycount}', '{math equation='x*200' x=$day_events|@count}');">{$daycount}</a>


	  <table id='day{$daycount}' cellpadding='0' cellspacing='0' class='profile_event_popup'>
          <tr>
          <td class='profile_event_transparent' colspan='3' style='height: 20px;'>&nbsp;</td>
          </tr>
          <tr>
          <td class='profile_event_transparent' style='width: 20px;'>&nbsp;</td>
          <td class='profile_event_popup2'>
          
            <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
            <td class='profile_event_popup_title'>{lang_sprintf id=3000276 1=$owner->user_displayname 2="`$month` `$daycount`"}</td>
            </tr>
            </table>
            
            {section name=event_loop loop=$day_events}
              
{* RI *}
<div id='seEvent_{$day_events[event_loop].event->event_info.event_id}' class="seEventMonth" style="margin-top: 20px;">

  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td class='seEventLeft' width='1'>
        <div class='seEventPhoto' style='width: 140px; height: 140px;'>
          <table cellpadding='0' cellspacing='0' width='140' height='140'>
            <tr>
              <td>
                <a href='{$url->url_create("event", $user->user_info.user_username, $day_events[event_loop].event->event_info.event_id)}'>
                  <img src='{$day_events[event_loop].event->event_photo("./images/nophoto.gif")}' border='0' width='{$misc->photo_size($day_events[event_loop].event->event_photo("./images/nophoto.gif"),"140","140","w")}' />
                </a>
              </td>
            </tr>
          </table>
        </div>
      </td>
      <td class='seEventRight' width='100%'>
      
        {* SHOW EVENT TITLE *}
        <div class='seEventTitle'>
          <a href='{$url->url_create("event", $user->user_info.user_username, $day_events[event_loop].event->event_info.event_id)}'>
            {if !$day_events[event_loop].event->event_info.event_title}<i>{lang_print id=589}</i>{else}{$day_events[event_loop].event->event_info.event_title|truncate:70:"...":false|choptext:40:"<br />"}{/if}
          </a>
        </div>
        
        {* SHOW EVENT CATEGORY *}
        {if !empty($day_events[event_loop].event->event_info.main_category_title)}
        <div class='seEventCategory'>
          {lang_print id=3000104}
          {* SHOW PARENT CATEGORY *}
          {if !empty($day_events[event_loop].event->event_info.parent_category_title)}
            <a href="browse_events.php?eventcat_id={$day_events[event_loop].event->event_info.parent_category_id}">{lang_print id=$day_events[event_loop].event->event_info.parent_category_title}</a>
            -
          {/if}
          <a href="browse_events.php?eventcat_id={$day_events[event_loop].event->event_info.main_category_id}">{lang_print id=$day_events[event_loop].event->event_info.main_category_title}</a>
        </div>
        {/if}
        
        {* SHOW EVENT STATS *}
        <div class='seEventStats'>
          {assign var=event_date_start value=$datetime->timezone($day_events[event_loop].event->event_info.event_date_start, $global_timezone)}
          {assign var=event_date_end value=$datetime->timezone($day_events[event_loop].event->event_info.event_date_end, $global_timezone)}
          
          {lang_print id=3000105}
          
          {* NO END DATE *}
          {if !$day_events[event_loop].event->event_info.event_date_end}
            {lang_sprintf id=3000203 1=$datetime->cdate($setting.setting_dateformat, $event_date_start) 2=$datetime->cdate($setting.setting_timeformat, $event_date_start)}
          
          {* SAME-DAY EVENT *}
          {elseif $datetime->cdate("F j, Y", $event_date_start)==$datetime->cdate("F j, Y", $event_date_end)}
            {lang_sprintf id=3000202 1=$datetime->cdate($setting.setting_dateformat, $event_date_start) 2=$datetime->cdate($setting.setting_timeformat, $event_date_start) 3=$datetime->cdate($setting.setting_timeformat, $event_date_end)}
          
          {* MULTI-DAY EVENT *}
          {else}
            {lang_sprintf id=3000204 1=$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $event_date_start) 2=$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $event_date_end)}
          {/if}
        </div>
        
        {* SHOW EVENT RSVP *}
        <div class='seEventStats'>
          {lang_print id=$day_events[event_loop].event_rsvp_lvid}
        </div>
        
        {* SHOW EVENT DESCRIPTION *}
        <div class='seEventBody' style='margin-top: 8px; margin-bottom: 8px;'>
          {$day_events[event_loop].event->event_info.event_desc|strip_tags|truncate:197:"...":true}
        </div>
      </td>
    </tr>
  </table>
  
</div>
{* RI *}
              
              {/section}
              
            </td>
            <td class='profile_event_transparent' style='width: 20px;'>&nbsp;</td>
          </tr>
          <tr>
            <td colspan='3' class='profile_event_transparent' style='height: 20px;'>&nbsp;</td>
          </tr>
        </table>
          
        {/if}
      </td>
      {assign var='daycount' value=$daycount+1}
    {/if}
    
    {* END THIS ROW *}
    {cycle name="endrow" values=",,,,,,</tr>"}
    
  {/section}

  </table>



{elseif $calendar_view=="month"}

  <table cellpadding='0' cellspacing='0' class='event_calendar'>
    <tr>
      <td class='event_cellheader'>{lang_print id=3000230}</td>
      <td class='event_cellheader'>{lang_print id=3000231}</td>
      <td class='event_cellheader'>{lang_print id=3000232}</td>
      <td class='event_cellheader'>{lang_print id=3000233}</td>
      <td class='event_cellheader'>{lang_print id=3000234}</td>
      <td class='event_cellheader'>{lang_print id=3000235}</td>
      <td class='event_cellheader'>{lang_print id=3000236}</td>
    </tr>
    
    {* SHOW DAYS OF MONTH *}
    {assign var='daycount' value=1}
    {section name=calendar loop=$total_cells}
    
    {* START A NEW ROW *}
    {cycle name="startrow" values="<tr>,,,,,,"}
    
    {* SHOW EMPTY CELLS BEFORE THE MONTH STARTS *}
    {if $smarty.section.calendar.index + 1<$first_day_of_month || $smarty.section.calendar.index + 1>$last_day_of_month}
      <td height='80' class='event_cellblank'>
        <div class='event_cellnum'>&nbsp;</div>
      </td>
    
    {* SHOW A DAY *}
    {else}
      {assign value=$events.$daycount var=day_events}
      <td height='80' id="event_cell{$daycount}" class='event_cell{if $today_month == $date_current AND $today_day == $daycount}3{elseif $day_events|@count != 0}2{else}1{/if}'>
      
        <table cellpadding='0' cellspacing='0' width='100%' height='100%'>
          <tr>
            <td class='event_celldesc'>
              
              {section name=event_loop loop=$day_events}
              <a href='javascript:void(0)' id="seEventMonthShow_{$day_events[event_loop].event->event_info.event_id}" title="{$daycount}" onClick="TB_show('', '#TB_inline?height=200&width=560&inlineId=event{$day_events[event_loop].event->event_info.event_id}', '', './images/trans.gif');">{$day_events[event_loop].event->event_info.event_title}</a><br>
              {if $day_events[event_loop].event->event_info.event_title != ""}
              <table id='event{$day_events[event_loop].event->event_info.event_id}' cellpadding='0' cellspacing='0' class='profile_event_popup'>
                <tr>
                  <td class='profile_event_transparent' colspan='3' style='height: 20px;'>&nbsp;</td>
                </tr>
                <tr>
                  <td class='profile_event_transparent' style='width: 20px;'>&nbsp;</td>
                  <td class='profile_event_popup2'>
{* RI *}
<div id='seEvent_{$day_events[event_loop].event->event_info.event_id}' class="seEventMonth">

  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td class='seEventLeft' width='1'>
        <div class='seEventPhoto' style='width: 140px; height: 140px;'>
          <table cellpadding='0' cellspacing='0' width='140' height='140'>
            <tr>
              <td>
                <a href='{$url->url_create("event", $user->user_info.user_username, $day_events[event_loop].event->event_info.event_id)}'>
                  <img src='{$day_events[event_loop].event->event_photo("./images/nophoto.gif")}' border='0' width='{$misc->photo_size($day_events[event_loop].event->event_photo("./images/nophoto.gif"),"140","140","w")}' />
                </a>
              </td>
            </tr>
          </table>
        </div>
      </td>
      <td class='seEventRight' width='100%'>
      
        {* SHOW EVENT TITLE *}
        <div class='seEventTitle'>
          <a href='{$url->url_create("event", $user->user_info.user_username, $day_events[event_loop].event->event_info.event_id)}'>
            {if !$day_events[event_loop].event->event_info.event_title}<i>{lang_print id=589}</i>{else}{$day_events[event_loop].event->event_info.event_title|truncate:70:"...":false|choptext:40:"<br />"}{/if}
          </a>
        </div>
        
        {* SHOW EVENT CATEGORY *}
        {if !empty($day_events[event_loop].event->event_info.main_category_title)}
        <div class='seEventCategory'>
          {lang_print id=3000104}
          {* SHOW PARENT CATEGORY *}
          {if !empty($day_events[event_loop].event->event_info.parent_category_title)}
            <a href="browse_events.php?eventcat_id={$day_events[event_loop].event->event_info.parent_category_id}">{lang_print id=$day_events[event_loop].event->event_info.parent_category_title}</a>
            -
          {/if}
          <a href="browse_events.php?eventcat_id={$day_events[event_loop].event->event_info.main_category_id}">{lang_print id=$day_events[event_loop].event->event_info.main_category_title}</a>
        </div>
        {/if}
        
        {* SHOW EVENT STATS *}
        <div class='seEventStats'>
          {assign var=event_date_start value=$datetime->timezone($day_events[event_loop].event->event_info.event_date_start, $global_timezone)}
          {assign var=event_date_end value=$datetime->timezone($day_events[event_loop].event->event_info.event_date_end, $global_timezone)}
          
          {lang_print id=3000105}
          
          {* NO END DATE *}
          {if !$day_events[event_loop].event->event_info.event_date_end}
            {lang_sprintf id=3000203 1=$datetime->cdate($setting.setting_dateformat, $event_date_start) 2=$datetime->cdate($setting.setting_timeformat, $event_date_start)}
          
          {* SAME-DAY EVENT *}
          {elseif $datetime->cdate("F j, Y", $event_date_start)==$datetime->cdate("F j, Y", $event_date_end)}
            {lang_sprintf id=3000202 1=$datetime->cdate($setting.setting_dateformat, $event_date_start) 2=$datetime->cdate($setting.setting_timeformat, $event_date_start) 3=$datetime->cdate($setting.setting_timeformat, $event_date_end)}
          
          {* MULTI-DAY EVENT *}
          {else}
            {lang_sprintf id=3000204 1=$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $event_date_start) 2=$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $event_date_end)}
          {/if}
        </div>
        
        {* SHOW EVENT RSVP *}
        <div class='seEventStats'>
          {lang_print id=3000277}
          
          <span class="seEventStatusAccept"{if $day_events[event_loop].event->is_member} style="display:none;"{/if}>
            <a href='javascript:void(0);' onclick="SocialEngine.Event.removeShow({$day_events[event_loop].event->event_info.event_id});">
              {lang_print id=$day_events[event_loop].event_rsvp_lvid}
            </a>
          </span>
          
          <span class="seEventStatusRSVP"{if !$day_events[event_loop].event->is_member} style="display:none;"{/if}>
            <a href='javascript:void(0);' onclick="SocialEngine.Event.rsvpShow({$day_events[event_loop].event->event_info.event_id});" id="seEventRSVP_{$day_events[event_loop].event->event_info.event_id}">
              {lang_print id=$day_events[event_loop].event_rsvp_lvid}
            </a>
          </span>
        </div>
        
        {* SHOW EVENT DESCRIPTION *}
        <div class='seEventBody' style='margin-top: 8px; margin-bottom: 8px;'>
          {$day_events[event_loop].event->event_info.event_desc|strip_tags|truncate:197:"...":true}
        </div>
        
        {* SHOW EVENT OPTIONS *}
        <div class='seEventOptions'>
          
          {* JOIN *}
          <div class="seEventOption1 seEventUserOptionJoin"{if !$day_events[event_loop].event->is_member_waiting || !$day_events[event_loop].event_approved} style="display:none;"{/if}>
            <a href='javascript:void(0);' onclick="parent.SocialEngine.Event.join({$day_events[event_loop].event->event_info.event_id});">
              <img src='./images/icons/event_join16.gif' border='0' class='button' />
              {lang_print id=3000168}
            </a>
          </div>
          
          {* CANCEL REQUEST *}
          <div class="seEventOption1 seEventUserOptionRequestCancel"{if !$day_events[event_loop].event->is_member_waiting || $day_events[event_loop].event_approved} style="display:none;"{/if}>
            <a href='javascript:void(0);' onclick="parent.SocialEngine.Event.cancelShow({$day_events[event_loop].event->event_info.event_id});">
              <img src='./images/icons/event_remove16.gif' border='0' class='button' />
              {lang_print id=3000170}
            </a>
          </div>
          
          {* EDIT *}
          <div class="seEventOption1 seEventUserOptionEdit"{if $day_events[event_loop].event->user_rank!=3} style="display:none;"{/if}>
            <a href='user_event_edit.php?event_id={$day_events[event_loop].event->event_info.event_id}'>
              <img src='./images/icons/event_edit16.gif' border='0' class='button' />
              {lang_print id=3000245}
            </a>
          </div>
          
          {* RSVP *}
          <div class="seEventOption1 seEventUserOptionRsvp"{if !$day_events[event_loop].event->is_member} style="display:none;"{/if}>
            <a href='javascript:void(0);' onclick="parent.SocialEngine.Event.rsvpShow({$day_events[event_loop].event->event_info.event_id});">
              <img src='./images/icons/event_rsvp16.gif' border='0' class='button' />
              {lang_print id=3000097}
            </a>
          </div>
          
          {* LEAVE *}
          <div class="seEventOption1 seEventUserOptionLeave"{if $day_events[event_loop].event->user_rank==3 || !$day_events[event_loop].event->is_member} style="display:none;"{/if}>
            <a href='javascript:void(0);' onclick="parent.SocialEngine.Event.leaveShow({$day_events[event_loop].event->event_info.event_id});">
              <img src='./images/icons/event_remove16.gif' border='0' class='button' />
              {lang_print id=3000219}
            </a>
          </div>
          
          {* DELETE *}
          <div class="seEventOption1 seEventUserOptionDelete"{if $day_events[event_loop].event->user_rank!=3} style="display:none;"{/if}>
            <a href='javascript:void(0);' onclick="parent.SocialEngine.Event.deleteShow({$day_events[event_loop].event->event_info.event_id});">
              <img src='./images/icons/event_delete16.gif' border='0' class='button' />
              {lang_print id=3000169}
            </a>
          </div>
          
        </div>
      </td>
    </tr>
  </table>
  
</div>
{* RI *}
                </td>
                <td class='profile_event_transparent' style='width: 20px;'>&nbsp;</td>
              </tr>
              <tr>
                <td colspan='3' class='profile_event_transparent' style='height: 20px;'>&nbsp;</td>
              </tr>
    	      </table>
            {/if}
            {/section}
            &nbsp;
            </td>
          </tr>
          <tr>
            <td class='event_cellnum{if $day_events|@count != 0}2{else}1{/if}'>{$daycount}</td>
          </tr>
        </table>
        
      </td>
    {assign var='daycount' value=$daycount+1}
    {/if}
    
    {* END THIS ROW *}
    {cycle name="endrow" values=",,,,,,</tr>"}
    
    {/section}
    
  </table>



{elseif $calendar_view=="day"}



{elseif $calendar_view=="year"}



{/if}





