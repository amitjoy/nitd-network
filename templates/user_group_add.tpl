{include file='header.tpl'}

{* $Id: user_group_add.tpl 34 2009-01-24 04:17:28Z john $ *}

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td valign='top'>
  <img src='./images/icons/group_add48.gif' border='0' class='icon_big' />
  <div class='page_header'>{lang_print id=2000095}</div>
  <div>{lang_print id=2000096}</div>
</td>
<td valign='top'>
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td class='button' nowrap='nowrap'>
        <a href='user_group.php'>
          <img src='./images/icons/back16.gif' border='0' class='button' />
          {lang_print id=2000120}
        </a>
      </td>
    </tr>
  </table>
</td>
</tr>
</table>

{* JAVASCRIPT FOR CATEGORIES/FIELDS *}
{literal}
<script type='text/javascript'>
<!--

  var cats = {0:{'title':'','subcats':{}}{/literal}{section name=cat_loop loop=$cats}, {$cats[cat_loop].cat_id}{literal}:{'title':'{/literal}{capture assign='cat_title'}{lang_print id=$cats[cat_loop].cat_title}{/capture}{$cat_title|replace:"&#039;":"\'"}{literal}', 'subcats':{{/literal}{section name=subcat_loop loop=$cats[cat_loop].subcats}{if !$smarty.section.subcat_loop.first}, {/if}{$cats[cat_loop].subcats[subcat_loop].subcat_id}:'{capture assign='subcat_title'}{lang_print id=$cats[cat_loop].subcats[subcat_loop].subcat_title}{/capture}{$subcat_title|replace:"&#039;":"\'"}'{/section}{literal}}}{/literal}{/section}{literal}};

  {/literal}{if $cats|@count > 0}{literal}
  window.addEvent('domready', function(){
    for(c in cats) {
      var optn = document.createElement("option");
      optn.text = cats[c].title;
      optn.value = c;
      if(c == {/literal}{$group_info.group_groupcat_id}{literal}) { optn.selected = true; }
      $('group_groupcat_id').options.add(optn);
    }
    populateSubcats({/literal}{$group_info.group_groupcat_id}{literal});
  });
  {/literal}{/if}{literal}

  function populateSubcats(group_groupcat_id) {
    var subcats = cats[group_groupcat_id].subcats;
    var subcatHash = new Hash(subcats);
    $$('tr[id^=all_fields_]').each(function(el) { if(el.id == 'all_fields_'+group_groupcat_id) { el.style.display = ''; } else { el.style.display = 'none'; }});
    if(group_groupcat_id == 0 || subcatHash.getValues().length == 0) {
      $('group_groupsubcat_id').options.length = 1;
      $('group_groupsubcat_id').style.display = 'none';
    } else {
      $('group_groupsubcat_id').options.length = 1;
      $('group_groupsubcat_id').style.display = '';
      for(s in subcats) {
        var optn = document.createElement("option");
        optn.text = subcats[s];
        optn.value = s;
        if(s == {/literal}{$group_info.group_groupsubcat_id}{literal}) { optn.selected = true; }
        $('group_groupsubcat_id').options.add(optn);
      }
    }
  }

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

<br>

{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <div class='error'><img src='./images/error.gif' border='0' class='icon'> {lang_print id=$is_error}</div>
  </td></tr></table>
<br>
{/if}

<table cellpadding='0' cellspacing='0' class='form'>
<tr><form action='user_group_add.php' method='POST'>
<td class='form1'>{lang_print id=2000094}*</td>
<td class='form2'><input type='text' class='text' name='group_title' value='{$group_info.group_title}' maxlength='100' size='30'></td>
</tr>
<tr>
<td class='form1'>{lang_print id=2000098}</td>
<td class='form2'><textarea rows='6' cols='50' name='group_desc'>{$group_info.group_desc}</textarea></td>
</tr>
{if $cats|@count > 0}
  <tr>
  <td class='form1'>{lang_print id=2000116}*</td>
  <td class='form2' nowrap='nowrap'>
    <select name='group_groupcat_id' id='group_groupcat_id' onChange='populateSubcats(this.options[this.selectedIndex].value);'></select>
    <select name='group_groupsubcat_id' id='group_groupsubcat_id' style='display: none;'><option value='0'></option></select>
  </td>
  </tr>

  {section name=cat_loop loop=$cats}
    {section name=field_loop loop=$cats[cat_loop].fields}
      <tr id='all_fields_{$cats[cat_loop].cat_id}'>
      <td class='form1'>{lang_print id=$cats[cat_loop].fields[field_loop].field_title}{if $cats[cat_loop].fields[field_loop].field_required != 0}*{/if}</td>
      <td class='form2'>

      {* TEXT FIELD *}
      {if $cats[cat_loop].fields[field_loop].field_type == 1}
        <div><input type='text' class='text' name='field_{$cats[cat_loop].fields[field_loop].field_id}' id='field_{$cats[cat_loop].fields[field_loop].field_id}' value='{$cats[cat_loop].fields[field_loop].field_value}' style='{$cats[cat_loop].fields[field_loop].field_style}' maxlength='{$cats[cat_loop].fields[field_loop].field_maxlength}'></div>

        {* JAVASCRIPT FOR CREATING SUGGESTION BOX *}
        {if $cats[cat_loop].fields[field_loop].field_options != "" && $cats[cat_loop].fields[field_loop].field_options|@count != 0}
        {literal}
        <script type="text/javascript">
        <!-- 
        window.addEvent('domready', function(){
	  var options = {
		script:"misc_js.php?task=suggest_field&limit=5&{/literal}{section name=option_loop loop=$cats[cat_loop].fields[field_loop].field_options}options[]={$cats[cat_loop].fields[field_loop].field_options[option_loop].label}&{/section}{literal}",
		varname:"input",
		json:true,
		shownoresults:false,
		maxresults:5,
		multisuggest:false,
		callback: function (obj) {  }
	  };
	  var as_json{/literal}{$cats[cat_loop].fields[field_loop].field_id}{literal} = new bsn.AutoSuggest('field_{/literal}{$cats[cat_loop].fields[field_loop].field_id}{literal}', options);
        });
        //-->
        </script>
        {/literal}
        {/if}


      {* TEXTAREA *}
      {elseif $cats[cat_loop].fields[field_loop].field_type == 2}
        <div><textarea rows='6' cols='50' name='field_{$cats[cat_loop].fields[field_loop].field_id}' style='{$cats[cat_loop].fields[field_loop].field_style}'>{$cats[cat_loop].fields[field_loop].field_value}</textarea></div>



      {* SELECT BOX *}
      {elseif $cats[cat_loop].fields[field_loop].field_type == 3}
        <div><select name='field_{$cats[cat_loop].fields[field_loop].field_id}' id='field_{$cats[cat_loop].fields[field_loop].field_id}' onchange="ShowHideDeps('{$cats[cat_loop].fields[field_loop].field_id}', this.value);" style='{$cats[cat_loop].fields[field_loop].field_style}'>
        <option value='-1'></option>
        {* LOOP THROUGH FIELD OPTIONS *}
        {section name=option_loop loop=$cats[cat_loop].fields[field_loop].field_options}
          <option id='op' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value == $cats[cat_loop].fields[field_loop].field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].label}</option>
        {/section}
        </select>
        </div>
        {* LOOP THROUGH DEPENDENT FIELDS *}
        <div id='field_options_{$cats[cat_loop].fields[field_loop].field_id}'>
        {section name=option_loop loop=$cats[cat_loop].fields[field_loop].field_options}
          {if $cats[cat_loop].fields[field_loop].field_options[option_loop].dependency == 1}

	    {* SELECT BOX *}
	    {if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_type == 3}
              <div id='field_{$cats[cat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 5px 5px 10px 5px;{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_id}'>
	        <option value='-1'></option>
	        {* LOOP THROUGH DEP FIELD OPTIONS *}
	        {section name=option2_loop loop=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options}
	          <option id='op' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
	        {/section}
	      </select>
              </div>	  

	    {* TEXT FIELD *}
	    {else}
              <div id='field_{$cats[cat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 5px 5px 10px 5px;{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
             <input type='text' class='text' name='field_{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_id}' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_value}' style='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
              </div>
	    {/if}

          {/if}
        {/section}
        </div>
    


      {* RADIO BUTTONS *}
      {elseif $cats[cat_loop].fields[field_loop].field_type == 4}
    
        {* LOOP THROUGH FIELD OPTIONS *}
        <div id='field_options_{$cats[cat_loop].fields[field_loop].field_id}'>
        {section name=option_loop loop=$cats[cat_loop].fields[field_loop].field_options}
          <div>
          <input type='radio' class='radio' onclick="ShowHideDeps('{$cats[cat_loop].fields[field_loop].field_id}', '{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}');" style='{$cats[cat_loop].fields[field_loop].field_style}' name='field_{$cats[cat_loop].fields[field_loop].field_id}' id='label_{$cats[cat_loop].fields[field_loop].field_id}_{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value == $cats[cat_loop].fields[field_loop].field_value} CHECKED{/if}>
          <label for='label_{$cats[cat_loop].fields[field_loop].field_id}_{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}'>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].label}</label>
          </div>

          {* DISPLAY DEPENDENT FIELDS *}
          {if $cats[cat_loop].fields[field_loop].field_options[option_loop].dependency == 1}

	    {* SELECT BOX *}
	    {if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_type == 3}
              <div id='field_{$cats[cat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_id}'>
	        <option value='-1'></option>
	        {* LOOP THROUGH DEP FIELD OPTIONS *}
	        {section name=option2_loop loop=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options}
	          <option id='op' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
	        {/section}
	      </select>
              </div>	  

	    {* TEXT FIELD *}
	    {else}
              <div id='field_{$cats[cat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
             <input type='text' class='text' name='field_{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_id}' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_value}' style='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
              </div>
	    {/if}

          {/if}

        {/section}
        </div>



      {* DATE FIELD *}
      {elseif $cats[cat_loop].fields[field_loop].field_type == 5}
        <div>
        <select name='field_{$cats[cat_loop].fields[field_loop].field_id}_1' style='{$cats[cat_loop].fields[field_loop].field_style}'>
        {section name=date1 loop=$cats[cat_loop].fields[field_loop].date_array1}
          <option value='{$cats[cat_loop].fields[field_loop].date_array1[date1].value}'{$cats[cat_loop].fields[field_loop].date_array1[date1].selected}>{if $smarty.section.date1.first}[ {lang_print id=$cats[cat_loop].fields[field_loop].date_array1[date1].name} ]{else}{$cats[cat_loop].fields[field_loop].date_array1[date1].name}{/if}</option>
        {/section}
        </select>

        <select name='field_{$cats[cat_loop].fields[field_loop].field_id}_2' style='{$cats[cat_loop].fields[field_loop].field_style}'>
        {section name=date2 loop=$cats[cat_loop].fields[field_loop].date_array2}
          <option value='{$cats[cat_loop].fields[field_loop].date_array2[date2].value}'{$cats[cat_loop].fields[field_loop].date_array2[date2].selected}>{if $smarty.section.date2.first}[ {lang_print id=$cats[cat_loop].fields[field_loop].date_array2[date2].name} ]{else}{$cats[cat_loop].fields[field_loop].date_array2[date2].name}{/if}</option>
        {/section}
        </select>

        <select name='field_{$cats[cat_loop].fields[field_loop].field_id}_3' style='{$cats[cat_loop].fields[field_loop].field_style}'>
        {section name=date3 loop=$cats[cat_loop].fields[field_loop].date_array3}
          <option value='{$cats[cat_loop].fields[field_loop].date_array3[date3].value}'{$cats[cat_loop].fields[field_loop].date_array3[date3].selected}>{if $smarty.section.date3.first}[ {lang_print id=$cats[cat_loop].fields[field_loop].date_array3[date3].name} ]{else}{$cats[cat_loop].fields[field_loop].date_array3[date3].name}{/if}</option>
        {/section}
        </select>
        </div>



      {* CHECKBOXES *}
      {elseif $cats[cat_loop].fields[field_loop].field_type == 6}
    
        {* LOOP THROUGH FIELD OPTIONS *}
        <div id='field_options_{$cats[cat_loop].fields[field_loop].field_id}'>
        {section name=option_loop loop=$cats[cat_loop].fields[field_loop].field_options}
          <div>
          <input type='checkbox' onclick="ShowHideDeps('{$cats[cat_loop].fields[field_loop].field_id}', '{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}', '{$cats[cat_loop].fields[field_loop].field_type}');" style='{$cats[cat_loop].fields[field_loop].field_style}' name='field_{$cats[cat_loop].fields[field_loop].field_id}[]' id='label_{$cats[cat_loop].fields[field_loop].field_id}_{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value|in_array:$cats[cat_loop].fields[field_loop].field_value} CHECKED{/if}>
          <label for='label_{$cats[cat_loop].fields[field_loop].field_id}_{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}'>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].label}</label>
          </div>

          {* DISPLAY DEPENDENT FIELDS *}
          {if $cats[cat_loop].fields[field_loop].field_options[option_loop].dependency == 1}
	    {* SELECT BOX *}
	    {if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_type == 3}
              <div id='field_{$cats[cat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_id}'>
	        <option value='-1'></option>
	        {* LOOP THROUGH DEP FIELD OPTIONS *}
	        {section name=option2_loop loop=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options}
	          <option id='op' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
	        {/section}
	      </select>
              </div>	  

	    {* TEXT FIELD *}
	    {else}
              <div id='field_{$cats[cat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
             <input type='text' class='text' name='field_{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_id}' value='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_value}' style='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$cats[cat_loop].fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
              </div>
	    {/if}
          {/if}

        {/section}
        </div>

      {/if}

      <div class='form_desc'>{lang_print id=$cats[cat_loop].fields[field_loop].field_desc}</div>
      {capture assign='field_error'}{lang_print id=$cats[cat_loop].fields[field_loop].field_error}{/capture}
      {if $field_error != ""}<div class='form_error'><img src='./images/icons/error16.gif' border='0' class='icon'> {$field_error}</div>{/if}
      </td>
      </tr>

    {/section}
  {/section}

{/if}


{* SHOW SEARCH PRIVACY OPTIONS IF ALLOWED BY ADMIN *}
{if $user->level_info.level_group_search == 1}
  <tr>
  <td class='form1' width='150'>{lang_print id=2000104}</td>
  <td class='form2'>
    <table cellpadding='0' cellspacing='0'>
      <tr><td><input type='radio' name='group_search' id='group_search_1' value='1'{if $group_info.group_search == 1} CHECKED{/if}></td><td><label for='group_search_1'>{lang_print id=2000105}</label></td></tr>
      <tr><td><input type='radio' name='group_search' id='group_search_0' value='0'{if $group_info.group_search == 0} CHECKED{/if}></td><td><label for='group_search_0'>{lang_print id=2000106}</label></td></tr>
    </table>
  </td>
 </tr>
{/if}

{* SHOW USER INVITATION OPTION *}
<tr>
<td class='form1' width='120'>{lang_print id=2000216}</td>
<td class='form2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='group_invite' id='group_invite_1' value='1'{if $group_info.group_invite == 1} CHECKED{/if}></td><td><label for='group_invite_1'>{lang_print id=2000217}</label></td></tr>
  <tr><td><input type='radio' name='group_invite' id='group_invite_0' value='0'{if $group_info.group_invite == 0} CHECKED{/if}></td><td><label for='group_invite_0'>{lang_print id=2000218}</label></td></tr>
  </table>
</td>
</tr>

{* SHOW APPROVAL SETTING IF ENABLED BY ADMIN *}
{if $user->level_info.level_group_approval == 1}
  <tr>
  <td class='form1' width='120'>{lang_print id=2000139}</td>
  <td class='form2'>
    <div class='group_form_desc'>{lang_print id=2000187}</div>
    <table cellpadding='0' cellspacing='0'>
    <tr><td><input type='radio' name='group_approval' id='group_approval0' value='0'{if $group_info.group_approval == 0} CHECKED{/if}></td><td><label for='group_approval0'>{lang_print id=2000142}</label></td></tr>
    <tr><td><input type='radio' name='group_approval' id='group_approval1' value='1'{if $group_info.group_approval == 1} CHECKED{/if}></td><td><label for='group_approval1'>{lang_print id=2000100}</label></td></tr>
    </table>
  </td>
  </tr>
{/if}

{* SHOW ALLOW PRIVACY SETTINGS *}
{if $privacy_options|@count > 1}
  <tr>
  <td class='form1' width='120'>{lang_print id=2000107}</td>
  <td class='form2'>
    <div class='group_form_desc'>{lang_print id=2000108}</div>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$privacy_options name=privacy_loop key=k item=v}
      <tr>
      <td><input type='radio' name='group_privacy' id='privacy_{$k}' value='{$k}'{if $group_info.group_privacy == $k} checked='checked'{/if}></td>
      <td><label for='privacy_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
  </td>
  </tr>
{/if}

{* SHOW ALLOW COMMENT SETTINGS *}
{if $comment_options|@count > 1}
  <tr>
  <td class='form1' width='120'>{lang_print id=2000109}</td>
  <td class='form2'>
    <div class='group_form_desc'>{lang_print id=2000110}</div>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$comment_options name=comment_loop key=k item=v}
      <tr>
      <td><input type='radio' name='group_comments' id='comment_{$k}' value='{$k}'{if $group_info.group_comments == $k} checked='checked'{/if}></td>
      <td><label for='comment_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
  </td>
  </tr>
{/if}


{* SHOW ALLOW DISCUSSION SETTINGS *}
{if $discussion_options|@count > 1}
  <tr>
  <td class='form1' width='120'>{lang_print id=2000111}</td>
  <td class='form2'>
    <div class='group_form_desc'>{lang_print id=2000112}</div>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$discussion_options name=discussion_loop key=k item=v}
      <tr>
      <td><input type='radio' name='group_discussion' id='discussion_{$k}' value='{$k}'{if $group_info.group_discussion == $k} checked='checked'{/if}></td>
      <td><label for='discussion_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
  </td>
  </tr>
{/if}


{* SHOW ALLOW UPLOAD SETTINGS *}
{if $upload_options|@count > 1}
  <tr>
  <td class='form1' width='120'>{lang_print id=2000212}</td>
  <td class='form2'>
    <div class='group_form_desc'>{lang_print id=2000213}</div>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$upload_options name=upload_loop key=k item=v}
      <tr>
      <td><input type='radio' name='group_upload' id='upload_{$k}' value='{$k}'{if $group_info.group_upload == $k} checked='checked'{/if}></td>
      <td><label for='upload_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
  </td>
  </tr>
{/if}


{* SHOW ALLOW TAG SETTINGS *}
{if $tag_options|@count > 1}
  <tr>
  <td class='form1' width='120'>{lang_print id=2000214}</td>
  <td class='form2'>
    <div class='group_form_desc'>{lang_print id=2000215}</div>
    <table cellpadding='0' cellspacing='0'>
    {foreach from=$tag_options name=tag_loop key=k item=v}
      <tr>
      <td><input type='radio' name='groupalbum_tag' id='tag_{$k}' value='{$k}'{if $groupalbum_info.groupalbum_tag == $k} checked='checked'{/if}></td>
      <td><label for='tag_{$k}'>{lang_print id=$v}</label></td>
      </tr>
    {/foreach}
    </table>
  </td>
  </tr>
{/if}

<tr>
<td>&nbsp;</td>
<td>
  <table cellpadding='0' cellspacing='0' style='margin-top: 10px;'>
  <tr>
  <td>
    <input type='submit' class='button' value='{lang_print id=2000113}'>&nbsp;
    <input type='hidden' name='task' value='doadd'>
    </form>
  </td>
  <td>
    <form action='user_group.php' method='get'>
    <input type='submit' class='button' value='{lang_print id=39}'>
    </form>
  </td>
  </tr>
  </table>
</td>
</tr>
</table>

{include file='footer.tpl'}