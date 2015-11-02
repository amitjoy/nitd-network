<html>
{* $Id: chat_frame.tpl 6 2009-01-11 06:01:29Z john $ *}
<head>
<script type="text/javascript" src="./include/js/mootools12.js"></script>
<script type="text/javascript" src="./include/js/mootools12-more.js"></script>
<link rel="stylesheet" href="./templates/styles_chat.css" title="stylesheet" type="text/css">  
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8"> 
</head>
<body onLoad='chat_login()'>

<div class='chat_curtain' id='chat_curtain'>
{lang_print id=3500004}
<br><br>
{lang_sprintf id=3500005 1=$user->user_displayname}
<br><br>
<img src='./images/icons/chat_working.gif' border='0'>
</div>

<table cellpadding='0' cellspacing='0' width='100%' height='100%'>
<tr>
<td class='chat_header'>{lang_print id=3500007}</td>
<td class='chat_header2' id='chat_onlineusers_header'{if $setting.setting_chat_showphotos == 1} style='text-align: center;'{/if}>&nbsp;</td>
</tr>
<tr>
<td class='chat_contentarea'>
  <div class='chat_content' id='chat_content' name='chat_content' style='height: 385px;'></div>
</td>
<td rowspan='2' class='chat_onlineusersarea'>
  <div style='overflow-y: auto; height: 477px;' class='chat_onlineusers' {if $setting.setting_chat_showphotos == 1} style='text-align: center;'{/if}>
    <div class='chat_onlineusers_me'{if $setting.setting_chat_showphotos == 1} style='text-align: center;'{/if}><a href='{$url->url_create('profile', $user->user_info.user_username)}' target='_blank'>{if $setting.setting_chat_showphotos == 1}<img src='{$user->user_photo("./images/nophoto.gif")}' width='70' class='photo' border='0'><br>{/if}{$user->user_displayname|truncate:15:'...':true}</a></div>
    <div id='chat_onlineusers'></div>
  </div>
  &nbsp;
</td>
</tr>
<tr>
<td class='chat_inputarea' style='height: 120px;'>

  <form action='chat_frame.php' target='chat_send' name='chat_form' id='chat_form' onSubmit="return chat_addmsg()" method='post'>
  <table cellpadding='0' cellspacing='0'>
  <tr>
  <td><input type='text' name='chat_msg' id='chat_msg' class='chat_msg' autocomplete='off' onkeypress='return chat_noenter()' onfocus='textfield_toggle()' onblur='textfield_toggle()' onkeyup='submit_toggle()'></td>
  <td><input type='submit' id='chat_button_submit' class='chat_submit2' DISABLED value='SEND' onClick="chat_clear()"></td>
  </tr>
  </table>
  <input type='hidden' name='chat_status' id='chat_status' value=''>
  <input type='hidden' name='chat_bold_value' id='chat_bold_value' value=''>
  <input type='hidden' name='chat_italic_value' id='chat_italic_value' value=''>
  <input type='hidden' name='chat_underline_value' id='chat_underline_value' value=''>
  <input type='hidden' name='chat_color_value' id='chat_color_value' value=''>
  <input type='hidden' name='task' value='chat_send'>
  </form>
  <div class='buttons'>
    <div class='button' style='width: 25px;'><div class='button1' id='button_bold' onClick="button_toggle('button_bold');"><img src='./images/icons/chat_bold.png' border='0' class='icon' onselect="return false" alt='{lang_print id=3500008}'></div></div>
    <div class='button' style='width: 25px;'><div class='button1' id='button_italic' onClick="button_toggle('button_italic');"><img src='./images/icons/chat_italic.png' border='0' class='icon' alt='{lang_print id=3500009}'></div></div>
    <div class='button' style='width: 25px;'><div class='button1' id='button_underline' onClick="button_toggle('button_underline');"><img src='./images/icons/chat_underline.png' border='0' class='icon' alt='{lang_print id=3500010}'></div></div>
    <div class='button' style='width: 25px;'><div class='button1' id='button_smilie' onClick="button_picksmilie()"><img src='./images/icons/chat_smile.png' border='0' class='icon' alt='{lang_print id=3500011}'></div>
      <div id='chat_smilieselect' class='smilieselect'>
	{literal}
        <script type='text/javascript'>
	<!--
        smilies = new Array('cool', 'cry', 'embarassed', 'foot-in-mouth', 'frown', 'innocent', 'kiss', 'laughing', 'sealed', 'smile', 'surprised', 'tongue', 'undecided', 'wink', 'yell');
	for(var i=0, len=smilies.length; i<len; ++i) {
	  document.write("<div class='smileyselect_smiley' onClick=\"button_picksmilie_go(':"+smilies[i]+":')\"><img src='./images/smilies_chat/"+smilies[i]+".png' border='0' title=':"+smilies[i]+":' alt=':"+smilies[i]+":'></div>");
	}
	//-->
	</script>
	{/literal}
      </div>
    </div>
    <div class='button' style='width: 45px;'><div class='button_color1' id='button_color'><img src='./images/trans.gif' style='height: 16px; width: 100%;' border='0' onClick="button_pickcolor()" alt='{lang_print id=3500012}'>
      <div id='chat_colorselect' class='colorselect'>
	{literal}
        <script type='text/javascript'>
	<!--
	colors = new Array('000000','000033','000066','000099','0000CC','0000FF','003300','003333','003366','003399','0033CC','0033FF','006600','006633','006666','006699','0066CC','0066FF','009900','009933','009966','009999','0099CC','0099FF','00CC00','00CC33','00CC66','00CC99','00CCCC','00CCFF','00FF00','00FF33','00FF66','00FF99','00FFCC','00FFFF','330000','330033','330066','330099','3300CC','3300FF','333300','333333','333366','333399','3333CC','3333FF','336600','336633','336666','336699','3366CC','3366FF','339900','339933','339966','339999','3399CC','3399FF','33CC00','33CC33','33CC66','33CC99','33CCCC','33CCFF','33FF00','33FF33','33FF66','33FF99','33FFCC','33FFFF','660000','660033','660066','660099','6600CC','6600FF','663300','663333','663366','663399','6633CC','6633FF','666600','666633','666666','666699','6666CC','6666FF','669900','669933','669966','669999','6699CC','6699FF','66CC00','66CC33','66CC66','66CC99','66CCCC','66CCFF','66FF00','66FF33','66FF66','66FF99','66FFCC','66FFFF','990000','990033','990066','990099','9900CC','9900FF','993300','993333','993366','993399','9933CC','9933FF','996600','996633','996666','996699','9966CC','9966FF','999900','999933','999966','999999','9999CC','9999FF','99CC00','99CC33','99CC66','99CC99','99CCCC','99CCFF','99FF00','99FF33','99FF66','99FF99','99FFCC','99FFFF','CC0000','CC0033','CC0066','CC0099','CC00CC','CC00FF','CC3300','CC3333','CC3366','CC3399','CC33CC','CC33FF','CC6600','CC6633','CC6666','CC6699','CC66CC','CC66FF','CC9900','CC9933','CC9966','CC9999','CC99CC','CC99FF','CCCC00','CCCC33','CCCC66','CCCC99','CCCCCC','CCCCFF','CCFF00','CCFF33','CCFF66','CCFF99','CCFFCC','CCFFFF','FF0000','FF0033','FF0066','FF0099','FF00CC','FF00FF','FF3300','FF3333','FF3366','FF3399','FF33CC','FF33FF','FF6600','FF6633','FF6666','FF6699','FF66CC','FF66FF','FF9900','FF9933','FF9966','FF9999','FF99CC','FF99FF','FFCC00','FFCC33','FFCC66','FFCC99','FFCCCC','FFCCFF','FFFF00','FFFF33','FFFF66','FFFF99','FFFFCC','FFFFFF');
	for(var i=0, len=colors.length; i<len; ++i) {
	  document.write("<div class='colorselect_color' onClick=\"button_pickcolor_go('"+colors[i]+"')\" style='background: #"+colors[i]+";'>&nbsp;</div>");
	}
	//-->
	</script>
	{/literal}
      </div>
    </div></div>
  <img src='./images/icons/chat_clock1.gif' id='chat_timestamp_icon' border='0' class='icon' onClick='button_timestamp_toggle()' style='margin-top: 2px; margin-left: 2px;' alt='{lang_print id=3500013}'>
  <img src='./images/icons/chat_audio1.gif' id='chat_audio_icon' border='0' class='icon' onClick='button_audio_toggle()' style='margin-top: 2px; margin-left: 4px;' alt='{lang_print id=3500014}'>
  <iframe src='about:blank' id='chat_audio_frame' frameborder='0' name='chat_audio_frame' width='5' height='5' style='visibility: hidden;'></iframe>
  </div>
</td>
</tr>
</table>

<iframe src='chat_frame.php?task=blank&nocache={0|rand:10000}' id='chat_send' name='chat_send' style='display: none;'></iframe>

{literal}
<script type='text/javascript'>
<!--

// SET GLOBAL VARS
var audio_on = 0;
var colorChanged = 0;
var chat_timestamp_on = 0;
var javascript_time = new Date();
var time_diff = {/literal}{$server_time}{literal}-javascript_time.getTime();
var max_message_id = 0;

// DETECT IE 5.5+
var version = 0;
var browser = "";
if (navigator.appVersion.indexOf("MSIE")!=-1){
temp=navigator.appVersion.split("MSIE")
version=parseFloat(temp[1])
}
if(version >= 5.5) {
  browser = "IE";
} else {
  browser = "";
}


// DISABLE SUBMIT BUTTON WHEN NO TEXT
function submit_toggle() {
  var chat_msg = document.getElementById('chat_msg');

  if(chat_msg.value == '') {
    document.getElementById('chat_button_submit').disabled = true;
    document.getElementById('chat_button_submit').className = "chat_submit2";
  } else {
    document.getElementById('chat_button_submit').disabled = false;
    document.getElementById('chat_button_submit').className = "chat_submit";
  }
}


// HIGHLIGHT TEXT INPUT FIELD WHEN FOCUSED
function textfield_toggle() {
  var chat_msg = document.getElementById('chat_msg');
  if(chat_msg.className == "chat_msg") {
    chat_msg.className = "chat_msg2";
  } else {
    chat_msg.className = "chat_msg";
  }
}


// PRESS A TEXT STYLE BUTTON
function button_toggle(id1) {

  // NAME THIS BUTTON AND NAME THE TEXT INPUT AREA
  var thisButton = document.getElementById(id1);
  var chat_msg = document.getElementById('chat_msg');
  var button_pressed = false;

  // FOCUS BACK ON TEXT FIELD
  chat_msg.focus();

  // CHANGE BUTTON ICON
  if(thisButton.className == "button1") {
    thisButton.className = "button2";
    button_pressed = true;
  } else {
    thisButton.className = "button1";
    button_pressed = false;
  }

  // DO BOLD
  if(thisButton.id == "button_bold") {
    if(button_pressed == true) {
      chat_msg.style.fontWeight = "bold";
      document.getElementById('chat_bold_value').value = "1";
    } else {
      chat_msg.style.fontWeight = "normal";
      document.getElementById('chat_bold_value').value = "0";
    }
  }

  // DO ITALIC
  if(thisButton.id == "button_italic") {
    if(button_pressed == true) {
      chat_msg.style.fontStyle = "italic";
      document.getElementById('chat_italic_value').value = "1";
    } else {
      chat_msg.style.fontStyle = "normal";
      document.getElementById('chat_italic_value').value = "0";
    }
  }

  // DO UNDERLINE
  if(thisButton.id == "button_underline") {
    if(button_pressed == true) {
      chat_msg.style.textDecoration = "underline";
      document.getElementById('chat_underline_value').value = "1";
    } else {
      chat_msg.style.textDecoration = "none";
      document.getElementById('chat_underline_value').value = "1";
    }
  }

}


// PRESS COLOR BUTTON
function button_pickcolor() {

  var colorSelect = document.getElementById('chat_colorselect');
  colorSelect.style.display = "block";

}
function button_pickcolor_go(colorhex) {

  var colorSelect = document.getElementById('chat_colorselect');
  colorSelect.style.display = "none";

  var buttonColor = document.getElementById('button_color');
  buttonColor.style.background = "#"+colorhex;

  var chat_msg = document.getElementById('chat_msg');
  chat_msg.style.color = "#"+colorhex;
  colorChanged = 1;

  document.getElementById('chat_color_value').value = colorhex;

  chat_msg.focus();

}



// PRESS SMILIE BUTTON
function button_picksmilie() {

  var smilieSelect = document.getElementById('chat_smilieselect');

  if(smilieSelect.style.display != "block") {
    button_toggle('button_smilie');
  }

  smilieSelect.style.display = "block";

}
function button_picksmilie_go(insertText) {

  var smilieSelect = document.getElementById('chat_smilieselect');
  smilieSelect.style.display = "none";

  var chat_msg = document.getElementById('chat_msg');
  if(chat_msg.value != "") {
    chat_msg.value = chat_msg.value + ' ' + insertText;
  } else {
    chat_msg.value = chat_msg.value + insertText;
  }

  button_toggle('button_smilie');

}




// TOGGLE TIMESTAMP DISPLAY
clockImage1 = new Image(16,16);
clockImage1.src = "./images/icons/chat_clock1.gif";
clockImage2 = new Image(16,16);
clockImage2.src = "./images/icons/chat_clock2.gif";
var chat_timestamp_icon = document.getElementById('chat_timestamp_icon');
function button_timestamp_toggle() {

  // SHOW OR HIDE TIMESTAMP SPANS
  var spans = document.getElementsByTagName('span');
  for (var i = 0; i < spans.length; i++) {
    if(spans[i].className == 'chat_timestamp') {
      if(spans[i].style.display != "inline") {
        spans[i].style.display = 'inline';
      } else {
        spans[i].style.display = 'none';
      }
    }
  }

  // SWITCH BUTTON ICON
  if(chat_timestamp_icon.src == clockImage1.src) {
    chat_timestamp_icon.src = clockImage2.src;
    chat_timestamp_on = 1;
  } else {
    chat_timestamp_icon.src = clockImage1.src;
    chat_timestamp_on = 0;
  }

}




// TOGGLE AUDIO  
audioImage1 = new Image(16,16);
audioImage1.src = "./images/icons/chat_audio1.gif";
audioImage2 = new Image(16,16);
audioImage2.src = "./images/icons/chat_audio2.gif";
function button_audio_toggle() {

  // SWITCH BUTTON ICON
  var chat_audio_icon = document.getElementById('chat_audio_icon');
  if(chat_audio_icon.src == audioImage1.src) {
    audio_on = 1;
    chat_audio_icon.src = audioImage2.src;
  } else {
    audio_on = 0;
    chat_audio_icon.src = audioImage1.src;
  }

}

// PLAY AUDIO
function button_audio_play() {

  var iframe = document.getElementById('chat_audio_frame');
  var doc = null;  
    if(iframe.contentDocument)  
      // Firefox, Opera  
      doc = iframe.contentDocument;  
    else if(iframe.contentWindow)  
      // Internet Explorer  
      doc = iframe.contentWindow.document;  
    else if(iframe.document)  
      // Others?  
      doc = iframe.document;  
    if(doc == null)  
      throw "Document not initialized";  

    flash='<object data="./images/icons/chat_sound.swf" type="application/x-shockwave-flash" width="1" height="1" style="visibility:hidden"><param name="movie" value="./images/icons/chat_sound.swf" /><param name="menu" value="false" /><param name="quality" value="high" /></object>';
    doc.open();  
    doc.write(flash);  
    doc.close();  

}

function chat_clear() {

  // CLEAR INPUT BOX
  setTimeout("document.getElementById('chat_msg').value=''", 100);

  setTimeout(submit_toggle, 100);

  // RE-ENABLE INPUT BOX AFTER A BIT
  setTimeout("document.getElementById('chat_status').value=''", 1000);

}


// TIMESTAMP FUNCTIONS
function timeAddZeros(i) {
  if(i < 10) {
    i="0" + i;
  }
  return i;
}
function timeConvertHours(h) {
  if(h >= 12) {
    h = h - 12;
  }
  return h;
}


function chat_addmsg() {

  // GET CHAT MESSAGE
  var chat_msg = document.getElementById('chat_msg');
  var chatMsg = chat_msg.value;

  // IF CHAT MESSAGE BLANK, DO NOTHING
  if(chatMsg == '') {
    return false;

  // SEND MESSAGE
  } else {

    // SET CHAT STATUS TO SENDING
    document.getElementById('chat_status').value = 'sending';

    // DISABLE INPUT BOX WHILE MESSAGE IS PROCESSED
    document.getElementById('chat_button_submit').disabled = true;
    document.getElementById('chat_button_submit').className = "chat_submit2";

    // REPLACE HTMLSPECIALCHARS
    chatMsg = chatMsg.replace(/&/g, "&amp;");
    chatMsg = chatMsg.replace(/</g, "&lt;");
    chatMsg = chatMsg.replace(/>/g, "&gt;");
    chatMsg = chatMsg.replace(/"/g, "&quot;");
    chatMsg = chatMsg.replace(/'/g, "&#039;");

    // ADD STYLES TO MESSAGE
    if(chat_msg.style.fontWeight == "bold") {
      chatMsg = "<b>" + chatMsg + "</b>";
    }
    if(chat_msg.style.fontStyle == "italic") {
      chatMsg = "<i>" + chatMsg + "</i>";
    }
    if(chat_msg.style.textDecoration == "underline") {
      chatMsg = "<u>" + chatMsg + "</u>";
    }
    if(colorChanged == 1) {
      chatMsg = "<font style='color:"+ chat_msg.style.color +"'>" + chatMsg;
    }

    // REPLACE SMILIES
    var chatMsg_withsmilies = chat_replacesmilies(chatMsg);

    // CREATE TIMESTAMP
    var now = new Date();
    var today = new Date(now.getTime()+time_diff);
    var h=today.getHours();
    var m=today.getMinutes();
    var s=today.getSeconds();
    h = timeConvertHours(h);
    m = timeAddZeros(m);
    s = timeAddZeros(s);
    if(h < 12) { var ampm = "am"; } else { var ampm = "pm"; }
    var timestamp=h+":"+m+":"+s+" "+ampm;
  
    // ADD TIMESTAMP STYLE
    var message_style = '';
    if(chat_timestamp_on == 1) {
      message_style = 'display: inline;';
    }
  
    // ADD MESSAGE
    var message_id = Math.random() * 100000000;
    message_id = message_id.round();
    document.getElementById('chat_content').innerHTML = document.getElementById('chat_content').innerHTML + '<div class="chat_message" id="message' + message_id + '"><span class="chat_timestamp" style="' + message_style + '">(' + timestamp + ') </span>{/literal}<b>{$user->user_displayname_short}</b>: {literal}' + chatMsg_withsmilies + '</div>';
    message_id = "message" + message_id;
  
    // PLAY AUDIO IF TURNED ON
    var chat_audio_icon = document.getElementById('chat_audio_icon');
    if(audio_on == 1) {
      button_audio_play();
    }
  
    // SCROLL CHAT AREA DOWN
    var myScroller = new Fx.Scroll('chat_content', {duration: 200, transition: Fx.Transitions.Quad.easeOut});
    myScroller.toBottom();
  
    chat_clear();

    return true;
  }
}

function chat_replacesmilies(theString) {

  // SMILEY ARRAY DEFINED EARLIER IN FILE, LOOP THROUGH IT AND REPLACE SMILEY CODES WITH IMAGES
  for(var i=0, len=smilies.length; i<len; ++i) {
    var smileyCode = ":" + smilies[i] + ":";
    var smileyCodeReplaced = "<img src='./images/smilies_chat/" + smilies[i] + ".png' border='0' class='icon'>";
    theString = theString.replace(smileyCode, smileyCodeReplaced);
  }
  return theString;

}

function chat_noenter() {

  // DONT ALLOW IE TO SUBMIT FORM WITH ENTER KEY FOR A BIT AFTER SUBMISSION
  if(document.getElementById('chat_status').value != '') {
    return !(window.event && window.event.keyCode == 13);
  }
}




// UPDATE THE CHAT
function chat_update() {

  // WAIT A FEW SECONDS, THEN UPDATE CHAT
  setTimeout("chat_update_go()", {/literal}{$setting.setting_chat_update}{literal});

}
function chat_update_go() {

  // GET NEW MESSAGES FROM DATABASE
  ajaxFunction('chat_update');

  // RESET CHAT UPDATE LOOP
  chat_update();
}




// LOGIN TO THE CHAT
function chat_login() {

  // ATTEMPT LOGIN
  setTimeout("ajaxFunction('chat_login');", 1500);

}



// SET NUMBER OF UPDATE ATTEMPTS UNTIL TIMEOUT
var update_attempts_remaining = 10;

// AJAX FUNCTIONS
function ajaxFunction(task) {

  var xmlHttp;

  // Firefox, Opera 8.0+, Safari
  try {
    xmlHttp=new XMLHttpRequest();

  // Internet Explorer
  } catch (e) {
    try {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");

    } catch (e) {
      try {
        xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");

      } catch (e) {
        alert("Your browser does not support AJAX!");
        return false;
      }
    }
  }



  // UPDATE CHAT MESSAGES AND ONLINE USERS
  if(task == "chat_update") {
    xmlHttp.onreadystatechange=function() {
      if(xmlHttp.readyState == 4) {

        // GET OUTPUT ARRAY
	var root = xmlHttp.responseXML.getElementsByTagName('response')[0];
 	var result = root.getElementsByTagName('result')[0].firstChild.nodeValue;
	var onlineusers_num = root.getElementsByTagName('onlineusers_num')[0].firstChild.nodeValue;
	var onlineusers = root.getElementsByTagName('onlineusers')[0].getElementsByTagName('user');
	var messages = root.getElementsByTagName('messages')[0].getElementsByTagName('message');

	// UPDATE SUCCESSFUL
	if(result == "success") {

          // RETRIEVE CHAT MESSAGES, REPLACE SMILIES, UPDATE CHAT BOX
	  var author, time, content, output_content = "", today, h, m, s, ampm, newupdates;
	  for(var counter=0;counter<messages.length;counter++) {
	    // GET MESSAGE ID AND COMPARE TO MAX MESSAGE ID
	    message_id = messages[counter].getElementsByTagName('id')[0].firstChild.nodeValue;
	    if(message_id > max_message_id) {
	   
	      // CREATE TIMESTAMP
	      time =  messages[counter].getElementsByTagName('time')[0].firstChild.nodeValue;
	      today = new Date(time*1000);
	      h=today.getHours();
	      m=today.getMinutes();
	      s=today.getSeconds();
	      h = timeConvertHours(h);
	      m = timeAddZeros(m);
	      s = timeAddZeros(s);
	      if(h < 12) { ampm = "am"; } else { ampm = "pm"; }
	      time=h+":"+m+":"+s+" "+ampm;

	      // GET CONTENT AND CREATE HTML OUTPUT
	      content = chat_replacesmilies(messages[counter].getElementsByTagName('content')[0].firstChild.nodeValue);
	      if(messages[counter].getElementsByTagName('author')[0].firstChild) {
	        newupdates = 1;
                author = messages[counter].getElementsByTagName('author')[0].firstChild.nodeValue;
	        output_content += "<div class='chat_message'><span class='chat_timestamp'";
	        if(chat_timestamp_on == 1) { output_content += " style='display: inline;'"; }
	        output_content += ">("+time+") </span><b>"+author+":</b> "+content+"</div>";
	      } else {
	        newupdates = 1;
	        output_content += "<div class='chat_message' style='font-style: italic;'><span class='chat_timestamp'";
	        if(chat_timestamp_on == 1) { output_content += " style='display: inline;'"; }
	        output_content += ">("+time+") </span>"+content+"</div>";
	      }
	      max_message_id = message_id;
	    }
          }
	  if(newupdates == 1) {
            document.getElementById('chat_content').innerHTML += output_content;
	  }

          // SCROLL CHAT AREA DOWN
	  if(newupdates == 1) {
            var myScroller = new Fx.Scroll('chat_content', {duration: 200, transition: Fx.Transitions.Quad.easeOut});
            myScroller.toBottom();
	  }

    // UPDATE ONLINE USERS (CLEAR FIRST)
	  var username = "";
	  var displayname = "";
	  var photo = "";
	  var output_users = "";
	  for(var counter=0;counter<onlineusers.length;counter++)
    {
      username = onlineusers[counter].getElementsByTagName('username')[0].firstChild.nodeValue;
      displayname = onlineusers[counter].getElementsByTagName('displayname')[0].firstChild.nodeValue;
	    output_users += "<div class='chat_onlineuser' {/literal}{if $setting.setting_chat_showphotos == 1}style='text-align: center;'{/if}{literal}><a href='profile.php?user="+username+"' target='_blank'>";
	    {/literal}{if $setting.setting_chat_showphotos == 1}{literal}
	      output_users += "<img src='";
	      if(onlineusers[counter].getElementsByTagName('photo')[0].firstChild) {
	        photo =  onlineusers[counter].getElementsByTagName('photo')[0].firstChild.nodeValue;
		output_users += photo;
	      } else {
		output_users += "./images/nophoto.gif";
	      }
	      output_users += "' width='70' border='0' class='photo'><br>";
	    {/literal}{/if}{literal} 
	    if(displayname.length > 15) { displayname = displayname.substr(0, 12)+'...'; }
	    output_users += displayname+"</a><img src='./images/trans.gif' border='0'></div>";
          }

          if(document.getElementById('chat_onlineusers').innerHTML != output_users) {
            document.getElementById('chat_onlineusers').innerHTML = output_users;
	  }

	  // UPDATE NUMBER OF ONLINE USERS TEXT
	  onlineusers_num++; // ADD 1 SINCE WE ARE ALREADY INCLUDED IN THE LIST
	  if(onlineusers_num == "1") {
	    document.getElementById('chat_onlineusers_header').innerHTML = "{/literal}{lang_sprintf id=3500015 1='" + onlineusers_num + "'}{literal}";
	  } else {
	    document.getElementById('chat_onlineusers_header').innerHTML = "{/literal}{lang_sprintf id=3500016 1='" + onlineusers_num + "'}{literal}";
	  }

          // PLAY AUDIO IF TURNED ON AND NEW MESSAGES ARRIVED
	  if(messages.length != 0) {
            var chat_audio_icon = document.getElementById('chat_audio_icon');
            if(chat_audio_icon.src ==  audioImage2.src) {
              button_audio_play();
            }
	  }

	  // RESET NUMBER OF UPDATE ATTEMPTS REMAINING
	  update_attempts_remaining = 10;

	// USER WAS PROBABLY KICKED
	} else if(result == "dropped") {

	  document.getElementById('chat_curtain').style.display = "block";
	  document.getElementById('chat_curtain').innerHTML = "{/literal}{lang_print id=3500017}{literal}";

	// UPDATE FAILED FOR SOME REASON, SO TRY AGAIN 5 TIMES BEFORE SHOWING CURTAIN
	} else {

	  // SUBTRACT ONE UPDATE ATTEMPT REMAINING
	  if(update_attempts_remaining > 0) {
	    update_attempts_remaining = update_attempts_remaining - 1;
	  }

	  // IF THERE ARE NO ATTEMPTS LEFT, SHOW CURTAIN BUT CONTINUE TRYING TO UPDATE
	  if(update_attempts_remaining == 0) {
	    document.getElementById('chat_curtain').style.display = "block";
	    document.getElementById('chat_curtain').innerHTML = "{/literal}{lang_print id=3500018}{literal}";
	  }

	}
      }
    }
    var ajax_url = "chat_frame.php?task=chat_update&nocache=" + new Date().getTime();
    xmlHttp.open("GET",ajax_url,true);
    xmlHttp.send(null);
  }



  // LOGIN TO CHAT
  if(task == "chat_login") {
    xmlHttp.onreadystatechange=function() {
      if(xmlHttp.readyState == 4) {

        // LOGIN SUCCESSFUL
        if(xmlHttp.responseText == "success") {

	  // HIDE CURTAIN
	  document.getElementById('chat_curtain').style.display = 'none';

          // CLEAR CHAT AREA JUST IN CASE PAGE IS CACHED
          document.getElementById('chat_content').innerHTML = "";

          // FOCUS ON MESSAGE INPUT FIELD
          document.getElementById('chat_msg').focus();

          // BEGIN CHAT UPDATES
          chat_update_go();

	// LOGIN FAILED BECAUSE USER HAS BEEN KICKED
	} else if(xmlHttp.responseText == "kicked") {

	  document.getElementById('chat_curtain').innerHTML = "{/literal}{lang_print id=3500019}{literal}";

	// LOGIN FAILED BECAUSE USER HAS BEEN BANNED
	} else if(xmlHttp.responseText == "banned") {

	  document.getElementById('chat_curtain').innerHTML = "{/literal}{lang_print id=3500020}{literal}";

	// LOGIN FAILED BECAUSE CHAT HAS BEEN DISABLED BY THE ADMIN
	} else if(xmlHttp.responseText == "chatoff") {

	  document.getElementById('chat_curtain').innerHTML = "{/literal}{lang_print id=3500021}{literal}";

	// LOGIN FAILED FOR SOME REASON
	} else {

	  document.getElementById('chat_curtain').innerHTML = "{/literal}{lang_print id=3500022}{literal}";

	}

      }
    }
    var ajax_url = "chat_frame.php?task=chat_login&nocache=" + new Date().getTime();
    xmlHttp.open("GET",ajax_url,true);
    xmlHttp.send(null);
  }





}


//-->
</script>
{/literal}

</body>
</html>