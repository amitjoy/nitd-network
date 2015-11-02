{include file='header.tpl'}

{* $Id: user_group_edit.tpl 34 2009-01-24 04:17:28Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
  <tr>
    <td class='tab0'>&nbsp;</td>
    <td class='tab1' NOWRAP><a href='user_group_edit.php?group_id={$group->group_info.group_id}'>{lang_print id=2000097}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_group_edit_members.php?group_id={$group->group_info.group_id}'>{lang_print id=2000118}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_group_edit_settings.php?group_id={$group->group_info.group_id}'>{lang_print id=2000119}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab3'>&nbsp;</td>
  </tr>
</table>

<table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
    <td valign='top'>
      <img src='./images/icons/group_edit48.gif' border='0' class='icon_big' />
      {capture assign="linked_groupname"}<a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}'>{$group->group_info.group_title|truncate:30:"...":true}</a>{/capture}
      <div class='page_header'>{lang_sprintf id=2000121 1=$linked_groupname}</div>
      {lang_print id=2000122}
    </td>
    <td valign='top' align='right'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td class='button'>
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
<br />


{* IF GROUP WAS JUST CREATED, SHOW SUCCESS MESSAGE *}
{if $justadded == 1}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <img src='./images/success.gif' border='0' class='icon' />
    {lang_print id=2000123}
  </td></tr></table>
  <br />
{/if}


{* SHOW RESULT MESSAGE *}
{if $result != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <img src='./images/success.gif' border='0' class='icon' />
    {lang_print id=191}
  </td></tr></table>
  <br />
{/if}

{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='result'>
    <img src='./images/error.gif' class='icon' border='0' />
    {lang_print id=$is_error}
  </td></tr></table>
  <br>
{/if}


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
      if(c == {/literal}{$group->group_info.group_groupcat_id}{literal}) { optn.selected = true; }
      $('group_groupcat_id').options.add(optn);
    }
    populateSubcats({/literal}{$group->group_info.group_groupcat_id}{literal});
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
        if(s == {/literal}{$group->group_info.group_groupsubcat_id}{literal}) { optn.selected = true; }
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


{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <form action='user_group_edit.php' method='post' target='_parent'>
  <div style='margin-top: 10px;'>{lang_sprintf id=2000186 1=$group->group_info.group_title}</div>
  <br>
  <input type='submit' class='button' value='{lang_print id=175}'>
  <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  <input type='hidden' name='task' value='delete_do'>
  <input type='hidden' name='group_id' value='{$group->group_info.group_id}'>
  </form>
</div>


{* SHOW GROUP PHOTO UPLOAD *}
<div class='header'>{lang_print id=2000124}</div>
<div class='group_box'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='editprofile_photoleft'>
    <div style='text-align: center;'>
      {lang_print id=770}<br>
      <table cellpadding='0' cellspacing='0' width='202'>
      <tr><td class='editprofile_photo'><img src='{$group->group_photo("./images/nophoto.gif")}' border='0'></td></tr>
      </table>
      {if $group->group_photo() != ""}  <br>[ <a href='user_group_edit.php?group_id={$group->group_info.group_id}&task=remove'>{lang_print id=771}</a> ]{/if}
    </div>
  </td>
  <td class='editprofile_photoright'>
    <form action='user_group_edit.php' method='post' enctype='multipart/form-data'>
    {lang_print id=772}<br><input type='file' class='text' name='photo' size='30'>
    <input type='submit' class='button' value='{lang_print id=714}'>
    <input type='hidden' name='task' value='upload'>
    <input type='hidden' name='MAX_FILE_SIZE' value='5000000'>
    <input type='hidden' name='group_id' value='{$group->group_info.group_id}'>
    </form>
    <br>{lang_sprintf id=2000125 1=$group->groupowner_level_info.level_group_photo_exts}
  </td>
  </tr>
  </table>
</div>

<br>

{* SHOW GROUP SETTINGS *}
<div class='header'>{lang_print id=2000097}</div>
<div class='group_box'>
<form action='user_group_edit.php' method='post'>
<table cellpadding='0' cellspacing='0'>
<td class='form1'>{lang_print id=2000094}*</td>
<td class='form2'><input type='text' class='text' name='group_title' value='{$group->group_info.group_title}' maxlength='100' size='30'></td>
</tr>
<tr>
<td class='form1'>{lang_print id=2000098}</td>
<td class='form2'><textarea rows='6' cols='50' name='group_desc'>{$group->group_info.group_desc}</textarea></td>
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
</table>
</div>

<br>

{* SHOW SUBMIT BUTTONS *}
<table cellpadding='0' cellspacing='0'>
<tr>
<td>
  <input type='submit' class='button' value='{lang_print id=173}'>&nbsp;
  <input type='hidden' name='task' value='dosave'>
  <input type='hidden' name='group_id' value='{$group->group_info.group_id}'>
  </form>
</td>
<td>
  <form action='user_group.php' method='get'>
  <input type='submit' class='button' value='{lang_print id=39}'>&nbsp;
  </form>
</td>
<td style='padding-left: 10px;'>
  {if $group->user_rank == 2}
    <a href="javascript:TB_show('{lang_print id=2000177}', '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', './images/trans.gif');"><img src='./images/icons/group_delete16.gif' border='0' class='button'>{lang_print id=2000177}</a>
  {/if}
</td>
</tr>
</table>

{include file='footer.tpl'}