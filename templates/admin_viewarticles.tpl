{include file='admin_header.tpl'}

<h2>{lang_print id=11150403}</h2>
{lang_print id=11150404}

<br><br>


{* SHOW SEARCH FIELD IF ANY ENTRIES EXIST *}

  <form action='admin_viewarticles.php' name='searchform' method='POST'>
    <input type='hidden' name='s' value='{$s}'>
    <input type='hidden' name='p' value='{$p}'>

    <table cellpadding='0' cellspacing='0' align='center'>
    <tr>
    <td><div id='article_search' class='box'><b>{lang_print id=11150408}</b>&nbsp;&nbsp;
    <input type='text' name='search' maxlength='100' size='30' value='{$search}'>&nbsp;
    <input type='submit' class='button' value='{lang_print id=11150426}'>
    </div></td>
    </tr>
    </table>

  </div>
  </form>

<br>

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <div class='center'>
  {if $p != 1}<a href='admin_viewarticles.php?s={$s}&search={$search}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=11150409}</a>{else}<font class='disabled'>&#171; {lang_print id=11150409}</font>{/if}
  {if $p_start == $p_end}
    &nbsp;|&nbsp; {lang_print id=11150410} {$p_start} {lang_print id=11150411} {$total_articleentries} &nbsp;|&nbsp;
  {else}
    &nbsp;|&nbsp; {lang_print id=11150412} {$p_start}-{$p_end} {lang_print id=11150411} {$total_articleentries} &nbsp;|&nbsp;
  {/if}
  {if $p != $maxpage}<a href='admin_viewarticles.php?s={$s}&search={$search}&p={math equation='p+1' p=$p}'>{lang_print id=11150413} &#187;</a>{else}<font class='disabled'>{lang_print id=11150413} &#187;</font>{/if}
  </div>
<br>
{/if}


{* DISPLAY MESSAGE IF NO BLOG ENTRIES *}
{if $total_articleentries == 0}
  <table cellpadding='0' cellspacing='0' align='center'><tr>
  <td class='result'>

    {* SHOW NO BLOG ENTRIES MESSAGE *}
    {if $search != ""}
      <img src='../images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=11150414}
    {else}
      <img src='../images/icons/bulb16.gif' border='0' class='icon'>{lang_print id=11150415}
    {/if}

  </td></tr></table>

{* DISPLAY ENTRIES *}
{else}

  <form action='admin_viewarticles.php' name='entryform' method='post'>
  <table cellpadding='0' cellspacing='0' class='list'>
  <tr>
  <td class='header'><a class='header' href='admin_viewarticles.php?search={$search}&p={$p}&s={$d}'>{lang_print id=11150418}</a></td>
  <td class='header'><a class='header' href='admin_viewarticles.php?search={$search}&p={$p}&s={$t}'>{lang_print id=11150419}</a></td>
  <td class='header'><a class='header' href='admin_viewarticles.php?search={$search}&p={$p}&s={$g}'>{lang_print id=11150432}</a></td>
  <td class='header'><a class='header' href='admin_viewarticles.php?search={$search}&p={$p}&s={$r}'>{lang_print id=11150428}</a></td>
  <td class='header'><a class='header' href='admin_viewarticles.php?search={$search}&p={$p}&s={$a}'>{lang_print id=11150429}</a></td>
  <td class='header'><a class='header' href='admin_viewarticles.php?search={$search}&p={$p}&s={$f}'>{lang_print id=11150430}</a></td>
  <td class='header'><a class='header' href='admin_viewarticles.php?search={$search}&p={$p}&s={$c}'>{lang_print id=11150420}</a></td>
  <td class='header'><a class='header' href='admin_viewarticles.php?search={$search}&p={$p}&s={$o}'>{lang_print id=11150427}</a></td>
  <td class='header'>{lang_print id=11150405}</td>
  </tr>

  {* LIST ARTICLE ENTRIES *}
  {foreach from=$articleentries item=articleentry}
    {assign var='article_title' value=$articleentry.article->article_info.article_title|truncate:50:"...":false}
    {assign var='article_date' value=$articleentry.article->article_info.article_date_start}
    <tr class='{cycle values="background1,background2"}'>
    <td class='item' nowrap='nowrap'>{if $articleentry.article->article_info.article_date_start > 0}{$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $article_date)}{else}{lang_print id=11150431}{/if}</td>
    <td class='item' width='100%'><a target='_blank' href='../article.php?article_id={$articleentry.article->article_info.article_id}'>{$article_title}</a> &nbsp;</td>

        <td class='item'>{if $articleentry.article->article_info.articlecat_title != ""}{$articleentry.article->article_info.articlecat_title}{else}{lang_print id=11150433}{/if}</td>


    <td class='item'>{if $articleentry.article->article_info.article_draft == 1}{lang_print id=11150406}{else}{lang_print id=11150407}{/if}</td>
    <td class='item'><a href='admin_viewarticles.php?search={$search}&p={$p}&s={$s}&task=approve&article_id={$articleentry.article->article_info.article_id}&value={if $articleentry.article->article_info.article_approved == 1}0{else}1{/if}'><img src='../images/icons/{if $articleentry.article->article_info.article_approved == 1}article_approved1.gif{else}article_approved0.gif{/if}' border='0' class='icon'></a></td>
    <td class='item'><a href='admin_viewarticles.php?search={$search}&p={$p}&s={$s}&task=feature&article_id={$articleentry.article->article_info.article_id}&value={if $articleentry.article->article_info.article_featured == 1}0{else}1{/if}'><img src='../images/icons/{if $articleentry.article->article_info.article_featured == 1}article_featured1.gif{else}article_featured0.gif{/if}' border='0' class='icon'></a></td>
    <td class='item' nowrap='nowrap'><a target='_blank' href='../article_comments.php?article_id={$articleentry.article->article_info.article_id}'>{$articleentry.article->article_info.total_comments} {lang_print id=11150422}</a>&nbsp;&nbsp;</td>
    <td class='item'><a href='{$url->url_create('profile', $articleentry.article_author->user_info.user_username)}' target='_blank'>{$articleentry.article_author->user_info.user_username}</a></td>
    <td class='item' nowrap='nowrap'>[ <a href='admin_loginasuser.php?user_id={$articleentry.article_author->user_info.user_id}&return_url={$url->url_encode("`$url->url_base`user_article_edit.php?article_id=`$articleentry.article->article_info.article_id`")}' target='_blank'>{lang_print id=11150423}</a> ]
    [ <a href='admin_viewarticles.php?search={$search}&p={$p}&s={$s}&task=delete&article_id={$articleentry.article->article_info.article_id}' onclick="return confirm('Are you sure you want to delete this article?');">{lang_print id=11150424}</a> ]
    </td>

    </tr>
  {/foreach}

  </table>

  </form>
{/if}






{include file='admin_footer.tpl'}