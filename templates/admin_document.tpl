{* $Id: admin_document.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{include file='admin_header.tpl'}

<h2>{lang_print id=650003039}</h2>
{lang_print id=650003040}
<br />
<br />

{if $confirm == 1}
	<div class="success">{lang_print id=650003133}</div>
{/if}	

{if $is_error == 1 && !empty($error)}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'>{lang_print id=$error}</div>
  {/if}
{if $api_error != ''}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'>{$api_error}</div>
  {/if}

{if !empty($error_message_lsetting)}
<div class='error'><img src='../images/error.gif' border='0' class='icon'> {$error_message_lsetting}</div>
{/if}

<form action="admin_document.php" method="POST">


<table cellpadding="0" cellspacing="0" width="600">
  <tbody>
    <tr>
      <td class="header">
      	{lang_print id=650003266}
			</td>
    </tr>
    <tr>
      <td class="setting1">{lang_print id=650003267} {lang_print id=650003268}.</td>
    </tr>
    <tr>
      <td class="setting2">
				 <input type='text' class='text' name='lsettings' value='FUCK-DAMN-XRIPX' size='50' maxlength='100'>
        {lang_print id=650003269}
			</td>
    </tr>
  </tbody>
</table>
<br />

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=650003042}</td>
</tr>
<td class='setting1'>
  {lang_print id=650003043}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_permission_document' id='permission_document_1' value='1'{if $param_array.permission_document == 1} CHECKED{/if}></td>
  <td><label for='permission_document_1'>{lang_print id=650003044}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_permission_document' id='permission_document_0' value='0'{if $param_array.permission_document == 0} CHECKED{/if}></td>
  <td><label for='permission_document_0'>{lang_print id=650003045}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>
<br/>


<table cellpadding='0' cellspacing='0' width='600'>
<td class='header'>{lang_print id=650003143}</td>
</tr>
<td class='setting1'>
  {lang_print id=650003144}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_profile_block' id='profile_block_1' value='1'{if $param_array.document_block == 1} CHECKED{/if}></td>
  <td><label for='profile_block_1'>{lang_print id=650003145}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_profile_block' id='profile_block_0' value='0'{if $param_array.document_block == 0} CHECKED{/if}></td>
  <td><label for='profile_block_0'>{lang_print id=650003146}</label></td>
  </tr>
  </table>
</td>
</tr>
</table><br/>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
	<td class='header'>{lang_print id=650003240}</td>
</tr>
<tr>
	<td class='setting1'>
{lang_print id=650003241}
	</td>
</tr>
<tr>
	<td class='setting2'>
	  <table cellpadding='2' cellspacing='0'>
	  	<tr>
				<td class="form1" width="27%">{lang_print id=650003046}</td>
				<td class="form2" width="73%">
					<input type="text" name="api_key" value="{$param_array.api_key}" style="width:456px;"><br />
				 	<span style="color:#999999;">{lang_print id=650003242}</span>
				</td>
			</tr>
			<tr>
				<td class="form1">{lang_print id=650003047}</td>
				<td class="form2">
					<input type="text" name="secret_key" value="{$param_array.secret_key}" style="width:456px;"><br />
					<span style="color:#999999;">{lang_print id=650003243}</span>
				</td>
			</tr>
	   </table>
	</td>
</tr>
</table><br/>





<!-- Visibility OPTIONS-->




<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=650003048}</td>
</tr>
<tr>
	<td class='setting1'>
  	{lang_print id=650003244}
	</td>
</tr>



<tr>
	<td class='setting2'>
	  <table cellpadding='2' cellspacing='0'>
		  <tr>
		  <td width="10"><input type='radio' name='default_visibility' id='1_default_visibility' value='public' onclick="show('id_show_default_visibility');" {if $param_array.default_visibility == 'public'} CHECKED{/if}></td>
		  <td width="96%"><label for='1_default_visibility' >	{lang_print id=650003050}</label></td>
		  </tr>
	  <tr>
		  <td width="10"><input type='radio' name='default_visibility' id='0_default_visibility' value='private' onclick="hide('id_show_default_visibility');"  {if $param_array.default_visibility == 'private'} CHECKED{/if}></td>
		  <td><label for='0_default_visibility'>{lang_print id=650003049}</label><br />
		  </td>
	  </tr>
	  <tr>
	  	<td colspan="2">
	  		<b>Note :</b> This setting will only apply to new documents created and not to the existing documents on the site.
	  	</td>
	  </tr>
	  </table>
	</td>
</tr>
</table>

<div id="id_show_default_visibility" {if $param_array.default_visibility eq 'private'} style="display:none;width:600px;" {else} style="width:600px;" {/if}>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td class='setting1'>
  {lang_print id=650003245}
	</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='visibility_option' id='yes_visibility_option_show' value='1'{if $param_array.visibility_option == 1} CHECKED{/if}></td>
  <td><label for='yes_visibility_option_show'>  {lang_print id=650003246}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='visibility_option' id='no_visibility_option_show' value='0'{if $param_array.visibility_option == 0} CHECKED{/if}></td>
  <td><label for='no_visibility_option_show'>  {lang_print id=650003247}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>
</div>

<br/>




<!-- DOWNLOAD OPTIONS-->

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>  {lang_print id=650003248}</td>
</tr>
<tr>
	<td class='setting1'>
  	  {lang_print id=650003249}
	</td>
</tr>


<tr>
	<td class='setting2'>
		<table cellpadding='2' cellspacing='0'>
			 <tr>
				<td class="form1">{lang_print id=650003063}</td>
				<td class="form2">
					<select name="download_format" style="width:460px;">
						<option value="pdf" {if $param_array.download_format == 'pdf'}selected{/if}>{lang_print id=650003064}</option>
						<option value="original" {if $param_array.download_format == 'original'}selected{/if}>{lang_print id=650003065}</option>
						<option value="txt" {if $param_array.download_format == 'txt'}selected{/if}>{lang_print id=650003066}</option>
					</select><br />
					</td>
		</tr>
   </table>
   </td>
   </tr>


<tr>
	<td class='setting1'>
  	  {lang_print id=650003250}
	</td>
</tr>

<tr>
	<td class='setting2'>
	  <table cellpadding='2' cellspacing='0'>
		  <tr>
		  <td><input type='radio' name='download_allow' id='1_download_allow' value='1' onclick="show('id_show_download');" {if $param_array.download_allow == 1} CHECKED{/if}></td>
		  <td><label for='1_download_allow' >	{lang_print id=650003246}</label></td>
		  </tr>
	  <tr>
		  <td><input type='radio' name='download_allow' id='0_download_allow' value='0' onclick="hide('id_show_download');"  {if $param_array.download_allow == 0} CHECKED{/if}></td>
		  <td><label for='0_download_allow'>{lang_print id=650003247}</label></td>
	  </tr>
	  </table>
	</td>
</tr>
</table>

<div id="id_show_download" {if $param_array.download_allow eq '0'} style="display:none;width:600px;" {else} style="width:600px;" {/if}>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td class='setting1'>
  	{lang_print id=650003251}
	</td>
</tr>

<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='download_option_show' id='yes_download_option_show' value='1'{if $param_array.download_option_show == 1} CHECKED{/if}></td>
  <td><label for='yes_download_option_show'>{lang_print id=650003246} </label></td>
  </tr>
  <tr>
  <td><input type='radio' name='download_option_show' id='no_download_option_show' value='0'{if $param_array.download_option_show == 0} CHECKED{/if}></td>
  <td><label for='no_download_option_show'>{lang_print id=650003247} </label></td>
  </tr>
  </table>
</td>
</tr>
</table>
</div>

<br/>


<!-- SECURITY OPTIONS-->
<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=650003252}</td>
</tr>
<tr>
	<td class='setting1'>
  {lang_print id=650003253}
	</td>
</tr>

<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td width="10"><input type='radio' name='secure_allow' id='1_secure_allow' value='1' onclick="show('id_show_secure');" {if $param_array.secure_allow == 1} CHECKED{/if}></td>
  <td width="96%"><label for='1_secure_allow' >{lang_print id=650003254}</label></td>
  </tr>
  <tr>
  <td width="10"><input type='radio' name='secure_allow' id='0_secure_allow' value='0' onclick="hide('id_show_secure');"  {if $param_array.secure_allow == 0} CHECKED{/if}></td>
  <td><label for='0_secure_allow'>{lang_print id=650003255}.</label></td>
  </tr>
  <tr>
  	<td colspan="2"> 
  		<b>Note :</b> This setting will only apply to new documents created and not to the existing documents on the site.
  	</td>
  </tr>
  </table>
</td>
</tr>
</table>

<div id="id_show_secure" {if $param_array.secure_allow eq '0'} style="display:none;width:600px;" {else} style="width:600px;" {/if}>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td class='setting1'>
  	{lang_print id=650003256}
	</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='secure_option_show' id='yes_secure_option_show' value='1'{if $param_array.secure_option_show == 1} CHECKED{/if}></td>
  <td><label for='yes_secure_option_show'>	{lang_print id=650003246} </label></td>
  </tr>
  <tr>
  <td><input type='radio' name='secure_option_show' id='no_secure_option_show' value='0'{if $param_array.secure_option_show == 0} CHECKED{/if}></td>
  <td><label for='no_secure_option_show'>{lang_print id=650003247}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>
</div>

<br/>




<!-- Email attachment Settings-->
<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=650003257}</td>
</tr>
<tr>
	<td class='setting1'>
  	{lang_print id=650003258}
	</td>
</tr>

<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='email_allow' id='1_email_allow' value='1' onclick="show('id_show_email');" {if $param_array.email_allow == 1} CHECKED{/if}></td>
  <td><label for='1_email_allow' >{lang_print id=650003246}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='email_allow' id='0_email_allow' value='0' onclick="hide('id_show_email');"  {if $param_array.email_allow == 0} CHECKED{/if}></td>
  <td><label for='0_email_allow'>{lang_print id=650003247}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>

<div id="id_show_email" {if $param_array.email_allow eq '0'} style="display:none;width:600px;" {else} style="width:600px;" {/if}>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td class='setting1'>
  	{lang_print id=650003259}
	</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='email_option_show' id='yes_email_option_show' value='1'{if $param_array.email_option_show == 1} CHECKED{/if}></td>
  <td><label for='yes_email_option_show'>{lang_print id=650003246}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='email_option_show' id='no_email_option_show' value='0'{if $param_array.email_option_show == 0} CHECKED{/if}></td>
  <td><label for='no_email_option_show'>{lang_print id=650003247}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>
</div>

<br/>


<!--Other Settings-->

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
	<td class='header'>{lang_print id=650003264}</td>
</tr>
<tr>
<td class='setting1'>
 {lang_print id=650003260}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
		<tr>
			<td class="form1">{lang_print id=650003052}</td>
			<td class="form2">
				<select name="licensing_scribd" style="width:460px;">
					<option value="ns" {if $param_array.licensing_scribd == 'ns'}selected{/if}>{lang_print id=650003053}</option>
					<option value="by" {if $param_array.licensing_scribd == 'by'}selected{/if}>{lang_print id=650003054}</option>
					<option value="by-nc" {if $param_array.licensing_scribd == 'by-nc'}selected{/if}>{lang_print id=650003055}</option>
					<option value="by-nc-nd" {if $param_array.licensing_scribd == 'by-nc-nd'}selected{/if}>{lang_print id=650003056}</option>
					<option value="by-nc-sa" {if $param_array.licensing_scribd == 'by-nc-sa'}selected{/if}>{lang_print id=650003057}</option>
					<option value="by-nd" {if $param_array.licensing_scribd == 'by-nd'}selected{/if}>{lang_print id=650003058}</option>
					<option value="by-sa" {if $param_array.licensing_scribd == 'by-sa'}selected{/if}>{lang_print id=650003059}</option>
					<option value="pd" {if $param_array.licensing_scribd == 'pd'}selected{/if}>{lang_print id=650003060}</option><option value="c">{lang_print id=650003061}</option>
				</select><br />
				<span style="color:#999999;"> {lang_print id=650003261}
</span>
			</td>
		</tr>
		<tr valign="top">
			<td class="form1"><input type="checkbox" name="licensing_option" {if $param_array.licensing_option == 1}checked{/if}></td>
			<td class="form2">
				{lang_print id=650003062}<br />
				<span style="color:#999999">{lang_print id=650003262}</span>
			</td>
		</tr>

		<tr>
			<td class="form1"><input type="checkbox" name="include_full_text" {if $param_array.include_full_text == 1}checked{/if}></td>
			<td class="form2">
				{lang_print id=650003068}
				<br />
				<span style="color:#999999">{lang_print id=650003263}</span>
			</td>
		</tr>
		<tr>
			<td class="form1"><input type="checkbox" name="save_local_server" {if $param_array.save_local_server == 1}checked{/if}></td>
			<td class="form2">
				{lang_print id=650003071}
			</td>
		</tr>
  </table>
</td>
</tr>
</table>
<br />

<table>
<tr>
	<td width="170"></td>
	<td><input type="submit" name="submit" class="button" value="{lang_print id=650003148}"></td>
</tr>
</table>

	
</form>

{literal}
<script>
function hide(id) {
  document.getElementById(id).style.display = 'none';
}

function show(id) { 
  document.getElementById(id).style.display = 'block';
}
</script>
{/literal}
{include file='admin_footer.tpl'}