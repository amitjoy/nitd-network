{include file='header.tpl'}

{* $Id: user_messages_outbox.tpl 8 2009-01-11 06:02:53Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_messages.php'>{lang_print id=780}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_messages_outbox.php'>{lang_print id=781}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<table cellpadding='0' cellspacing='0'>
<tr>
<td class='messages_left'>
  <img src='./images/icons/messages48.gif' border='0' class='icon_big'>
  <div class='page_header'>{lang_print id=797}</div>
  <div>{lang_sprintf id=798 1=$total_pms}</div>
</td>
<td class='messages_right'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='button' nowrap='nowrap'>
    <img src='./images/icons/sendmessage16.gif' border='0' class='icon' /><a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?TB_iframe=true&height=400&width=450', '', './images/trans.gif');">{lang_print id=784}</a>
  </td></tr></table>
</td>
</tr>
</table>

<br />

{* JAVASCRIPT FOR CHECK ALL MESSAGES FEATURE *}
{literal}
  <script language='JavaScript'> 
  <!---
  var checkboxcount = 1;
  function doCheckAll() {
    if(checkboxcount == 0) {
      with (document.messageform) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = false;
      }}
      checkboxcount = checkboxcount + 1;
      }
    } else
      with (document.messageform) {
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


{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div class='center'>
  {if $p != 1}<a href='user_messages_outbox.php?p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
  {if $p_start == $p_end}
    &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_pms} &nbsp;|&nbsp; 
  {else}
    &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_pms} &nbsp;|&nbsp; 
  {/if}
  {if $p != $maxpage}<a href='user_messages_outbox.php?p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
  </div>
<br>
{/if}


{* CHECK IF THERE ARE NO MESSAGES IN OUTBOX *}
{if $total_pms == 0}

  <div class='center'>
    <table cellpadding='0' cellspacing='0'><tr>
    <td class='result'><img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=799}</td>
    </tr></table>
  </div>


{* DISPLAY MESSAGES *}
{else}

  <form action='user_messages_outbox.php' method='post' name='messageform'>
  <table class='messages_table' cellpadding='0' cellspacing='0'>
  <tr>
  <td class='messages_header'><a href='javascript:void(0);' onClick='doCheckAll();this.blur();'><img src='./images/icons/checkall16.gif' border='0' style='margin-left: 3px;'></a></td>
  <td class='messages_header'>{lang_print id=790}</td>
  <td class='messages_header'>&nbsp;</td>
  <td class='messages_header' colspan='2'>{lang_print id=520}</td>
  </tr>
  {* LIST SENT MESSAGES *}
  {section name=pm_loop loop=$pms}
    <tr class='messages_read'>
    <td class='messages_message' width='1'><input type='checkbox' name='delete_convos[]' value='{$pms[pm_loop].pmconvo_id}'></td>
    <td class='messages_photo' width='1'><a href='{$url->url_create('profile', $pms[pm_loop].pm_user->user_info.user_username)}'><img class='photo' src='{$pms[pm_loop].pm_user->user_photo('./images/nophoto.gif', TRUE)}' border='0' class='photo' width='60' height='60' alt="{lang_sprintf id=786 1=$pms[pm_loop].pm_user->user_displayname_short}"></a></td>
    <td class='messages_message' width='130' nowrap='nowrap'>
      <b><a href='{$url->url_create('profile', $pms[pm_loop].pm_user->user_info.user_username)}'>{if $pms[pm_loop].pm_recipients == 1}{$pms[pm_loop].pm_user->user_displayname}{else}{lang_sprintf id=800 1=$pms[pm_loop].pm_recipients}{/if}</a></b>
      <div class='messages_date'>{$datetime->cdate("`$setting.setting_timeformat` `$setting.setting_dateformat`", $datetime->timezone($pms[pm_loop].pm_date, $global_timezone))}</div>
    </td>
    <td class='messages_message' width='100%'><b><a href='user_messages_view.php?b=1&pmconvo_id={$pms[pm_loop].pmconvo_id}#bottom'>{$pms[pm_loop].pmconvo_subject|truncate:50}</b><br>{$pms[pm_loop].pm_body|truncate:150|choptext:75:"<br>"}</a></td>
    <td class='messages_message' align='right' nowrap='nowrap'>[ <a href='user_messages_view.php?b=1&pmconvo_id={$pms[pm_loop].pmconvo_id}&task=delete'>{lang_print id=155}</a> ]</td>
    </tr>
  {/section}
  </table>
  
  <br>

  <input type='submit' class='button' value='{lang_print id=788}'>
  <input type='hidden' name='task' value='deleteselected'>
  <input type='hidden' name='p' value='{$p}'>
  </form>

{/if}

{include file='footer.tpl'}