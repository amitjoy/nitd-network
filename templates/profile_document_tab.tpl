{* $Id: profile_document_tab.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{if $owner->level_info.level_document_allow != 0 && $total_docs > 0}
  <table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
    <tr>
      <td class="tab_head">
        {lang_print id=650003010} ({$total_docs})
      </td>
    </tr>
    <tr>
      <td>
        {section name=document_loop loop=$documents}
        <div class="tab_doc_listing">
					<table cellpadding='0' cellspacing='0' width='100%'>
						<tr>
							<td valign='top' width="130">
								<a href='{$url->url_create("document", $documents[document_loop]->document_owner->user_info.user_username, $documents[document_loop]->document_info.document_id, $documents[document_loop]->document_info.document_slug)}'><img src='{$documents[document_loop]->document_info.document_thumbnail}'  class="photo" height="120" width="120" /></a>
							</td>
							<td valign='top'>
								<div class="document_name fleft" style="padding-left:3px;">
									<a href='{$url->url_create("document", $documents[document_loop]->document_owner->user_info.user_username, $documents[document_loop]->document_info.document_id, $documents[document_loop]->document_info.document_slug)}'>{$documents[document_loop]->document_info.document_title}</a>
								</div>
			  				<div class="fright">
										{if $documents[document_loop]->document_info.document_featured == 1}
										<img src="./images/icons/docment_featured.png" alt="" class="icon" border="0" title="Featured" />
									{/if}
								</div>
								<div class="clr"></div>
							
								<div class="document_list" style="border:none;">
								{assign var='document_datecreated' value=$datetime->time_since($documents[document_loop]->document_info.document_datecreated)}{capture assign="created"}{lang_sprintf id=$document_datecreated[0] 1=$document_datecreated[1]}{/capture}
									{lang_sprintf id=650003162 1=$created}<br />
									<span class="gry-txt">
										<a href="#" class="gry-txt">{lang_sprintf id=650003021 1=$documents[document_loop]->document_info.total_comments}</a>,
										{lang_sprintf id=650003022 1=$documents[document_loop]->document_info.document_views}
									</span>	<br />
									
									{$documents[document_loop]->document_info.document_description|escape:html|truncate:100}
								</div>
							</td>
						</tr>
					</table>
				</div>
        {if $smarty.section.document_loop.last != true}<div style='font-size: 1pt; margin-top: 2px; margin-bottom: 2px;'>&nbsp;</div>{/if}
        {/section}
      </td>
    </tr>
  </table>

{/if}

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div class='center clr'>
  {if $p != 1}
     <a href='profile.php?user={$owner->user_info.user_username}&v=document&p={math equation="p-1" p=$p}'>&#171; {lang_print id=650003027}</a>
   {else}
   		<font class='disabled'>&#171; {lang_print id=650003027}</font>
  {/if}
  &nbsp;|&nbsp;
	{if $p_start == $p_end}
		<b>{lang_sprintf id=184 1=$p_start 2=$total_docs}</b>
		{else}
		<b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_docs}</b>
	{/if}
   &nbsp;|&nbsp;
  {if $p != $maxpage}
  	<a href='profile.php?user={$owner->user_info.user_username}&v=document&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>
  {else}
  		<font class='disabled'>{lang_print id=183} &#187;</font>
  {/if}
  </div>
{/if}
