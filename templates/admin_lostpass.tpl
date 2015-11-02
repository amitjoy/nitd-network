{include file='admin_header_global.tpl'}

{* $Id: admin_lostpass.tpl 235 2009-11-13 04:30:39Z phil $ *}


{literal}
<style type='text/css'>
html, body {
	height: 100% !important;
}
body {
	color: #666666;
	text-align: center;
	background-color: #EEEEEE;
	background-image: none;
}
td { 
	font-family: "Trebuchet MS", tahoma, verdana, serif;
	font-size: 12px;
}
td.box {
	border: 1px dashed #AAAAAA;
	padding: 15px;
	background: #FFFFFF;
	font-family: "Trebuchet MS", tahoma, verdana, serif;
	font-size: 12px;
}
td.login {
	font-family: "Trebuchet MS", tahoma, verdana, serif;
	font-size: 12px;
}
input.text {
	font-family: arial, tahoma, verdana, serif;
	font-size: 12px; 
}
div.error {
	text-align: center;
	padding-top: 3px;
	font-weight: bold;
}
input.button {
	font-family: arial, tahoma, verdana, serif;
	font-size: 12px;
	background: #DDDDDD;
	padding: 2px;
	font-weight: bold;
}
div.page_header {
	font-size: 19px;
}
td.success {
	font-weight: bold;
	padding: 7px 8px 7px 7px;
	background: #f3fff3; 
}
td.error {
	font-weight: bold;
	color: #FF0000;
	padding: 7px 8px 7px 7px;
	background: #FFF3F3;
}
</style>
{/literal}

<table cellpadding='0' cellspacing='0' style='width: 100%; height: 100%;'>
<tr>
<td>

  <table cellpadding='0' cellspacing='0' align='center' width='600'>
  <tr>
  <td class='box'>

    <div class='page_header'>{lang_print id=33}</div>
    {lang_print id=34}
    <br><br>

    {* SHOW SUCCESS MESSAGE IF NO ERROR *}
    {if $submitted == 1 AND $is_error == 0}

      <table cellpadding='0' cellspacing='0'>
      <tr><td class='success'>
        {lang_print id=35}
      </td></tr></table>
      <br>

    {else}

      {* SHOW ERROR MESSAGE *}
      {if $is_error == 1}
      <table cellpadding='0' cellspacing='0'>
      <tr><td class='error'>
        <img src='../images/error.gif' border='0' class='icon2'>{lang_print id=36}
      </td></tr></table>
      <br>
      {/if}
 
      <form action='admin_lostpass.php' method='post'>
      <table cellpadding='0' cellspacing='0' align='center'>
      <tr>
      <td>{lang_print id=37}&nbsp;</td>
      <td><input type='text' class='text' name='admin_email' maxlength='70' size='40'></td>
      </tr>
      <tr>
      <td>&nbsp;</td>
      <td>
        <table cellpadding='0' cellspacing='0' style='margin-top: 5px;'>
        <td valign='top'>
          <input type='submit' class='button' value='{lang_print id=38}'>&nbsp;
          <input type='hidden' name='task' value='send_email'>
          </form>
        </td>
        <td valign='top'>
          <form action='admin_login.php' method='post'>
          <input type='submit' class='button' value='{lang_print id=39}'>
          </form>
        </td>
        </tr>
        </table>
      </td>
      </tr>
      </table>

    {/if}

  </td>
  </tr>
  </table>
</td>
</tr>
</table>

</body>
</html>
