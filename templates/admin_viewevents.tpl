{include file='admin_header.tpl'}

{* $Id: admin_viewevents.tpl 9 2009-01-11 06:03:21Z john $ *}

<h2>{lang_print id=3000002}</h2>
{lang_print id=3000073}
<br />
<br />

<form action='admin_viewevents.php' method='post'>
<table cellpadding='0' cellspacing='0' width='400' align='center'>
  <tr>
    <td align='center'>
      <div class='box'>
        <table cellpadding='0' cellspacing='0' align='center'>
          <tr>
            <td>
              {lang_print id=3000074}<br />
              <input type='text' class='text' name='f_title' value='{$f_title}' size='15' maxlength='100' />
              &nbsp;
            </td>
            <td>
              {lang_print id=3000075}<br />
              <input type='text' class='text' name='f_owner' value='{$f_owner}' size='15' maxlength='50'>
              &nbsp;
            </td>
            <td>
              {lang_block id=1002 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
            </td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>
<input type='hidden' name='s' value='{$s}' />
</form>
<br />


{if !$total_events}

  <table cellpadding='0' cellspacing='0' width='400' align='center'>
    <tr>
      <td align='center'>
        <div class='box' style='width: 300px;'><b>{lang_print id=3000079}</b></div>
      </td>
    </tr>
  </table>
  <br />

{else}

  {* JAVASCRIPT FOR CHECK ALL *}
  {literal}
  <script language='JavaScript'> 
  <!---
  var checkboxcount = 1;
  function doCheckAll() {
    if(checkboxcount == 0) {
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = false;
      }}
      checkboxcount = checkboxcount + 1;
      }
    } else
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = true;
      }}
      checkboxcount = checkboxcount - 1;
      }
  }
  // -->
  </script>
  {/literal}

  <div class='pages'>
    {lang_sprintf id=3000076 1=$total_events} &nbsp;|&nbsp; {lang_print id=1005}
    {section name=page_loop loop=$pages}
      {if $pages[page_loop].link}
        {$pages[page_loop].page}
      {else}
        <a href='admin_viewevents.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>
      {/if}
    {/section}
  </div>
  
  <form action='admin_viewevents.php' method='post' name='items'>
  <table cellpadding='0' cellspacing='0' class='list'>
    <tr>
      <td class='header' width='10'><input type='checkbox' name='select_all' onClick='javascript:doCheckAll()'></td>
      <td class='header' width='10' style='padding-left: 0px;'><a class='header' href='admin_viewevents.php?s={$i}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=87}</a></td>
      <td class='header'><a class='header' href='admin_viewevents.php?s={$t}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=3000074}</a></td>
      <td class='header'><a class='header' href='admin_viewevents.php?s={$o}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=3000075}</a></td>
      <td class='header' width='150'><a class='header' href='admin_viewevents.php?s={$d}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=88}</a></td>
      <td class='header' width='100'>{lang_print id=153}</td>
    </tr>
    
    {section name=event_loop loop=$events}
    <tr class='{cycle values="background1,background2"}'>
      <td class='item' style='padding-right: 0px;'>
        <input type='checkbox' name='delete_events[]' value='{$events[event_loop].event->event_info.event_id}' />
      </td>
      <td class='item' style='padding-left: 0px;'>
        {$events[event_loop].event->event_info.event_id}
      </td>
      <td class='item'>
        {$events[event_loop].event->event_info.event_title}
      </td>
      <td class='item'>
        <a href='{$url->url_create("profile", $events[event_loop].event_creator->user_info.user_username)}' target='_blank'>{$events[event_loop].event_creator->user_displayname}</a>
      </td>
      <td class='item'>
        {assign var=event_date_start value=$datetime->timezone($events[event_loop].event->event_info.event_date_start, $global_timezone)}
        {$datetime->cdate("`$setting.setting_dateformat` `$event51` `$setting.setting_timeformat`", $event_date_start)}
      </td>
      <td class='item'>
        [ <a href='admin_loginasuser.php?user_id={$events[event_loop].event->event_info.event_user_id}&url={$url->url_encode("`$url->url_base`event.php?event_id=`$events[event_loop].event->event_info.event_id`")}' target='_blank'>{lang_print id=3000077}</a> ]
        [ <a href='javascript:void(0);' onClick="if(confirm('{lang_print id=3000078}')) {literal}{{/literal} location.href='admin_viewevents.php?task=deleteevents&delete_events[]={$events[event_loop].event->event_info.event_id}&s={$s}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'; {literal}}{/literal}">{lang_print id=155}</a> ]
      </td>
    </tr>
    {/section}
  </table>
  <br />

  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td>
        {lang_block id=788 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
      </td>
      <td align='right' valign='top'>
        <div class='pages2'>
          {lang_print id=3000076 1=$total_events}
          &nbsp;|&nbsp;
          {lang_print id=1005}
          {section name=page_loop loop=$pages}
            {if $pages[page_loop].link}
              {$pages[page_loop].page}
            {else}
              <a href='admin_viewevents.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>
            {/if}
          {/section}
        </div>
      </td>
    </tr>
  </table>
  
  <input type='hidden' name='task' value='deleteevents' />
  <input type='hidden' name='s' value='{$s}' />
  <input type='hidden' name='p' value='{$p}' />
  <input type='hidden' name='f_title' value='{$f_title}' />
  <input type='hidden' name='f_owner' value='{$f_owner}' />
  </form>

{/if}

{include file='admin_footer.tpl'}