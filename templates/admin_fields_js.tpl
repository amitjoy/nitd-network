
{* $Id: admin_fields_js.tpl 8 2009-01-11 06:02:53Z john $ *}

{literal}
<script type="text/javascript">
<!-- 

// THIS FUNCTION CHANGES THE ORDER OF ELEMENTS
function changeorder(listorder, divId) {
  $('ajaxframe').src = 'admin_fields.php?task=changeorder&type='+cat_type+'&listorder='+listorder+'&divId='+divId;
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
	    $('ajaxframe').src = 'admin_fields.php?type='+cat_type+'&task=savecat&cat_id='+catid+'&cat_dependency='+cat_dependency+'&cat_title='+encodeURIComponent(catinput.value);
	  } else {
	    savecat_result(catid, catid, oldcat_title);
	  }
	} else {
	  $('ajaxframe').src = 'admin_fields.php?type='+cat_type+'&task=savecat&cat_id='+catid+'&cat_dependency='+cat_dependency+'&cat_title='+encodeURIComponent(catinput.value);
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
	      catdiv.innerHTML += '<div style="padding-left: 20px; padding-top: 3px; padding-bottom: 3px;">{/literal}{lang_print id=98}{literal} - <a href="javascript:addsubcat(\''+new_catid+'\');">[{/literal}{lang_print id=99}{literal}]</a></div>';
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
	  if((showCatFields == 1 && cat_dependency == 0) || (showSubcatFields == 1 && cat_dependency != 0)) {
	    catdiv.innerHTML += '<div style="padding-left: 20px; padding-top: 5px; padding-bottom: 3px;">{/literal}{lang_print id=100}{literal} - <a href="admin_fields.php?type='+cat_type+'&task=addfield&cat_id='+new_catid+'&hideSearch='+hideSearch+'&hideDisplay='+hideDisplay+'&hideSpecial='+hideSpecial+'&TB_iframe=true&height=450&width=450" class="smoothbox" title="{/literal}{lang_print id=101}{literal}">[{/literal}{lang_print id=101}{literal}]</a></div>';
	    var fielddiv = document.createElement('div');
	    fielddiv.id = 'fields_'+new_catid;
	    fielddiv.style.cssText = 'padding-left: 20px;';
	    catdiv.appendChild(fielddiv);
	    TB_init();
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


// SET NUMBER OF OPTIONS
var num_options = 0;

// SET DEP VAR
var hide_dep = false;


// ADD OPTION FUNCTION
function addOptions(value, label, dependency, dep_label, dep_id) {
	var ni = $('field_options');
	var newdiv = document.createElement('div');
	if(value == '') { value = num_options+1; }
	var divHTML = "<input type='text' name='field_options["+num_options+"][value]' class='text' maxlength='3' value='"+value+"' style='width: 40px;'><input type='text' name='field_options["+num_options+"][label]' class='text' maxlength='50' value='"+label+"' style='width: 120px;'>";
	if(!hide_dep) { 
	  divHTML += "<select name='field_options["+num_options+"][dependency]' class='text'><option value='0'";
	  if(dependency == 0) { divHTML += " SELECTED"; }
	  divHTML += ">{/literal}{lang_print id=144}{literal}</option><option value='1'";
	  if(dependency == 1) { divHTML += " SELECTED"; }
	  divHTML += ">{/literal}{lang_print id=145}{literal}</option></select><input type='text' class='text' name='field_options["+num_options+"][dependent_label]' style='width: 120px;' maxlength='100' value='"+dep_label+"'><input type='hidden' name='field_options["+num_options+"][dependent_id]' value='"+dep_id+"'>";
	}
	divHTML += "<br>";
	newdiv.innerHTML = divHTML;
	ni.appendChild(newdiv);
	num_options++;
}


// THIS FUNCTION CHANGES THE SUBCATEGORIES BASED ON THE SELECTED CATEGORIES
function changefieldcat(current_cat_id) {
	var cat_id = $('field_cat_id').value;
	var subcatSelect = $('field_subcat_id');
	subcatSelect.innerHTML = '';
	if($('field_subcat_id_div').style.display == 'none') { return; }
	for(var x in categories[cat_id].subcats) {
	  var newOption = document.createElement('option');
	  newOption.value = x;
	  newOption.appendChild(document.createTextNode(categories[cat_id].subcats[x]));
	  if(x == current_cat_id) { newOption.selected = true; }
	  subcatSelect.appendChild(newOption);
	}
}


// THIS FUNCTION CHANGES THE VISIBILITY OF THE DIVS BASED ON FIELD TYPE
function changefieldtype() {
	$('field_maxlength_div').style.display = "none";
	$('field_link_div').style.display = "none";
	$('field_regex_div').style.display = "none";
	$('field_suggestions_div').style.display = "none";
	$('field_options_div').style.display = "none";
	$('field_html_div').style.display = "none";

	if($('field_type').value=='1') {
	  $('field_maxlength_div').style.display = "block";
	  $('field_link_div').style.display = "block";
	  $('field_regex_div').style.display = "block";
	  $('field_suggestions_div').style.display = "block";
	  $('field_html_div').style.display = "block";
	} else if($('field_type').value=='2') {
	  $('field_html_div').style.display = "block";
	} else if($('field_type').value=='3') {
	  $('field_options_div').style.display = "block";
	} else if($('field_type').value=='4') {
	  $('field_options_div').style.display = "block";
	} else if($('field_type').value=='6') {
	  $('field_options_div').style.display = "block";
	}
}


// THIS FUNCTION OPENS A FORM TO ADD A FIELD
function addfield(catid) {
	var catSelect = $('field_cat_id');
	catSelect.innerHTML = '';
	for(var x in categories) {
	    var newOption = document.createElement('option');
	    newOption.value = x;
	    newOption.appendChild(document.createTextNode(categories[x].title));
	    if(x == catid) { newOption.selected = true; $('field_subcat_id_div').style.display = 'none'; }
	    var num_subcats = 0;
	    for(var y in categories[x].subcats) { if(y == catid) { newOption.selected = true; } num_subcats++; }
	    if((num_subcats != 0 && cat_type == 'profile') || cat_type != 'profile') { catSelect.appendChild(newOption); }
	}
	catSelect.disabled = false;
	changefieldcat(catid);
	addOptions('', '', '0', '', '');
	$('task').value = 'savefield';
	changefieldtype();
	if(hideSearch) { $('field_search_div').style.display = 'none'; }
	if(hideDisplay) { $('field_display_div').style.display = 'none'; }
	if(hideSpecial) { $('field_special_div').style.display = 'none'; }
	$('submitButtons').innerHTML = '<input type="submit" class="button" value="{/literal}{lang_print id=101}{literal}"><input type="button" class="button" value="{/literal}{lang_print id=39}{literal}" onClick="parent.TB_remove()">';
}


// THIS FUNCTION OPENS A FORM TO EDIT A FIELD
function editfield(fieldid, catid, title, desc, error, type, style, maxlength, link, options, required, regex, html, search, display, special) {

	var catSelect = $('field_cat_id');
	catSelect.innerHTML = '';
	for(var x in categories) {
	  if(categories[x].subcats.length != 0) {
	    var newOption = document.createElement('option');
	    newOption.value = x;
	    newOption.appendChild(document.createTextNode(categories[x].title));
	    if(x == catid) { newOption.selected = true; $('field_subcat_id_div').style.display = 'none'; }
	    for(var y in categories[x].subcats) { if(y == catid) { newOption.selected = true; }}
	    catSelect.appendChild(newOption);
	  }
	}
	catSelect.disabled = false;
	changefieldcat(catid);
	if(hideSearch) { $('field_search_div').style.display = 'none'; }
	if(hideDisplay) { $('field_display_div').style.display = 'none'; }
	if(hideSpecial) { $('field_special_div').style.display = 'none'; }
	$('field_title').value = title;
	$('field_type').value = type;
	$('field_style').value = style;
	$('field_desc').value = desc;
	$('field_error').value = error;
	$('field_html').value = html;
	$('field_search').value = search;
	$('field_display').value = display;
	$('field_special').value = special;
	$('field_required').value = required;
	$('field_maxlength').value = maxlength;
	$('field_link').value = link;
	$('field_regex').value = regex;
	var options = options.split("<~!~>");
	if(options.length > 0 && options[0] != '') {
	  for(var a=0;a<options.length;a++) {
	    if(options[a].length > 0) {
	      option = options[a].split("<!>");
	      if(type == 1) {
		$('field_suggestions').innerHTML += option[1]+"\n";
	      } else {
	        addOptions(option[0], option[1], option[2], option[3], option[4]);
	      }
	    }
	  }
	}
	$('field_id').value = fieldid;
	$('task').value = 'savefield';
	changefieldtype();
	$('submitButtons').innerHTML = '<input type="submit" class="button" value="{/literal}{lang_print id=140}{literal}"><input type="button" class="button" value="{/literal}{lang_print id=141}{literal}" onClick="removefield('+fieldid+')"><input type="button" class="button" value="{/literal}{lang_print id=39}{literal}" onClick="parent.TB_remove()">';

}


// THIS FUNCTION ENACTS THE CHANGES OF THE SAVE
function savefield_result(is_error, error_message, oldfield_id, newfield_id, field_title, field_cat_id, field_options) {

	if(hideSearch) hideSearch = 1;
	if(hideDisplay) hideDisplay = 1;
	if(hideSpecial) hideSpecial = 1;

	if(is_error != 0) {
	  $('fielderror').innerHTML = "<img src='../images/error.gif' border='0' class='icon'>&nbsp;"+error_message;
	  $('fielderror').className = "fielderror";
	  scroll(0,0);
	} else {
	  if(oldfield_id == 0) {
	    var catfields = parent.document.getElementById('fields_'+field_cat_id);
	    var newdiv = parent.document.createElement('div');
	    newdiv.id = 'field_'+newfield_id;
	    newdiv.style.cssText = 'padding-left: 15px; padding-bottom: 3px;';
	    var divHTML = "<div><img src='../images/item.gif' border='0' class='handle_field_"+field_cat_id+"' style='vertical-align: middle; margin-right: 5px; cursor: move;'><a href='admin_fields.php?type="+cat_type+"&task=getfield&field_id="+newfield_id+"&hideSearch="+hideSearch+"&hideDisplay="+hideDisplay+"&hideSpecial="+hideSpecial+"&TB_iframe=true&height=450&width=450' class='smoothbox' title='{/literal}{lang_print id=140}{literal}'>"+field_title+"</a></div><div id='dep_fields_"+newfield_id+"' style='margin-left: 15px;'></div>";
	    newdiv.innerHTML = divHTML;
	    catfields.appendChild(newdiv);
	  } else {
	    var fielddiv = parent.document.getElementById('field_'+newfield_id); 
	    var fieldarea = fielddiv.parentNode;
	    if(fieldarea.id != 'fields_'+field_cat_id) {
	      fieldarea.removeChild(fielddiv);
	      var newfieldarea = parent.document.getElementById('fields_'+field_cat_id); 
	      var newdiv = parent.document.createElement('div');
	      newdiv.id = 'field_'+newfield_id;
	      newdiv.style.cssText = 'padding-left: 15px; padding-bottom: 3px;';
	      var divHTML = "<div><img src='../images/item.gif' border='0' class='handle_field_"+field_cat_id+"' style='vertical-align: middle; margin-right: 5px; cursor: move;'><a href='admin_fields.php?type="+cat_type+"&task=getfield&field_id="+newfield_id+"&hideSearch="+hideSearch+"&hideDisplay="+hideDisplay+"&hideSpecial="+hideSpecial+"&TB_iframe=true&height=450&width=450' class='smoothbox' title='{/literal}{lang_print id=140}{literal}'>"+field_title+"</a></div><div id='dep_fields_"+newfield_id+"' style='margin-left: 15px;'></div>";
	      newdiv.innerHTML = divHTML;
	      newfieldarea.appendChild(newdiv);
	    } else {
	      var divHTML = "<div><img src='../images/item.gif' border='0' class='handle_field_"+field_cat_id+"' style='vertical-align: middle; margin-right: 5px; cursor: move;'><a href='admin_fields.php?type="+cat_type+"&task=getfield&field_id="+newfield_id+"&hideSearch="+hideSearch+"&hideDisplay="+hideDisplay+"&hideSpecial="+hideSpecial+"&TB_iframe=true&height=450&width=450' class='smoothbox' title='{/literal}{lang_print id=140}{literal}'>"+field_title+"</a></div><div id='dep_fields_"+newfield_id+"' style='margin-left: 15px;'></div>";
	      fielddiv.innerHTML = divHTML;
	    }
	  }

	  var depfieldDiv = parent.document.getElementById('dep_fields_'+newfield_id);
	  var options = field_options.split("<~!~>");
	  if(options.length > 0 && options[0] != '') {
	    for(var a=0;a<options.length;a++) {
	      if(options[a].length > 0) {
	        option = options[a].split("<!>");
		if(option[2] == 1) {
	          var depDiv = parent.document.createElement('div');
	          depDiv.id = 'dep_field_'+option[4];
	          depDiv.style.cssText = 'padding-top: 3px;';
	          divHTML = "<div><img src='../images/item_dep.gif' border='0' class='icon2'>"+option[1]+" <a href='admin_fields.php?type=profile&task=getdepfield&field_id="+option[4]+"&hideSearch="+hideSearch+"&hideDisplay="+hideDisplay+"&hideSpecial="+hideSpecial+"&TB_iframe=true&height=450&width=450' class='smoothbox' title='{/literal}{lang_print id=148}{literal}' id='dep_field_title_"+option[4]+"'>";
	          divHTML += "<i>{/literal}{lang_print id=102}{literal}</i>";
	          divHTML += "</a></div>";
	          depDiv.innerHTML = divHTML;
	          depfieldDiv.appendChild(depDiv);
	        }
	      }
	    }
	  }
	  parent.createSortable('fields_'+field_cat_id, 'img.handle_field_'+field_cat_id);
	  parent.TB_init();
	  parent.TB_remove();
	}
}


// THIS FUNCTION OPENS A FORM TO EDIT A DEPENDENT FIELD
function editdepfield(fieldid, catid, type, title, style, maxlength, link, required, regex, display, options) {

	var catSelect = $('field_cat_id');
	catSelect.innerHTML = '';
	for(var x in categories) {
	  var newOption = document.createElement('option');
	  newOption.value = x;
	  newOption.appendChild(document.createTextNode(categories[x].title));
	  if(x == catid) { newOption.selected = true; $('field_subcat_id_div').style.display = 'none'; }
	  for(var y in categories[x].subcats) { if(y == catid) { newOption.selected = true; }}
	  catSelect.appendChild(newOption);
	}
	catSelect.disabled = false;
	changefieldcat(catid);
	if(hideDisplay) { $('field_display_div').style.display = 'none'; }
	$('field_cat_id').disabled = true;
	$('field_subcat_id').disabled = true;
	$('field_title').value = title;
	$('field_type').value = type;
        $('field_type')[6] = null;
        $('field_type')[5] = null;
        $('field_type')[4] = null;
        $('field_type')[2] = null;
	$('field_style').value = style;
	$('field_desc_div').style.display = 'none';
	$('field_error_div').style.display = 'none';
	$('field_html_div').style.display = 'none';
	$('field_search_div').style.display = 'none';
	$('field_special_div').style.display = 'none';
	$('field_suggestions_div').innerHTML = '';
	$('field_display').value = display;
	$('field_required').value = required;
	$('field_maxlength').value = maxlength;
	$('field_link').value = link;
	$('field_regex').value = regex;
	hide_dep = true;
	var options = options.split("<~!~>");
	if(options.length > 0 && options[0] != '') {
	  for(var a=0;a<options.length;a++) {
	    if(options[a].length > 0) {
	      option = options[a].split("<!>");
	      addOptions(option[0], option[1], option[2], option[3], option[4]);
	    }
	  }
	}
	$('field_dependency_div').style.display = 'none';
	$('field_dependent_label_div').style.display = 'none';
	$('field_id').value = fieldid;
	$('task').value = 'savedepfield';
	changefieldtype();
	$('submitButtons').innerHTML = '<input type="submit" class="button" value="{/literal}{lang_print id=140}{literal}"><input type="button" class="button" value="{/literal}{lang_print id=39}{literal}" onClick="parent.TB_remove()">';

}


// THIS FUNCTION ENACTS THE CHANGES OF THE SAVE
function savedepfield_result(depfield_id, depfield_title) {
	parent.TB_remove();
}


// THIS FUNCTION CONFIRMS THE DELETION OF A FIELD
function removefield(fieldid) {
	if(confirm('{/literal}{lang_print id=142}{literal}')) {
	  $('ajaxframe').src = 'admin_fields.php?task=removefield&type='+cat_type+'&field_id='+fieldid;
	}
}


// THIS FUNCTION REMOVES THE FIELD FROM THE PAGE
function removefield_result(fieldid) {
	parent.TB_remove();
	var fielddiv = parent.document.getElementById('field_'+fieldid); 
	var fieldarea = fielddiv.parentNode;
	fieldarea.removeChild(fielddiv);
}



//-->
</script>
{/literal}


