{include file='admin_header.tpl'}

{* $Id: admin_group.tpl 247 2009-11-14 03:30:43Z phil $ *}

<h2>{lang_print id=2000003}</h2>
{lang_print id=2000073}
<br />
<br />


{* JAVASCRIPT FOR ADDING CATEGORIES AND FIELDS *}
{literal}
<script type="text/javascript">
<!-- 
var categories;
var cat_type = 'group';
var showCatFields = 1;
var showSubcatFields = 0;
var subcatTab = 0;
var hideSearch = 1;
var hideDisplay = 1;
var hideSpecial = 1;

function createSortable(divId, handleClass) {
	new Sortables($(divId), {handle:handleClass, onComplete: function() { changeorder(this.serialize(), divId); }});
}

Sortables.implement({
	serialize: function(){
		var serial = [];
		this.list.getChildren().each(function(el, i){
			serial[i] = el.getProperty('id');
		}, this);
		return serial;
	}
});


window.addEvent('domready', function(){	createSortable('categories', 'img.handle_cat'); });

//-->
</script>
{/literal}

{* INCLUDE JAVASCRIPT AND FIELD DIV *}
{include file='admin_fields_js.tpl'}





{if $result != 0}
  <div class='success'><img src='../images/success.gif' class='icon' border='0'> {lang_print id=191}</div>
{/if}


<form action='admin_group.php' method='POST'>

<table cellpadding='0' cellspacing='0' width='600'>
<td class='header'>{lang_print id=192}</td>
</tr>
<td class='setting1'>
  {lang_print id=2000074}
</td>
</tr>
<tr>
<td class='setting2'>
  <table cellpadding='2' cellspacing='0'>
  <tr>
  <td><input type='radio' name='setting_permission_group' id='permission_group_1' value='1'{if $setting.setting_permission_group == 1} CHECKED{/if}></td>
  <td><label for='permission_group_1'>{lang_print id=2000075}</label></td>
  </tr>
  <tr>
  <td><input type='radio' name='setting_permission_group' id='permission_group_0' value='0'{if $setting.setting_permission_group == 0} CHECKED{/if}></td>
  <td><label for='permission_group_0'>{lang_print id=2000076}</label></td>
  </tr>
  </table>
</td>
</tr>
</table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=2000077}</td>
</tr>
<td class='setting1'>
{lang_print id=2000078}
</td></tr><tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td><input type='radio' name='setting_group_discussion_code' id='discussion_code_1' value='1'{if $setting.setting_group_discussion_code == 1} CHECKED{/if}>&nbsp;</td><td><label for='discussion_code_1'>{lang_print id=2000079}</label></td></tr>
  <tr><td><input type='radio' name='setting_group_discussion_code' id='discussion_code_0' value='0'{if $setting.setting_group_discussion_code == 0} CHECKED{/if}>&nbsp;</td><td><label for='discussion_code_0'>{lang_print id=2000080}</label></td></tr>
  </table>
</td></tr></table>

<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr>
<td class='header'>{lang_print id=2000330}</td>
</tr>
<tr>
<td class='setting1'>
{lang_print id=2000331}
</td>
</tr>
<tr>
<td class='setting2'>
<input type='text' class='text' name='setting_group_discussion_html' value='{$setting.setting_group_discussion_html}' maxlength='250' size='60'>
</td>
</tr>
</table>
  
<br>

<table cellpadding='0' cellspacing='0' width='600'>
<tr><td class='header'>{lang_print id=2000081}</td></tr>
<tr><td class='setting1'>
{lang_print id=2000082}
</td></tr><tr><td class='setting2'>


  {* SHOW ADD CATEGORY LINK *}
  <div style='font-weight: bold;'>&nbsp;{lang_print id=2000083} - <a href='javascript:addcat();'>[{lang_print id=104}]</a></div>

  <div id='categories' style='padding-left: 5px; font-size: 11px;'>

  {* LOOP THROUGH CATEGORIES *}
  {section name=cat_loop loop=$cats}

    {* CATEGORY DIV *}
    <div id='cat_{$cats[cat_loop].cat_id}'>

      {* SHOW CATEGORY *}
      <div style='font-weight: bold;'><img src='../images/folder_open_yellow.gif' border='0' class='handle_cat' style='vertical-align: middle; margin-right: 5px; cursor: move;'><span id='cat_{$cats[cat_loop].cat_id}_span'><a href='javascript:editcat("{$cats[cat_loop].cat_id}", "0");' id='cat_{$cats[cat_loop].cat_id}_title'>{lang_print id=$cats[cat_loop].cat_title}</a></span></div>

      {* SHOW ADD SUBCATEGORY LINK *}
      <div style='padding-left: 20px; padding-top: 3px; padding-bottom: 3px;'>{lang_print id=1202} - <a href='javascript:addsubcat("{$cats[cat_loop].cat_id}");'>[{lang_print id=1203}]</a></div>

      {* JAVASCRIPT FOR SORTING CATEGORIES AND FIELDS *}
      {literal}
      <script type="text/javascript">
      <!-- 
      window.addEvent('domready', function(){ createSortable('subcats_{/literal}{$cats[cat_loop].cat_id}{literal}', 'img.handle_subcat_{/literal}{$cats[cat_loop].cat_id}{literal}'); });
      //-->
      </script>
      {/literal}

      {* SUBCATEGORY DIV *}
      <div id='subcats_{$cats[cat_loop].cat_id}' style='padding-left: 20px;'>

        {* LOOP THROUGH SUBCATEGORIES *}
        {section name=subcat_loop loop=$cats[cat_loop].subcats}
          <div id='cat_{$cats[cat_loop].subcats[subcat_loop].subcat_id}' style='padding-left: 15px;'>
            <div><img src='../images/folder_open_green.gif' border='0' class='handle_subcat_{$cats[cat_loop].cat_id}' style='vertical-align: middle; margin-right: 5px; cursor: move;'><span id='cat_{$cats[cat_loop].subcats[subcat_loop].subcat_id}_span'><a href='javascript:editcat("{$cats[cat_loop].subcats[subcat_loop].subcat_id}", "{$cats[cat_loop].cat_id}");' id='cat_{$cats[cat_loop].subcats[subcat_loop].subcat_id}_title'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].subcat_title}</a></span></div>
          </div>
        {/section}

      </div>

      {* SHOW ADD FIELD LINK *}
      <div style='padding-left: 20px; padding-top: 5px; padding-bottom: 3px;'>{lang_print id=100} - <a href='admin_fields.php?type=group&task=addfield&cat_id={$cats[cat_loop].cat_id}&hideSearch=1&hideDisplay=1&hideSpecial=1&TB_iframe=true&height=450&width=450' class='smoothbox' title='{lang_print id=101}'>[{lang_print id=101}]</a></div>

      {* JAVASCRIPT FOR SORTING CATEGORIES AND FIELDS *}
      {literal}
      <script type="text/javascript">
      <!-- 
      window.addEvent('domready', function(){ createSortable('fields_{/literal}{$cats[cat_loop].cat_id}{literal}', 'img.handle_field_{/literal}{$cats[cat_loop].cat_id}{literal}'); });
      //-->
      </script>
      {/literal}

      {* FIELD DIV *}
      <div id='fields_{$cats[cat_loop].cat_id}' style='padding-left: 20px;'>
          
        {* LOOP THROUGH FIELDS *}
        {section name=field_loop loop=$cats[cat_loop].fields}
          <div id='field_{$cats[cat_loop].fields[field_loop].field_id}' style='padding-left: 15px; padding-bottom: 3px;'>
            <div><img src='../images/item.gif' border='0' class='handle_field_{$cats[cat_loop].cat_id}' style='vertical-align: middle; margin-right: 5px; cursor: move;'><a href='admin_fields.php?type=group&task=getfield&field_id={$cats[cat_loop].fields[field_loop].field_id}&hideSearch=1&hideDisplay=1&hideSpecial=1&TB_iframe=true&height=450&width=450' class='smoothbox' title='{lang_print id=140}'>{lang_print id=$cats[cat_loop].fields[field_loop].field_title}</a></div>

            {* DEPENDENT FIELD DIV *}
            <div id='dep_fields_{$cats[cat_loop].fields[field_loop].field_id}' style='margin-left: 15px;'>

              {* LOOP THROUGH DEPENDENT FIELDS *}
              {section name=dep_field_loop loop=$cats[cat_loop].fields[field_loop].field_options}
	        {if $cats[cat_loop].fields[field_loop].field_options[dep_field_loop].dependency != 0}
                  <div id='dep_field_{$cats[cat_loop].fields[field_loop].field_options[dep_field_loop].dep_field_id}' style='padding-top: 3px;'>
                    <div><img src='../images/item_dep.gif' border='0' class='icon2'>{lang_print id=$cats[cat_loop].fields[field_loop].field_options[dep_field_loop].label} <a href='admin_fields.php?type=group&task=getdepfield&field_id={$cats[cat_loop].fields[field_loop].field_options[dep_field_loop].dep_field_id}&hideSearch=1&hideDisplay=1&hideSpecial=1&TB_iframe=true&height=450&width=450' class='smoothbox' title='{lang_print id=148}' id='dep_field_title_{$cats[cat_loop].fields[field_loop].field_options[dep_field_loop].dep_field_id}'><i>{lang_print id=102}</i></a></div>
		  </div>
		{/if}
              {/section}

            </div>
          </div>
        {/section}
      </div>
    </div>
  {/section}

  </div>



</td></tr></table>
  
<br>

<input type='submit' class='button' value='{lang_print id=173}'>
<input type='hidden' name='task' value='dosave'>
</form>


{include file='admin_footer.tpl'}