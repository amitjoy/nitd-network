{* $Id: user_document_email.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{include file="header_global.tpl"}
{* JAVASCRIPT FOR CLOSING BOX *}
  {literal}
  <script type="text/javascript">
  <!-- 
  setTimeout("window.parent.TB_remove();", "{/literal}{$time_out}{literal}");
  if(window.parent.update) { setTimeout("window.parent.update('{/literal}{$success}{literal}');", "800"); }
  //-->
  </script>
  {/literal}
{* SHOW ERROR *}
{if $is_error == 1}
  <table cellpadding='0' cellspacing='0' align="center" style="margin-top:10px;">
  <tr>
    <td class='error'>
    {foreach item=err from=$error_array name=errorArray}
    <img src='./images/error.gif' border='0' class='icon'>
    {lang_print id=$err}
    <br/>
    {/foreach}
    </td>
  </tr>
</table>

{/if}

{* SHOW SUCCESS MESSAGE *}
{if $msg != ''}
  <table cellpadding='0' cellspacing='0' align="center" style="margin-top:100px;">
  <tr>
  <td class='success' style="text-align:center;"><img src='./images/success.gif' border='0' class='icon'>
     {lang_print id=$msg}
  </td>
  </tr>
  </table>
  <br>
{/if}


{if $excep_error == 1}
<table cellpadding='0' cellspacing='0'>
  <tr>
    <td class='error' style="text-align:left;">
    <div>
    <img src='./images/error.gif' border='0' class='icon'>
    {$excep_message}
    </div>
    </td>
  </tr>
</table>
{/if}

{if $no_form != 1}
<form method="post">
<table>
	<tr>
		<td class="form1">To</td>
		<td class="form2" align="left"><input type="text" class="text" name="to" value="{$to}" maxlength="30" /></td>
	</tr>
	<tr>
		<td class="form1">Attachment</td>
		<td class="form2" align="left"><img src="./images/icons/document_attach.gif" class="icon" alt="" /> 
			{$document->document_info.document_filename}</td>
	</tr>
	<tr>
		<td class="form1">Subject</td>
		<td class="form2" align="left"><input type="text" class="text" name="subject" value="{$subject}" maxlength="30" /></td>
	</tr>
	<tr>
		<td class="form1">Message</td>
		<td class="form2" align="left"><textarea name="message"  style="width: 350px;" rows="5" >{$message}</textarea></td>
	</tr>
	<tr>
		<td class="form1"></td>
		<td class="form2" align="left"><input type="submit" name="submit" value="Send" class="button" /></td>
	</tr>
</table>
</form>
{/if}
