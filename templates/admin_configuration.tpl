{include file='admin_header.tpl'}

{* $Id: admin_configuration.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>Configuration File Generator</h2>
<p>
  Caching takes some load off the database server by storing commonly selected
  data in temporary files (file-based caching) or memory (memcached).
</p>

{if $is_error}
  <div class='error'>
    <img src='../images/error.gif' border='0' class='icon' />
    {if is_numeric($is_error)}{lang_print id=$is_error}{else}{$is_error}{/if}
  </div>
{/if}


{if empty($type)}

  {literal}
  <script type="text/javascript">
    
    var FxSlides = {};
    var isFirst = true;
    
    function getSlideObject(id)
    {
      if( FxSlides[id] )
      {
        return FxSlides[id];
      }
      else
      {
        var slide = new Fx.Slide(id, {transition: Fx.Transitions.Bounce.easeOut, duration: 1000});
        FxSlides[id] = slide;
        return slide;
      }
    }
    
    function changeConfigMode(type)
    {
      var typeID = 'SEConfiguration'+type.capitalize();
      var mainSlide = getSlideObject(typeID);
      var isChained = false;
      
      if( !isFirst )
      {
        $$('.SEConfiguration').each(function(configContainer)
        {
          var thisSlide = getSlideObject(configContainer.id);
          //alert(configContainer.id + ' ' + typeID);
          if( configContainer.id!=typeID )
          {
            if( !isChained )
            {
              isChained = true;
              thisSlide.show();
              thisSlide.chain(function()
              {
                mainSlide.hide().slideIn();
              });
            }
            thisSlide.slideOut();
          }
          
        });
      }
      else
      {
        isFirst = false;
        mainSlide.hide().slideIn();
      }
    }
    
    window.addEvent('load', function()
    {
      $$('.SEConfiguration').each(function(configContainer)
      {
        var thisSlide = getSlideObject(configContainer.id);
        configContainer.style.display = '';
        thisSlide.hide();
      });
    });
    
  </script>
  {/literal}
  
  <form action='admin_configuration.php' method='post'>

  <table cellpadding='0' cellspacing='0' width='600'>
    <tr>
      <td class='header'>Configuration File Options</td>
    </tr>
    <tr>
      <td class='setting1'>
        Please select a type of file to generate.
      </td>
    </tr>
    <tr>
      <td class='setting2'>
        <table>
          <tr>
            <td><input type="radio" id="config_type_main" name="type" value="main" onchange="changeConfigMode(this.value);" /></td>
            <td><label for="config_type_main">Main (database_config.php)</label></td>
          </tr>
          <tr>
            <td><input type="radio" id="config_type_htaccess" name="type" value="htaccess" onchange="changeConfigMode(this.value);" /></td>
            <td><label for="config_type_htaccess">Htaccess (.htaccess)</label></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  
  <table cellpadding='0' cellspacing='0' width='600' id="SEConfigurationMain" class="SEConfiguration SEConfigurationMain" style="display: none;">
    <tr>
      <td class='setting1'>
        Blah Blah
      </td>
    </tr>
    <tr>
      <td class='setting2'>
      </td>
    </tr>
  </table>
  
  <table cellpadding='0' cellspacing='0' width='600' id="SEConfigurationHtaccess" class="SEConfiguration SEConfigurationHtaccess" style="display: none;">
    <tr>
      <td class='setting1'>
        Blah Blah
      </td>
    </tr>
    <tr>
      <td class='setting2'>
      </td>
    </tr>
  </table>
  <br />

  <input type='submit' class='button' value='{lang_print id=173}' />
  <input type='hidden' name='task' value='download' />

  </form>

{elseif $type=="main"}


{elseif $type=="htaccess"}


{/if}

{include file='admin_footer.tpl'}