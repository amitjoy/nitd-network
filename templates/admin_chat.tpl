{include file='admin_header.tpl'}

{* $Id: admin_chat.tpl 6 2009-01-11 06:01:29Z john $ *}

<h2>{lang_print id=3500003}</h2>
{lang_print id=3501002}
<br />
<br />

{if $result == 1}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{elseif $result == 2}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_sprintf id=3501004 1=$chatuser_kicked}</div>
{/if}



<form action='admin_chat.php' method='post'>

<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=3501023}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=3501024}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='2' cellspacing='0'>
        <tr>
          <td>
            <input type='radio' name='setting_chat_enabled' id='setting_chat_enabled_1' value='1'{if $setting.setting_chat_enabled} CHECKED{/if} />
          </td>
          <td>
            <label for='setting_chat_enabled_1'>{lang_print id=3501025}</label>
          </td>
        </tr>
        <tr>
          <td>
            <input type='radio' name='setting_chat_enabled' id='setting_chat_enabled_0' value='0'{if !$setting.setting_chat_enabled} CHECKED{/if} />
          </td>
          <td>
            <label for='setting_chat_enabled_0'>{lang_print id=3501026}</label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
    <td class='setting1'>
      {lang_print id=3501027}
    </td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='2' cellspacing='0'><tr><td>
        <input type='radio' name='setting_im_enabled' id='setting_im_enabled_1' value='1'{if $setting.setting_im_enabled} CHECKED{/if} />
      </td><td>
        <label for='setting_im_enabled_1'>{lang_print id=3501028}</label>
      </td></tr>
      <tr><td>
        <input type='radio' name='setting_im_enabled' id='setting_im_enabled_0' value='0'{if !$setting.setting_im_enabled} CHECKED{/if} />
      </td><td>
        <label for='setting_im_enabled_0'>{lang_print id=3501029}</label></td>
      </tr></table>
    </td>
  </tr>
</table>
<br />



<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=3501012}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=3501013}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <input type="text" class="text" name="setting_chat_update" value="{$setting.setting_chat_update|default:2}" />
      <label>{lang_print id=3501011}</label>
    </td>
  </tr>
</table>
<br />



<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=3501015}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=3501016}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='2' cellspacing='0'>
        <tr>
          <td><input type='radio' name='setting_chat_showphotos' id='setting_chat_showphotos_1' value='1'{if  $setting.setting_chat_showphotos} checked{/if}></td>
          <td><label for='setting_chat_showphotos_1'>{lang_print id=3501017}</label></td>
        </tr>
        <tr>
          <td><input type='radio' name='setting_chat_showphotos' id='setting_chat_showphotos_0' value='0'{if !$setting.setting_chat_showphotos} checked{/if}></td>
          <td><label for='setting_chat_showphotos_0'>{lang_print id=3501018}</label></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />



<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=3501005}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=3501006}</td>
  </tr>
  <tr>
    <td class='setting2'>
      {section name=chatusers_loop loop=$chatusers}
        <a href='admin_chat.php?task=kick&chatuser_id={$chatusers[chatusers_loop].chatuser_id}'>{$chatusers[chatusers_loop].chatuser_user_username}</a>
        {if $smarty.section.chatusers_loop.last != true}, {/if} 
      {sectionelse}
        {lang_print id=3501007}
      {/section}
    </td>
  </tr>
</table>
<br />



<table cellpadding='0' cellspacing='0' width='600'><tr><td class='header'>
  {lang_print id=3501019}
</td></tr>
<tr><td class='setting1'>
  {lang_print id=3501020}
</td></tr>
<tr><td class='setting2'>
  <textarea name='chatbanned' cols='40' rows='3' style='width: 100%;'>{section name=chatbanned_loop loop=$chatbanned}{$chatbanned[chatbanned_loop].chatbanned_user_username}{if $smarty.section.chatbanned_loop.last != true}, {/if}{/section}</textarea>
</td></tr></table>
<br />


<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=3501030}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=3501031}</td>
  </tr>
  <tr>
    <td class='setting2'><input type='text' class='text' name='setting_im_html' value='{$setting.setting_im_html}' maxlength='250' size='60' /></td>
  </tr>
</table>
<br />


<input type='hidden' name='task' value='dosave' />
<input type='submit' class='button' value='{lang_print id=173}' />
</form>


{include file='admin_footer.tpl'}