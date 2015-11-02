{include file='admin_header.tpl'}

{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}

<script type="text/javascript" src="../include/js/feedback.js"></script>

<script type="text/javascript">
 var del_id=0;
</script>


<style type="text/css">
 a.current_feed_page {font-size: 14px; font-weight: bold;}
 td{vertical-align: top;}
 form{width: auto;}
 .item input{border: 0; background: #dddddd; font-size: 9px; width: 18px; height: 18px; padding: 0; margin-right: 1px;}
  .closed_feedback td { color:#CCCCCC;}
 .closed_feedback td a {color:#6699cc;}
</style>
{/literal}



{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
 <div style='margin-top: 10px;'>
  {lang_print id=17001021}
 </div>
 <br>
 <input type='button' class='button' value='{lang_print id=175}' id='deletebutton' onClick='$("deleteform_"+del_id).submit(); parent.TB_remove();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>


<h2>{lang_print id=17001001}</h2>
<div>{lang_print id=17001032}</div>
<br />

{if !empty($query_errors)}
Something wrong with queries:<br />
<div style="color: red;">
 {section name=query loop=$query_errors}
  {$query_errors[$smarty.section.query.index]}<br />
  {/section}
</div>
{/if}


<form enctype="multipart/formdata" method="POST">
 <div class="box">
  <table align="center" cellspacing="0" cellpadding="0">
   <tr>
    <td>
     {lang_print id=17001014}<br>


     <select name="feedback_type_filter" class="text">
      <option selected="" value="all">all</option>
      {section name=type loop=$feedbacks_types}
      {if $feedback_type_filter == $feedbacks_types[type].id}
      <option selected="" value={$feedbacks_types[type].id}>{$feedbacks_types[type].name}</option>
      {else}
      <option value={$feedbacks_types[type].id}>{$feedbacks_types[type].name}</option>
      {/if}
      {/section}
     </select>


    </td>
    <td>{lang_print id=17001019}<br />
     <select name="feedback_status_filter" class="text">
      {section name=sec start=0 loop=3}

      {if $feedback_status_filter == $smarty.section.sec.index}
      {if $smarty.section.sec.index == 0}
      <option selected="" value="all">all</option>
      {else}
      <option selected="" value="{$smarty.section.sec.index}">{if $smarty.section.sec.index == 1}{lang_print id=17001024}{else}{lang_print id=17001010}{/if}</option>
      {/if}

      {else}
      {if $smarty.section.sec.index == 0}
      <option value="all">all</option>
      {else}
      <option value="{$smarty.section.sec.index}">{if $smarty.section.sec.index == 1}{lang_print id=17001024}{else}{lang_print id=17001010}{/if}</option>
      {/if}
      {/if}
      {/section}

     </select>
    </td>
    <td>e-mail<br><input value="{$feedback_mail_filter}" type="text" maxlength="50" size="15" name="feedback_mail_filter" class="text">&nbsp;</td>
    <td style="padding-top: 9px;">&nbsp;<input type="submit" value="Filter" class="button"></td>


   </tr>
  </table>
 </div>
</form>



<table>
 <tr>
  <td style="text-transform: capitalize;">
   <strong>{$total_feeds}</strong> {lang_print id=17001016}s {lang_print id=17001025}&nbsp;&nbsp;|&nbsp;&nbsp;{lang_print id=1005}
  </td>
  {section name=link start=0 loop=$pages }
  <td>

   {if $smarty.section.link.index == $page_num}
   [<a class="current_feed_page" href="{$smarty.server.PHP_SELF}?pagenum={$smarty.section.link.index}{$filter_string}">{$smarty.section.link.index+1}</a>]
   {else}
   [<a href="{$smarty.server.PHP_SELF}?pagenum={$smarty.section.link.index}{$filter_string}">{$smarty.section.link.index+1}</a>]
   {/if}
  </td>
  {/section}

 </tr>
</table>
<br>
<table cellspacing="0" cellpadding="0" style="width: 100%;" class="list">
 <tr>
  <td class="header" style="text-transform: capitalize">
   {lang_print id=17001013}
  </td>
  <td class="header" style="text-transform: capitalize">
   {lang_print id=17001014}
  </td>
  <td class="header" style="text-transform: capitalize">
   {lang_print id=17001015}
  </td>
  <td class="header" style="text-transform: capitalize">
   {lang_print id=17001016}
  </td>

  <td class="header" style="text-transform: capitalize">Options</td>
 </tr>

 {section name=row loop=$feedback}
 <tr  {if $feedback[row].status == 2} class="closed_feedback" {/if}>

  <td class="item" nowrap>

   <i>{assign var='last_updated' value=$datetime->time_since($feedback[row].time)}{lang_sprintf id=$last_updated[0] 1=$last_updated[1]}</i>

  </td><td class="item" nowrap>

   {$feedback[row].typename}


  </td><td class="item" nowrap>
   {assign var=col value=$feedback[row].user_mail}
   <a target="_blank" href="../profile.php?user={$user_array[$col].user_username}">{$user_array[$col].user_displayname}</a><br />

   {assign var=col value=$feedback[row].user_mail}
   {$user_array[$col].user_email}

  </td>

  <td class="item" style="width: 100%">
   {$feedback[row].text}
  </td>


  <td class="item" style="padding: 0">


   <table>
    <tr>
     <td>
      <a href="javascript:void(0);" onclick="showMoreInfo('{$feedback[row].id}')" value="+" id="more_info_button_{$feedback[row].id}">{lang_print id=17001023}</a>
     </td>
     <td>
      |
     </td>
     <td>

      {if $feedback[row].status == 2}
      <form enctype="multipart/formdata" action="" method="POST" id="reopen_{$feedback[row].id}">
       <input type="hidden" value={$feedback[row].id} name="task_to_reopen">
       <input type="hidden" value={$page_num} name="pagenum">

       <input type="hidden" value="{$feedback_mail_filter}" name="feedback_mail_filter">
       <input type="hidden" value="{$feedback_type_filter}" name="feedback_type_filter">
       <input type="hidden" value="{$feedback_status_filter}" name="feedback_status_filter">

       <a href="javascript: void(0);" onclick="javascript: $('reopen_{$feedback[row].id}').submit();">{lang_print id=17001020}</a>
      </form>
      {else}
      <form enctype="multipart/formdata" action="" method="POST" id="close_{$feedback[row].id}">
       <input type="hidden" value={$feedback[row].id} name="task_to_close">
       <input type="hidden" value={$page_num} name="pagenum">

       <input type="hidden" value="{$feedback_mail_filter}" name="feedback_mail_filter">
       <input type="hidden" value="{$feedback_type_filter}" name="feedback_type_filter">
       <input type="hidden" value="{$feedback_status_filter}" name="feedback_status_filter">

       <a href="javascript: void(0);" onclick="javascript: $('close_{$feedback[row].id}').submit();">{lang_print id=17001008}</a>
      </form>
      {/if}

     </td>
     <td>
      |
     </td>
     <td>
      <form enctype="multipart/formdata" action="{$smarty.server.PHP_SELF}" method="POST" id="deleteform_{$feedback[row].id}">
       <input type="hidden" value={$feedback[row].id} name="task_to_delete">
       <input type="hidden" value={$page_num} name="pagenum">

       <input type="hidden" value="{$feedback_mail_filter}" name="feedback_mail_filter">
       <input type="hidden" value="{$feedback_type_filter}" name="feedback_type_filter">
       <input type="hidden" value="{$feedback_status_filter}" name="feedback_status_filter">

       <a href="javascript: void(0);" onclick="javascript: del_id={$feedback[row].id}; del_id=confirmDeleteFeedback('{$feedback[row].id}', '{lang_print id=17001009}');">{lang_print id=17001009}</a>
      </form>

     </td>
    </tr>
   </table>


  </td>



 </tr>

 <tr>
  <td style="width: 100%;background: #eeeeee;" colspan="5">

   <div style=" padding: 5px; font-weight: bold; display: none;" id="more_info_{$feedback[row].id}">
    <table style="width: 100%;">
     <tr>
      <td style="width: 100%;">
       <a href="{$feedback[row].link}" target="_blank">{$feedback[row].link}</a>
      </td>
     </tr>
     <tr>

      <td style="width: 100%;">
       {$feedback[row].browser}&nbsp;
      </td>
     </tr>
    </table>
   </div>
  </td>
 </tr>
 {/section}


</table>


{include file='admin_footer.tpl'}

