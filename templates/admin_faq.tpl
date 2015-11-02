{include file='admin_header.tpl'}

{* $Id: admin_faq.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=935}</h2>
{lang_print id=936}

<br><br>

<input type='button' class='button' value='{lang_print id=104}' onClick='addCategory();'>
<input type='button' class='button' value='{lang_print id=937}' onClick='addQuestion();'>

<br><br>

<table cellpadding='0' cellspacing='0' class='list'>
<tr>
<td class='header'>{lang_print id=939}</td>
<td class='header' align='center' nowrap='nowrap'>{lang_print id=940}</td>
<td class='header' align='center' nowrap='nowrap'>{lang_print id=941}</td>
<td class='header' align='center' nowrap='nowrap'>{lang_print id=942}</td>
<td class='header' width='150'>{lang_print id=153}</td>
</tr>
{section name=faqcat_loop loop=$faqcats}
  <tr class='background1'>
  <td class='item' colspan='4'><b>{lang_print id=$faqcats[faqcat_loop].faqcat_title}</b></td>
  <td class='item' style='font-weight: bold;' nowrap='nowrap'>
    {capture assign='faqcat_title'}{lang_print id=$faqcats[faqcat_loop].faqcat_title}{/capture}
    <a href="javascript: editCategory('{$faqcats[faqcat_loop].faqcat_id}', '{$faqcat_title|replace:"&#039;":"\&#039;"}');">{lang_print id=187}</a>
     | <a href="javascript: confirmDeleteCat('{$faqcats[faqcat_loop].faqcat_id}');">{lang_print id=155}</a>
    {if $smarty.section.faqcat_loop.first != TRUE} | <a href='admin_faq.php?task=movecategory&faqcat_id={$faqcats[faqcat_loop].faqcat_id}'>{lang_print id=943}</a>{/if}
  </td>
  </tr>

  {section name=faq_loop loop=$faqcats[faqcat_loop].faqs}
    <tr class='background2'>
    <td class='item' style='padding-left: 30px;'>{lang_print id=$faqcats[faqcat_loop].faqs[faq_loop].faq_subject}</td>
    <td class='item' align='center'><a href='#' onClick="{literal}if(confirm('{/literal}{lang_print id=950}{literal}')) { location.href='admin_faq.php?task=resetviews&faq_id={/literal}{$faqcats[faqcat_loop].faqs[faq_loop].faq_id}{literal}'; }{/literal}" title='{lang_sprintf id=949 1=$faqcats[faqcat_loop].faqs[faq_loop].faq_views}'>{$faqcats[faqcat_loop].faqs[faq_loop].faq_views_average}</a></td>
    <td class='item' align='center'>{$datetime->cdate("`$setting.setting_dateformat`", $datetime->timezone("`$faqcats[faqcat_loop].faqs[faq_loop].faq_datecreated`", $setting.setting_timezone))}</td>
    <td class='item' align='center'>{if $faqcats[faqcat_loop].faqs[faq_loop].faq_dateupdated != 0}{$datetime->cdate("`$setting.setting_dateformat`", $datetime->timezone("`$faqcats[faqcat_loop].faqs[faq_loop].faq_dateupdated`", $setting.setting_timezone))}{/if}&nbsp;</td>
    <td class='item' nowrap='nowrap'>
      {capture assign='faq_subject'}{lang_print id=$faqcats[faqcat_loop].faqs[faq_loop].faq_subject}{/capture}
      {capture assign='faq_content'}{lang_print id=$faqcats[faqcat_loop].faqs[faq_loop].faq_content}{/capture}
      <a href="javascript:editQuestion('{$faqcats[faqcat_loop].faqs[faq_loop].faq_id}', '{$faqcats[faqcat_loop].faqcat_id}', '{$faq_subject|replace:"&#039;":"\&#039;"}', '{$faq_content|replace:"&#039;":"\&#039;"|replace:"'":"\'"|replace:'"':'&quot;'|replace:"<":"&lt;"|replace:">":"&gt;"}');">{lang_print id=187}</a>
       | <a href="javascript: confirmDeleteQuest('{$faqcats[faqcat_loop].faqs[faq_loop].faq_id}');">{lang_print id=155}</a>
      {if $smarty.section.faq_loop.first != TRUE} | <a href='admin_faq.php?task=movequestion&faq_id={$faqcats[faqcat_loop].faqs[faq_loop].faq_id}'>{lang_print id=943}</a>{/if}
    </td>
    </tr>
  {/section}
{/section}
</table>

{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
var faqcat_id = 0;
function addCategory() {
  $('cat_title').value = '';
  $('cat_title').defaultValue = '';
  $('faqcat_id').value = 0;
  $('faqcat_task').value = "addcategory";
  $('faqcat_submit').value = "{/literal}{lang_print id=104}{literal}";
  TB_show('{/literal}{lang_print id=104}{literal}', '#TB_inline?height=250&width=350&inlineId=addcategory', '', '../images/trans.gif');
}

function editCategory(id, title) {
  $('cat_title').value = title;
  $('cat_title').defaultValue = title;
  $('faqcat_id').value = id;
  $('faqcat_task').value = "editcategory";
  $('faqcat_submit').value = "{/literal}{lang_print id=951}{literal}";
  TB_show('{/literal}{lang_print id=951}{literal}', '#TB_inline?height=250&width=350&inlineId=addcategory', '', '../images/trans.gif');
}

function confirmDeleteCat(id) {
  faqcat_id = id;
  faq_id = 0;
  $('confirm_message').innerHTML = '{/literal}{lang_print id=953}{literal}';
  TB_show('{/literal}{lang_print id=952}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
}


var faq_id = 0;
function addQuestion() {
  $('faq_faqcat_id').options[0].defaultSelected = true;
  $('faq_subject').value = '';
  $('faq_subject').defaultValue = '';
  $('faq_content').value = '';
  $('faq_content').defaultValue = '';
  $('faq_id').value = 0;
  $('faq_task').value = "addquestion";
  $('faq_submit').value = "{/literal}{lang_print id=937}{literal}";
  TB_show('{/literal}{lang_print id=937}{literal}', '#TB_inline?height=350&width=450&inlineId=addquestion', '', '../images/trans.gif');
}

function editQuestion(id, cat_id, subject, content) {
  $('faq_faqcat_id').value = cat_id;
  $('faq_faqcat_id').options[$('faq_faqcat_id').selectedIndex].defaultSelected = true;
  $('faq_subject').value = subject;
  $('faq_subject').defaultValue = subject;
  $('faq_content').value = content;
  $('faq_content').defaultValue = content;
  $('faq_id').value = id;
  $('faq_task').value = "editquestion";
  $('faq_submit').value = "{/literal}{lang_print id=954}{literal}";
  TB_show('{/literal}{lang_print id=954}{literal}', '#TB_inline?height=350&width=450&inlineId=addquestion', '', '../images/trans.gif');
}

function confirmDeleteQuest(id) {
  faq_id = id;
  faqcat_id = 0;
  $('confirm_message').innerHTML = '{/literal}{lang_print id=956}{literal}';
  TB_show('{/literal}{lang_print id=955}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
}



function deletedo() {
  if(faq_id != 0) {
    window.location = 'admin_faq.php?task=deletequestion&faq_id='+faq_id;
  } else if(faqcat_id != 0) {
    window.location = 'admin_faq.php?task=deletecategory&faqcat_id='+faqcat_id;
  }
}

//-->
</script>
{/literal}


{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;' id='confirm_message'>
    {lang_print id=953}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deletedo();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>


{* HIDDEN DIV TO DISPLAY ADD/EDIT CATEGORY *}
<div style='display: none;' id='addcategory'>
  <form action='admin_faq.php' method='post' target='_parent' onSubmit="{literal}if(this.cat_title.value == ''){ alert('{/literal}{lang_print id=945}{literal}'); return false;}else{return true;}{/literal}">
  <div style='margin-top: 10px;'>{lang_print id=944}</div>
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td align='right' class='form1'>{lang_print id=258}:&nbsp;</td>
  <td class='form2'><input type='text' class='text' name='cat_title' id='cat_title' size='30' maxlength='50'></td>
  </tr>
  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'>
    <input type='submit' class='button' id='faqcat_submit' value='{lang_print id=104}'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
    <input type='hidden' name='task' id='faqcat_task' value='addcategory'>
    <input type='hidden' name='faqcat_id' id='faqcat_id' value='0'>
    </form>
  </td>
  </tr>
  </table>
</div>


{* HIDDEN DIV TO DISPLAY ADD/EDIT QUESTION *}
<div style='display: none;' id='addquestion'>
  <form action='admin_faq.php' method='post' target='_parent' onSubmit="{literal}if(this.faq_subject.value == ''){ alert('{/literal}{lang_print id=947}{literal}'); return false;}else{return true;}{/literal}">
  <div style='margin-top: 10px;'>{lang_print id=948}</div>
  <br>
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td class='form1' align='right'>{lang_print id=107}&nbsp;</td>
  <td class='form2'>
    <select name='faqcat_id' id='faq_faqcat_id' class='text'>
    {section name=faqcat_loop loop=$faqcats}
      <option value='{$faqcats[faqcat_loop].faqcat_id}'>{lang_print id=$faqcats[faqcat_loop].faqcat_title}</option>
    {/section}
    </select>
  </td>
  </tr>
  <tr>
  <td class='form1' align='right'>{lang_print id=946}&nbsp;</td>
  <td class='form2'><input type='text' class='text' name='faq_subject' id='faq_subject' size='50'></td>
  </tr>
  <tr>
  <td class='form1' align='right' valign='top'>{lang_print id=938}&nbsp;</td>
  <td class='form2'><textarea name='faq_content' id='faq_content' rows='10' cols='45'></textarea></td>
  </tr>
  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'>
    <input type='submit' class='button' id='faq_submit' value='{lang_print id=937}'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
    <input type='hidden' name='task' id='faq_task' value='addquestion'>
    <input type='hidden' name='faq_id' id='faq_id' value='0'>
    </form>
  </td>
  </tr>
  </table>

</div>

{include file='admin_footer.tpl'}