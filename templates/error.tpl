{include file='header.tpl'}

{* $Id: error.tpl 8 2009-01-11 06:02:53Z john $ *}

<img src='./images/icons/error48.gif' border='0' class='icon_big'>
<div class='page_header'>{lang_print id=$error_header}</div>
{lang_print id=$error_message}

<br />
<br />
<br />

<input type='button' class='button' value='{lang_print id=$error_submit}' onClick='history.go(-1)'>

{include file='footer.tpl'}