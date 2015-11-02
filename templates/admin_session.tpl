{include file='admin_header.tpl'}

{* $Id: admin_session.tpl 238 2009-11-13 23:05:56Z john $ *}

<h2>{lang_print id=1244}</h2>
<div>{lang_print id=1299}</div>
<br />


{if $result}
  <div class='success'>
    <img src='../images/success.gif' class='icon' border='0' />
    {lang_print id=191}
  </div>
  <br>
{/if}

{if $is_error}
  <div class='error'>
    <img src='../images/error.gif' border='0' class='icon' />
    {if is_numeric($is_error)}{lang_print id=$is_error}{else}{$is_error}{/if}
  </div>
  <br>
{/if}


{literal}
<script type="text/javascript">
  
  function addSessionServer(type)
  {
    var typeCapitalized = type.capitalize();
    
    if( !$('SESession' + type + 'ServersTemplate') ) return;
    if( !$('SESession' + type + 'ServersContainer') ) return;
    
    var serverCount = $('SESession' + type + 'ServersContainer').getElements('table').length + 1;
    var newServerTemplate = $('SESession' + type + 'ServersTemplate').clone();
    
    newServerTemplate.style.display = '';
    newServerTemplate.getElement('.SESessionMemcacheServerIndex').innerHTML += ' &nbsp; {/literal}{lang_print id=1248}{literal} '+serverCount;
    
    newServerTemplate.inject($('SESession' + type + 'ServersContainer'));
  }
  
  function removeSessionServer(tdobj)
  {
    $(tdobj).getParent('table').destroy();
  }
  
</script>
{/literal}




<form action='admin_session.php' method='post'>

<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=1300}</td>
  </tr>
  
  <tr>
    <td class='setting1'>{lang_print id=1301}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='1' cellspacing='0'>
        <tr>
          <td><input type="radio" id="setting_session_storage_none" name="setting_session_options[storage]" value="none"{if empty($session_options.storage) || $session_options.storage=="none"} checked{/if} /></td>
          <td><label for="setting_session_storage_none">{lang_print id=1302}</label></td>
        </tr>
        <tr>
          <td><input type="radio" id="setting_session_storage_file" name="setting_session_options[storage]" value="file"{if !'file'|in_array:$available_storage} disabled{elseif $session_options.storage=="file"} checked{/if} /></td>
          <td><label for="setting_session_storage_file">{lang_print id=1257}</label></td>
        </tr>
        <tr>
          <td><input type="radio" id="setting_session_storage_memcache" name="setting_session_options[storage]" value="memcache"{if !'memcache'|in_array:$available_storage} disabled{elseif $session_options.storage=="memcache"} checked{/if} /></td>
          <td><label for="setting_session_storage_memcache">{lang_print id=1258}</label></td>
        </tr>
        <tr>
          <td><input type="radio" id="setting_session_storage_db" name="setting_session_options[storage]" value="db"{if !'db'|in_array:$available_storage} disabled{elseif $session_options.storage=="db"} checked{/if} /></td>
          <td><label for="setting_session_storage_db">Database</label></td>
        </tr>
      </table>
    </td>
  </tr>
  
  <tr>
    <td class='setting1'>{lang_print id=1303}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <input type="text" class="text" size="5" maxlength="6" name="setting_session_options[expire]" value="{$session_options.expire|default:900}" />
      <label>{lang_print id=1262}</label>
    </td>
  </tr>
  
</table>
<br />



<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=1304}</td>
  </tr>
  
  <tr>
    <td class='setting1'>{lang_print id=1305}</td>
  </tr>
  <tr>
    <td class='setting2'><input type="text" class="text" size="50" name="setting_session_options[root]" value="{$session_options.root|default:''}" /></td>
  </tr>
  
</table>
<br />



<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=1306}</td>
  </tr>
  
  <tr>
    <td class='setting1'>{lang_print id=1307} {lang_sprintf id=1280 1="javascript:void(0);" 2="addSessionServer('Memcache');"}</td>
  </tr>
  <tr>
    <td class='setting2' id="SESessionMemcacheServersContainer">
      {if is_array($session_options) && is_array($session_options.servers)}
      {section name=cache_server_loop loop=$session_options.servers}
        <table>
          <tr>
            <td colspan="2" class="SESessionMemcacheServerIndex">
              <a href="javascript:void(0);" onclick="removeSessionServer(this);">x</a> &nbsp;
              {lang_print id=1248} {counter name="memcache_servers"}
            </td>
          </tr>
          <tr>
            <td>{lang_print id=1281}</td>
            <td><input class="text" type="text" name="setting_session_options[server_hosts][]" value="{$session_options.servers[cache_server_loop].host}" /></td>
          </tr>
          <tr>
            <td>{lang_print id=1282}</td>
            <td><input class="text" type="text" name="setting_session_options[server_ports][]" value="{$session_options.servers[cache_server_loop].port}" /></td>
          </tr>
        </table>
      {/section}
      {/if}
    </td>
  </tr>
  
</table>
<br />




<input type='submit' class='button' value='{lang_print id=173}' />
<input type='hidden' name='task' value='dosave' />

</form>




<table id="SESessionMemcacheServersTemplate" style="display:none;">
  <tr>
    <td colspan="2" class="SESessionMemcacheServerIndex">
      <a href="javascript:void(0);" onclick="removeSessionServer(this);">x</a>
    </td>
  </tr>
  <tr>
    <td>{lang_print id=1281}</td>
    <td><input class="text" type="text" name="setting_session_options[server_hosts][]" value="localhost" /></td>
  </tr>
  <tr>
    <td>{lang_print id=1282}</td>
    <td><input class="text" type="text" name="setting_session_options[server_ports][]" value="11211" /></td>
  </tr>
</table>
  
{include file='admin_footer.tpl'}