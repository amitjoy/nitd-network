{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article.php'>{lang_print id=11152001}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_article_settings.php'>{lang_print id=11152004}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='articles.php'>{lang_print id=11152003}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<img src='./images/icons/article48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=11152005}</div>
<div>{lang_print id=11152006}</div>

<br>

{* SHOW SUCCESS MESSAGE *}
{if $result != 0}
  <table cellpadding='0' cellspacing='0'><tr><td class='result'>
  <div class='success'><img src='./images/success.gif' border='0' class='icon'> {lang_print id=11152007}</div>
  </td></tr></table>
{/if}

<br>

<form action='user_article_settings.php' method='POST'>

<div><b>{lang_print id=11152009}</b></div>
<div class='form_desc'>{lang_print id=11152010}</div>

{* ONLY DISPLAY IF USER CAN CREATE GROUPS *}
{if $user->level_info.level_article_allow != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='checkbox' value='1' id='articlecomment' name='usersetting_notify_articlecomment'{if $user->usersetting_info.usersetting_notify_articlecomment == 1} CHECKED{/if}></td><td><label for='articlecomment'>{lang_print id=11152012}</label></td></tr>
  </table>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='checkbox' value='1' id='articlemediacomment' name='usersetting_notify_articlemediacomment'{if $user->usersetting_info.usersetting_notify_articlemediacomment == 1} CHECKED{/if}></td><td><label for='articlemediacomment'>{lang_print id=11152013}</label></td></tr>
  </table>
{/if}

<br>

<input type='submit' class='button' value='{lang_print id=11152008}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='footer.tpl'}