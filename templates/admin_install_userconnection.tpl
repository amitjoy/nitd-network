{include file='admin_header.tpl'}
{* $Id: admin_install_userconnection.tpl 1 2009-09-07 09:36:11Z SocialEngineAddOns $ *}
<h2>User Connections Plugin Setup</h2>
<br>
<strong>A fresh copy of the User Connections Plugin will be installed on your server!</strong><br>
<strong>Please take a backup of your site's code and database before you continue.</strong><br>

	{if !empty($error_message_lsetting)}
	  <div class='error'><img src='../images/error.gif' border='0' class='icon'> {$error_message_lsetting}</div>
	{/if}
	
	<form name='install_plugin' id="install_plugin" action='admin_viewplugins.php?install=userconnection' method='POST'>
	<input type="hidden" name="install" value="userconnection">
	<input type="hidden" name="task" value="check">
	
	<b>Please fill out all the fields below.<br><br>
	
        <table cellpadding="0" cellspacing="0" width="600">
        <tbody>
          <tr>
            <td class="header">
            	License Key 
						</td>
          </tr>
          <tr>
            <td class="setting1">Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area..</td>
          </tr>
          <tr>
            <td class="setting2">
							 <input type='text' class='text' name='lsettings' value='{$result.license_key}' size='50' maxlength='100'>
              Format: XXXXXX-XXXXXX-XXXX 
						</td>
          </tr>
        </tbody>
      </table>
			<br />
	

		<br><br>
	
	<div id="submit_button" style="display:">
	<input type="button" onClick="document.install_plugin.submit();document.getElementById('submit_button').style.display='none';document.getElementById('submit_wait').style.display='';" class="button" value="Install Userconnection Plugin"> <input type="button" class="button" value="Cancel" onClick="location.href='admin_viewplugins.php'">
	</div>
	
	<div id="submit_wait" style="display:none">
		<br>&nbsp;&nbsp;&nbsp;Installing User Connection Path ... Please wait!
	</div>
	<br><br><br>
	

	
	</form>


{include file='admin_footer.tpl'}