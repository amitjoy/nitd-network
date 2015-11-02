
{* $Id: header.tpl 189 2009-07-08 22:50:39Z john $ *}

{* INCLUDE HEADER CODE *}
{include file="header_global.tpl"}

{if $smarty.const.SE_DEBUG && $admin->admin_exists}{include file="header_debug.tpl"}{/if}

<div id="smoothbox_container"></div>

<script type="text/javascript" src="./include/js/simpletabs_1.3.js"></script>
<link rel="stylesheet" href="./templates/glassy.css" title="stylesheet" type="text/css"> 

{* INSERT USER'S CUSTOM CSS IF ANY *}
<style type='text/css'>{$global_css}</style>




{* BEGIN CENTERING TABLE *}

<table class="top-login" align="center">
	<tr>
    	<td align="left">

<span class="top-span">        
{literal}        
<script language="JavaScript">
  <!--
    var now = new Date();
    var days = new Array(
      'Sunday','Monday','Tuesday',
      'Wednesday','Thursday','Friday','Saturday');
    var months = new Array(
      'January','February','March','April','May',
      'June','July','August','September','October',
      'November','December');
    var date = ((now.getDate()<10) ? "0" : "")+ now.getDate();
    function fourdigits(number)	{
      return (number < 1000) ? number + 1900 : number;}
    today =  days[now.getDay()] + ", " +
       months[now.getMonth()] + " " +
       date + ", " +
       (fourdigits(now.getYear()));
     document.write(today);
  //-->
</script>
{/literal} 

</span> 

        
        </td>
    	<td align="right">

{* IF USER IS LOGGED IN, SHOW APPROPRIATE TOP MENU ITEMS *}
  {if $user->user_exists != 0}

      
      {* SHOW MY NOTIFICATIONS POPUP *}
      <div class='newupdates' id='newupdates' style='display: none;'>
        <div class='newupdates_content'>
            <a href='javascript:void(0);' class='newupdates' onClick="SocialEngine.Viewer.userNotifyPopup(); return false;">
            {assign var="notify_total" value=$notifys.total_grouped}
            {lang_sprintf id=1019 1="<span id='notify_total'>`$notify_total`</span>"}
            </a>
            &nbsp;&nbsp; 
            <a href='javascript:void(0);' class='newupdates' onClick="SocialEngine.Viewer.userNotifyHide(); return false;">X</a>
          </div>
      </div>
      
      <span class="top-span"> 
          {lang_sprintf id=649 1="<a href='user_home.php' class='top_menu_item'>`$user->user_displayname_short`</a>"}
          &nbsp;&nbsp;
          <a href='user_logout.php?token={$token}' class='top_menu_item'>{lang_print id=26}</a>
      </span>


  {* IF USER IS NOT LOGGED IN, SHOW APPROPRIATE TOP MENU ITEMS *}
  {else}
   <span class="top-span"> 
    
    <a href='login.php' class='top_menu_item'>{lang_print id=30}</a>
   </span>
  {/if}
  	
		</td>
    </tr>
</table>

<div id="body-wrapper">

<table cellpadding='0' cellspacing='0' class='body' align='center'>
<tr>
<td>



{* START TOPBAR *}
<div id="header">


  {* LOGO AND SEARCH *}
  <table cellpadding='0' cellspacing='0' style='width: 100%; padding-top: 10px;' align='center'>
  <tr>
  <td align='left'>
    <a href='./'><img src='./images/logo.png' border='0' alt='' /></a>
  </td>
  <td align='right' valign='bottom' style='padding-bottom: 10px;'>
    
    
  {* PAGE-TOP ADVERTISEMENT BANNER *}
  {if $ads->ad_top != ""}
    <div class='ad_top' style='display: block; visibility: visible;'>{$ads->ad_top}</div>
  {/if}
    
        
  </td>
  </tr>
  </table>

</div>
{* END TOP BAR *}






{* START TOP MENU *}
<table cellpadding='0' cellspacing='0' style='width: 100%;' align='center'>
<tr>
<td nowrap='nowrap' class='top_menu'>
  <div class='top_menu_link_container'>
    <div class='top_menu_link'>
      <a href='home.php' class='top_menu_item'>
        {lang_print id=645}
      </a>
    </div>
  </div>
  
  

  {* SHOW ANY PLUGIN MENU ITEMS *}
  {hook_foreach name=menu_main var=menu_main_args complete=menu_main_complete max=7}
    <div class='top_menu_link_container'>
      <div class='top_menu_link'>
        <a href='{$menu_main_args.file}' class='top_menu_item'>
          {lang_print id=$menu_main_args.title}
        </a>
      </div>
    </div>
  {/hook_foreach}
  
  {if !$menu_main_complete}
    <div class='top_menu_link_container top_menu_main_link_container'>
      <div class='top_menu_link top_menu_main_link'>
        <a href="javascript:void(0);" onclick="$('menu_main_dropdown').style.display = ( $('menu_main_dropdown').style.display=='none' ? 'inline' : 'none' ); this.blur(); return false;" class='top_menu_item'>
          {lang_print id=1316}
        </a>
      </div>
      <div class='menu_main_dropdown' id='menu_main_dropdown' style='display: none;'>
        <div>
          {* SHOW ANY PLUGIN MENU ITEMS *}
          {hook_foreach name=menu_main var=menu_main_args start=7}
          <div class='menu_main_item_dropdown'>
            <a href='{$menu_main_args.file}' class='menu_main_item' style="text-align: left;">
              {lang_print id=$menu_main_args.title}
            </a>
          </div>
          {/hook_foreach}
        </div>
      </div>
    </div>
  {/if}
  
  <div class='top_menu_link_container_end'>
    <div class='top_menu_link'>
      &nbsp;
    </div>
  </div>

</td>
<td nowrap='nowrap' align='right' class='top_menu2'>

	<form action='search.php' method='post'>
    <input type='text' name='search_text' class='text header-search' size='25' />
    <input type='submit' class='button header-search-button' value='{lang_print id=644}' />
    <input type='hidden' name='task' value='dosearch' />
    <input type='hidden' name='t' value='0' />
    </form>

</td>
</tr>
</table>
{* END TOP MENU *}



{* USER NOTIFICATIONS *}
{if $user->user_exists}
{lang_javascript ids=1198,1199}
<script type='text/javascript'>
<!--
var notify_update_interval;
window.addEvent('domready', function() {ldelim}
  SocialEngine.Viewer.userNotifyGenerate({$se_javascript->generateNotifys($notifys)});
  SocialEngine.Viewer.userNotifyShow();
  notify_update_interval = (function() {ldelim}
    if( notify_update_interval ) SocialEngine.Viewer.userNotifyUpdate();
  {rdelim}).periodical(60 * 1000);
{rdelim});
//-->
</script>
<div style='display: none;' id='newupdates_popup'></div>
{/if}
 

{* START USER MENU *}
{literal}
<script type='text/javascript'>
<!--
var open_menu;
var current_timeout = new Array();

function showMenu(id1)
{
  if($(id1))
  {
    if($(id1).style.display == 'none')
    {
      if($(open_menu)) { hideMenu($(open_menu)); }
      $(id1).style.display='inline';
      startMenuTimeout($(id1));
      $(id1).addEvent('mouseover', function(e) { killMenuTimeout(this); });
      $(id1).addEvent('mouseout', function(e) { startMenuTimeout(this); });
      open_menu = id1;
    }
  }
}

function killMenuTimeout(divEl)
{
  clearTimeout(current_timeout[divEl.id]);
  current_timeout[divEl.id] = '';
}

function startMenuTimeout(divEl)
{
  if(current_timeout[divEl.id] == '') {
    current_timeout[divEl.id] = setTimeout(function() { hideMenu(divEl); }, 1000);
  }
}

function hideMenu(divEl)
{
  divEl.style.display = 'none'; 
  current_timeout[divEl.id] = '';
  divEl.removeEvent('mouseover', function(e) { killMenuTimeout(this); });
  divEl.removeEvent('mouseout', function(e) { startMenuTimeout(this); });
}

function SwapOut(id1) {
  $(id1).src = Rollarrow1.src;
  return true;
}

function SwapBack(id1) {
  $(id1).src = Rollarrow0.src;
  return true;
}
//-->
</script>
{/literal}

{if $user->user_exists != 0}
  <table cellpadding='0' cellspacing='0' style='width: 100%;' align='center' class='menu_user_table'>
  <tr>
  <td class='menu_user'>
  
    {* SHOW WHATS NEW MENU ITEM *}
    <div class='menu_item'>
      <div style='float: left;'>
        <a href='user_home.php' class='menu_item'><img src='./images/icons/menu_home.gif' border='0' class='menu_icon' alt='' />{lang_print id=1161}</a>
        <img src='./images/icons/menu_arrow.gif' border='0' style='vertical-align: middle; cursor: pointer;' onMouseOver="showMenu('menu_dropdown_whatsnew');" alt='' />
        <div>
          <div class='menu_dropdown' id='menu_dropdown_whatsnew' style='display: none;'>
            <div>
              <div class='menu_item_dropdown'><a href='network.php' class='menu_item'><img src='./images/icons/mynetwork16.gif' border='0' class='menu_icon2' alt='' />{lang_print id=1162}</a></div>
            </div>
          </div>
        </div>
      </div>
      <div class='menu_sep'><img src='./images/icons/menu_sep.gif' border='0'></div>
    </div>
    
    {* SHOW PROFILE MENU ITEM *}
    <div class='menu_item'>
      <div style='float: left; background: none;'>
        <a href='{$url->url_create("profile", $user->user_info.user_username)}' class='menu_item'>
          <img src='./images/icons/profile16.gif' border='0' class='menu_icon' alt='' />
          {lang_print id=652}
        </a>
        <img src='./images/icons/menu_arrow.gif' border='0' style='vertical-align: middle; cursor: pointer;' onMouseOver="showMenu('menu_dropdown_profile');" alt='' />
        <div>
        <div class='menu_dropdown' id='menu_dropdown_profile' style='display: none;'>
          <div>
            <div class='menu_item_dropdown'><a href='user_editprofile.php' class='menu_item'><img src='./images/icons/profile_edit16.gif' border='0' class='menu_icon2'>{lang_print id=1163}</a></div>
              <div class='menu_item_dropdown'><a href='user_editprofile_photo.php' class='menu_item'><img src='./images/icons/profile_editphoto16.gif' border='0' class='menu_icon2' alt='' />{lang_print id=1164}</a></div>
              {if $user->level_info.level_profile_style != 0 || $user->level_info.level_profile_style_sample != 0}
              {/if}
            </div>
          </div>
        </div>
      </div>
      <div class='menu_sep'><img src='./images/icons/menu_sep.gif' border='0' alt='' /></div>
    </div>

    {* SHOW APPS MENU ITEM IF ENABLED *}
    {if $global_plugins.plugin_controls.show_menu_user}
      <div class='menu_item'>
        <div style='float: left; background: none;'>
          <a href="javascript:showMenu('menu_dropdown_apps');" onMouseUp="this.blur()" class='menu_item'>
            <img src='./images/icons/menu_apps.gif' border='0' class='menu_icon' alt='' />
            {lang_print id=1166}
          </a>
          <img src='./images/icons/menu_arrow.gif' border='0' style='vertical-align: middle; cursor: pointer;' onMouseOver="showMenu('menu_dropdown_apps');" alt='' />
          <div>
            <div class='menu_dropdown' id='menu_dropdown_apps' style='display: none;'>
              <div>
                {* SHOW ANY PLUGIN MENU ITEMS *}
                {hook_foreach name=menu_user_apps var=user_apps_args}
                <div class='menu_item_dropdown'>
                  <a href='{$user_apps_args.file}' class='menu_item'>
                    <img src='./images/icons/{$user_apps_args.icon}' border='0' class='menu_icon2' alt='' />
                    {lang_print id=$user_apps_args.title}
                  </a>
                </div>
                {/hook_foreach}
              </div>
            </div>
          </div>
        </div>
        <div class='menu_sep'><img src='./images/icons/menu_sep.gif' border='0' alt='' /></div>
      </div>
    {/if}

    {* SHOW MESSAGES MENU ITEM IF ENABLED *}
    {if $user->level_info.level_message_allow != 0}
      <div class='menu_item'>
        <div style='float: left; background: none;'>
          <a href='user_messages.php' class='menu_item'><img src='./images/icons/message_inbox16.gif' border='0' class='menu_icon' alt='' />{lang_print id=654}{if $user_unread_pms != 0} ({$user_unread_pms}){/if}</a>
          <img src='./images/icons/menu_arrow.gif' border='0' style='vertical-align: middle; cursor: pointer;' onMouseOver="showMenu('menu_dropdown_messages');" />
          <div>
            <div class='menu_dropdown' id='menu_dropdown_messages' style='display: none;'>
              <div>
                <div class='menu_item_dropdown'><a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?TB_iframe=true&height=400&width=450', '', './images/trans.gif');" class='menu_item'><img src='./images/icons/message_new16.gif' border='0' class='menu_icon2' alt='' />{lang_print id=1167}</a></div>
                <div class='menu_item_dropdown'><a href='user_messages.php' class='menu_item'><img src='./images/icons/message_inbox16.gif' border='0' class='menu_icon2' alt='' />{lang_print id=1168}</a></div>
                <div class='menu_item_dropdown'><a href='user_messages_outbox.php' class='menu_item'><img src='./images/icons/message_outbox16.gif' border='0' class='menu_icon2' alt='' />{lang_print id=1169}</a></div>
              </div>
            </div>
          </div>
        </div>
        <div class='menu_sep'><img src='./images/icons/menu_sep.gif' border='0' /></div>
      </div>
    {/if}
    
    {* SHOW FRIENDS MENU ITEM IF ENABLED *}
    {if $setting.setting_connection_allow != 0}
      <div class='menu_item'>
        <div style='float: left; background: none;'>
          <a href='user_friends.php' class='menu_item'><img src='./images/icons/friends16.gif' border='0' class='menu_icon' alt='' />{lang_print id=653}</a>
          <img src='./images/icons/menu_arrow.gif' border='0' style='vertical-align: middle; cursor: pointer;' onMouseOver="showMenu('menu_dropdown_friends');" alt='' />
          <div>
            <div class='menu_dropdown' id='menu_dropdown_friends' style='display: none;'>
              <div>
                <div class='menu_item_dropdown'><a href='user_friends.php' class='menu_item'><img src='./images/icons/friends16.gif' border='0' class='menu_icon2' alt='' />{lang_print id=1170}</a></div>
                <div class='menu_item_dropdown'><a href='user_friends_requests.php' class='menu_item'><img src='./images/icons/friends_incoming16.gif' border='0' class='menu_icon2' alt='' />{lang_print id=1171}</a></div>
                <div class='menu_item_dropdown'><a href='user_friends_requests_outgoing.php' class='menu_item'><img src='./images/icons/friends_outgoing16.gif' border='0' class='menu_icon2' alt='' />{lang_print id=1172}</a></div>
              </div>
            </div>
          </div>
        </div>
        <div class='menu_sep'><img src='./images/icons/menu_sep.gif' border='0' alt='' /></div>
      </div>
    {/if}
    
    {* SHOW SETTINGS MENU ITEM *}
    <div class='menu_item'>
      <div style='float: left; background: none;'>
        <a href='user_account.php' class='menu_item'><img src='./images/icons/settings16.gif' border='0' class='menu_icon' alt='' />{lang_print id=655}</a>
        <img src='./images/icons/menu_arrow.gif' border='0' style='vertical-align: middle; cursor: pointer;' onMouseOver="showMenu('menu_dropdown_settings');" alt='' />
        <div>
          <div class='menu_dropdown' id='menu_dropdown_settings' style='display: none;'>
            <div>
              <div class='menu_item_dropdown'><a href='user_account.php' class='menu_item'><img src='./images/icons/settings16.gif' border='0' class='menu_icon2' alt='' />{lang_print id=1173}</a></div>
              <div class='menu_item_dropdown'><a href='user_account_privacy.php' class='menu_item'><img src='./images/icons/settings_privacy16.gif' border='0' class='menu_icon2' alt='' />{lang_print id=1174}</a></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div style='clear: both; background: none; height: 0px;'></div>
    
  </td>
  </tr>
  </table>
{/if}
{* END USER MENU *}








<table cellpadding='0' cellspacing='0' align='center' style='width: 100%;'>
<tr>

{* SHOW LEFT-SIDE ADVERTISEMENT BANNER *}
{if $ads->ad_left != ""}
  <td valign='top'><div class='ad_left' style='display: block; visibility: visible;'>{$ads->ad_left}</div></td>
{/if}

<td valign='top'>

{* START MAIN LAYOUT *}
<div class='content'>
	<div style="padding: 10px;">

  {* SHOW BELOW-MENU ADVERTISEMENT BANNER *}
  {if $ads->ad_belowmenu != ""}<div class='ad_belowmenu' style='display: block; visibility: visible;'>{$ads->ad_belowmenu}</div>{/if}
