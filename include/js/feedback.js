function makeFeedback(title) {
 TB_show(title, 'feedback.php?TB_iframe=true&height=320&width=300', '', './images/trans.gif');

}

/********* feedback.tpl **********************/
function showErr() {

 if($("feedback_txt").value == ""){
  $('feedback_error').setStyle('display', 'block');
  return false;
 }

 return true;
}

function showSuccess(){
 $('main_table').setStyle('display', 'none');
 $('success_table').setStyle('display', 'block');
 window.setInterval(closeWindow,1000);
}

function closeWindow(){
 $('user_feedback').submit();
 //window.document.forms['user_feedback'].submit();
 parent.window.location.reload();
}

function closeWindow2(){
 parent.window.location.reload();
}
/*******************************/
/********* admin_feedback.tpl **********************/
function confirmDeleteFeedback(id, title) {
 TB_show(title, '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
 $('deletebutton').focus();
 return id;
}


function showMoreInfo(id){
 var table=$("more_info_"+id);
 var button=$("more_info_button_"+id);

 if(table.getStyle("display") == "none"){
  table.setStyle('display', 'block');
  button.value="-";
 }
 else{
  table.setStyle('display', 'none');
  button.value="+";
 }

}



/*******************************/
/********* admin_feedback_settings.tpl **********************/
function addFeedbackType(name) {
 TB_show(name, 'admin_feedbacktype_add.php?TB_iframe=true&height=90&width=300', '', './images/trans.gif');

}

function editFeedbackType(id, name) {
 TB_show(name, 'admin_feedbacktype_edit.php?id=' + id +'TB_iframe=true&height=90&width=300', '', './images/trans.gif');

}

function confirmDeleteFeedbackType(id, name) {
 TB_show(name, '#TB_inline?height=100&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
 document.getElementById('deletebutton').focus();
 return id;
}

/*******************************/
/********* admin_feedback_settings.tpl **********************/
function editType(){
 $('admin_feedbacktype_edit').submit();
 parent.window.location.reload();
}
/*******************************/
