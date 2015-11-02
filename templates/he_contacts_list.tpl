{*
@author Eldar
@copyright Hire-Experts LLC
@version Contacts Module 1.01
*}

{foreach from=$contacts item='contact'}
    <a class="item" id="contact_{$contact->user_info.user_id}" href='javascript:he_contacts.choose_contact({$contact->user_info.user_id});'>
        <span class='photo' style='background-image:url({$contact->user_photo("./images/he_contacts_no_photo.png", TRUE)})'><span class="inner"></span></span>
        <span class="name">{$contact->user_displayname}</span>
        <div class="clr"></div>
    </a>
{/foreach}