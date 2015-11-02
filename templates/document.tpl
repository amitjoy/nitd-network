{* $Id: document.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{include file='header.tpl'}

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<!--left column start here-->
		<td width="660" class="document_view_left">
			<div class="fleft">
				<img src='./images/icons/document48.gif' border='0' class='icon_big'>
			</div>
			<div style="float:left;width:613px;">
				<div class="page_header">{$document->document_info.document_title}</div>
				<div class="fleft" style="padding-right:20px;">
					{lang_print id=650003165} {$document->document_info.document_datecreated|date_format} {lang_print id=650003164} <a href='{$url->url_create("profile", $document->document_owner->user_info.user_username)}' title='{$document->document_owner->user_displayname}'>{$document->document_owner->user_displayname}</a>
				</div>
				<div class="fleft" style="padding-right:20px;">
					{lang_print id=650003166} {$document->document_info.document_dateupdated|date_format} 
				</div>
				<div>
					{lang_print id=650003038} <a href="browse_documents.php" title="{lang_print id=650003167}">{lang_print id=650003167}</a> &raquo; 
					{if $main_cat}
					<a href='{$url->url_create("browsedoccat", $main_cat.category_id)}' title="{$main_cat.category_name}">{$main_cat.category_name}</a> &raquo;
					<a href='{$url->url_create("browsedoccat", $document->document_info.category_id)}' title="{if $document->document_info.category_name != ''}{$document->document_info.category_name} {else}{lang_print id=650003160}{/if}">{if $document->document_info.category_name != ''}{$document->document_info.category_name} {else}{lang_print id=650003160}{/if}</a>
					{else}
					<a href='{$url->url_create("browsedoccat", $document->document_info.category_id)}' title="{if $document->document_info.category_name != ''}{$document->document_info.category_name} {else}{lang_print id=650003160}{/if}">{if $document->document_info.category_name != ''}{$document->document_info.category_name} {else}{lang_print id=650003160}{/if}</a>
					{/if}
				</div>
				<div class="gry-txt">
					{lang_sprintf id=650003022 1=$document->document_info.document_views}, {lang_sprintf id=650003021 1=$document->document_info.document_totalcomments}
				</div>
			</div>
			
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
			
			
			<div class="document_des">
				 {$document->document_info.document_description|nl2br}  
			</div>
		<br/>
		{if $document->document_info.document_status == 1}
			<!--The document full text comes here if downloading has been enabled for this document-->
			{if $link && $doc_full_text}
				<noscript>
				{$doc_full_text}
				</noscript>
			{/if}
			{literal}
			<script type='text/javascript' src='http://www.scribd.com/javascripts/view.js'></script>
			<div id='embedded_flash'><a href="http://www.scribd.com"></a></div>
			<script type="text/javascript">
				var scribd_doc = scribd.Document.getDoc('{/literal}{$document->document_info.document_doc_id}{literal}', '{/literal}{$document->document_info.document_access_key}{literal}');
				var oniPaperReady = function(e){
				// scribd_doc.api.setPage(3);
				}
				scribd_doc.addParam( 'jsapi_version', 1 );
				scribd_doc.addParam( 'height', 600 );
				scribd_doc.addParam( 'width', 667 );
				scribd_doc.addParam("full_screen_type", 'flash');
				{/literal}
				{if !empty($document->document_info.document_secure)}
				{literal}
				scribd_doc.addParam("use_ssl", 'true'); 
				scribd_doc.grantAccess({/literal}'{$uid}'{literal}, {/literal}'{$sessionId}'{literal}, {/literal}'{$signature}'{literal}); 
				{/literal}
				{/if}
				{literal}
				scribd_doc.addEventListener( 'iPaperReady', oniPaperReady );
				scribd_doc.write( 'embedded_flash' );
			</script>
			{/literal}
			{else}
			  {if $document->document_owner->user_info.user_id == $user->user_info.user_id}
			   {if $document->document_info.document_status == 0}
			   <div class="alert-message-box">
					<img src="./images/icons/document-alert-img.gif" class="icon" align="" style="vertical-align:middle;" />
			   	{lang_print id=650003168}
			   </div>
			   {elseif $document->document_info.document_status == 2}
				 
			   <div class="alert-message-box">
					<img src="./images/icons/document-alert-img.gif" class="icon" align="" style="vertical-align:middle;" />
			    {lang_sprintf id=650003169 1=$document->document_info.document_id}
				 </div>
			   {else}
				 <div class="alert-message-box">
					<img src="./images/icons/document-alert-img.gif" class="icon" align="" style="vertical-align:middle;" />
			   	{lang_sprintf id=650003170 1=$document->document_info.document_id}
				 </div>
			   {/if}
			  {else}
			   {if $document->document_info.document_status == 0}
			   {lang_print id=650003168}
			   {else}
			   {lang_print id=650003171}
			   {/if}
			  {/if} 
			{/if}	
					
			<table cellpadding="0" cellspacing="0" class="m-top-1" width="100%">
				<tr valign="top">
					<td align="left">
					{if $user->user_exists == 1}
						{if $link && $params.download_allow}
							<img src="./images/icons/document_download.gif" class="doc_img" />
							<a href="{$link}" target="_blank" title="{lang_print id=650003213}">{lang_print id=650003213}</a>
						{/if}
					</td>
					{/if \}
					<td align="right">
					{if $document->document_info.document_license == 'by-nc'}
					<a rel="license" href="http://creativecommons.org/licenses/by-nc/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc/3.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc/3.0/" target="_blank">Creative Commons Attribution-Noncommercial 3.0 Unported License</a>
					{elseif $document->document_info.document_license == 'by-nc-nd'}
					<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/" target="_blank">Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 Unported License</a>
					{elseif $document->document_info.document_license == 'by-nc-sa'}
					<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank">Creative Commons Attribution-Noncommercial-Share Alike 3.0 Unported License</a>
					{elseif $document->document_info.document_license == 'by-nd'}
					<a rel="license" href="http://creativecommons.org/licenses/by-nd/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nd/3.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nd/3.0/" target="_blank">Creative Commons Attribution-No Derivative Works 3.0 Unported License</a>
					{elseif $document->document_info.document_license == 'by-sa'}
					<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank">Creative Commons Attribution-Share Alike 3.0 Unported License</a>
					{elseif $document->document_info.document_license == 'by'}
					<a rel="license" href="http://creativecommons.org/licenses/by/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/" target="_blank">Creative Commons Attribution 3.0 Unported License</a>
					{elseif $document->document_info.document_license == 'pd'}
					This document has been released into the public domain.
					{elseif $document->document_info.document_license == 'c'}
					This document is &copy; {$smarty.now|date_format:"%Y"} by {$owner->user_info.user_displayname} - all rights reserved.
					{/if}
					</td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" width="100%">	
				<tr valign="middle">
					<td nowrap="nowrap">
						<div class="btn">
							<img src="./images/icons/back16.gif" class="doc_img" />
							<a href='{$url->url_create("userdocs", $owner->user_info.user_username)}' title="{lang_sprintf id=650003172 1=$owner->user_info.user_fname}">{lang_sprintf id=650003172 1=$owner->user_info.user_fname}</a>
						</div>
						{if $user->user_exists == 1}
						<div class="btn">
							<img src="./images/icons/report16.gif" class="doc_img" />
							<a href="javascript:TB_show('{lang_print id=650003140}', 'user_report.php?return_url={$url->url_current()|escape:url}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');" tile="{lang_print id=650003177}">{lang_print id=650003177}</a>
						</div>
						{if $document->document_info.document_attachment == 1 && $document->document_info.document_status == 1 && $params.email_allow}
						<div class="btn">
							<img src="./images/icons/message_inbox16.gif" alt="" class="doc_img" />
							<a href="javascript:TB_show('{lang_print id=650003173}', 'user_document_email.php?user={$owner->user_info.user_username}&document_id={$document->document_info.document_id}&TB_iframe=true&height=300&width=450', '', './images/trans.gif');" title="{lang_print id=650003173}">{lang_print id=650003173}</a>
						</div>
						{/if}
						{/if}
					</td>
					<td align="right">
						<div>
  					<a rel="nofollow" target="_blank" href="http://delicious.com/save?v=5&noui&jump=close&url={$url->url_create('document', $owner->user_info.user_username, $document->document_info.document_id, $document->document_info.document_slug)|escape:url}&title={$document->document_info.document_title|escape:url}"><img src="./images/icons/socialbookmarking_delicious16.gif" border="0" alt="Delicious" /></a>
          <a rel="nofollow" target="_blank" href="http://digg.com/submit?phase=2&media=news&url={$url->url_create('document', $owner->user_info.user_username, $document->document_info.document_id, $document->document_info.document_slug)|escape:url}&title={$document->document_info.document_title|escape:url}"><img src="./images/icons/socialbookmarking_digg16.gif" border="0" alt="Digg" /></a>
          <a rel="nofollow" target="_blank" href="http://www.facebook.com/share.php?u={$url->url_create('document', $owner->user_info.user_username, $document->document_info.document_id, $document->document_info.document_slug)|escape:url}&t={$document->document_info.document_title|escape:url}"><img src="./images/icons/socialbookmarking_facebook16.gif" border="0" alt="Facebook" /></a>
          <a rel="nofollow" target="_blank" href="http://cgi.fark.com/cgi/fark/farkit.pl?u={$url->url_create('document', $owner->user_info.user_username, $document->document_info.document_id, $document->document_info.document_slug)|escape:url}&h={$document->document_info.document_title|escape:url}"><img src="./images/icons/socialbookmarking_fark16.gif" border="0" alt="Fark" /></a>
          <a rel="nofollow" target="_blank" href="http://www.myspace.com/Modules/PostTo/Pages/?u={$url->url_create('document', $owner->user_info.user_username, $document->document_info.document_id, $document->document_info.document_slug)|escape:url}&t={$document->document_info.document_title|escape:url}"><img src="./images/icons/socialbookmarking_myspace16.gif" border="0" alt="MySpace" /></a>
         <a target="_blank" href="http://twitthis.com/twit?url={$url->url_create('document', $owner->user_info.user_username, $document->document_info.document_id, $document->document_info.document_slug)|escape:url}"><img src="./images/icons/socialbookmarking_twitter16.png" border="0" alt="Twitter" /></a>
						</div>
					</td>
				</tr>
			</table>
			
			
			{* DISPLAY POST COMMENT BOX *}
				<div style='margin-left: auto; margin-right: auto;margin-top:10px;'>
					<div id="document_{$document->document_info.document_id}_postcomment"></div>
					<div id="document_{$document->document_info.document_id}_comments" style='margin-left: auto; margin-right: auto;'></div>
					{lang_javascript ids=39,155,175,182,183,184,185,187,784,787,829,830,831,832,833,834,835,854,856,891,1025,1026,1032,1034,1071}
			 <script type="text/javascript">
					SocialEngine.DocumentComments = new SocialEngineAPI.Comments({ldelim}
					'canComment' : {if $allowed_to_comment}true{else}false{/if},
					'commentCode' : {if $setting.setting_comment_code}true{else}false{/if},
					'commentHTML' : '{$setting.setting_comment_html}',
					'type' : 'document',
					'typeIdentifier' : 'document_id',
					'typeID' : {$document->document_info.document_id},
					
					'typeTab' : 'documents',
					'typeCol' : 'document',
					'initialTotal' : {$total_comments|default:0},
					'paginate' : false,
					'cpp' : 20
					{rdelim});
					SocialEngine.RegisterModule(SocialEngine.DocumentComments);
					// Backwards
					function addComment(is_error, comment_body, comment_date)
					{ldelim}
						SocialEngine.DocumentComments.addComment(is_error, comment_body, comment_date);
					{rdelim}
					
					function getComments(direction)
					{ldelim}
						SocialEngine.DocumentComments.getComments(direction);
					{rdelim}
				</script>
			</div>
		</td>
		
		
		
		
		<!--right column start here-->
		<td class="document_view_right" bgcolor="#666666">
			<table cellpadding="0" cellspacing="0" width="100%" class="m-btm-1">
				<tr>
					<td class="doc_profile_photo">
						<a href='{$url->url_create("profile",$owner->user_info.user_username)}' title='{$owner->user_info.user_displayname}'><img class='photo' src='{$owner->user_photo("./images/nophoto.gif")}' border='0'></a><br />
						<a href='{$url->url_create("profile",$owner->user_info.user_username)}' title='{$owner->user_info.user_displayname}'><strong>{$owner->user_info.user_displayname}</strong></a>
					</td>
				</tr>
			</table>
			{assign var=total_docs value=$documents|@count}
			{if $total_docs > 0}
			<table cellpadding="0" cellspacing="0" width="100%" class="m-btm-1">
				<tr>
    			<td class="doc_header">
      			{lang_sprintf id=650003174 1=$owner->user_info.user_displayname} 
    			</td>
				</tr>	
				<tr>
					<td class="doc_profile">
					{foreach item=doc from=$documents}
						<div class="tab_doc_listing">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td valign='top' width="65">
										<a href='{$url->url_create("document", $doc->document_owner->user_info.user_username, $doc->document_info.document_id, $doc->document_info.document_slug)}'>
										<img  src="{$doc->document_info.document_thumbnail}" class="photo" border="0" width="60" height="60" title="{$doc->document_info.document_title}" />
										</a>	
									</td>
									<td valign="top">
										<div>
											<a href='{$url->url_create("document", $doc->document_owner->user_info.user_username, $doc->document_info.document_id, $doc->document_info.document_slug)}'  title='{$doc->document_info.document_title}'><b>{$doc->document_info.document_title|truncate:15:"..":true}</b></a>
										</div>
										<!--add new details start here-->
										<div class="side_document_list" style="border:none;padding-top:0px;">
										<span class="gry-txt">
											{assign var='document_datecreated' value=$datetime->time_since($doc->document_info.document_datecreated)}{capture assign="created"}{lang_sprintf id=$document_datecreated[0] 1=$document_datecreated[1]}{/capture}
											{lang_sprintf id=650003162 1=$created},<br />
											{lang_sprintf id=650003022 1=$doc->document_info.document_views}, 
											{lang_sprintf id=650003021 1=$doc->document_info.total_comments}
											</span>
										</div>
										<!--add new details end here-->
									</td>
								</tr>
							</table>		
						</div>
					{/foreach}	
					{if $total_entries > 3}
						<div class="profile_inner_more">
							<a href='{$url->url_create("userdocs", $owner->user_info.user_username)}' title='{lang_print id=650003175}'>{lang_print id=650003176}  &raquo;</a>
						</div>	
					{/if}	
      		</td>
				</tr>	
			</table>
			{/if}
			{assign var=total_tags value=$document->document_info.tags|@count}
			{if $total_tags > 0}
			<table cellpadding="0" cellspacing="0" width="100%" class="m-btm-1">
				<tr>
    			<td class="doc_header">
      			{lang_print id=650003175} 
    			</td>
				</tr>
				<tr>
					<td class="doc_profile">
					{foreach item=tag from=$document->document_info.tags name=tag_loop}
					{if $smarty.foreach.tag_loop.iteration == 1}
					<a href='{$url->url_create("browsedoctag", $tag)}' title='{$tag}'><b>{$tag}</b></a>
					{else}
					, <a href='{$url->url_create("browsedoctag", $tag)}' title='{$tag}'><b>{$tag}</b></a>
					{/if}
						
					{/foreach}	
					</td>
				</tr>
			</table>	
			{/if}
		</td>
	</tr>
</table>	
{include file='footer.tpl'}