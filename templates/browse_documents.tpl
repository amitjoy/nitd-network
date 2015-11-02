{* $Id: browse_documents.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{include file='header.tpl'}
<img src='./images/icons/document48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=650003018}</div>
<div>{lang_print id=650003152}</div>

<br />
 
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


<table cellpadding="0" cellspacing="3" width="100%" class="clr">
	<tr>
		<td class="bro-doc_leftside" width="200">
			<div class="left_search_box">
        <table cellpadding='0' cellspacing='1'>
					<tr>
						<td colspan="2" nowrap="nowrap" style="padding-bottom:8px;">
							<form action='browse_documents.php' name='searchform' method='post'>
   							<input type='text' class="text" name='search' maxlength='100' value='{$search}' style="width:120px;" />
          &nbsp;<input type='submit' class='button' value='{lang_print id=650003026}'>
								<input type='hidden' name='s' value='{$s}'>
								<input type='hidden' name='p' value='{$p}'>
							</form>
						</td>
					</tr>
          <tr>
            <td> {lang_print id=650003036}&nbsp; </td>
            <td><select class='small' name='v' onchange="window.location.href='browse_documents.php?s={$s}&v='+this.options[this.selectedIndex].value;">
                <option value='0'{if $v == "0"} SELECTED{/if}>{lang_print id=650003015}</option>
                
      {if $user->user_exists}
                <option value='1'{if $v == "1"} SELECTED{/if}>{lang_print id=650003016}</option>
                {/if}
      
                <option value='2'{if $v == "2"} SELECTED{/if}>{lang_print id=650003153}</option>
              </select>
            </td>
						</tr>
						<tr>
            <td> {lang_print id=650003037}&nbsp; </td>
            <td><select class='small' name='s' onchange="window.location.href='browse_documents.php?v={$v}&s='+this.options[this.selectedIndex].value;">
                <option value='document_datecreated DESC'{if $s == "document_datecreated DESC"} SELECTED{/if}>{lang_print id=650003019}</option>
                <option value='document_dateupdated DESC'{if $s == "document_dateupdated DESC"} SELECTED{/if}>{lang_print id=650003154}</option>
                <option value='document_views DESC'{if $s == "document_views DESC"} SELECTED{/if}>{lang_print id=650003020}</option>
                <option value='total_comments DESC'{if $s == "total_comments DESC"} SELECTED{/if}>{lang_print id=650003155}</option>
              </select>
            </td>
          </tr>
        </table>
      </div>
			
				{assign var=total_maintags value=$tag_array|@count}
				{if $total_maintags > 0}		
			<div class="leftside_listbox">
				<div class="leftside_list">
					<div class="document_head_tagcloud">
						{lang_print id=650003231} ({ $total_maintags })
					</div>
					{foreach item=frequency key=tag from=$tag_array }
		  		{math assign='step' equation="n + (a-b)*m" n=$tag_data.min_font_size a=$frequency b=$tag_data.min_frequency m=$tag_data.step} 
					<a href='{$url->url_create("browsedoctag", $tag)}' style="font-size:{$step}px;" title='{$tag}'>{$tag}<sup>{$frequency}</sup></a> {/foreach} 
					<br/>
					<div align="right" style="padding-top:5px;"><a href="document_tags.php">{lang_print id=650003156}&raquo;</a></div>
				</div>
			</div>
			{/if}
			<div class="leftside_listbox">
    		<div class="leftside_list">
					<a href="browse_documents.php">{lang_print id=650003157}</a><br/>
    		</div>
    		{if $total_uncategorized > 0}
    		<div class="leftside_list">
					<a href='{$url->url_create("browsedoccat", 0)}'>{lang_print id=650003160}</a><br/>
    		</div>
    		{/if}
				{foreach item=category from=$categories}
				{assign var=total_subcat value=$category.sub_categories|@count}
				<div class="leftside_list">
					{if $total_subcat > 0}
					<a href="javascript:show_subcat('{$category.category_id}');" id="button_{$category.category_id}">
						<img  src="./images/icons/{if $main_cat_id == $category.category_id}minus{else}plus{/if}16.gif" class='icon' border="0" id="img_{$category.category_id}"/>
					</a>&nbsp;
					<a href='{$url->url_create("browsedoccat", $category.category_id)}'>{$category.category_name}</a>
					<div class="leftside_sublist" style="display:{if $main_cat_id == $category.category_id}block{else}none{/if}" id="subcat_{$category.category_id}"> 
						{foreach item=subcat from=$category.sub_categories} 
							<a href='{$url->url_create("browsedoccat", $subcat.sub_cat_id)}'>{$subcat.sub_cat_name}</a> <br/>
						{/foreach} 
					</div>
				{else} 
				<img  src="./images/icons/minus16_disabled.gif" class='icon' border="0" />
				<a href='{$url->url_create("browsedoccat", $category.category_id)}'>{$category.category_name}</a> 
				{/if} 
      	</div>
				{/foreach} 
			</div>		
		</td>
		
		<td class="bro-doc_rightside">
			{if $maxpage > 1}
			<div style="margin:5px 0;">
				<table width="100%" cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td class="paging_bg">
							<div style='text-align: center;'>
								{if $p != 1}<a href='browse_documents.php?s={$s}&v={$v}&p={math equation="p-1" p=$p}&i={$i}&tag={$tag_main}&search={$search}'>&#171; {lang_print id=650003027}</a>{else}&#171; {lang_print id=650003027}{/if}
								&nbsp;|&nbsp;&nbsp;
							{if $p_start == $p_end}
							<b>{lang_sprintf id=184 1=$p_start 2=$total_entries}</b>
							{else}
							<b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_entries}</b>
						{/if}
						&nbsp;&nbsp;|&nbsp;
							{if $p != $maxpage}<a href='browse_documents.php?s={$s}&v={$v}&p={math equation="p+1" p=$p}&i={$i}&tag={$tag_main}&search={$search}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
							</div>
						</td>
					</tr>
				</table>
			</div>			
			{/if}
			
			

			{assign var=total_featured value=$featured|@count}
			{if $total_featured > 0}
						
			{* ASSIGN INDICES CODE FOR JAVASCRIPT CAROUSEL *}
			{assign var="current_index" value=3}
			{capture assign="previous_index"}{if $current_index == 0}{math equation="x-1" x=$featured|@count}{else}{math equation="x-1" x=$current_index}{/if}{/capture}
			{capture assign="next_index"}{if $current_index+1 == $featured|@count}0{else}{math equation="x+1" x=$current_index}{/if}{/capture}
			{capture assign="current_num"}{math equation="x+1" x=$current_index}{/capture}
			
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="header">{lang_print id=650003158}</td>
				</tr>
				<tr>
					<td class="profile">
						<div class="doc_carousel">
						{*Featured Documents starts here*}
						<table cellpadding='0' cellspacing='0' align='center'>
							<tr>
								<td>
									<a href='javascript:void(0);' onClick='moveLeft();this.blur()'><img src='./images/doc_car_left.png' border='0' onMouseOver="this.src='./images/doc_car_left_over.png';" onMouseOut="this.src='./images/doc_car_left.png';">
									</a>
								</td>
								<td>
									<div id='document_carousel' style='width: 610px; margin: 0px 5px 0px 5px; text-align: center; overflow: hidden;'>
										<table cellpadding='0' cellspacing='0'>
											<tr>
											<td id='thumb-2' style='padding: 0px 5px 0px 5px;'><img src='./images/media_placeholder.gif' border='0' width='70'></td>
									    <td id='thumb-1' style='padding: 0px 5px 0px 5px;'><img src='./images/media_placeholder.gif' border='0' width='70'></td>
									    <td id='thumb0' style='padding: 0px 5px 0px 5px;'><img src='./images/media_placeholder.gif' border='0' width='70'></td>
												{foreach name=item_loop from=$featured key=k item=doc}  
												{* SHOW THUMBNAILS *}
												<td id='thumb{$smarty.foreach.item_loop.iteration}' class='carousel_item' align="center" width="120"> 
													<a href='{$url->url_create("document", $doc->document_owner->user_info.user_username, $doc->document_info.document_id, $doc->document_info.document_slug)}' title='{$doc->document_info.document_title}'>
													<img src='{$doc->document_info.document_thumbnail}' border='0'  onClick='this.blur()' class="photo" width="120" height="120" alt="{$doc->document_info.document_title}" /></a>
													<a href='{$url->url_create("document", $doc->document_owner->user_info.user_username, $doc->document_info.document_id, $doc->document_info.document_slug)}' title='{$doc->document_info.document_title}'>{$doc->document_info.document_title|truncate:21:"..":true}</a> 
												</td>
												{/foreach} 
											  <td id='thumb{math equation="x+1" x=$featured|@count}' style='padding: 0px 5px 0px 5px;'><img src='./images/media_placeholder.gif' border='0' width='70'></td>
										    <td id='thumb{math equation="x+2" x=$featured|@count}' style='padding: 0px 5px 0px 5px;'><img src='./images/media_placeholder.gif' border='0' width='70'></td>
										    <td id='thumb{math equation="x+3" x=$featured|@count}' style='padding: 0px 5px 0px 5px;'><img src='./images/media_placeholder.gif' border='0' width='70'></td>
											</tr>
										</table>
									</div>
									</td>
									<td>
										<a href='javascript:void(0);' onClick='moveRight();this.blur()'><img src='./images/doc_car_right.png' border='0' onMouseOver="this.src='./images/doc_car_right_over.png';" onMouseOut="this.src='./images/doc_car_right.png';"></a>
									</td>
								</tr>
							</table>
						{*Featured Documents ends here*}
						</div>
					</td>
				</tr>
			</table>				
{/if}	
			{assign var=total_docs value=$documents|@count}
			{if $total_docs > 0}
			{section name=document_loop loop=$documents}
			<div style="width:670px;" class="document_browse_item">
				<table cellpadding='0' cellspacing='0'>
					<tr valign="top">
						<td width="130">
							<div> 
							<a href='{$url->url_create("document", $documents[document_loop]->document_owner->user_info.user_username, $documents[document_loop]->document_info.document_id, $documents[document_loop]->document_info.document_slug)}' title='{$documents[document_loop]->document_info.document_title}'>
								<img src="{$documents[document_loop]->document_info.document_thumbnail}" class='photo' width="120" height="120" alt="{$documents[document_loop]->document_info.document_title}" /> 
							</a>	
							</div>
							</td>
							<td width="80%">
								<div class="title_row" style="border:none;">
									<div class="document_name fleft">
										<a href='{$url->url_create("document", $documents[document_loop]->document_owner->user_info.user_username, $documents[document_loop]->document_info.document_id, $documents[document_loop]->document_info.document_slug)}' title="{$documents[document_loop]->document_info.document_title}">{$documents[document_loop]->document_info.document_title|truncate:70:"...":true}</a> 
									</div>	
									<div class="fright">
										{if $documents[document_loop]->document_info.document_featured == 1}
										<img src="./images/icons/docment_featured.png" alt="" class="icon" border="0" title="Featured" />
									{/if}
									</div>
									<div class="clr"></div>
								</div>	
								<div style="clear:both;"></div>
								<div class="document_list" style="border:none;">
									<span class="gry-txt">
									{lang_sprintf id=650003021 1=$documents[document_loop]->document_info.total_comments},
										{lang_sprintf id=650003022 1=$documents[document_loop]->document_info.document_views}
										<!--code for created by and last update -->
									
										
										{assign var='document_datecreated' value=$datetime->time_since($documents[document_loop]->document_info.document_datecreated)}{capture assign="created"}{lang_sprintf id=$document_datecreated[0] 1=$document_datecreated[1]}{/capture}
								
										{assign var='document_dateupdated' value=$datetime->time_since($documents[document_loop]->document_info.document_dateupdated)}{capture assign="updated"}{lang_sprintf id=$document_dateupdated[0] 1=$document_dateupdated[1]}{/capture}
										
										- {lang_sprintf id=650003162 1=$created}
										- {lang_sprintf id=650003163 1=$updated} {lang_print id=650003164} 
										<a href='{$url->url_create("profile", $documents[document_loop]->document_owner->user_info.user_username)}' title='{$documents[document_loop]->document_owner->user_displayname}'>{$documents[document_loop]->document_owner->user_displayname}</a>
										</span>
									
									<!-- tags-->
									{assign var=total_tags value=$documents[document_loop]->document_info.tags|@count}
									{if $total_tags > 0}
									<br />
									{lang_print id=650003161}
										{foreach item=tag from=$documents[document_loop]->document_info.tags name=tag_loop}
										{if $smarty.foreach.tag_loop.iteration == 1}
										<a href='{$url->url_create("browsedoctag", $tag)}' title='{$tag}'><b>{$tag}</b></a>
										{else}
										, <a href='{$url->url_create("browsedoctag", $tag)}' title='{$tag}'><b>{$tag}</b></a>
										{/if}
											
										{/foreach} 
										{/if}
									<br /> 
										<b>{lang_print id=650003038}</b> 
										{if $documents[document_loop]->document_info.main_cat}
										<a href='{$url->url_create("browsedoccat", $documents[document_loop]->document_info.main_cat.category_id)}' title="{if $document->document_info.category_name != ''}{$document->document_info.category_name} {else}Default{/if}">{$documents[document_loop]->document_info.main_cat.category_name}</a> &raquo;
					<a href='{$url->url_create("browsedoccat", $documents[document_loop]->document_info.category_id)}' title="ebook">{if $documents[document_loop]->document_info.category_name != ''}{$documents[document_loop]->document_info.category_name} {else}{lang_print id=650003160}{/if}</a>
										{else}
										<a href='{if $documents[document_loop]->document_info.category_id != ""}{$url->url_create("browsedoccat", $documents[document_loop]->document_info.category_id)}{else}{$url->url_create("browsedoccat", 0)}{/if}'>{if $documents[document_loop]->document_info.category_name != ''}{$documents[document_loop]->document_info.category_name} {else}{lang_print id=650003160}{/if}</a>
										{/if}
									 <br />
										{$documents[document_loop]->document_info.document_description|escape:html|truncate:100} 
								</div>
						</td>
					</tr>
				</table>
			</div>
			{/section} 
			{else}
			  <table cellpadding='0' cellspacing='0' align='center'><tr>
  <td class='result'>
     

      <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=650003159}

  </td></tr></table> 
			{/if}
			
			<div style='clear: both; height: 10px;'></div>
			{if $maxpage > 1}
			<div style="margin:5px 0;">
				<table width="100%" cellpadding="0" cellspacing="0" align="center" class="clr">
					<tr>
						<td class="paging_bg">
							<div style='text-align: center;'>
								{if $p != 1}<a href='browse_documents.php?s={$s}&v={$v}&p={math equation="p-1" p=$p}&i={$i}&tag={$tag_main}&search={$search}'>&#171; {lang_print id=650003027}</a>{else}&#171; {lang_print id=650003027}{/if}
								&nbsp;|&nbsp;&nbsp;
								
							{if $p_start == $p_end}
							<b>{lang_sprintf id=184 1=$p_start 2=$total_entries}</b>
							{else}
							<b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_entries}</b>
						{/if}
						&nbsp;&nbsp;|&nbsp;
							{if $p != $maxpage}<a href='browse_documents.php?s={$s}&v={$v}&p={math equation="p+1" p=$p}&i={$i}&tag={$tag_main}&search={$search}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
							</div>
						</td>
					</tr>
				</table>
			</div>			
			{/if}

		</td>
		
	</tr>
</table>		




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
			document.getElementById('img_' + cat_id).src = './images/icons/plus16.gif';
		}
		else {
			document.getElementById('subcat_' + cat_id).style.display = 'block';
			document.getElementById('img_' + cat_id).src = './images/icons/minus16.gif';
		}
	}
}
</script>
{/literal}

{if $total_featured > 0}

{* JAVASCRIPT FOR CAROUSEL *}
{literal}
<script type='text/javascript'>
<!--

  var visiblePhotos = 7;
  var current_id = 0;
  var myFx;

  window.addEvent('domready', function() {
    myFx = new Fx.Scroll('document_carousel');
    current_id = parseInt({/literal}{math equation="x-2" x=$current_index}{literal});
    var position = $('thumb'+current_id).getPosition($('document_carousel'));
    myFx.set(position.x, position.y);
  });


  function moveLeft() {
    if($('thumb'+(current_id-1))) {
      myFx.toElement('thumb'+(current_id-1));
      myFx.toLeft();
      current_id = parseInt(current_id-1);
    }
  }

  function moveRight() {
    if($('thumb'+(current_id+visiblePhotos))) {
      myFx.toElement('thumb'+(current_id+1));
      myFx.toRight();
      current_id = parseInt(current_id+1);
    }
  }

  
//-->
</script>
{/literal}

{/if}