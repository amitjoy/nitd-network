{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_article.php'>{lang_print id=11151201}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_settings.php'>{lang_print id=11151202}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='articles.php'>{lang_print id=11151207}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<table cellpadding='0' cellspacing='0' width='100%'><tr><td class='page'>

<img src='./images/icons/article48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=11151203}</div>
<div>{lang_print id=11151204}</div>

<br>

{* SHOW BUTTONS *}
<table cellpadding='0' cellspacing='0'>
<tr>
<td style='padding-right: 10px;'>
  <table cellpadding='0' cellspacing='0'><tr>
  <td class='button' nowrap='nowrap'><a href='user_article_add.php'><img src='./images/icons/article_newentry16.gif' border='0' class='icon'>{lang_print id=11151205}</a></td>
  </tr></table>
</td>
<td style='padding-right: 10px;'>
  <table cellpadding='0' cellspacing='0'><tr>
  <td class='button' nowrap='nowrap'><a href="javascript:showhide('article_search');"><img src='./images/icons/search16.gif' border='0' class='icon'>{lang_print id=11151206}</a></td>
  </tr></table>
</td>
<td>
  <b>My Article Link: <a href='{$url->url_create('articles',$user->user_info.user_username)}'>{$url->url_create('articles',$user->user_info.user_username)}</a></b>
</td>
</tr>
</table>

<br>

{* SHOW SEARCH FIELD IF ANY ENTRIES EXIST *}
  <form action='user_article.php' name='searchform' method='POST'>
  <div class='article_search' id='article_search' {if $search == ""}style='display: none;'{/if}>
    <table cellpadding='0' cellspacing='0' align='center'>
    <tr>
    <td><b>{lang_print id=11151208}</b>&nbsp;&nbsp;</td>
    <td><input type='text' name='search' maxlength='100' size='30' value='{$search}'>&nbsp;</td>
    <td><input type='submit' class='button' value='{lang_print id=11151226}'></td>
    </tr>
    </table>
    <input type='hidden' name='s' value='{$s}'>
    <input type='hidden' name='p' value='{$p}'>
  </div>
  </form>


{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div class='center'>
  {if $p != 1}<a href='user_article.php?s={$s}&search={$search}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=11151209}</a>{else}<font class='disabled'>&#171; {lang_print id=11151209}</font>{/if}
  {if $p_start == $p_end}
    &nbsp;|&nbsp; {lang_print id=11151210} {$p_start} {lang_print id=11151211} {$total_articleentries} &nbsp;|&nbsp; 
  {else}
    &nbsp;|&nbsp; {lang_print id=11151212} {$p_start}-{$p_end} {lang_print id=11151211} {$total_articleentries} &nbsp;|&nbsp; 
  {/if}
  {if $p != $maxpage}<a href='user_article.php?s={$s}&search={$search}&p={math equation='p+1' p=$p}'>{lang_print id=11151213} &#187;</a>{else}<font class='disabled'>{lang_print id=11151213} &#187;</font>{/if}
  </div>
<br>
{/if}


{* JAVASCRIPT FOR CHECK ALL BUTTON *}
{literal}
<script language='JavaScript'> 
<!---
var checkboxcount = 1;
function doCheckAll() {
  if(checkboxcount == 0) {
    with (document.entryform) {
    for (var i=0; i < elements.length; i++) {
    if (elements[i].type == 'checkbox') {
    elements[i].checked = false;
    }}
    checkboxcount = checkboxcount + 1;
    }
  } else
    with (document.entryform) {
    for (var i=0; i < elements.length; i++) {
    if (elements[i].type == 'checkbox') {
    elements[i].checked = true;
    }}
    checkboxcount = checkboxcount - 1;
  }
}
function SymError() { return true; }
window.onerror = SymError;
var SymRealWinOpen = window.open;
function SymWinOpen(url, name, attributes) { return (new Object()); }
window.open = SymWinOpen;
appendEvent = function(el, evname, func) {
 if (el.attachEvent) { // IE
   el.attachEvent('on' + evname, func);
 } else if (el.addEventListener) { // Gecko / W3C
   el.addEventListener(evname, func, true);
 } else {
   el['on' + evname] = func;
 }
};
appendEvent(window, 'load', windowonload);
function windowonload() { 
  document.searchform.search.focus(); 
  document.searchform.search.value+=''; 
} 
// -->
</script>
{/literal}

{* DISPLAY MESSAGE IF NO BLOG ENTRIES *}
{if $total_articleentries == 0}
  <table cellpadding='0' cellspacing='0' align='center'><tr>
  <td class='result'>
     
    {* SHOW NO BLOG ENTRIES MESSAGE *}
    {if $search != ""}
      <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=11151214}
    {else}
      <img src='./images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=11151215} <a href='user_article_add.php'>{lang_print id=11151216}</a> {lang_print id=11151217}
    {/if}

  </td></tr></table>

{* DISPLAY ENTRIES *}
{else}

  <table cellpadding='0' cellspacing='0' class='article_table'>
  <tr>
  <td class='article_header'><a href='user_article.php?search={$search}&p={$p}&s={$d}'>{lang_print id=11151218}</a></td>
  <td class='article_header'><a href='user_article.php?search={$search}&p={$p}&s={$t}'>{lang_print id=11151219}</a></td>
  <td class='article_header'><a href='user_article.php?search={$search}&p={$p}&s={$g}'>{lang_print id=11151232}</a></td>
  <td class='article_header'><a href='user_article.php?search={$search}&p={$p}&s={$a}'>{lang_print id=11151229}</a></td>
  <td class='article_header'><a href='user_article.php?search={$search}&p={$p}&s={$f}'>{lang_print id=11151230}</a></td> 
  <td class='article_header'><a href='user_article.php?search={$search}&p={$p}&s={$c}'>{lang_print id=11151220}</a></td>
  <td class='article_header'>{lang_print id=11151227}</td>
  </tr>

  {* LIST ARTICLE ENTRIES *}
  {foreach from=$articleentries item=articleentry}
    {assign var='article_title' value=$articleentry.article->article_info.article_title|truncate:50:"...":false}
    {assign var='article_date' value=$articleentry.article->article_info.article_date_start}
    <tr>
    <td class='article_entry' nowrap='nowrap'>{if $articleentry.article->article_info.article_date_start > 0}{$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $article_date)}{else}{lang_print id=11151231}{/if}</td>
    <td class='article_entry' width='100%'><a href='{$url->url_create('article',$user->user_info.user_username,$articleentry.article->article_info.article_id,$articleentry.article->article_info.article_slug)}'><img src='./images/icons/{if $articleentry.article->article_info.article_draft == 1}article_draft1.gif{else}article_draft0.gif{/if}' border='0' class='icon'>{$article_title}</a> &nbsp;</td>
    <td class='article_entry'>{if $articleentry.article->article_info.articlecat_title != ""}{$articleentry.article->article_info.articlecat_title}{else}{lang_print id=11151233}{/if}</td>
    <td class='article_entry'><img src='./images/icons/{if $articleentry.article->article_info.article_approved == 1}article_approved1.gif{else}article_approved0.gif{/if}' border='0' class='icon'></td>
    <td class='article_entry'><img src='./images/icons/{if $articleentry.article->article_info.article_featured == 1}article_featured1.gif{else}article_featured0.gif{/if}' border='0' class='icon'></td>    
    
    <td class='article_entry' nowrap='nowrap'><a href='user_article_edit_comments.php?article_id={$articleentry.article->article_info.article_id}'>{$articleentry.article->article_info.total_comments} {lang_print id=11151222}</a>&nbsp;&nbsp;</td>
    <td class='article_entry' nowrap='nowrap'><a href='user_article_edit.php?article_id={$articleentry.article->article_info.article_id}'>{lang_print id=11151223}</a> &nbsp;|&nbsp; <a href='user_article_edit_delete.php?article_id={$articleentry.article->article_info.article_id}'>{lang_print id=11151224}</a> &nbsp;</td>
    </tr>
    
  {/foreach}
  
  </table>

  <br>

{/if}

</td></tr></table>

{include file='footer.tpl'}