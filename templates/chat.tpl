{include file='header.tpl'}

{* $Id: chat.tpl 6 2009-01-11 06:01:29Z john $ *}

<table cellpadding='0' cellspacing='0' align='center'>
<tr>
<td align='center'>
  
  {if $user->user_exists && $user->level_info.level_chat_allow}
    
    {* OLD CHAT INTERFACE *}
    <iframe src='chat_frame.php?nocache={0|rand:100000}' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' style='border: 1px solid #AAAAAA; width: 600px; height: 500px;'></iframe>
    
  {else}
    
    Access denied
    
  {/if}
  
</td>
</tr>
</table>
<br />
<br />


{include file='footer.tpl'}


