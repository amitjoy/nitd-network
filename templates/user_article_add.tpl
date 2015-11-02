{include file='header.tpl'}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
<td class='tab1' NOWRAP><a href='user_article.php'>{lang_print id=11151305}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='user_article_settings.php'>{lang_print id=11151327}</a></td>
<td class='tab'>&nbsp;</td>
<td class='tab2' NOWRAP><a href='articles.php'>{lang_print id=11151307}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<table cellpadding='0' cellspacing='0' width='100%'><tr><td class='page'>

<div>
<img src='./images/icons/article48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=11151308}</div>
<div>{lang_print id=11151309}</div>

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

<br><br><br>

{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <div class='error'><img src='./images/error.gif' border='0' class='icon'> {lang_print id=$error_message}</div>
  </td></tr></table>
<br>
{/if}


<table cellpadding='0' cellspacing='0' class='form'>
<tr><form action='user_article_add.php' method='POST'>
<td class='form1'>{lang_print id=11151310}</td>
<td class='form2'><input type='text' class='text' name='article_title' value='{$article_title}' maxlength='100' size='60'></td>
</tr>
{if $cats|@count != 0}
  <tr>
  <td class='form1'>{lang_print id=11151312}</td>
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
<td class='article_header'>{lang_print id=11151313}</td>
</tr>
<tr>
<td class='article_box'>

<table cellpadding='0' cellspacing='0'>

 <tr>
  <td class='form1' width='250'>{lang_print id=11151335}</td>
  <td class='form2'>
    <input type='text' name='article_tags' value='{$article_tags}' size='64' class='text' />
    <br>{lang_print id=11151336}
  </td>
 </tr>

{* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
{if $user->level_info.level_article_search == 1}
  <tr>
  <td class='form1' width='250'>{lang_print id=11151318}</td>
  <td class='form2'>
    <table cellpadding='0' cellspacing='0'>
      <tr><td><input type='radio' name='article_search' id='article_search_1' value='1'{if $article_search == 1} CHECKED{/if}></td><td><label for='article_search_1'>{lang_print id=11151319}</label></td></tr>
      <tr><td><input type='radio' name='article_search' id='article_search_0' value='0'{if $article_search == 0} CHECKED{/if}></td><td><label for='article_search_0'>{lang_print id=11151320}</label></td></tr>
    </table>
  </td>
 </tr>
{/if}

{* SHOW ALLOW PRIVACY SETTINGS *}
{if $privacy_options|@count > 1}
  <tr>
  <td class='form1' width='250'>{lang_print id=11151321}</td>
  <td class='form2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$privacy_options name=privacy_loop key=k item=v}
      <tr>
      <td><input type='radio' name='article_privacy' id='privacy_{$k}' value='{$k}'{if $article_privacy == $k} checked='checked'{/if}></td>
      <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
    <div class='form_desc'>{lang_print id=11151322}</div>
  </td>
  </tr>
{/if}

{* SHOW ALLOW COMMENT SETTINGS *}
{if $comment_options|@count > 1}
  <tr>
  <td class='form1' width='250'>{lang_print id=11151323}</td>
  <td class='form2'>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$comment_options name=comment_loop key=k item=v}
      <tr>
      <td><input type='radio' name='article_comments' id='comments_{$k}' value='{$k}'{if $article_comments == $k} checked='checked'{/if}></td>
      <td><label for='comments_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
    <div class='form_desc'>{lang_print id=11151324}</div>
  </td>
  </tr>
{/if}

</table>

</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0'>
<tr>
<td>
  <input type='submit' class='button' value='{lang_print id=11151325}' name='publish'>
  <input type='submit' class='button' value='{lang_print id=11151334}' name='draft'>&nbsp;
  <input type='hidden' name='task' value='doadd'>
  </form>
</td>
<td>
  <form action='user_article.php' method='get'>
  <input type='submit' class='button' value='{lang_print id=11151326}'>
  </form>
</td>
</tr>
</table>

</td></tr></table>

{include file='footer.tpl'}