{include file='header.tpl'}

{* $Id: user_event_edit_settings.tpl 9 2009-01-11 06:03:21Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
  <tr>
    <td class='tab0'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_event_edit.php?event_id={$event->event_info.event_id}'>{lang_print id=3000137}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_event_edit_members.php?event_id={$event->event_info.event_id}'>{lang_print id=3000138}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab1' NOWRAP><a href='user_event_edit_settings.php?event_id={$event->event_info.event_id}'>{lang_print id=3000001}</a></td><td class='tab'>&nbsp;</td>
    <td class="tab3">&nbsp;</td>
  </tr>
</table>

<table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
    <td valign='top'>
      
      <img src='./images/icons/event_edit48.gif' border='0' class='icon_big'>
      <div class='page_header'>{lang_sprintf id=3000156 1="event.php?event_id=`$event->event_info.event_id`" 2=$event->event_info.event_title}</div>
      <div style="width: 500px;">{lang_print id=3000157}</div>
      
    </td>
    <td valign='top' align='right'>
      
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td class='button' nowrap='nowrap'>
            <a href='user_event.php'>
              <img src='./images/icons/back16.gif' border='0' class='button' />
              {lang_print id=3000109}
            </a>
          </td>
        </tr>
      </table>
      
    </td>
  </tr>
</table>
<br />


{* SHOW SUCCESS MESSAGE *}
{if $result}
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td class='result'>
      <img src='./images/success.gif' border='0' class='icon' />
      {lang_print id=191}
      </td>
    </tr>
  </table>
  <br />
{/if}



<form action='user_event_edit_settings.php?event_id={$event->event_info.event_id}' method='POST'>

{* EVENT STYLE *}
{if $user->level_info.level_event_style}
<div><b>{lang_print id=3000158}</b></div>
<div class="form_desc">{lang_print id=3000159}</div>
<textarea name='style_event' rows='17' cols='50' style='width: 100%; font-family: courier, serif;'>{$style_event}</textarea>
<br />
<br />
{/if}


{* EVENT INVITE *}
{if $user->level_info.level_event_inviteonly}
<div><b>{lang_print id=3000117}</b></div>
<div class="form_desc">{lang_print id=3000118}</div>
<table cellpadding='0' cellspacing='0'>
  <tr>
    <td><input type='radio' name='event_inviteonly' id='event_inviteonly_0' value='0'{if !$event->event_info.event_inviteonly} checked{/if} /></td>
    <td><label for='event_inviteonly_0'>{lang_print id=3000119}</label></td>
  </tr>
  <tr>
    <td><input type='radio' name='event_inviteonly' id='event_inviteonly_1' value='1'{if  $event->event_info.event_inviteonly} checked{/if} /></td>
    <td><label for='event_inviteonly_1'>{lang_print id=3000120}</label></td>
  </tr>
</table>
<br />
<br />
{/if}


{* EVENT SEARCH *}
{if $user->level_info.level_event_search}
<div><b>{lang_print id=3000121}</b></div>
<div class="form_desc">{lang_print id=3000122}</div>
<table cellpadding='0' cellspacing='0'>
  <tr>
    <td><input type='radio' name='event_search' id='event_search_1' value='1'{if  $event->event_info.event_search} checked{/if} /></td>
    <td><label for='event_search_1'>{lang_print id=3000123}</label></td>
  </tr>
  <tr>
    <td><input type='radio' name='event_search' id='event_search_0' value='0'{if !$event->event_info.event_search} checked{/if} /></td>
    <td><label for='event_search_0'>{lang_print id=3000124}</label></td>
  </tr>
</table>
<br />
<br />
{/if}


{* EVENT INVITE *}
{* if $user->level_info.level_event_invite *}
<div><b>{lang_print id=3000267}</b></div>
<div class="form_desc">{lang_print id=3000268}</div>
<table cellpadding='0' cellspacing='0'>
  <tr>
    <td><input type='radio' name='event_invite' id='event_invite_1' value='1'{if  $event->event_info.event_invite} checked{/if} /></td>
    <td><label for='event_invite_1'>{lang_print id=3000265}</label></td>
  </tr>
  <tr>
    <td><input type='radio' name='event_invite' id='event_invite_0' value='0'{if !$event->event_info.event_invite} checked{/if} /></td>
    <td><label for='event_invite_0'>{lang_print id=3000266}</label></td>
  </tr>
</table>
<br />
<br />
{* /if *}


{* EVENT PRIVACY *}
{if $privacy_options|@count > 1}
<div><b>{lang_print id=3000125}</b></div>
<div class="form_desc">{lang_print id=3000126}</div>
<table cellpadding='0' cellspacing='0'>
{foreach from=$privacy_options name=privacy_loop key=k item=v}
  <tr>
    <td><input type='radio' name='event_privacy' id='privacy_{$k}' value='{$k}'{if $event->event_info.event_privacy == $k} checked{/if} /></td>
    <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
  </tr>
{/foreach}
</table>
<br />
<br />
{/if}


{* EVENT COMMENTS *}
{if $comment_options|@count > 1}
<div><b>{lang_print id=3000127}</b></div>
<div class="form_desc">{lang_print id=3000128}</div>
<table cellpadding='0' cellspacing='0'>
{foreach from=$comment_options name=comment_loop key=k item=v}
  <tr>
    <td><input type='radio' name='event_comments' id='comment_{$k}' value='{$k}'{if $event->event_info.event_comments == $k} checked{/if} /></td>
    <td><label for='comment_{$k}'>{lang_print id=$v}</label></td>
  </tr>
{/foreach}
</table>
<br />
<br />
{/if}


{* EVENT UPLOAD *}
{if $upload_options|@count > 1}
<div><b>{lang_print id=3000129}</b></div>
<div class="form_desc">{lang_print id=3000130}</div>
<table cellpadding='0' cellspacing='0'>
{foreach from=$upload_options name=upload_loop key=k item=v}
  <tr>
    <td><input type='radio' name='event_upload' id='event_upload_{$k}' value='{$k}'{if $event->event_info.event_upload == $k} checked{/if} /></td>
    <td><label for='event_upload_{$k}'>{lang_print id=$v}</label></td>
  </tr>
{/foreach}
</table>
<br />
<br />
{/if}


{* EVENT TAG *}
{if $tag_options|@count > 1}
<div><b>{lang_print id=3000131}</b></div>
<div class="form_desc">{lang_print id=3000132}</div>
<table cellpadding='0' cellspacing='0'>
{foreach from=$tag_options name=tag_loop key=k item=v}
  <tr>
    <td><input type='radio' name='event_tag' id='event_tag_{$k}' value='{$k}'{if $event->event_info.event_tag == $k} checked{/if} /></td>
    <td><label for='event_tag_{$k}'>{lang_print id=$v}</label></td>
  </tr>
{/foreach}
</table>
<br />
<br />
{/if}




{* SHOW SUBMIT BUTTONS *}
<table cellpadding='0' cellspacing='0'>
  <tr>
    <td>
      {lang_block id=173 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />&nbsp;{/lang_block}
      <input type='hidden' name='task' value='dosave' />
      </form>
    </td>
    <td>
      <form action='user_event_edit_settings.php?event_id={$event->event_info.event_id}' method='GET'>
      {lang_block id=39 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
      </form>
    </td>
  </tr>
</table>
<br />


{include file='footer.tpl'}