{include file='admin_header.tpl'}

<h2>{lang_print id=6000002}</h2>
{lang_print id=6000004}
<br />
<br />





<input type='button' class='button' value='{lang_print id=6000005}' onClick='addCategory();'>
<input type='button' class='button' value='{lang_print id=6000006}' {if $forumcats|@count == 0}disabled='disabled'{else}onClick='addForum();'{/if}>

<br><br>

<table cellpadding='0' cellspacing='0' class='list'>
<tr>
<td class='header' colspan='2'>{lang_print id=6000007}</td>
<td class='header' width='150' style='text-align: right;'>{lang_print id=153}&nbsp;&nbsp;</td>
</tr>
{section name=forumcat_loop loop=$forumcats}
  <tr class='background1'>
  <td class='item' colspan='2' style='font-weight: bold;'>{lang_print id=$forumcats[forumcat_loop].forumcat_title}</td>
  <td class='item' nowrap='nowrap' style='vertical-align: top; text-align: right;'>
    {capture assign='forumcat_title'}{lang_print id=$forumcats[forumcat_loop].forumcat_title}{/capture}
    {if $smarty.section.forumcat_loop.first != TRUE}<a href='admin_forum.php?task=movecategory&forumcat_id={$forumcats[forumcat_loop].forumcat_id}'>{lang_print id=943}</a> | {/if}
    <a href="javascript: editCategory('{$forumcats[forumcat_loop].forumcat_id}', '{$forumcat_title|replace:"&#039;":"\&#039;"}');">{lang_print id=187}</a>
     | <a href="javascript: confirmDeleteCat('{$forumcats[forumcat_loop].forumcat_id}');">{lang_print id=155}</a>
  &nbsp;
  </td>
  </tr>

  {section name=forum_loop loop=$forumcats[forumcat_loop].forums}
    {capture assign='forum_title'}{lang_print id=$forumcats[forumcat_loop].forums[forum_loop].forum_title}{/capture}
    {capture assign='forum_desc'}{lang_print id=$forumcats[forumcat_loop].forums[forum_loop].forum_desc}{/capture}
    <tr class='background2'>
    <td class='item' valign='top' width='1' style='padding-right: 0px;'><img src='../images/icons/forum_forum16.gif' border='0'></td>
    <td class='item'>
      <div style='font-weight: bold;'>{lang_print id=$forumcats[forumcat_loop].forums[forum_loop].forum_title}</div>
      <div>{lang_print id=$forumcats[forumcat_loop].forums[forum_loop].forum_desc}</div>
      <div style='margin-top: 10px;'>
        {lang_print id=6000008} {section name=mod_loop loop=$forumcats[forumcat_loop].forums[forum_loop].forum_mods}{if $smarty.section.mod_loop.first != TRUE}, {/if}{$forumcats[forumcat_loop].forums[forum_loop].forum_mods[mod_loop].user_displayname} ({$forumcats[forumcat_loop].forums[forum_loop].forum_mods[mod_loop].user_username}){sectionelse}{lang_print id=6000019}{/section}
        &nbsp;[ <a href='javascript: moderators("{$forumcats[forumcat_loop].forums[forum_loop].forum_id}", "{$forum_title|replace:"&#039;":"\&#039;"|replace:'"':'&quot;'|replace:'&quot;':'\&quot;'}", {$forumcats[forumcat_loop].forums[forum_loop].forum_mods_js}, {$forumcats[forumcat_loop].forums[forum_loop].forum_mods_id_js});'>{lang_print id=6000020}</a> ]
      </div>
    </td>
    <td class='item' nowrap='nowrap' style='vertical-align: top; text-align: right;'>
      {if $smarty.section.forum_loop.first != TRUE}<a href='admin_forum.php?task=moveforum&forum_id={$forumcats[forumcat_loop].forums[forum_loop].forum_id}'>{lang_print id=943}</a> | {/if}
      <a href='javascript:editForum("{$forumcats[forumcat_loop].forums[forum_loop].forum_id}", "{$forumcats[forumcat_loop].forumcat_id}", "{$forum_title|replace:"&#039;":"\&#039;"|replace:'"':'&quot;'|replace:'&quot;':'\&quot;'}", "{$forum_desc|replace:"&#039;":"\&#039;"|replace:"'":"\'"|replace:'"':'&quot;'|replace:'&quot;':'\&quot;'|replace:"<":"&lt;"|replace:">":"&gt;"}", {$forumcats[forumcat_loop].forums[forum_loop].forum_level_view}, {$forumcats[forumcat_loop].forums[forum_loop].forum_level_post});'>{lang_print id=187}</a>
       | <a href="javascript: confirmDeleteForum('{$forumcats[forumcat_loop].forums[forum_loop].forum_id}');">{lang_print id=155}</a>
    &nbsp;
    </td>
    </tr>
  {/section}
{/section}
</table>

{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
var forumcat_id = 0;
function addCategory() {
  $('forumcat_title').value = '';
  $('forumcat_title').defaultValue = '';
  $('forumcat_id').value = 0;
  $('forumcat_task').value = "addcategory";
  $('forumcat_submit').value = "{/literal}{lang_print id=104}{literal}";
  TB_show('{/literal}{lang_print id=104}{literal}', '#TB_inline?height=250&width=350&inlineId=addcategory', '', '../images/trans.gif');
}

function editCategory(id, title) {
  $('forumcat_title').value = title;
  $('forumcat_title').defaultValue = title;
  $('forumcat_id').value = id;
  $('forumcat_task').value = "editcategory";
  $('forumcat_submit').value = "{/literal}{lang_print id=951}{literal}";
  TB_show('{/literal}{lang_print id=951}{literal}', '#TB_inline?height=250&width=350&inlineId=addcategory', '', '../images/trans.gif');
}

function confirmDeleteCat(id) {
  forumcat_id = id;
  forum_id = 0;
  $('confirm_message').innerHTML = '{/literal}{lang_print id=6000010}{literal}';
  TB_show('{/literal}{lang_print id=952}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
}


var forum_id = 0;
function addForum() {
  $('forum_forumcat_id').options[0].defaultSelected = true;
  $('forum_title').value = '';
  $('forum_title').defaultValue = '';
  $('forum_desc').value = '';
  $('forum_desc').defaultValue = '';
  $('forum_id').value = 0;
  $('forum_task').value = "addforum";
  $('forum_submit').value = "{/literal}{lang_print id=6000011}{literal}";
  for(var i=0;i<$('view_levels').options.length;i++) { 
    $('view_levels').options[i].selected = true; 
    $('view_levels').options[i].defaultSelected = true; 
  }
  for(var i=0;i<$('post_levels').options.length;i++) { 
    $('post_levels').options[i].selected = true; 
    $('post_levels').options[i].defaultSelected = true; 
  }
  TB_show('{/literal}{lang_print id=6000011}{literal}', '#TB_inline?height=500&width=450&inlineId=addforum', '', '../images/trans.gif');
}

function editForum(id, cat_id, title, description, level_view, level_post) {
  $('forum_forumcat_id').value = cat_id;
  $('forum_forumcat_id').options[$('forum_forumcat_id').selectedIndex].defaultSelected = true;
  $('forum_title').value = title;
  $('forum_title').defaultValue = title;
  $('forum_desc').value = description;
  $('forum_desc').defaultValue = description;
  $('forum_id').value = id;
  $('forum_task').value = "editforum";
  $('forum_submit').value = "{/literal}{lang_print id=6000012}{literal}";
  for(var i=0;i<$('view_levels').options.length;i++) { 
    if(level_view.indexOf($('view_levels').options[i].value) == -1) {
      $('view_levels').options[i].selected = false; 
      $('view_levels').options[i].defaultSelected = false; 
    } else {
      $('view_levels').options[i].selected = true; 
      $('view_levels').options[i].defaultSelected = true; 
    }
  }
  for(var i=0;i<$('post_levels').options.length;i++) { 
    if(level_post.indexOf($('post_levels').options[i].value) == -1) {
      $('post_levels').options[i].selected = false; 
      $('post_levels').options[i].defaultSelected = false; 
    } else {
      $('post_levels').options[i].selected = true; 
      $('post_levels').options[i].defaultSelected = true; 
    }
  }
  TB_show('{/literal}{lang_print id=6000012}{literal}', '#TB_inline?height=500&width=450&inlineId=addforum', '', '../images/trans.gif');
}

function confirmDeleteForum(id) {
  forum_id = id;
  forumcat_id = 0;
  $('confirm_message').innerHTML = '{/literal}{lang_print id=6000013}{literal}';
  TB_show('{/literal}{lang_print id=6000014}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
}

var forum_moderators = [];
function moderators(id, title, moderators_full, moderators_ids) {
  $('forummoderator_forum_id').value = id;
  forum_moderators = moderators_ids;
  $('moderator_table').getElements('span[id=mod_span]').each(function(el) { el.destroy(); });
  if(moderators_full.length > 0) {
    for(var m=0;m<moderators_full.length;m++) {
      var newSpan = new Element('span', {id: 'mod_span', html: '<input type="hidden" name="mods[]" value="' + moderators_full[m].user_id + '"><input type="checkbox" style="vertical-align: middle;" name="mods_keep[' + moderators_full[m].user_id + ']" value="1" checked="checked">' + moderators_full[m].user_displayname + ' (' + moderators_full[m].user_username + ')&nbsp;&nbsp;&nbsp;'});
      newSpan.inject($('moderator_column'));
    }
    $('mod_none').style.display = 'none';
    $('mod_row').style.display = '';
  } else {
    $('mod_none').style.display = '';
    $('mod_row').style.display = 'none';
  }
  TB_show('{/literal}{lang_print id=6000021}{literal} - "' + title + '"', '#TB_inline?height=450&width=550&inlineId=moderators', '', '../images/trans.gif');
}



function deletedo() {
  if(forum_id != 0) {
    window.location = 'admin_forum.php?task=deleteforum&forum_id='+forum_id;
  } else if(forumcat_id != 0) {
    window.location = 'admin_forum.php?task=deletecategory&forumcat_id='+forumcat_id;
  }
}

//-->
</script>
{/literal}


{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;' id='confirm_message'>
    {lang_print id=6000010}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deletedo();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>


{* HIDDEN DIV TO DISPLAY ADD/EDIT CATEGORY *}
<div style='display: none;' id='addcategory'>
  <form action='admin_forum.php' method='post' target='_parent' onSubmit="{literal}if(this.forumcat_title.value == ''){ alert('{/literal}{lang_print id=945}{literal}'); return false;}else{return true;}{/literal}">
  <div style='margin-top: 10px;'>{lang_print id=6000009}</div>
  <br>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td align='right' class='form1'>{lang_print id=258}:&nbsp;</td>
  <td class='form2'><input type='text' class='text' name='forumcat_title' id='forumcat_title' size='30' maxlength='50'></td>
  </tr>
  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'>
    <input type='submit' class='button' id='forumcat_submit' value='{lang_print id=104}'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
    <input type='hidden' name='task' id='forumcat_task' value='addcategory'>
    <input type='hidden' name='forumcat_id' id='forumcat_id' value='0'>
    </form>
  </td>
  </tr>
  </table>
</div>


{* HIDDEN DIV TO DISPLAY ADD/EDIT FORUM *}
<div style='display: none;' id='addforum'>
  <form action='admin_forum.php' method='post' target='_parent' onSubmit="{literal}if(this.forum_title.value == ''){ alert('{/literal}{lang_print id=6000015}{literal}'); return false;}else{return true;}{/literal}">
  <div style='margin-top: 10px;'>{lang_print id=6000016}</div>
  <br>
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td class='form1' align='right'>{lang_print id=107}&nbsp;</td>
  <td class='form2'>
    <select name='forumcat_id' id='forum_forumcat_id' class='text'>
    {section name=forumcat_loop loop=$forumcats}
      <option value='{$forumcats[forumcat_loop].forumcat_id}'>{lang_print id=$forumcats[forumcat_loop].forumcat_title}</option>
    {/section}
    </select>
  </td>
  </tr>
  <tr>
  <td class='form1' align='right'>{lang_print id=6000017}&nbsp;</td>
  <td class='form2'><input type='text' class='text' name='forum_title' id='forum_title' size='50' maxlength='50'></td>
  </tr>
  <tr>
  <td class='form1' align='right' valign='top'>{lang_print id=6000018}&nbsp;</td>
  <td class='form2'><textarea name='forum_desc' class='text' id='forum_desc' rows='7' cols='50'></textarea></td>
  </tr>
  <tr>
  <td class='form1' align='right' valign='top'>&nbsp;</td>
  <td class='form2'>
    {lang_print id=6000028}<br>
    <select size='4' class='text' name='view_levels[]' id='view_levels' multiple='multiple' style='width: 250px;'>
      <option value='0' selected='selected'>{lang_print id=6000055}</option>
    {section name=level_loop loop=$levels}
      <option value='{$levels[level_loop].level_id}' selected='selected'>{$levels[level_loop].level_name|truncate:75:"...":true}{if $levels[level_loop].level_default == 1} {lang_print id=382}{/if}</option>
    {/section}
    </select>
  </td>
  </tr>
  <tr>
  <td class='form1' align='right' valign='top'>&nbsp;</td>
  <td class='form2'>
    {lang_print id=6000029}<br>
    <select size='4' class='text' name='post_levels[]' id='post_levels' multiple='multiple' style='width: 250px;'>
      <option value='0' selected='selected'>{lang_print id=6000055}</option>
    {section name=level_loop loop=$levels}
      <option value='{$levels[level_loop].level_id}' selected='selected'>{$levels[level_loop].level_name|truncate:75:"...":true}{if $levels[level_loop].level_default == 1} {lang_print id=382}{/if}</option>
    {/section}
    </select>
  </td>
  </tr>
  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'>
    <input type='submit' class='button' id='forum_submit' value='{lang_print id=6000011}'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
    <input type='hidden' name='task' id='forum_task' value='addforum'>
    <input type='hidden' name='forum_id' id='forum_id' value='0'>
    </form>
  </td>
  </tr>
  </table>

</div>




{* HIDDEN DIV TO DISPLAY MANAGE MODERATORS *}
<div style='display: none;' id='moderators'>

  {* JAVASCRIPT FOR SEARCHING USERS *}
  {literal}
  <script type="text/javascript">
  <!-- 
    function searchUsers(mod_user, page) {
      if(mod_user == '') { alert('{/literal}{lang_print id=6000054}{literal}'); return false; }
      var url = 'admin_forum.php?task=searchusers&mod_user='+mod_user+'&page='+page;
      var request = new Request.JSON({secure: false, url: url,
		onComplete: function(jsonObj) {
			if(jsonObj.total_results == 0) {
			  $('TB_window').getElement('div[id=no_users]').style.display = 'block';
			  $('TB_window').getElement('div[id=results]').style.display = 'none';
			} else {
			  $('TB_window').getElement('div[id=no_users]').style.display = 'none';
			  $('TB_window').getElement('div[id=results]').style.display = 'block';
			  $('TB_window').getElement('div[id=results]').innerHTML = '';
			  $('TB_window').getElement('div[id=pages]').innerHTML = '';
			   
			  if(jsonObj.total_results > jsonObj.results.length) {
			    $('TB_window').getElement('div[id=pages]').style.display = 'block';
			    var total_pages = Math.ceil(jsonObj.total_results/10);
			    var newHTML = '{/literal}{lang_print id=1005}{literal}';
			    for(var p=1;p<=total_pages;p++) {
			      if(p == jsonObj.page) {
			        newHTML += ' <b>'+p+'</b> ';
			      } else {
			        newHTML += ' <a href="javascript:searchUsers(\''+mod_user+'\', '+p+');">'+p+'</a> ';
			      }
			    }
			    $('TB_window').getElement('div[id=pages]').innerHTML = newHTML;
			  }

			  newHTML = '';
			  for(var i=0;i<jsonObj.results.length;i++) {
			    newHTML += "<div><input type='checkbox' id='user_" + jsonObj.results[i].id + "' ";
			    if(forum_moderators.indexOf(jsonObj.results[i].id) == -1) {
			      newHTML += "onClick=\"this.disabled=true;addMod('"+jsonObj.results[i].id+"', '"+jsonObj.results[i].display_name+"', '"+jsonObj.results[i].username+"');\"";
			    } else {
			      newHTML += "disabled='disabled' checked='checked'";
			    }
			    newHTML += "> <label for='user_" + jsonObj.results[i].id + "'>" + jsonObj.results[i].display_name + " (" + jsonObj.results[i].username + ")</label></div>";
			  }
			  $('TB_window').getElement('div[id=results]').innerHTML = newHTML;
			}
		}
    }).send();
    }

    function addMod(user_id, display_name, username) {
      forum_moderators.push(user_id);
      $('TB_window').getElement('tr[id=mod_none]').style.display = 'none';
      $('TB_window').getElement('tr[id=mod_row]').style.display = '';
      var newSpan = new Element('span', {id: 'mod_span', html: '<input type="hidden" name="mods[]" value="' + user_id + '"><input type="checkbox" style="vertical-align: middle;" name="mods_keep[' + user_id + ']" value="1" checked="checked"> ' + display_name + ' (' + username + ')&nbsp;&nbsp;&nbsp;'});
      newSpan.inject($('TB_window').getElement('td[id=moderator_column]'));
    }
  //-->
  </script>
  {/literal}

  <form action='admin_forum.php' method='post' target='_parent'>
  <div style='margin-top: 10px;'>{lang_print id=6000023}</div>
  <br>

  <table cellpadding='0' cellspacing='0' class='list' id='moderator_table' style='margin-bottom: 10px;'>
  <tr><td class='header'>{lang_print id=6000025}</td></tr>
  <tr id='mod_none' class='background2'><td class='item'>{lang_print id=6000026}</td></tr>
  <tr id='mod_row' class='background2'><td class='item' id='moderator_column'></td></tr>
  </table>

  <input type='submit' class='button' value='{lang_print id=6000022}'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  <input type='hidden' name='task' value='moderators'>
  <input type='hidden' name='forummoderator_forum_id' id='forummoderator_forum_id' value='0'>
  </form>
  
  <div style='padding: 10px; background: #EEEEEE; border: 1px solid #CCCCCC; margin-top: 15px;'>
    <div>
      {lang_print id=6000024}
    </div>
    <div style='margin-top: 7px;'>
      <form method='post' onSubmit='searchUsers(this.mod_user.value, 1); return false;'>
      <input type='text' class='text' name='mod_user' id='mod_user' size='50' maxlength='50'>
      <input type='submit' class='button_small' value='{lang_print id=6000027}'>
      </form>
    </div>
  </div>

  <br>
  
  <div id='no_users' style='width:100%;height:125px;overflow:auto;border:1px solid #AAAAAA;display:none;'>{lang_print id=1003}</div>
  <div id='results' style='width:70%;height:125px;overflow:auto;border:1px solid #AAAAAA;display:none;padding:6px;'></div>
  <div id='pages' style='display: none; margin-top: 5px;'></div>

</div>


{include file='admin_footer.tpl'}