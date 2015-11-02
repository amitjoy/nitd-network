{include file='header.tpl'}

{* $Id: user_event.tpl 9 2009-01-11 06:03:21Z john $ *}

<img src='./images/icons/event_event48.gif' border='0' class='icon_big' style="margin-bottom: 15px;">
<div class='page_header'>{lang_print id=3000086}</div>
<div>
  {lang_sprintf id=3000087 1='browse_events.php'}
</div>
<br />


{* SHOW BUTTONS *}
<div style='margin-top: 25px; margin-bottom:10px;'>
  {if $user->level_info.level_event_allow & 4}
  <div class='button' style='float: left; padding-right: 20px;'>
    <a href='user_event_add.php'><img src='./images/icons/event_newevent16.gif' border='0' class='button' />{lang_print id=3000088}</a>
  </div>
  {/if}
  <div class='button' style='float: left; padding-right: 20px;'>
    <a href="javascript:void(0);" onclick="$('event_search').style.display = ( $('event_search').style.display=='block' ? 'none' : 'block');"><img src='./images/icons/search16.gif' border='0' class='button' />{lang_print id=3000089}</a>
  </div>
  <div style='clear: both; height: 0px;'></div>
</div>


{* SEARCH FIELD *}
<div id='event_search' class="seEventSearch"{if empty($search)} style='display: none;'{/if}>
  <div style='padding: 10px;'>
    <form action='user_event.php' name='searchform' method='post'>
    <table cellpadding='0' cellspacing='0' align='center'>
      <tr>
        <td><b>{lang_print id=3000218}</b>&nbsp;&nbsp;</td>
        <td><input type='text' name='search' maxlength='100' size='30' value='{$search}' />&nbsp;</td>
        <td>{lang_block id=646 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}</td>
      </tr>
    </table>
    <input type='hidden' name='s' value='{$s}' />
    <input type='hidden' name='p' value='{$p}' />
    <input type='hidden' name='view' value='list' />
    </form>
  </div>
</div>


{* SHOW VIEWS *}
<div style='margin-top: 20px;'>
  {lang_print id=3000090}
  <a href="user_event.php?view=list">{lang_print id=3000091}</a> |
  <a href="user_event.php?view=month">{lang_print id=3000092}</a>
</div>


{* JAVASCRIPT *}
{lang_javascript ids=861,3000080,3000081,3000082,3000083,3000084,3000085,3000093,3000097,3000153,3000154,3000219}
<script type="text/javascript" src="./include/js/class_event.js"></script>
<script type="text/javascript">
  
  SocialEngine.Event = new SocialEngineAPI.Event();
  SocialEngine.RegisterModule(SocialEngine.Event);
  
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


{* HIDDEN DIV TO DISPLAY DELETE CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmeventdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=3000094}
  </div>
  <br />
  {lang_block id=175 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.SocialEngine.Event.deleteConfirm();' />{/lang_block}
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


{* HIDDEN DIV TO DISPLAY RSVP MESSAGE *}
<div style='display: none;' id='confirmeventrsvp'>
  <div style='margin: 10px 0px 10px 0px;'>
    {lang_print id=3000098}
  </div>
  <div>
    <a href="javascript:void(0);" onclick="parent.SocialEngine.Event.rsvpConfirm(1);">{lang_print id=3000099}</a><br />
    <a href="javascript:void(0);" onclick="parent.SocialEngine.Event.rsvpConfirm(2);">{lang_print id=3000100}</a><br />
    <a href="javascript:void(0);" onclick="parent.SocialEngine.Event.rsvpConfirm(3);">{lang_print id=3000101}</a><br />
  </div>
</div>



{* VIEW - CALENDAR - MONTH *}
{if $view=="month"}

  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td align='center'>
        
        <div class='event_title'><a href='user_event.php?view={$view}&date={$date_last}'>&#171;</a> {$month}, {$year} <a href='user_event.php?view={$view}&date={$date_next}'>&#187;</a></div>
        
        {include file='event_calendar.tpl' calendar_view=$view}
        
      </td>
    </tr>
  </table>


{* VIEW - LIST *}
{elseif $view=="list"}
  
  
  {* DISPLAY MESSAGE IF NO EVENTS *}
  <div id="seClassifiedNullMessage"{if $total_events} style="display: none;"{/if}>
    <table cellpadding='0' cellspacing='0' align='center'>
      <tr>
        <td class='result'>
          {if !empty($search) || !($user->level_info.level_event_allow & 4)}
            <img src='./images/icons/bulb16.gif' border='0' class='icon' />
            {lang_print id=3000103}
          {else}
            <img src='./images/icons/bulb16.gif' border='0' class='icon' />
            {lang_sprintf id=3000102 1='user_event_add.php'}
          {/if}
        </td>
      </tr>
    </table>
  </div>
  
  
  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='center'>
      {if $p != 1}
        <a href='user_event.php?view=list&search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>
      {else}
        <font class='disabled'>&#171; {lang_print id=182}</font>
      {/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_events} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_events} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}
        <a href='user_event.php?view=list&search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
      {else}
        <font class='disabled'>{lang_print id=183} &#187;</font>
      {/if}
    </div>
    <br />
  {/if}
  
  
  {* DISPLAY EVENT LISTINGS *}
  {section name=event_loop loop=$events}
  <div id='seEvent_{$events[event_loop].event->event_info.event_id}' class="seEvent {cycle values='seEvent1,seEvent2'}">

    <table cellpadding='0' cellspacing='0' width='100%'>
      <tr>
        <td class='seEventLeft' width='1'>
          <div class='seEventPhoto' style='width: 140px;'>
            <table cellpadding='0' cellspacing='0' width='140'>
              <tr>
                <td>
                  <a href='{$url->url_create("event", $user->user_info.user_username, $events[event_loop].event->event_info.event_id)}'>
                    <img src='{$events[event_loop].event->event_photo("./images/nophoto.gif")}' border='0' width='{$misc->photo_size($events[event_loop].event->event_photo("./images/nophoto.gif"),"140","140","w")}' />
                  </a>
                </td>
              </tr>
            </table>
          </div>
        </td>
        <td class='seEventRight' width='100%'>
        
          {* SHOW EVENT TITLE *}
          <div class='seEventTitle'>
            <a href='{$url->url_create("event", $user->user_info.user_username, $events[event_loop].event->event_info.event_id)}'>
              {if !$events[event_loop].event->event_info.event_title}<i>{lang_print id=589}</i>{else}{$events[event_loop].event->event_info.event_title|truncate:70:"...":false|choptext:40:"<br />"}{/if}
            </a>
          </div>
          
          {* SHOW EVENT CATEGORY *}
          {if !empty($events[event_loop].event->event_info.main_category_title)}
          <div class='seEventCategory'>
            {lang_print id=3000104}
            {* SHOW PARENT CATEGORY *}
            {if !empty($events[event_loop].event->event_info.parent_category_title)}
              <a href="browse_events.php?eventcat_id={$events[event_loop].event->event_info.parent_category_id}">{lang_print id=$events[event_loop].event->event_info.parent_category_title}</a>
              -
            {/if}
            <a href="browse_events.php?eventcat_id={$events[event_loop].event->event_info.main_category_id}">{lang_print id=$events[event_loop].event->event_info.main_category_title}</a>
          </div>
          {/if}
          
          {* SHOW EVENT STATS *}
          <div class='seEventStats'>
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
          
          {* SHOW EVENT RSVP *}
          <div class='seEventStats'>
            {lang_print id=3000277}
            
            <span class="seEventStatusAccept"{if $events[event_loop].event->is_member} style="display:none;"{/if}>
              <a href='javascript:void(0);' onclick="SocialEngine.Event.removeShow({$events[event_loop].event->event_info.event_id});">
                {lang_print id=$events[event_loop].event_rsvp_lvid}
              </a>
            </span>
            
            <span class="seEventStatusRSVP"{if !$events[event_loop].event->is_member} style="display:none;"{/if}>
              <a href='javascript:void(0);' onclick="SocialEngine.Event.rsvpShow({$events[event_loop].event->event_info.event_id});" id="seEventRSVP_{$events[event_loop].event->event_info.event_id}">
                {lang_print id=$events[event_loop].event_rsvp_lvid}
              </a>
            </span>
          </div>
          
          {* SHOW EVENT DESCRIPTION *}
          <div class='seEventBody' style='margin-top: 8px; margin-bottom: 8px;'>
            {$events[event_loop].event->event_info.event_desc|strip_tags|truncate:197:"...":true}
          </div>
          
          {* SHOW EVENT OPTIONS *}
          <div class='seEventOptions'>
            
            {* JOIN *}
            <div class="seEventOption1 seEventUserOptionJoin"{if !$events[event_loop].event->is_member_waiting || !$events[event_loop].event_approved} style="display:none;"{/if}>
              <a href='javascript:void(0);' onclick="SocialEngine.Event.join({$events[event_loop].event->event_info.event_id});">
                <img src='./images/icons/event_join16.gif' border='0' class='button' />
                {lang_print id=3000168}
              </a>
            </div>
            
            {* CANCEL REQUEST *}
            <div class="seEventOption1 seEventUserOptionRequestCancel"{if !$events[event_loop].event->is_member_waiting || $events[event_loop].event_approved} style="display:none;"{/if}>
              <a href='javascript:void(0);' onclick="SocialEngine.Event.cancelShow({$events[event_loop].event->event_info.event_id});">
                <img src='./images/icons/event_remove16.gif' border='0' class='button' />
                {lang_print id=3000170}
              </a>
            </div>
            
            {* EDIT *}
            <div class="seEventOption1 seEventUserOptionEdit"{if $events[event_loop].event->user_rank!=3} style="display:none;"{/if}>
              <a href='user_event_edit.php?event_id={$events[event_loop].event->event_info.event_id}'>
                <img src='./images/icons/event_edit16.gif' border='0' class='button' />
                {lang_print id=3000245}
              </a>
            </div>
            
            {* RSVP *}
            <div class="seEventOption1 seEventUserOptionRsvp"{if !$events[event_loop].event->is_member} style="display:none;"{/if}>
              <a href='javascript:void(0);' onclick="SocialEngine.Event.rsvpShow({$events[event_loop].event->event_info.event_id});">
                <img src='./images/icons/event_rsvp16.gif' border='0' class='button' />
                {lang_print id=3000097}
              </a>
            </div>
            
            {* LEAVE *}
            <div class="seEventOption1 seEventUserOptionLeave"{if $events[event_loop].event->user_rank==3 || !$events[event_loop].event->is_member} style="display:none;"{/if}>
              <a href='javascript:void(0);' onclick="SocialEngine.Event.leaveShow({$events[event_loop].event->event_info.event_id});">
                <img src='./images/icons/event_remove16.gif' border='0' class='button' />
                {lang_print id=3000219}
              </a>
            </div>
            
            {* DELETE *}
            <div class="seEventOption1 seEventUserOptionDelete"{if $events[event_loop].event->user_rank!=3} style="display:none;"{/if}>
              <a href='javascript:void(0);' onclick="SocialEngine.Event.deleteShow({$events[event_loop].event->event_info.event_id});">
                <img src='./images/icons/event_delete16.gif' border='0' class='button' />
                {lang_print id=3000169}
              </a>
            </div>
            
          </div>
        </td>
      </tr>
    </table>
    
  </div>
  {/section}

  <div style='clear: both; height: 0px;'></div>
  
  
  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='center'>
      {if $p != 1}
        <a href='user_event.php?view=list&search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>
      {else}
        <font class='disabled'>&#171; {lang_print id=182}</font>
      {/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_events} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_events} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}
        <a href='user_event.php?view=list&search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
      {else}
        <font class='disabled'>{lang_print id=183} &#187;</font>
      {/if}
    </div>
    <br />
  {/if}
  
  
{/if}

{include file='footer.tpl'}