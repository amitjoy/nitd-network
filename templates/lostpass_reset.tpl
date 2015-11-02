{include file='header.tpl'}

{* $Id: lostpass_reset.tpl 8 2009-01-11 06:02:53Z john $ *}

<div class='page_header'>{lang_print id=43}</div>

{if $valid == 1 && $submitted == 1}
  
  {lang_print id=751}

{elseif $valid == 1 && $submitted == 0}

  {lang_print id=45}

  <br />
  <br />

  {if $is_error != 0}{lang_print id=$is_error}{/if}

  <table cellpadding='0' cellspacing='0' class='form'>
  <form action='lostpass_reset.php' method='POST'>
  <tr>
  <td class='form1'>{lang_print id=46}:</td>
  <td class='form2'><input type='password' class='text' name='user_password' maxlength='50' size='40'></td>
  </tr>
  <tr>
  <td class='form1'>{lang_print id=47}</td>
  <td class='form2'><input type='password' class='text' name='user_password2' maxlength='50' size='40'></td>
  </tr>
  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'>
    <table cellpadding='0' cellspacing='0'>
    <td><input type='submit' class='button' value='{lang_print id=42}'>&nbsp;</td>
    <input type='hidden' name='task' value='reset'>
    <input type='hidden' name='r' value='{$r}'>
    <input type='hidden' name='user' value='{$owner->user_info.user_username}'>
    </form>
    <form action='login.php' method='POST'>
    <td><input type='submit' class='button' value='{lang_print id=39}'></td>
    </tr>
    </form>
    </table>
  </td>
  </tr>
  </form>
  </table>

{else}

  {lang_print id=$is_error}

{/if}

{include file='footer.tpl'}
