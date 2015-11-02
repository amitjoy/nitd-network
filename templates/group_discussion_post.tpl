{include file='header.tpl'}

{* $Id: group_discussion_post.tpl 34 2009-01-24 04:17:28Z john $ *}

<div class='page_header'>
  <a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}&v=discussions'>{$group->group_info.group_title}</a> 
  &#187; {lang_print id=2000258}
</div>

<br />

{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
<table cellpadding='0' cellspacing='0'>
  <tr>
    <td class='error'>
      <img src='./images/error.gif' border='0' class='icon' />
      {lang_print id=$is_error}
    </td>
  </tr>
</table>
<br />
{/if}

<form action='group_discussion_post.php' method='post'>

<table cellpadding='0' cellspacing='0'>

<tr>
<td class='form1'>{lang_print id=2000300}</td>
<td class='form2'><input type='text' class='text' name='topic_subject' value='{$topic_subject}' maxlength='50' size='40' /></td>
</tr>

<tr>
<td class='form1'>{lang_print id=2000301}</td>
<td class='form2'>
  <textarea name='topic_body' rows='5' cols='65'>{$topic_body}</textarea>
  {if $setting.setting_group_discussion_html != ""}
    <br>{lang_sprintf id=1034 1=$setting.setting_group_discussion_html|replace:",":", "}
  {/if}
</td>
</tr>


{if $setting.setting_group_discussion_code == 1}
  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'>
    <table cellspacing='0' cellpadding='0'>
    <tr>
      <td valign='top'>
        <img src='./images/secure.php' id='secure_image' border='0' height='20' width='67' class='signup_code' /><br />
        <a href="javascript:void(0);" onClick="javascript:$('secure_image').src = $('secure_image').src + '?' + (new Date()).getTime();">{lang_print id=975}</a>
      </td>
      <td style='padding-top: 4px;'><input type='text' name='comment_secure' id='comment_secure' class='text' size='6' maxlength='10'>&nbsp;</td>
      <td>
        {capture assign=tip}{lang_print id=691}{/capture}
        <img src='./images/icons/tip.gif' border='0' class='Tips1' title='{$tip|replace:quotes}' />
      </td>
    </tr>
    </table>
  </td>
  </tr>
{/if}

<tr>
<td class='form'>&nbsp;</td>
<td class='form2'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    <input type='submit' class='button' value='{lang_print id=2000302}' />&nbsp;
    <input type='hidden' name='task' value='topic_create' />
    <input type='hidden' name='group_id' value='{$group->group_info.group_id}' />
    </form>
  </td>
  <td>
    <form action='group.php' method='get'>
    <input type='submit' class='button' value='{lang_print id=39}' />
    <input type='hidden' name='group_id' value='{$group->group_info.group_id}' />
    <input type='hidden' name='v' value='discussions' />
    </form>
  </td>
  </tr>
  </table>
</td>
</tr>
</table>

{include file='footer.tpl'}