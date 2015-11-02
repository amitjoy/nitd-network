{include file='header.tpl'}

{* $Id: lostpass.tpl 133 2009-03-22 20:16:35Z john $ *}

<div class='page_header'>{lang_print id=33}</div>

{lang_print id=34}
<br />
<br />

{* SHOW SUCCESS MESSAGE IF NO ERROR *}
{if $submitted == 1 AND $is_error == 0}

  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td class='result'>
        <div class='success'>
          <img src='./images/success.gif' border='0' class='icon' />
          {lang_print id=35}
        </div>
      </td>
    </tr>
  </table>

{else}

  {if $is_error != 0}{lang_print id=$is_error}{/if}
 
  <form action='lostpass.php' method='post'>
  <table cellpadding='0' cellspacing='0' class='form'>
    <tr>
      <td class='form1'>{lang_print id=37}</td>
      <td class='form2'><input type='text' class='text' name='user_email' maxlength='70' size='40' /></td>
    </tr>
    <tr>
      <td class='form1'>&nbsp;</td>
      <td class='form2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td>
              <input type='submit' class='button' value='{lang_print id=749}' />&nbsp;
              <input type='hidden' name='task' value='send_email' />
              </form>
            </td>
            <td>
              <form action='login.php' method='POST'>
              <input type='submit' class='button' value='{lang_print id=39}' />
              </form>
            </td>
          </tr>
        </table>
        </form>
      </td>
    </tr>
  </table>

{/if}

{include file='footer.tpl'}