{if $owner->level_info.level_article_allow != 0 AND $total_article_entries > 0}

  <div class='profile_headline'>
    {lang_print id=11150102} ({$total_article_entries}) {if $total_article_entries > 5}&nbsp;[ <a href='{$url->url_create('articles', $owner->user_info.user_username)}'>more</a> ]{/if}
  </div>

  {foreach from=$article_entries item=articleentry}
  {assign var='status_date' value=$datetime->time_since($articleentry.article->article_info.article_date_start)}
  
      <div class='profile_articleentry'>
        <a href='article.php?article_id={$articleentry.article->article_info.article_id}'><img src='./images/icons/article16.gif' border='0' class='icon'>{$articleentry.article->article_info.article_title}</a>
      </div>
      <div class='profile_articleentry_date'>
        {lang_print id=11150520} {lang_sprintf id=$status_date[0] 1=$status_date[1]}
      </div>
      <br>
  {/foreach}

{/if}