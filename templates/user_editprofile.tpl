{include file='header.tpl'}

{* $Id: user_editprofile.tpl 8 2009-01-11 06:02:53Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
<tr>
<td class='tab0'>&nbsp;</td>
{section name=cat_loop loop=$cats}
  <td class='tab{if $cats[cat_loop].subcat_id == $cat_id}1{else}2{/if}' NOWRAP><a href='user_editprofile.php?cat_id={$cats[cat_loop].subcat_id}'>{lang_print id=$cats[cat_loop].subcat_title}</a></td>
  <td class='tab'>&nbsp;</td>
  {if $cats[cat_loop].subcat_id == $cat_id}{capture assign='pagename'}{lang_print id=$cats[cat_loop].subcat_title}{/capture}{/if}
{/section}
{if $user->level_info.level_photo_allow != 0}<td class='tab2' NOWRAP><a href='user_editprofile_photo.php'>{lang_print id=762}</a></td><td class='tab'>&nbsp;</td>{/if}
{if $user->level_info.level_profile_style != 0 || $user->level_info.level_profile_style_sample != 0}<td class='tab2' NOWRAP><a href='user_editprofile_style.php'>{lang_print id=763}</a></td>{/if}
<td class='tab3'>&nbsp;</td>
</tr>
</table>

<img src='./images/icons/editprofile48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_sprintf id=764 1=$pagename}</div>
<div>{lang_print id=765}</div>
<br />
<br />

{* SHOW RESULT MESSAGE *}
{if $result == 2}
  <br>
  <table cellpadding='0' cellspacing='0'><tr><td class='result'>
  {capture assign="old_subnet_name"}{lang_print id=$old_subnet_name}{/capture}
  {capture assign="new_subnet_name"}{lang_print id=$new_subnet_name}{/capture}
  <div class='success'><img src='./images/success.gif' border='0' class='icon'> {lang_print id=191}<br>{lang_sprintf id=767 1=$old_subnet_name 2=$new_subnet_name}</div>
  </td></tr></table>
  <br>
{elseif $result == 1}
  <br>
  <table cellpadding='0' cellspacing='0'><tr><td class='result'>
  <div class='success'><img src='./images/success.gif' border='0' class='icon'> {lang_print id=191}</div>
  </td></tr></table>
  <br>
{/if}

{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
  <br>
  <table cellpadding='0' cellspacing='0'><tr><td class='result'>
  <div class='error'><img src='./images/error.gif' border='0' class='icon'> {lang_print id=$is_error}</div>
  </td></tr></table>
  <br>
{/if}

{* JAVASCRIPT FOR SHOWING DEP FIELDS *}
{literal}
<script type="text/javascript">
<!-- 
  function ShowHideDeps(field_id, field_value, field_type) {
    if(field_type == 6) {
      if($('field_'+field_id+'_option'+field_value)) {
        if($('field_'+field_id+'_option'+field_value).style.display == "block") {
	  $('field_'+field_id+'_option'+field_value).style.display = "none";
	} else {
	  $('field_'+field_id+'_option'+field_value).style.display = "block";
	}
      }
    } else {
      var divIdStart = "field_"+field_id+"_option";
      for(var x=0;x<$('field_options_'+field_id).childNodes.length;x++) {
        if($('field_options_'+field_id).childNodes[x].nodeName == "DIV" && $('field_options_'+field_id).childNodes[x].id.substr(0, divIdStart.length) == divIdStart) {
          if($('field_options_'+field_id).childNodes[x].id == 'field_'+field_id+'_option'+field_value) {
            $('field_options_'+field_id).childNodes[x].style.display = "block";
          } else {
            $('field_options_'+field_id).childNodes[x].style.display = "none";
          }
        }
      }
    }
  }
//-->
</script>
{/literal}

<form action='user_editprofile.php' method='POST'>
<table cellpadding='0' cellspacing='0' class='form'>

{* LOOP THROUGH FIELDS *}
{section name=field_loop loop=$fields}
  <tr>
  <td class='form1' width='150'>{lang_print id=$fields[field_loop].field_title}{if $fields[field_loop].field_required != 0}*{/if}</td>
  <td class='form2'>

    {* TEXT FIELD *}
    {if $fields[field_loop].field_type == 1}
      <div><input type='text' class='text' name='field_{$fields[field_loop].field_id}' id='field_{$fields[field_loop].field_id}' value='{$fields[field_loop].field_value}' style='{$fields[field_loop].field_style}' maxlength='{$fields[field_loop].field_maxlength}'></div>

      {* JAVASCRIPT FOR CREATING SUGGESTION BOX *}
      {if $fields[field_loop].field_options != "" && $fields[field_loop].field_options|@count != 0}
      {literal}
      <script type="text/javascript">
      <!-- 
      window.addEvent('domready', function(){
	var options = {
		script:"misc_js.php?task=suggest_field&limit=5&{/literal}{section name=option_loop loop=$fields[field_loop].field_options}options[]={$fields[field_loop].field_options[option_loop].label}&{/section}{literal}",
		varname:"input",
		json:true,
		shownoresults:false,
		maxresults:5,
		multisuggest:false,
		callback: function (obj) {  }
	};
	var as_json{/literal}{$fields[field_loop].field_id}{literal} = new bsn.AutoSuggest('field_{/literal}{$fields[field_loop].field_id}{literal}', options);
      });
      //-->
      </script>
      {/literal}
      {/if}


    {* TEXTAREA *}
    {elseif $fields[field_loop].field_type == 2}
      <div><textarea rows='6' cols='50' name='field_{$fields[field_loop].field_id}' style='{$fields[field_loop].field_style}'>{$fields[field_loop].field_value}</textarea></div>



    {* SELECT BOX *}
    {elseif $fields[field_loop].field_type == 3}
      <div><select name='field_{$fields[field_loop].field_id}' id='field_{$fields[field_loop].field_id}' onchange="ShowHideDeps('{$fields[field_loop].field_id}', this.value);" style='{$fields[field_loop].field_style}'>
      <option value='-1'></option>
      {* LOOP THROUGH FIELD OPTIONS *}
      {section name=option_loop loop=$fields[field_loop].field_options}
        <option id='op' value='{$fields[field_loop].field_options[option_loop].value}'{if $fields[field_loop].field_options[option_loop].value == $fields[field_loop].field_value} SELECTED{/if}>{lang_print id=$fields[field_loop].field_options[option_loop].label}</option>
      {/section}
      </select>
      </div>
      {* LOOP THROUGH DEPENDENT FIELDS *}
      <div id='field_options_{$fields[field_loop].field_id}'>
      {section name=option_loop loop=$fields[field_loop].field_options}
        {if $fields[field_loop].field_options[option_loop].dependency == 1}

	  {* SELECT BOX *}
	  {if $fields[field_loop].field_options[option_loop].dep_field_type == 3}
            <div id='field_{$fields[field_loop].field_id}_option{$fields[field_loop].field_options[option_loop].value}' style='margin: 5px 5px 10px 5px;{if $fields[field_loop].field_options[option_loop].value != $fields[field_loop].field_value} display: none;{/if}'>
            {lang_print id=$fields[field_loop].field_options[option_loop].dep_field_title}{if $fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
            <select name='field_{$fields[field_loop].field_options[option_loop].dep_field_id}'>
	      <option value='-1'></option>
	      {* LOOP THROUGH DEP FIELD OPTIONS *}
	      {section name=option2_loop loop=$fields[field_loop].field_options[option_loop].dep_field_options}
	        <option id='op' value='{$fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
	      {/section}
	    </select>
            </div>	  

	  {* TEXT FIELD *}
	  {else}
            <div id='field_{$fields[field_loop].field_id}_option{$fields[field_loop].field_options[option_loop].value}' style='margin: 5px 5px 10px 5px;{if $fields[field_loop].field_options[option_loop].value != $fields[field_loop].field_value} display: none;{/if}'>
            {lang_print id=$fields[field_loop].field_options[option_loop].dep_field_title}{if $fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
            <input type='text' class='text' name='field_{$fields[field_loop].field_options[option_loop].dep_field_id}' value='{$fields[field_loop].field_options[option_loop].dep_field_value}' style='{$fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
            </div>
	  {/if}

        {/if}
      {/section}
      </div>
  


    {* RADIO BUTTONS *}
    {elseif $fields[field_loop].field_type == 4}
    
      {* LOOP THROUGH FIELD OPTIONS *}
      <div id='field_options_{$fields[field_loop].field_id}'>
      {section name=option_loop loop=$fields[field_loop].field_options}
        <div>
        <input type='radio' class='radio' onclick="ShowHideDeps('{$fields[field_loop].field_id}', '{$fields[field_loop].field_options[option_loop].value}');" style='{$fields[field_loop].field_style}' name='field_{$fields[field_loop].field_id}' id='label_{$fields[field_loop].field_id}_{$fields[field_loop].field_options[option_loop].value}' value='{$fields[field_loop].field_options[option_loop].value}'{if $fields[field_loop].field_options[option_loop].value == $fields[field_loop].field_value} CHECKED{/if}>
        <label for='label_{$fields[field_loop].field_id}_{$fields[field_loop].field_options[option_loop].value}'>{lang_print id=$fields[field_loop].field_options[option_loop].label}</label>
        </div>

        {* DISPLAY DEPENDENT FIELDS *}
        {if $fields[field_loop].field_options[option_loop].dependency == 1}

	  {* SELECT BOX *}
	  {if $fields[field_loop].field_options[option_loop].dep_field_type == 3}
            <div id='field_{$fields[field_loop].field_id}_option{$fields[field_loop].field_options[option_loop].value}' style='margin: 5px 5px 10px 5px;{if $fields[field_loop].field_options[option_loop].value != $fields[field_loop].field_value} display: none;{/if}'>
            {lang_print id=$fields[field_loop].field_options[option_loop].dep_field_title}{if $fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
            <select name='field_{$fields[field_loop].field_options[option_loop].dep_field_id}'>
	      <option value='-1'></option>
	      {* LOOP THROUGH DEP FIELD OPTIONS *}
	      {section name=option2_loop loop=$fields[field_loop].field_options[option_loop].dep_field_options}
	        <option id='op' value='{$fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
	      {/section}
	    </select>
            </div>	  

	  {* TEXT FIELD *}
	  {else}
            <div id='field_{$fields[field_loop].field_id}_option{$fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $fields[field_loop].field_options[option_loop].value != $fields[field_loop].field_value} display: none;{/if}'>
            {lang_print id=$fields[field_loop].field_options[option_loop].dep_field_title}{if $fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
            <input type='text' class='text' name='field_{$fields[field_loop].field_options[option_loop].dep_field_id}' value='{$fields[field_loop].field_options[option_loop].dep_field_value}' style='{$fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
            </div>
	  {/if}

        {/if}

      {/section}
      </div>


    {* DATE FIELD *}
    {elseif $fields[field_loop].field_type == 5}
      <div>
      <select name='field_{$fields[field_loop].field_id}_1' style='{$fields[field_loop].field_style}'>
      {section name=date1 loop=$fields[field_loop].date_array1}
        <option value='{$fields[field_loop].date_array1[date1].value}'{$fields[field_loop].date_array1[date1].selected}>{if $smarty.section.date1.first}[ {lang_print id=$fields[field_loop].date_array1[date1].name} ]{else}{$fields[field_loop].date_array1[date1].name}{/if}</option>
      {/section}
      </select>

      <select name='field_{$fields[field_loop].field_id}_2' style='{$fields[field_loop].field_style}'>
      {section name=date2 loop=$fields[field_loop].date_array2}
        <option value='{$fields[field_loop].date_array2[date2].value}'{$fields[field_loop].date_array2[date2].selected}>{if $smarty.section.date2.first}[ {lang_print id=$fields[field_loop].date_array2[date2].name} ]{else}{$fields[field_loop].date_array2[date2].name}{/if}</option>
      {/section}
      </select>

      <select name='field_{$fields[field_loop].field_id}_3' style='{$fields[field_loop].field_style}'>
      {section name=date3 loop=$fields[field_loop].date_array3}
        <option value='{$fields[field_loop].date_array3[date3].value}'{$fields[field_loop].date_array3[date3].selected}>{if $smarty.section.date3.first}[ {lang_print id=$fields[field_loop].date_array3[date3].name} ]{else}{$fields[field_loop].date_array3[date3].name}{/if}</option>
      {/section}
      </select>
      </div>


      {* CHECKBOXES *}
      {elseif $fields[field_loop].field_type == 6}
    
        {* LOOP THROUGH FIELD OPTIONS *}
        <div id='field_options_{$fields[field_loop].field_id}'>
        {section name=option_loop loop=$fields[field_loop].field_options}
          <div>
          <input type='checkbox' onclick="ShowHideDeps('{$fields[field_loop].field_id}', '{$fields[field_loop].field_options[option_loop].value}', '{$fields[field_loop].field_type}');" style='{$fields[field_loop].field_style}' name='field_{$fields[field_loop].field_id}[]' id='label_{$fields[field_loop].field_id}_{$fields[field_loop].field_options[option_loop].value}' value='{$fields[field_loop].field_options[option_loop].value}'{if $fields[field_loop].field_options[option_loop].value|in_array:$fields[field_loop].field_value} CHECKED{/if}>
          <label for='label_{$fields[field_loop].field_id}_{$fields[field_loop].field_options[option_loop].value}'>{lang_print id=$fields[field_loop].field_options[option_loop].label}</label>
          </div>

          {* DISPLAY DEPENDENT FIELDS *}
          {if $fields[field_loop].field_options[option_loop].dependency == 1}

	    {* SELECT BOX *}
	    {if $fields[field_loop].field_options[option_loop].dep_field_type == 3}
              <div id='field_{$fields[field_loop].field_id}_option{$fields[field_loop].field_options[option_loop].value}' style='margin: 5px 5px 10px 5px;{if $fields[field_loop].field_options[option_loop].value != $fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$fields[field_loop].field_options[option_loop].dep_field_title}{if $fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$fields[field_loop].field_options[option_loop].dep_field_id}'>
	        <option value='-1'></option>
	        {* LOOP THROUGH DEP FIELD OPTIONS *}
	        {section name=option2_loop loop=$fields[field_loop].field_options[option_loop].dep_field_options}
	          <option id='op' value='{$fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
	        {/section}
	      </select>
              </div>	  

	    {* TEXT FIELD *}
	    {else}
              <div id='field_{$fields[field_loop].field_id}_option{$fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $fields[field_loop].field_options[option_loop].value|in_array:$fields[field_loop].field_value == FALSE} display: none;{/if}'>
              {lang_print id=$fields[field_loop].field_options[option_loop].dep_field_title}{if $fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <input type='text' class='text' name='field_{$fields[field_loop].field_options[option_loop].dep_field_id}' value='{$fields[field_loop].field_options[option_loop].dep_field_value}' style='{$fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
              </div>
	    {/if}

          {/if}

        {/section}
        </div>


    {/if}

    <div class='form_desc'>{lang_print id=$fields[field_loop].field_desc}</div>

    {capture assign='current_subnet'}{lang_print id=$user->subnet_info.subnet_name}{/capture}
    {if $fields[field_loop].field_id == $setting.setting_subnet_field1_id || $fields[field_loop].field_id == $setting.setting_subnet_field2_id}{lang_sprintf id=766 1=$current_subnet}{/if}

    {capture assign='field_error'}{lang_print id=$fields[field_loop].field_error}{/capture}
    {if $field_error != ""}<div class='form_error'><img src='./images/icons/error16.gif' border='0' class='icon'> {$field_error}</div>{/if}
    </td>
    </tr>
  {/section}

<tr>
<td class='form1'>&nbsp;</td>
<td class='form2'><input type='submit' class='button' value='{lang_print id=173}'></td>
<input type='hidden' name='task' value='dosave'>
<input type='hidden' name='cat_id' value='{$cat_id}'>
</tr>
</form>
</table>




{include file='footer.tpl'}