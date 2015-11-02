{* $Id: admin_document_category.tpl 1 2010-02-02 09:36:11Z SocialEngineAddOns $ *}
{* REFERENCE : admin_profile.tpl *}

{include file='admin_header.tpl'}


<h2>{lang_print id=650003219}</h2>
{lang_print id=650003220} 
<br />
<br />




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
{include file='admin_document_js.tpl'}


{* SHOW ADD CATEGORY LINK *}
<div style='font-weight: bold;'>&nbsp;{lang_print id=650003221} - <a href='javascript:addcat();'>[Add Category]</a></div>

<div id='categories' style='padding-left: 5px; font-size: 8pt;'>

{* LOOP THROUGH CATEGORIES *}
{section name=cat_loop loop=$categories}

  {* CATEGORY DIV *}
  <div id='cat_{$categories[cat_loop].category_id}'>

    {* SHOW CATEGORY *}
    <div style='font-weight: bold;'><img src='../images/folder_open_yellow.gif' border='0' class='handle_cat' style='vertical-align: middle; margin-right: 5px; cursor: move;'><span id='cat_{$categories[cat_loop].category_id}_span'><a href='javascript:editcat("{$categories[cat_loop].category_id}", "0");' id='cat_{$categories[cat_loop].category_id}_title'>{$categories[cat_loop].category_name}</a></span></div>

    {* SHOW ADD SUBCATEGORY LINK *}
    <div style='padding-left: 20px; padding-top: 3px; padding-bottom: 3px;'>Sub Categories - <a href='javascript:addsubcat("{$categories[cat_loop].category_id}");'>[Add New]</a></div>

    {* JAVASCRIPT FOR SORTING CATEGORIES AND FIELDS *}
    {literal}
    <script type="text/javascript">
    <!-- 
    window.addEvent('domready', function(){ createSortable('subcats_{/literal}{$categories[cat_loop].category_id}{literal}', 'img.handle_subcat_{/literal}{$categories[cat_loop].category_id}{literal}'); });
    //-->
    </script>
    {/literal}

    {* SUBCATEGORY DIV *}
    <div id='subcats_{$categories[cat_loop].category_id}' style='padding-left: 20px;'>

      {* LOOP THROUGH SUBCATEGORIES *}
      {section name=subcat_loop loop=$categories[cat_loop].sub_categories}
        <div id='cat_{$categories[cat_loop].sub_categories[subcat_loop].sub_cat_id}' style='padding-left: 15px;'>
          <div><img src='../images/folder_open_green.gif' border='0' class='handle_subcat_{$categories[cat_loop].category_id}' style='vertical-align: middle; margin-right: 5px; cursor: move;'><span id='cat_{$categories[cat_loop].sub_categories[subcat_loop].sub_cat_id}_span'><a href='javascript:editcat("{$categories[cat_loop].sub_categories[subcat_loop].sub_cat_id}", "{$categories[cat_loop].category_id}");' id='cat_{$categories[cat_loop].sub_categories[subcat_loop].sub_cat_id}_title'>{$categories[cat_loop].sub_categories[subcat_loop].sub_cat_name}</a></span></div>

          
        </div>
      {/section}
    </div>
  </div>
{/section}

</div>

{include file='admin_footer.tpl'}