{include file='header.tpl'}

{* $Id: browse_events.tpl 131 2009-03-22 00:54:31Z john $ *}

<div class='page_header'>
  {if $eventcat == ""}
    {lang_print id=3000205}
  {else}
    <a href='browse_events.php'>{lang_print id=3000205}</a> >
    {if $eventsubcat == ""}
      {lang_print id=$eventcat.eventcat_title}
    {else}
      <a href='browse_events.php?v={$v}&s={$s}&eventcat_id={$eventcat.eventcat_id}'>{lang_print id=$eventcat.eventcat_title}</a> >
      {lang_print id=$eventsubcat.eventcat_title}
    {/if}
  {/if}
</div>

<table cellpadding='0' cellspacing='0' width='100%' style='margin-top: 10px;'>
<tr>
<td style='width: 200px; vertical-align: top;'>

  <div style='padding: 10px; background: #F2F2F2; border: 1px solid #BBBBBB; font-weight: bold;'>

    <div style='text-align: center; line-height: 12pt;'>
      <table cellpadding='0' cellspacing='0' align='center'>
      <tr>
      <td>{lang_print id=3000090}&nbsp;</td>
      <td>
        <select class='event_small' name='v' onchange="window.location.href='browse_events.php?c={$c}&s={$s}&v='+this.options[this.selectedIndex].value;">
        <option value='1'{if $v == "1"} SELECTED{/if}>{lang_print id=3000206}</option>
        {if $user->user_exists}<option value='2'{if $v == "2"} SELECTED{/if}>{lang_print id=3000207}</option>{/if}
        <option value='3'{if $v == "3" || empty($v)} SELECTED{/if}>{lang_print id=3000210}</option>
        </select>
      </td>
      </tr>
      </table>
    </div>

    <div style='text-align: center; line-height: 12pt; margin-top: 5px;'>
      <table cellpadding='0' cellspacing='0' align='center'>
      <tr>
      <td>{lang_print id=3000208}&nbsp;</td>
      <td>
        <select class='event_small' name='s' onchange="window.location.href='browse_events.php?c={$c}&v={$v}&s='+this.options[this.selectedIndex].value;">
        <option value='event_totalmembers DESC'{if $s == "event_totalmembers DESC"} SELECTED{/if}>{lang_print id=3000209}</option>
        <option value='event_date_start ASC'{if $s == "event_date_start ASC" || empty($s)} SELECTED{/if}>{lang_print id=3000211} (AZ)</option>
        <option value='event_date_start DESC'{if $s == "event_date_start DESC"} SELECTED{/if}>{lang_print id=3000211} (ZA)</option>
        <option value='event_date_end ASC'{if $s == "event_date_end ASC"} SELECTED{/if}>{lang_print id=3000212} (AZ)</option>
        <option value='event_date_end DESC'{if $s == "event_date_end DESC"} SELECTED{/if}>{lang_print id=3000212} (ZA)</option>
        </select>
      </td>
      </tr>
      </table>
    </div>

  </div>

  {* CATEGORY JAVASCRIPT *}
  {literal}
  <script type="text/javascript">
  <!-- 

  // ADD ABILITY TO MINIMIZE/MAXIMIZE CATS
  var cat_minimized = new Hash.Cookie('cat_cookie', {duration: 3600});

  //-->
  </script>
  {/literal}


  <div style='margin-top: 10px; padding: 5px; background: #F2F2F2; border: 1px solid #BBBBBB; margin: 10px 0px 10px 0px; font-weight: bold;'>

    <div style='padding: 5px 8px 5px 8px; border: 1px solid #DDDDDD; background: #FFFFFF;'>
      <a href='browse_events.php?s={$s}&v={$v}'>{lang_print id=3000213}</a>
    </div>
    {section name=cat_loop loop=$cats}

      {* CATEGORY JAVASCRIPT *}
      {literal}
      <script type="text/javascript">
      <!-- 
        window.addEvent('domready', function() { 
          if(cat_minimized.get({/literal}{$cats[cat_loop].cat_id}{literal}) == 1) {
	    $('subcats_{/literal}{$cats[cat_loop].cat_id}{literal}').style.display = '';
	    $('icon_{/literal}{$cats[cat_loop].cat_id}{literal}').src = './images/icons/minus16.gif';
	  }
	});
      //-->
      </script>
      {/literal}

      <div style='padding: 5px 8px 5px 8px; border: 1px solid #DDDDDD; border-top: none; background: #FFFFFF;'>
        <img id='icon_{$cats[cat_loop].cat_id}' src='./images/icons/{if $cats[cat_loop].subcats|@count > 0 && $cats[cat_loop].subcats != ""}plus16{else}minus16_disabled{/if}.gif' {if $cats[cat_loop].subcats|@count > 0 && $cats[cat_loop].subcats != ""}style='cursor: pointer;' onClick="if($('subcats_{$cats[cat_loop].cat_id}').style.display == 'none') {literal}{{/literal} $('subcats_{$cats[cat_loop].cat_id}').style.display = ''; this.src='./images/icons/minus16.gif'; cat_minimized.set({$cats[cat_loop].cat_id}, 1); {literal}} else {{/literal} $('subcats_{$cats[cat_loop].cat_id}').style.display = 'none'; this.src='./images/icons/plus16.gif'; cat_minimized.set({$cats[cat_loop].cat_id}, 0); {literal}}{/literal}"{/if} border='0' class='icon'><a href='browse_events.php?s={$s}&v={$v}&eventcat_id={$cats[cat_loop].cat_id}'>{lang_print id=$cats[cat_loop].cat_title}</a>
        <div id='subcats_{$cats[cat_loop].cat_id}' style='display: none;'>
          {section name=subcat_loop loop=$cats[cat_loop].subcats}
            <div style='font-weight: normal;'><img src='./images/trans.gif' border='0' class='icon' style='width: 16px;'><a href='browse_events.php?s={$s}&v={$v}&eventcat_id={$cats[cat_loop].subcats[subcat_loop].subcat_id}'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].subcat_title}</a></div>
          {/section}
        </div>
      </div>
    {/section}
  </div>

</td>
<td style='vertical-align: top; padding-left: 10px;'>

  {* NO EVENTS AT ALL *}
  {if $events|@count == 0}
    <br>
    <table cellpadding='0' cellspacing='0' align='center'>
      <tr>
        <td class='result'>
          <img src='./images/icons/bulb16.gif' border='0' class='icon' />
          {lang_print id=3000214}
        </td>
      </tr>
    </table>
  {/if}

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='event_pages_top'>
    {if $p != 1}<a href='browse_events.php?s={$s}&v={$v}&eventcat_id={$eventcat_id}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
    &nbsp;|&nbsp;&nbsp;
    {if $p_start == $p_end}
      <b>{lang_sprintf id=184 1=$p_start 2=$total_events}</b>
    {else}
      <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_events}</b>
    {/if}
    &nbsp;&nbsp;|&nbsp;
    {if $p != $maxpage}<a href='browse_events.php?s={$s}&v={$v}&eventcat_id={$eventcat_id}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
    </div>
  {/if}

  {section name=event_loop loop=$events}
    <div style='padding: 10px; border: 1px solid #CCCCCC; margin-bottom: 10px;'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td>
        <a href='{$url->url_create("event", $smarty.const.NULL, $events[event_loop].event->event_info.event_id)}'>
          <img class='photo' src='{$events[event_loop].event->event_photo("./images/nophoto.gif", TRUE)}' border='0' width='60' height='60' />
        </a>
      </td>
      <td style='vertical-align: top; padding-left: 10px;'>
        <div style='font-weight: bold; font-size: 10pt;'>
          <a href='event.php?event_id={$events[event_loop].event->event_info.event_id}'>
            {$events[event_loop].event->event_info.event_title}
          </a>
        </div>
        <div style='color: #777777; font-size: 7pt; margin-bottom: 2px;'>
          {assign var=event_date_start value=$datetime->timezone($events[event_loop].event->event_info.event_date_start, $global_timezone)}
          {assign var=event_date_end value=$datetime->timezone($events[event_loop].event->event_info.event_date_end, $global_timezone)}
          
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
        <div style='color: #777777; font-size: 7pt; margin-bottom: 5px;'>
          {assign var='event_dateupdated' value=$datetime->time_since($events[event_loop].event->event_info.event_dateupdated)}{capture assign="updated"}{lang_sprintf id=$event_dateupdated[0] 1=$event_dateupdated[1]}{/capture}
          {lang_sprintf id=3000215 1=$events[event_loop].event->event_info.event_totalmembers} - 
          {lang_sprintf id=3000216 1=$events[event_loop].event_creator->user_displayname 2=$url->url_create("profile", $events[event_loop].event_creator->user_info.user_username)} - 
          {lang_sprintf id=3000217 1=$updated}
        </div>
        <div>
          {$events[event_loop].event->event_info.event_desc|strip_tags|truncate:300:"...":true}
        </div>
      </td>
      </tr>
      </table>
    </div>
  {/section}

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='event_pages_bottom'>
    {if $p != 1}<a href='browse_events.php?s={$s}&v={$v}&eventcat_id={$eventcat_id}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
    &nbsp;|&nbsp;&nbsp;
    {if $p_start == $p_end}
      <b>{lang_sprintf id=184 1=$p_start 2=$total_events}</b>
    {else}
      <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_events}</b>
    {/if}
    &nbsp;&nbsp;|&nbsp;
    {if $p != $maxpage}<a href='browse_events.php?s={$s}&v={$v}&eventcat_id={$eventcat_id}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
    </div>
  {/if}

</td>
</tr>
</table>







{include file='footer.tpl'}
