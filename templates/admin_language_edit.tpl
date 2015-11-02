{include file='admin_header.tpl'}

{* $Id: admin_language_edit.tpl 235 2009-11-13 04:30:39Z phil $ *}

<h2><a href='admin_language.php'>{lang_print id=49}</a>: {$language.language_name}</h2>
{lang_print id=178}

<br><br>

{* DISPLAY NOTE ABOUT NOT HAVING ANY PHRASES *}
{if $total_vars == 0 && $phrase == "" && $phrase_id == ""}
{lang_print id=179}


{* DISPLAY PHRASES *}
{else}

  {* SEARCH BOX *}
  <table cellpadding='0' cellspacing='0' align='center'><tr><td align='center'>
  <div class='box'>
  <form action='admin_language_edit.php' method='get'>
  <div style='float: left; text-align: left; padding-right: 5px;'>{lang_print id=1018}<br><input type='text' class='text' name='phrase_id' value='{$phrase_id}' size='5'></div>
  <div style='float: left; text-align: center; padding-right: 5px; padding-top: 17px; font-weight: bold;'>{lang_print id=349}</div>
  <div style='float: left; text-align: left; padding-right: 5px;'>{lang_print id=180}<br><input type='text' class='text' name='phrase' value='{$phrase}' size='35'></div>
  <div style='float: left; padding-top: 5px;'><input type='submit' class='button' value='{lang_print id=181}'></div>
  <input type='hidden' name='language_id' value='{$language.language_id}'>
  </form>
  <div style='clear: both;'></div>
  </div>
  </td></tr></table>

  <br>

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div align='center'>
      {if $p != 1}<a href='admin_language_edit.php?language_id={$language.language_id}&phrase={$phrase}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_vars} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_vars} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}<a href='admin_language_edit.php?language_id={$language.language_id}&phrase={$phrase}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
      </div>
    </div>
  {/if}

  <table cellpadding='0' cellspacing='0' class='list'>
  <tr>
  <td class='header' width='20'>{lang_print id=87}</td>
  <td class='header' width='20'>&nbsp;</td>
  <td class='header'>{lang_print id=186}</td>
  <td class='header' width='100' align='center'>{lang_print id=1159}</td>
  <td class='header' width='150' align='right'>{lang_print id=1160}</td>
  </tr>
  {section name=var_loop loop=$langvars}
    <tr class='background1' id='tr_{$langvars[var_loop].languagevar_id}'>
    <td class='item' valign='top' nowrap='nowrap'>{$langvars[var_loop].languagevar_id}</td>
    <td class='item' valign='top' nowrap='nowrap'>[ <a href="javascript:void(0);" onclick="editPhrase('{$langvars[var_loop].languagevar_id}', {$smarty.section.var_loop.index+1});" onFocus="toggleRow('tr_{$langvars[var_loop].languagevar_id}');" onBlur="toggleRow('tr_{$langvars[var_loop].languagevar_id}');" tabindex='{$smarty.section.var_loop.index+1}' id='link_{$smarty.section.var_loop.index+1}'>{lang_print id=187}</a> ]</td>
    <td class='item'><span id='span_{$langvars[var_loop].languagevar_id}'>{$langvars[var_loop].languagevar_value}</span></td>
    <td class='item' align='center'>{$langvars[var_loop].languagevar_category}</td>
    <td class='item' align='right'>{$langvars[var_loop].languagevar_default}</td>
    </tr>
  {sectionelse}
    <tr class='background1'>
    <td colspan='6' class='item' align='center'>
      {lang_print id=582}
    </td>
    </tr>
  {/section}
  </table>

  {* JAVASCRIPT FOR HIGLIGHTING ROWS *}
  {literal}
  <script type="text/javascript">
  <!--
  function toggleRow(id1) {
    if($(id1).className == "background2") {
      $(id1).className = "background1"; 
    } else if($(id1).className == "background1") {
      $(id1).className = "background2";
    }
  }
  //-->
  </script>
  {/literal}
  
  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div align='center'>
      {if $p != 1}<a href='admin_language_edit.php?language_id={$language.language_id}&phrase={$phrase}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_vars} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_vars} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}<a href='admin_language_edit.php?language_id={$language.language_id}&phrase={$phrase}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
      </div>
    </div>
  {/if}


  {* JAVASCRIPT FOR CONFIRMING DELETION *}
  {literal}
  <script type="text/javascript">
  <!-- 
  var current_tabindex = 1;
  function editPhrase(id, tabindex) {
    current_tabindex = tabindex+1;
    $('languagevar_id').value = id;
    var request = new Request.JSON({secure: false, url: 'admin_language_edit.php?task=getphrase&languagevar_id='+id,
		onComplete: function(jsonObj) { 
			edit(jsonObj.phrases);
		}
    }).send();
  }
  function edit(phrases) {
    phrases.each(function(phrase) {
      for(var x in phrase) {
        $('var_'+x).innerHTML = phrase[x];
      }
    });

    TB_show('{/literal}{lang_print id=188}{literal}', '#TB_inline?height=400&width=600&inlineId=editphrase', '', '../images/trans.gif');
    setTimeout("$('TB_window').getElementById('var_{/literal}{$language.language_id}{literal}').focus();$('TB_window').getElementById('var_{/literal}{$language.language_id}{literal}').select();", "300");
  }
  function edit_result(id, phrase_value) {
    $('span_'+id).innerHTML = phrase_value.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    changefocus();
  }
  function changefocus() {
    if($('link_'+current_tabindex)) $('link_'+current_tabindex).focus();
  }
  //-->
  </script>
  {/literal}

  {* HIDDEN DIV TO DISPLAY EDIT PHRASES *}
  <div style='display: none;' id='editphrase'>
  <form action='admin_language_edit.php' method='post' name='editform' target='ajaxframe' onSubmit='parent.TB_remove();'>
  {lang_print id=189}<br><br>
  {section name=lang_loop loop=$lang_packlist}
    {$lang_packlist[lang_loop].language_name}<br>
    <textarea name='languagevar_value[{$lang_packlist[lang_loop].language_id}]' id='var_{$lang_packlist[lang_loop].language_id}' cols='25' rows='7' onFocus='this.select();' tabindex='{math equation='x+y+1' x=$langvars|@count y=$smarty.section.lang_loop.index}' class='text' style='width: 100%; font-size: 12px;'></textarea>
    <br><br>
  {/section}
  <input type='submit' class='button' value='{lang_print id=188}' tabindex='{math equation='x+y+1' x=$langvars|@count y=$lang_packlist|@count}'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();parent.changefocus();' tabindex='{math equation='x+y+1' x=$langvars|@count y=$lang_packlist|@count}'>
  <input type='hidden' name='task' value='edit'>
  <input type='hidden' name='languagevar_id' id='languagevar_id' value=''>
  <input type='hidden' name='language_id' value='{$language.language_id}'>
  <input type='hidden' name='p' value='{$p}'>
  <input type='hidden' name='phrase' value='{$phrase}'>
  </form>
  </div>

{/if}

{include file='admin_footer.tpl'}