{include file='admin_header.tpl'}

{* $Id: admin_viewusers_edit.tpl 43 2009-01-29 21:50:31Z szerrade $ *}

{capture assign='user_showname'}{if $setting.setting_username}{$user->user_info.user_username}{else}{$user->user_displayname}{/if}{/capture}
<h2>{lang_sprintf id=1123 1=$user_showname}</h2>
{lang_print id=1124}
<br />
<br />

{if $is_error != 0}
  <div class='error'><img src='../images/error.gif' border='0' class='icon'> {lang_print id=$is_error}</div>
  <br>
{/if}

{if $result != 0}
  {capture assign="old_subnet_name"}{lang_print id=$old_subnet_name}{/capture}
  {capture assign="new_subnet_name"}{lang_print id=$new_subnet_name}{/capture}
  <div class='success'><img src='../images/success.gif' border='0' class='icon'> {lang_sprintf id=$result 1=$old_subnet_name 2=$new_subnet_name}</div>
  <br>
{/if}

<table cellpadding='0' cellspacing='0'>
<tr>
<td valign='top'>

<form action='admin_viewusers_edit.php' method='post'>
<table cellpadding='0' cellspacing='0'>
<tr>
<td class='form1'>{lang_print id=37}</td>
<td class='form2'>
  <input type='text' class='text' name='user_email' value='{$user->user_info.user_email}' size='30' maxlength='70'>
  {if $user->user_info.user_verified == 0} 
  <br>({lang_print id=1008} - <a href='admin_viewusers_edit.php?user_id={$user->user_info.user_id}&task=resend&s={$s}&p={$p}&f_user={$f_user}&f_email={$f_email}&f_level={$f_level}&f_subnet={$f_subnet}&f_enabled={$f_enabled}'>{lang_print id=1131}</a> - <a href='admin_viewusers_edit.php?user_id={$user->user_info.user_id}&task=verify&s={$s}&p={$p}&f_user={$f_user}&f_email={$f_email}&f_level={$f_level}&f_subnet={$f_subnet}&f_enabled={$f_enabled}'>{lang_print id=1132}</a>)
  {/if}
</td>
</tr>
{if $setting.setting_username}
  <tr>
  <td class='form1'>{lang_print id=1133}</td>
  <td class='form2'><input type='text' class='text' name='user_username' value='{$user->user_info.user_username}' size='30' maxlength='50'></td>
  </tr>
{/if}
<tr>
<td class='form1'>{lang_print id=1134}</td>
<td class='form2'><input type='password' class='text' name='user_password' value='' size='30' maxlength='50'><br>{lang_print id=1135}</td>
</tr>
<tr>
<td class='form1'>{lang_print id=1136}</td>
<td class='form2'><select class='text' name='user_enabled'><option value='1'{if $user->user_info.user_enabled == 1} SELECTED{/if}>{lang_print id=999}</option><option value='0'{if $user->user_info.user_enabled == 0} SELECTED{/if}>{lang_print id=1137}</option></td>
</tr>
<tr>
<td class='form1'>{lang_print id=1138}</td>
<td class='form2'><select class='text' name='user_level_id'>{section name=level_loop loop=$levels}<option value='{$levels[level_loop].level_id}'{if $user->user_info.user_level_id == $levels[level_loop].level_id} SELECTED{/if}>{$levels[level_loop].level_name}</option>{/section}</td>
</tr>
<tr>
<td class='form1'>{lang_print id=617}</td>
<td class='form2'><select class='text' name='user_profilecat_id'>{section name=cat_loop loop=$cats}<option value='{$cats[cat_loop].cat_id}'{if $user->user_info.user_profilecat_id == $cats[cat_loop].cat_id} SELECTED{/if}>{lang_print id=$cats[cat_loop].cat_title}</option>{/section}</td>
</tr>
<tr>
<td class='form1'>{lang_print id=1139}</td>
<td class='form2'><input type='text' class='text' name='user_invitesleft' value='{$user->user_info.user_invitesleft}' maxlength='3' size='2'></td>
</tr>
<tr>
<td class='form1'>&nbsp;</td>
<td class='form2'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td>
    <input type='submit' class='button' value='{lang_print id=173}'>&nbsp;
    <input type='hidden' name='task' value='edituser'>
    <input type='hidden' name='user_id' value='{$user->user_info.user_id}'>
    <input type='hidden' name='s' value='{$s}'>
    <input type='hidden' name='p' value='{$p}'>
    <input type='hidden' name='f_user' value='{$f_user}'>
    <input type='hidden' name='f_email' value='{$f_email}'>
    <input type='hidden' name='f_level' value='{$f_level}'>
    <input type='hidden' name='f_subnet' value='{$f_subnet}'>
    <input type='hidden' name='f_enabled' value='{$f_enabled}'>
    </form>
  </td>
  <td>
    <form action='admin_viewusers.php' method='GET'>
    <input type='submit' class='button' value='{lang_print id=39}'></td>
    <input type='hidden' name='s' value='{$s}'>
    <input type='hidden' name='p' value='{$p}'>
    <input type='hidden' name='f_user' value='{$f_user}'>
    <input type='hidden' name='f_email' value='{$f_email}'>
    <input type='hidden' name='f_level' value='{$f_level}'>
    <input type='hidden' name='f_subnet' value='{$f_subnet}'>
    <input type='hidden' name='f_enabled' value='{$f_enabled}'>
    </form>
  </td>
  </tr>
  </table>
</td>
</tr>
</table>

</td>
<td valign='top'>

<div class='smallbox' style='width: 250px; margin-left: 20px;'>
  <div class='smallbox_header'><b>{lang_print id=24}</b></div>
  <div class='smallbox_content'>
    <div>{$total_friends} {lang_print id=1125}</div>
    <div>{$user->user_info.user_logins} {lang_print id=1126}</div>
    <div>{$total_messages} {lang_print id=1127}</div>
    <div>{$total_comments} {lang_print id=1128}</div>
    <div>{lang_print id=1129} {if $user->user_info.user_lastlogindate == 0}{lang_print id=1130}{else}{assign var="user_lastlogin" value=$datetime->timezone($user->user_info.user_lastlogindate, $setting.setting_timezone)}{$datetime->cdate("`$setting.setting_dateformat`, `$setting.setting_timeformat`", $user_lastlogin)}{/if}</div>
    <div>{lang_print id=1144} {if $user->user_info.user_ip_signup == ""}---{else}{$user->user_info.user_ip_signup}{/if}</div>
    <div>{lang_print id=1145} {$user->user_info.user_ip_lastactive}</div>
  </div>
</div>

</td>
</tr>
</table>


<br>

{* SHOW RECENT ACTIVITY *}
{if $actions|@count > 0}
  {literal}
  <script language="JavaScript">
  <!-- 
    Rollimage0 = new Image(10,12);
    Rollimage0.src = "../images/icons/action_delete1.gif";
    Rollimage1 = new Image(10,12);
    Rollimage1.src = "../images/icons/action_delete2.gif";

    var total_actions = {/literal}{$actions|@count}{literal};
    function action_delete(action_id) {
      $('action_' + action_id).style.display = 'none';
      total_actions--;
      if(total_actions == 0)
        $('actions').style.display = "none";
    }
  //-->
  </script>
  {/literal}

  {* SHOW RECENT ACTIONS *}
  <div class='smallbox' style='width: 575px;' id='actions'>
    <div class='smallbox_header' style='margin-bottom: 7px;'><b>{lang_print id=851}</b></div>
    {section name=actions_loop loop=$actions}
      <div id='action_{$actions[actions_loop].action_id}' style='padding: 0px 5px 5px 5px; {if $smarty.section.actions_loop.last != true}border-bottom: 1px solid #EAEAEA; margin-bottom: 5px;{/if}'>
        <table cellpadding='0' cellspacing='0'>
          <tr>	
	  <td valign='top'><img src='../images/icons/{$actions[actions_loop].action_icon}' border='0' class='icon2'></td>
	  <td valign='top' width='100%'>
	    <div style='color: #999999; float: right; padding-left: 5px;'>
	      {assign var='action_date' value=$datetime->time_since($actions[actions_loop].action_date)}
	      {lang_sprintf id=$action_date[0] 1=$action_date[1]}
	      <img src='../images/icons/action_delete1.gif' style='vertical-align: middle; margin-left: 3px; cursor: pointer; cursor: hand;' border='0' onmouseover="this.src=Rollimage1.src;" onmouseout="this.src=Rollimage0.src;" onClick="javascript:$('ajaxframe').src='admin_viewusers_edit.php?task=action_delete&user_id={$user->user_info.user_id}&action_id={$actions[actions_loop].action_id}'">
	    </div>

	    {if $actions[actions_loop].action_media !== FALSE}{capture assign='action_media'}{section name=action_media_loop loop=$actions[actions_loop].action_media}<a href='../{$actions[actions_loop].action_media[action_media_loop].actionmedia_link}'><img src='../{$actions[actions_loop].action_media[action_media_loop].actionmedia_path}' border='0' width='{$actions[actions_loop].action_media[action_media_loop].actionmedia_width}'></a>{/section}{/capture}{/if}




	    {capture assign='action_text'}{lang_sprintf id=$actions[actions_loop].action_text 1=$actions[actions_loop].action_vars[0] 2=$actions[actions_loop].action_vars[1] 3=$actions[actions_loop].action_vars[2] 4=$actions[actions_loop].action_vars[3] 5=$actions[actions_loop].action_vars[4] 6=$actions[actions_loop].action_vars[5] 7=$actions[actions_loop].action_vars[6]}{/capture}
	    {$action_text|replace:"[media]":$action_media|choptext:50:"<br>"}
          </td>
	  </tr>
	</table>
      </div>
    {/section}

  </div>
{/if}
{* END RECENT ACTIVITY *}





{include file='admin_footer.tpl'}