{include file='admin_header.tpl'}

{literal}
<script type="text/javascript" src="../include/js/feedback.js"></script>
<script type="text/javascript">
 var del_id=0; 
</script>

<style type="text/css">
 .td1{width: 50%;}
 .td2{text-align: right; width: 20%;}
 .td3{text-align: right; width: 20%;}
</style>

{/literal}

{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
 <div style='margin-top: 10px;'>
  {lang_print id=17001022}
 </div>
 <br>
 <input type='button' class='button' value='{lang_print id=175}' id='deletebutton' onClick='parent.TB_remove(); window.document.getElementById("deleteform_"+del_id).submit();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>



<h2>{lang_print id=17001026}</h2>
<div>{lang_print id=17001027}</div>
<br />

<table cellspacing="0" cellpadding="0" style="width: auto;" class="list">
 <tr>
  <td class="header td1" style="text-transform: capitalize;">{lang_print id=17001014}</td>
  <td class="header td2" style="text-transform: capitalize;">[ {lang_print id=187} ]</td>
  <td class="header td2" style="text-transform: capitalize;">[ {lang_print id=17001009} ]</td>
 </tr>

 {section name=type loop=$feedbacks_types}
 <tr class="background2">
  <td class="item td1">{$feedbacks_types[type].name}</td>
  <td class="item td2">
   [ <a href="#" onclick="javascript: editFeedbackType('{$feedbacks_types[type].id}','{$feedbacks_types[type].name}');">{lang_print id=187}</a> ]
  </td>
  <td class="item td3">
   [ <a href="#" onclick="javascript: del_id = confirmDeleteFeedbackType('{$feedbacks_types[type].id}', '{lang_print id=17001009}');">{lang_print id=17001009}</a> ]
   <form enctype="multipart/formdata" method="POST" id="deleteform_{$feedbacks_types[type].id}" style="display: none;">
    <input type="hidden" name="type_to_delete" value="{$feedbacks_types[type].id}">
   </form>
  </td>
 </tr>
 {/section}

</table>

<br>

<a href="#"onclick="javascript: addFeedbackType('{lang_print id=17001036}');">{lang_print id=17001028}</a><br>


{include file='admin_footer.tpl'}