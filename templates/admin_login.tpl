{include file='admin_header_global.tpl'}

{* $Id: admin_login.tpl 235 2009-11-13 04:30:39Z phil $ *}


{literal}
<script type="text/javascript">
<!--
window.addEvent('domready', function() { document.getElementById('username').focus(); });
//-->
</script>


<style type='text/css'>
html, body {
	height: 100% !important;
}
body {
	text-align: center;
	background-color: #EEEEEE;
	background-image: url(../images/admin_login.gif);
	background-repeat: no-repeat;
	background-position: center center;
}
div.box {
	border: 1px dashed #AAAAAA;
	padding: 15px;
	background: #FFFFFF;
	font-family: "Trebuchet MS", tahoma, verdana, serif;
	font-size: 12px;
        width: 470px;
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
</style>
{/literal}



<table cellspacing='0' cellpadding='0' style='width: 100%; height: 100%;'>
<tr>
<td align='center'>
	<form action='admin_login.php' method='POST'>
	<div class='box'>
		<table cellpadding='0' cellspacing='0'>
		<tr>
		<td class='login'>{lang_print id=28}: &nbsp;</td>
		<td class='login'><input type='text' class='text' name='username' id='username' maxlength='50'> &nbsp;</td>
		<td class='login'>{lang_print id=29}: &nbsp;</td>
		<td class='login'><input type='password' class='text' name='password' id='password' maxlength='50'> &nbsp;</td>
		<td class='login'><input type='submit' class='button' value='{lang_print id=30}'></td>
		</tr>
		</table>
	        {if $is_error != 0}<div class='error'>{lang_print id=$is_error}</div>{/if}
	</div>
	<input type='hidden' name='task' value='dologin'>
	<NOSCRIPT><input type='hidden' name='javascript' value='no'></NOSCRIPT>
	</form>
</td>
</tr>
</table>

</body>
</html>