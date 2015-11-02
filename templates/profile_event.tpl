
{* $Id: profile_event.tpl 9 2009-01-11 06:03:21Z john $ *}

{* BEGIN EVENTS *}
<table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
  <tr>
    <td class='header'>{lang_print id=3000244}</td>
  </tr>
  <tr>
    <td class='profile'>
      <iframe name='calendarframe' id='calendarframe' src="{$url->url_base}/profile_event_calendar.php?user={$owner->user_info.user_username}" width="100%" height='100%' scrolling='no' frameborder='0' style="height:175px;"></iframe>
    </td>
  </tr>
</table>

<p id='show_events' style='display: none;'></p>
{literal}
<script type='text/javascript'>
<!--
function se_popup(id1, height) {
  table = parent.frames['calendarframe'].document.getElementById(id1);
  $('show_events').innerHTML = '<table cellspacing="0" cellpadding="0" id="profile_event_popup" class="profile_event_popup">'+table.innerHTML+'</table>';
  TB_show('', '#TB_inline?height='+height+'&width=560&inlineId=profile_event_popup', '', './images/trans.gif');
}
//-->
</script>
{/literal}
{* END EVENTS *}
