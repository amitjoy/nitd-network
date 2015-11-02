{include file='admin_header_global.tpl'}

{* $Id: admin_header.tpl 8 2009-01-11 06:02:53Z john $ *}

<div class='topbar'>
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td valign='top'><img src='../images/admin_icon.gif' border='0'></td>
  <td valign='top' align='right'><img src='../images/admin_watermark.gif' border='0'></td>
  </tr>
  </table>
</div>

<table cellpadding='0' cellspacing='0'>
<tr>
<td class='leftside'>
<div class='menu'><a href='admin_announcements.php' class='menu'><img src='../images/icons/admin_announcements16.gif' border='0' class='icon2'>{lang_print id=23}</a></div>
<div class='menu'><a href='admin_viewusers.php' class='menu'><img src='../images/icons/admin_users16.gif' border='0' class='icon2'>{lang_print id=4}</a></div>
<div class='menu'><a href='admin_forum.php' class='menu'><img src='../images/icons/admin_session16.gif' border='0' class='icon2'>{lang_print id=6000002}</a></div>
<div class='menu'><a href='admin_viewreports.php' class='menu'><img src='../images/icons/admin_reports16.gif' border='0' class='icon2'>{lang_print id=6}{if $total_reports != 0} ({$total_reports}){/if}</a></div>
  {literal}
  <script type="text/javascript">
  <!-- 
  window.addEvent('domready', function() { 
    var Slideup1 = new Fx.Slide('slideup1');
    if(menu_minimized.get(1) == 0) { $('min1_icon').innerHTML = '[ + ]'; Slideup1.hide(); }
    $('min1').addEvent('click', function(e){
	e = new Event(e);
	if(menu_minimized.get(1) == 0) { 
	  menu_minimized.set(1, 1);
	  Slideup1.slideIn(); 
	  $('min1_icon').innerHTML = '[ - ]';
	} else { 
	  menu_minimized.set(1, 0);
	  Slideup1.slideOut(); 
	  $('min1_icon').innerHTML = '[ + ]';
	}
	e.stop();
	this.blur();
    });
  });
  //-->
  </script>
  {/literal}


  {literal}
  <script type="text/javascript">
  <!-- 
  window.addEvent('domready', function()
  { 
    var Slideup2 = new Fx.Slide('slideup2');
    if(menu_minimized.get(2) == 0) { $('min2_icon').innerHTML = '[ + ]'; Slideup2.hide(); }
    $('min2').addEvent('click', function(e)
    {
      e = new Event(e);
      if(menu_minimized.get(2) == 0)
      { 
        menu_minimized.set(2, 1);
        Slideup2.slideIn(); 
        $('min2_icon').innerHTML = '[ - ]';
      }
      else
      { 
        menu_minimized.set(2, 0);
        Slideup2.slideOut(); 
        $('min2_icon').innerHTML = '[ + ]';
      }
      e.stop();
      this.blur();
    });
  });
  //-->
  </script>
  {/literal}


  {* DISPLAY PLUGIN SETTINGS *}

    {literal}
    <script type="text/javascript">
    <!-- 
    window.addEvent('domready', function()
    { 
      var Slideup5 = new Fx.Slide('slideup5');
      if(menu_minimized.get(5) == 0) { $('min5_icon').innerHTML = '[ + ]'; Slideup5.hide(); }
      $('min5').addEvent('click', function(e)
      {
        e = new Event(e);
        if(menu_minimized.get(5) == 0)
        { 
          menu_minimized.set(5, 1);
          Slideup5.slideIn(); 
          $('min5_icon').innerHTML = '[ - ]';
        }
        else
        { 
          menu_minimized.set(5, 0);
          Slideup5.slideOut(); 
          $('min5_icon').innerHTML = '[ + ]';
        }
        e.stop();
        this.blur();
      });
    });
    //-->
    </script>
    {/literal}


  

  {literal}
  <script type="text/javascript">
  <!-- 
  window.addEvent('domready', function()
  { 
    var Slideup3 = new Fx.Slide('slideup3');
    if(menu_minimized.get(3) == 0) { $('min3_icon').innerHTML = '[ + ]'; Slideup3.hide(); }
    $('min3').addEvent('click', function(e)
    {
      e = new Event(e);
      if(menu_minimized.get(3) == 0)
      { 
        menu_minimized.set(3, 1);
        Slideup3.slideIn(); 
        $('min3_icon').innerHTML = '[ - ]';
      }
      else
      { 
        menu_minimized.set(3, 0);
        Slideup3.slideOut(); 
        $('min3_icon').innerHTML = '[ + ]';
      }
      e.stop();
      this.blur();
    });
  });
  //-->
  </script>
  {/literal}
<div class='menu'><a href='admin_logout.php' class='menu'><img src='../images/icons/admin_logout16.gif' border='0' class='icon2'>{lang_print id=26}</a></div>


  {literal}
  <script type="text/javascript">
  <!-- 
  window.addEvent('domready', function()
  { 
    var Slideup4 = new Fx.Slide('slideup4');
    if(menu_minimized.get(4) == 0) { $('min4_icon').innerHTML = '[ + ]'; Slideup4.hide(); }
    $('min4').addEvent('click', function(e)
    {
      e = new Event(e);
      if(menu_minimized.get(4) == 0)
      { 
        menu_minimized.set(4, 1);
        Slideup4.slideIn(); 
        $('min4_icon').innerHTML = '[ - ]';
      }
      else
      { 
        menu_minimized.set(4, 0);
        Slideup4.slideOut(); 
        $('min4_icon').innerHTML = '[ + ]';
      }
      e.stop();
      this.blur();
    });
  });
  //-->
  </script>
  {/literal}

</td>
<td class='rightside'>

  



