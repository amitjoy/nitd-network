{include file='header.tpl'}

{* $Id: help.tpl 8 2009-01-11 06:02:53Z john $ *}

<img src='./images/icons/help48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=957}</div>
<div>{lang_print id=958}</div>
<br />
<br />

{* JAVASCRIPT FOR CHANGING FRIEND MENU OPTION *}
{literal}
<script type="text/javascript">
<!-- 
  function faq_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
      $('ajaxframe').src = 'help.php?task=view&faq_id='+id;
    }
  }
//-->
</script>
{/literal}

{section name=faqcat_loop loop=$faqcats}
  {if $faqcats[faqcat_loop].faqs|@count != 0}
    <div class='header'>{lang_print id=$faqcats[faqcat_loop].faqcat_title}</div>
    {section name=faq_loop loop=$faqcats[faqcat_loop].faqs}
      <div class='faq_questions'>
      <a href="javascript:void(0);" onClick="faq_show('{$faqcats[faqcat_loop].faqs[faq_loop].faq_id}');">{lang_print id=$faqcats[faqcat_loop].faqs[faq_loop].faq_subject}</a><br>
      <div class='faq' style='display: none;' id='{$faqcats[faqcat_loop].faqs[faq_loop].faq_id}'>{lang_print id=$faqcats[faqcat_loop].faqs[faq_loop].faq_content}</div>
      </div>
    {/section}
    <br>
  {/if}
{/section}

{include file='footer.tpl'}