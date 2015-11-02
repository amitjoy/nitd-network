{* $Id: user_documents.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{include file='header.tpl'}
{literal}
<script type="text/javascript">  
 
function confirmDelete(id) {
	  document_id = id;
  	TB_show('{/literal}{lang_print id=650003012}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
 }
 function deleteDoc() {
  		window.location = 'user_documents.php?task=delete&document_id='+document_id;
  	}
  	
 function publishDoc(id) {
 	 document_id = id;
 	 TB_show('{/literal}{lang_print id=650003138}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmpublish', '', '../images/trans.gif');
 }
 
 function docPublish() {
 	 window.location = 'user_documents.php?task=publish&document_id='+document_id;
 }
  	</script>
{/literal}

{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
  <div style='display: none;' id='confirmdelete'>
    <div class="m-top-1">
      {lang_print id=650003196}
    </div>
    <br>
    <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteDoc();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  </div>
  
  {* HIDDEN DIV TO DISPLAY CONFIRATION MSG FOR PUBLISH DOCUMENT*} 
  <div style='display: none;' id='confirmpublish'>
    <div class="m-top-1">
      {lang_print id=650003197}
    </div>
    <br>
    <input type='button' class='button' value='{lang_print id=650003139}' onClick='parent.TB_remove();parent.docPublish();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  </div>
  
{* SHOW SUCCESS MESSAGE *}
{if $success != ''}
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='success'><img src='./images/success.gif' border='0' class='icon'>
   {if $success == 1}
     {lang_print id=650003134}
   {elseif $success ==2}
     {lang_print id=650003133}
   {/if}
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

<table cellpadding="0" cellspacing="0" width="100%">
	<tr valign="top">
		<td width="600px">
			<img src='./images/icons/document48.gif' border='0' class='icon_big'>
			<div class='page_header'>{lang_print id=650003023}</div>
			<div>{lang_print id=650003024}</div>
			<br>
			{if $confirm == 1}
			<div class='success'><img src='./images/success.gif' border='0' class='icon'>{lang_print id=650003133}</div>
			<br>
			{/if}
			<div>
				<img src='./images/icons/plus16.gif' border='0' class='icon'>
  			<a href='user_document.php' title='{lang_print id=650003198}'>{lang_print id=650003198}</a>
  			&nbsp;&nbsp;&nbsp;&nbsp;
  			{if $total_entries!=0}
				<img src='./images/icons/search16.gif' border='0' class='icon'>	
  			<a href="javascript:void(0)" onClick="show_hide('id_document_search');" title="{lang_print id=650003026}">{lang_print id=650003026}</a>
  			{/if}
  		</div>
			<br>

			{* SHOW SUCCESS MESSAGE *}
			{if $msg != ''}
			<table cellpadding='0' cellspacing='0'>
				<tr>
					<td class='success'><img src='./images/success.gif' border='0' class='icon'>{$msg}</td>
				</tr>
			</table>
			<br>
			{/if}

			{* SHOW SEARCH FIELD IF ANY DOCUMENTS EXIST *}
			  <div class='document_search' id='id_document_search' style='{if $search == ""} display: none;{/if}'>
			    <form action='user_documents.php' name='searchform' method='post'>
			    <table cellpadding='0' cellspacing='0' align='center'>
			    <tr>
			    <td>{lang_print id=650003025} &nbsp;</td>
			    <td><input type='text' name='search' maxlength='100' size='30' value='{$search}'>&nbsp;</td>
			    <td><input type='submit' class='button' value='{lang_print id=650003026}'></td>
			    </tr>
			    </table>
			    <input type='hidden' name='s' value='{$s}'>
			    <input type='hidden' name='p' value='{$p}'>
			    </form>
			  </div>
			
			  
		
			{* DISPLAY PAGINATION MENU IF APPLICABLE *}
			{if $maxpage > 1}
			  <div class='center'>
			  {if $p != 1}
			     <a href='user_documents.php?search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=650003027}</a>
			   {else}
			   		<font class='disabled'>&#171; {lang_print id=650003027}</font>
			  {/if}
			  &nbsp;|&nbsp;
				{if $p_start == $p_end}
					<b>{lang_sprintf id=184 1=$p_start 2=$total_entries}</b>
					{else}
					<b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_entries}</b>
				{/if}
			   &nbsp;|&nbsp;
			  {if $p != $maxpage}
			  	<a href='user_documents.php?search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
			  {else}
			  		<font class='disabled'>{lang_print id=183} &#187;</font>
			  {/if}
			  </div>
			  <br>
			{/if}

		{* DISPLAY MESSAGE IF NO DOCUMENT ENTRIES *}
		{if !empty($total_entries)}
	  {section name=document_loop loop=$documents}
	    <div class="document_listing">
	      <table cellpadding='0' cellspacing='0' width='100%'>
	      	<tr valign="top">
	        	<td valign='top' width="130">
	        		<div>
	        		<a href='{$url->url_create("document", $documents[document_loop]->document_owner->user_info.user_username, $documents[document_loop]->document_info.document_id, $documents[document_loop]->document_info.document_slug)}' title='{$documents[document_loop]->document_info.document_title}'>
	          		<img src="{$documents[document_loop]->document_info.document_thumbnail}" class="photo" height="120" width="120">
	          		</a>
	        		</div>
	     			</td>
			 			<td>
							<div class="title_row">
								<div class="document_name fleft">
									<a href='{$url->url_create("document", $documents[document_loop]->document_owner->user_info.user_username, $documents[document_loop]->document_info.document_id, $documents[document_loop]->document_info.document_slug)}' title='{$documents[document_loop]->document_info.document_title}'>{$documents[document_loop]->document_info.document_title|truncate:70:"...":true}</a><br />
								</div>
								<div class="fright">
								{if $documents[document_loop]->document_info.document_featured == 1}
									<img src="./images/icons/document_goldmedal1.gif" alt="" class="icon" border="0" title="Featured" />
									{/if}
									{if $documents[document_loop]->document_info.document_approved == 1}
									<img src="./images/icons/document_approved1.gif" alt="" class="icon" border="0" title="Approved" />
									
									{else}
									<img src="./images/icons/document_approved0.gif" alt="Not Approved" class="icon" border="0" title="Not Approved" />
									{/if}
								</div>
								<div class="clr"></div>
								<div class="alert-message">
									{if $documents[document_loop]->document_info.document_status == 0}
										<img src="./images/icons/document_wait.gif" alt="Wait" title="Wait" class="icon" border="0" />
										{lang_print id=650003199}
									{elseif $documents[document_loop]->document_info.document_status == 2}
										<img src="./images/icons/document_alert.gif" alt="Alert" title="Alert" class="icon" border="0" />
										{lang_print id=650003200}
									{elseif $documents[document_loop]->document_info.document_status == 3}
										<img src="./images/icons/document_alert.gif" alt="Alert" title="Alert" class="icon" border="0" />
										{lang_print id=650003201}
									{/if}
									</div>
							</div>
							<div class="document_list_des">
								{assign var='document_datecreated' value=$datetime->time_since($documents[document_loop]->document_info.document_datecreated)}{capture assign="created"}{lang_sprintf id=$document_datecreated[0] 1=$document_datecreated[1]}{/capture}
								
								{assign var='document_dateupdated' value=$datetime->time_since($documents[document_loop]->document_info.document_dateupdated)}{capture assign="updated"}{lang_sprintf id=$document_dateupdated[0] 1=$document_dateupdated[1]}{/capture}
								
									<span class="gry-txt">{lang_sprintf id=650003162 1=$created},
									{lang_sprintf id=650003163 1=$updated},
									 
								  {lang_sprintf id=650003021 1=$documents[document_loop]->document_info.total_comments},
									{lang_sprintf id=650003022 1=$documents[document_loop]->document_info.document_views}</span>
									<br />
									{lang_print id=650003038} 
									{if $documents[document_loop]->document_info.main_cat}
									<a href='{$url->url_create("browsedoccat", $documents[document_loop]->document_info.main_cat.category_id)}' title="{if $document->document_info.category_name != ''}{$document->document_info.category_name} {else}Default{/if}">{$documents[document_loop]->document_info.main_cat.category_name}</a> &raquo;
				<a href='{$url->url_create("browsedoccat", $documents[document_loop]->document_info.category_id)}' title="ebook">{if $documents[document_loop]->document_info.category_name != ''}{$documents[document_loop]->document_info.category_name} {else}{lang_print id=650003160} {/if}</a>
									{else}
									<a href='{if $documents[document_loop]->document_info.category_id != ""}{$url->url_create("browsedoccat", $documents[document_loop]->document_info.category_id)}{else}{/if}{$url->url_create("browsedoccat", 0)}'>{if $documents[document_loop]->document_info.category_name != ''}{$documents[document_loop]->document_info.category_name} {else}{lang_print id=650003160} {/if}</a>
									{/if}
								<div>
									{if $documents[document_loop]->document_info.document_description != ''}
									Description: {$documents[document_loop]->document_info.document_description|escape:html|truncate:120:"...":true}
									{/if}
								</div>
								{assign var=total_tags value=$documents[document_loop]->document_info.tags|@count}
								{if $total_tags > 0}
								{lang_print id=650003161}
									{foreach item=tag from=$documents[document_loop]->document_info.tags name=tag_loop}
									{if $smarty.foreach.tag_loop.iteration == 1}
									<a href='{$url->url_create("browsedoctag", $tag)}' title='{$tag}'><b>{$tag}</b></a>
									{else}
									, <a href='{$url->url_create("browsedoctag", $tag)}' title='{$tag}'><b>{$tag}</b></a>
									{/if}
										
									{/foreach} 
									{/if}						
							</div>
							<div class="listing_doc_btn_links">
								<img src="./images/icons/document_view_document.png" alt="" class="icon2" />
								<a href="{$url->url_create("document", $documents[document_loop]->document_owner->user_info.user_username, $documents[document_loop]->document_info.document_id,  $documents[document_loop]->document_info.document_slug)}" title="{lang_print id=650003202}">{lang_print id=650003202}</a>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<img src="./images/icons/document_edit_document.png" alt="" class="icon2" />
								<a href="user_document.php?document_id={$documents[document_loop]->document_info.document_id}" title="{lang_print id=650003203}">{lang_print id=650003203}</a>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								{if $documents[document_loop]->document_info.document_publish == 0}
								<img src="./images/icons/document_publish.png" alt="" class="icon2" />
								<a href="javascript: publishDoc('{$documents[document_loop]->document_info.document_id}');" title="{lang_print id=650003204}">{lang_print id=650003204}</a>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	
								{/if}						
								<img src="./images/icons/action_delete2.gif" alt="" class="icon2" />
	          		<a href="javascript: confirmDelete('{$documents[document_loop]->document_info.document_id}');" title="{lang_print id=650003205}">{lang_print id=650003205}</a>
							</div>
			 			</td>	
	      	</tr>
	      </table>
	    </div>
	  {/section}
	  
	  {/if}
	  
	  {if empty($total_entries)}
	   <br>
  		<table cellpadding='0' cellspacing='0'>
  			<tr>
  				<td class='result'>
  					 {if !empty($search)}
  					  <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=650003105}
		         {else}
		     		 <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=650003112} <a href='user_document.php'>{lang_print id=650003113}</a> {lang_print id=650003114}
		         {/if}		
		 			</td>
		 		</tr>
		 	</table>
		
		 {/if}
		 
		 		
			{* DISPLAY PAGINATION MENU IF APPLICABLE *}
			{if $maxpage > 1}
			  <div class='center clr'>
			  {if $p != 1}
			     <a href='user_documents.php?search={$search}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=650003027}</a>
			   {else}
			   		<font class='disabled'>&#171; {lang_print id=650003027}</font>
			  {/if}
			  &nbsp;|&nbsp;
				{if $p_start == $p_end}
					<b>{lang_sprintf id=184 1=$p_start 2=$total_entries}</b>
					{else}
					<b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_entries}</b>
				{/if}
			   &nbsp;|&nbsp;
			  {if $p != $maxpage}
			  	<a href='user_documents.php?search={$search}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
			  {else}
			  		<font class='disabled'>{lang_print id=183} &#187;</font>
			  {/if}
			  </div>
			  <br>
			{/if}
	
</td>
</table>


{include file='footer.tpl'}

{literal}
<script type="text/javascript">
function show_hide(id1) {
	if(document.getElementById(id1).style.display=='none') {
		document.getElementById(id1).style.display='block';
	} else {
		document.getElementById(id1).style.display='none';
	}
}
</script>
{/literal}