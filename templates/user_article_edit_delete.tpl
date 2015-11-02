{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit.php?article_id={$article->article_info.article_id}'>{lang_print id=11151601}</a></td><td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit_files.php?article_id={$article->article_info.article_id}'>{lang_print id=11151604}</a></td><td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit_comments.php?article_id={$article->article_info.article_id}'>{lang_print id=11151605}</a></td><td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_article_edit_delete.php?article_id={$article->article_info.article_id}'>{lang_print id=11151607}</a></td><td class='tab'>&nbsp;</td>
<td class='tab3'><a href='user_article.php'>&#171; {lang_print id=11151608}</a></td>
</tr>
</table>

<table cellpadding='0' cellspacing='0' width='100%'><tr><td class='page'>

<div>
<img src='./images/icons/article48.gif' border='0' class='icon_big'>
   <div class='page_header'>{lang_print id=11151609} <a href='article.php?article_id={$article->article_info.article_id}'>{$article->article_info.article_title|truncate:30:"...":true}</a></div>
   {lang_print id=11151610}
</div>

<br>

<table cellpadding='0' cellspacing='0'>
<tr>
<td>
  <form action='user_article_edit_delete.php' method='post'>
  <input type='submit' class='button' value='{lang_print id=11151611}'>&nbsp;
  <input type='hidden' name='task' value='dodelete'>
  <input type='hidden' name='article_id' value='{$article->article_info.article_id}'>
  </form>
</td>
<td>
  <form action='user_article_edit.php' method='get'>
  <input type='submit' class='button' value='{lang_print id=11151612}'>
  <input type='hidden' name='article_id' value='{$article->article_info.article_id}'>
  </form>
</td>
</tr>
</table>

</td></tr></table>

{include file='footer.tpl'}