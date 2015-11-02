
{*
@author Eldar
@copyright Hire-Experts LLC
@version Quiz 1.02
*}

{include file='header.tpl'}

<script type="text/javascript" src="./include/js/he_quiz.js"></script>

{include file='quiz_tabs.tpl'}

    
{lang_javascript ids=690691100,690691101,690691102}


<form method="post" enctype="multipart/form-data" onsubmit="return he_quiz.check_results({$min_result_count});">

<div class="he_quiz_results">
{assign var=result_number value=0}

{foreach from=$result_arr item=result name=quiz_result}
{assign var=result_number value=$smarty.foreach.quiz_result.iteration}
<div class="he_quiz_result">
<table class="quiz_tbl" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th align="left">{lang_print id=690691095}<b class="he_result_number">{$result_number}</b></th>
            <th align="right"><a href="javascript://" onclick="he_quiz.delete_result(this)">{lang_print id=690691098}</a></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><label>{lang_print id=690691096}</label></td>
            <td><input class="result_title text" type="text" name="title[]" value="{$result.title}"/></td>
        </tr>
        <tr>
            <td><label>{lang_print id=690691097}</label></td>
            <td>
                <input type="hidden" class="result_id" name="result_id[]" value="{$result.id}"/>
                <textarea class="text" rows="5" cols="50" name="description[]">{$result.description}</textarea>
            </td>
        </tr>
        <tr>
            <td><label>{lang_print id=690691195}</label></td>
            <td>
                <input class="result_photo text" type="file" name="photo_{$result_number}"/>
                <input type="hidden" name="photo[]" value="{$result.photo}"/>
                {if $result.photo}
                <div style="padding-top: 8px; padding-bottom: 8px;">
                    <img src="{$photo_url}{$result.photo}" alt="{$result.title}" width="100px;"/>
                </div>
                {/if}
            </td>
        </tr>
    </tbody>
</table>
</div>
{/foreach}
</div>

<div class="he_quiz_add_result">
    <input type="button" class="button" id="he_quiz_add_result_btn" value="{lang_print id=690691115}" onclick="he_quiz.add_result({$result_number})"/>
</div>

<div class="he_quiz_save_result">
    <input type="hidden" name="task" value="save_results"/>
    <input type="submit" class="button" value="{lang_print id=1209}"/>
</div>

</form>


<div style="display: none;">
<div class="he_quiz_result" id="he_quiz_result_tpl">
<table class="quiz_tbl" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th align="left">{lang_print id=690691095}<b class="he_result_number"></b></th>
            <th align="right"><a href="javascript://" class="he_result_del">{lang_print id=690691098}</a></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><label>{lang_print id=690691096}</label></td>
            <td><input class="result_title text" type="text" name="title[]" value=""/></td>
        </tr>
        <tr>
            <td><label>{lang_print id=690691097}</label></td>
            <td>
                <input type="hidden" class="result_id" name="result_id[]" value="0"/>
                <textarea class="text" rows="5" cols="50" name="description[]"></textarea>
            </td>
        </tr>
        <tr>
            <td><label>{lang_print id=690691195}</label></td>
            <td>
                <input class="result_photo text" type="file" name="photo"/>
                <input type="hidden" name="photo[]" value=""/>
            </td>
        </tr>
    </tbody>
</table>
</div>
</div>

{include file='footer.tpl'}