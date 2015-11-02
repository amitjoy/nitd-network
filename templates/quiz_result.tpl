
{*
@author Eldar
@copyright Hire-Experts LLC
@version Quiz 1.02
*}

{include file='header.tpl'}

<div class="he_message_cont">
    <div id="he_message" {if $message}class="he_message_{$message.type}"{else}style="display: none;"{/if}>
		<span class="t">{if $message.title}{$message.title}{/if}</span>
        <span class="c">{if $message.text}{$message.text}{/if}</span>
    </div>
</div>

<div class="he_quiz_header">
	<div class="he_quiz_thumb"><img src="{if $quiz_info.photo_src}{$quiz_info.photo_src}{else}./images/he_quiz_thumb.jpg{/if}"/></div>
	<div class="he_quiz_content">
		<div class="page_header">{$quiz_info.name}</div>
		<div>{$quiz_info.description}</div>
	</div>
	<div class="clr"></div>
</div>

<div class="he_quiz_passed_message">{lang_print id=690691153}</div>
<div class="he_quiz_passed">
	<div class="t">{$quiz_result.title}</div>
	<div class="d">
	{if !$quiz_result.photo}
        {$quiz_result.description}
    {else}
        <div style="float: left; width: 110px;"><img src="{$photo_url}{$quiz_result.photo}" alt="{$quiz_result.title}" width="100px;"/></div>
        <div style="float: left; width: 489px;">{$quiz_result.description}</div>
        <div class="clr"></div>
    {/if}
	</div>
</div>
<div class="btn">
    <input type="button" class="button" onclick="he_contacts.link('{lang_print id=690691170}', 'quiz_suggest_to_friends.php?quiz_id={$quiz_info.quiz_id}', false)" value="{lang_print id=690691170}" />
    <div class="portal_spacer"></div>
    <input type="button" class="button" onclick="window.location.href='browse_quiz_results.php?quiz_id={$quiz_info.quiz_id}'" value="{lang_print id=690691128}"/>
</div>

{include file='footer.tpl'}