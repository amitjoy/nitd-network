{include file='header.tpl'}

{* $Id: user_blog_settings.tpl 5 2009-01-11 06:01:16Z john $ *}

<table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
    <td valign='top'>
      
      <img src='./images/icons/blog_blog48.gif' border='0' class='icon_big'>
      <div class='page_header'>{lang_print id=1500001}</div>
      <div>{lang_print id=1500069}</div>
      
    </td>
    <td valign='top' align='right'>
      
      <table cellpadding='0' cellspacing='0' width='130'>
      <tr><td class='button' nowrap='nowrap'><a href='user_blog.php'><img src='./images/icons/back16.gif' border='0' class='button'>{lang_print id=1500055}</a></td></tr>
      </table>
      
    </td>
  </tr>
</table>
<br />


{* SHOW SUCCESS MESSAGE *}
{if $result != 0}
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td class='result'>
        <div class='success'><img src='./images/success.gif' border='0' class='icon'> {lang_print id=191}</div>
      </td>
    </tr>
  </table>
  <br />
{/if}


<form action='user_blog_settings.php' method='POST'>

{if $user->level_info.level_blog_style && $user->level_info.level_blog_create}
  <div><b>{lang_print id=1500070}</b></div>
  <div class='form_desc'>{lang_print id=1500071}</div>
  <textarea name='style_blog' rows='17' cols='50' style='width: 100%; font-family: courier, serif;'>{$style_blog}</textarea>
  <br />
  <br />
{/if}




{* NOTIFICATION SETTINGS *}
<div><b>{lang_print id=1500073}</b></div>
<br />

{if $user->level_info.level_blog_create}

  {assign var="comment_options" value=$user->level_info.level_blog_comments|unserialize}
  {if !("0"|in_array:$comment_options) || $comment_options|@count != 1}
  <table cellpadding='0' cellspacing='0' class='editprofile_options'>
    <tr>
      <td><input type='checkbox' value='1' id='blogcomment' name='usersetting_notify_blogcomment'{if $user->usersetting_info.usersetting_notify_blogcomment} CHECKED{/if}></td>
      <td><label for='blogcomment'>{lang_print id=1500074}</label></td>
    </tr>
  </table>
  {/if}

  <table cellpadding='0' cellspacing='0' class='editprofile_options'>
    <tr>
      <td><input type='checkbox' value='1' id='blogtrackback' name='usersetting_notify_blogtrackback'{if $user->usersetting_info.usersetting_notify_blogtrackback} CHECKED{/if}></td>
      <td><label for='blogtrackback'>{lang_print id=1500075}</label></td>
    </tr>
  </table>

{/if}
{if $user->level_info.level_blog_view}

  <table cellpadding='0' cellspacing='0' class='editprofile_options'>
    <tr>
      <td><input type='checkbox' value='1' id='newblogsubscriptionentry' name='usersetting_notify_newblogsubscriptionentry'{if $user->usersetting_info.usersetting_notify_newblogsubscriptionentry} CHECKED{/if}></td>
      <td><label for='newblogsubscriptionentry'>{lang_print id=1500076}</label></td>
    </tr>
  </table>

{/if}

<br />


{lang_block id=173 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' />{/lang_block}
<input type='hidden' name='task' value='dosave' />
</form>

</td></tr></table>

{include file='footer.tpl'}