{* $Id: admin_install_document.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}

{include file='admin_header.tpl'}
<h2>Documents/Scribd iPaper plugin Setup</h2>
<br>
<strong>A fresh copy of the Documents/Scribd iPaper Plugin will be installed on your server!</strong><br>
<strong>Please take a backup of your site's code and database before you continue.</strong><br>

	{if !empty($error_message_lsetting)}
	  <div class='error'><img src='../images/error.gif' border='0' class='icon'> {$error_message_lsetting}</div>
	{/if}
	
	{if $is_error == 1 && !empty($error)}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'>{lang_print id=$error}</div>
  {/if}
  
	{if $api_error != ''}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'>{$api_error}</div>
  {/if}

  
	
	<form name='install_plugin' id="install_plugin" action='admin_viewplugins.php?install=document' method='POST'>
	<input type="hidden" name="install" value="document">
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
							<input type='text' class='text' name='lsettings' value='FUCK-DAMN-XRIPX' size='50' maxlength='100'>
              Format: XXXXXX-XXXXXX-XXXX 
						</td>
          </tr>
        </tbody>
      </table>
			<br />
			
			
			<table cellpadding='0' cellspacing='0' width='600'>
			<tr>
				<td class='header'>Scribd API Credentials</td>
			</tr>
			<tr>
				<td class='setting1'>
			  	To sign up for a Scribd API account, visit here: <a href="http://www.scribd.com/developers/signup_api_details" target="_blank">http://www.scribd.com/developers/signup_api_details</a>.
				</td>
			</tr>
			<tr>
				<td class='setting2'>
				  <table cellpadding='2' cellspacing='0'>
				  	<tr>
							<td class="form1" width="27%">API Key</td>
							<td class="form2" width="73%">
								<input type="text" name="api_key" value="{$api_key}" style="width:456px;"><br />
							 	<span style="color:#999999;">The Scribd API Key for your website.</span>
							</td>
						</tr>
						<tr>
							<td class="form1">Secret Key</td>
							<td class="form2">
								<input type="text" name="secret_key" value="{$secret_key}" style="width:456px;"><br />
								<span style="color:#999999;">The Scribd Secret Key for your website.</span>
							</td>
						</tr>
				   </table>
				</td>
			</tr>
			</table><br/>
	

		<br><br>
	
	<div id="submit_button" style="display:">
	<input type="button" onClick="document.install_plugin.submit();document.getElementById('submit_button').style.display='none';document.getElementById('submit_wait').style.display='';" class="button" value="Install Documents/Scribd iPaper Plugin"> <input type="button" class="button" value="Cancel" onClick="location.href='admin_viewplugins.php'">
	</div>
	
	<div id="submit_wait" style="display:none">
		<br>&nbsp;&nbsp;&nbsp; Installing Documents/Scribd iPaper plugin ... Please wait!
	</div>
	<br><br><br>
	

	
	</form>


{include file='admin_footer.tpl'}