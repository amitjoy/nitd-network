{include file='admin_header.tpl'}

{* $Id: admin_profile.tpl 235 2009-11-13 04:30:39Z phil $ *}

<h2>{lang_print id=16}</h2>
{lang_print id=95} 
<br />
<br />


{* DISPLAY CATS FOR EDITING - RE-ADD LATER *}
{*
{section name=cat_loop loop=$cats}

  { * CATEGORY DIV * }
  <div id='catEdit_{$cats[cat_loop].cat_id}'>
    <a href="admin_profile_edit.php?profilecat_id={$cats[cat_loop].cat_id}">
      {lang_print id=$cats[cat_loop].cat_title}
    </a>
  </div>
  
{/section}
<br />
*}



{* JAVASCRIPT FOR ADDING CATEGORIES AND FIELDS *}
{literal}
<script type="text/javascript">
<!-- 
var categories;
var cat_type = 'profile';
var showCatFields = 0;
var showSubcatFields = 1;
var subcatTab = 1;
var hideSearch = 0;
var hideDisplay = 0;
var hideSpecial = 0;

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


{* SHOW ADD CATEGORY LINK *}
<div style='font-weight: bold;'>&nbsp;{lang_print id=103} - <a href='javascript:addcat();'>[{lang_print id=104}]</a></div>

<div id='categories' style='padding-left: 5px; font-size: 11px;'>

{* LOOP THROUGH CATEGORIES *}
{section name=cat_loop loop=$cats}

  {* CATEGORY DIV *}
  <div id='cat_{$cats[cat_loop].cat_id}'>

    {* SHOW CATEGORY *}
    <div style='font-weight: bold;'><img src='../images/folder_open_yellow.gif' border='0' class='handle_cat' style='vertical-align: middle; margin-right: 5px; cursor: move;'><span id='cat_{$cats[cat_loop].cat_id}_span'><a href='javascript:editcat("{$cats[cat_loop].cat_id}", "0");' id='cat_{$cats[cat_loop].cat_id}_title'>{lang_print id=$cats[cat_loop].cat_title}</a></span></div>

    {* SHOW ADD SUBCATEGORY LINK *}
    <div style='padding-left: 20px; padding-top: 3px; padding-bottom: 3px;'>{lang_print id=98} - <a href='javascript:addsubcat("{$cats[cat_loop].cat_id}");'>[{lang_print id=99}]</a></div>

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

          {* SHOW ADD FIELD LINK *}
          <div style='padding-left: 20px; padding-top: 5px; padding-bottom: 3px;'>{lang_print id=100} - <a href='admin_fields.php?type=profile&task=addfield&cat_id={$cats[cat_loop].subcats[subcat_loop].subcat_id}&TB_iframe=true&height=450&width=450' class='smoothbox' title='{lang_print id=101}'>[{lang_print id=101}]</a></div>

	  {* JAVASCRIPT FOR SORTING CATEGORIES AND FIELDS *}
	  {literal}
	  <script type="text/javascript">
	  <!-- 
	  window.addEvent('domready', function(){ createSortable('fields_{/literal}{$cats[cat_loop].subcats[subcat_loop].subcat_id}{literal}', 'img.handle_field_{/literal}{$cats[cat_loop].subcats[subcat_loop].subcat_id}{literal}'); });
	  //-->
	  </script>
	  {/literal}

          {* FIELD DIV *}
          <div id='fields_{$cats[cat_loop].subcats[subcat_loop].subcat_id}' style='padding-left: 20px;'>
          
            {* LOOP THROUGH FIELDS *}
            {section name=field_loop loop=$cats[cat_loop].subcats[subcat_loop].fields}
              <div id='field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' style='padding-left: 15px; padding-bottom: 3px;'>
                <div><img src='../images/item.gif' border='0' class='handle_field_{$cats[cat_loop].subcats[subcat_loop].subcat_id}' style='vertical-align: middle; margin-right: 5px; cursor: move;'><a href='admin_fields.php?type=profile&task=getfield&field_id={$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}&TB_iframe=true&height=450&width=450' class='smoothbox' title='{lang_print id=140}'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_title}</a></div>

                {* DEPENDENT FIELD DIV *}
                <div id='dep_fields_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_id}' style='margin-left: 15px;'>

                  {* LOOP THROUGH DEPENDENT FIELDS *}
                  {section name=dep_field_loop loop=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options}
		    {if $cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[dep_field_loop].dependency != 0}
                      <div id='dep_field_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[dep_field_loop].dep_field_id}' style='padding-top: 3px;'>
                        <div><img src='../images/item_dep.gif' border='0' class='icon2'>{lang_print id=$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[dep_field_loop].label} <a href='admin_fields.php?type=profile&task=getdepfield&field_id={$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[dep_field_loop].dep_field_id}&TB_iframe=true&height=450&width=450' class='smoothbox' title='{lang_print id=148}' id='dep_field_title_{$cats[cat_loop].subcats[subcat_loop].fields[field_loop].field_options[dep_field_loop].dep_field_id}'><i>{lang_print id=102}</i></a></div>
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
  </div>
{/section}

</div>

{include file='admin_footer.tpl'}