{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit.php?article_id={$article->article_info.article_id}'>{lang_print id=11151501}</a></td><td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit_files.php?article_id={$article->article_info.article_id}'>{lang_print id=11151504}</a></td><td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_article_edit_comments.php?article_id={$article->article_info.article_id}'>{lang_print id=11151505}</a></td><td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit_delete.php?article_id={$article->article_info.article_id}'>{lang_print id=11151507}</a></td><td class='tab'>&nbsp;</td>
<td class='tab3'><a href='user_article.php'>&#171; {lang_print id=11151508}</a></td>
</tr>
</table>

<table cellpadding='0' cellspacing='0' width='100%'><tr><td class='page'>

<div>
   <img src='./images/icons/article48.gif' border='0' class='icon_big'>
   <div class='page_header'>{lang_print id=11151509} <a href='article.php?article_id={$article->article_info.article_id}'>{$article->article_info.article_title|truncate:30:"...":true}</a></div>
   {lang_print id=11151510}
</div>

<br><br>

{* JAVASCRIPT FOR CHECK ALL MESSAGES FEATURE *}
{literal}
  <script language='JavaScript'> 
  <!---
  var checkboxcount = 1;
  function doCheckAll() {
    if(checkboxcount == 0) {
      with (document.comments) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = false;
      }}
      checkboxcount = checkboxcount + 1;
      }
      select_all.checked=false;
    } else
      with (document.comments) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = true;
      }}
      checkboxcount = checkboxcount - 1;
      select_all.checked=true;
      }
  }
  // -->
  </script>
{/literal}


<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
{if $total_comments > 0}
  <td width='150'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>&nbsp;[ <a href='javascript:doCheckAll()'>{lang_print id=11151520}</a> ]</td>
    </tr>
    </table>
  </td>
{/if}

{* DISPLAY PAGINATION MENU IF APPLICABLE *}
{if $maxpage > 1}
  <td align='right'>
  {if $p != 1}<a href='user_article_edit_comments.php?article_id={$article->article_info.article_id}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=11151511}</a>{else}<font class='disabled'>&#171; {lang_print id=11151511}</font>{/if}
  {if $p_start == $p_end}
    &nbsp;|&nbsp; {lang_print id=11151512} {$p_start} {lang_print id=11151513} {$total_comments} &nbsp;|&nbsp; 
  {else}
    &nbsp;|&nbsp; {lang_print id=11151514} {$p_start}-{$p_end} {lang_print id=11151513} {$total_comments} &nbsp;|&nbsp; 
  {/if}
  {if $p != $maxpage}<a href='user_article_edit_comments.php?article_id={$article->article_info.article_id}&p={math equation='p+1' p=$p}'>{lang_print id=11151515} &#187;</a>{else}<font class='disabled'>{lang_print id=11151515} &#187;</font>{/if}
  </td>
{/if}

</tr>
</table>


{if $total_comments == 0}
  {* DISPLAY MESSAGE IF THERE ARE NO COMMENTS *}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'><img src='./images/icons/bulb16.gif' class='icon' border='0'> {lang_print id=11151516}</td></tr>
  </table>


{else}
  {* LIST COMMENTS *}
  <form action='user_article_edit_comments.php' method='post' name='comments'>
  {section name=comment_loop loop=$comments}
    <div class='editprofile_bar'></div>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td valign='top'><input type='checkbox' name='comment_{$comments[comment_loop].comment_id}' value='1' style='margin-top: 2px;'></td>
    <td class='editprofile_item1'>
      {if $comments[comment_loop].comment_author->user_info.user_id != 0}
        <a href='{$url->url_create('profile', $comments[comment_loop].comment_author->user_info.user_username)}'><img src='{$comments[comment_loop].comment_author->user_photo('./images/nophoto.gif')}' class='photo' border='0' width='{$misc->photo_size($comments[comment_loop].comment_author->user_photo('./images/nophoto.gif'),'75','75','w')}'></a>
      {else}
        <img src='./images/nophoto.gif' class='photo' border='0' width='75'>
      {/if}
    </td>
    <td class='editprofile_item2'>
    <div><b>{if $comments[comment_loop].comment_author->user_info.user_id != 0}<a href='{$url->url_create('profile',$comments[comment_loop].comment_author->user_info.user_username)}'>{$comments[comment_loop].comment_author->user_info.user_username}</a>{else}{lang_print id=11151517}{/if}</b>
     - {$datetime->cdate("`$setting.setting_timeformat` `$user_article_edit_comments19` `$setting.setting_dateformat`", $datetime->timezone($comments[comment_loop].comment_date, $global_timezone))}
    </div>
    {$comments[comment_loop].comment_body}
    </td>
    </tr>
    </table>
  {/section}

  <br>

  <input type='submit' class='button' value='{lang_print id=11151518}'>
  <input type='hidden' name='task' value='delete'>
  <input type='hidden' name='p' value='{$p}'>
  <input type='hidden' name='article_id' value='{$article->article_info.article_id}'>
  </form>
{/if}

</td></tr></table>

{include file='footer.tpl'}