{* $Id: admin_document_js.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{* #REFERENCE : admin_fields_js.php *}
{* $Id: admin_document_js.tpl 8 2009-01-11 06:02:53Z john $ *}

{literal}
<script type="text/javascript">
<!-- 

// THIS FUNCTION CHANGES THE ORDER OF ELEMENTS
function changeorder(listorder, divId) {
  $('ajaxframe').src = 'admin_document_cat_update.php?task=changeorder&listorder='+listorder+'&divId='+divId;
}


// THIS FUNCTION PREVENTS THE ENTER KEY FROM SUBMITTING THE FORM
function noenter_cat(catid, e) { 
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	if(keycode == 13) {
	  var catinput = $('cat_'+catid+'_input'); 
	  catinput.blur();
	  return false;
	}
}


// THIS FUNCTION ADDS A CATEGORY INPUT TO THE PAGE
function addcat() {
	var catarea = $('categories');
	var newdiv = document.createElement('div');
	newdiv.id = 'cat_new';
	newdiv.innerHTML ='<div style="font-weight: bold;"><img src="../images/folder_open_yellow.gif" border="0" class="handle_cat" style="vertical-align: middle; margin-right: 5px; cursor: move;"><span id="cat_new_span"><input type="text" id="cat_new_input" maxlength="100" onBlur="savecat(\'new\', \'\', \'\')" onkeypress="return noenter_cat(\'new\', event)"></span></div>';
	catarea.appendChild(newdiv);
	var catinput = $('cat_new_input');
	catinput.focus();
}


// THIS FUNCTION ADDS A SUB CATEGORY INPUT TO THE PAGE
function addsubcat(catid) {
	var catarea = $('subcats_'+catid);
	var newdiv = document.createElement('div');
	newdiv.id = 'cat_new';
	newdiv.style.cssText = 'padding-left: 15px;';
	if(catarea.nextSibling) { 
	  var thisdiv = catarea.nextSibling;
	  while(thisdiv.nodeName != "DIV") { if(thisdiv.nextSibling) { thisdiv = thisdiv.nextSibling; } else { break; } }
	  if(thisdiv.nodeName != "DIV") { next_catid = "new"; } else { next_catid = thisdiv.id.substr(4); }
	} else {
	  next_catid = 'new';
	}
	newdiv.innerHTML = '<div><img src="../images/folder_open_green.gif" border="0" class="handle_subcat_'+catid+'" style="vertical-align: middle; margin-right: 5px; cursor: move;"><span id="cat_new_span"><input type="text" id="cat_new_input" maxlength="100" onBlur="savecat(\'new\', \'\', \''+catid+'\')" onkeypress="return noenter_cat(\'new\', event)"></span></span></div>';
	catarea.appendChild(newdiv);
	var catinput = $('cat_new_input');
	catinput.focus();
}


// THIS FUNCTION RUNS THE APPROPRIATE SAVING ACTION
function savecat(catid, oldcat_title, cat_dependency) {
	var catinput = $('cat_'+catid+'_input'); 
	if(catinput.value == "" && catid == "new") {
	  removecat(catid);
	} else if(catinput.value == "" && catid != "new") {
	  if(confirm('{/literal}{lang_print id=105}{literal}')) {
	    $('ajaxframe').src = 'admin_document_cat_update.php?task=savecat&cat_id='+catid+'&cat_dependency='+cat_dependency+'&cat_title='+encodeURIComponent(catinput.value);
	  } else {
	    savecat_result(catid, catid, oldcat_title);
	  }
	} else {
	  $('ajaxframe').src = 'admin_document_cat_update.php?task=savecat&cat_id='+catid+'&cat_dependency='+cat_dependency+'&cat_title='+encodeURIComponent(catinput.value);
	}
}


// THIS FUNCTION IS ENACTS THE FRONT-END CHANGES FOR THE SAVED CATEGORY
function savecat_result(old_catid, new_catid, cat_title, cat_dependency) {
	var catinput = $('cat_'+old_catid+'_input'); 
	var catspan = $('cat_'+old_catid+'_span'); 
	var catdiv = $('cat_'+old_catid); 
	catdiv.id = 'cat_'+new_catid;
	catspan.id = 'cat_'+new_catid+'_span';
	catspan.innerHTML = '<a href="javascript:editcat(\''+new_catid+'\', \''+cat_dependency+'\');" id="cat_'+new_catid+'_title">'+cat_title+'</a>';
	if(old_catid == 'new') {
	  if(cat_dependency == 0) {
	    if(subcatTab == 1) {
	      catdiv.innerHTML += '<div style="padding-left: 20px; padding-top: 3px; padding-bottom: 3px;">{/literal}Sub Categories {literal} - <a href="javascript:addsubcat(\''+new_catid+'\');">[{/literal}Add New{literal}]</a></div>';
	    } else {
	      catdiv.innerHTML += '<div style="padding-left: 20px; padding-top: 3px; padding-bottom: 3px;">{/literal}{lang_print id=1202}{literal} - <a href="javascript:addsubcat(\''+new_catid+'\');">[{/literal}{lang_print id=1203}{literal}]</a></div>';
	    }
	    var subcatdiv = document.createElement('div');
	    subcatdiv.id = 'subcats_'+new_catid;
	    subcatdiv.style.cssText = 'padding-left: 20px;';
	    catdiv.appendChild(subcatdiv);
	    createSortable('categories', 'img.handle_cat');
	  } else {
	    createSortable('subcats_'+cat_dependency, 'img.handle_subcat_'+cat_dependency);
	  }
	}
}


// THIS FUNCTION REMOVES A CATEGORY FROM THE PAGE
function removecat(catid) {
	var catdiv = $('cat_'+catid); 
	var catarea = catdiv.parentNode;
	catarea.removeChild(catdiv);
}


// THIS FUNCTION CREATES AN INPUT FOR EDITING A CATEGORY
function editcat(catid, cat_dependency) {
	var catspan = $('cat_'+catid+'_span'); 
	var cattitle = $('cat_'+catid+'_title');
	catspan.innerHTML = '<input type="text" id="cat_'+catid+'_input" maxlength="100" onBlur="savecat(\''+catid+'\', \''+cattitle.innerHTML.replace(/'/g, "&amp;#039;")+'\', \''+cat_dependency+'\')" onkeypress="return noenter_cat(\''+catid+'\', event)" value="'+cattitle.innerHTML+'">';
	var catinput = $('cat_'+catid+'_input'); 
	catinput.focus();
}




//-->
</script>
{/literal}


