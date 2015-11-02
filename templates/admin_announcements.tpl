{include file='admin_header.tpl'}

{* $Id: admin_announcements.tpl 8 2009-01-11 06:02:53Z john $ *}

<h2>{lang_print id=23}</h2>

<br>
<br><br>
<b><a href='javascript: postNews();'>{lang_print id=586}</a></b>
<br>{lang_print id=587}

<br><br>

{* LIST PAST ANNOUNCEMENTS *}
{if $news|@count > 0}
  <table cellpadding='0' cellspacing='0' class='list'>
  <tr>
  <td class='header' width='10'>{lang_print id=87}</td>
  <td class='header' width='80%'>{lang_print id=588}</td>
  <td class='header' width='50'>{lang_print id=153}</td>
  </tr>
  {section name=news_loop loop=$news}
    <tr class='{cycle values="background1,background2"}'>
    <td class='item' valign='top'>{$news[news_loop].announcement_id}</td>
    <td class='item'>
      <div><b>{if $news[news_loop].announcement_subject != ""}{$news[news_loop].announcement_subject|truncate:50:"...":true}{else}<i>{lang_print id=589}</i>{/if}</b></div>
      <div>{if $news[news_loop].announcement_date != ""}{$news[news_loop].announcement_date}{else}<i>{lang_print id=590}</i>{/if}</div>
      <br><div>{$news[news_loop].announcement_body|truncate:300:"...":true}</div>
    </td>
    <td class='item' valign='top' nowrap='nowrap' align='right'>
      [ <a href="javascript:editNews('{$news[news_loop].announcement_id}');">{lang_print id=187}</a> ]<br>
      {if $smarty.section.news_loop.last != true}[ <a href='admin_announcements.php?task=moveup&announcement_id={$news[news_loop].announcement_id}'>{lang_print id=591}</a> ]<br>{/if}
      [ <a href="javascript:confirmDelete('{$news[news_loop].announcement_id}');">{lang_print id=155}</a> ]
    </td>
    </tr>
  {/section}
  </table>
{/if}






{* JAVASCRIPT FOR CONFIRMING DELETION *}
{literal}
<script type="text/javascript">
<!-- 
var announcement_id = 0;
function confirmDelete(id) {
  announcement_id = id;
  TB_show('{/literal}{lang_print id=598}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');

}

function deleteNews() {
  window.location = 'admin_announcements.php?task=deletenews&announcement_id='+announcement_id;
}

function editNews(id) {
  $('announcement_id').value = id;
  var url = 'admin_announcements.php?task=getnews&announcement_id='+id;
  var request = new Request.JSON({secure: false, url: url,
	onComplete: function(jsonObj) {
		edit(jsonObj);
	}
  }).send();
}

function edit(announcement) {
  $('announcement_date').value = announcement.date;
  $('announcement_date').defaultValue = announcement.date;
  $('announcement_subject').value = announcement.subject;
  $('announcement_subject').defaultValue = announcement.subject;
  $('announcement_body').innerHTML = announcement.body;
  TB_show('{/literal}{lang_print id=597}{literal}', '#TB_inline?height=400&width=600&inlineId=postnews', '', '../images/trans.gif');
}

function postNews() {
  $('announcement_date').value = '';
  $('announcement_date').defaultValue = '';
  $('announcement_subject').value = '';
  $('announcement_subject').defaultValue = '';
  $('announcement_body').innerHTML = '';
  TB_show('{/literal}{lang_print id=592}{literal}', '#TB_inline?height=400&width=500&inlineId=postnews', '', '../images/trans.gif');
}

//-->
</script>
{/literal}

{* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
<div style='display: none;' id='confirmdelete'>
  <div style='margin-top: 10px;'>
    {lang_print id=599}
  </div>
  <br>
  <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteNews();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
</div>


{* HIDDEN DIV TO DISPLAY POST NEWS ANNOUNCEMENT *}
<div style='display: none;' id='postnews'>
  <form action='admin_announcements.php' method='post' target='_parent'>
  <div style='margin-top: 10px;'>{lang_print id=593}</div>
  <br>
  <b>{lang_print id=88}</b>
  <br><input type='text' name='date' id='announcement_date' size='50' class='text' maxlength='200'>
  <br>{lang_print id=594}
  <br><br>
  <b>{lang_print id=520}</b>
  <br><input type='text' name='subject' id='announcement_subject' size='50' class='text' maxlength='200'>
  <br><br>
  <b>{lang_print id=588}</b> {lang_print id=595}
  <br><textarea name='body' id='announcement_body' class='text' rows='7' cols='80'></textarea>
  <br><br>
  <input type='submit' class='button' value='{lang_print id=596}'>&nbsp;<input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  <input type='hidden' name='task' value='postnews'>
  <input type='hidden' name='announcement_id' id='announcement_id' value='0'>
  </form>
</div>

{include file='admin_footer.tpl'}