{include file='admin_header.tpl'}

{* $Id: admin_cache.tpl 238 2009-11-13 23:05:56Z john $ *}

<h2>{lang_print id=1243}</h2>
<div>{lang_print id=1245}</div>
<br />

{* RE-ADD LATER
<div class='box' style='width: 500px;margin-bottom: 15px;'>
  {lang_print id=1246}
  {lang_sprintf id=1247 1="javascript:void(0);" 2="TB_show('cache_config.php', 'admin_cache.php?task=dogenerate&TB_iframe=true&height=400&width=450', '', '../images/trans.gif');"}
</div>
*}

{if $result}
<div class='success'>
  <img src='../images/success.gif' class='icon' border='0' />
  {lang_print id=191}
</div>
{/if}


{if $is_error}
  <div class='error'>
    <img src='../images/error.gif' border='0' class='icon' />
    {if is_numeric($is_error)}{lang_print id=$is_error}{else}{$is_error}{/if}
  </div>
{/if}


{literal}
<script type="text/javascript">
  
  function addCachingServer(type)
  {
    var typeCapitalized = type.capitalize();
    
    if( !$('SECache' + type + 'ServersTemplate') ) return;
    if( !$('SECache' + type + 'ServersContainer') ) return;
    
    var serverCount = $('SECache' + type + 'ServersContainer').getElements('table').length + 1;
    var newServerTemplate = $('SECache' + type + 'ServersTemplate').clone();
    
    newServerTemplate.style.display = '';
    newServerTemplate.getElement('.SECacheMemcacheServerIndex').innerHTML += ' &nbsp; {/literal}{lang_print id=1248}{literal} '+serverCount;
    
    newServerTemplate.inject($('SECache' + type + 'ServersContainer'));
  }
  
  function removeCachingServer(tdobj)
  {
    $(tdobj).getParent('table').destroy();
  }
  
</script>
{/literal}


<form action='admin_cache.php' method='post'>

<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=1249}</td>
  </tr>
  
  <tr>
    <td class='setting1'><b>{lang_print id=1250}</b> {lang_print id=1251}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table>
        <tr>
          <td><input type="radio" id="setting_cache_enabled_1" name="setting_cache_enabled" value="1"{if empty($available_storage)} disabled{elseif !empty($setting.setting_cache_enabled)} checked{/if} /></td>
          <td><label for="setting_cache_enabled_1">{lang_print id=1252}</label></td>
        </tr>
        <tr>
          <td><input type="radio" id="setting_cache_enabled_0" name="setting_cache_enabled" value="0"{if empty($setting.setting_cache_enabled) || empty($available_storage)} checked{/if} /></td>
          <td><label for="setting_cache_enabled_0">{lang_print id=1253}</label></td>
        </tr>
      </table>
    </td>
  </tr>
  
  <tr>
    <td class='setting1'><b>{lang_print id=1254}</b>{lang_print id=1255}<br /><br /><i>{lang_print id=1256}</i></td>
  </tr>
  <tr>
    <td class='setting2'>
      <table>
        <tr>
          <td><input type="radio" id="setting_cache_default_file" name="setting_cache_default" value="file"{if !'file'|in_array:$available_storage} disabled{elseif $setting.setting_cache_default=="file"} checked{/if} /></td>
          <td><label for="setting_cache_default_file">{lang_print id=1257}</label></td>
        </tr>
        <tr>
          <td><input type="radio" id="setting_cache_default_memcache" name="setting_cache_default" value="memcache"{if !'memcache'|in_array:$available_storage} disabled{elseif $setting.setting_cache_default=="memcache"} checked{/if} /></td>
          <td><label for="setting_cache_default_memcache">{lang_print id=1258}</label></td>
        </tr>

        <tr>
          <td><input type="radio" id="setting_cache_default_apc" name="setting_cache_default" value="apc"{if !'apc'|in_array:$available_storage} disabled{elseif $setting.setting_cache_default=="apc"} checked{/if} /></td>
          <td><label for="setting_cache_default_apc">APC</label></td>
        </tr>
        <tr>
          <td><input type="radio" id="setting_cache_default_xcache" name="setting_cache_default" value="xcache"{if !'xcache'|in_array:$available_storage} disabled{elseif $setting.setting_cache_default=="xcache"} checked{/if} /></td>
          <td><label for="setting_cache_default_xcache">Xcache</label></td>
        </tr>

      </table>
    </td>
  </tr>
  
  <tr>
    <td class='setting1'><b>{lang_print id=1259}</b> {lang_print id=1260}<br /><br /><i>{lang_print id=1261}</i></td>
  </tr>
  
  <tr>
    <td class='setting2'>
      <table>
        <tr>
          <td><input class="text" type="text" size="5" maxlength="10" id="setting_cache_lifetime" name="setting_cache_lifetime" value="{$setting.setting_cache_lifetime|default:120}" /></td>
          <td><label for="setting_cache_lifetime">{lang_print id=1262}</label></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />




<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=1263}</td>
  </tr>
  
  <tr>
    <td class='setting1'>{lang_print id=1264}</td>
  </tr>
  
  {if 'file'|in_array:$available_storage}
  <tr>
    <td class='setting1' style="background: #F3FFF3; color: #00CC00;">{lang_print id=1265}</td>
  </tr>
  {else}
  <tr>
    <td class='setting2' style="background: #FFF3F3; color: #FF0000;">{lang_print id=1266}</td>
  </tr>
  {/if}
  
  <tr>
    <td class='setting1'><b>{lang_print id=1267}</b> {lang_print id=1268}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <input style="width:100%;" class="text" type="text" size="50" name="setting_cache_file_options[root]" value="{$cache_file_options.root|default:'./cache'}" />
    </td>
  </tr>
  
  <tr>
    <td class='setting1'><b>{lang_print id=1269}</b> {lang_print id=1270}</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td>
            <input type="checkbox" id="setting_cache_file_options_locking" name="setting_cache_file_options[locking]" value="1"{if !isset($cache_file_options.locking) || $cache_file_options.locking} checked{/if} />
          </td>
          <td><label for="setting_cache_file_options_locking">{lang_print id=1271}</label></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />




<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>{lang_print id=1272}</td>
  </tr>
  
  <tr>
    <td class='setting1'>{lang_print id=1273}</td>
  </tr>
  
  {if 'memcache'|in_array:$available_storage}
  <tr>
    <td class='setting2' style="background: #F3FFF3; color: #00FF00;">{lang_print id=1274}</td>
  </tr>
  {else}
  <tr>
    <td class='setting2' style="background: #FFF3F3; color: #FF0000;">{lang_print id=1275}</td>
  </tr>
  {/if}
  
  <tr>
    <td class='setting1'><b>{lang_print id=1276}</b> {lang_print id=1277}</td>
  </tr>
  
  <tr>
    <td class='setting2'>
      <table>
        <tr>
          <td><input type="checkbox" id="setting_cache_memcache_options_compression" name="setting_cache_memcache_options[compression]"{if $cache_memcache_options.compression} checked{/if} /></td>
          <td><label for="setting_cache_memcache_options_compression">{lang_print id=1278}</label></td>
        </tr>
      </table>
    </td>
  </tr>
  
  <tr>
    <td class='setting1'><b>{lang_print id=1279}</b> {lang_sprintf id=1280 1="javascript:void(0);" 2="addCachingServer('Memcache');"}</td>
  </tr>
  <tr>
    <td class='setting2' id="SECacheMemcacheServersContainer">
      {if is_array($cache_memcache_options) && is_array($cache_memcache_options.servers) && !empty($cache_memcache_options.servers)}
      {section name=memcache_server_loop loop=$cache_memcache_options.servers}
        <table>
          <tr>
            <td colspan="2" class="SECacheMemcacheServerIndex">
              <a href="javascript:void(0);" onclick="removeCachingServer(this);">x</a> &nbsp;
              {lang_print id=1248} {counter name="memcache_servers"}
            </td>
          </tr>
          <tr>
            <td>{lang_print id=1281}</td>
            <td><input class="text" type="text" name="setting_cache_memcache_options[server_hosts][]" value="{$cache_memcache_options.servers[memcache_server_loop].host}" /></td>
          </tr>
          <tr>
            <td>{lang_print id=1282}</td>
            <td><input class="text" type="text" name="setting_cache_memcache_options[server_ports][]" value="{$cache_memcache_options.servers[memcache_server_loop].port}" /></td>
          </tr>
        </table>
      {/section}
      {else}
        <table>
          <tr>
            <td colspan="2" class="SECacheMemcacheServerIndex">
              <a href="javascript:void(0);" onclick="removeCachingServer(this);">x</a>
              {lang_print id=1248} {counter name="memcache_servers"}
            </td>
          </tr>
          <tr>
            <td>{lang_print id=1281}</td>
            <td><input class="text" type="text" name="setting_cache_memcache_options[server_hosts][]" value="localhost" /></td>
          </tr>
          <tr>
            <td>{lang_print id=1282}</td>
            <td><input class="text" type="text" name="setting_cache_memcache_options[server_ports][]" value="11211" /></td>
          </tr>
        </table>
      {/if}
    </td>
  </tr>
</table>
<br />





<table cellpadding='0' cellspacing='0' width='600'>
  <tr>
    <td class='header'>Xcache Cache Settings</td>
  </tr>

  <tr>
    <td class='setting1'>The settings below are applicable if you have selected Xcache caching above.</td>
  </tr>

  {if 'xcache'|in_array:$available_storage}
  <tr>
    <td class='setting1' style="background: #F3FFF3; color: #00CC00;">Successfully initialized. The Memcache extension was detected.</td>
  </tr>
  {else}
  <tr>
    <td class='setting2' style="background: #FFF3F3; color: #FF0000;">Ensure you have the extension installed and that the proper username and password are filled out below.</td>
  </tr>
  {/if}

  <tr>
    <td class='setting1'><b>Auth Settings</b> Required to flush cache entries</td>
  </tr>
  <tr>
    <td class='setting2'>
      <table cellpadding='0' cellspacing='0'>
        <tr>
          <td>
            <label for="setting_cache_file_options_user">Username</label>&nbsp;
          </td>
          <td>
            <input class="text" type="text" id="setting_cache_xcache_options_user" name="setting_cache_xcache_options[user]" value="{$cache_xcache_options.user}" />
          </td>
        </tr>
        <tr>
          <td>
            <label for="setting_cache_file_options_password">Password</label>&nbsp;
          </td>
          <td>
            <input class="text" type="password" id="setting_cache_xcache_options_password" name="setting_cache_xcache_options[password]" value="{$cache_xcache_options.password}" />
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br />



<input type='submit' class='button' value='{lang_print id=173}' />
<input type='hidden' name='task' value='dosave' />

</form>
<br />




<table id="SECacheMemcacheServersTemplate" style="display:none;">
  <tr>
    <td colspan="2" class="SECacheMemcacheServerIndex">
      <a href="javascript:void(0);" onclick="removeCachingServer(this);">x</a>
    </td>
  </tr>
  <tr>
    <td>{lang_print id=1281}</td>
    <td><input class="text" type="text" name="setting_cache_memcache_options[server_hosts][]" value="localhost" /></td>
  </tr>
  <tr>
    <td>{lang_print id=1282}</td>
    <td><input class="text" type="text" name="setting_cache_memcache_options[server_ports][]" value="11211" /></td>
  </tr>
</table>

{include file='admin_footer.tpl'}