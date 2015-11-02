{include file='header.tpl'}

{* $Id: signup_verify.tpl 8 2009-01-11 06:02:53Z john $ *}

<div class='page_header'>{lang_print id=1045}</div>

{* DISPLAY ERROR *}
{if $is_error != 0}
  <div class='error'><img src='./images/error.gif' border='0' class='icon'>{lang_print id=$is_error}</div>


{* DISPLAY RESEND *}
{elseif $resend == 1}
  {if $result != 0}
    <div class='success'><img src='./images/success.gif' border='0' class='icon'> {lang_print id=$result}</div>
  {else}
    <div>{lang_print id=1043}</div>
    <br>
    <form action='signup_verify.php' method='POST'>
    {lang_print id=1015}<br><input type='text' class='text' name='resend_email' size='40' maxlength='70'>
    <br><br>
    <input type='submit' class='button' value='{lang_print id=1016}'>
    <input type='hidden' name='task' value='resend_do'>
    </form>
  {/if}


{* DISPLAY SUCCESSFUL VERIFICATION *}
{else}
  <div class='success'><img src='./images/success.gif' border='0' class='icon'> {lang_sprintf id=$result 1=$old_subnet_name 2=$new_subnet_name}</div>
  <br>
  <form action='login.php' method='GET'>
  <input type='submit' class='button' value='{lang_print id=1017}'>
  </form>
{/if}

{include file='footer.tpl'}