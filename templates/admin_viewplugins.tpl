{include file='admin_header.tpl'}

{* $Id: admin_viewplugins.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=7}</h2>
{lang_print id=1107}
<br />
<br />

{if $plugins_ready|@count == 0 & $plugins_installed|@count == 0}
<table cellpadding='0' cellspacing='0'>
  <tr>
    <td class='result'>
      <img src='../images/icons/bulb16.gif' border='0' class='icon'>
      <b>{lang_print id=1108}</b>
    </td>
  </tr>
</table>
<br />
{/if}

{literal}
<script type="text/javascript">
  
  var pluginSortable;
  
  window.addEvent('load', function()
  {
    pluginSortable = new Sortables($('SEPluginsList'), {
      revert: { duration: 750, transition: 'elastic:out' },
      constrain: false,
      handle: 'table',
      clone: true,
      opacity: 0.7,
      onStart: function()
      {
        this.clone.setStyle('z-index', '10');
        this.clone.setStyle('margin-top', '100px');
        this.clone.setStyle('margin-left', '200px');
      },
      onComplete: function()
      {
        var orderedList = new Array();
        $('SEPluginsList').getElements('li').each(function(listElement)
        {
          var pluginType = listElement.id.replace('SEPluginList_', '');
          orderedList.push(pluginType);
        });;
        
        var request = new Request.JSON({
          'url' : 'admin_viewplugins.php',
          'method' : 'post',
          'data' : {
            'task' : 'doorder',
            'order' : orderedList
          },
          'onComplete' : function(responseJSON)
          {
            if( !responseJSON.result )
              alert('order failed');
          }
        });
        
        request.send();
      }
    });
  }); 
  
</script>
{/literal}

{* LIST READY-TO-BE-INSTALLED PLUGINS *}
{section name=ready_loop loop=$plugins_ready}
<table width='100%' cellpadding='0' cellspacing='0' class='stats' style='margin-bottom: 10px;'>
  <tr>
    <td class='plugin'>
      <table cellpadding='0' cellspacing='0' width="100%">
        <tr>
          <td width="20"><img src='../images/icons/{$plugins_ready[ready_loop].plugin_icon|default:"admin_plugins16.gif"}' border='0' class='icon2'></td>
          <td class='plugin_name'>{$plugins_ready[ready_loop].plugin_name} v{$plugins_ready[ready_loop].plugin_version}</td>
          {*<td align="right"><img src="../images/icons/bulb16.gif" /></td>*}
        </tr>
      </table>
      <div style='margin-top: 5px;'>{$plugins_ready[ready_loop].plugin_desc}</div>
      <div style='margin-top: 7px;'>
        <a href='admin_viewplugins.php?install={$plugins_ready[ready_loop].plugin_type}'>{lang_print id=1109}</a>
      </div>
    </td>
  </tr>
</table>
{/section}

{* LIST INSTALLED PLUGINS *}
<ul style="list-style:none; padding: 0; margin: 0;" id="SEPluginsList">

{section name=installed_loop loop=$plugins_installed}
<li id="SEPluginList_{$plugins_installed[installed_loop].plugin_type}">
<table width='100%' cellpadding='0' cellspacing='0' class='stats' style='margin-bottom: 10px;'>
  <tr>
    <td class='plugin'>
      <table cellpadding='0' cellspacing='0' width="100%">
        <tr>
          <td width="20"><img src='../images/icons/{$plugins_installed[installed_loop].plugin_icon|default:"admin_plugins16.gif"}' border='0' class='icon2'></td>
          <td class='plugin_name'>{$plugins_installed[installed_loop].plugin_name} v{$plugins_installed[installed_loop].plugin_version}</td>
          {*<td align="right"><img src="../images/icons/checkall16.gif" /></td>*}
        </tr>
      </table>
      <div style='margin-top: 5px;'>{$plugins_installed[installed_loop].plugin_desc}</div>
      {if $plugins_installed[installed_loop].plugin_version_ready != "" && $plugins_installed[installed_loop].plugin_version_ready <= $plugins_installed[installed_loop].plugin_version}
      <table width='100%' cellpadding='0' cellspacing='0' style='margin-top: 10px; margin-bottom: 3px;'>
        <tr>
          <td class='error'>
            <img src='../images/icons/error16.gif' border='0' class='icon'>
            {lang_sprintf id=1110 1=$plugins_installed[installed_loop].plugin_type}
          </td>
        </tr>
      </table>
      {/if}
      <div style='margin-top: 7px;'>
        {if $plugins_installed[installed_loop].plugin_version_ready > $plugins_installed[installed_loop].plugin_version}
          <a href='admin_viewplugins.php?install={$plugins_installed[installed_loop].plugin_type}'>{lang_print id=1111}</a> | 
        {elseif $plugins_installed[installed_loop].plugin_version_avail > $plugins_installed[installed_loop].plugin_version}
          <a href='http://www.socialengine.net/login.php' target='_blank'>{lang_print id=1112}</a> | 
        {/if}
        {if $plugins_installed[installed_loop].plugin_disabled}
          <a href='admin_viewplugins.php?enable={$plugins_installed[installed_loop].plugin_type}'>{lang_print id=1200}</a>
        {else}
          <a href='admin_viewplugins.php?disable={$plugins_installed[installed_loop].plugin_type}'>{lang_print id=1201}</a>
        {/if}
      </div>
    </td>
  </tr>
</table>
</li>
{/section}

</ul>

{include file='admin_footer.tpl'}