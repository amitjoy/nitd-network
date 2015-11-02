{include file='header.tpl'}

{* $Id: search_advanced.tpl 217 2009-08-11 23:20:02Z phil $ *}

{* SHOW PAGE TITLE *}
{if $showfields == 1}
  <img src='./images/icons/search48.gif' border='0' class='icon_big'>
  <div class='page_header'>{lang_print id=1087}</div>
  <div>{lang_print id=1088}</div>
{elseif $showfields == 0}
  <img src='./images/icons/search48.gif' border='0' class='icon_big'>
  <div class='page_header'>{lang_sprintf id=1083 1="`$linked_field_title`: `$linked_field_value`"}</div>
  <div>{lang_sprintf id=1084 1=$total_users 2="`$linked_field_title`: `$linked_field_value`"}</div>
{/if}

<br><br>

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td style='width: 200px; vertical-align: top;'>

{* SHOW FIELDS IF USER IS DOING A MANUAL SEARCH *}
{if $showfields == 1}

  {* SHOW ERROR IF NO FIELDS *}
  {if $cats_menu == NULL}
    <br>
    <table cellpadding='0' cellspacing='0' align='center'>
    <tr><td class='result'><img src='./images/icons/bulb22.gif' border='0' class='icon'> {lang_print id=1114}</td></tr>
    </table>

  {else}

    <form action='search_advanced.php' method='post'>
    <div class='header'>{lang_print id=1089}</div>
    <div class='browse_fields'>

      {* START BY SHOWING PROFILE CATEGORIES *}
      {if $cats_menu|@count > 0}
	<div style='padding-top: 5px;'>
          <select name='categories' class='text' onChange="location.href='search_advanced.php?cat_selected='+this.options[this.selectedIndex].value;">
          {section name=cat_menu_loop loop=$cats_menu}
            <option value='{$cats_menu[cat_menu_loop].cat_id}'{if $cats_menu[cat_menu_loop].cat_id == $cat_selected} selected='selected'{/if}>{lang_print id=$cats_menu[cat_menu_loop].cat_title}</option>
          {/section}
          </select>
	</div>
      {/if}

      {* LOOP THROUGH FIELDS *}
      {section name=cat_loop loop=$cats}
      {section name=subcat_loop loop=$cats[cat_loop].subcats}
      {section name=field_loop loop=$cats[cat_loop].subcats[subcat_loop].fields}

        <div>

          <div style='font-weight: bold; margin-top: 5px;'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_title}{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_special == 1} {lang_print id=736}{/if}</div>

          {* TEXT FIELD/TEXTAREA *}
          {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 1 || $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 2}

	    {* RANGED SEARCH *}
	    {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_search == 2}
	      <input type='text' class='text' size='5' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_min' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value_min}' maxlength='100'>
	      - 
	      <input type='text' class='text' size='5' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_max' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value_max}' maxlength='100'>	  

	    {* EXACT VALUE SEARCH *}
	    {else}
              <input type='text' class='text' size='15' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value}' maxlength='100'>
	    {/if}

		  {* RADIO BUTTONS *}
		  {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 4}
              {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
			  <input type="radio" name="field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}"
			    id="radio_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}"
				value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'
				{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value}
					checked="true"{/if}
				>
			  <label for="radio_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}">
               {lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}
			  </label><br>
              {/section}


          {* SELECT BOX/RADIO BUTTONS *}
          {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 3}

	    {* RANGED SEARCH *}
	    {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_search == 2}
              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_min'>
              <option value='-1'></option>
              {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
                <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value_min} SELECTED{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}</option>
              {/section}
              </select>
	      - 
              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_max'>
              <option value='-1'></option>
              {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
                <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value_max} SELECTED{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}</option>
              {/section}
              </select>

	    {* EXACT VALUE SEARCH *}
	    {else}
              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}'>
              <option value='-1'></option>
              {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
                <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} SELECTED{/if}>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}</option>
              {/section}
              </select>
	    {/if}


          {* DATE FIELD *}
          {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 5}


	    {* BIRTHDAYS *}
	    {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_special == 1}
              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_3_min'>
              {section name=date3_min loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3}
                <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3_min].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value_min == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3_min].value} SELECTED{/if}>{if $smarty.section.date3_min.first}[ {lang_print id=1116} ]{else}{math equation='x-y' x=$smarty.now|date_format:"%Y" y=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3_min].name}{/if}</option>
              {/section}
              </select>
	      -
              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_3_max'>
              {section name=date3_max loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3}
                <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3_max].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value_max == $cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3_max].value} SELECTED{/if}>{if $smarty.section.date3_max.first}[ {lang_print id=1117} ]{else}{math equation='x-y' x=$smarty.now|date_format:"%Y" y=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3_max].name}{/if}</option>
              {/section}
              </select>


	    {* NORMAL DATES *}
	    {else}
              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_1'>
              {section name=date1 loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1}
                <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1[date1].value}'{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1[date1].selected}>{if $smarty.section.date1.first}{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1[date1].name}{else}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array1[date1].name}{/if}</option>
              {/section}
              </select>

              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_2'>
              {section name=date2 loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2}
                <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2[date2].value}'{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2[date2].selected}>{if $smarty.section.date2.first}{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2[date2].name}{else}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array2[date2].name}{/if}</option>
              {/section}
              </select>

              <select name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_3'>
              {section name=date3 loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3}
                <option value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3].value}'{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3].selected}>{if $smarty.section.date3.first}{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3].name}{else}{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].date_array3[date3].name}{/if}</option>
              {/section}
              </select>
	    {/if}


          {* CHECKBOXES *}
          {elseif $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_type == 6}
    
            {* LOOP THROUGH FIELD OPTIONS *}
            {section name=option_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
              <table cellpadding='0' cellspacing='0'>
	      <tr>
	      <td><input type='checkbox' name='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}[]' id='label_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}' value='{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'{if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value|in_array:$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_value} checked='checked'{/if} style='vertical-align: middle;'></td>
	      <td><label for='label_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].value}'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[option_loop].label}</label></td>
	      </tr>
	      </table>
            {/section}

          {/if}

      </div>
      {/section}
      {/section}
      {/section}

      {* SHOW SUBMIT BUTTON *}
      <div>
	<div style='padding-top: 5px;'>
	  <b>{lang_print id=1091}</b><br>
          <select name='sort' class='small'>
          <option value='user_dateupdated DESC'{if $sort == "user_dateupdated DESC"} SELECTED{/if}>{lang_print id=1092} {lang_print id=1093}</option>
          <option value='user_dateupdated ASC'{if $sort == "user_dateupdated ASC"} SELECTED{/if}>{lang_print id=1092} {lang_print id=1094}</option>
          <option value='user_lastlogindate DESC'{if $sort == "user_lastlogindate DESC"} SELECTED{/if}>{lang_print id=1095} {lang_print id=1093}</option>
          <option value='user_lastlogindate ASC'{if $sort == "user_lastlogindate ASC"} SELECTED{/if}>{lang_print id=1095} {lang_print id=1094}</option>
          <option value='user_signupdate DESC'{if $sort == "user_signupdate DESC"} SELECTED{/if}>{lang_print id=1096} {lang_print id=1093}</option>
          <option value='user_signupdate ASC'{if $sort == "user_signupdate ASC"} SELECTED{/if}>{lang_print id=1096} {lang_print id=1094}</option>
          </select>
	  <table cellpadding='0' cellspacing='0' style='padding-top: 5px;'>
	  <tr><td><input type='checkbox' name='user_withphoto' id='user_withphoto' value='1'{if $user_withphoto == 1} checked='checked'{/if}></td><td><label for='user_withphoto'>{lang_print id=1122}</label></td></tr>
	  <tr><td><input type='checkbox' name='user_online' id='user_online' value='1'{if $user_online == 1} checked='checked'{/if}></td><td><label for='user_online'>{lang_print id=1121}</label></td></tr>
	  </table>
	</div>
        <div style='padding-top: 10px; padding-bottom: 5px;'>
          <input type='submit' class='button' value='{lang_print id=1090}'>&nbsp;&nbsp;
          <input type='hidden' name='task' value='dosearch'>
          <input type='hidden' name='cat_selected' value='{$cat_selected}'>
	</div>
      </div>
      </form>
  {/if}
{/if}



</td>
<td style='padding-left: 10px;' valign='top'>



{* SHOW MESSAGE IF NO RESULTS FOUND *}
{if $total_users == 0 && ($showfields == 0 || $cats_menu != NULL)}
  <br>
  <table cellpadding='0' cellspacing='0' align='center'>
  <tr><td class='result'><img src='./images/icons/bulb22.gif' border='0' class='icon'> {lang_print id=1085}</td></tr>
  </table>


{* SHOW RESULTS *}
{elseif $total_users != 0}

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='browse_pages'>
      {if $p != 1}<a href='search_advanced.php?{$url_string}cat_selected={$cat_selected}&task={$task}&sort={$sort}&user_online={$user_online}&user_withphoto={$user_withphoto}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_users} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_users} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}<a href='search_advanced.php?{$url_string}cat_selected={$cat_selected}&task={$task}&sort={$sort}&user_online={$user_online}&user_withphoto={$user_withphoto}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </div>
  {/if}

  {* DISPLAY BROWSE RESULTS IN THUMBNAIL FORM *}
  {section name=user_loop loop=$users}
    <div class='browse_result' style='float: left; padding: 5px; width: 100px; height: 100px; text-align: center;'>
      <a href='{$url->url_create('profile',$users[user_loop]->user_info.user_username)}'><img src='{$users[user_loop]->user_photo('./images/nophoto.gif', TRUE)}' class='photo' style='display: block; margin-left: auto; margin-right: auto;' width='60' height='60' border='0' alt="{lang_sprintf id=509 1=$users[user_loop]->user_displayname_short}">{$users[user_loop]->user_displayname|truncate:20:"...":true}</a>
      {if $users[user_loop]->is_online == 1}<div style='margin-top: 3px;'><img src='./images/icons/online16.gif' border='0' class='icon2'>{lang_print id=1086}</div>{/if}
    </div>
    {cycle name="newrow" values=",,,,,<div style='clear: both; margin-top: 10px;'>&nbsp;</div>"}
  {/section}
  <div style='clear: both;'></div>

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='browse_pages'>
      {if $p != 1}<a href='search_advanced.php?{$url_string}cat_selected={$cat_selected}&task={$task}&sort={$sort}&user_online={$user_online}&user_withphoto={$user_withphoto}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_users} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_users} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}<a href='search_advanced.php?{$url_string}cat_selected={$cat_selected}&task={$task}&sort={$sort}&user_online={$user_online}&user_withphoto={$user_withphoto}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </div>
  {/if}

{/if}

</td>
</tr>
</table>

{include file='footer.tpl'}
