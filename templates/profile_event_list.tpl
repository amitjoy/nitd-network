
{* $Id: profile_event_list.tpl 9 2009-01-11 06:03:21Z john $ *}

{* BEGIN EVENTS *}
{if ($owner->level_info.level_event_allow & 6) && $total_events>0}

  <div class='profile_headline'>{lang_print id=3000007} ({$total_events})</div>
  <div>
    {* LOOP THROUGH FIRST 5 BLOG ENTRIES *}
    {section name=event_loop loop=$events max=5}
    <div class='profile_event_main'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td valign='top'>
            <a href='{$url->url_create("event", $owner->user_info.user_username, $events[event_loop].event->event_info.event_id)}'>
              <img src='./images/icons/event_event16.gif' border='0' class='icon' />
            </a>
          </td>
          <td valign='top'>
            <div class='profile_event_title'>
              <a href='{$url->url_create("event", $owner->user_info.user_username, $events[event_loop].event->event_info.event_id)}'>
                {$events[event_loop].event->event_info.event_title|truncate:35:"...":true}
              </a>
            </div>
            <div class='profile_event_date'>
              {assign var=event_date_start value=$datetime->timezone($events[event_loop].event->event_info.event_date_start, $global_timezone)}
              {assign var=event_date_end value=$datetime->timezone($events[event_loop].event->event_info.event_date_end, $global_timezone)}
              
              {lang_print id=3000105}
              
              {* NO END DATE *}
              {if !$events[event_loop].event->event_info.event_date_end}
                {lang_sprintf id=3000203 1=$datetime->cdate($setting.setting_dateformat, $event_date_start) 2=$datetime->cdate($setting.setting_timeformat, $event_date_start)}
              
              {* SAME-DAY EVENT *}
              {elseif $datetime->cdate("F j, Y", $event_date_start)==$datetime->cdate("F j, Y", $event_date_end)}
                {lang_sprintf id=3000202 1=$datetime->cdate($setting.setting_dateformat, $event_date_start) 2=$datetime->cdate($setting.setting_timeformat, $event_date_start) 3=$datetime->cdate($setting.setting_timeformat, $event_date_end)}
              
              {* MULTI-DAY EVENT *}
              {else}
                {lang_sprintf id=3000204 1=$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $event_date_start) 2=$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $event_date_end)}
              {/if}
            </div>
            <div class='profile_event_desc'>
              {$events[event_loop].event->event_info.event_desc|strip_tags|truncate:160:"...":true}
            </div>
          </td>
        </tr>
      </table>
    </div>
    {/section}
    {* IF MORE THAN 5 ENTRIES, SHOW VIEW MORE LINKS *}
    {*
    {if $total_events > 5}
    <div style='border-top: 1px solid #DDDDDD; padding-top: 10px;'>
      <div style='float: left;'>
        <a href='{$url->url_create("events", $owner->user_info.user_username)}'>
          <img src='./images/icons/event_event16.gif' border='0' class='button' style='float: left;' />
          {lang_print id=1500121}
        </a>
      </div>
      <div style='clear: both; height: 0px;'></div>
    </div>
    {/if}
    *}
  </div>
  
{/if}