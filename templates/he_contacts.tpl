{*
@author Eldar
@copyright Hire-Experts LLC
@version Contacts Module 1.01
*}

{include file="header_global.tpl"}

<script type="text/javascript" src="./include/js/he_contacts.js"></script>

<script type="text/javascript">
    window.addEvent('domready', function(){ldelim}
        he_contacts.init('{$callback_url}', {$message_allowed}, {$last});
    {rdelim});
</script>

<div id="he_contacts_loading" style="display:none;">&nbsp;</div>
<div id="he_contacts_message" style="display:none;"></div>
<div class="he_contacts">
    <div class="contacts">
        <div class="label">{lang_print id=690698000} <span class="info">{lang_print id=690698001}</span></div>
        <div id="he_contacts_list">
            {if $contacts}
                {$contacts_compiled}
            {else}
                <div class="no">{lang_print id=690698002}</div>
            {/if}
            <div class="clr" id="he_contacts_end_line"></div>
        </div>
        <div class="{if $more_contacts_existed}more{else}more_disabled{/if}" id="he_contacts_more" onclick="he_contacts.get_more_contacts()">{lang_print id=690698003}</div>
        <div class="clr"></div>
    </div>

    <div class="emails">
        <div class="label">{lang_print id=690698004} <span class="info">{lang_print id=690698005}</span></div>
        <textarea id="he_contacts_emails"></textarea>
    </div>
    
    <div class="btn"><input type="button" class="button" onclick="he_contacts.send();" value="{lang_print id=690698006}" /></div>
</div>

</body>
</html>