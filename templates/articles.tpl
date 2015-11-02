{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
{if $user->level_info.level_article_allow != 0}
<td class='tab2' NOWRAP><a href='user_article.php'>{lang_print id=11150601}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_settings.php'>{lang_print id=11150603}</a></td>
<td class='tab'>&nbsp;</td>
{/if}
<td class='tab1' NOWRAP><a href='articles.php'>{lang_print id=11150604}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<img src='./images/icons/article48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=11150604}
{if $owner->user_exists}
 by <a href='{$url->url_create('profile', $owner->user_info.user_username)}'>{$owner->user_displayname}</a>
{/if}
</div>
<div>{lang_print id=11150605}</div>

<br><br>

<form method="post" name="searchform" action="articles.php?{if $owner->user_exists}user={$owner->user_info.user_username}&{/if}articlecat_id={$categories[cat_loop].articlecat_id}">
<div id="article_search" class="article_searchsort">
<table cellspacing="0" cellpadding="0" align="center">
<tbody>
<tr>
  <td><label for="featured">{lang_print id=11150631}</label></td><td><input type="checkbox" value="1" id="featured" name="f" {if $f==1}checked='checked'{/if} /></td>
  <td>{lang_print id=11150630}</td><td><input type="text" class="text" name="keyword" value="{$keyword}" size="20" /></td>
  <td>{lang_print id=11150632}</td><td><input type="text" class="text" name="tag" value="{$tag}" size="10" /></td>
  <td>{lang_print id=11150624}</td><td>
<select name="s">
  <option value="date" {if $s=='date'}selected="selected"{/if}>{lang_print id=11150625}</option>
  <option value="view" {if $s=='view'}selected="selected"{/if}>{lang_print id=11150626}</option>
  <option value="title" {if $s=='title'}selected="selected"{/if}>{lang_print id=11150627}</option>
</select> <input type="submit" value="{lang_print id=11150629}" class="button" /> 


</td>
</tr>
</tbody>
</table>
</div>
</form>




<br>

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td class='article_browse_left'>

  {* SHOW HEADER TEXT *}
  <div class='article_browse_title'>
    {if $nocat == 1}
      {lang_print id=11150602}
    {elseif $articlecat_title != ""}
      {$articlecat_title}
    {else}
      {lang_print id=11150609}
    {/if} 
    ({$total_articles} {if $total_articles != 1}{lang_print id=11150607}{else}{lang_print id=11150608}{/if})
  </div>

  {* SHOW ZERO ARTICLES FOUND NOTICE *}
  {if $total_articles == 0}
    <br>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td class='result'><img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=11150610}</td>
    </tr>
    </table>
  {/if}

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <br>
    <div class='center'>
    {if $p != 1}<a href='articles.php?{if $owner->user_exists}user={$owner->user_info.user_username}&{/if}{if $keyword != ""}keyword={$keyword}&{/if}{if $tag != ""}tag={$tag}&{/if}{if $f == 1}f=1&{/if}s={$s}&articlecat_id={$articlecat_id}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=11150616}</a>{else}<font class='disabled'>&#171; {lang_print id=11150616}</font>{/if}
    {if $p_start == $p_end}
      &nbsp;|&nbsp; {lang_print id=11150617} {$p_start} {lang_print id=11150618} {$total_articles} &nbsp;|&nbsp; 
    {else}
      &nbsp;|&nbsp; {lang_print id=11150619} {$p_start}-{$p_end} {lang_print id=11150618} {$total_articles} &nbsp;|&nbsp; 
    {/if}
    {if $p != $maxpage}<a href='articles.php?{if $owner->user_exists}user={$owner->user_info.user_username}&{/if}{if $keyword != ""}keyword={$keyword}&{/if}{if $tag != ""}tag={$tag}&{/if}{if $f == 1}f=1&{/if}s={$s}&articlecat_id={$articlecat_id}&p={math equation='p+1' p=$p}'>{lang_print id=11150620} &#187;</a>{else}<font class='disabled'>{lang_print id=11150620} &#187;</font>{/if}
    </div>
  {/if}

  
  {* LIST ARTICLE ENTRIES *}
  {foreach from=$article_array item=articleentry}
    {assign var='article_title' value=$articleentry.article->article_info.article_title}
    {assign var='article_date_start' value=$articleentry.article->article_info.article_date_start}
    
    {assign var=start_dateformat value="`$setting.setting_dateformat`, `$setting.setting_timeformat`"}
    
    <div class='article_row {if $articleentry.article->article_info.article_featured == 1} article_featured{/if}'>
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top" class='article_row1' width='100%'>
             <div class='article_title'><a href='{$url->url_create('article',$user->user_info.user_username,$articleentry.article->article_info.article_id,$articleentry.article->article_info.article_slug)}'>{$articleentry.article->article_info.article_title}</a> {if $articleentry.article->article_info.article_featured == 1}<img src="./images/icons/article_featured.gif" />{/if}</div>
             <div class='article_body'>{$articleentry.article->article_info.article_body|strip_tags:false|truncate:300:"..."}</div>
             <div class='article_meta'>{lang_print id=11150611} {$datetime->cdate("`$start_dateformat`", $article_date_start)} 
              {lang_print id=11150613} <a href='{$url->url_create('profile', $articleentry.article->article_info.user_username)}'>{$articleentry.article_author->user_displayname}</a>
              | <a href='article.php?article_id={$articleentry.article->article_info.article_id}#comments'>{$articleentry.article->article_info.total_comments} {lang_print id=11150633}</a>
             </div>
             <div class='article_tag'><strong>{lang_print id=11150615}</strong>
             {foreach from=$articleentry.tags item=tagname}
               <a href="articles.php?{if $owner->user_exists}user={$owner->user_info.user_username}&{/if}tag={$tagname}">{$tagname}</a>
             {/foreach}
             </div>
          </td>
          <td valign="top" class='article_row2' NOWRAP>
            <a href='article.php?article_id={$articleentry.article->article_info.article_id}'><img src='{$articleentry.article->article_photo('./images/nophoto.gif')}' class='photo' width='{$misc->photo_size($articleentry.article->article_photo('./images/nophoto.gif'),'100','100','w')}' border='0'></a>
          </td>
        </tr>
      </table>
    </div>
  {/foreach}  
  

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <br>
    <div class='center'>
    {if $p != 1}<a href='articles.php?{if $owner->user_exists}user={$owner->user_info.user_username}&{/if}{if $keyword != ""}keyword={$keyword}&{/if}{if $tag != ""}tag={$tag}&{/if}{if $f == 1}f=1&{/if}s={$s}&articlecat_id={$articlecat_id}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=11150616}</a>{else}<font class='disabled'>&#171; {lang_print id=11150616}</font>{/if}
    {if $p_start == $p_end}
      &nbsp;|&nbsp; {lang_print id=11150617} {$p_start} {lang_print id=11150618} {$total_articles} &nbsp;|&nbsp; 
    {else}
      &nbsp;|&nbsp; {lang_print id=11150619} {$p_start}-{$p_end} {lang_print id=11150618} {$total_articles} &nbsp;|&nbsp; 
    {/if}
    {if $p != $maxpage}<a href='articles.php?{if $owner->user_exists}user={$owner->user_info.user_username}&{/if}{if $keyword != ""}keyword={$keyword}&{/if}{if $tag != ""}tag={$tag}&{/if}{if $f == 1}f=1&{/if}s={$s}&articlecat_id={$articlecat_id}&p={math equation='p+1' p=$p}'>{lang_print id=11150620} &#187;</a>{else}<font class='disabled'>{lang_print id=11150620} &#187;</font>{/if}
    </div>
  {/if}

</td>
<td class='article_browse_right'>

  {* LIST CATEGORIES *}
  <table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 3px;'>
  <tr>
  <td class='article_browse_cat2' nowrap='nowrap' style='padding-left: 6px;'>
    <b><a href='articles.php?{if $owner->user_exists}user={$owner->user_info.user_username}{/if}'>{lang_print id=11150612}</a></b>
  </td>
  </tr>
  </table>
  {section name=cat_loop loop=$categories}
    <table cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 3px;'>
    <tr>
    <td class='article_browse_cat1' width='1'>
      <a href='javascript:void(0)' onClick="expandcats('subcats{$categories[cat_loop].articlecat_id}', '0')"><span id='subcats{$categories[cat_loop].articlecat_id}_icon'>{if $categories[cat_loop].articlecat_expanded != 1}<img src='./images/icons/article_plus16.gif' border='0'>{else}<img src='./images/icons/article_minus16.gif' border='0'>{/if}</span></a>
    </td>
    <td class='article_browse_cat2' nowrap='nowrap'>
      <b><a href='articles.php?{if $owner->user_exists}user={$owner->user_info.user_username}&{/if}{if $keyword != ""}keyword={$keyword}&{/if}{if $tag != ""}tag={$tag}&{/if}{if $f == 1}f=1&{/if}s={$s}&articlecat_id={$categories[cat_loop].articlecat_id}' onClick="expandcats('subcats{$categories[cat_loop].articlecat_id}', '1')">{$categories[cat_loop].articlecat_title|truncate:40:"...":true}</a></b>
      {if $categories[cat_loop].articlecat_totalarticles > 0}
        &nbsp;({$categories[cat_loop].articlecat_totalarticles} {if $categories[cat_loop].articlecat_totalarticles != 1}{lang_print id=11150607}{else}{lang_print id=11150608}{/if})
      {/if}
    </td>
    </tr>
    </table>

    {* LIST SUBCATEGORIES *}
    <div id='subcats{$categories[cat_loop].articlecat_id}' style='{if $categories[cat_loop].articlecat_expanded != 1}display: none; {/if}padding: 3px 3px 8px 10px;'>
      {section name=subcat_loop loop=$categories[cat_loop].articlecat_subcats}
        <div>
          <a href='articles.php?{if $owner->user_exists}user={$owner->user_info.user_username}&{/if}{if $keyword != ""}keyword={$keyword}&{/if}{if $tag != ""}tag={$tag}&{/if}{if $f == 1}f=1&{/if}s={$s}&articlecat_id={$categories[cat_loop].articlecat_subcats[subcat_loop].subcategory_id}'>{$categories[cat_loop].articlecat_subcats[subcat_loop].subcategory_title|truncate:20:"...":true}</a>
          {if $categories[cat_loop].articlecat_subcats[subcat_loop].subcategory_totalarticles > 0}
            <font class='small'>({$categories[cat_loop].articlecat_subcats[subcat_loop].subcategory_totalarticles} {if $categories[cat_loop].articlecat_subcats[subcat_loop].subcategory_totalarticles != 1}{lang_print id=11150607}{else}{lang_print id=11150608}{/if})</font>
          {/if}
        </div>
      {/section}
    </div>
  {/section}

  {* SHOW UNCATEGORIZED ARTICLES CATGORY IF ANY ARTICLES HAVE NO CAT *}
  {if $articles_totalnocat > 0}

    <table cellpadding='0' cellspacing='0' width='100%'{if not $smarty.section.cat_loop.last} style='margin-bottom: 3px;'{/if}>
    <tr>
    <td class='article_browse_cat1' width='1'>
      <img src='./images/icons/article_minus16_disabled.gif' border='0'>
    </td>
    <td class='article_browse_cat2' nowrap='nowrap'>
      <b><a href='articles.php?{if $owner->user_exists}user={$owner->user_info.user_username}&{/if}{if $keyword != ""}keyword={$keyword}&{/if}{if $tag != ""}tag={$tag}&{/if}{if $f == 1}f=1&{/if}s={$s}&articlecat_id=0'>{lang_print id=11150609}</a></b>
      {if $articles_totalnocat > 0}
        &nbsp;({$articles_totalnocat} {if $articles_totalnocat != 1}{lang_print id=11150607}{else}{lang_print id=11150608}{/if})
      {/if}
    </td>
    </tr>
    </table>
  {/if}
  
  <div class="article_popular_tags">
    <h3>{lang_print id=11150634}</h3>
    <div class="articletags">
    {foreach from=$popular_tags item=poptag}
      <a class="tag{$poptag.class}" href="articles.php?tag={$poptag.name}">{$poptag.name}</a>
    {/foreach}
    </div>
  </div>
  
</td>
</tr>
</table>

{literal}
<script type='text/javascript'>
<!--
function expandcats(id1, linkclicked) {
  var icon_var = id1 + '_icon';
  if(document.getElementById(id1).style.display == "none") {
    document.getElementById(id1).style.display = "block";
    document.getElementById(icon_var).innerHTML = "<img src='./images/icons/article_minus16.gif' border='0'>"; 
    setCookie(id1, "1");
  } else {
    if(linkclicked == 0) {
      document.getElementById(id1).style.display = "none";
      document.getElementById(icon_var).innerHTML = "<img src='./images/icons/article_plus16.gif' border='0'>"; 
      setCookie(id1, "0");
    }
  }
  document.getElementById(icon_var).blur();
}
//-->
</script>
{/literal}

{include file='footer.tpl'}