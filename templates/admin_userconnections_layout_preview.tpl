{ *  PREVIEW LAYOUT  * }
{ * $Id: admin_userconnections_layout_preview.tpl 1 2009-09-16 09:36:11Z SocialEngineAddOns $ * }
{ if $preview_number eq '0' }
	<img src='../images/icons/userconnection-sidebar-vertical.jpg' class='icon' border='0' alt="sidebar-vertical" title="sidebar-vertical">
{ elseif $preview_number eq '1' }
	<img src='../images/icons/userconnection-profile-tab.jpg' class='icon' border='0' alt="profile-tab" title="profile-tab">
{ elseif $preview_number eq '2' }
	<img src='../images/icons/userconnection-sidebar-horizental.jpg' class='icon' border='0' alt="sidebar-horizental" title="sidebar-horizental">
{ else }	
	<img src='../images/icons/userconnection-sidebar-vertical-without-images.jpg' class='icon' border='0' alt="sidebar-vertical-without-images" title="sidebar-vertical-without-images">
{ /if }