{include file='header.tpl'}

{* $Id: help_contact.tpl 8 2009-01-11 06:02:53Z john $ *}

<img src='./images/icons/help48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=754}</div>
<div>{lang_print id=1035}</div>
<br />

{* SHOW SUCCESS MESSAGE *}
{if $result != 0}
  <br />
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='success'><img src='./images/success.gif' border='0' class='icon'>{lang_print id=$result}</td>
  </tr>
  </table>
{/if}

{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
  <br />
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='error'><img src='./images/error.gif' border='0' class='icon'>{lang_print id=$is_error}</td>
  </tr>
  </table>
{/if}

<br>

{* SHOW FORM IF NOT ALREADY SUBMITTED *}
{if $success == 0}
  <form action='help_contact.php' method='POST'>
  <table cellpadding='0' cellspacing='0' class='form'>
  <tr>
  <td class='form1'>{lang_print id=258}:</td>
  <td class='form2'><input type='text' class='text' name='contact_name' maxlength='50' size='30' value='{$contact_name}'></td>
  </tr>
  <tr>
  <td class='form1'>{lang_print id=37}</td>
  <td class='form2'><input type='text' class='text' name='contact_email' maxlength='70' size='30' value='{$contact_email}'></td>
  </tr>
  <tr>
  <td class='form1'>{lang_print id=520}:</td>
  <td class='form2'><input type='text' class='text' name='contact_subject' maxlength='50' size='30' value='{$contact_subject}'></td>
  </tr>
  <tr>
  <td class='form1'>{lang_print id=521}</td>
  <td class='form2'><textarea name='contact_message' rows='7' cols='60'>{$contact_message}</textarea></td>
  </tr>
  {if !empty($setting.setting_contact_code)}
  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'>
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td><input type='text' name='contact_secure' class='text' size='6' maxlength='10'>&nbsp;</td>
      <td>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td align='center'>
              <img src='./images/secure.php' id='secure_image' border='0' height='20' width='67' class='signup_code' /><br />
              <a href="javascript:void(0);" onClick="$('secure_image').src = './images/secure.php?' + (new Date()).getTime();">{lang_print id=975}</a>
            </td>
            <td>{lang_print id=691 assign=tip}<img src='./images/icons/tip.gif' border='0' class='Tips1' title='{$tip|escape:quotes}'></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </td>
  </tr>
  {/if}
  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'><input type='submit' class='button' value='{lang_print id=839}'></td>
  </tr>
  </table>
  
  
  <input type='hidden' name='task' value='dosend' />
  </form>
{/if}

{include file='footer.tpl'}