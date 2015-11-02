
{literal}
<script type='text/javascript'>

var lastDebugPosTop = Cookie.read('seLastDebugPosTop');
var lastDebugPosLeft = Cookie.read('seLastDebugPosLeft');

window.addEvent('load', function()
{
  var request = new Request({
    url: 'misc_js.php',
    method: 'post',
    data: {
      task: 'get_debug_info',
      id: '{/literal}{$debug_uid}{literal}'
    },
    onComplete: function(data)
    {
      $('se_debug_window_body_container').innerHTML = data;
      
      var DebugDrag = new Drag.Move('se_debug_window', {
        handle: $('se_debug_window_menu'),
        onComplete: function(el)
        {
          Cookie.write('seLastDebugPosTop', lastDebugPosTop = el.getCoordinates().top);
          Cookie.write('seLastDebugPosLeft', lastDebugPosLeft = el.getCoordinates().left);
        }
      });
      
      $('se_debug_window').style.display = '';
      
      if( $type(lastDebugPosTop) )
        $('se_debug_window').style.top = lastDebugPosTop+'px';
      if( $type(lastDebugPosLeft) )
        $('se_debug_window').style.left = lastDebugPosLeft+'px';
      
    }
  }).send();
});

var se_debug_visible_tab = 'summary';
function loadDebugTab(tabId)
{
  if(tabId == se_debug_visible_tab)
  {
    return false;
  }
  if($('se_debug_'+tabId))
  {
    $('se_debug_tabs_'+tabId).className='profile_tab2';
    $('se_debug_'+tabId).style.display = "block";
    if($('se_debug_tabs_'+se_debug_visible_tab))
    {
      $('se_debug_tabs_'+se_debug_visible_tab).className='profile_tab';
      $('se_debug_'+se_debug_visible_tab).style.display = "none";
    }
    se_debug_visible_tab = tabId;
  }
}

</script>
{/literal}

<table cellpadding='0' cellspacing='0' id="se_debug_window" style="display:none;">
  <tr>
    <td id="se_debug_window_menu" height="1">
      Debug
    </td>
  </tr>
  <tr>
    <td id="se_debug_window_body">
      <div id="se_debug_window_body_container"></div>
    </td>
  </tr>
</table>
