{include file='admin_header.tpl'}

{* $Id: admin_viewpolls.tpl 53 2009-02-06 04:55:08Z john $ *}

<h2>{lang_print id=2500002}</h2>
{lang_print id=2500099}
<br />
<br />

<form action='admin_viewpolls.php' method='post'>
<table cellpadding='0' cellspacing='0' width='400' align='center'>
  <tr>
    <td align='center'>
      <div class='box'>
        <table cellpadding='0' cellspacing='0' align='center'>
          <tr>
            <td>
              {lang_print id=2500082}
              <br />
              <input type='text' class='text' name='f_title' value='{$f_title}' size='15' maxlength='100' />
              &nbsp;
            </td>
            <td>
              {lang_print id=2500083}
              <br />
              <input type='text' class='text' name='f_owner' value='{$f_owner}' size='15' maxlength='50' />
              &nbsp;
            </td>
            <td>{lang_block id=1002 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}' /></td>{/lang_block}
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>
<input type='hidden' name='s' value='{$s}' />
</form>
<br />


{if $total_polls == 0}

  <table cellpadding='0' cellspacing='0' width='400' align='center'>
    <tr>
      <td align='center'><div class='box' style='width: 300px;'><b>{lang_print id=2500043}</b></div></td>
    </tr>
  </table>
  <br />

{else}

  {* JAVASCRIPT FOR CHECK ALL *}
  {literal}
  <script language='JavaScript'> 
  <!---
  var checkboxcount = 1;
  function doCheckAll() {
    if(checkboxcount == 0) {
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = false;
      }}
      checkboxcount = checkboxcount + 1;
      }
    } else
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = true;
      }}
      checkboxcount = checkboxcount - 1;
      }
  }
  // -->
  </script>
  {/literal}

  <div class='pages'>
    {lang_sprintf id=2500084 1=$total_polls}
    &nbsp;|&nbsp;
    {lang_print id=1005}
    {section name=page_loop loop=$pages}
      {if $pages[page_loop].link == '1'}
        {$pages[page_loop].page}
      {else}
        <a href='admin_viewpolls.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>
      {/if}
    {/section}
  </div>
  
  
  <form action='admin_viewpolls.php' method='post' name='items'>
  <table cellpadding='0' cellspacing='0' class='list'>
    <tr>
      <td class='header' width='10'><input type='checkbox' name='select_all' onClick='javascript:doCheckAll()'></td>
      <td class='header' width='10' style='padding-left: 0px;'><a class='header' href='admin_viewpolls.php?s={$i}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=87}</a></td>
      <td class='header' width='100%'><a class='header' href='admin_viewpolls.php?s={$t}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=2500082}</a></td>
      <td class='header'><a class='header' href='admin_viewpolls.php?s={$v}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=2500085}</a></td>
      <td class='header'><a class='header' href='admin_viewpolls.php?s={$o}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=2500083}</a></td>
      <td class='header' width='150'><a class='header' href='admin_viewpolls.php?s={$d}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=2500086}</a></td>
      <td class='header' width='100'>{lang_print id=2500087}</td>
    </tr>
    
    {section name=poll_loop loop=$polls}
    <tr class='{cycle values="background1,background2"}'>
      <td class='item' style='padding-right: 0px;'><input type='checkbox' name='delete_polls[]' value='{$polls[poll_loop]->poll_info.poll_id}'></td>
      <td class='item' style='padding-left: 0px;'>{$polls[poll_loop]->poll_info.poll_id}</td>
      <td class='item'>{$polls[poll_loop]->poll_info.poll_title|truncate:70:"...":true}</td>
      <td class='item' align='center'>{$polls[poll_loop]->poll_info.poll_totalvotes}</td>
      <td class='item'><a href='{$url->url_create("profile", $polls[poll_loop]->poll_owner->user_info.user_username)}' target='_blank'>{$polls[poll_loop]->poll_owner->user_displayname}</a></td>
      {assign var=poll_date_start value=$datetime->timezone($polls[poll_loop]->poll_info.poll_datecreated, $global_timezone)}
      <td class='item' nowrap='nowrap'>{$datetime->cdate("`$setting.setting_dateformat` `$poll51` `$setting.setting_timeformat`", $poll_date_start)}</td>
      <td class='item' nowrap='nowrap'>[ <a href='admin_loginasuser.php?user_id={$polls[poll_loop]->poll_info.poll_user_id}&return_url={$url->url_encode("`$url->url_base`poll.php?user=`$polls[poll_loop]->poll_owner->user_info.user_username`&poll_id=`$polls[poll_loop]->poll_info.poll_id`")}' target='_blank'>{lang_print id=2500088}</a> ] [ <a href="javascript:if(confirm('{lang_print id=2500056}')) {literal}{{/literal} location.href = 'admin_viewpolls.php?task=deleteentry&poll_id={$polls[poll_loop]->poll_info.poll_id}&s={$s}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'; {literal}}{/literal}">{lang_print id=155}</a> ]</td>
    </tr>
    {/section}
    
  </table>
  <br />

  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td>
        {lang_block id=788 var=langBlockTemp}<input type='submit' class='button' value='{$langBlockTemp}'>{/lang_block}
      </td>
      <td align='right' valign='top'>
        <div class='pages2'>
          {lang_sprintf id=2500084 1=$total_polls}
          &nbsp;|&nbsp;
          {lang_print id=1005}
          {section name=page_loop loop=$pages}
            {if $pages[page_loop].link == '1'}
              {$pages[page_loop].page}
            {else}
              <a href='admin_viewpolls.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>
            {/if}
          {/section}
        </div>
      </td>
    </tr>
  </table>

  <input type='hidden' name='task' value='delete'>
  <input type='hidden' name='s' value='{$s}'>
  <input type='hidden' name='p' value='{$p}'>
  <input type='hidden' name='f_title' value='{$f_title}'>
  <input type='hidden' name='f_owner' value='{$f_owner}'>
  </form>
{/if}

{include file='admin_footer.tpl'}