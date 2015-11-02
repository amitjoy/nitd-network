{include file='admin_header.tpl'}

{* $Id: admin_ads_modify.tpl 8 2009-01-11 06:02:53Z john $ *}

{if $ad_exists == 1}
  <h2>{lang_print id=408}</h2>
  {lang_print id=409}
{else}
  <h2>{lang_print id=344}</h2>
  {lang_print id=345}
{/if}

<br><br>

{if $is_error != 0}
  <div class='error'><img src='../images/error.gif' border='0' class='icon'> {lang_print id=$is_error}</div><br>
{/if}

{literal}
<script type="text/javascript">
<!--

  Ads = new Array()
  Ads['top1'] = new Image(213,37);
  Ads['top1'].src = "../images/admin_ads_top.gif";
  Ads['top2'] = new Image(213,37);
  Ads['top2'].src = "../images/admin_ads_top2.gif";
  Ads['top3'] = new Image(213,37);
  Ads['top3'].src = "../images/admin_ads_top3.gif";
  Ads['belowmenu1'] = new Image(213,30);
  Ads['belowmenu1'].src = "../images/admin_ads_belowmenu.gif";
  Ads['belowmenu2'] = new Image(213,30);
  Ads['belowmenu2'].src = "../images/admin_ads_belowmenu2.gif";
  Ads['belowmenu3'] = new Image(213,30);
  Ads['belowmenu3'].src = "../images/admin_ads_belowmenu3.gif";
  Ads['left1'] = new Image(37,113);
  Ads['left1'].src = "../images/admin_ads_left.gif";
  Ads['left2'] = new Image(37,113);
  Ads['left2'].src = "../images/admin_ads_left2.gif";
  Ads['left3'] = new Image(37,113);
  Ads['left3'].src = "../images/admin_ads_left3.gif";
  Ads['right1'] = new Image(37,113);
  Ads['right1'].src = "../images/admin_ads_right.gif";
  Ads['right2'] = new Image(37,113);
  Ads['right2'].src = "../images/admin_ads_right2.gif";
  Ads['right3'] = new Image(37,113);
  Ads['right3'].src = "../images/admin_ads_right3.gif";
  Ads['bottom1'] = new Image(213,37);
  Ads['bottom1'].src = "../images/admin_ads_bottom.gif";
  Ads['bottom2'] = new Image(213,37);
  Ads['bottom2'].src = "../images/admin_ads_bottom2.gif";
  Ads['bottom3'] = new Image(213,37);
  Ads['bottom3'].src = "../images/admin_ads_bottom3.gif";
  Ads['feed1'] = new Image(213,37);
  Ads['feed1'].src = "../images/admin_ads_feed.gif";
  Ads['feed2'] = new Image(213,37);
  Ads['feed2'].src = "../images/admin_ads_feed2.gif";
  Ads['feed3'] = new Image(213,37);
  Ads['feed3'].src = "../images/admin_ads_feed3.gif";
  
  function highlight_over(id1) {
    if($(id1).src != Ads[id1+'3'].src) {
      $(id1).src=Ads[id1+'2'].src;
    }
  }
  function highlight_out(id1) {
    if($(id1).src != Ads[id1+'3'].src) {
      $(id1).src=Ads[id1+'1'].src;
    }
  }
  function highlight_click(id1) {
    var position3 = id1+"3";
    var position2 = id1+"2";
    if($(id1).src != Ads[id1+'3'].src) {
      $('top').src=Ads['top1'].src;
      $('belowmenu').src=Ads['belowmenu1'].src;
      $('left').src=Ads['left1'].src;
      $('right').src=Ads['right1'].src;
      $('bottom').src=Ads['bottom1'].src;
      $('feed').src=Ads['feed1'].src;
      $(id1).src=Ads[id1+'3'].src;
      $('banner_position').value=id1;
    } else {
      $(id1).src=Ads[position2].src;
      $("banner_position").value="";
    }
  }

  function uploadbanner() {
    var bannersrc = $('bannersrc').value;
    if(bannersrc == "") {
      alert('{/literal}{capture assign=error_message}{lang_print id=362}{/capture}{$error_message|replace:"'":"&#039;"}{literal}');
    } else {
      $('uploadform').submit();
      $('banner_upload_submit').setStyles({display:'none'});
      $('banner_upload_status').setStyles({display:'block'});
    }
  }
  function uploadbanner_result(imagename, iserror) {
    if(iserror == 1) {
      $('banner_upload_status').setStyles({display:'none'});
      $('banner_upload_submit').setStyles({display:'block'});
      alert('{/literal}{capture assign=error_message}{lang_print id=363}{/capture}{$error_message|replace:"'":"&#039;"}{literal}');
    } else {
      $('banner_upload').setStyles({display:'none'});
      $('banner_preview_upload').setStyles({display:'block'});
      var bannersrc = "./uploads_admin/ads/"+imagename;
      var bannerlink = $('bannerlink').value;
      var bannerhtml = "<a href='"+bannerlink+"' target='_blank'><img src='"+bannersrc+"' border='0'></a>";
      set_preview($('banner_upload_content'), bannerhtml);
      $('ad_html').value=bannerhtml;
      $('banner_filename_delete').value=imagename;
      $('banner_filename').value=imagename;
    }
  }
  function uploadbanner2() {
    $('banner_preview_upload').setStyles({display:'none'});
    $('banner_final_div').setStyles({display:'block'});
    $('banner_upload_status').setStyles({display:'none'});
    $('banner_upload_submit').setStyles({display:'block'});
    var bannerhtml = $('ad_html').value;
    set_preview($('banner_final'), bannerhtml);
  }
  function uploadbanner_cancel() {
    $('banner_preview_upload').setStyles({display:'none'});
    $('banner_upload_status').setStyles({display:'none'});
    $('banner_upload').setStyles({display:'block'});
    $('banner_upload_submit').setStyles({display:'block'});
    $('cancelform').submit();
  }
  function savebanner() {
    var bannerhtml = $('bannerhtml').value;
    if(bannerhtml == "") {
      alert('{/literal}{capture assign=error_message}{lang_print id=353}{/capture}{$error_message|replace:"'":"&#039;"}{literal}');
    } else {
      $('banner_html').setStyles({display:'none'});
      $('banner_preview_html').setStyles({display:'block'});
      set_preview($('banner_html_content'), bannerhtml);
    }
  }
  function savebanner2() {
    $('banner_preview_html').setStyles({display:'none'});
    $('banner_final_div').setStyles({display:'block'});
    var bannerhtml = $('bannerhtml').value;
    set_preview($('banner_final'), bannerhtml); 
    $('ad_html').value=bannerhtml;
  }
  function set_preview(preview_object, banner_html) {
    var banner_html_string = banner_html;
    banner_html_string = banner_html_string.replace(/(.)\.\/uploads\_admin/gi, "$1../uploads_admin");
    preview_object.innerHTML = banner_html_string;
  }
  function startover() {
    $('banner_final_div').setStyles({display:'none'});
    $('banner_options').setStyles({display:'block'});
    $('cancelform').submit();
  }
  function backtohtml() {
    $('banner_final_div').setStyles({display:'none'});
    $('banner_html').setStyles({display:'block'});
    $('bannerhtml').value=$('ad_html').value;
  }

  {/literal}{if $ad_html != ""}{literal}
  window.addEvent('domready', function(){
    $('banner_options').setStyles({display:'none'});
    $('banner_final_div').setStyles({display:'block'});
    var bannerhtml = $('banner_final').innerHTML;
    set_preview($('banner_final'), bannerhtml);
  });
  {/literal}{/if}{literal}
//-->
</script>
{/literal}

<table cellpadding='0' cellspacing='0' width='100%'>
<tr><td class='header'>{lang_print id=346}</td></tr>
<tr><td class='setting1'>
{lang_print id=347}
</td></tr>
<tr><td class='setting2'>

  <table cellspacing='0' cellpadding='0' width='100%'>
  <tr><td class='bannerborder'>

    <div id='banner_options'>
      <h3><b><a onClick="{literal}$('banner_upload').setStyles({display:'block'});$('banner_options').setStyles({display:'none'});{/literal}" href="#">{lang_print id=348}</a> &nbsp;&nbsp;{lang_print id=349}&nbsp;&nbsp; <a href="#" onClick="{literal}$('banner_html').setStyles({display:'block'});$('banner_options').setStyles({display:'none'});{/literal}">{lang_print id=350}</a></b></h3>
    </div>

    <div id='banner_upload' style='display: none;'>
      <b>{lang_print id=358}</b>
      <form action='admin_ads_modify.php' enctype='multipart/form-data' method='post' id='uploadform' name='uploadform' target='ajaxframe'>
      <table cellpadding='0' cellspacing='0' align='center'>
      <tr>
      <td class='form1'>{lang_print id=359}</td>
      <td class='form2'><input type='file' name='file1' size='40' id='bannersrc' class='text'></td>
      </tr>
      <tr>
      <td class='form1'>{lang_print id=360}</td>
      <td class='form2'><input type='text' name='link' size='52' id='bannerlink' value='http://' class='text'></td>
      </tr>
      <tr>
      <td class='form1'>&nbsp;</td>
      <td class='form2'>
	<div id='banner_upload_submit'>
          [ <a href="javascript:uploadbanner()">{lang_print id=361}</a> ] &nbsp;
          [ <a href="#" onClick="{literal}$('banner_options').setStyles({display:'block'});$('banner_upload').setStyles({display:'none'});{/literal}">{lang_print id=39}</a> ]
	</div>
	<div id='banner_upload_status' style='display: none;'><img src='../images/admin_uploading.gif' border='0'></div>
      </td>
      </tr>
      </table>
      <input type='hidden' name='task' value='doupload'>
      </form>
    </div>

    <div id='banner_preview_upload' style='display: none;'>
      <div style='margin-bottom: 5px;'><b>{lang_print id=354}</b></div>
      <div id='banner_upload_content'></div>
      <div style='margin-top: 5px;'>
        [ <a href="javascript:uploadbanner2();">{lang_print id=355}</a> ] &nbsp;
        [ <a href="javascript:uploadbanner_cancel();">{lang_print id=39}</a> ]
      </div>
      <form action='admin_ads_modify.php' method='post' id='cancelform' name='cancelform' target='ajaxframe'>
      <input type='hidden' name='banner_filename_delete' id='banner_filename_delete' value='{$ad_filename}'>
      <input type='hidden' name='task' value='cancelbanner'>
      </form>
    </div>

    <div id='banner_html' style='display: none;'>
      <b>{lang_print id=351}</b><br>
      <textarea rows='4' cols='90' class='text' id='bannerhtml'></textarea>
      <div style='margin-top: 5px;'>
        [ <a href="javascript:savebanner();">{lang_print id=352}</a> ] &nbsp;
        [ <a href="#" onClick="{literal}$('banner_options').setStyles({display:'block'});$('banner_html').setStyles({display:'none'});{/literal}">{lang_print id=39}</a> ]
      </div>
    </div>

    <div id='banner_preview_html' style='display: none;'>
      <div style='margin-bottom: 5px;'><b>{lang_print id=354}</b></div>
      <div id='banner_html_content'></div>
      <div style='margin-top: 5px;'>
        [ <a href="javascript:savebanner2();">{lang_print id=355}</a> ] &nbsp;
        [ <a href="#" onClick="{literal}$('banner_html').setStyles({display:'block'});$('banner_preview_html').setStyles({display:'none'});{/literal}">{lang_print id=39}</a> ]
      </div>
    </div>

    <div id='banner_final_div' style='display: none;'>
      <div id='banner_final_title'><b>{lang_print id=354}</b></div>
      <div id='banner_final' style='padding-top: 3px; padding-bottom: 3px;'>{$ad_html}</div>
      <div id='banner_final_startover'><a href="javascript:startover()">{lang_print id=356}</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:backtohtml()">{lang_print id=357}</a></div>
    </div>

  </td></tr>
  </table>

</td></tr>
</table>

<br>

<form action='admin_ads_modify.php' method='post'>
<input type='hidden' name='ad_html' id='ad_html' value='{$ad_html_encoded}'>
<input type='hidden' name='banner_filename' id='banner_filename' value='{$ad_filename}'>
<table cellpadding='0' cellspacing='0' width='100%'>
<tr><td class='header'>{lang_print id=364}</td></tr>
<tr><td class='setting1'>
{lang_print id=365}
<br><br>
<b>{lang_sprintf id=366 1=$nowdate}</b>
</td></tr>
<tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td class='form1'>{lang_print id=367}</td>
  <td class='form2'><input type='text' class='text' name='ad_name' size='64' maxlength='250' value='{$ad_name}'></td>
  </tr>
  <tr>
  <td class='form1'>{lang_print id=368}</td>
  <td class='form2'>
    <select class='text' name='ad_date_start_month'>
    <option></option>
    {section name=start_month start=1 loop=13}
      {capture assign='month'}{0|mktime:0:0:$smarty.section.start_month.index:1:1990}{/capture}
      <option value='{$smarty.section.start_month.index}'{if $datetime->cdate("n", $ad_date_start) == $smarty.section.start_month.index} selected='selected'{/if}>{$datetime->cdate("M", $month)}</option>
    {/section}
    </select>
    <select class='text' name='ad_date_start_day'>
    <option></option>
    {section name=start_day start=1 loop=32}
      <option value='{$smarty.section.start_day.index}'{if $datetime->cdate("j", $ad_date_start) == $smarty.section.start_day.index} selected='selected'{/if}>{$smarty.section.start_day.index}</option>
    {/section}
    </select>
    <select class='text' name='ad_date_start_year'>
    <option></option>
    {section name=start_year start=2008 loop=2019}
      <option value='{$smarty.section.start_year.index}'{if $datetime->cdate("Y", $ad_date_start) == $smarty.section.start_year.index} selected='selected'{/if}>{$smarty.section.start_year.index}</option>
    {/section}
    </select>
    <select class='text' name='ad_date_start_hour'>
    <option></option>
    {section name=start_hour start=1 loop=13}
      <option value='{$smarty.section.start_hour.index}'{if $datetime->cdate("g", $ad_date_start) == $smarty.section.start_hour.index} selected='selected'{/if}>{$smarty.section.start_hour.index}</option>
    {/section}
    </select>&nbsp;<b>:</b>&nbsp;
    <select class='text' name='ad_date_start_minute'>
    <option></option>
    {section name=start_minute start=0 loop=60}
      {if $smarty.section.start_minute.index < 10}{assign var="minute" value="0`$smarty.section.start_minute.index`"}{else}{assign var="minute" value=$smarty.section.start_minute.index}{/if}
      <option value='{$minute}'{if $datetime->cdate("i", $ad_date_start) == $minute} selected='selected'{/if}>{$minute}</option>
    {/section}
    </select>
    <select class='text' name='ad_date_start_ampm'>
    <option></option>
    <option value='0'{if $datetime->cdate("A", $ad_date_start) == "AM"} selected='selected'{/if}>AM</option>
    <option value='1'{if $datetime->cdate("A", $ad_date_start) == "PM"} selected='selected'{/if}>PM</option>
    </select>
  </td>
  </tr>
  <tr>
  <td class='form1'>{lang_print id=369}</td>
  <td class='form2'>

    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td><input type='radio' name='ad_date_end_options' id='timelimit0' value='0' onClick="{literal}$('enddate').setStyles({display:'none'});{/literal}"{if $ad_date_end_options == 0} checked='checked'{/if}></td>
    <td><label for='timelimit0'>{lang_print id=370}</label></td>
    </tr>
    <tr>
    <td><input type='radio' name='ad_date_end_options' id='timelimit1' value='1' onClick="{literal}$('enddate').setStyles({display:'block'});{/literal}"{if $ad_date_end_options == 1} checked='checked'{/if}></td>
    <td><label for='timelimit1'>{lang_print id=371}</label></td>
    </tr>
    </table>

    <div style='margin-top: 7px; margin-bottom: 2px;{if $ad_date_end_options == 0} display: none;{/if}' id='enddate'>
      <select class='text' name='ad_date_end_month'>
      <option></option>
      {section name=end_month start=1 loop=13}
        {capture assign='month'}{0|mktime:0:0:$smarty.section.end_month.index:1:1990}{/capture}
        <option  value='{$smarty.section.end_month.index}'{if $datetime->cdate("n", $ad_date_end) == $smarty.section.end_month.index} selected='selected'{/if}>{$datetime->cdate("M", $month)}</option>
      {/section}
      </select>
      <select class='text' name='ad_date_end_day'>
      <option></option>
      {section name=end_day start=1 loop=32}
        <option value='{$smarty.section.end_day.index}'{if $datetime->cdate("j", $ad_date_end) == $smarty.section.end_day.index} selected='selected'{/if}>{$smarty.section.end_day.index}</option>
      {/section}
      </select>
      <select class='text' name='ad_date_end_year'>
      <option></option>
      {section name=end_year start=2008 loop=2019}
        <option value='{$smarty.section.end_year.index}'{if $datetime->cdate("Y", $ad_date_end) == $smarty.section.end_year.index} selected='selected'{/if}>{$smarty.section.end_year.index}</option>
      {/section}
      </select>
      <select class='text' name='ad_date_end_hour'>
      <option></option>
      {section name=end_hour start=1 loop=13}
        <option value='{$smarty.section.end_hour.index}'{if $datetime->cdate("g", $ad_date_end) == $smarty.section.end_hour.index} selected='selected'{/if}>{$smarty.section.end_hour.index}</option>
      {/section}
      </select>&nbsp;<b>:</b>&nbsp;
      <select class='text' name='ad_date_end_minute'>
      <option></option>
      {section name=end_minute start=0 loop=60}
        {if $smarty.section.end_minute.index < 10}{assign var="minute" value="0`$smarty.section.end_minute.index`"}{else}{assign var="minute" value=$smarty.section.end_minute.index}{/if}
        <option value='{$minute}'{if $datetime->cdate("i", $ad_date_end) == $minute} selected='selected'{/if}>{$minute}</option>
      {/section}
      </select>
      <select class='text' name='ad_date_end_ampm'>
      <option></option>
      <option value='0'{if $datetime->cdate("A", $ad_date_end) == "AM"} selected='selected'{/if}>AM</option>
      <option value='1'{if $datetime->cdate("A", $ad_date_end) == "PM"} selected='selected'{/if}>PM</option>
      </select>
    </div>
  </td>
  </tr>
  <tr>
  <td class='form1'>{lang_print id=372}</td>
  <td class='form2'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td><input type='text' id='ad_limit_views' name='ad_limit_views' class='text' size='7' maxlength='10'{if $ad_limit_views == 0} disabled='disabled' style='background: #DDDDDD;'{/if} value='{if $ad_limit_views != 0}{$ad_limit_views}{/if}'>&nbsp;&nbsp;&nbsp;</td>
    <td><input type='checkbox' id='ad_limit_views_unlimited' name='ad_limit_views_unlimited' value='1' class='checkbox'{if $ad_limit_views == 0} checked='checked'{/if} onClick="{literal}if(this.checked == true){ $('ad_limit_views').value = ''; $('ad_limit_views').disabled=true; $('ad_limit_views').style.backgroundColor='#DDDDDD'; } else { $('ad_limit_views').disabled=false; $('ad_limit_views').style.backgroundColor='#FFFFFF'; }{/literal}"> <label for='ad_limit_views_unlimited'>{lang_print id=373}</label></td>
    </tr>
    </table>
  </td>
  </tr>
  <tr>
  <td class='form1'>{lang_print id=374}</td>
  <td class='form2'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td><input type='text' id='ad_limit_clicks' name='ad_limit_clicks' class='text' size='7' maxlength='10'{if $ad_limit_clicks == 0} disabled='disabled' style='background: #DDDDDD;'{/if} value='{if $ad_limit_clicks != 0}{$ad_limit_clicks}{/if}'>&nbsp;&nbsp;&nbsp;</td>
    <td><input type='checkbox' id='ad_limit_clicks_unlimited' name='ad_limit_clicks_unlimited' value='1' class='checkbox'{if $ad_limit_clicks == 0} checked='checked'{/if} onClick="{literal}if(this.checked == true){ $('ad_limit_clicks').value = ''; $('ad_limit_clicks').disabled=true; $('ad_limit_clicks').style.backgroundColor='#DDDDDD'; } else { $('ad_limit_clicks').disabled=false; $('ad_limit_clicks').style.backgroundColor='#FFFFFF'; }{/literal}"> <label for='ad_limit_clicks_unlimited'>{lang_print id=373}</label></td>
    </tr>
    </table>
  </td>
  </tr>
  <tr>
  <td class='form1'>{lang_print id=375}</td>
  <td class='form2'>
    <table cellpadding='0' cellspacing='0'>
    <tr>
    <td><input type='text' id='ad_limit_ctr' name='ad_limit_ctr' class='text' size='7' maxlength='7'{if $ad_limit_ctr == 0} disabled='disabled' style='background: #DDDDDD;'{/if} value='{if $ad_limit_ctr != 0}{$ad_limit_ctr}{/if}'>&nbsp;&nbsp;&nbsp;</td>
    <td><input type='checkbox' id='ad_limit_ctr_unlimited' name='ad_limit_ctr_unlimited' value='1' class='checkbox'{if $ad_limit_ctr == 0} checked='checked'{/if} onClick="{literal}if(this.checked == true){ $('ad_limit_ctr').value = ''; $('ad_limit_ctr').disabled=true; $('ad_limit_ctr').style.backgroundColor='#DDDDDD'; } else { $('ad_limit_ctr').disabled=false; $('ad_limit_ctr').style.backgroundColor='#FFFFFF'; }{/literal}"> <label for='ad_limit_ctr_unlimited'>{lang_print id=373}</label></td>
    </tr>
    </table>
  </td>
  </tr>
  </table>
</td></tr>
</table>

<br>


<table cellpadding='0' cellspacing='0' width='100%'>
<tr><td class='header'>{lang_print id=376}</td></tr>
<tr><td class='setting1'>
  {lang_print id=377}
</td></tr>
<tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0' align='center'>
  <tr>
  <td colspan='3'><img src='../images/admin_ads_top{if $ad_position == "top"}3{/if}.gif' style='cursor: pointer;' border='0' id='top' onMouseOver="highlight_over('top')" onMouseOut="highlight_out('top')" onClick="highlight_click('top')"></td>
  </tr>
  <tr>
  <td colspan='3'><img src='../images/admin_ads_menu.gif' border='0'></td>
  </tr>
  <tr>
  <td colspan='3'><img src='../images/admin_ads_belowmenu{if $ad_position == "belowmenu"}3{/if}.gif' style='cursor: pointer;' border='0' id='belowmenu' onMouseOver="highlight_over('belowmenu')" onMouseOut="highlight_out('belowmenu')" onClick="highlight_click('belowmenu')"></td>
  </tr>
  <tr>
  <td><img src='../images/admin_ads_left{if $ad_position == "left"}3{/if}.gif' style='cursor: pointer;' border='0' id='left' onMouseOver="highlight_over('left')" onMouseOut="highlight_out('left')" onClick="highlight_click('left')"></td>
  <td><img src='../images/admin_ads_feed{if $ad_position == "feed"}3{/if}.gif' style='cursor: pointer;' border='0' id='feed' onMouseOver="highlight_over('feed')" onMouseOut="highlight_out('feed')" onClick="highlight_click('feed')"><div style='padding-top: 3px;'><img src='../images/admin_ads_content.gif' border='0'></div></td>
  <td><img src='../images/admin_ads_right{if $ad_position == "right"}3{/if}.gif' style='cursor: pointer;' border='0' id='right' onMouseOver="highlight_over('right')" onMouseOut="highlight_out('right')" onClick="highlight_click('right')"></td>
  </tr>
  <tr>
  <td colspan='3'><img src='../images/admin_ads_bottom{if $ad_position == "bottom"}3{/if}.gif' style='cursor: pointer;' border='0' id='bottom' onMouseOver="highlight_over('bottom')" onMouseOut="highlight_out('bottom')" onClick="highlight_click('bottom')"></td>
  </tr>
  </table>
  <input type='hidden' name='banner_position' id='banner_position' value='{$ad_position}'>
</td></tr>
<tr><td class='setting1'>
  {$admin_ads_add57}
  <div style='text-align: center;'>
    <h3><b>{literal}{$ads->ads_display('{/literal}{$ad_id}{literal}')}{/literal}</b></h3>
  </div>
</td></tr>
</table>

<br>

{* DETERMINE HOW MANY LEVELS AND SUBNETS TO SHOW BEFORE ADDING SCROLLBARS *}
{if $levels|@count > 10 OR $subnets|@count+1 > 10}
  {assign var='options_to_show' value='10'}
{elseif $levels|@count > $subnets|@count+1}
  {assign var='options_to_show' value=$levels|@count}
{elseif $levels|@count < $subnets|@count+1}
  {assign var='options_to_show' value=$subnets|@count+1}
{elseif $levels|@count == $subnets|@count+1}
  {assign var='options_to_show' value=$levels|@count}
{/if}

<table cellpadding='0' cellspacing='0' width='100%'>
<tr><td class='header'>{lang_print id=379}</td></tr>
<tr><td class='setting1'>
  {lang_print id=380}
</td></tr>
<tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0' align='center'>
  <tr>
  <td><b>{lang_print id=8}</b></td>
  <td style='padding-left: 10px;'><b>{lang_print id=9}</b></td>
  </tr>
  <tr>
  <td>
    <select size='{$options_to_show}' class='text' name='ad_levels[]' multiple='multiple' style='width: 335px;'>
    {section name=level_loop loop=$levels}
      <option value='{$levels[level_loop].level_id}'{if $levels[level_loop].level_id|in_array:$ad_levels_array} selected='selected'{/if}>{$levels[level_loop].level_name|truncate:75:"...":true}{if $levels[level_loop].level_default == 1} {lang_print id=382}{/if}</option>
    {/section}
    </select>
  </td>
  <td style='padding-left: 10px;'>
    <select size='{$options_to_show}' class='text' name='ad_subnets[]' multiple='multiple' style='width: 335px;'>
    <option value='0'{if '0'|in_array:$ad_subnets_array === TRUE} selected='selected'{/if}>{lang_print id=383}</option>
    {section name=subnet_loop loop=$subnets}
      <option value='{$subnets[subnet_loop].subnet_id}'{if $subnets[subnet_loop].subnet_id|in_array:$ad_subnets_array} selected='selected'{/if}>{lang_print id=$subnets[subnet_loop].subnet_name}</option>
    {/section}
    </select>
  </td>
  </tr>
  </table>
</td></tr>
<tr><td class='setting2'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td><input type='checkbox' name='ad_public' id='ad_public' value='1'{if $ad_public == 1} checked='checked'{/if}></td>
  <td><label for='ad_public'>{lang_print id=384}</label></td>
  </tr>
  </table>
</td></tr>
</table>

<br>

<table cellpadding='0' cellspacing='0'>
<tr>
<td>
  <input type='submit' class='button' value='{if $ad_exists == 0}{lang_print id=385}{else}{lang_print id=173}{/if}'>&nbsp;
  <input type='hidden' name='task' value='dosave'>
  <input type='hidden' name='ad_id' value='{$ad_id}'>
  </form>
</td>
<td>
  <form action='admin_ads.php' method='post'>
  <input type='submit' class='button' value='{lang_print id=39}'>
  </form>
</td>
</tr>
</table>

<br>

</td>
</tr>
</table>


{include file='admin_footer.tpl'}