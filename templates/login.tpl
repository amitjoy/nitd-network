{include file='header.tpl'}

{* $Id: login.tpl 158 2009-04-09 01:19:50Z john $ *}

<div class='page_header'>{lang_print id=658}</div>

<br />

{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='error'><img src='./images/error.gif' border='0' class='icon'>{lang_print id=$is_error}</td></tr></table>
<br>
{/if}

<form action='login.php' method='POST' name='login'>
<table cellpadding='0' cellspacing='0' style='margin-left: 20px;'>
  <tr>
    <td class='form1'>{lang_print id=89}:</td>
    <td class='form2'><input type='text' class='text' name='email' id='email' value='{$email}' size='30' maxlength='70'></td>
  </tr>
  <tr>
    <td class='form1'>{lang_print id=29}:</td>
    <td class='form2'><input type='password' class='text' name='password' id='password' size='30' maxlength='50'></td>
  </tr>
  {if !empty($setting.setting_login_code) || (!empty($setting.setting_login_code_failedcount) && $failed_login_count>=$setting.setting_login_code_failedcount)}
  <tr>
    <td class='form1'>&nbsp;</td>
    <td class='form2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td><input type='text' name='login_secure' class='text' size='6' maxlength='10' />&nbsp;</td>
          <td>
            <table cellpadding='0' cellspacing='0'>
              <tr>
                <td align='center'>
                  <img src='./images/secure.php' id='secure_image' border='0' height='20' width='67' class='signup_code' /><br />
                  <a href="javascript:void(0);" onClick="$('secure_image').src = './images/secure.php?' + (new Date()).getTime();">{lang_print id=975}</a>
                </td>
                <td>{capture assign=tip}{lang_print id=691}{/capture}<img src='./images/icons/tip.gif' border='0' class='Tips1' title='{$tip|escape:quotes}'></td>
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
    <td class='form2'>
      <input type='submit' class='button' value='{lang_print id=30}' />&nbsp; 
      <input type='checkbox' class='checkbox' name='persistent' id='persistent' value='1'>
      <label for='persistent'>{lang_print id=660}</label>
      <br />
      <br />
      <img src='./images/icons/help16.gif' border='0' class='icon' />
      <a href='lostpass.php'>{lang_print id=675}</a>
      <noscript><input type='hidden' name='javascript_disabled' value='1' /></noscript>
      <input type='hidden' name='task' value='dologin' />
      <input type='hidden' name='return_url' value='{$return_url}' />
    </td>
  </tr>
</table>
</form>

{literal}
<script language="JavaScript">
<!--
window.addEvent('domready', function() {
	if($('email').value == "") {
	  $('email').focus();
	} else {
	  $('password').focus();
	}
});
// -->
</script>
{/literal}

{include file='footer.tpl'}