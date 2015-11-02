{* $Id: admin_viewdocuments.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{include file='admin_header.tpl'}


<h2>{lang_print id=650003101}</h2>
{lang_print id=650003102}
<br />
<br />

{if $excep_error == 1}
<table cellpadding='0' cellspacing='0'>
  <tr>
    <td class='error' style="text-align:left;">
    <div>
    <img src='./images/error.gif' border='0' class='icon'>
    {$excep_message}
    </div>
    </td>
  </tr>
</table>
{/if}

<form action='admin_viewdocuments.php' method='post'>
<table cellpadding='0' cellspacing='0' width='400' align='center'>
  <tr>
    <td align='center'>
      <div class='box'>
        <table cellpadding='0' cellspacing='0' align='center'>
          <tr>
            <td>
              {lang_print id=650003103}
              <br />
              <input type='text' class='text' name='f_title' value='{$f_title}' size='15' maxlength='100' />
              &nbsp;
            </td>
            <td>
              {lang_print id=650003104}
              <br />
              <input type='text' class='text' name='f_owner' value='{$f_owner}' size='15' maxlength='50' />
              &nbsp;
            </td>
            <td><input type='submit' class='button' value='{lang_print id=650003230}' /></td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>
<input type='hidden' name='s' value='{$s}' />
</form>
<br />


{if $total_documents == 0}

  <table cellpadding='0' cellspacing='0' width='400' align='center'>
    <tr>
      <td align='center'><div class='box'><b>{lang_print id=650003105}</b></div></td>
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
    {lang_sprintf id=650003106 1=$total_documents}
    &nbsp;|&nbsp;
    {lang_print id=1005}
    {section name=page_loop loop=$pages}
      {if $pages[page_loop].link == '1'}
        {$pages[page_loop].page}
      {else}
        <a href='admin_viewdocuments.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>
      {/if}
    {/section}
  </div>
  
  
  <form action='admin_viewdocuments.php' method='post' name='items'>
  <table cellpadding='0' cellspacing='0' class='list'>
    <tr>
      <td class='header' width='10'>
      	<input type='checkbox' name='select_all' onClick='javascript:doCheckAll()'>
      </td>
      <td class='header' width='10' style='padding-left: 0px;'>
      	<a class='header' href='admin_viewdocuments.php?s={$i}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=87}</a>
      </td>
      <td class='header'><a class='header' href='admin_viewdocuments.php?s={$t}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=650003103}</a></td>
      <td class='header'>{lang_print id=650003107}</td>
      <td class='header'><a class='header' href='admin_viewdocuments.php?s={$f}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>Featured</a></td>
      <td class='header'><a class='header' href='admin_viewdocuments.php?s={$a}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=650003108}</a></td>
      <td class='header'><a class='header' href='admin_viewdocuments.php?s={$o}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=650003104}</a></td>
      <td class='header' width='150'><a class='header' href='admin_viewdocuments.php?s={$d}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>{lang_print id=650003109}</a></td>
      <td class='header' width='100'>{lang_print id=650003110}</td>
    </tr>
    
    {section name=document_loop loop=$documents}
    <tr class='{cycle values="background1,background2"}'>
      <td class='item' style='padding-right: 0px;'><input type='checkbox' name='delete_docs[]' value='{$documents[document_loop]->document_info.document_id}'></td>
      <td class='item' style='padding-left: 0px;'>{$documents[document_loop]->document_info.document_id}</td>
      <td class='item'>{$documents[document_loop]->document_info.document_title|truncate:70:"...":true}</td>
      <td class='item'>{$documents[document_loop]->document_info.document_description|truncate:70:"...":true}</td>
      
      <td class='item' align='center'><a href='admin_viewdocuments.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}&task=featured&document_id={$documents[document_loop]->document_info.document_id}&value={if $documents[document_loop]->document_info.document_featured == 1}0{else}1{/if}'><img src='../images/icons/document_goldmedal{$documents[document_loop]->document_info.document_featured}.gif' border='0' class='icon'></a></td>
      
      <td class='item' align='center'><a href='admin_viewdocuments.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}&task=approve&document_id={$documents[document_loop]->document_info.document_id}&value={if $documents[document_loop]->document_info.document_approved == 1}0{else}1{/if}'><img src='../images/icons/document_approved{$documents[document_loop]->document_info.document_approved}.gif' border='0' class='icon'></a></td>
      <td class='item'><a href='{$url->url_create("profile", $documents[document_loop]->document_owner->user_info.user_username)}' target='_blank'>{$documents[document_loop]->document_owner->user_displayname}</a></td>
      {assign var=document_date_start value=$datetime->timezone($documents[document_loop]->document_info.document_datecreated, $global_timezone)}
       <td class='item' nowrap='nowrap'>{$datetime->cdate("`$setting.setting_dateformat` `$setting.setting_timeformat`", $document_date_start)}</td>
      <td class='item' nowrap='nowrap'>[ <a href='admin_loginasuser.php?user_id={$documents[document_loop]->document_info.document_user_id}&return_url={$url->url_encode("`$url->url_base`document.php?user=`$documents[document_loop]->document_owner->user_info.user_username`&document_id=`$documents[document_loop]->document_info.document_id`")}' target='_blank'>{lang_print id=650003111}</a> ] [ <a href="javascript:if(confirm('{lang_print id=2500056}')) {literal}{{/literal} location.href = 'admin_viewdocuments.php?task=deleteentry&document_id={$documents[document_loop]->document_info.document_id}&s={$s}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'; {literal}}{/literal}">{lang_print id=155}</a> ]</td>
    </tr>
    {/section}
    
  </table>
  <br />

  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td>
        <input type='submit' class='button' value='{lang_print id=788}'>{/lang_block}
      </td>
      <td align='right' valign='top'>
        <div class='pages2'>
          {lang_sprintf id=650003106 1=$total_documents}
          &nbsp;|&nbsp;
          {lang_print id=1005}
          {section name=page_loop loop=$pages}
            {if $pages[page_loop].link == '1'}
              {$pages[page_loop].page}
            {else}
              <a href='admin_viewdocuments.php?s={$s}&p={$pages[page_loop].page}&f_title={$f_title}&f_owner={$f_owner}'>{$pages[page_loop].page}</a>
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