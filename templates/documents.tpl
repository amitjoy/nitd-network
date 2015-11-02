{* $Id: documents.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{include file='header.tpl'}
<div class='page_header'>{lang_sprintf id=650003174 1=$owner->user_displayname 2=$url->url_create("profile", $owner->user_info.user_username) 3=$url->url_create("document", $documents[document_loop]->document_owner->user_info.user_username, $documents[document_loop]->document_info.document_id)}</div>
<br />

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
		{if $maxpage > 1}
    <div style="margin:5px;width:710px;" class="clr">
			<table width="100%" cellpadding="0" cellspacing="0" align="center">
        <tr>
					<td class="paging_bg">
						<div style='text-align: center;'> {if $p != 1}<a href='documents.php?user={$owner->user_info.user_username}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
          	&nbsp;|&nbsp;&nbsp;
          	{if $p_start == $p_end} <b>{lang_sprintf id=184 1=$p_start 2=$total_entries}</b> {else} <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_entries}</b> {/if}
          	&nbsp;&nbsp;|&nbsp;
          	{if $p != $maxpage}<a href='documents.php?user={$owner->user_info.user_username}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if} </div>
					</td>
				</tr>
      </table>
		</div>
    {/if}
    <div> 
			{section name=document_loop loop=$documents}
  		<div class="document_listing">
        <table cellpadding='0' cellspacing='0' width="100%">
      		<tr valign="top">
            <td width="130">
							<div> 
								<a href='{$url->url_create("document", $documents[document_loop]->document_owner->user_info.user_username, $documents[document_loop]->document_info.document_id)}'><img src="{$documents[document_loop]->document_info.document_thumbnail}" class="photo" width="120" height="120" alt="" /> </a>
							</div>
							</td>
            <td>
							<div class="title_row">
                <div class="document_name fleft"> 
									<a href='{$url->url_create("document", $documents[document_loop]->document_owner->user_info.user_username, $documents[document_loop]->document_info.document_id, $documents[document_loop]->document_info.document_slug)}'>{$documents[document_loop]->document_info.document_title|truncate:70:"...":true}</a> </div>
								<div class="fright">
										{if $documents[document_loop]->document_info.document_featured == 1}
										<img src="./images/icons/docment_featured.png" alt="" class="icon" border="0" title="Featured" />
									{/if}
									</div>
                <div class="clr"></div>
              </div>
          		<div class="document_list" style="border:none;"> 
								{assign var='document_datecreated' value=$datetime->time_since($documents[document_loop]->document_info.document_datecreated)}{capture assign="created"}{lang_sprintf id=$document_datecreated[0] 1=$document_datecreated[1]}{/capture}
                
                {lang_print id=650003038} 
                {if $documents[document_loop]->document_info.main_cat}
										<a href='{$url->url_create("browsedoccat", $documents[document_loop]->document_info.main_cat.category_id)}' title="{$documents[document_loop]->document_info.main_cat.category_name}">{$documents[document_loop]->document_info.main_cat.category_name}</a> &raquo;
					<a href='{$url->url_create("browsedoccat", $documents[document_loop]->document_info.category_id)}' title="{if $documents[document_loop]->document_info.category_name != ''}{$documents[document_loop]->document_info.category_name} {else}{lang_print id=650003160}{/if}">{if $documents[document_loop]->document_info.category_name != ''}{$documents[document_loop]->document_info.category_name} {else}{lang_print id=650003160}{/if}</a>
										{else}
										<a href='{if $documents[document_loop]->document_info.category_id != ""}{$url->url_create("browsedoccat", $documents[document_loop]->document_info.category_id)}{else}{$url->url_create("browsedoccat", 0)}{/if}' title="{if $documents[document_loop]->document_info.category_name != ''}{$documents[document_loop]->document_info.category_name} {else}{lang_print id=650003160}{/if}">{if $documents[document_loop]->document_info.category_name != ''}{$documents[document_loop]->document_info.category_name} {else}{lang_print id=650003160}{/if}</a>
										{/if}
                <br />
                <span class="gry-txt"> 
									{lang_sprintf id=2500108 1=$created 2=$url->url_create("profile", $documents[document_loop]->document_owner->user_info.user_username) 3=$documents[document_loop]->document_owner->user_displayname} 
									{lang_sprintf id=650003021 1=$documents[document_loop]->document_info.total_comments},
			            {lang_sprintf id=650003022 1=$documents[document_loop]->document_info.document_views},
			            {assign var='document_dateupdated' value=$datetime->time_since($documents[document_loop]->document_info.document_dateupdated)}{capture assign="updated"}{lang_sprintf id=$document_dateupdated[0] 1=$document_dateupdated[1]}{/capture}
            			{lang_sprintf id=650003162 1=$created}
								</span>
                {assign var=total_tags value=$documents[document_loop]->document_info.tags|@count}
									{if $total_tags > 0}
									<br />
									{lang_print id=650003161}									
										{foreach item=tag from=$documents[document_loop]->document_info.tags name=tag_loop}
										{if $smarty.foreach.tag_loop.iteration == 1}
										<a href='{$url->url_create("browsedoctag", $tag)}'><b>{$tag}</b></a>
										{else}
										, <a href='{$url->url_create("browsedoctag", $tag)}'><b>{$tag}</b></a>
										{/if}
											
										{/foreach} 
										{/if} <br />
								{$documents[document_loop]->document_info.document_description|escape:html|truncate:100}
							</div>
						</td>
          </tr>
    		</table>
      </div>
  {/section}
 </div>
 <div style='clear: both; height: 10px;'></div>
{* DISPLAY PAGINATION MENU IF APPLICABLE *}
	{if $maxpage > 1}
  <div style="margin:5px;width:710px;" class="clr">
		<table width="100%" cellpadding="0" cellspacing="0" align="center">
      <tr>
				<td class="paging_bg">
					<div style='text-align: center;'> {if $p != 1}<a href='documents.php?user={$owner->user_info.user_username}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
        	&nbsp;|&nbsp;&nbsp;
        	{if $p_start == $p_end} <b>{lang_sprintf id=184 1=$p_start 2=$total_entries}</b> {else} <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_entries}</b> {/if}
        	&nbsp;&nbsp;|&nbsp;
        	{if $p != $maxpage}<a href='documents.php?user={$owner->user_info.user_username}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if} </div>
				</td>
			</tr>
    </table>
	</div>
  {/if}
{include file='footer.tpl'}


{literal}
<script type="text/javascript">
function showhide(id1) {
	if(document.getElementById(id1).style.display=='none') {
		document.getElementById(id1).style.display='block';
	} else {
		document.getElementById(id1).style.display='none';
	}
}

function show_subcat(cat_id) {
	if(document.getElementById('subcat_' + cat_id)) {
		if(document.getElementById('subcat_' + cat_id).style.display == 'block') {
			document.getElementById('subcat_' + cat_id).style.display = 'none';
			//document.getElementById('button_' + cat_id).html = 'close';
		}
		else {
			document.getElementById('subcat_' + cat_id).style.display = 'block';
			//document.getElementById('button_' + cat_id).html = 'open';
		}
	}
}
</script>
{/literal} 