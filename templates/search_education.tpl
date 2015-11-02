{include file='header.tpl'}


<img src='./images/icons/education48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=11040601}</div>
<div>{lang_print id=11040602}</div>

<br><br>

{* SHOW RESULT MESSAGE *}
{if $result != ""}
<table cellpadding='0' cellspacing='0'><tr><td class='result'>
<div class='success'><img src='./images/success.gif' border='0' class='icon'> {$result}</div>
</td></tr></table>
<br>
{/if}

{* SHOW ERROR MESSAGE *}
{if $is_error != 0}
<table cellpadding='0' cellspacing='0'><tr><td class='result'>
<div class='error'><img src='./images/error.gif' border='0' class='icon'> {$error_message}</div>
</td></tr></table>
<br>
{/if}


<form action='search_education.php' method='POST' name='profile'>
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td class="browse_header">{lang_print id=11040603}</td>
  </tr>
  <tr>
    <td class="browse_fields">
      {* START SEARCH FIELDS *}
      <table cellpadding='0' cellspacing='0' class='form'>
        <tr>
          <td class='form1'>{lang_print id=11040606}</td>
          <td class='form2'><input type="text" class="text" id="edu{$eid}" name="search[education_name]" value="{$search.education_name}" size="30" /></td>
        </tr> 
        <tr>
          <td class='form1'>{lang_print id=11040607}</td>
          <td class='form2'>
            <select name="search[education_for]" size="1">
              <option value=""></option>
              {foreach from=$foroptions key=foroptionkey item=foroptionval}
              <option value="{$foroptionkey}" {if $foroptionkey == $search.education_for}selected="selected"{/if}>{$foroptionval}</option>
              {/foreach}
            </select>
            <select name="search[education_year]" size="1">
              <option value="">{lang_print id=11040608}</option>
              {foreach from=$yearoptions key=yearoptionkey item=yearoptionval}
              <option value="{$yearoptionkey}" {if $yearoptionkey == $search.education_year}selected="selected"{/if}>{$yearoptionval}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class='form1'>{lang_print id=11040609}</td>
          <td class='form2'><input type="text" class="text" name="search[education_degree]" value="{$search.education_degree}" size="30" /></td>
        </tr> 
        <tr>
          <td class='form1'>{lang_print id=11040610}</td>
          <td class='form2'><input type="text" class="text" name="search[education_concentration1]" value="{$search.education_concentration1}" size="30" /></td>
        </tr> 
        <tr>
          <td class='form1'>{lang_print id=11040611}</td>
          <td class='form2'><input type="text" class="text" name="search[education_concentration2]" value="{$search.education_concentration2}" size="30" /></td>
        </tr> 
        <tr>
          <td class='form1'>{lang_print id=11040612}</td>
          <td class='form2'><input type="text" class="text" name="search[education_concentration3]" value="{$search.education_concentration3}" size="30" /></td>
        </tr> 
        <tr><td colspan="2"><hr noshade size="0" /></td></tr> 
      </table>   
      {* END SEARCH FIELDS *}
      <table cellpadding='0' cellspacing='0' class='form'>
        <tr>
        <td class='form1'>&nbsp;</td>
        <td class='form2'><input type='submit' class='button' value='{lang_print id=11040613}'></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<input type='hidden' name='task' value='search'>
</form>

{if $total_educations == 0}
  <br>
  <table cellpadding='0' cellspacing='0' align='center'>
  <tr><td class='result'><img src='./images/icons/bulb22.gif' border='0' class='icon'> {lang_print id=11040604}</td></tr>
  </table>
{else}

      {* DISPLAY PAGINATION MENU IF APPLICABLE *}
      {if $maxpage > 1}
        <br><br>
        <div class='center'>
        <b>
        {if $p != 1}<a href='search_education.php?{$search_query}task={$task}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=11040614}</a>{else}<font class='disabled'>&#171; {lang_print id=11040614}</font>{/if}
        {if $p_start == $p_end}
          &nbsp;|&nbsp; {lang_print id=11040615} {$p_start} {lang_print id=11040617} {$total_educations} &nbsp;|&nbsp; 
        {else}
          &nbsp;|&nbsp; {lang_print id=11040616} {$p_start}-{$p_end} {lang_print id=11040617} {$total_educations} &nbsp;|&nbsp; 
        {/if}
        {if $p != $maxpage}<a href='search_education.php?{$search_query}task={$task}&p={math equation='p+1' p=$p}'>{lang_print id=11040618} &#187;</a>{else}<font class='disabled'>{lang_print id=11040618} &#187;</font>{/if}
        </b>
        </div>
        <br>
      {/if}  

  <table cellpadding='0' cellspacing='0' class='education_search_results'>
  {foreach from=$educations item=education key=eid}
    <tr>
      <td class='user_thumb'>
        <a href='{$url->url_create('profile',$education.user->user_info.user_username)}'>{$education.user->user_info.user_username|truncate:20:"...":true}<br>
        <img src='{$education.user->user_photo('./images/nophoto.gif')}' class='photo' width='{$misc->photo_size($education.user->user_photo('./images/nophoto.gif'),'90','90','w')}' border='0' alt="{$education.user->user_info.user_username}"></a>
      </td>
      <td class='user_education'>
        <h3 class="education_header">{$education.search_education_name} {$education.search_education_year}</h3>
        <table cellpadding='0' cellspacing='0' class="education">
          <tr><td width="130">{lang_print id=11040607}</td><td>{$education.search_education_for}</td></tr>
          <tr><td width="130">{lang_print id=11040609}</td><td>{$education.search_education_degree}</td></tr>
          <tr><td width="130">{lang_print id=11040610}</td><td>{$education.search_education_concentration1}</td></tr>
          <tr><td width="130">{lang_print id=11040611}</td><td>{$education.search_education_concentration2}</td></tr>
          <tr><td width="130">{lang_print id=11040612}</td><td>{$education.search_education_concentration3}</td></tr>
        </table>
      </td>
    </tr>
  {/foreach}  
  </table>
  
      {* DISPLAY PAGINATION MENU IF APPLICABLE *}
      {if $maxpage > 1}
        <br><br>
        <div class='center'>
        <b>
        {if $p != 1}<a href='search_education.php?{$search_query}task={$task}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=11040614}</a>{else}<font class='disabled'>&#171; {lang_print id=11040614}</font>{/if}
        {if $p_start == $p_end}
          &nbsp;|&nbsp; {lang_print id=11040615} {$p_start} {lang_print id=11040617} {$total_educations} &nbsp;|&nbsp; 
        {else}
          &nbsp;|&nbsp; {lang_print id=11040616} {$p_start}-{$p_end} {lang_print id=11040617} {$total_educations} &nbsp;|&nbsp; 
        {/if}
        {if $p != $maxpage}<a href='search_education.php?{$search_query}task={$task}&p={math equation='p+1' p=$p}'>{lang_print id=11040618} &#187;</a>{else}<font class='disabled'>{lang_print id=11040618} &#187;</font>{/if}
        </b>
        </div>
        <br>
      {/if}  
  
{/if}


{include file='footer.tpl'}
