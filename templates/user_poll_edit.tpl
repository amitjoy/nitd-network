{include file='header.tpl'}

{* $Id: user_poll_edit.tpl 12 2009-01-11 06:04:12Z john $ *}

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td valign='top'>

  <img src='./images/icons/poll_poll48.gif' border='0' class='icon_big'>
  <div class='page_header'>{lang_print id=2500057}</div>
  <div>{lang_print id=2500058}</div>

</td>
<td valign='top' align='right'>

  <table cellpadding='0' cellspacing='0' width='130'>
  <tr><td class='button' nowrap='nowrap'><a href='user_poll.php'><img src='./images/icons/back16.gif' border='0' class='button'>{lang_print id=2500070}</a></td></tr>
  </table>

</td>
</tr>
</table>
<br />


{* SHOW ERROR MESSAGE *}
{if !empty($is_error)}
  <table cellpadding='0' cellspacing='0'><tr>
    <td class='error'><img src='./images/error.gif' border='0' class='icon'>{lang_print id=$is_error}</td>
  </tr></table>
  <br />
{/if}


<form action='user_poll_edit.php' method='post'>

<table cellpadding='0' cellspacing='0'>
  <tr>
    <td class='form1'>{lang_print id=2500059}</td>
    <td class='form2'><input type='text' name='poll_title' class='text' maxlength='200' value='{$poll_title}' style='width: 300px;'></td>
  </tr>
  <tr>
    <td class='form1'>{lang_print id=2500060}</td>
    <td class='form2'>
      <textarea name='poll_desc' rows='5' cols='50' class='text' style='width: 300px;'>{$poll_desc}</textarea>
      <br>
      {* SHOW SETTINGS LINK IF NECESSARY *}
      {if $privacy_options|@count > 1 OR $comment_options|@count > 1}
        <div id='settings_show' class='poll_settings_link'>
          <a href="javascript:void(0);" onclick="javascript:$('entry_settings').style.display='block';$('settings_show').style.display='none';">{lang_print id=2500061}</a>
        </div>
      {/if}

      <div id='entry_settings' class='poll_settings' style='display: none; margin-top: 7px;'>
        {* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
        {if $search_polls == 1}
          <b>{lang_print id=2500062}</b>
          <table cellpadding='0' cellspacing='0'>
            <tr>
              <td><input type='radio' name='poll_search' id='poll_search_1' value='1'{if  $poll_search} CHECKED{/if}></td>
              <td><label for='poll_search_1'>{lang_print id=2500063}</label></td>
            </tr>
            <tr>
              <td><input type='radio' name='poll_search' id='poll_search_0' value='0'{if !$poll_search} CHECKED{/if}></td>
              <td><label for='poll_search_0'>{lang_print id=2500064}</label></td>
            </tr>
          </table>
        {/if}

        {* ADD SPACE IF BOTH OPTIONS ARE AVAILABLE *}
        {if $search_polls == 1 && ($privacy_options|@count > 1 OR $comment_options|@count > 1)}<br>{/if}

        {* SHOW PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
        {if $privacy_options|@count > 1}
          <b>{lang_print id=2500065}</b>
          <table cellpadding='0' cellspacing='0'>
          {foreach from=$privacy_options key=k item=v name=privacy_loop}
            <tr>
            <td><input type='radio' name='poll_privacy' id='privacy_{$k}' value='{$k}'{if $poll_privacy == $k} checked='checked'{/if}></td>
            <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
            </tr>
          {/foreach}
          </table>
        {/if}

        {* ADD SPACE IF BOTH OPTIONS ARE AVAILABLE *}
        {if $privacy_options|@count > 1 AND $comment_options|@count > 1}<br>{/if}

        {* SHOW COMMENT OPTIONS IF ALLOWED BY ADMIN *}
        {if $comment_options|@count > 1}
          <b>{lang_print id=2500066}</b>
          <table cellpadding='0' cellspacing='0'>
          {foreach from=$comment_options key=k item=v name=comment_loop}
            <tr>
            <td><input type='radio' name='poll_comments' id='comments_{$k}' value='{$k}'{if $poll_comments == $k} checked='checked'{/if}></td>
            <td><label for='comments_{$k}'>{lang_print id=$v}</label></td>
            </tr>
          {/foreach}
          </table>
        {/if}
      </div>
    </td>
  </tr>
  <tr>
    <td class='form1'>&nbsp;</td>
    <td class='form2'>
      
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td>
            {lang_block id=2500057 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}'>{/lang_block}&nbsp;
            <input type='hidden' name='task' value='doedit'>
            <input type='hidden' name='poll_id' value='{$poll_id}'>
            </form>
          </td>
          <td>
            <form action='user_poll.php' method='get'>
            {lang_block id=39 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}'>{/lang_block}
            </form>
          </td>
        </tr>
      </table>
      
    </td>
  </tr>
</table>

{include file='footer.tpl'}