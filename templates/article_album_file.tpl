{include file='header.tpl'}


{* JAVASCRIPT FOR ADDING COMMENT *}
{literal}
<script type='text/javascript'>
<!--
var comment_changed = 0;
var last_comment = {/literal}{$comments|@count}{literal};
var next_comment = last_comment+1;
var total_comments = {/literal}{$total_comments}{literal};

function removeText(commentBody) {
  if(comment_changed == 0) {
    commentBody.value='';
    commentBody.style.color='#000000';
    comment_changed = 1;
  }
}

function addText(commentBody) {
  if(commentBody.value == '') {
    commentBody.value = '{/literal}{lang_print id=11150918}{literal}';
    commentBody.style.color = '#888888';
    comment_changed = 0;
  }
}

function checkText() {
  if(comment_changed == 0) { 
    var commentBody = document.getElementById('comment_body');
    commentBody.value=''; 
  }
  var commentSubmit = document.getElementById('comment_submit');
  commentSubmit.value = '{/literal}{lang_print id=11150919}{literal}';
  commentSubmit.disabled = true;
  
}

function addComment(is_error, comment_body, comment_date) {
  if(is_error == 1) {
    var commentError = document.getElementById('comment_error');
    commentError.style.display = 'block';
    if(comment_body == '') {
      commentError.innerHTML = '{/literal}{lang_print id=11150920}{literal}';
    } else {
      commentError.innerHTML = '{/literal}{lang_print id=11150921}{literal}';
    }
    var commentSubmit = document.getElementById('comment_submit');
    commentSubmit.value = '{/literal}{lang_print id=11150910}{literal}';
    commentSubmit.disabled = false;
  } else {
    var commentError = document.getElementById('comment_error');
    commentError.style.display = 'none';
    commentError.innerHTML = '';

    var commentBody = document.getElementById('comment_body');
    commentBody.value = '';
    addText(commentBody);

    var commentSubmit = document.getElementById('comment_submit');
    commentSubmit.value = '{/literal}{lang_print id=11150910}{literal}';
    commentSubmit.disabled = false;

    if(document.getElementById('comment_secure')) {
      var commentSecure = document.getElementById('comment_secure');
      commentSecure.value=''
      var secureImage = document.getElementById('secure_image');
      secureImage.src = secureImage.src + '?' + (new Date()).getTime();
    }

    total_comments++;
    var totalComments = document.getElementById('total_comments');
    totalComments.innerHTML = total_comments;

    var newComment = document.createElement('div');
    var divIdName = 'comment_'+next_comment;
    newComment.setAttribute('id',divIdName);
    var newTable = "<table cellpadding='0' cellspacing='0' width='100%'><tr><td class='album_item1' width='80'>";
    {/literal}
      {if $user->user_info.user_id != 0}
        newTable += "<a href='{$url->url_create('profile',$user->user_info.user_username)}'><img src='{$user->user_photo('./images/nophoto.gif')}' class='photo' border='0' width='{$misc->photo_size($user->user_photo('./images/nophoto.gif'),'75','75','w')}'></a></td><td class='album_item2'><table cellpadding='0' cellspacing='0' width='100%'><tr><td class='album_comment_author'><b><a href='{$url->url_create('profile',$user->user_info.user_username)}'>{$user->user_info.user_username}</a></b> - {$datetime->cdate("`$setting.setting_timeformat` `$article_album_file22` `$setting.setting_dateformat`", $datetime->timezone($smarty.now, $global_timezone))}</td><td class='album_comment_author' align='right' nowrap='nowrap'>&nbsp;[ <a href='user_messages_new.php?to={$user->user_info.user_username}'>{lang_print id=11150923}</a> ]</td>";
      {else}
        newTable += "<img src='./images/nophoto.gif' class='photo' border='0' width='75'></td><td class='album_item2'><table cellpadding='0' cellspacing='0' width='100%'><tr><td class='album_comment_author'><b>{lang_print id=11150914}</b> - {$datetime->cdate("`$setting.setting_timeformat` `$article_album_file22` `$setting.setting_dateformat`", $datetime->timezone($smarty.now, $global_timezone))}</td><td class='album_comment_author' align='right' nowrap='nowrap'>&nbsp;</td>";
      {/if}
      newTable += "</tr><tr><td colspan='2' class='album_comment_body'>"+comment_body+"</td></tr></table></td></tr></table>";
    {literal}
    newComment.innerHTML = newTable;
    var mediaComments = document.getElementById('media_comments');
    var prevComment = document.getElementById('comment_'+last_comment);
    mediaComments.insertBefore(newComment, prevComment);
    next_comment++;
    last_comment++;
  }
}
//-->
</script>
{/literal}

<div class='page_header'><img src='./images/icons/article_album22.gif' border='0' class='icon'> <a href='article.php?article_id={$article->article_info.article_id}'>{$article->article_info.article_title}</a>'s <a href='article_album.php?article_id={$article->article_info.article_id}'>{lang_print id=11150904}</a></div>

{* SET MEDIA PATH *}
{assign var='articlemedia_dir' value=$article->article_dir($article->article_info.article_id)}
{assign var='articlemedia_path' value="`$articlemedia_dir``$articlemedia_info.articlemedia_id`.`$articlemedia_info.articlemedia_ext`"}



{* DISPLAY IMAGE *}
{if $articlemedia_info.articlemedia_ext == "jpg" OR 
    $articlemedia_info.articlemedia_ext == "jpeg" OR 
    $articlemedia_info.articlemedia_ext == "gif" OR 
    $articlemedia_info.articlemedia_ext == "png" OR 
    $articlemedia_info.articlemedia_ext == "bmp"}
  {assign var='file_src' value="<img src='`$articlemedia_path`' border='0'>"}

{* DISPLAY AUDIO *}
{elseif $articlemedia_info.articlemedia_ext == "mp3" OR 
        $articlemedia_info.articlemedia_ext == "mp4" OR 
        $articlemedia_info.articlemedia_ext == "wav"}
  {assign var='articlemedia_download' value="[ <a href='`$articlemedia_path`'>`$article_album_file5`</a> ]"}
  {assign var='file_src' value="<a href='`$articlemedia_path`'><img src='./images/icons/audio_big.gif' border='0'></a>"}

{* DISPLAY WINDOWS VIDEO *}
{elseif $articlemedia_info.articlemedia_ext == "mpeg" OR 
	$articlemedia_info.articlemedia_ext == "mpg" OR 
	$articlemedia_info.articlemedia_ext == "mpa" OR 
	$articlemedia_info.articlemedia_ext == "avi" OR 
	$articlemedia_info.articlemedia_ext == "ram" OR 
	$articlemedia_info.articlemedia_ext == "rm"}
  {assign var='articlemedia_download' value="[ <a href='`$articlemedia_path`'>`$article_album_file6`</a> ]"}
  {assign var='file_src' value="
    <object id='video'
      classid='CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6'
      type='application/x-oleobject'>
      <param name='url' value='`$articlemedia_path`'>
      <param name='sendplaystatechangearticles' value='True'>
      <param name='autostart' value='true'>
      <param name='autosize' value='true'>
      <param name='uimode' value='mini'>
      <param name='playcount' value='9999'>
    </OBJECT>
  "}

{* DISPLAY QUICKTIME FILE *}
{elseif $articlemedia_info.articlemedia_ext == "mov" OR 
	$articlemedia_info.articlemedia_ext == "moov" OR 
	$articlemedia_info.articlemedia_ext == "movie" OR 
	$articlemedia_info.articlemedia_ext == "qtm" OR 
	$articlemedia_info.articlemedia_ext == "qt"}
  {assign var='articlemedia_download' value="[ <a href='`$articlemedia_path`'>`$article_album_file6`</a> ]"}
  {assign var='file_src' value="
    <embed src='`$articlemedia_path`' controller='true' autosize='1' scale='1' width='550' height='350'>
  "}

{* EMBED FLASH FILE *}
{elseif $articlemedia_info.articlemedia_ext == "swf"}
  {assign var='file_src' value="
    <object width='350' height='250' classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0' id='mymoviename'> 
      <param name='movie' value='$articlemedia_path'>  
      <param name='quality' value='high'> 
      <param name='bgcolor' value='#ffffff'> 
      <embed src='`$articlemedia_path`' quality='high' bgcolor='#ffffff' name='Flash Movie' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer'> 
      </embed> 
    </object> 
  "}

{* DISPLAY UNKNOWN FILETYPE *}
{else}
  {assign var='articlemedia_download' value="[ <a href='`$articlemedia_path`'>`$article_album_file7`</a> ]"}
  {assign var='file_src' value="<a href='`$articlemedia_path`'><img src='./images/icons/file_big.gif' border='0'></a>"}
{/if}





<br>

{* SHOW ARROWS, HIDE IF NECESSARY *}
<table cellpadding='0' cellspacing='0' align='center'>
<tr>
<td width='30' align='right'>{if $link_first != "#"}<a href='{$link_first}'><img src='./images/icons/arrow_start.gif' class='icon' border='0'></a>{/if}</td>
<td width='30' align='right'>{if $link_back != "#"}<a href='{$link_back}'><img src='./images/icons/arrow_back.gif' class='icon' border='0'></a>{/if}</td>
<td align='center' nowrap='nowrap' style='padding-right: 8px;'><b>[ <a href='article_album.php?article_id={$article->article_info.article_id}'>{lang_print id=11150908} {$article->article_info.article_title}{lang_print id=11150909}</a> ]</b></td>
<td width='30'>{if $link_next != "#"}<a href='{$link_next}'><img src='./images/icons/arrow_next.gif' class='icon' border='0'></a>{/if}</td>
<td width='30'>{if $link_end != "#"}<a href='{$link_end}'><img src='./images/icons/arrow_end.gif' class='icon' border='0'></a>{/if}</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' align='center' width='100%'>
<tr>
<td align='center'>
  <div class='album_title'>{$articlemedia_info.articlemedia_title}</div>
  {if $articlemedia_info.articlemedia_desc != ""}{$articlemedia_info.articlemedia_desc}<br><br>{/if}
  {if $link_next != "#"}<a href='{$link_next}'>{$file_src}</a>{else}{$file_src}{/if}
  {if $articlemedia_download != ""}<br><br>{$articlemedia_download}{/if}

  <br><br>

  {* SHOW REPORT LINK *}
  <table cellpadding='0' cellspacing='0' align='center'>
  <tr>
  <td>
    <table cellpadding='0' cellspacing='0'>
    <tr><td class='button'>
      <a href='user_report.php?return_url={$url->url_current()}'><img src='./images/icons/report16.gif' border='0' class='icon'>{lang_print id=11150911}</a>
    </td></tr>
    </table>
  </td>
  </tr>
  </table>
</td>
</tr>
</table>


<br>

{* BEGIN COMMENTS *}
<table cellpadding='0' cellspacing='0' width='100%'>
<tr>  
<td class='header'>
  {lang_print id=11150912} (<span id='total_comments'>{$total_comments}</span>)
</td>
</tr>
{if $allowed_to_comment != 0}
  <tr>
  <td class='album_postcomment'>
    <form action='article_album_file.php' method='post' target='AddCommentWindow' onSubmit='checkText()'>
    <textarea name='comment_body' id='comment_body' rows='2' cols='65' onfocus='removeText(this)' onblur='addText(this)' style='color: #888888; width: 100%;'>{lang_print id=11150918}</textarea>

    <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
    {if $setting.setting_comment_code == 1}
      <td width='75' valign='top'><img src='./images/secure.php' id='secure_image' border='0' height='20' width='67' class='signup_code'></td>
      <td width='68' style='padding-top: 4px;'><input type='text' name='comment_secure' id='comment_secure' class='text' size='6' maxlength='10'></td>
      <td width='10'><img src='./images/icons/tip.gif' border='0' class='icon' onMouseover="tip('{lang_print id=11150924}')"; onMouseout="hidetip()"></td>
    {/if}
    <td align='right' style='padding-top: 5px;'>
    <input type='submit' id='comment_submit' class='button' value='{lang_print id=11150910}'>
    <input type='hidden' name='articlemedia_id' value='{$articlemedia_info.articlemedia_id}'>
    <input type='hidden' name='article_id' value='{$article->article_info.article_id}'>
    <input type='hidden' name='task' value='dopost'>
    </form>
    </td>
    </tr>
    </table>
    <div id='comment_error' style='color: #FF0000; display: none;'></div>
    <iframe name='AddCommentWindow' style='display: none' src=''></iframe>
  </td>
  </tr>
{/if}
<tr>
<td class='album' id='media_comments'>

  {* LOOP THROUGH ARTICLE MEDIA COMMENTS *}
  {section name=comment_loop loop=$comments}
    <div id='comment_{math equation='t-c' t=$comments|@count c=$smarty.section.comment_loop.index}'>
    <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
    <td class='album_item1' width='80'>
      {if $comments[comment_loop].comment_author->user_info.user_id != 0}
        <a href='{$url->url_create('profile',$comments[comment_loop].comment_author->user_info.user_username)}'><img src='{$comments[comment_loop].comment_author->user_photo('./images/nophoto.gif')}' class='photo' border='0' width='{$misc->photo_size($comments[comment_loop].comment_author->user_photo('./images/nophoto.gif'),'75','75','w')}'></a>
      {else}
        <img src='./images/nophoto.gif' class='photo' border='0' width='75'>
      {/if}
    </td>
    <td class='album_item2'>
      <table cellpadding='0' cellspacing='0' width='100%'>
      <tr>
      <td class='album_comment_author'><b>{if $comments[comment_loop].comment_author->user_info.user_id != 0}<a href='{$url->url_create('profile',$comments[comment_loop].comment_author->user_info.user_username)}'>{$comments[comment_loop].comment_author->user_info.user_username}</a>{else}{lang_print id=11150914}{/if}</b> - {$datetime->cdate("`$setting.setting_timeformat` `$article_album_file22` `$setting.setting_dateformat`", $datetime->timezone($comments[comment_loop].comment_date, $global_timezone))}</td>
      <td class='album_comment_author' align='right' nowrap='nowrap'>&nbsp;[ <a href='user_messages_new.php?to={$comments[comment_loop].comment_author->user_info.user_username}'>{lang_print id=11150923}</a> ]</td>
      </tr>
      <tr>
      <td colspan='2' class='album_comment_body'>{$comments[comment_loop].comment_body}</td>
      </tr>
      </table>
    </td>
    </tr>
    </table>
    </div>
  {/section}

</td>
</tr>
</table>
{* END COMMENTS *}


{include file='footer.tpl'}