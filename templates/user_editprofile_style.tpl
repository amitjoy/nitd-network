{include file='header.tpl'}

{* $Id: user_editprofile_style.tpl 130 2009-03-21 23:36:57Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
{section name=cat_loop loop=$cats}
  <td class='tab2' NOWRAP><a href='user_editprofile.php?cat_id={$cats[cat_loop].subcat_id}'>{lang_print id=$cats[cat_loop].subcat_title}</a></td><td class='tab'>&nbsp;</td>
{/section}
{if $user->level_info.level_photo_allow != 0}<td class='tab2' NOWRAP><a href='user_editprofile_photo.php'>{lang_print id=762}</a></td><td class='tab'>&nbsp;</td>{/if}
<td class='tab1' NOWRAP><a href='user_editprofile_style.php'>{lang_print id=763}</a></td>
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<img src='./images/icons/editprofile48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=763}</div>
<div>{lang_print id=964}</div>
<br />

{* SHOW SUCCESS MESSAGE *}
{if $result != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='success'><img src='./images/success.gif' border='0' class='icon'> {lang_print id=191}</td>
  </tr>
  </table><br>
{/if}

{* SHOW PROFILE CSS TEXTAREA IF ALLOWED *}
<form action='user_editprofile_style.php' method='post'>
{if $user->level_info.level_profile_style != 0}
  <div><b>{lang_print id=965}</b></div>
  <div class='form_desc'>{lang_print id=966}</div>
  <textarea name='style_profile' id='style_profile' rows='17' cols='50' style='width: 100%; font-family: courier, serif;'>{$style_info.profilestyle_css}</textarea>
  <br><br>
{/if}


{* SHOW PREMADE CSS TEMPLATES *}
{if $user->level_info.level_profile_style_sample != 0 && $sample_css|@count != 0}
  <div><b>{lang_print id=979}</b></div>
  <div class='form_desc'>{lang_print id=980}</div>

  <br>

  {* ORIGINAL LAYOUT OPTION *}
  <div id='css_0' class='editprofile_examplecss{if $style_info.profilestyle_css|strip == ""}_selected{/if}'>
    <a href='javascript:void(0)' onClick='switch_css("0");this.blur()'><img src='./images/sample_styles/original_css.gif' border='0'></a><br>
    <a href='javascript:void(0)' onClick='switch_css("0");this.blur()'>Original Layout</a>
    <div style='display:none;' id='sample_0'></div>
  </div>

  {* EXAMPLE CSS OPTIONS *}
  {section name=sample_loop loop=$sample_css}
    <div id='css_{$sample_css[sample_loop].stylesample_id}' class='editprofile_examplecss{if $style_info.profilestyle_stylesample_id == $sample_css[sample_loop].stylesample_id}_selected{/if}'>
      <a href='javascript:void(0)' onClick='switch_css("{$sample_css[sample_loop].stylesample_id}");this.blur()'><img src='./images/sample_styles/{$sample_css[sample_loop].stylesample_thumb}' border='0'></a><br>
      <a href='javascript:void(0)' onClick='switch_css("{$sample_css[sample_loop].stylesample_id}");this.blur()'>{$sample_css[sample_loop].stylesample_name}</a>
      <div style='display:none;' id='sample_{$sample_css[sample_loop].stylesample_id}'>{$sample_css[sample_loop].stylesample_css|nl2br}</div>
    </div>
  {/section}

  <div style='clear: both'></div>
  <input type='hidden' name='style_profile_sample' id='style_profile_sample' value='{$style_info.profilestyle_stylesample_id}'>

  {* JAVASCRIPT FOR SWITCHING CSS *}
  {literal}
  <script type='text/javascript'>
  <!--
  {/literal}
  {if $user->level_info.level_profile_style == 0} 
  var selected = {$style_info.profilestyle_stylesample_id};
  {literal} 
  function switch_css(id) {
    if(id == selected) { id = 0; }
    if($('css_'+selected)) { $('css_'+selected).className = 'editprofile_examplecss'; }
    if($('css_'+id)) { $('css_'+id).className = 'editprofile_examplecss_selected'; }
    $('style_profile_sample').value = id;
    selected = id;
  }
  {/literal}
  {else}
  {literal} 
  function switch_css(id) {
    if(confirm("{/literal}{lang_print id=981}{literal}")) {
      var new_css = $('sample_'+id).innerHTML.replace(/\n/g, '').replace(/<br>/ig, '\n');
      $('style_profile').value=new_css;
    }
  }
  {/literal}{/if}{literal}

  //-->
  </script>
  {/literal}
  <br>
{/if}


<input type='submit' class='button' value='{lang_print id=173}' />
<input type='hidden' name='task' value='dosave' />
</form>

{include file='footer.tpl'}