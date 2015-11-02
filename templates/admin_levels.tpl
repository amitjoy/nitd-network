{include file='admin_header.tpl'}

{* $Id: admin_levels.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=8}</h2>
{lang_print id=271}

<br><br>

<input type='button' class='button' value='{lang_print id=272}' onClick='createLevel();'>

<br><br>

<table cellpadding='0' cellspacing='0' class='list'>
<tr>
<td class='header' width='10'><a class='header' href='admin_levels.php?s={$i}'>{lang_print id=87}</a></td>
<td class='header'><a class='header' href='admin_levels.php?s={$n}'>{lang_print id=258}</a></td>
<td class='header' align='center'><a class='header' href='admin_levels.php?s={$u}'>{lang_print id=273}</td>
<td class='header' align='center'>{lang_print id=152}</td>
<td class='header' width='100'>{lang_print id=153}</td>
</tr>
{section name=level_loop loop=$levels}
  <tr class='{cycle values="background2,background1"}'>
  <td class='item'>{$levels[level_loop].level_id}</td>
  <td class='item'>{$levels[level_loop].level_name}</td>
  <td class='item' align='center'><a href='admin_viewusers.php?f_level={$levels[level_loop].level_id}'>{$levels[level_loop].users} {lang_print id=274}</a></td>
  <td class='item' align='center'>{if $levels[level_loop].level_default == 0}<a href='admin_levels.php?task=savechanges&default={$levels[level_loop].level_id}'><img src='../images/icons/admin_checkbox1.gif' border='0' class='icon'></a>{else}<img src='../images/icons/admin_checkbox2.gif' border='0' class='icon'>{/if}</td>
  <td class='item'>[ <a href='admin_levels_edit.php?level_id={$levels[level_loop].level_id}'>{lang_print id=187}</a> ]{if $levels[level_loop].level_default == 0} [ <a href="javascript: confirmDelete('{$levels[level_loop].level_id}')">{lang_print id=155}</a> ]{/if}</td>
  </tr>
{/section}
</table>

{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
var level_id = 0;
function confirmDelete(id) {
  level_id = id;
  TB_show('{/literal}{lang_print id=280}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');

}

function deletePack() {
  window.location = 'admin_levels.php?task=delete&level_id='+level_id;
}

function createLevel() {
  TB_show('{/literal}{lang_print id=272}{literal}', '#TB_inline?height=250&width=350&inlineId=createlevel', '', '../images/trans.gif');
}

//-->
</script>
{/literal}


{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=279}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deletePack();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>


{* HIDDEN DIV TO DISPLAY CREATE LEVEL *}
<div style='display: none;' id='createlevel'>
  <form action='admin_levels.php' method='post' target='_parent' onSubmit="{literal}if(this.level_name.value == ''){ alert('{/literal}{lang_print id=276}{literal}'); return false;}else{return true;}{/literal}">
  <div style='margin-top: 10px;'>{lang_print id=275}</div>
  <br>
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td align='right'>{lang_print id=258}:&nbsp;</td>
  <td><input type='text' class='text' name='level_name' id='level_name' size='30' maxlength='50'></td>
  </tr>
  <tr>
  <td align='right' valign='top'>{lang_print id=277}:&nbsp;</td>
  <td><textarea name='level_desc' rows='4' cols='40' class='text' style='width: 100%;'></textarea></td>
  </tr>
  </table>

  <br>
  <input type='submit' class='button' value='{lang_print id=278}'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  <input type='hidden' name='task' value='create'>
  </form>
</div>

{include file='admin_footer.tpl'}