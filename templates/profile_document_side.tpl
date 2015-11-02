{* $Id: profile_document_side.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
<table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 10px;'>
  <tr>
    <td class='header'>
      {lang_print id=650003010}
    </td>
  </tr>
  
  <tr>
    <td class='profile' style="padding-top:0px;">
			{foreach item=document from=$documents}
			<div class="tab_doc_listing" style="margin-top:-1px;">
				<table cellpadding='0' cellspacing='0' width='100%'>
					<tr>
						<td valign='top' width="65">
							<a href='{$url->url_create("document", $document->document_owner->user_info.user_username, $document->document_info.document_id, $document->document_info.document_slug)}'>
							<img src='{$document->document_info.document_thumbnail}'  class="photo" onClick='this.blur()' width="60" height="60"></a>
						</td>
						<td valign="top">
							<div>
								<a href='{$url->url_create("document", $document->document_owner->user_info.user_username, $document->document_info.document_id, $document->document_info.document_slug)}'>
								<b>{$document->document_info.document_title|truncate:15:"..":true}</b>
								</a>
							</div>
							<div class="side_document_list" style="border:none;padding-top:0px;">
							<span class="gry-txt">
								{assign var='document_datecreated' value=$datetime->time_since($document->document_info.document_datecreated)}{capture assign="created"}{lang_sprintf id=$document_datecreated[0] 1=$document_datecreated[1]}{/capture}
								{lang_sprintf id=650003162 1=$created},
								{lang_sprintf id=650003022 1=$document->document_info.document_views}, 
								{lang_sprintf id=650003021 1=$document->document_info.total_comments}
								</span>
							</div>
						</td>
					</tr>
				</table>
			</div>	
			{/foreach}
			<div class="tab_doc_listing" style="padding:5px 5px 5px 0;margin:0;" align="right">			
				<a href='{$url->url_create("userdocs", $owner->user_info.user_username)}'><b>{lang_print id=650003176} &raquo;</b></a>
			</div>
		</td>
  </tr>
</table>

{* END DOCUMENT *}