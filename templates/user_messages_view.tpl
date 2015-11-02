{include file='header.tpl'}

{* $Id: user_messages_view.tpl 194 2009-07-15 21:44:24Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab{if $b == 0}1{else}2{/if}' NOWRAP><a href='user_messages.php'>{lang_print id=780}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab{if $b == 0}2{else}1{/if}' NOWRAP><a href='user_messages_outbox.php'>{lang_print id=781}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<img src='./images/icons/messages48.gif' border='0' class='icon_big' />
<div class='page_header'>{$pmconvo_info.pmconvo_subject}</div>
{capture assign='collaborators'}{section name=coll_loop loop=$collaborators}<a href='{$url->url_create("profile", $collaborators[coll_loop]->user_info.user_username)}'>{$collaborators[coll_loop]->user_displayname}</a>{if $smarty.section.coll_loop.last != TRUE}, {/if}{/section}{/capture}
<div>{lang_sprintf id=801 1=$collaborators}</div>
<br />
<br />


{* JAVASCRIPT FOR AUTOGROWING TEXTAREA *}
{literal}
<script type="text/javascript">
<!--
  window.addEvent('domready', function() { textarea_autogrow('reply_body'); });
//-->
</script>
{/literal}


{* LOOP THROUGH MESSAGES IN THREAD *}
<table cellpadding='0' cellspacing='0' width='100%'>
{section name=pm_loop loop=$pms}
  <tr>
  <td class='messages_view1' width='1'>
    <a href='{$url->url_create("profile",$pms[pm_loop].author->user_info.user_username)}'><img class='photo' src='{$pms[pm_loop].author->user_photo("./images/nophoto.gif", TRUE)}' width='60' height='60' border='0'></a>
    {if $smarty.section.pm_loop.last}<a name='bottom'></a>{/if}
  </td>
  <td class='messages_authorbox' nowrap='nowrap'>
    <div class='messages_author'><a href='{$url->url_create("profile",$pms[pm_loop].author->user_info.user_username)}'>{$pms[pm_loop].author->user_displayname|truncate:20:"...":true}</a></div>
    <div class='messages_date'>{$datetime->cdate("`$setting.setting_timeformat` `$setting.setting_dateformat`", $datetime->timezone($pms[pm_loop].pm_date, $global_timezone))}</div>
  </td>
  <td class='messages_view2'>{$pms[pm_loop].pm_body|choptext:75:"<br>"}</td>
  </tr>
  <tr><td colspan='3'>&nbsp;</td></tr>
{/section}

{* DISPLAY REPLY TO ALL BOX *}
<tr>
<td colspan='2'>&nbsp;</td>
<td class='messages_view2_bottom'>
  <a name='reply'></a>
  <div id='reply_error' style='display: none;'>{lang_print id=796}</div>
  {if $blockerror}<div id='reply_error2' style='display: {$blockerror};'>{lang_print id=1321}</div>{/if}
  <form action='user_messages_view.php#bottom' method='POST' onSubmit="{literal}if(this.reply.value.replace(/ /g, '') == '') { $('reply_error').style.display='block'; return false; } else { return true; }{/literal}">
  {if $collaborators|@count == 1}{lang_print id=802}{else}{lang_print id=803}{/if}<br>
  <textarea name='reply' id='reply_body' rows='3' cols='60' style='margin-bottom: 5px; width: 100%;'></textarea>
  <br>
  <input type='submit' class='button' value='{lang_print id=791}'>

  {* SHOW BACK TO INBOX *}
  {if $b == 0}
    <input type='button' class='button' value='{lang_print id=805}' onClick="window.location.href='user_messages.php';">
  {* SHOW BACK TO OUTBOX *}
  {else}
    <input type='button' class='button' value='{lang_print id=806}' onClick="window.location.href='user_messages_outbox.php';">
  {/if}

  <input type='hidden' name='task' value='reply'>
  <input type='hidden' name='pmconvo_id' value='{$pmconvo_info.pmconvo_id}'>
  </form>

  <div style='padding-top: 15px;'><a href='user_messages_view.php?pmconvo_id={$pmconvo_info.pmconvo_id}&task=delete'>{lang_print id=1181}</a></div>

</td>
</tr>

</table>



{include file='footer.tpl'}