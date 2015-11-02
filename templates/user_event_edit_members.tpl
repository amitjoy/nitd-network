{include file='header.tpl'}

{* $Id: user_event_edit_members.tpl 9 2009-01-11 06:03:21Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
  <tr>
    <td class='tab0'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_event_edit.php?event_id={$event->event_info.event_id}'>{lang_print id=3000137}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab1' NOWRAP><a href='user_event_edit_members.php?event_id={$event->event_info.event_id}'>{lang_print id=3000138}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_event_edit_settings.php?event_id={$event->event_info.event_id}'>{lang_print id=3000001}</a></td><td class='tab'>&nbsp;</td>
    <td class="tab3">&nbsp;</td>
  </tr>
</table>

<table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
    <td valign='top'>
      
      <img src='./images/icons/event_event48.gif' border='0' class='icon_big'>
      <div class='page_header'>{lang_sprintf id=3000141 1="event.php?event_id=`$event->event_info.event_id`" 2=$event->event_info.event_title}</div>
      <div style="width: 500px;">{lang_print id=3000142}</div>
      
    </td>
    <td valign='top' align='right'>
      
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td class='button' nowrap='nowrap'>
            <a href='user_event.php'><img src='./images/icons/back16.gif' border='0' class='button' />{lang_print id=3000109}</a>
          </td>
        </tr>
      </table>
      
    </td>
  </tr>
</table>
<br />


{* JAVASCRIPT *}
{lang_javascript ids=3000080,3000081,3000082,3000083,3000084,3000085,3000093,3000097,3000153,3000154,3000170,3000219,3000223,3000225,3000229}
<script type="text/javascript" src="./include/js/class_event.js"></script>
<script type="text/javascript">
  
  SocialEngine.Event = new SocialEngineAPI.Event({$event->event_generate_javascript_structure()});
  SocialEngine.RegisterModule(SocialEngine.Event);
  
</script>


{* HIDDEN DIV TO DISPLAY MEMBER DELETE CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmeventmemberdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=3000155}
  </div>
  <br />
  {lang_block id=175 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.SocialEngine.Event.memberDeleteConfirm();' />{/lang_block}
  {lang_block id=39 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();' />{/lang_block}
</div>


{* HIDDEN DIV TO DISPLAY MEMBER CANCEL CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmeventmembercancel'>
  <div style='margin-top: 10px;'>
    {lang_print id=3000224}
  </div>
  <br />
  {lang_block id=175 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.SocialEngine.Event.memberCancelConfirm();' />{/lang_block}
  {lang_block id=39 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();' />{/lang_block}
</div>


{* HIDDEN DIV TO DISPLAY MEMBER INVITE *}
<div style='display: none;' id='eventmemberinvite'>
  {* NO FRIENDS MESSAGE *}
  <div style='text-align:center;margin:10px;font-weight:bold;border: 1px dashed #CCCCCC;background: #FFFFFF;padding: 7px 8px 7px 7px;' id='noFriends'>
    <img src='./images/icons/bulb16.gif' class='icon'>
    {lang_print id=3000227}
    <br />
    <br />
    {lang_block id=39 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();' />{/lang_block}
  </div>
  
  {* INVITE DIALOG *}
  <div style='display:none;text-align:left;padding:10px;' id='inviteForm'>
    <div>{lang_print id=3000226}</div>
    <br />
    
    <div><a href='javascript:void(0);' id="eventMemberInviteSelectAll" onClick="var checkboxes = document.getElementsByTagName('input'); for( var i=0, l=checkboxes.length; i<l; i++ ) if( checkboxes[i].type=='checkbox' && parseInt(checkboxes[i].value)>0 ) {ldelim} checkboxes[i].checked = true; parent.SocialEngine.Event.memberInviteUpdate(checkboxes[i].value, true); {rdelim}">{lang_print id=3000228}</a></div>
    <div id='invite_friendlist' class='invite_friendlist'></div>
    
    <div style='margin-top: 20px;'>
      {lang_block id=3000225 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.SocialEngine.Event.memberInviteSend();' />{/lang_block}
      {lang_block id=39 var=langBlockTemp}<input type='button' class='button' value='{$langBlockTemp}' onClick='parent.TB_remove();' />{/lang_block}
    </div>
  </div>
  
  {* RESULT DIALOG *}
  <div style='display:none;text-align:left;padding:10px;' id='inviteResults'></div>
</div>




<table cellpadding="0" cellspacing="0" width="100%"><tr><td valign="top" width="270">
{* BEGIN LEFT COLUMN *}
  
  
  
  <div style="border: 1px solid rgb(187, 187, 187); padding: 10px; background: rgb(238, 238, 238) none repeat scroll 0% 0%; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;">
    
    <form name="event_members_form" action="user_event_edit_members.php?event_id={$event->event_info.event_id}" method="post">
    
    <table align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td style="font-weight: bold;" align="right">{lang_print id=643}&nbsp;</td>
        <td style="padding-left: 3px;">
          <table cellpadding="0" cellspacing="0">
            <tr>
              <td><input maxlength="100" name="search" class="event_search text" value="{$search}" type="text" />&nbsp;</td>
              <td>{lang_block id=646 var=langBlockTemp}<input class="button" value="{$langBlockTemp}" style="vertical-align: middle;" type="submit" />{/lang_block}</td>
            </tr>
          </table>
        </td>
      </tr>
      
      <tr>
        <td style="font-weight: bold;" align="right">{lang_print id=3000144}&nbsp;</td>
        <td style="padding: 3px;">
          <select name="v" class="event_small" onchange="document.event_members_form.submit();">
            <option value=""{if !isset($v)} selected{/if}>{lang_print id=3000143}</option>
            
            {if $event->event_info.event_inviteonly}<option value="-2"{if $v=="-2"} selected{/if}>{lang_print id=3000080}</option>{/if}
            <option value="-1"{if $v=="-1"} selected{/if}>{lang_print id=3000222}</option>
            
            <option value="0"{if $v=="0"} selected{/if}>{lang_print id=3000081}</option>
            <option value="1"{if $v=="1"} selected{/if}>{lang_print id=3000082}</option>
            <option value="2"{if $v=="2"} selected{/if}>{lang_print id=3000083}</option>
            <option value="3"{if $v=="3"} selected{/if}>{lang_print id=3000084}</option>
          </select>
        </td>
      </tr>
      
      <tr>
        <td style="font-weight: bold;" align="right">{lang_print id=900}&nbsp;</td>
        <td style="padding: 3px;">
          <select name="s" class="event_small" onchange="document.event_members_form.submit();">
            <option value=""{if !isset($v)} selected{/if}> </option>
            <option value="se_users.user_dateupdated DESC"{if $v=='se_users.user_dateupdated DESC'} selected{/if}>{lang_print id=901}</option>
            <option value="se_users.user_lastlogindate DESC"{if $v=='se_users.user_lastlogindate DESC'} selected{/if}>{lang_print id=902}</option>
          </select>
        </td>
      </tr>
    </table>
    
    <input name="p" value="1" type="hidden" />
    </form>
    
  </div>

  <div style="margin-top: 10px;">
    <table align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td>
          <a href="javascript:void(0)" onclick="SocialEngine.Event.memberInvitePopulate();">
            <img src="./images/icons/event_invite16.gif" class="button" border="0" />
            {lang_print id=3000145}
          </a>
        </td>
      </tr>
    </table>
  </div>
  
  
  
{* END LEFT COLUMN *}
</td><td style="padding-left: 10px;" valign="top">
{* BEGIN RIGHT COLUMN *}
  
  
  
  {if !$total_members}
  
  <table align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td class="result">
        <img src="./images/icons/bulb16.gif" class="icon" />
        {lang_print id=3000146}
      </td>
    </tr>
  </table>

  {else}
  
  {* PAGINATION *}
  <div class="event_pages_top">
    {if $p != 1}
      <a href='javascript:void(0);' onclick='document.event_members_form.p.value={math equation="p-1" p=$p};document.event_members_form.submit();'>&#171; {lang_print id=182}</a>
    {else}
      <font class='disabled'>&#171; {lang_print id=182}</font>
    {/if}
    {if $p_start == $p_end}
      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_members} &nbsp;|&nbsp; 
    {else}
      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_members} &nbsp;|&nbsp; 
    {/if}
    {if $p != $maxpage}
      <a href='javascript:void(0);' onclick='document.event_members_form.p.value={math equation="p+1" p=$p};document.event_members_form.submit();'>{lang_print id=183} &#187;</a>
    {else}
      <font class='disabled'>{lang_print id=183} &#187;</font>
    {/if}
  </div>
  
  
  
  {* MEMBER LIST *}
  {section name=member_loop loop=$members}
  {assign var=member_status value=$members[member_loop].eventmember_status}
  <div class="event_member">
    
    <table cellpadding="0" cellspacing="0">
      <tr>
        <td>
          <a href="{$url->url_create('profile', $members[member_loop].member->user_info.user_username)}">
            <img src="{$members[member_loop].member->user_photo('./images/nophoto.gif')}" class="photo" border="0" width="60" height="60" />
          </a>
        </td>
        <td style="padding-left: 7px; vertical-align: top;" width="100%">
          <div class="event_member_title">
            <img src="./images/icons/user16.gif" class="icon" border="0" />
            <a href="{$url->url_create('profile', $members[member_loop].member->user_info.user_username)}">{$members[member_loop].member->user_displayname}</a>
          </div>
          <div style="padding-top: 5px;">
            <div class="event_member_info">{lang_print id=3000147} {lang_print id=$event->event_rsvp_levels.$member_status}</div>
            {if $members[member_loop].member->user_info.user_dateupdated}
            {assign var="user_dateupdated" value=$datetime->time_since($members[member_loop].member->user_info.user_dateupdated)}
            <div class="event_member_info">{lang_print id=3000148} &nbsp;{lang_sprintf id=$user_dateupdated[0] 1=$user_dateupdated[1]}</div>
            {/if}
            {if $members[member_loop].member->user_info.user_lastlogindate}
            {assign var="user_lastlogindate" value=$datetime->time_since($members[member_loop].member->user_info.user_lastlogindate)}
            <div class="event_member_info">{lang_print id=906} &nbsp;{lang_sprintf id=$user_lastlogindate[0] 1=$user_lastlogindate[1]}</div>
            {/if}
          </div>
        </td>
        <td style="vertical-align: top;" nowrap="nowrap">
          <div>
            
            {* SELF *}
            {if $members[member_loop].member->user_info.user_id==$event->event_info.event_user_id}
              <div>{lang_print id=3000152}</div>
              
            {* IF MEMBER IS REQUESTING MEMBERSHIP *}
            {elseif !$members[member_loop].eventmember_approved && $members[member_loop].eventmember_status}
              <div><a href='javascript:void(0);' onclick='SocialEngine.Event.memberAccept({$members[member_loop].member->user_info.user_id});'>{lang_print id=3000149}</a></div>
              <div><a href='javascript:void(0);' onclick='SocialEngine.Event.memberReject({$members[member_loop].member->user_info.user_id});'>{lang_print id=3000150}</a></div>
              
            {* IF MEMBER WAS INVITED BY LEADER *}
            {elseif $members[member_loop].eventmember_approved && !$members[member_loop].eventmember_status}
              <div><a href='javascript:void(0);' onclick='SocialEngine.Event.memberCancel({$members[member_loop].member->user_info.user_id});'>Cancel Invite</a></div>
              
            {* NORMAL MEMBER *}
            {else}
              
              {* SHOW REMOVE MEMBER LINK *}
              <div><a href='javascript:void(0);' onclick='SocialEngine.Event.memberDelete({$members[member_loop].member->user_info.user_id});'>{lang_print id=3000151}</a></div>
              
              {* SHOW SEND MESSAGE LINK *}
              <div><a href='javascript:void(0);' onClick="TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$members[member_loop].member->user_displayname}&to_id={$members[member_loop].member->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');">{lang_print id=839}</a></div>
              
            {/if}
          </div>
        </td>
      </tr>
    </table>
  </div>
  {/section}
  
  
  
  {* PAGINATION *}
  <div class="event_pages_bottom">
    {if $p != 1}
      <a href='javascript:void(0);' onclick='document.event_members_form.p.value={math equation="p-1" p=$p};document.event_members_form.submit();'>&#171; {lang_print id=182}</a>
    {else}
      <font class='disabled'>&#171; {lang_print id=182}</font>
    {/if}
    {if $p_start == $p_end}
      &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_members} &nbsp;|&nbsp; 
    {else}
      &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_members} &nbsp;|&nbsp; 
    {/if}
    {if $p != $maxpage}
      <a href='javascript:void(0);' onclick='document.event_members_form.p.value={math equation="p+1" p=$p};document.event_members_form.submit();'>{lang_print id=183} &#187;</a>
    {else}
      <font class='disabled'>{lang_print id=183} &#187;</font>
    {/if}
  </div>
  
  {/if}
  
  
  
{* END RIGHT COLUMN *}
</td></tr></table>




{include file='footer.tpl'}