{include file='admin_header.tpl'}

<h2>{lang_print id=690691138}</h2>
{lang_print id=690691181}
<br />
<br />
  {* JAVASCRIPT FOR CHECK ALL *}
  {literal}
  <script language='JavaScript'> 
  <!---
  var checkboxcount = 1;
  function doCheckAll() {
    if(checkboxcount == 0) {
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = false;
      }}
      checkboxcount = checkboxcount + 1;
      }
    } else
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = true;
      }}
      checkboxcount = checkboxcount - 1;
      }
  }

  var q_id = 0;
  function confirmDelete(id) {
    q_id = id;
    TB_show('{/literal}{lang_print id=1013}{literal}', '#TB_inline?height=150&width=300&inlineId=confirmdelete', '', '../images/trans.gif');
  }

  function deleteUser() {
    window.location = 'admin_viewquizzes.php?task=delete&id='+q_id;
  }
  // -->
  </script>
  {/literal}

  {* HIDDEN DIV TO DISPLAY CONFIRMATION MESSAGE *}
  <div style='display: none;' id='confirmdelete'>
    <div style='margin-top: 10px;'>
      {lang_print id=690691177}
    </div>
    <br>
    <input type='button' class='button' value='{lang_print id=175}' onClick='parent.TB_remove();parent.deleteUser();'> <input type='button' class='button' value='{lang_print id=39}' onClick='parent.TB_remove();'>
  </div>
   
  <div class='pages'>{$total_quizzes} {lang_print id=690691138} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewquizzes.php?s={$s}&p={$pages[page_loop].page}'>{$pages[page_loop].page}</a>{/if} {/section}</div>
  <form action='admin_viewquizzes.php' method='post' name='items'>
  <table cellpadding='0' cellspacing='0' class='list' width='100%'>
  <tr>
  <td class='header' width='10'><input type='checkbox' name='select_all' onClick='javascript:doCheckAll()'></td>
  <td class='header' width='10' align='center'>{lang_print id=87}</td>
  <td class='header'>{lang_print id=28}</td>
  <td class='header'>{lang_print id=258}</td>
  <td class='header'>{lang_print id=277}</td>
  <td class='header'>{lang_print id=690691175}</td>
  <td class='header'>{lang_print id=259}</td>
  <td class='header'>{lang_print id=153}</td>
  </tr>
 {if $total_quizzes != 0}
  <!-- LOOP THROUGH USERS -->
  {section name=quizz_loop loop=$quizzes}
    <tr class='{cycle values="background1,background2"}'>
    	<td class='item'><input type='checkbox' name='delete[]' value='{$quizzes[quizz_loop].quiz_id}' ></td>
	   	<td class='item' align='center' ><a>{$quizzes[quizz_loop].quiz_id}</a></td>
	   	<td class='item'><a>{$quizzes[quizz_loop].user_username|truncate:25:"...":true}</a></td>
	   	<td class='item'><a>{$quizzes[quizz_loop].name|truncate:25:"...":true}</a></td>
	   	<td class='item'><a>{$quizzes[quizz_loop].description|truncate:100}</a></td>
	   	<td class='item'><a>{if $quizzes[quizz_loop].status==0}{lang_print id=1001}{else}{lang_print id=1000}{/if}</a></td>
    	<td class='item'><a href=''>{if $quizzes[quizz_loop].approved==0}<a href='admin_viewquizzes.php?s={$d}&p={$pages[page_loop].page}&st=1&id={$quizzes[quizz_loop].quiz_id}'>{lang_print id=690691174}{else}<a href='admin_viewquizzes.php?s={$d}&p={$pages[page_loop].page}&st=0&id={$quizzes[quizz_loop].quiz_id}'>{lang_print id=690691173}{/if}</a></td>
    	<td class='item' nowrap='nowrap'><a href="javascript: confirmDelete('{$quizzes[quizz_loop].quiz_id}');">{lang_print id=155}</a></td>
    </tr>
  {/section}
  {else}
  <tr>
  	<td class='item' colspan=8>{lang_print id=690691160}</td>
  </tr>
  {/if}
  </table>
  
  <table cellpadding='0' cellspacing='0' width='100%'>
  <tr>
  <td>
    <br>
    <input type='submit' class='button' value='{lang_print id=788}'>
    <input type='hidden' name='task' value='dodelete'>
    </form>
  </td>
  <td align='right' valign='top'><br />
    <div class='pages'>{$total_quizzes} {lang_print id=690691138} &nbsp;|&nbsp; {lang_print id=1005} {section name=page_loop loop=$pages}{if $pages[page_loop].link == '1'}{$pages[page_loop].page}{else}<a href='admin_viewquizzes.php?s={$s}&p={$pages[page_loop].page}'>{$pages[page_loop].page}</a>{/if} {/section}</div>
  </td>
  </tr>
  </table>
  </form>
  
{include file='admin_footer.tpl'}