{* $Id: admin_levels_documentsettings.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{include file='admin_header.tpl'}
<h2>{lang_print id=650003076}: {$level_name}</h2>
{lang_print id=650003077}

<table cellspacing='0' cellpadding='0' width='100%' class="m-top-2">
<tr>
<td class='vert_tab0'>&nbsp;</td>
<td valign='top' class='pagecell' rowspan='{math equation="x+5" x=$level_menu|@count}'>

  <h2>{lang_print id=650003227}</h2>
  {lang_print id=650003228}
  <br />
  <br />

  {* SHOW SUCCESS MESSAGE *}
  {if $success == 1}
    <div class='success'><img src='../images/success.gif' class='icon' border='0'>{lang_print id=650003121}</div>
  {/if}

  {* SHOW ERROR MESSAGE *}
  {if $is_error != 0}
    <div class='error'><img src='../images/error.gif' class='icon' border='0'> {lang_print id=$error_message}</div>
  {/if}

<form action="admin_levels_documentsettings.php" method="POST">

<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=650003078}</td>
  </tr>
  <tr>
    <td class='setting1'>{lang_print id=650003079}</td>
  </tr>
  <tr>  
	  <td class='setting2'>
		  <table cellpadding='0' cellspacing='0'>
		  <tr>
		   <td><input type='radio' name='level_document_allow' id='document_allow_1' value='1'{if $document_allow == 1} CHECKED{/if}>&nbsp;</td>
		   <td><label for='document_allow_1'>{lang_print id=650003080}</label></td>
		  </tr>
		  <tr>
		    <td><input type='radio' name='level_document_allow' id='document_allow_0' value='0'{if $document_allow == 0} CHECKED{/if}>&nbsp;</td>
		    <td><label for='document_allow_0'>{lang_print id=650003081}</label></td>
		  </tr>
		  </table>
	  </td>
	</tr>
</table>
<br />
	
<table cellpadding='0' cellspacing='0' width='600'>
	<tr>
	  <td class='header'>{lang_print id=650003082}</td>
	</tr>
	<tr>
	  <td class='setting1'>{lang_print id=650003083}</td>
	</tr>
	<tr>  
	  <td class='setting2'>    
		  <table cellpadding='0' cellspacing='0'>
		    <tr>
		    <td><input type='radio' name='level_document_approved' id='document_approved_1' value='1'{if $document_approved == 1} CHECKED{/if}>&nbsp;</td>
		    <td><label for='document_approved_1'>{lang_print id=650003084}</label></td>
		   </tr>
		    <tr>
		    <td><input type='radio' name='level_document_approved' id='document_approved_0' value='0'{if $document_approved == 0} CHECKED{/if}>&nbsp;</td>
		    <td><label for='document_approved_0'>{lang_print id=650003085}</label></td>
		    </tr>
		  </table>
	  </td>
	</tr>
</table>
<br>
  
<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
	  <td class='header'>{lang_print id=650003086}</td>
	</tr>
	<tr>
	  <td class='setting1'>{lang_print id=650003087}</td>
	</tr>
	<tr>  
	  <td class='setting2'> 
		  <table cellpadding='0' cellspacing='0'>
			  <tr>
			  	<td><input type='text' class='text' size='2' name='level_document_entries' maxlength='3' value='{$entries_value}'></td>
			  	<td>&nbsp; {lang_print id=650003088}</td>
			  </tr>
		  </table>
	  </td>
	</tr>
	<tr>
		<td class='setting1'>
	    {lang_print id=650003149}{$max_size} KB.
		</td>
 </tr> 
 <tr>
 	<td class='setting2'>
  	<table cellpadding='0' cellspacing='0'>
  		<tr>
  			<td><input type='text' class='text' size='5' name='level_document_filesize' maxlength='6' value='{$document_filesize}'></td>
  			<td>&nbsp; KB<br/>{lang_print id=650003151} {$max_size} KB.
  		</td>
  	 </tr>
  </table>
	</td>
 </tr>
</table>

<br>
  
  
  <table cellpadding='0' cellspacing='0' width='600'>
  	<tr>
  		<td class='header'>{lang_print id=650003089}</td>
    </tr>
  	<tr>
  		<td class='setting1'>
  		<b>{lang_print id=650003090}</b><br/>
  		{lang_print id=650003091}
  		</td>
   </tr>
   <tr>
   		<td class='setting2'>
    	  <table cellpadding='0' cellspacing='0'>
      		<tr>
      			<td><input type='radio' name='level_document_search' id='document_search_1' value='1'{if $document_search == 1} CHECKED{/if}></td>
      			<td><label for='document_search_1'>{lang_print id=650003092}</label>&nbsp;&nbsp;</td>
      	  </tr>
      		<tr>
	      		<td><input type='radio' name='level_document_search' id='document_search_0' value='0'{if $document_search == 0} CHECKED{/if}></td>
	      		<td><label for='document_search_0'>{lang_print id=650003093}  </label>&nbsp;&nbsp;</td>
      		</tr>
        </table>
  		</td>
   </tr>
  		
   <tr>
     <td class='setting1'>
      <b>{lang_print id=650003094}</b><br/>
     {lang_print id=650003095}
     </td>
   </tr>
  <tr>
    <td class='setting2'>
    	<table cellpadding='0' cellspacing='0'>
    		{foreach from=$document_privacy key=k item=v}
      		<tr>
      				<td><input type='checkbox' name='level_document_privacy[]' id='privacy_{$k}' value='{$k}'{if $k|in_array:$level_document_privacy} checked='checked'{/if}></td>
      				<td><label for='privacy_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td>
      	 </tr>
      {/foreach}
      </table>
    </td>
  </tr>
  <tr>
  	<td class='setting1'>
 		 <b>{lang_print id=650003096}</b><br/>
  		{lang_print id=650003097}
    </td>
  </tr>
  <tr>
  	<td class='setting2'>
   	 <table cellpadding='0' cellspacing='0'>
	     {foreach from=$document_comments key=k item=v}
	      <tr>
		      <td><input type='checkbox' name='level_document_comments[]' id='comments_{$k}' value='{$k}'{if $k|in_array:$level_document_comments} checked='checked'{/if}></td>
		      <td><label for='comments_{$k}'>{lang_print id=$v}</label>&nbsp;&nbsp;</td>
	     </tr>
	    {/foreach}
    </table>
  </td>
  </tr>
</table>

<br>
  <input type="submit" name="update" class="button" value="Save Changes">
	<input type='hidden' name='task' value='dosave'>
  <input type='hidden' name='level_id' value='{$level_info.level_id}'>
</form>
</td>
</tr>

{* DISPLAY MENU *}
<tr>
	<td width='100' nowrap='nowrap' class='vert_tab'><div style='width: 100px;'><a href='admin_levels_edit.php?level_id={$level_info.level_id}'>{lang_print id=285}</a></div></td>
</tr>
<tr>
	<td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'>
		<div style='width: 100px;'><a href='admin_levels_usersettings.php?level_id={$level_info.level_id}'>{lang_print id=286}</a></div>
	</td>
</tr>
<tr>
	<td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;'><div style='width: 100px;'><a href='admin_levels_messagesettings.php?level_id={$level_info.level_id}'>{lang_print id=287}</a></div>
</td>
</tr>
{section name=level_plugin_loop loop=$global_plugins}
{section name=level_page_loop loop=$global_plugins[level_plugin_loop].plugin_pages_level}
  <tr><td width='100' nowrap='nowrap' class='vert_tab' style='border-top: none;{if $global_plugins[level_plugin_loop].plugin_pages_level[level_page_loop].page == $page} border-right: none;{/if}'><div style='width: 100px;'><a href='{$global_plugins[level_plugin_loop].plugin_pages_level[level_page_loop].link}?level_id={$level_info.level_id}'>{lang_print id=$global_plugins[level_plugin_loop].plugin_pages_level[level_page_loop].title}</a></div></td></tr>
{/section}
{/section}

<tr>
<td class='vert_tab0'>
  <div style='height: 760px;'>&nbsp;</div>
</td>
</tr>
</table>
{include file='admin_footer.tpl'}