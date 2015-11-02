{* $Id: admin_ie6check.tpl 1 2010-04-172 09:36:11Z SocialEngineAddOns $ *}
{include file='admin_header.tpl'}

{* showing the title on the admin page *}
<h2>{lang_print id = 650005014}</h2>
{lang_print id = 650005015}
<br />
<br />

{if $confirm == 1}
	<div class="success">{lang_print id = 650005013}</div>
{/if}

<form action='admin_ie6check.php' method='post'>

{* Making checkboxes for showing the browsers starts *}
<table cellpadding="0" cellspacing="0" width="600">
  <tbody>
    <tr>
      <td class="header">
      	{lang_print id = 650005002}
			</td>
    </tr>
    <tr>
      <td class="setting1">{lang_print id = 650005003}</td>
    </tr>
    <tr>
      <td class="setting2">
				
				<input type="checkbox" name="browser[]" value="mozila" {if $check_Array.browsers.mozila == mozila}checked="checked" {/if}> {lang_print id = 650005005} <br />
				<input type="checkbox" name="browser[]" value="chrome" {if $check_Array.browsers.chrome == chrome}checked="checked" {/if}> {lang_print id = 650005006}<br />
				<input type="checkbox" name="browser[]" value="safari" {if $check_Array.browsers.safari == safari}checked="checked" {/if}> {lang_print id = 650005004} <br />
				<input type="checkbox" name="browser[]" value="opera" {if $check_Array.browsers.opera == opera}checked="checked" {/if}> {lang_print id = 650005007} <br />
				
				<input type="checkbox" name="browser[]" value="explorer" {if $check_Array.browsers.explorer == explorer}checked="checked" {/if}> {lang_print id = 650005008} <br />
				<input type="checkbox" name="browser[]" value="netscape" {if $check_Array.browsers.netscape == netscape}checked="checked" {/if}> {lang_print id = 650005009} <br /><br />
       </td>
    </tr>
  </tbody>
</table>
<br />
<div class="error">{lang_print id = $browser_error}</div>
{* Making checkboxes for showing the browsers ends *}

{* Making textarea for showing the message starts *}
<table cellpadding="0" cellspacing="0" width="600">
  <tbody>
    <tr>
      <td class="header">
      	{lang_print id = 650005010}
			</td>
    </tr>
    <tr>
      <td class="setting1">{lang_print id = 650005011}</td>
    </tr>
    <tr>
      <td class="setting2">
				<textarea name="show_message" rows="5" cols="60">{$check_Array.message}</textarea><br /><br />
			</td>
    </tr>
  </tbody>
</table>

<div class="error">{lang_print id = $message_error}</div>
{* Making textarea for showing the message ends *}

<table>
<tr>
	<td width="170"></td>
	<td><input type="submit" name="task" value="update" class='button'></td>
</tr>
</table>

</form>

{include file='admin_footer.tpl'}