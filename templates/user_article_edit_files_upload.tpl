{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit.php?article_id={$article->article_info.article_id}'>{lang_print id=11151901}</a></td><td class='tab'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_article_edit_files.php?article_id={$article->article_info.article_id}'>{lang_print id=11151904}</a></td><td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit_comments.php?article_id={$article->article_info.article_id}'>{lang_print id=11151905}</a></td><td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit_delete.php?article_id={$article->article_info.article_id}'>{lang_print id=11151907}</a></td><td class='tab'>&nbsp;</td>
<td class='tab3'><a href='user_article.php'>&#171; {lang_print id=11151908}</a></td>
</tr>
</table>

<table cellpadding='0' cellspacing='0' width='100%'><tr><td class='page'>



<table cellpadding='0' cellspacing='0'>
<tr>
<td width='100%'>

    <img src='./images/icons/article48.gif' border='0' class='icon_big'>
   <div class='page_header'>{lang_print id=11151909} <a href='article.php?article_id={$article->article_info.article_id}'>{$article->article_info.article_title|truncate:30:"...":true}</a></div>
   {lang_print id=11151910}

</td>
<td align='right' valign='top'>

  <table cellpadding='0' cellspacing='0' width='120'>
  <tr><td class='button' nowrap='nowrap'><img src='./images/icons/album_back16.gif' border='0' class='icon'>&nbsp; <a href='user_article_edit_files.php?article_id={$article->article_info.article_id}'>{lang_print id=11151911}</a></td></tr>
  </table>

</td>
</tr>
</table>

<br><br>

<div>
  {lang_print id=11151912} {$space_left} MB {lang_print id=11151913}<br>
  {lang_print id=11151914} {$allowed_exts}<br>
  {lang_print id=11151915} {$max_filesize} KB.
</div>

{* SHOW IMAGES UPLOADED MESSAGE *}
{if $file1_result != "" OR $file2_result != "" OR $file3_result != "" OR $file4_result != "" OR $file5_result != ""}
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <div class='success' style='text-align: left;'> 
      <div>{$file1_result}</div>
      <div>{$file2_result}</div>
      <div>{$file3_result}</div>
      <div>{$file4_result}</div>
      <div>{$file5_result}</div>
    </div>
  </td>
  </tr>
  </table>
{/if}

<br>

<form action='user_article_edit_files_upload.php' name='uploadform' method='post' onsubmit='doupload()' enctype='multipart/form-data'>

<div id='div1'>
  <table cellpadding='0' cellspacing='0' class='form'>
  <tr>
  <td class='form1' width='65'><img src='./images/icons/album_files22.gif' border='0' class='icon'>&nbsp; {lang_print id=11151916}</td>
  <td class='form2'><input type='file' name='file1' size='60' class='text' onchange="showdiv('div2');showdiv('div_submit');"></td>
  </tr>
  </table>
</div>

<div id='div2' style='display: none;'>
  <table cellpadding='0' cellspacing='0' class='form'>
  <tr>
  <td class='form1' width='65'><img src='./images/icons/album_files22.gif' border='0' class='icon'>&nbsp; {lang_print id=11151917}</td>
  <td class='form2'><input type='file' name='file2' size='60' class='text' onchange="showdiv('div3');"></td>
  </tr>
  </table>
</div>

<div id='div3' style='display: none;'>
  <table cellpadding='0' cellspacing='0' class='form'>
  <tr>
  <td class='form1' width='65'><img src='./images/icons/album_files22.gif' border='0' class='icon'>&nbsp; {lang_print id=11151918}</td>
  <td class='form2'><input type='file' name='file3' size='60' class='text' onchange="showdiv('div4');"></td>
  </tr>
  </table>
</div>

<div id='div4' style='display: none;'>
  <table cellpadding='0' cellspacing='0' class='form'>
  <tr>
  <td class='form1' width='65'><img src='./images/icons/album_files22.gif' border='0' class='icon'>&nbsp; {lang_print id=11151919}</td>
  <td class='form2'><input type='file' name='file4' size='60' class='text' onchange="showdiv('div5');"></td>
  </tr>
  </table>
</div>

<div id='div5' style='display: none;'>
  <table cellpadding='0' cellspacing='0' class='form'>
  <tr>
  <td class='form1' width='65'><img src='./images/icons/album_files22.gif' border='0' class='icon'>&nbsp; {lang_print id=11151920}</td>
  <td class='form2'><input type='file' name='file5' size='60' class='text'></td>
  </tr>
  </table>
</div>

<div id='div_submit' style='display: none;'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='form1' width='65'>&nbsp;</td>
  <td class='form1'>
    <input type='submit' class='button' name='submit' value='{lang_print id=11151921}'>&nbsp;
    <input type='hidden' name='task' value='doupload'>
    <input type='hidden' name='MAX_FILE_SIZE' value='50000000'>
    <input type='hidden' name='article_id' value='{$article->article_info.article_id}'>
  </td>
  <td class='form2'>
    &nbsp;<input type='text' class='album_uploadstatus' name='status' id='status' readonly='readonly'>
    </form>
  </td>
  </tr>
  </table>
</div>

{literal}
<script lang='javascript'>
<!--
function doupload() {
document.uploadform.submit.disabled = true;
document.uploadform.status.value = "{/literal}{lang_print id=11151922}{literal}";
window.setTimeout("doMsg1()", 400);
}
function doMsg1() {
document.uploadform.status.value  = document.uploadform.status.value + '.';
if(document.uploadform.status.value == '{/literal}{lang_print id=11151922}{literal}'+'....') { document.uploadform.status.value = '{/literal}{lang_print id=11151922}{literal}'; }
window.setTimeout("doMsg1()", 400);
}
// -->
</script>
{/literal}


</td></tr></table>

{include file='footer.tpl'}