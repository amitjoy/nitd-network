{include file='admin_header.tpl'}

<h2>{lang_print id=11150201}</h2>
{lang_print id=11150202}

<br><br>

{if $is_error != 0}
<div class='error'><img src='../images/error.gif' border='0' class='icon'> {$error_message}</div>
{/if}

{if $result != ""}
  <div class='success'><img src='../images/success.gif' border='0' class='icon'> {lang_print id=$result}</div>
{/if}


  {* JAVASCRIPT FOR ADDING CATEGORIES *}
  {literal}
  <script type="text/javascript">
  {/literal}
  <!-- Begin
  var articlecat_id = {$cat_max_id}+1;
  {literal}
  function addInput(fieldname) {
    var ni = document.getElementById(fieldname);
    var newdiv = document.createElement('div');
    var divIdName = 'my'+articlecat_id+'Div';
    newdiv.setAttribute('id',divIdName);
    newdiv.innerHTML = "<table cellpadding='0' cellspacing='0'><tr><td><input type='text' class='text' name='articlecat_title[" + articlecat_id +"]' size='30' maxlength='100'></td></tr><tr><td><p id='newsubcategory" + articlecat_id +"' style='margin: 0px;'></p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:addInput2('newsubcategory', '" + articlecat_id +"')\">Add Subcategory</a></td></tr><tr><td><input type='hidden' name='num_subcat_" + articlecat_id +"' id='num_subcat_" + articlecat_id +"' value='1'></td></tr><tr><td>&nbsp;</td></tr></table>";
    ni.appendChild(newdiv);
    articlecat_id++;
    window.document.articleForm.num_articlecategories.value=articlecat_id;
  }
  function addInput2(fieldname, catid) {
    fieldname = fieldname+catid;
    dep_cat_id = document.getElementById('num_subcat_'+catid).value;

    var ni = document.getElementById(fieldname);
    var newdiv = document.createElement('div');
    var divIdName = 'my'+dep_cat_id+'Div';
    newdiv.setAttribute('id',divIdName);
    newdiv.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='articlecat_title_sub[" + catid + "][" + dep_cat_id + "]' class='text' size='30' maxlength='50'><br>";
    ni.appendChild(newdiv);
    dep_cat_id++;
    document.getElementById('num_subcat_'+catid).value=dep_cat_id;
  }
  // End -->
  </script>
  {/literal}



<form action='admin_article.php' method='POST' name='articleForm'>
<br>

<table cellpadding='0' cellspacing='0' width='600'>
<td class='header'>{lang_print id=11150210}</td>
</tr>
<td class='setting1'>
{lang_print id=11150211}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_permission_article' id='permission_article_1' value='1'{if $setting_permission_article == 1} CHECKED{/if}></td>
  <td><label for='permission_article_1'>{lang_print id=11150213}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_permission_article' id='permission_article_0' value='0'{if $setting_permission_article == 0} CHECKED{/if}></td>
  <td><label for='permission_article_0'>{lang_print id=11150214}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>



<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=11150215}</td>
</tr>
<td class='setting1'>
  {lang_print id=11150216}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td width='80'>{lang_print id=11150204}</td>
  <td><input type='text' class='text' size='30' name='setting_email_articlecomment_subject' value='{$setting_email_articlecomment_subject}' maxlength='200'></td>
  </tr><tr>
  <td valign='top'>{lang_print id=11150205}</td>
  <td><textarea rows='6' cols='80' class='text' name='setting_email_articlecomment_message'>{$setting_email_articlecomment_message}</textarea><br>{lang_print id=11150217}</td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=11150218}</td>
</tr>
<td class='setting1'>
  {lang_print id=11150219}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td width='80'>{lang_print id=11150204}</td>
  <td><input type='text' class='text' size='30' name='setting_email_articlemediacomment_subject' value='{$setting_email_articlemediacomment_subject}' maxlength='200'></td>
  </tr><tr>
  <td valign='top'>{lang_print id=11150205}</td>
  <td><textarea rows='6' cols='80' class='text' name='setting_email_articlemediacomment_message'>{$setting_email_articlemediacomment_message}</textarea><br>{lang_print id=11150220}</td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=11150221}</td></tr>
<td class='setting1'>
{lang_print id=11150222}
</td></tr>
<tr>
<td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><b>{lang_print id=11150221}</b></td></tr>
  {foreach from=$categories item=category}
    <tr><td><input type='text' class='text' name='articlecat_title[{$category.articlecat_id}]' value='{$category.articlecat_title}' size='30' maxlength='100'></td></tr>
    <tr><td>
    {foreach item=subcat from=$category.subcategories}
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='articlecat_title_sub[{$category.articlecat_id}][{$subcat.articlecat_id}]' value='{$subcat.articlecat_title}' class='text' size='30' maxlength='50'><br>
    {/foreach}
    <p id='newsubcategory{$category.articlecat_id}' style='margin: 0px;'></p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:addInput2('newsubcategory', '{$category.articlecat_id}')">{lang_print id=11150224}</a></p>
    </td></tr>
    <tr><td><input type='hidden' id='num_subcat_{$category.articlecat_id}' name='num_subcat_{$category.articlecat_id}' value='{$category.next_subcat}'></td></tr>
    <tr><td>&nbsp;</td></tr>
  {/foreach}

  </td>
  </tr>
  </table>
  <p id='newcategory' style='margin: 0px;'></p>
  <a href="javascript:addInput('newcategory')">{lang_print id=11150223}</a><input type='hidden' name='num_articlecategories' value='{$cat_max_id}'></td></tr>
</td>
</tr>
</table>

<br>
<input type='submit' class='button' value='{lang_print id=11150209}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='admin_footer.tpl'}