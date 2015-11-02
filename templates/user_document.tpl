{* $Id: user_documents.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{include file='header.tpl'}

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td> 
			<img src='./images/icons/document-new.png' border='0' class='icon_big' />
			<div class='page_header'>{if $doc_id != 0}{lang_print id=650003178}{else}{lang_print id=650003179}{/if}</div>
			<div><!--{if $doc_id != 0}Edit a Document{else}Add a new document{/if}-->{lang_print id=650003180}</div>
			</td>
			<td align="right">
				<table>
					<tr><td class="button"><img src="./images/icons/back16.gif" class="doc_img" alt="" /><a href="user_documents.php" title="{lang_print id=650003181}">{lang_print id=650003181}</a></td></tr>
				</table>
			</td>
		</tr>
</table>			

{* SHOW ERROR *}
{if $is_error == 1}
<table cellpadding='0' cellspacing='0'>
  <tr>
    <td class='error' style="text-align:left;">
    {foreach item=err from=$error_array name=errorArray}
    {if !empty($err)} 
      <div> <img src='./images/error.gif' border='0' class='icon'>{lang_print id=$err}</div>
    {/if}
    {/foreach}
    </td>
  </tr>
</table>
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

<form action='user_document.php{if $doc_id != 0}?document_id={$doc_id}{/if}' name='document_form' method='post' enctype="multipart/form-data">
  <table cellpadding='0' cellspacing='0' align='left'>
    <tr>
      <td class="form1" style="width:140px;">{lang_print id=650003229}<font color="red">*</font></td>
      <td class="form2"><input type="text" name="doc_title" value="{$doc_title}" size="50"></td>
    </tr>
    <tr>
      <td class="form1" style="width:140px;">{lang_print id=650003107}</td>
      <td class="form2"><textarea rows="7" cols="46" name="doc_description" >{$doc_description}</textarea></td>
    </tr>
		<tr>
      <td class="form1" style="width:140px;">{lang_print id=650003115}<font color="red">*</font></td>
      <td class="form2">
				<table cellpadding="0" cellspacing="0" align="left">
					<tr>
						<td>
							<select name="document_category" onchange="show_select_box();" id="main_select">
								<option value="0">{lang_print id=650003182}</option>
								{foreach item=category  from=$categories}
								<option value="{$category.category_id}" {if $category_id == $category.category_id || $dependency == $category.category_id}selected{/if}>{$category.category_name}</option>
							 {/foreach}
							</select>
						</td>
						<td style="padding-left:20px;">	
							{foreach item=category  from=$categories}
							{assign var=total_subcat value=$category.sub_categories|@count}
							{if $total_subcat > 0}
							<select name="document_subcat_{$category.category_id}" id="subcat_{$category.category_id}" style="display:{if $category.category_id == $dependency}block{else}none{/if};">
							{foreach item=subcat from=$category.sub_categories}
							 <option value="{$subcat.sub_cat_id}" {if $category_id == $subcat.sub_cat_id}selected{/if}>{$subcat.sub_cat_name}</option>
							{/foreach}
							</select>
							{/if}
							{/foreach}
						</td>
					</tr>
				</table>			
			</td>
    </tr>
      {if $params.visibility_option == 1 && $params.default_visibility == 'public'}
    <tr>
      <td class="form1" style="width:140px;">{lang_print id=650003117}</td>
      <td class="form2"><select name="default_visibility">
          <option value="private" {if $default_visibility == 'private'}selected="selected"{/if}>{lang_print id=650003049}</option>
          <option value="public" {if $default_visibility == 'public'}selected{/if}>{lang_print id=650003050}</option>
        </select><br />
				<span class="gry-txt">{lang_print id=650003265}</span>
      </td>
    </tr>
    {/if}
    
    {if $params.default_visibility == 'public' &&  $params.visibility_option == 0 }
		  <input type='hidden' name="default_visibility" value="public"/>
		{/if}
    
    {if $params.licensing_option == 1}
    <tr>
      <td class="form1" style="width:140px;">{lang_print id=650003183}</td>
      <td class="form2"><select name="license_document">
          <option value="ns" {if $license == 'ns'}selected{/if}>{lang_print id=650003053}</option>
          <option value="by" {if $license == 'by'}selected{/if}>{lang_print id=650003054}</option>
          <option value="by-nc" {if $license == 'by-nc'}selected{/if}>{lang_print id=650003055}</option>
          <option value="by-nc-nd" {if $license == 'by-nc-nd'}selected{/if}>{lang_print id=650003056}</option>
          <option value="by-nc-sa" {if $license == 'by-nc-sa'}selected{/if}>{lang_print id=650003057}</option>
          <option value="by-nd" {if $license == 'by-nd'}selected{/if}>{lang_print id=650003058}</option>
          <option value="by-sa" {if $license == 'by-sa'}selected{/if}>{lang_print id=650003059}</option>
          <option value="pd" {if $license == 'pd'}selected{/if}>{lang_print id=650003060}</option>
          <option value="c" {if $license == 'c'}selected{/if}>{lang_print id=650003061}</option>
        </select>
				<br />
				<span class="gry-txt">{lang_print id=650003226}</span>
      </td>
    </tr>
    {/if}
    <tr>
      <td class="form1" style="width:140px;">{lang_print id=650003184}</td>
      <td class="form2"><input type="text" name="doc_tags" value="{$doc_tags}" size="50">
      <br />
				<span class="gry-txt">{lang_print id=650003225}</span>
		  </td>
    </tr>
    <tr>
      <td class="form1" style="width:140px;">{lang_print id=650003185}{if $submit_value == 'Publish'}<font color="red">*</font>{/if}</td>
      <td class="form2">
      	<input type="file" name="document">
      	<img src='./images/icons/tip.gif' border='0' class='Tips1' title="{lang_print id=650003239}">
      	<br />
      	{if $doc_id != 0}
      		<span class="gry-txt">{lang_sprintf id=650003237 1=$file_maxsize}</span>
      	{else}
      		<span class="gry-txt">{lang_sprintf id=650003238 1=$file_maxsize}</span>
    		{/if}
      </td>
    </tr>
    
    <!-- SHOW THE DOWNLOAD OPTION IF ADMIN HAD SELECTED Enable document downloading -->
    {if $params.download_allow == 1 &&  $params.download_option_show == 1 }
		<tr>
			<td class="form1" style="width:140px;">
				{lang_print id=650003186}
			</td>
			<td class="form2">
				<table cellpadding="0" cellspacing="0" align="left">
					<tr>
						<td><input type='radio' name="document_download" value="1" {if $document_download == 1}checked{/if}/></td>
						<td><label for="allowdownload1">{lang_print id=650003187}</label> </td>
					</tr>
					<tr>
						<td><input type='radio' name="document_download" value="0" {if $document_download == 0}checked{/if}/></td>
						<td><label for="allowdownload2">{lang_print id=650003188}</label></td>
					</tr>
				</table>				
			</td>
		</tr>
		{/if}
		
	<!--	EMAIL ATTACHEMENT-->
		
		{if $params.download_allow == 1 &&  $params.download_option_show == 0 }
		  <input type='hidden' name="document_download" value="1"/>
		{/if}
		
		{if $params.email_allow == 1 && $params.email_option_show == 1  }		
		<tr>
			<td class="form1" style="width:140px;">
				{lang_print id=650003189}
			</td>
			<td class="form2">
				<table cellpadding="0" cellspacing="0" align="left">
					<tr>
						<td><input type='radio' name="allow_attachment" value="1" {if $document_attachment == 1}checked{/if}/></td>
						<td><label for="allowemailattachment1">{lang_print id=650003190}</label> </td>
					</tr>
					<tr>
						<td><input type='radio' name="allow_attachment" value="0" {if $document_attachment == 0}checked{/if}/></td>
						<td><label for="allowemailattachment2">{lang_print id=650003191}</label></td>
					</tr>
				</table>				
			</td>
		</tr>
		{/if}
		
		{if $params.email_allow == 1 &&  $params.email_option_show == 0 }
		  <input type='hidden' name="allow_attachment" value="1"/>
		{/if}
		
		
		
		<!--SECURE DOCUMENTS-->
		
		<!--SHOW SECURE SETTINGS IN CASE OF DOCUMNET CREATION ONLY-->
		{if $doc_id == 0}
	
		{if $params.secure_allow == 1 && $params.secure_option_show == 1  }		
		<tr>
			<td class="form1" style="width:140px;">
				{lang_print id=650003233}
			</td>
			<td class="form2">
				<table cellpadding="0" cellspacing="0" align="left">
					<tr>
						<td><input type='radio' name="document_secure" value="1" id="allow_secure1" {if $document_secure == 1}checked{/if}/></td>
						<td><label for="allow_secure1">{lang_print id=650003234}</label> </td>
					</tr>
					<tr>
						<td><input type='radio' name="document_secure" value="0" id="allow_secure0" {if $document_secure == 0}checked{/if}/></td>
						<td><label for="allow_secure0">{lang_print id=650003235}</label></td>
					</tr>
				</table>				
			</td>
		</tr>
		{/if}
		
		{if $params.secure_allow == 1 &&  $params.secure_option_show == 0 }
		  <input type='hidden' name="document_secure" value="1"/>
		{/if}
		
		{/if}
		
		{if $doc_id != 0}
		 <input type='hidden' name="document_secure" value="{$document_secure}" />		
		{/if}
		
		{* HIDE THE DISPLAY SETTINGS *}
		
		{if $privacy_options|@count > 1 OR $comment_options|@count > 1}	      
		<tr valign="top">
		  <td class="form1">
			</td>
			<td class="form2">
				<div id="id_show_settings"><a href="javascript:void(0);" onclick="show_hide('id_document_settings', 'id_show_settings');">{lang_print id=650003236}</a></div>
			</td>
		</tr>
		{/if}
   
		
		<tr>
		<td colspan="2">
		<div id="id_document_settings" style="display:none; width:100%; margin-top:-15px;">
		<table width="100%">
		
		{* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
		{if $user->level_info.level_document_search == 1}
		<tr valign="top">
			<td class="form1" style="width:140px;">
				{lang_print id=650003118}
			</td>
			<td class="form2">
				<table cellpadding="0" cellspacing="0" align="left">
					<tr>
						<td><input type='radio' name='document_search' id='document_search_1' value='1' {if $document_search == 1} checked='checked'{/if}></td>
						<td><label for='document_search_1'>{lang_print id=650003119}</label> </td>
					</tr>
					<tr>
          	<td><input type='radio' name='document_search' id='document_search_0' value='0' {if $document_search == 0} checked='checked'{/if}></td>
            <td><label for='document_search_0'>{lang_print id=650003120}</label></td>
           </tr>
				</table>				
			</td>
		</tr>
		{/if}
		
		{* SHOW PRIVACY OPTIONS *}
		
		{if $privacy_options|@count > 1} 		
		<tr valign="top">
			<td class="form1" style="width:140px;">
				{lang_print id=650003195}
			</td>
			<td class="form2">
				<table cellpadding="0" cellspacing="0" align="left">
					{foreach from=$privacy_options name=privacy_loop key=k item=v}
					<tr>
						<td><input type='radio' name='document_privacy' id='privacy_{$k}' value='{$k}'{if $document_privacy == $k} checked='checked'{/if}></td>
						<td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
					</tr>
					{/foreach}
				</table>				
			</td>
		</tr>
		  {/if}
		
		{* SHOW COMMENT OPTIONS *}
		{if $comment_options|@count > 1} 	
		<tr>
			<td class="form1" style="width:140px;">
				{lang_print id=650003192}
			</td>
			<td class="form2">
				<table cellpadding="0" cellspacing="0" align="left">
					{foreach from=$comment_options name=comment_loop key=k item=v}
          	<tr>
             <td><input type='radio' name='document_comments' id='comments_{$k}' value='{$k}'{if $document_comments == $k} checked='checked'{/if}></td>
             <td><label for='comments_{$k}'>{lang_print id=$v}</label></td>
            </tr>
           {/foreach}
				</table>				
			</td>
		</tr>	
		{/if}	
				</table>
		</div>
		</td>
		</tr>
		
    
    <input type="hidden" name="document_doc_id" value="{$document_doc_id}">
    <input type="hidden" name="document_filepath" value="{$document_filepath}">
    <tr>
      <td class="form1" style="width:140px;"></td>
      <td class="form2">
				<!--<input type="submit" name="upload" class="button" value="{$submit_value}" id="submit_button" onclick="javascript:hidebutton();">-->

				<div id="submit_button">
					<input type="submit" name="upload" class="button" value="{$submit_value}" onclick="javascript:showlightbox();">&nbsp;&nbsp;&nbsp;
					{if $submit_value == 'Publish'}
						<input type="submit" name="draft" class="button" value="{lang_print id=650003193}" id="Draft_button" onclick="javascript:showlightbox();">&nbsp;&nbsp;&nbsp;
					{/if}
					<input type="submit" name="Cancel" class="button" value="{lang_print id=650003194}" id="Cancel_button" onclick="javascript:hidebutton();">

			</td>
    </tr>
    <tr>
      <td class="form1" style="width:140px;"></td>
      <td class="form2"><div id="loading_img" style="display:none;"><img src="./images/icons/loading2.gif"></div></td>
    </tr>
  </table>
</form>
<div id="light" class="white_content">Uploading<img src="./images/icons/document-uploading.gif" class="doc_img" alt="" /></div>
<div id="fade" class="black_overlay"></div>
{literal}
<script type="text/javascript">
function hidebutton() {
	if(document.getElementById('submit_button'))
	document.getElementById('submit_button').style.display='none';
	if(document.getElementById('loading_img'))
	document.getElementById('loading_img').style.display='block';
}

function showlightbox() {
	document.getElementById('light').style.display='block';
	document.getElementById('fade').style.display='block';
}

function show_hide(id1, id2) { 
  document.getElementById(id1).style.display = 'block';
  document.getElementById(id2).style.display = 'none';
}

function show_select_box() {
	var ids = [
 	{/literal}
 	{foreach item=category from=$categories}
 	"{$category.category_id}",
 	{/foreach}
 	{literal}
 	];
 	
 	for(var i= 0; i<ids.length; i++)
  {
  	if(document.getElementById('subcat_' + ids[i])) {
  		document.getElementById('subcat_' + ids[i]).style.display = 'none';
  	}
  }
 	
	var value = document.getElementById('main_select').value;
	if(value > 0) {
		if(document.getElementById('subcat_' + value)) 
		document.getElementById('subcat_' + value).style.display = 'block';
	}
}
</script>
{/literal}

{include file='footer.tpl'}