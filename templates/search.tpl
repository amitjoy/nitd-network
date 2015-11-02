{include file='header.tpl'}

{* $Id: search.tpl 8 2009-01-11 06:02:53Z john $ *}

<img src='./images/icons/search48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=646}</div>
<div>{lang_print id=924}</div>
<br />
<br />

<form action='search.php' name='search_form' method='post'>
<table cellpadding='0' cellspacing='0' align='center'>
<tr>
<td class='search'>
  <table cellpadding='0' cellspacing='0' align='center'>
  <tr>
  <td>{lang_print id=925}</td>
  <td>&nbsp;<input type='text' size='30' class='text' name='search_text' id='search_text' value='{$search_text}' maxlength='100'></td>
  <td>
    &nbsp;<input type='submit' class='button' value='{lang_print id=646}'>
    <input type='hidden' name='task' value='dosearch'>
    <input type='hidden' name='t' value='0'>
  </td>
  </tr>
  <tr>
  <td>&nbsp;</td>
  <td colspan='2'>&nbsp;<a href='search_advanced.php'>{lang_print id=926}</a></td>
  </tr>
  </table>
</div>
</form>
</td>
</tr>
</table>

<br>

{if $search_text != ""}

  {if $is_results == 0}

    <table cellpadding='0' cellspacing='0' align='center'>
    <tr>
    <td class='result'>
      <img src='./images/icons/bulb16.gif' class='icon'>
      {lang_sprintf id=927 1=$search_text}
    </td>
    </tr>
    </table>

  {else}


    {* SHOW DIFFERENT RESULT TOTALS *}
    <table class='tabs' cellpadding='0' cellspacing='0'>
    <tr>
    <td class='tab0'>&nbsp;</td>
      {section name=search_loop loop=$search_objects}
        <td class='tab{if $t == $search_objects[search_loop].search_type}1{else}2{/if}' NOWRAP>{if $search_objects[search_loop].search_total == 0}{lang_sprintf id=$search_objects[search_loop].search_lang 1=$search_objects[search_loop].search_total}{else}<a href='search.php?task=dosearch&search_text={$url_search}&t={$search_objects[search_loop].search_type}'>{lang_sprintf id=$search_objects[search_loop].search_lang 1=$search_objects[search_loop].search_total}</a>{/if}</td>
        <td class='tab'>&nbsp;</td>
      {/section}
      <td class='tab3'>&nbsp;</td>
    </tr>
    </table>

    <div class='search_results'>

      {* SHOW PAGES *}
      {if $p != 1}<a href='search.php?task=dosearch&search_text={$url_search}&t={$t}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a> &nbsp;|&nbsp;&nbsp;{/if}
      {if $p_start == $p_end}
        <b>{lang_sprintf id=184 1=$p_start 2=$total_results}</b> ({lang_sprintf id=928 1=$search_time}) 
      {else}
        <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_results}</b> ({lang_sprintf id=928 1=$search_time}) 
      {/if}
      {if $p != $maxpage}&nbsp;&nbsp;|&nbsp; <a href='search.php?task=dosearch&search_text={$url_search}&t={$t}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{/if}

      <br><br>

      {* SHOW RESULTS *}
      {section name=result_loop loop=$results}
	
	<div class='search_result{cycle name="class_name" values="1,2,2,1"}' style='width: 400px; float: left; border: 1px solid #CCCCCC; margin: 5px;'>
	<table cellpadding='0' cellspacing='0'>
        <tr>
        <td valign='top' style='padding-right: 4px;'>
	  <a href="{$results[result_loop].result_url}" class="title"><img src='{$results[result_loop].result_icon}' class='photo' width='60' height='60' border='0'></a>
	</td>
	<td valign='top'>
          <div class='search_result_text'>
	    {capture assign='result_title'}{lang_sprintf id=$results[result_loop].result_name 1=$results[result_loop].result_name_1}{/capture}
            <a href="{$results[result_loop].result_url}" class="title">{$result_title|truncate:40:"...":true}</a>
            <div class='search_result_text2'>{lang_sprintf id=$results[result_loop].result_desc 1=$results[result_loop].result_desc_1 2=$results[result_loop].result_desc_2 3=$results[result_loop].result_desc_3}</div>
	    {if $results[result_loop].result_online == 1}<div style='margin-top: 5px;'><img src='./images/icons/online16.gif' border='0' class='icon'>{lang_print id=929}</div>{/if}
          </div>
	</td>
	</tr>
	</table>
	</div>
        {cycle name="clear_cycle" values=",<div style='clear: both; height: 0px;'></div>"}
      {/section}

      <div style='clear:both;'></div><br />

      {* SHOW PAGES *}
      {if $p != 1}<a href='search.php?task=dosearch&search_text={$url_search}&t={$t}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a> &nbsp;|&nbsp;&nbsp;{/if}
      {if $p_start == $p_end}
        <b>{lang_sprintf id=184 1=$p_start 2=$total_results}</b> ({lang_sprintf id=928 1=$search_time}) 
      {else}
        <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_results}</b> ({lang_sprintf id=928 1=$search_time}) 
      {/if}
      {if $p != $maxpage}&nbsp;&nbsp;|&nbsp; <a href='search.php?task=dosearch&search_text={$url_search}&t={$t}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{/if}


    </div>
  {/if}
{/if}


{* JAVASCRIPT TO AUTOFOCUS ON SEARCH FIELD *}
{literal}
<script type="text/javascript">
<!-- 
  window.addEvent('load', function(){ $('search_text').focus(); });
//-->
</script>
{/literal}


{include file='footer.tpl'}