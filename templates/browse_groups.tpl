{include file='header.tpl'}

{* $Id: browse_groups.tpl 247 2009-11-14 03:30:43Z phil $ *}

<div class='page_header'>
  {if $groupcat == ""}
    {lang_print id=2000126}
  {else}
    <a href='browse_groups.php'>{lang_print id=2000126}</a> >
    {if $groupsubcat == ""}
      {lang_print id=$groupcat.groupcat_title}
    {else}
      <a href='browse_groups.php?v={$v}&s={$s}&groupcat_id={$groupcat.groupcat_id}'>{lang_print id=$groupcat.groupcat_title}</a> >
      {lang_print id=$groupsubcat.groupcat_title}
    {/if}
  {/if}
</div>

<table cellpadding='0' cellspacing='0' width='100%' style='margin-top: 10px;'>
<tr>
<td style='width: 200px; vertical-align: top;'>

  <div style='padding: 10px; background: #F2F2F2; border: 1px solid #BBBBBB; font-weight: bold;'>

    <div style='text-align: center; line-height: 16px;'>
      {lang_print id=1000128}&nbsp;
      <select class='group_small' name='v' onchange="window.location.href='browse_groups.php?s={$s}&v='+this.options[this.selectedIndex].value;">
      <option value='0'{if $v == "0"} SELECTED{/if}>{lang_print id=2000127}</option>
      {if $user->user_exists}<option value='1'{if $v == "1"} SELECTED{/if}>{lang_print id=2000128}</option>{/if}
      </select>
    </div>

    <div style='text-align: center; line-height: 16px; margin-top: 5px;'>
      {lang_print id=1000131}&nbsp;
      <select class='group_small' name='s' onchange="window.location.href='browse_groups.php?v={$v}&s='+this.options[this.selectedIndex].value;">
      <option value='group_totalmembers DESC'{if $s == "group_totalmembers DESC"} SELECTED{/if}>{lang_print id=2000129}</option>
      <option value='group_datecreated DESC'{if $s == "group_datecreated DESC"} SELECTED{/if}>{lang_print id=2000130}</option>
      </select>
    </div>

  </div>

  {* CATEGORY JAVASCRIPT *}
  {literal}
  <script type="text/javascript">
  <!-- 

  // ADD ABILITY TO MINIMIZE/MAXIMIZE CATS
  var cat_minimized = new Hash.Cookie('cat_cookie', {duration: 3600});

  //-->
  </script>
  {/literal}


  <div style='margin-top: 10px; padding: 5px; background: #F2F2F2; border: 1px solid #BBBBBB; margin: 10px 0px 10px 0px; font-weight: bold;'>

    <div style='padding: 5px 8px 5px 8px; border: 1px solid #DDDDDD; background: #FFFFFF;'>
      <a href='browse_groups.php?s={$s}&v={$v}'>{lang_print id=2000131}</a>
    </div>
    {section name=cat_loop loop=$cats}

      {* CATEGORY JAVASCRIPT *}
      {literal}
      <script type="text/javascript">
      <!-- 
        window.addEvent('domready', function() { 
          if(cat_minimized.get({/literal}{$cats[cat_loop].cat_id}{literal}) == 1) {
	    $('subcats_{/literal}{$cats[cat_loop].cat_id}{literal}').style.display = '';
	    $('icon_{/literal}{$cats[cat_loop].cat_id}{literal}').src = './images/icons/minus16.gif';
	  }
	});
      //-->
      </script>
      {/literal}

      <div style='padding: 5px 8px 5px 8px; border: 1px solid #DDDDDD; border-top: none; background: #FFFFFF;'>
        <img id='icon_{$cats[cat_loop].cat_id}' src='./images/icons/{if $cats[cat_loop].subcats|@count > 0 && $cats[cat_loop].subcats != ""}plus16{else}minus16_disabled{/if}.gif' {if $cats[cat_loop].subcats|@count > 0 && $cats[cat_loop].subcats != ""}style='cursor: pointer;' onClick="if($('subcats_{$cats[cat_loop].cat_id}').style.display == 'none') {literal}{{/literal} $('subcats_{$cats[cat_loop].cat_id}').style.display = ''; this.src='./images/icons/minus16.gif'; cat_minimized.set({$cats[cat_loop].cat_id}, 1); {literal}} else {{/literal} $('subcats_{$cats[cat_loop].cat_id}').style.display = 'none'; this.src='./images/icons/plus16.gif'; cat_minimized.set({$cats[cat_loop].cat_id}, 0); {literal}}{/literal}"{/if} border='0' class='icon'><a href='browse_groups.php?s={$s}&v={$v}&groupcat_id={$cats[cat_loop].cat_id}'>{lang_print id=$cats[cat_loop].cat_title}</a>
        <div id='subcats_{$cats[cat_loop].cat_id}' style='display: none;'>
          {section name=subcat_loop loop=$cats[cat_loop].subcats}
            <div style='font-weight: normal;'><img src='./images/trans.gif' border='0' class='icon' style='width: 16px;'><a href='browse_groups.php?s={$s}&v={$v}&groupcat_id={$cats[cat_loop].subcats[subcat_loop].subcat_id}'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].subcat_title}</a></div>
          {/section}
        </div>
      </div>
    {/section}
  </div>

</td>
<td style='vertical-align: top; padding-left: 10px;'>

  {* NO GROUPS AT ALL *}
  {if $groups|@count == 0}
    <br>
    <table cellpadding='0' cellspacing='0' align='center'>
      <tr>
        <td class='result'>
          <img src='./images/icons/bulb16.gif' border='0' class='icon' />
          {lang_print id=2000132}
        </td>
      </tr>
    </table>
  {/if}

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='group_pages_top'>
    {if $p != 1}<a href='browse_groups.php?s={$s}&v={$v}&groupcat_id={$groupcat_id}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
    &nbsp;|&nbsp;&nbsp;
    {if $p_start == $p_end}
      <b>{lang_sprintf id=184 1=$p_start 2=$total_groups}</b>
    {else}
      <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_groups}</b>
    {/if}
    &nbsp;&nbsp;|&nbsp;
    {if $p != $maxpage}<a href='browse_groups.php?s={$s}&v={$v}&groupcat_id={$groupcat_id}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
    </div>
  {/if}

  {section name=group_loop loop=$groups}
  <div style='padding: 10px; border: 1px solid #CCCCCC; margin-bottom: 10px;'>
    <table cellpadding='0' cellspacing='0'>
      <tr>
        <td>
          <a href='{$url->url_create("group", $smarty.const.NULL, $groups[group_loop].group->group_info.group_id)}'>
            <img src='{$groups[group_loop].group->group_photo("./images/nophoto.gif", TRUE)}' border='0' width='60' height='60' />
          </a>
        </td>
        <td style='vertical-align: top; padding-left: 10px;'>
          <div style='font-weight: bold; font-size: 13px;'>
            <a href='{$url->url_create("group", $smarty.const.NULL, $groups[group_loop].group->group_info.group_id)}'>
              {$groups[group_loop].group->group_info.group_title}
            </a>
          </div>
          <div style='color: #777777; font-size: 9px; margin-bottom: 5px;'>
            {assign var='group_dateupdated' value=$datetime->time_since($groups[group_loop].group->group_info.group_dateupdated)}
            {capture assign="updated"}{lang_sprintf id=$group_dateupdated[0] 1=$group_dateupdated[1]}{/capture}
            {capture assign='group_leader'}<a href='{$url->url_create("profile", $groups[group_loop].group_leader->user_info.user_username)}'>{$groups[group_loop].group_leader->user_displayname}</a>{/capture}
            {lang_sprintf id=2000133 1=$groups[group_loop].group_members 2=$group_leader} - {lang_sprintf id=2000134 1=$updated}
          </div>
          <div>
            {$groups[group_loop].group->group_info.group_desc|strip_tags|truncate:300:"...":true}
          </div>
        </td>
      </tr>
    </table>
  </div>
  {/section}

  {* DISPLAY PAGINATION MENU IF APPLICABLE *}
  {if $maxpage > 1}
    <div class='group_pages_bottom'>
    {if $p != 1}<a href='browse_groups.php?s={$s}&v={$v}&groupcat_id={$groupcat_id}&p={math equation="p-1" p=$p}'>&#171; {lang_print id=182}</a>{else}&#171; {lang_print id=182}{/if}
    &nbsp;|&nbsp;&nbsp;
    {if $p_start == $p_end}
      <b>{lang_sprintf id=184 1=$p_start 2=$total_groups}</b>
    {else}
      <b>{lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_groups}</b>
    {/if}
    &nbsp;&nbsp;|&nbsp;
    {if $p != $maxpage}<a href='browse_groups.php?s={$s}&v={$v}&groupcat_id={$groupcat_id}&p={math equation="p+1" p=$p}'>{lang_print id=183} &#187;</a>{else}{lang_print id=183} &#187;{/if}
    </div>
  {/if}

</td>
</tr>
</table>







{include file='footer.tpl'}