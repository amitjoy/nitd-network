
{* $Id: header_chat.tpl 6 2009-01-11 06:01:29Z john $ *}

{if !$global_smoothbox && $user->user_exists}

  {literal}
  <script type="text/javascript">
    var use_seIM = {/literal}{if $user->level_info.level_im_allow && $user->level_info.level_im_allow}1{else}0{/if}{literal};
    var use_seChat = {/literal}{if $global_page=="chat" && $setting.setting_chat_allow && $user->level_info.level_chat_allow}1{else}0{/if}{literal};
  </script>
  {/literal}
  
  {* ASSIGN MENU VARIABLES *}
  {if $setting.setting_chat_enabled}
    {capture assign=chat_menu_title}{lang_print id=3500025}{/capture}
    {array var="chat_menu" value="chat.php"}
    {array var="chat_menu" value="chat_chat16.gif"}
    {array var="chat_menu" value=$chat_menu_title}
    {array var="global_plugin_menu" value=$chat_menu} 
  {/if}
  
  {* LOAD IM SCRIPTS *}
  {if $user->level_info.level_im_allow}
    <link rel="stylesheet" href="./templates/styles_im.css" title="stylesheet" type="text/css" />
    <script type="text/javascript" src="./include/js/seIM/InstantMessengerUtilities.js"></script>
    <script type="text/javascript" src="./include/js/seIM/InstantMessengerConversations.js"></script>
    <script type="text/javascript" src="./include/js/seIM/InstantMessengerGUI.js"></script>
    <script type="text/javascript" src="./include/js/seIM/InstantMessengerCore.js"></script>
  {/if}
  
{/if}