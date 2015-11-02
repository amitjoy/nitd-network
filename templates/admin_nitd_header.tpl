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

  <div class='menu_section'>
  <div id='min1'><div class='menu_header' style='float:left;'>{lang_print id=2}</div><div class='menu_header' style='text-align: right;' id='min1_icon'>[ - ]</div></div>
  <div><img src='../images/admin_menu_bar.gif' border='0'></div>
  <div id='slideup1'>
  <div class='menu'><a href='admin_home.php' class='menu'><img src='../images/icons/admin_summary16.gif' border='0' class='icon2'>{lang_print id=3}</a></div>
  <div class='menu'><a href='admin_viewusers.php' class='menu'><img src='../images/icons/admin_users16.gif' border='0' class='icon2'>{lang_print id=4}</a></div>
  <div class='menu'><a href='admin_viewadmins.php' class='menu'><img src='../images/icons/admin_admins16.gif' border='0' class='icon2'>{lang_print id=5}</a></div>
  <div class='menu'><a href='admin_viewreports.php' class='menu'><img src='../images/icons/admin_reports16.gif' border='0' class='icon2'>{lang_print id=6}{if $total_reports != 0} ({$total_reports}){/if}</a></div>
  <div class='menu'><a href='admin_viewplugins.php' class='menu'><img src='../images/icons/admin_plugins16.gif' border='0' class='icon2'>{lang_print id=7}</a></div>
  <div class='menu'><a href='admin_levels.php' class='menu'><img src='../images/icons/admin_levels16.gif' border='0' class='icon2'>{lang_print id=8}</a></div>
  <div class='menu'><a href='admin_subnetworks.php' class='menu'><img src='../images/icons/admin_subnetworks16.gif' border='0' class='icon2'>{lang_print id=9}</a></div>
  <div class='menu'><a href='admin_ads.php' class='menu'><img src='../images/icons/admin_ads16.gif' border='0' class='icon2'>{lang_print id=10}</a></div>
  </div>
  </div>

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

  <div class='menu_section'>
  <div id='min2'><div class='menu_header' style='float:left;'>{lang_print id=11}</div><div class='menu_header' style='text-align: right;' id='min2_icon'>[ - ]</div></div>
  <div><img src='../images/admin_menu_bar.gif' border='0'></div>
  <div id='slideup2'>
  <div class='menu'><a href='admin_general.php' class='menu'><img src='../images/icons/admin_setting16.gif' border='0' class='icon2'>{lang_print id=12}</a></div>
  <div class='menu'><a href='admin_signup.php' class='menu'><img src='../images/icons/admin_signup16.gif' border='0' class='icon2'>{lang_print id=13}</a></div>
  <div class='menu'><a href='admin_activity.php' class='menu'><img src='../images/icons/admin_activity16.gif' border='0' class='icon2'>{lang_print id=14}</a></div>
  <div class='menu'><a href='admin_profile.php' class='menu'><img src='../images/icons/admin_profile16.gif' border='0' class='icon2'>{lang_print id=16}</a></div>
  <div class='menu'><a href='admin_banning.php' class='menu'><img src='../images/icons/admin_banning16.gif' border='0' class='icon2'>{lang_print id=17}</a></div>
  <div class='menu'><a href='admin_connections.php' class='menu'><img src='../images/icons/admin_connections16.gif' border='0' class='icon2'>{lang_print id=18}</a></div>
  <div class='menu'><a href='admin_emails.php' class='menu'><img src='../images/icons/admin_emails16.gif' border='0' class='icon2'>{lang_print id=20}</a></div>
  <div class='menu'><a href='admin_emails_config.php' class='menu'><img src='../images/icons/admin_emails16.gif' border='0' class='icon2'>Email Settings</a></div>
  <div class='menu'><a href='admin_cache.php' class='menu'><img src='../images/icons/admin_cache16.gif' border='0' class='icon2'>{lang_print id=1243}</a></div>
  <div class='menu'><a href='admin_session.php' class='menu'><img src='../images/icons/admin_session16.gif' border='0' class='icon2'>{lang_print id=1244}</a></div>
  </div>
  </div>

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
  {if $global_plugins|@count > 0}
    <div class='menu_section'>
    <div id='min5'><div class='menu_header' style='float:left;'>{lang_print id=1211}</div><div class='menu_header' style='text-align: right;' id='min5_icon'>[ - ]</div></div>
    <div><img src='../images/admin_menu_bar.gif' border='0'></div>
    <div id='slideup5'>
    {foreach from=$global_plugins key=plugin_k item=plugin_v}
      {if $plugin_v.plugin_pages_main|@count > 0}
        {section name=page_loop loop=$plugin_v.plugin_pages_main}
          <div class='menu'><a href='{$plugin_v.plugin_pages_main[page_loop].file}' class='menu'><img src='../images/icons/{$plugin_v.plugin_pages_main[page_loop].icon}' border='0' class='icon2'>{lang_print id=$plugin_v.plugin_pages_main[page_loop].title}</a></div>
        {/section}
      {/if}
    {/foreach}
    </div>
    </div>
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
  {/if}


  <div class='menu_section'>
  <div id='min3'><div class='menu_header' style='float:left;'>{lang_print id=48}</div><div class='menu_header' style='text-align: right;' id='min3_icon'>[ - ]</div></div>
  <div><img src='../images/admin_menu_bar.gif' border='0'></div>
  <div id='slideup3'>
  <div class='menu'><a href='admin_templates.php' class='menu'><img src='../images/icons/admin_templates16.gif' border='0' class='icon2'>{lang_print id=15}</a></div>
  <div class='menu'><a href='admin_language.php' class='menu'><img src='../images/icons/admin_language16.gif' border='0' class='icon2'>{lang_print id=49}</a></div>
  <div class='menu'><a href='admin_url.php' class='menu'><img src='../images/icons/admin_url16.gif' border='0' class='icon2'>{lang_print id=19}</a></div>
  </div>
  </div>

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

  <div class='menu_section'>
  <div id='min4'><div class='menu_header' style='float:left;'>{lang_print id=21}</div><div class='menu_header' style='text-align: right;' id='min4_icon'>[ - ]</div></div>
  <div><img src='../images/admin_menu_bar.gif' border='0'></div>
  <div id='slideup4'>
  <div class='menu'><a href='admin_faq.php' class='menu'><img src='../images/icons/admin_faq16.gif' border='0' class='icon2'>{lang_print id=935}</a></div>
  <div class='menu'><a href='admin_invite.php' class='menu'><img src='../images/icons/admin_invite16.gif' border='0' class='icon2'>{lang_print id=22}</a></div>
  <div class='menu'><a href='admin_announcements.php' class='menu'><img src='../images/icons/admin_announcements16.gif' border='0' class='icon2'>{lang_print id=23}</a></div>
  <div class='menu'><a href='admin_stats.php' class='menu'><img src='../images/icons/admin_stats16.gif' border='0' class='icon2'>{lang_print id=24}</a></div>
  <div class='menu'><a href='admin_log.php' class='menu'><img src='../images/icons/admin_log16.gif' border='0' class='icon2'>{lang_print id=25}</a></div>
  <div class='menu'><a href='admin_logout.php' class='menu'><img src='../images/icons/admin_logout16.gif' border='0' class='icon2'>{lang_print id=26}</a></div>
  </div>
  </div>

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

  



