{include file='admin_header.tpl'}

{* $Id: admin_subnetworks.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=9}</h2>
{lang_print id=612}
<br />
<br />

{literal}
<script language='JavaScript'>
<!--

//-->
</script>
{/literal}

<div id='button1' style='display: block;'>
  [ <a onClick="{literal}$('subnet_help').setStyles({display:'block'});$('button1').setStyles({display:'none'});{/literal}" href="#">{lang_print id=613}</a> ]
  <br><br>
</div>

<div id='subnet_help' style='display: none;'>
  {lang_print id=614}
  <br><br>
</div>


{if $result != 0}<div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=$result}</div>{/if}

<div class='center'>
<div class='box' style='width: 500px;'>

<table cellpadding='0' cellspacing='0'>
<tr><form action='admin_subnetworks.php' method='POST'><td>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td align='right'>{lang_print id=615} &nbsp;</td>
  <td>
  <select class='text' name='setting_subnet_field1_id'>
  <option value='-2'></option>
  <option value='-1'{if $primary.field_id == "-1"} SELECTED{/if}>{lang_print id=617}</option>
  <option value='0'{if $primary.field_id == "0"} SELECTED{/if}>{lang_print id=616}</option>
  {section name=cat_loop loop=$cats}
    {section name=subcat_loop loop=$cats[cat_loop].subcats}
      {section name=field_loop loop=$cats[cat_loop].subcats[subcat_loop].fields}
        <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}'{if $primary.field_id == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id} selected='selected'{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_special == 1} {lang_print id=736}{/if}</option>
      {/section}
    {/section}
  {/section}
  </select>
  </td>
  </tr>
  <tr>
  <td align='right'>{lang_print id=618} &nbsp;</td>
  <td>
  <select class='text' name='setting_subnet_field2_id'>
  <option value='-2'></option>
  <option value='-1'{if $secondary.field_id == "-1"} SELECTED{/if}>{lang_print id=617}</option>
  <option value='0'{if $secondary.field_id == "0"} SELECTED{/if}>{lang_print id=616}</option>
  {section name=cat_loop loop=$cats}
    {section name=subcat_loop loop=$cats[cat_loop].subcats}
      {section name=field_loop loop=$cats[cat_loop].subcats[subcat_loop].fields}
        <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}'{if $secondary.field_id == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id} selected='selected'{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_special == 1} {lang_print id=736}{/if}</option>
      {/section}
    {/section}
  {/section}
  </select>
  </td>
  </tr>
  </table>
</td><td>
&nbsp; <input type='submit' class='button' value='{lang_print id=619}'>
</td><input type='hidden' name='task' value='doupdate'><input type='hidden' name='s' value='{$s}'></form></tr></table>
</div>
</div>

<br>

<input type='submit' class='button' value='{lang_print id=620}' onClick='createSubnet();'>

<br><br>

<table cellpadding='0' cellspacing='0' class='list'>
<tr>
<td class='header' width='10'><a class='header' href='admin_subnetworks.php?s={$i}'>{lang_print id=87}</a></td>
<td class='header' width='200'>{lang_print id=258}</td>
<td class='header' align='center'><a class='header' href='admin_subnetworks.php?s={$u}'>{lang_print id=273}</a></td>
<td class='header'>{lang_print id=621}</td>
<td class='header' width='100'>{lang_print id=153}</td>
</tr>
<tr class='background1'>
<td class='item'>0</td>
<td class='item'>{lang_print id=622}</td>
<td class='item' align='center'><a href='admin_viewusers.php?f_subnet=0'>{$default_users}</a></td>
<td class='item'>{lang_print id=623}</td>
<td class='item'>&nbsp;</td>
</tr>
{section name=subnet_loop loop=$subnets}
  {capture assign="subnet_name"}{lang_print id=$subnets[subnet_loop].subnet_name}{/capture}
  <tr class='{cycle values="background2,background1"}'>
  <td class='item'>{$subnets[subnet_loop].subnet_id}</td>
  <td class='item'>{lang_print id=$subnets[subnet_loop].subnet_name}</td>
  <td class='item' align='center'><a href='admin_viewusers.php?f_subnet={$subnets[subnet_loop].subnet_id}'>{$subnets[subnet_loop].subnet_users}</a></td>
  <td class='item'>{lang_print id=$primary.field_title}{if $primary.field_special == 1} {lang_print id=736}{/if} {$subnets[subnet_loop].subnet_field1_qual} {$subnets[subnet_loop].subnet_field1_value_formatted}<br>{if $subnets[subnet_loop].subnet_field2_qual != "" && $subnets[subnet_loop].subnet_field2_value_formatted != ""}{lang_print id=$secondary.field_title}{if $secondary.field_special == 1} {lang_print id=736}{/if} {$subnets[subnet_loop].subnet_field2_qual} {$subnets[subnet_loop].subnet_field2_value_formatted}{/if}</td>
  <td class='item'>[ <a href="javascript: editSubnet('{$subnets[subnet_loop].subnet_id}', '{$subnet_name|replace:"&#039;":"\&#039;"}', '{$subnets[subnet_loop].subnet_field1_qual}', '{$subnets[subnet_loop].subnet_field1_value}', '{$subnets[subnet_loop].subnet_field1_month}', '{$subnets[subnet_loop].subnet_field1_day}', '{$subnets[subnet_loop].subnet_field1_year}', '{$subnets[subnet_loop].subnet_field2_qual}', '{$subnets[subnet_loop].subnet_field2_value}', '{$subnets[subnet_loop].subnet_field2_month}', '{$subnets[subnet_loop].subnet_field2_day}', '{$subnets[subnet_loop].subnet_field2_year}');">{lang_print id=187}</a> ] [ <a href="javascript: confirmDelete('{$subnets[subnet_loop].subnet_id}');">{lang_print id=155}</a> ]</td>
  </tr>
{/section}
</table>


{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
var subnet_id = 0;
function confirmDelete(id) {
  subnet_id = id;
  TB_show('{/literal}{lang_print id=636}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');

}

function deleteSubnet() {
  window.location = 'admin_subnetworks.php?task=delete&subnet_id='+subnet_id;
}

function createSubnet() {
  $('task').value = 'create';
  $('createbutton').value = '{/literal}{lang_print id=624}{literal}';
  $('subnet_id').value = 0;  
  $('subnet_name').value = '';  
  $('subnet_name').defaultValue = '';  
  $('subnet_field1_qual').options[$('subnet_field1_qual').selectedIndex].defaultSelected = false;
  if($('subnet_field1_value')) {
    if($('subnet_field1_value').options) { 
      $('subnet_field1_value').options[$('subnet_field1_value').selectedIndex].defaultSelected = false; 
    } else {
      $('subnet_field1_value').value = '';
      $('subnet_field1_value').defaultValue = '';
    }
  } else {
    $('subnet_field1_month').options[$('subnet_field1_month').selectedIndex].defaultSelected = false;
    $('subnet_field1_day').options[$('subnet_field1_day').selectedIndex].defaultSelected = false;
    $('subnet_field1_year').options[$('subnet_field1_year').selectedIndex].defaultSelected = false;
  }
  if($('subnet_field2_qual')) {
    $('subnet_field2_qual').options[$('subnet_field2_qual').selectedIndex].defaultSelected = false;
    if($('subnet_field2_value')) {
      if($('subnet_field2_value').options) { 
        $('subnet_field2_value').options[$('subnet_field2_value').selectedIndex].defaultSelected = false; 
      } else {
        $('subnet_field2_value').value = '';
        $('subnet_field2_value').defaultValue = '';
      }
    } else {
      $('subnet_field2_month').options[$('subnet_field2_month').selectedIndex].defaultSelected = false;
      $('subnet_field2_day').options[$('subnet_field2_day').selectedIndex].defaultSelected = false;
      $('subnet_field2_year').options[$('subnet_field2_year').selectedIndex].defaultSelected = false;
    }
  }
  TB_show('{/literal}{lang_print id=624}{literal}', '#TB_inline?height=450&width=500&inlineId=createsubnet', '', '../images/trans.gif');
}

function editSubnet(subnet_id, subnet_name, subnet_field1_qual, subnet_field1_value, subnet_field1_month, subnet_field1_day, subnet_field1_year, subnet_field2_qual, subnet_field2_value, subnet_field2_month, subnet_field2_day, subnet_field2_year) {
  $('task').value = 'edit';
  $('createbutton').value = '{/literal}{lang_print id=635}{literal}';
  $('subnet_id').value = subnet_id;  
  $('subnet_name').value = subnet_name;  
  $('subnet_name').defaultValue = subnet_name;  
  $('subnet_field1_qual').value = subnet_field1_qual;
  $('subnet_field1_qual').options[$('subnet_field1_qual').selectedIndex].defaultSelected = true;
  if($('subnet_field1_value')) {
    $('subnet_field1_value').value = subnet_field1_value;
    if($('subnet_field1_value').options) { 
      $('subnet_field1_value').options[$('subnet_field1_value').selectedIndex].defaultSelected = true; 
    } else {
      $('subnet_field1_value').defaultValue = subnet_field1_value;
    }
  } else {
    $('subnet_field1_month').value = subnet_field1_month;
    $('subnet_field1_month').options[$('subnet_field1_month').selectedIndex].defaultSelected = true;
    $('subnet_field1_day').value = subnet_field1_day;
    $('subnet_field1_day').options[$('subnet_field1_day').selectedIndex].defaultSelected = true;
    $('subnet_field1_year').value = subnet_field1_year;
    $('subnet_field1_year').options[$('subnet_field1_year').selectedIndex].defaultSelected = true;
  }
  if($('subnet_field2_qual')) {
    $('subnet_field2_qual').value = subnet_field2_qual;
    $('subnet_field2_qual').options[$('subnet_field2_qual').selectedIndex].defaultSelected = true;
    if($('subnet_field2_value')) {
      $('subnet_field2_value').value = subnet_field2_value;
      if($('subnet_field2_value').options) { 
        $('subnet_field2_value').options[$('subnet_field2_value').selectedIndex].defaultSelected = true; 
      } else {
        $('subnet_field2_value').defaultValue = subnet_field2_value;
      }
    } else {
      $('subnet_field2_month').value = subnet_field2_month;
      $('subnet_field2_month').options[$('subnet_field2_month').selectedIndex].defaultSelected = true;
      $('subnet_field2_day').value = subnet_field2_day;
      $('subnet_field2_day').options[$('subnet_field2_day').selectedIndex].defaultSelected = true;
      $('subnet_field2_year').value = subnet_field2_year;
      $('subnet_field2_year').options[$('subnet_field2_year').selectedIndex].defaultSelected = true;
    }
  }
  TB_show('{/literal}{lang_print id=635}{literal}', '#TB_inline?height=450&width=500&inlineId=createsubnet', '', '../images/trans.gif');
}

//-->
</script>
{/literal}


{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=637}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteSubnet();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>


{* HIDDEN DIV TO DISPLAY CREATE SUBNETWORK *}
<div style='display: none;' id='createsubnet'>
  <form action='admin_subnetworks.php' method='post' target='_parent' onSubmit="{literal}if(this.subnet_name.value == ''){ alert('{/literal}{lang_print id=633}{literal}'); return false; } else if($(this).getElement('select[id=subnet_field1_qual]').value == '' || ($(this).getElement('input[id=subnet_field1_value]') && $(this).getElement('input[id=subnet_field1_value]').value == '') || ($(this).getElement('select[id=subnet_field1_value]') && $(this).getElement('select[id=subnet_field1_value]').value == '') || ($(this).getElement('select[id=subnet_field1_month]') && $(this).getElement('select[id=subnet_field1_month]').value == '') || ($(this).getElement('select[id=subnet_field1_day]') && $(this).getElement('select[id=subnet_field1_day]').value == '') || ($(this).getElement('select[id=subnet_field1_year]') && $(this).getElement('select[id=subnet_field1_year]').value == '')) { alert('{/literal}{lang_print id=634}{literal}'); return false; } else { return true; }{/literal}">
  <div style='margin-top: 10px;'>{lang_print id=625}</div>
  <br>
  <b>{lang_print id=258}:</b><br>
  <input type='text' class='text' name='subnet_name' id='subnet_name' maxlength='20'>
  <br><br>
  <b>{lang_print id=621}:</b><br>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>
    <select class='text'>
    <option>{lang_print id=$primary.field_title}{if $primary.field_special == 1} {lang_print id=736}{/if}</option>
    </select>&nbsp;
    </td>
    <td>
    <select class='text' name='subnet_field1_qual' id='subnet_field1_qual'>
    <option value=''></option>
    <option value='=='>{lang_print id=626}</option>
    <option value='!='>{lang_print id=627}</option>
    <option value='>'>{lang_print id=628}</option>
    <option value='<'>{lang_print id=629}</option>
    <option value='>='>{lang_print id=630}</option>
    <option value='<='>{lang_print id=631}</option>
    </select>&nbsp;
    </td>
    <td>
      {* TEXT FIELD *}
      {if $primary.field_type == 1 OR $primary.field_type == 2}
        <input type='text' class='text' name='subnet_field1_value' id='subnet_field1_value' maxlength='250' size='30'>

      {* SELECT BOX *}
      {elseif $primary.field_type == 3 OR $primary.field_type == 4}
        <select class='text' name='subnet_field1_value' id='subnet_field1_value'>
        <option value=''></option>
        {* LOOP THROUGH FIELD OPTIONS *}
        {section name=option_loop loop=$primary.field_options}
          <option value='{$primary.field_options[option_loop].value}'>{lang_print id=$primary.field_options[option_loop].label}</option>
        {/section}
        </select>

      {* DATE FIELD *}
      {elseif $primary.field_type == 5}
        <select class='text' name='subnet_field1_month' id='subnet_field1_month'>
        <option value=''>{lang_print id=579}</option>
        {section name=field1_month start=1 loop=13}
          <option value='{$smarty.section.field1_month.index}'>{$datetime->cdate("M", $datetime->MakeTime(0, 0, 0, $smarty.section.field1_month.index, 1, 1990))}</option>
        {/section}
        </select>
 
        <select class='text' name='subnet_field1_day' id='subnet_field1_day'>
        <option value=''>{lang_print id=580}</option>
        {section name=field1_day start=1 loop=32}
          <option value='{$smarty.section.field1_day.index}'>{$smarty.section.field1_day.index}</option>
        {/section}
        </select>

        <select class='text' name='subnet_field1_year' id='subnet_field1_year'>
        <option value=''>{lang_print id=581}</option>
        {section name=field1_year loop=2009 max=80 step=-1}
          <option value='{$smarty.section.field1_year.index}'>{$smarty.section.field1_year.index}</option>
        {/section}
        </select>
      {/if}
    </td>
    </tr>
    </table>
  <br>
  {if $secondary.field_id != -2}
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td>
    <select class='text'>
    <option>{lang_print id=$secondary.field_title}{if $secondary.field_special == 1} {lang_print id=736}{/if}</option>
    </select>&nbsp;
    </td>
    <td>
    <select class='text' name='subnet_field2_qual' id='subnet_field2_qual'>
    <option value=''></option>
    <option value='=='>{lang_print id=626}</option>
    <option value='!='>{lang_print id=627}</option>
    <option value='>'>{lang_print id=628}</option>
    <option value='<'>{lang_print id=629}</option>
    <option value='>='>{lang_print id=630}</option>
    <option value='<='>{lang_print id=631}</option>
    </select>&nbsp;
    </td>
    <td>
      {* TEXT FIELD *}
      {if $secondary.field_type == 1 OR $secondary.field_type == 2}
        <input type='text' class='text' name='subnet_field2_value' id='subnet_field2_value' maxlength='250' size='30'>

      {* SELECT BOX *}
      {elseif $secondary.field_type == 3 OR $secondary.field_type == 4}
        <select class='text' name='subnet_field2_value' id='subnet_field2_value'>
        <option value=''></option>
        {* LOOP THROUGH FIELD OPTIONS *}
        {section name=option_loop loop=$secondary.field_options}
          <option value='{$secondary.field_options[option_loop].value}'>{lang_print id=$secondary.field_options[option_loop].label}</option>
        {/section}
        </select>

      {* DATE FIELD *}
      {elseif $secondary.field_type == 5}
        <select class='text' name='subnet_field2_month' id='subnet_field2_month'>
        <option value=''>{lang_print id=579}</option>
        {section name=field2_month start=1 loop=13}
          <option value='{$smarty.section.field2_month.index}'>{$datetime->cdate("M", $datetime->MakeTime(0, 0, 0, $smarty.section.field2_month.index, 1, 1990))}</option>
        {/section}
        </select>
 
        <select class='text' name='subnet_field2_day' id='subnet_field2_day'>
        <option value=''>{lang_print id=580}</option>
        {section name=field2_day start=1 loop=32}
          <option value='{$smarty.section.field2_day.index}'>{$smarty.section.field2_day.index}</option>
        {/section}
        </select>

        <select class='text' name='subnet_field2_year' id='subnet_field2_year'>
        <option value=''>{lang_print id=581}</option>
        {section name=field2_year loop=2009 max=80 step=-1}
          <option value='{$smarty.section.field2_year.index}'>{$smarty.section.field2_year.index}</option>
        {/section}
        </select>
      {/if}
    </td>
    </tr>
    </table>
  <br>
  {/if}

  <br>
  <input type='submit' class='button' id='createbutton' value='{lang_print id=624}'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  <input type='hidden' name='task' value='create' id='task'>
  <input type='hidden' name='subnet_id' id='subnet_id' value='0'>
  </form>
</div>


{include file='admin_footer.tpl'}