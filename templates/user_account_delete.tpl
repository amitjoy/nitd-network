{include file='header.tpl'}

{* $Id: user_account_delete.tpl 115 2009-03-14 01:46:48Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
  <tr>
    <td class='tab0'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_account.php'>{lang_print id=655}</a></td>
    <td class='tab'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_account_privacy.php'>{lang_print id=1055}</a></td>
    <td class='tab'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_account_pass.php'>{lang_print id=756}</a></td>
    <td class='tab'>&nbsp;</td>
    <td class='tab1' NOWRAP><a href='user_account_delete.php'>{lang_print id=757}</a></td>
    <td class='tab3'>&nbsp;</td>
  </tr>
</table>

<img src='./images/icons/delete48.gif' border='0' class='icon_big' />
<div class='page_header'>{lang_print id=759}</div>
<div>{lang_print id=760}</div>
<br />

<table cellpadding='0' cellspacing='0'>
<tr>
<td>
  <input type='submit' class='button' value='{lang_print id=761}' onClick='SocialEngine.Viewer.userDelete();' />&nbsp;
</td>
<td>
  <form action='user_account.php' method='post'>
  <input type='submit' class='button' value='{lang_print id=39}' />
  </form>
</td>
</tr>
</table>


{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
{lang_javascript id=759}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=1146}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.SocialEngine.Viewer.userDeleteConfirm("{$delete_token}");' />
  <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();' />
</div>


{include file='footer.tpl'}