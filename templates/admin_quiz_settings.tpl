
{*
@author Ermek
@copyright Hire-Experts LLC
@version Quiz 1.02
*}

{include file='admin_header.tpl'}

<link rel="stylesheet" href="../templates/he_admin_styles.css" title="stylesheet" type="text/css" />  

{literal}
<script type="text/javascript">
var he_quiz_cat = 
{
    construct: function()
    {
        var cats = $('quiz_cats').getElements('.quiz_cat');
        for ( var i = 0; i < cats.length; i++ )
        {
            this.prepare_cat(cats[i]);
        }        
    },
    add_cat: function()
    {
        var $node = $('quiz_cat_tpl').clone();
        $node.removeProperty('id');
        
        this.prepare_cat($node);

        $('quiz_cats').adopt($node);                
    },

    prepare_cat: function( $node )
    {
        $node.getElement('.cat_delete').addEvent('click', function()
        {
            var result = confirm('Are you sure you want to delete category');

            if ( !result ) {
                return false;
            } 
            
            $(this).getParent('.quiz_cat').destroy();           
        });
        $node.getElement('.cat_label').addEvent('click', function()
        {
            $(this).setStyle('display', 'none');
            $(this).getPrevious('.cat_label_input').setStyle('display', 'block').set('value', $(this).get('text'));
        });
        
        $node.getElement('.cat_label_input').addEvent('blur', function()
        {
            if ( $(this).value.trim().length==0 )
            {
                return false;
            } 
            
            $(this).setStyle('display', 'none');
            $(this).getNext('.cat_label').setStyle('display', 'block').set('text', $(this).value.trim());           
        });
    }       
}

window.addEvent('domready', function()
{
    he_quiz_cat.construct();
});

</script>
{/literal}

<h2>{lang_print id=690691140}</h2>
{lang_print id=690691141}
<br />
<br />

<form method="post">
<table cellpadding="0" cellspacing="0" class="he_tbl" style="width: 600px">
<thead>
	<tr>
		<td class="header" colspan="2">{lang_print id=690691142}</td>
	</tr>
</thead>
<tbody>
	<tr>
		<td class="item">{lang_print id=690691143}</td>
		<td class="item"><input type="text" name="setting_he_quiz_min_result" value="{$setting_he_quiz_min_result}"/></td>
	</tr>
	<tr>
		<td class="item">{lang_print id=690691144}</td>
		<td class="item"><input type="text" name="setting_he_quiz_min_question" value="{$setting_he_quiz_min_question}"/></td>
	</tr>
	<tr>
		<td class="item">{lang_print id=690691176}</td>
		<td class="item">
			{if $setting_he_quiz_approval_status==0}
				<input type="checkbox" name="setting_he_quiz_approval_status" />
			{else}
				<input type="checkbox" name="setting_he_quiz_approval_status" checked />
			{/if}
		</td>
	</tr>
</tbody>
</table>
<br/>
<input type="hidden" name="task" value="save_settings"/>
<input class="button" type="submit" value="{lang_print id=173}"/>
 </form>

<br />
<br />

<h2>{lang_print id=690691182}</h2>
{lang_print id=690691183}
<form method="post">
    <div id="quiz_cats" style="width: 200px;">
    {foreach from=$cats item=cat name=quiz_cats}
        <div class="quiz_cat">
        {if $smarty.foreach.quiz_cats.first}
            <div style="float: left; width: 169px;">
               <input type="text" name="cat_label[]" class="text cat_label_input" value="{$cat.label}" style="display: none"/>
               <input type="hidden" name="cat_id[]" value="{$cat.id}" />
                <a href="javascript://" class="cat_label" title="{lang_print id=187}">{$cat.label}</a>
            </div>
            <div style="float: left; width: 30px;">
                <a href="javascript://" onclick="he_quiz_cat.add_cat()" title="{lang_print id=104}">
                    <img src="../images/he_quiz_cat_add.png" border="0" alt="{lang_print id=104}"/>
                </a>
                <a href="javascript://" class="cat_delete" style="display: none"></a>
            </div>
            <div class="clr"></div>
       {else}
            <div style="float: left; width: 169px;">
                <input type="text" name="cat_label[]" class="text cat_label_input" value="{$cat.label}" style="display: none"/>
                <input type="hidden" name="cat_id[]" value="{$cat.id}" />
                <a href="javascript://" class="cat_label" title="{lang_print id=187}">{$cat.label}</a>
            </div>
            <div style="float: left; width: 30px;">
                <a href="javascript://" class="cat_delete" style="display: block" title="{lang_print id=155}">
                    <img src="../images/he_quiz_cat_delete.png" alt="{lang_print id=155}" style="width: 16px; height: 16px;" border="0" />
                </a>
            </div>
            <div class="clr"></div>
       {/if}
       </div>
    {/foreach}
    
    </div>
    <br/>
    <div style="width: 200px;">
        <input type="hidden" name="task" value="save_changes" />
        <input type="submit" value="{lang_print id=173}" class="button"/>
    </div>
</form>

<br />
<br />

<h2>{lang_print id=690691162}</h2>
{lang_print id=690691163}

<div class="he_box" style="width: 600px; margin-left:0">
	<center><h2>{ldelim}he_quiz_list count=5{rdelim}</h2></center>
</div>

<br />


<div style="display: none;">
    <div id="quiz_cat_tpl" class="quiz_cat" style="width: 200px;">
        <div style="float: left; width: 169px;">
            <input type="text" name="cat_label[]" class="text cat_label_input" />
            <input type="hidden" name="cat_id[]" class="text cat_label_input" />
            <a href="javascript://" class="cat_label" style="display: none"></a>
        </div>
        <div style="float: left; width: 30px;">
            <a href="javascript://" class="cat_delete" style="display: block">
                <img src="../images/he_quiz_cat_delete.png" style="width: 16px; height: 16px;" border="0" />
            </a>
        </div>
        <div class="clr"></div>
    </div>
</div>

{include file='admin_footer.tpl'}