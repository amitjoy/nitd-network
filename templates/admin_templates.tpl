{include file='admin_header.tpl'}

{* $Id: admin_templates.tpl 235 2009-11-13 04:30:39Z phil $ *}

<h2>{lang_print id=15}</h2>
{lang_print id=467}
<br />
<br />

<div class='floatleft' style='width: 250px;'>
<b>{lang_print id=468}</b><br>
<table cellpadding='0' cellspacing='0'>
{section name=file_loop loop=$user_files}
<tr>
<td><img src='../images/icon_file.gif' border='0'>&nbsp;</td>
<td><a href="javascript: editTemplate('{$user_files[file_loop].filename}');">{$user_files[file_loop].filename}</a></td>
</tr>
{/section}
</table>
</div>

<div class='floatleft' style='width: 200px; padding-left: 20px;'>
<b>{lang_print id=469}</b><br>
<table cellpadding='0' cellspacing='0'>
{section name=file_loop loop=$base_files}
<tr>
<td><img src='../images/icon_file.gif' border='0'>&nbsp;</td>
<td><a href="javascript: editTemplate('{$base_files[file_loop].filename}');">{$base_files[file_loop].filename}</a></td>
</tr>
{/section}
</table>
</div>

<div>
<b>{lang_print id=470}</b><br>
<table cellpadding='0' cellspacing='0'>
{section name=file_loop loop=$head_files}
<tr>
<td><img src='../images/icon_file.gif' border='0'>&nbsp;</td>
<td><a href="javascript: editTemplate('{$head_files[file_loop].filename}');">{$head_files[file_loop].filename}</a></td>
</tr>
{/section}
</table>
</div>


{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
  function editTemplate(t) {
    $('t').value = t;
    var url = 'admin_templates.php?task=gettemplate&t='+t;
	var request = new Request.JSON({secure: false, url: url,
		onComplete: function(jsonObj) {
			if(jsonObj.is_error == 0) {
			  edit(jsonObj.template);
			} else {
			  alert(jsonObj.error_message);
			}
		}
	}).send();
  }
  function edit(template) {
    TB_show('{/literal}{lang_print id=471}{literal}', '#TB_inline?height=600&width=700&inlineId=template', '', '../images/trans.gif');
    $("TB_window").getElements('textarea[id=template_code]').each(function(el) { el.value = template; });
  }

//-->
</script>
{/literal}

{* HIDDEN DIV TO DISPLAY TEMPLATE EDITING FIELD *}
<div style='display: none;' id='template'>
  <form action='admin_templates.php' method='post' name='editform' target='ajaxframe' onSubmit='parent.TB_remove();'>
  <div style='margin-top: 10px; margin-bottom: 10px;'>{lang_print id=472}</div>
  <textarea name='template_code' id='template_code' rows='20' style='width: 100%; font-size: 11px; height: 485px; font-family: verdana, serif;'>{$template_code}</textarea>
  <br><br>
  <input type='submit' class='button' value='{lang_print id=173}'> <input type='button' class='button' value='{lang_print id=466}' onClick='parent.TB_remove();'>
  <input type='hidden' name='task' value='save'>
  <input type='hidden' name='t' id='t' value=''>
  </form>
</div>


{include file='admin_footer.tpl'}