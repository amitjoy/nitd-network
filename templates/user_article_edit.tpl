{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_article_edit.php?article_id={$article->article_info.article_id}'>{lang_print id=11151410}</a></td><td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit_files.php?article_id={$article->article_info.article_id}'>{lang_print id=11151413}</a></td><td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit_comments.php?article_id={$article->article_info.article_id}'>{lang_print id=11151414}</a></td><td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_edit_delete.php?article_id={$article->article_info.article_id}'>{lang_print id=11151416}</a></td><td class='tab'>&nbsp;</td>
<td class='tab3'><a href='user_article.php'>{lang_print id=11151446}</a></td>
</tr>
</table>

<table cellpadding='0' cellspacing='0' width='100%'><tr><td class='page'>

<div>
<img src='./images/icons/article48.gif' border='0' class='icon_big'>
   <div class='page_header'>{lang_print id=11151417} <a href='{$url->url_create('article',$user->user_info.user_username,$article->article_info.article_id,$article->article_info.article_slug)}'>{$article->article_info.article_title|truncate:30:"...":true}</a></div>
   {lang_print id=11151418}
</div>

<br><br>

{* IF ARTICLE WAS JUST CREATED, SHOW SUCCESS MESSAGE *}
{if $justadded == 1}
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <img src='./images/success.gif' border='0' class='icon'>{lang_print id=11151419}
  </td></tr></table>
{/if}

{literal}
<script type='text/javascript'>
<!--
var subcats = new Array();
{/literal}
{section name=cat_loop loop=$cats}
subcats[{$cats[cat_loop].articlecat_id}] = new Array('0', '' {section name=subcat_loop loop=$cats[cat_loop].subcats}{if $smarty.section.subcat_loop.first == TRUE},{/if} '{$cats[cat_loop].subcats[subcat_loop].subarticlecat_id}', '{$cats[cat_loop].subcats[subcat_loop].subarticlecat_title}'{if $smarty.section.subcat_loop.last != true},{/if}{/section});
{/section}
{literal}
function populate() {
  var articlecat_id = document.getElementById('articlecats').options[document.getElementById('articlecats').selectedIndex].value;
  if(!articlecat_id) return;
  var list = subcats[articlecat_id];
  document.getElementById('subarticlecats').options.length = 0;
  var selected_op = 0;
  for(i=0;i<list.length;i+=2)
  {
    if({/literal}{$subarticlecat_id}{literal} == list[i]) {
      var selected = true;
      var selected_op = i/2;
    } else {
      var selected = false;
    }
    document.getElementById('subarticlecats').options[i/2] = new Option(list[i+1],list[i],selected);
  }
  document.getElementById('subarticlecats').options[selected_op].selected = true;
  if(document.getElementById('subarticlecats').options.length == 1) {
    document.getElementById('subarticlecats').style.visibility = 'hidden';
  } else {
    document.getElementById('subarticlecats').style.visibility = 'visible';
  }
}

//-->
</script>
{/literal}

<br>

{* SHOW RESULT MESSAGE *}
{if $result != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <img src='./images/success.gif' border='0' class='icon'>{lang_print id=11151409}
  </td></tr></table><br>
{/if}

{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <img src='./images/error.gif' class='icon' border='0'>{lang_print id=$error_message}
  </td></tr></table><br>
{/if}

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td class='article_header'>{lang_print id=11151420}</td>
</tr>
<tr>
<td class='article_box'>

  {* SHOW PHOTO ON LEFT AND UPLOAD FIELD ON RIGHT *}
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='editprofile_photoleft'>
    {lang_print id=11151421}<br>
    <table cellpadding='0' cellspacing='0' width='202'>
    <tr><td class='editprofile_photo'><img src='{$article->article_photo("./images/nophoto.gif")}' border='0'></td></tr>
    </table>
    {if $article->article_photo() != ""}  <br>[ <a href='user_article_edit.php?article_id={$article->article_info.article_id}&task=remove'>{lang_print id=11151422}</a> ]{/if}
  </td>
  <td class='editprofile_photoright'>
    <form action='user_article_edit.php' method='POST' enctype='multipart/form-data'>
    {lang_print id=11151423}<br><input type='file' class='text' name='photo' size='30'>
    <input type='submit' class='button' value='{lang_print id=11151424}'>
    <input type='hidden' name='task' value='upload'>
    <input type='hidden' name='MAX_FILE_SIZE' value='5000000'>
    <input type='hidden' name='article_id' value='{$article->article_info.article_id}'>
    </form>
    <br>{lang_print id=11151425} {$article->articleowner_level_info.level_article_photo_exts}
  </td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>


<form action='user_article_edit.php' method='post'>
<table cellpadding='0' cellspacing='0'>
<tr>
<td class='form1'>{lang_print id=11151427}</td>
<td class='form2'><input type='text' class='text' name='article_title' value='{$article_title}' maxlength='100' size='30'></td>
</tr>

{if $cats|@count != 0}
  <tr>
  <td class='form1'>{lang_print id=11151429}</td>
  <td class='form2'>
    <select name='articlecat_id' id='articlecats' onChange='populate();'><option value='0'></option>
    {section name=cat_loop loop=$cats}
      <option value='{$cats[cat_loop].articlecat_id}'{if $articlecat_id == $cats[cat_loop].articlecat_id} SELECTED{/if}>{$cats[cat_loop].articlecat_title}</option>
    {/section}
    </select>&nbsp;
    <select name='subarticlecat_id' id='subarticlecats' style='visibility: hidden;'><option value='0'></option></select>
    {literal}
    <script type='text/javascript'>
    <!--
    if({/literal}{$articlecat_id}{literal} != 0) {
      populate();
    }
    //-->
    </script>
    {/literal}
  </td>
  </tr>
{/if}

</table>

  <br>

  <script type="text/javascript">
  <!--
  var sBasePath = "./include/fckeditor/" ;
  var sToolbar ;
  var oFCKeditor = new FCKeditor( 'article_body' ) ;
  oFCKeditor.BasePath	= sBasePath ;
  oFCKeditor.Height = "300" ;
  if ( sToolbar != null )
    oFCKeditor.ToolbarSet = sToolbar ;
  oFCKeditor.Value = '{$article_body}' ;
  oFCKeditor.Create() ;
  //-->
  </script>


<br /><br />

{* SHOW ARTICLE SETTINGS *}
<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td class='article_header'>{lang_print id=11151430}</td>
</tr>
<tr>
<td class='article_box'>

<table cellpadding='0' cellspacing='0'>
 <tr>
  <td class='form1' width='120'>{lang_print id=11151455}</td>
  <td class='form2'>
    <input type='text' name='article_tags' value='{$article_tags}' size='64' class='text' />
    <br>{lang_print id=11151456}
  </td>
 </tr>
{* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
{if $user->level_info.level_article_search == 1}
  <tr>
  <td class='form1' width='120'>{lang_print id=11151436}</td>
  <td class='form2'>
    <table cellpadding='0' cellspacing='0'>
      <tr><td><input type='radio' name='article_search' id='article_search_1' value='1'{if $article_search == 1} CHECKED{/if}></td><td><label for='article_search_1'>{lang_print id=11151437}</label></td></tr>
      <tr><td><input type='radio' name='article_search' id='article_search_0' value='0'{if $article_search == 0} CHECKED{/if}></td><td><label for='article_search_0'>{lang_print id=11151438}</label></td></tr>
    </table>
  </td>
 </tr>
 <tr><td colspan='2'>&nbsp;</td></tr>
{/if}

{* SHOW ALLOW PRIVACY SETTINGS *}
{if $privacy_options|@count > 1}
  <tr>
  <td class='form1' width='120'>{lang_print id=11151439}</td>
  <td class='form2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$privacy_options name=privacy_loop key=k item=v}
      <tr>
      <td><input type='radio' name='article_privacy' id='privacy_{$k}' value='{$k}'{if $article_privacy == $k} checked='checked'{/if}></td>
      <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
    <div class='form_desc'>{lang_print id=11151440}</div>
  </td>
  </tr>
{/if}

{* SHOW ALLOW COMMENT SETTINGS *}
{if $comment_options|@count > 1}
  <tr>
  <td class='form1' width='120'>{lang_print id=11151441}</td>
  <td class='form2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$comment_options name=comment_loop key=k item=v}
      <tr>
      <td><input type='radio' name='article_comments' id='comments_{$k}' value='{$k}'{if $article_comments == $k} checked='checked'{/if}></td>
      <td><label for='comments_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
    <div class='form_desc'>{lang_print id=11151442}</div>
  </td>
  </tr>
{/if}
</table>

</td>
</tr>
</table>

<br>

{* SHOW SUBMIT BUTTONS *}
<table cellpadding='0' cellspacing='0'>
<tr>
<td>
{if $article->article_info.article_draft == 1}
  <input type='submit' class='button' value='{lang_print id=11151453}' name='publish'>
  <input type='submit' class='button' value='{lang_print id=11151454}' name='draft'>&nbsp;
{else}
  <input type='submit' class='button' value='{lang_print id=11151443}' name='update'>&nbsp;
{/if}
  <input type='hidden' name='task' value='dosave'>
  <input type='hidden' name='article_id' value='{$article->article_info.article_id}'>
  </form>
</td>
<td>
  <form action='user_article.php' method='GET'>
  <input type='submit' class='button' value='{lang_print id=11151445}'>
  </form>
</td>
</tr>
</table>

<br><br>

{include file='footer.tpl'}