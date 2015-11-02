
<table cellpadding='0' cellspacing='0' class='portal_table' align='center' width='100%'>
<tr><td class='header'>Latest Articles</td></tr>
<tr>
<td class='portal_box'>
  <div id="recent_articles">
  {foreach from=$article_entries item=articleentry}
    {assign var='article_date_start' value=$articleentry.article->article_info.article_date_start}
    {assign var=start_dateformat value="`$setting.setting_dateformat`, `$setting.setting_timeformat`"}  
    <div class='recent_articleentry'>
      <a href='article.php?article_id={$articleentry.article->article_info.article_id}'><img src='./images/icons/article16.gif' border='0' class='icon'>{$articleentry.article->article_info.article_title}</a>
    </div>
    <div class='recent_articleentry_meta'>
      {lang_print id=11150611} {$datetime->cdate("`$start_dateformat`", $article_date_start)} 
      {lang_print id=11150613} <a href='{$url->url_create('profile', $articleentry.article->article_info.user_username)}'>{$articleentry.article_author->user_displayname}</a>
    </div>
  {/foreach}
  </div>
</td>
</tr>
</table>  