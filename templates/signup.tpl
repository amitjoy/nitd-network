{include file='header.tpl'}

{* $Id: signup.tpl 235 2009-11-13 04:30:39Z phil $ *}

{if $step == 5}
{* SHOW COMPLETION PAGE *}
  <img src='./images/icons/signup48.gif' border='0' class='icon_big'>
  <div class='page_header'>{lang_print id=729}</div>
  <div>{lang_print id=730}
  {if $setting.setting_signup_enable == 0}{lang_print id=731}{/if}
  {if $setting.setting_signup_randpass == 1}{lang_print id=732}{/if}
  {if $setting.setting_signup_verify == 0}{lang_print id=733}{else}{lang_print id=734}{/if}
  </div>
  <br />
  <br />
  
  {if $setting.setting_signup_verify == 0 && $setting.setting_signup_enable != 0 && $setting.setting_signup_randpass == 0}
    <form action='login.php' method='GET'>
    <input type='submit' class='button' value='{lang_print id=693}'>
    <input type='hidden' name='email' value='{$new_user->user_info.user_email}'>
    </form>
  {else}
    <form action='home.php' method='GET'>
    <input type='submit' class='button' value='{lang_print id=735}'>
    </form>
  {/if}

{* SHOW STEP FOUR *}
{elseif $step == 4}
  <img src='./images/icons/signup48.gif' border='0' class='icon_big'>
  <div class='page_header'>{lang_print id=722}</div>
  <div>{lang_print id=723}</div>
  <br />
  <br />

  <form action='signupon13.php' method='POST'>
  <table cellpadding='0' cellspacing='0' class='form'>
  <tr>
    <td>
      <b>{lang_print id=724}</b>
      <div>{lang_print id=725}</div>
      <textarea name='invite_emails' rows='3' cols='70' style='margin-top: 3px;'></textarea><br><br>
    </td>
  </tr>
  <tr>
    <td>
      <b>{lang_print id=726}</b>
      <div>{lang_print id=727}</div>
      <textarea name='invite_message' rows='3' cols='70' style='margin-top: 3px;'></textarea>
    </td>
  </tr>
  </table>

  <br>

  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    <input type='submit' class='button' value='{lang_print id=728}'>&nbsp;
    <input type='hidden' name='task' value='{$next_task}'>
    </form>
  </td>
  <td>
    <form action='signupon13.php' method='POST'><input type='submit' class='button' value='{lang_print id=717}'>
    <input type='hidden' name='task' value='{$next_task}'>
    <input type='hidden' name='step' value='{$step}'>
    </form>
  </td>
  </tr>
  </table>





{* SHOW STEP THREE *}
{elseif $step == 3}
  <img src='./images/icons/signup48.gif' border='0' class='icon_big'>
  <div class='page_header'>{lang_print id=712}</div>
  <div>{lang_print id=713}</div><br>
  <br>

  {* SHOW ERROR MESSAGE *}
  {if $is_error != 0}
    <table cellpadding='0' cellspacing='0'>
    <tr><td class='result'>
      <div class='error'><img src='./images/error.gif' border='0' class='icon'> {lang_print id=$is_error}</div>
    </td></tr></table>
    <br>
  {/if}

  <table cellpadding='0' cellspacing='0' align='center' width='450'>
  <tr>
  <td class='signup_photo'>
    <form action='signupon13.php' method='POST' enctype='multipart/form-data'>
    <input type='file' class='text' name='photo' size='40'>
    <input type='submit' class='button' value='{lang_print id=714}'>
    <input type='hidden' name='step' value='{$step}'>
    <input type='hidden' name='task' value='{$next_task}'>
    <input type='hidden' name='MAX_FILE_SIZE' value='5000000'>
    </form>
    <div class='signup_photo_desc'>
      {lang_print id=715} {$new_user->level_info.level_photo_exts}.
    </div>
  </td>
  </table>

  <br>

  {* SHOW USER PHOTO IF ONE HAS BEEN UPLOADED, OTHERWISE SHOW SKIP BUTTON *}
  {if $new_user->user_photo() != ""}
    <div class='center'>
      <img src='{$new_user->user_photo()}' border='0' class='photo'><br><br>
      <form action='signupon13.php' method='POST'>
      <input type='submit' class='button' value='{lang_print id=716}'>
      <input type='hidden' name='task' value='{$last_task}'>
      </form>
    </div>
  {else}
    <div class='center'>
      <div style='font-size: 22px; font-weight: bold;'>{lang_print id=349}</div><br>
      <form action='signupon13.php' method='POST'>
      <input type='submit' class='button' value='{lang_print id=717}'>
      <input type='hidden' name='task' value='{$last_task}'>
      </form>
    </div>
  {/if}

  <br>




{* SHOW STEP TWO *}
{elseif $step == 2}
  <img src='./images/icons/signup48.gif' border='0' class='icon_big'>
  <div class='page_header'>{lang_print id=710}</div>
  <div>{lang_print id=711}</div><br>
  <br><br>

  {* SHOW ERROR MESSAGE *}
  {if $is_error != 0}
    <table cellpadding='0' cellspacing='0'>
    <tr><td class='result'>
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

  <form action='signupon13.php' method='POST'>
  {* LOOP THROUGH TABS *}
  {section name=cat_loop loop=$cats}
  {section name=subcat_loop loop=$cats[cat_loop].subcats}
    {if $cats[cat_loop].subcats[subcat_loop].fields|@count != 0}
    <div class='signup_header'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].subcat_title}</div>
    <table cellpadding='0' cellspacing='0'>

    {* LOOP THROUGH FIELDS IN TAB *}
    {section name=field_loop loop=$cats[cat_loop].subcats[subcat_loop].fields}
      <tr>
      <td class='form1' width='150'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_required != 0}*{/if}</td>
      <td class='form2'>



      {* TEXT FIELD *}
      {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 1}
        <div><input type='text' class='text' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value}' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}' maxlength='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_maxlength}'></div>

        {* JAVASCRIPT FOR CREATING SUGGESTION BOX *}
        {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options != "" && $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options|@count != 0}
        {literal}
        <script type="text/javascript">
        <!-- 
        window.addEvent('domready', function(){
	  var options = {
		script:"misc_js.php?task=suggest_field&limit=5&{/literal}{section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}options[]={$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}&{/section}{literal}",
		varname:"input",
		json:true,
		shownoresults:false,
		maxresults:5,
		multisuggest:false,
		callback: function (obj) {  }
	  };
	  var as_json{/literal}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}{literal} = new bsn.AutoSuggest('field_{/literal}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}{literal}', options);
        });
        //-->
        </script>
        {/literal}
        {/if}


      {* TEXTAREA *}
      {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 2}
        <div><textarea rows='6' cols='50' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}'>{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value}</textarea></div>



      {* SELECT BOX *}
      {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 3}
        <div><select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' onchange="ShowHideDeps('{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}', this.value);" style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}'>
        <option value='-1'></option>
        {* LOOP THROUGH FIELD OPTIONS *}
        {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
          <option id='op' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}</option>
        {/section}
        </select>
        </div>
        {* LOOP THROUGH DEPENDENT FIELDS *}
        <div id='field_options_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}'>
        {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
          {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dependency == 1}

	    {* SELECT BOX *}
	    {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_type == 3}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 5px 5px 10px 5px;{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_id}'>
	        <option value='-1'></option>
	        {* LOOP THROUGH DEP FIELD OPTIONS *}
	        {section name=option2_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options}
	          <option id='op' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
	        {/section}
	      </select>
              </div>	  

	    {* TEXT FIELD *}
	    {else}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 5px 5px 10px 5px;{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
             <input type='text' class='text' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_id}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_value}' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
              </div>
	    {/if}

          {/if}
        {/section}
        </div>
    


      {* RADIO BUTTONS *}
      {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 4}
    
        {* LOOP THROUGH FIELD OPTIONS *}
        <div id='field_options_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}'>
        {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
          <div>
          <input type='radio' class='radio' onclick="ShowHideDeps('{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}', '{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}');" style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' id='label_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} CHECKED{/if}>
          <label for='label_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}</label>
          </div>

          {* DISPLAY DEPENDENT FIELDS *}
          {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dependency == 1}

	    {* SELECT BOX *}
	    {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_type == 3}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_id}'>
	        <option value='-1'></option>
	        {* LOOP THROUGH DEP FIELD OPTIONS *}
	        {section name=option2_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options}
	          <option id='op' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
	        {/section}
	      </select>
              </div>	  

	    {* TEXT FIELD *}
	    {else}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
             <input type='text' class='text' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_id}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_value}' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
              </div>
	    {/if}

          {/if}

        {/section}
        </div>



      {* DATE FIELD *}
      {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 5}
        <div>
        <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_1' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}'>
        {section name=date1 loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1}
          <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1[date1].value}'{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1[date1].selected}>{if $smarty.section.date1.first}[ {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1[date1].name} ]{else}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1[date1].name}{/if}</option>
        {/section}
        </select>

        <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_2' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}'>
        {section name=date2 loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2}
          <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2[date2].value}'{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2[date2].selected}>{if $smarty.section.date2.first}[ {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2[date2].name} ]{else}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2[date2].name}{/if}</option>
        {/section}
        </select>

        <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_3' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}'>
        {section name=date3 loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3}
          <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3].value}'{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3].selected}>{if $smarty.section.date3.first}[ {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3].name} ]{else}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3].name}{/if}</option>
        {/section}
        </select>
        </div>



      {* CHECKBOXES *}
      {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 6}
    
        {* LOOP THROUGH FIELD OPTIONS *}
        <div id='field_options_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}'>
        {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
          <div>
          <input type='checkbox' onclick="ShowHideDeps('{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}', '{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}', '{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type}');" style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_style}' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}[]' id='label_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value|in_array:$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} CHECKED{/if}>
          <label for='label_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}</label>
          </div>

          {* DISPLAY DEPENDENT FIELDS *}
          {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dependency == 1}
	    {* SELECT BOX *}
	    {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_type == 3}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_id}'>
	        <option value='-1'></option>
	        {* LOOP THROUGH DEP FIELD OPTIONS *}
	        {section name=option2_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options}
	          <option id='op' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_options[option2_loop].label}</option>
	        {/section}
	      </select>
              </div>	  

	    {* TEXT FIELD *}
	    {else}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_option{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' style='margin: 0px 5px 10px 23px;{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value != $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} display: none;{/if}'>
              {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_required != 0}*{/if}
             <input type='text' class='text' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_id}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_value}' style='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_style}' maxlength='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].dep_field_maxlength}'>
              </div>
	    {/if}
          {/if}

        {/section}
        </div>

      {/if}

      <div class='form_desc'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_desc}</div>
      {capture assign='field_error'}{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_error}{/capture}
      {if $field_error != ""}<div class='form_error'><img src='./images/icons/error16.gif' border='0' class='icon'> {$field_error}</div>{/if}
    </td>
    </tr>
    {/section}
  </table>
  <br>
  {/if}
  {/section}
  {/section}

  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='form1' width='100'>&nbsp;</td>
  <td class='form2'><input type='submit' class='button' value='{lang_print id=693}'></td>
  </tr>
  </table>
  <input type='hidden' name='task' value='{$next_task}'>
  <input type='hidden' name='step' value='{$step}'>
  <input type='hidden' name='signup_email' value='{$signup_email}'>
  <input type='hidden' name='signup_password' value='{$signup_password}'>
  <input type='hidden' name='signup_password2' value='{$signup_password2}'>
  <input type='hidden' name='signup_username' value='{$signup_username}'>
  <input type='hidden' name='signup_lang' value='{$signup_lang}'>
  <input type='hidden' name='signup_invite' value='{$signup_invite}'>
  <input type='hidden' name='signup_secure' value='{$signup_secure}'>
  <input type='hidden' name='signup_agree' value='{$signup_agree}'>
  <input type='hidden' name='signup_cat' value='{$signup_cat}'>
  </form>










{* SHOW STEP ONE *}
{else}
  <img src='./images/icons/signup48.gif' border='0' class='icon_big' />
  <div class='page_header'>{lang_print id=679}</div>
  <div>{lang_print id=680}</div>
  <br />
  <br />

  {* SHOW ERROR MESSAGE *}
  {if $is_error != 0}
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td class='result'>
          <div class='error'>
            <img src='./images/error.gif' border='0' class='icon' />
            {lang_print id=$is_error}
          </div>
        </td>
      </tr>
    </table>
    <br />
  {/if}

  <form action='signupon13.php' method='POST'>
  <div class='signup_header'>{lang_print id=681}</div>
  <table cellpadding='0' cellspacing='0'>
    <tr>
      <td class='form1' width='100'>{lang_print id=37}</td>
      <td class='form2'>
        <input name='signup_email' type='text' class='text' maxlength='70' size='40' value='{$signup_email}' />
        <div class='form_desc'>{lang_print id=682}</div>
      </td>
    </tr>

  {if $setting.setting_signup_randpass == 0}
    <tr>
      <td class='form1'>{lang_print id=29}:</td>
      <td class='form2'>
        <input name='signup_password' type='password' class='text' maxlength='50' size='40' value='{$signup_password}'>
        <div class='form_desc'>{lang_print id=53}</div>
      </td>
    </tr>
    <tr>
      <td class='form1'>{lang_print id=266}:</td>
      <td class='form2'>
        <input name='signup_password2' type='password' class='text' maxlength='50' size='40' value='{$signup_password2}'>
        <div class='form_desc'>{lang_print id=683}</div>
      </td>
    </tr>
  {else}
    <input type='hidden' name='signup_password' value=''>
    <input type='hidden' name='signup_password2' value=''>
  {/if}
  </table>
  <br />
  
  
  <div class='signup_header'>{lang_print id=684}</div>
  <table cellpadding='0' cellspacing='0'>
  {if $setting.setting_username}
    <tr>
      <td class='form1'>{lang_print id=28}:</td>
      <td class='form2'>
        <input name='signup_username' type='text' class='text' maxlength='50' size='40' value='{$signup_username}'>
        {capture assign=tip}{lang_print id=685}{/capture}
        <img src='./images/icons/tip.gif' border='0' class='Tips1' title="{$tip|escape:quotes}">
        <div class='form_desc'>{lang_print id=686}</div>
      </td>
    </tr>
  {/if}
  {if $cats|@count > 1}
    <tr>
      <td class='form1'>{lang_print id=709}:</td>
      <td class='form2'>
        <select name='signup_cat'>
        {section name=cat_loop loop=$cats}
          <option value='{$cats[cat_loop].cat_id}'{if $signup_cat == $cats[cat_loop].cat_id} selected='selected'{/if}>{lang_print id=$cats[cat_loop].cat_title}</option>
        {/section}
        </select>
      </td>
    </tr>
  {/if}
  
  {if $setting.setting_lang_allow == 1}
    <tr>
      <td class='form1'>{lang_print id=687}:</td>
      <td class='form2'>
        <select name='signup_lang'>
          {section name=lang_loop loop=$lang_packlist}
            <option value='{$lang_packlist[lang_loop].language_id}'{if $lang_packlist[lang_loop].language_default == 1} selected='selected'{/if}>{$lang_packlist[lang_loop].language_name}</option>
          {/section}
        </select>
      </td>
    </tr>
  {/if}
  
  
  {if $setting.setting_signup_code || $setting.setting_signup_tos || $setting.setting_signup_invite}
  </table>
  <br />
  
  <div class='signup_header'>{lang_print id=688}</div>
  <table cellpadding='0' cellspacing='0'>
  {/if}
  
    
    {if $setting.setting_signup_invite}
    <tr>
      <td class='form1' width='100'>{lang_print id=689}</td>
      <td class='form2'><input type='text' name='signup_invite' value='{$signup_invite}' class='text' size='10' maxlength='10'></td>
    </tr>
    {/if}
    
    {if $setting.setting_signup_code}
    <tr>
      <td class='form1' width='100'>{lang_print id=690}</td>
      <td class='form2'>
        <table cellpadding='0' cellspacing='0'>
          <tr>
            <td><input type='text' name='signup_secure' class='text' size='6' maxlength='10'>&nbsp;</td>
            <td>
              <table cellpadding='0' cellspacing='0'>
                <tr>
                  <td align='center'>
                    <img src='./images/secure.php' id='secure_image' border='0' height='20' width='67' class='signup_code'><br />
                    <a href="javascript:void(0);" onClick="$('secure_image').src = './images/secure.php?' + (new Date()).getTime();">{lang_print id=975}</a>
                  </td>
                  <td>{capture assign=tip}{lang_print id=691}{/capture}<img src='./images/icons/tip.gif' border='0' class='Tips1' title='{$tip|escape:quotes}'></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    {/if}
    
    {if $setting.setting_signup_tos}
    <tr>
      <td class='form1' width='100'>&nbsp;</td>
      <td class='form2'>
        <input type='checkbox' name='signup_agree' id='tos' value='1'{if $signup_agree == 1} CHECKED{/if}>
        <label for='tos'> {lang_print id=692}</label>
      </td>
    </tr>
    {/if}
    
    <tr>
      <td colspan='2'>&nbsp;</td>
    </tr>
    <tr>
      <td class='form1'>&nbsp;</td>
      <td class='form2'><input type='submit' class='button' value='{lang_print id=693}'></td>
    </tr>
  </table>
  
  <input type='hidden' name='task' value='{$next_task}'>
  <input type='hidden' name='step' value='{$step}'>
  </form>

{/if}


{include file='footer.tpl'}