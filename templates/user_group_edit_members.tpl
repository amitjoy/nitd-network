{include file='header.tpl'}

{* $Id: user_group_edit_members.tpl 34 2009-01-24 04:17:28Z john $ *}

<table class='tabs' cellpadding='0' cellspacing='0'>
  <tr>
    <td class='tab0'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_group_edit.php?group_id={$group->group_info.group_id}'>{lang_print id=2000097}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab1' NOWRAP><a href='user_group_edit_members.php?group_id={$group->group_info.group_id}'>{lang_print id=2000118}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab2' NOWRAP><a href='user_group_edit_settings.php?group_id={$group->group_info.group_id}'>{lang_print id=2000119}</a></td><td class='tab'>&nbsp;</td>
    <td class='tab3'>&nbsp;</td>
  </tr>
</table>

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td valign='top'>
  <img src='./images/icons/group_group48.gif' border='0' class='icon_big' />
  {capture assign="linked_groupname"}<a href='{$url->url_create("group", $smarty.const.NULL, $group->group_info.group_id)}'>{$group->group_info.group_title|truncate:30:"...":true}</a>{/capture}
  <div class='page_header'>{lang_sprintf id=2000101 1=$linked_groupname}</div>
  {lang_print id=2000102}
</td>
<td valign='top' align='right'>
  <table cellpadding='0' cellspacing='0'>
  <tr><td class='button'><a href='user_group.php'><img src='./images/icons/back16.gif' border='0' class='button' />{lang_print id=2000120}</a></td></tr>
  </table>
</td>
</tr>
</table>

<br />

<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td valign='top' width='270'>

  <div style='padding-right: 10px; padding: 10px; background: #EEEEEE; border: 1px solid #BBBBBB;'>
    <form action='user_group_edit_members.php' method='post'>
    <table cellpadding='0' cellspacing='0' align='center'>
    <tr>
    <td align='right' style='font-weight: bold;'>{lang_print id=643}&nbsp;</td>
    <td style='padding-left: 3px;'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td><input type='text' maxlength='100' name='search' class='group_search text' value='{$search}'><input type='hidden' name='p' value='{$p}'><input type='hidden' name='s' value='{$s}'><input type='hidden' name='v' value='{$v}'>&nbsp;</td>
      <td><input type='submit' class='button' value='{lang_print id=646}' style='vertical-align: middle;'><input type='hidden' name='group_id' value='{$group->group_info.group_id}'></td>
      </tr>
      </table>
    </td>
    </tr>
    <tr>
    <td align='right' style='font-weight: bold;'>{lang_print id=2000171}&nbsp;</td>
    <td style='padding: 3px;'>
      <select name='v' class='group_small' onChange="window.location.href='user_group_edit_members.php?group_id={$group->group_info.group_id}&search={$search}&s={$s}&v='+this.options[this.selectedIndex].value;">
      {if $group->group_info.group_approval}<option value='3'{if $v == 3} selected='selected'{/if}>{lang_print id=2000175}</option>{/if}
      <option value='2'{if $v == 2} selected='selected'{/if}>{lang_print id=2000103}</option>
      <option value='0'{if $v == 0} selected='selected'{/if}>{lang_print id=2000169}</option>
      <option value='1'{if $v == 1} selected='selected'{/if}>{lang_print id=2000170}</option>
      </select>
    </td>
    </tr>
    <tr>
    <td align='right' style='font-weight: bold;'>{lang_print id=900}&nbsp;</td>
    <td style='padding: 3px;'>
      <select name='s' class='group_small' onChange="window.location.href='user_group_edit_members.php?group_id={$group->group_info.group_id}&search={$search}&v={$v}&s='+this.options[this.selectedIndex].value;">
      <option value='{$u}'{if $s == "ud"} selected='selected'{/if}>{lang_print id=901}</option>
      <option value='{$l}'{if $s == "ld"} selected='selected'{/if}>{lang_print id=902}</option>
      <option value='{$t}'{if $s == "t"} selected='selected'{/if}>{lang_print id=2000172}</option>
      <option value='{$r}'{if $s == "r"} selected='selected'{/if}>{lang_print id=2000173}</option>
      </select>
    </td>
    </tr>
    </table>
    </form>
  </div>

  <div style='margin-top: 10px;'>
    <table cellpadding='0' cellspacing='0' align='center'>
    <tr>
    <td>
      <a href='javascript:void(0)' onClick="TB_show('{lang_print id=2000174}', 'user_group_invite.php?group_id={$group->group_info.group_id}&TB_iframe=true&height=300&width=450', '', '../images/trans.gif');"><img src='./images/icons/group_invite16.gif' border='0' class='button'>{lang_print id=2000174}</a>
    </td>
    </tr>
    </table>
  </div>

</td>
<td valign='top' style='padding-left: 10px;'>

  {* SHOW MESSAGE IF NO USERS *}
  {if $total_members == 0}

    <table cellpadding='0' cellspacing='0' align='center'>
    <tr>
    <td class='result'>
      <img src='./images/icons/bulb16.gif' class='icon'>
      {lang_print id=2000176}
    </td>
    </tr>
    </table>


  {else}

    {* DISPLAY PAGINATION MENU IF APPLICABLE *}
    <div class='group_pages_top'>
      {if $p != 1}<a href='user_group_edit_members.php?group_id={$group->group_info.group_id}&s={$s}&v={$v}&search={$search}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_members} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_members} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}<a href='user_group_edit_members.php?group_id={$group->group_info.group_id}&s={$s}&v={$v}&search={$search}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </div>
 
    {section name=member_loop loop=$members}
      <div class='group_member'>
      <table cellpadding='0' cellspacing='0'>
      <tr>
      <td><a href='{$url->url_create('profile', $members[member_loop].member->user_info.user_username)}'><img src='{$members[member_loop].member->user_photo('./images/nophoto.gif', TRUE)}' class='photo' width='60' height='60' border='0'></a></td>
      <td style='padding-left: 7px; vertical-align: top;' width='100%'>
        <div class='group_member_title'>
          <img src='./images/icons/user16.gif' border='0' class='icon'><a href='{$url->url_create('profile', $members[member_loop].member->user_info.user_username)}'>{$members[member_loop].member->user_displayname}</a>
        </div>
        <div style='padding-top: 5px;'>
	  {if $members[member_loop].groupmember_approved == 1 && $members[member_loop].groupmember_status == 1}
            {capture assign='member_rank'}{if $members[member_loop].groupmember_rank == 2}{lang_print id=2000179}{elseif $members[member_loop].groupmember_rank == 1 && $group->groupowner_level_info.level_group_officers == 1}{lang_print id=2000180}{else}{lang_print id=2000181}{/if}{/capture}
	    <div class='group_member_info'>{if $members[member_loop].groupmember_title != "" && $group->groupowner_level_info.level_group_titles == 1}{lang_sprintf id=2000182 1=$member_rank 2=$members[member_loop].groupmember_title}{else}{lang_sprintf id=2000178 1=$member_rank}{/if}</div>
	  {/if}
	  {if $members[member_loop].member->user_info.user_dateupdated != "0"}
            <div class='group_member_info'>{lang_print id=2000183} &nbsp;{assign var="user_dateupdated" value=$datetime->time_since($members[member_loop].member->user_info.user_dateupdated)}{lang_sprintf id=$user_dateupdated[0] 1=$user_dateupdated[1]}</div>
          {/if}
	  {if $members[member_loop].member->user_info.user_lastlogindate != "0"}
            <div class='group_member_info'>{lang_print id=906} &nbsp;{assign var="user_lastlogindate" value=$datetime->time_since($members[member_loop].member->user_info.user_lastlogindate)}{lang_sprintf id=$user_lastlogindate[0] 1=$user_lastlogindate[1]}</div>
          {/if}
        </div>
      </td>
      <td nowrap='nowrap' style='vertical-align: top;'>
        {* IF MEMBER IS REQUESTING MEMBERSHIP *}
	{if $members[member_loop].groupmember_approved == 0}
          <div><a href='user_group_edit_members.php?task=approve&group_id={$group->group_info.group_id}&groupmember_id={$members[member_loop].groupmember_id}&s={$s}&v={$v}&search={$search}&p={$p}'>{lang_print id=2000188}</a></div>
          <div><a href='user_group_edit_members.php?task=reject&group_id={$group->group_info.group_id}&groupmember_id={$members[member_loop].groupmember_id}&s={$s}&v={$v}&search={$search}&p={$p}'>{lang_print id=2000189}</a></div>



        {* IF MEMBER HAS BEEN INVITED *}
	{elseif $members[member_loop].groupmember_approved == 1 && $members[member_loop].groupmember_status == 0}
          <div><a href='user_group_edit_members.php?task=cancel&group_id={$group->group_info.group_id}&groupmember_id={$members[member_loop].groupmember_id}&s={$s}&v={$v}&search={$search}&p={$p}'>{lang_print id=2000190}</a></div>


        {* GENERIC MEMBER *}
        {else}
          {* IF MEMBER HAS A LOWER RANK OR USER IS OWNER, SHOW EDIT AND REMOVE LINKS *}
          {if $group->user_rank > $members[member_loop].groupmember_rank || $group->user_rank == 2}
            <div><a href='javascript:void(0)' onClick="TB_show('{lang_print id=2000184}', 'user_group_edit_member.php?group_id={$group->group_info.group_id}&groupmember_id={$members[member_loop].groupmember_id}&TB_iframe=true&height=200&width=350', '', '../images/trans.gif');">{lang_print id=2000184}</a></div>
            {if $members[member_loop].member->user_info.user_id != $user->user_info.user_id}
              <div><a href='javascript:void(0);' onClick="confirmDelete('{$members[member_loop].groupmember_id}');">{lang_print id=2000185}</a></div>
            {/if}
          {/if}
          {* SHOW SEND MESSAGE LINK IF USER IS NOT LOOKING AT HIMSELF *}
          {if $members[member_loop].member->user_info.user_id != $user->user_info.user_id}
            <div><a href="javascript:TB_show('{lang_print id=784}', 'user_messages_new.php?to_user={$members[member_loop].member->user_displayname}&to_id={$members[member_loop].member->user_info.user_username}&TB_iframe=true&height=400&width=450', '', './images/trans.gif');">{lang_print id=839}</a></div>
          {/if}
        {/if}
      </td>
      </tr>
      </table>
      </div>
    {/section}
 
    {* DISPLAY PAGINATION MENU IF APPLICABLE *}
    <div class='group_pages_bottom' style='margin-top: 10px;'>
      {if $p != 1}<a href='user_group_edit_members.php?group_id={$group->group_info.group_id}&s={$s}&v={$v}&search={$search}&p={math equation='p-1' p=$p}'>&#171; {lang_print id=182}</a>{else}<font class='disabled'>&#171; {lang_print id=182}</font>{/if}
      {if $p_start == $p_end}
        &nbsp;|&nbsp; {lang_sprintf id=184 1=$p_start 2=$total_members} &nbsp;|&nbsp; 
      {else}
        &nbsp;|&nbsp; {lang_sprintf id=185 1=$p_start 2=$p_end 3=$total_members} &nbsp;|&nbsp; 
      {/if}
      {if $p != $maxpage}<a href='user_group_edit_members.php?group_id={$group->group_info.group_id}&s={$s}&v={$v}&search={$search}&p={math equation='p+1' p=$p}'>{lang_print id=183} &#187;</a>{else}<font class='disabled'>{lang_print id=183} &#187;</font>{/if}
    </div>

    {* JAVASCRIPT FOR CONFIRMING DELETION *}
    {literal}
    <script type="text/javascript">
    <!-- 
    var groupmember_id = 0;
    function confirmDelete(id) {
      groupmember_id = id;
      TB_show('{/literal}{lang_print id=2000185}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
    }

    function removeUser() {
      {/literal}window.location = 'user_group_edit_members.php?task=remove&group_id={$group->group_info.group_id}&s={$s}&v={$v}&search={$search}&p={$p}&groupmember_id='+groupmember_id;{literal}
    }
    //-->
    </script>
    {/literal}

    {* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
    <div style='display: none;' id='confirmdelete'>
      <div style='margin-top: 10px;'>{lang_print id=2000191}</div>
      <br>
      <input type='button' class='button' value='{lang_print id=2000185}' onClick='parent.TB_remove();parent.removeUser();'>
      <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
    </div>

  {/if}

{include file='footer.tpl'}