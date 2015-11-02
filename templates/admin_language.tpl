{include file='admin_header.tpl'}

{* $Id: admin_language.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=49}</h2>
<div>{lang_print id=147}</div>
<br />


<table cellpadding='0' cellspacing='0' class='list' style='width: 700px;'>
  <tr>
    <td class='header'>{lang_print id=149}</td>
    <td class='header'>{lang_print id=150}</td>
    <td class='header'>{lang_print id=1147}</td>
    <td class='header'>{lang_print id=151}</td>
    <td class='header' align='center'>{lang_print id=152}</td>
    <td class='header' width='175'>{lang_print id=153}</td>
  </tr>
  {section name=lang_loop loop=$lang_packlist}
  <tr class='{cycle values="background2,background1"}'>
    <td class='item'><a href="javascript:editPack('{$lang_packlist[lang_loop].language_id}', '{$lang_packlist[lang_loop].language_name|replace:"&#039;":"\&#039;"}', '{$lang_packlist[lang_loop].language_code}', '{$lang_packlist[lang_loop].language_setlocale}', '{$lang_packlist[lang_loop].language_autodetect_regex}')">{$lang_packlist[lang_loop].language_name}</a></td>
    <td class='item'>{$lang_packlist[lang_loop].language_code}</td>
    <td class='item'>{$lang_packlist[lang_loop].language_setlocale}</td>
    <td class='item'>{$lang_packlist[lang_loop].language_autodetect_regex}</td>
    <td class='item' align='center'>{if $lang_packlist[lang_loop].language_default == 1}<img src='../images/icons/admin_checkbox2.gif' border='0' class='icon'>{else}<a href='admin_language.php?task=setdefault&language_id={$lang_packlist[lang_loop].language_id}'><img src='../images/icons/admin_checkbox1.gif' border='0' class='icon'></a>{/if}</td>
    <td class='item'>
      [ <a href='admin_language_edit.php?language_id={$lang_packlist[lang_loop].language_id}'>{lang_print id=154}</a> ]
      [ <a href='admin_language_export.php?language_id={$lang_packlist[lang_loop].language_id}&task=doexport'>{lang_print id=1283}</a> ]
      {if $lang_packlist[lang_loop].language_default != 1}
      [ <a href="javascript:confirmDelete('{$lang_packlist[lang_loop].language_id}');">{lang_print id=155}</a> ]
      {/if}
    </td>
  </tr>
  {/section}
</table>
<br />


<a href="admin_language.php" onclick="createPack(); return false;">{lang_print id=156}</a>  |  
<a href="admin_language_import.php">{lang_print id=1284}</a>


{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
var lang_id = 0;
function confirmDelete(id) {
  lang_id = id;
  TB_show('{/literal}{lang_print id=157}{literal}', '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', '../images/trans.gif');

}

function deletePack() {
  window.location = 'admin_language.php?task=delete&language_id='+lang_id;
}

function createPack() {
  $('createbutton').value = '{/literal}{lang_print id=158}{literal}';
  $('language_id').value = '0';  
  $('language_name').defaultValue = '';  
  $('language_name').value = '';  
  $('language_code').defaultValue = '';  
  $('language_code').value = '';  
  $('language_setlocale').value = '';  
  
  if( $('language_setlocale_select') )
  {
    $('language_setlocale_select').options[$('language_setlocale_select').selectedIndex].defaultSelected = false;
    $('language_setlocale_select').options[0].selected = true;
  }
  
  $('language_autodetect_regex').defaultValue = '';  
  $('language_autodetect_regex').value = '';  
  TB_show('{/literal}{lang_print id=158}{literal}', '#TB_inline?height=350&width=300&inlineId=createpack', '', '../images/trans.gif');
}

function editPack(id, lang_name, lang_code, lang_setlocale, lang_autodetect_regex)
{
  $('createbutton').value = '{/literal}{lang_print id=159}{literal}';
  $('language_id').value = id;  
  $('language_name').defaultValue = lang_name;  
  $('language_name').value = lang_name;  
  $('language_code').defaultValue = lang_code;  
  $('language_code').value = lang_code;  
  $('language_setlocale').value = lang_setlocale;
  $('language_setlocale').defaultValue = lang_setlocale;
  
  if( $('language_setlocale_select') )
  {
    $('language_setlocale_select').options[$('language_setlocale_select').selectedIndex].defaultSelected = false;
    $('language_setlocale_select').options[0].selected = true;
    $('language_setlocale_select').value = lang_setlocale;
    $('language_setlocale_select').options[$('language_setlocale_select').selectedIndex].defaultSelected = true;
  }
  
  $('language_autodetect_regex').defaultValue = lang_autodetect_regex;
  $('language_autodetect_regex').value = lang_autodetect_regex;
  TB_show('{/literal}{lang_print id=159}{literal}', '#TB_inline?height=350&width=300&inlineId=createpack', '', '../images/trans.gif');
}

//-->
</script>
{/literal}

<br><br>

<form action='admin_language.php' method='post'>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=160}</td></tr>
<tr><td class='setting1'>{lang_print id=161}</td></tr>
<tr>
<td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_lang_allow' id='lang_allow_1' value='1'{if $setting.setting_lang_allow == 1} checked='checked'{/if}></td>
  <td><label for='lang_allow_1'>{lang_print id=162}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_lang_allow' id='lang_allow_0' value='0'{if $setting.setting_lang_allow == 0} checked='checked'{/if}></td>
  <td><label for='lang_allow_0'>{lang_print id=163}</label></td>
  </tr>
  </table>
</td>
</tr>
<tr><td class='setting1'>{lang_print id=164}</td></tr>
<tr>
<td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_lang_anonymous' id='lang_anonymous_1' value='1'{if $setting.setting_lang_anonymous == 1} checked='checked'{/if}></td>
  <td><label for='lang_anonymous_1'>{lang_print id=165}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_lang_anonymous' id='lang_anonymous_0' value='0'{if $setting.setting_lang_anonymous == 0} checked='checked'{/if}></td>
  <td><label for='lang_anonymous_0'>{lang_print id=166}</label></td>
  </tr>
  </table>
</td>
</tr>
<tr><td class='setting1'>
  {lang_print id=167}
  <br><br>
  <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>{lang_print id=168}</td>
    <td>&nbsp;&nbsp;{$HTTP_ACCEPT_LANGUAGE}</td>
    </tr>
    <tr>
    <td>{lang_print id=169}</td>
    <td>&nbsp;&nbsp;{$HTTP_ACCEPT_LANGUAGE_CLEAN}</td>
    </tr>
    <tr>
    <td>{lang_print id=170}</td>
    <td>&nbsp;&nbsp;{$AUTODETECTED_LANGUAGE}</td>
    </tr>
  </table>
</td></tr>
<tr>
<td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_lang_autodetect' id='lang_autodetect_1' value='1'{if $setting.setting_lang_autodetect == 1} checked='checked'{/if}></td>
  <td><label for='lang_autodetect_1'>{lang_print id=171}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_lang_autodetect' id='lang_autodetect_0' value='0'{if $setting.setting_lang_autodetect == 0} checked='checked'{/if}></td>
  <td><label for='lang_autodetect_0'>{lang_print id=172}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>
<input type='hidden' name='task' value='dosave'>
<input type='submit' class='button' value='{lang_print id=173}'>
</form>

<br>

{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=174}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deletePack();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>


{* HIDDEN DIV TO DISPLAY CREATE LANGUAGE *}
<div style='display: none;' id='createpack'>
  <form action='admin_language.php' name='createForm' method='post' target='_parent' onSubmit="{literal}if(this.language_name.value == ''){ alert('{/literal}{lang_print id=176}{literal}'); return false;}else{return true;}{/literal}">
  <div style='margin-top: 10px;'>{lang_print id=177}</div>
  <br />
  
  <table cellpadding='0' cellspacing='2'>
    <tr>
      <td align='right'>{lang_print id=149}:&nbsp;</td>
      <td><input type='text' class='text' name='language_name' id='language_name' maxlength='20' /></td>
    </tr>
    <tr>
      <td align='right'>{lang_print id=150}:&nbsp;</td>
      <td><input type='text' class='text' name='language_code' id='language_code' maxlength='9' /></td>
    </tr>
    <tr>
      <td align='right'>{lang_print id=1147}:&nbsp;</td>
      <td>
        <input type='text' class='text' id='language_setlocale' name='language_setlocale' value="" />
        {if !empty($locales) && is_array($locales)}
        <br />
        <select class="text" id='language_setlocale_select' onchange="if( this.value!='' ) $(this).getParent().getElement('input').value=this.value;">
          <option value=''></option>
          {section name=locale_loop loop=$locales}
          <option value='{$locales[locale_loop]}'>{$locales[locale_loop]}</option>
          {/section}
        </select>
        {/if}
      </td>
    </tr>
    <tr>
      <td align='right'>{lang_print id=151}:&nbsp;</td>
      <td><input type='text' class='text' name='language_autodetect_regex' id='language_autodetect_regex' maxlength='64' /></td>
    </tr>
  </table>
  <br />
  
  <input type='submit' class='button' id='createbutton' value='{lang_print id=158}'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();' />
  <input type='hidden' name='task' value='create' />
  <input type='hidden' name='language_id' id='language_id' value='0' />
  </form>
</div>




{include file='admin_footer.tpl'}