<?php

$plugin_name = "Forum Plugin";
$plugin_version = "3.03";
$plugin_type = "forum";
$plugin_desc = "This plugin lets your users interact with each other via threaded forum topics. Users can create new topics, post replies, and attach images or other files. You can also appoint moderators to help manage and organize the discussions. Forums are a great way to generate more return traffic and interactivity within your social network.";
$plugin_icon = "forum_forum16.gif";
$plugin_menu_title = "6000001";
$plugin_pages_main = "6000002<!>forum_forum16.gif<!>admin_forum.php<~!~>6000003<!>forum_settings16.gif<!>admin_forumsettings.php<~!~>";
$plugin_pages_level = "";
$plugin_url_htaccess = "";
$plugin_db_charset = 'utf8';
$plugin_db_collation = 'utf8_unicode_ci';
$plugin_reindex_totals = TRUE;


if($install == "forum")
{
  //######### GET CURRENT PLUGIN INFORMATION
  $sql = "SELECT * FROM se_plugins WHERE plugin_type='$plugin_type' LIMIT 1";
  $resource = $database->database_query($sql) or die($database->database_error()." <b>SQL was: </b>$sql");
 
  $plugin_info = array();
  if( $database->database_num_rows($resource) )
    $plugin_info = $database->database_fetch_assoc($resource);
 

  //######### INSERT ROW INTO se_plugins
  if($database->database_num_rows($database->database_query("SELECT plugin_id FROM se_plugins WHERE plugin_type='$plugin_type'")) == 0) {
    $database->database_query("INSERT INTO se_plugins (plugin_name,
					plugin_version,
					plugin_type,
					plugin_desc,
					plugin_icon,
					plugin_menu_title,
					plugin_pages_main,
					plugin_pages_level,
					plugin_url_htaccess
					) VALUES (
					'$plugin_name',
					'$plugin_version',
					'$plugin_type',
					'".str_replace("'", "\'", $plugin_desc)."',
					'$plugin_icon',
					'$plugin_menu_title',
					'$plugin_pages_main',
					'$plugin_pages_level',
					'$plugin_url_htaccess')");


  //######### UPDATE PLUGIN VERSION IN se_plugins
  } else {
    $database->database_query("UPDATE se_plugins SET plugin_name='$plugin_name',
					plugin_version='$plugin_version',
					plugin_desc='".str_replace("'", "\'", $plugin_desc)."',
					plugin_icon='$plugin_icon',
					plugin_menu_title='$plugin_menu_title',
					plugin_pages_main='$plugin_pages_main',
					plugin_pages_level='$plugin_pages_level',
					plugin_url_htaccess='$plugin_url_htaccess' WHERE plugin_type='$plugin_type'");

  }



  //######### CREATE se_forumcats
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_forumcats'")) == 0) {
    $database->database_query("CREATE TABLE `se_forumcats` (
    `forumcat_id` int(9) NOT NULL auto_increment,
    `forumcat_order` SMALLINT UNSIGNED NOT NULL default 0,
    `forumcat_title` INT UNSIGNED NOT NULL default 0,
    PRIMARY KEY  (`forumcat_id`)
    )");
  }



  //######### CREATE se_forummedia
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_forummedia'")) == 0) {
    $database->database_query("
      CREATE TABLE `se_forummedia` (
        `forummedia_id`             INT           UNSIGNED  NOT NULL auto_increment,
        `forummedia_forumtopic_id`  INT           UNSIGNED  NOT NULL default '0',
        `forummedia_ext`            VARCHAR(8)              NOT NULL default '',
        `forummedia_filesize`       INT           UNSIGNED  NOT NULL default '0',
        PRIMARY KEY  (`forummedia_id`),
        KEY `INDEX` (`forummedia_forumtopic_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ");
  }



  //######### CREATE se_forumlevels
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_forumlevels'")) == 0) {
    $database->database_query("
      CREATE TABLE `se_forumlevels` (
        `forumlevel_forum_id`  INT           UNSIGNED  NOT NULL default '0',
        `forumlevel_level_id`  INT           UNSIGNED  NOT NULL default '0',
	`forumlevel_post`  TINYINT(1)  UNSIGNED  NOT NULL default '0',
        UNIQUE KEY `unique` (`forumlevel_forum_id`, `forumlevel_level_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ");
  }


  //######### CREATE se_forumlogs
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_forumlogs'")) == 0) {
    $database->database_query("
      CREATE TABLE `se_forumlogs` (
        `forumlog_user_id`         int(9)                NOT NULL default '0',
        `forumlog_forumtopic_id`   int(9)                NOT NULL default '0',
        `forumlog_date`  int(14) NOT NULL default '0',
        UNIQUE KEY `unique` (`forumlog_user_id`, `forumlog_forumtopic_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ");
  }



  //######### CREATE se_forummoderators
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_forummoderators'")) == 0) {
    $database->database_query("
      CREATE TABLE `se_forummoderators` (
        `forummoderator_forum_id`  INT           UNSIGNED  NOT NULL default '0',
        `forummoderator_user_id`  INT           UNSIGNED  NOT NULL default '0',
        UNIQUE KEY `unique` (`forummoderator_forum_id`, `forummoderator_user_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ");
  }
  


  //######### CREATE se_forumposts
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_forumposts'")) == 0) {
    $database->database_query("
      CREATE TABLE `se_forumposts` (
        `forumpost_id` int(9) NOT NULL auto_increment,
        `forumpost_forumtopic_id` int(9) NOT NULL default '0',
        `forumpost_authoruser_id` int(9) NOT NULL default '0',
        `forumpost_date` int(14) NOT NULL default '0',
        `forumpost_excerpt` varchar(100) NOT NULL default '',
        `forumpost_body` text NULL,
        `forumpost_forummedia_id` int(9) NOT NULL default '0',
	`forumpost_deleted` int(1) NOT NULL default '0',
        PRIMARY KEY  (`forumpost_id`),
        KEY `INDEX` (`forumpost_forumtopic_id`,`forumpost_authoruser_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ");
  }

    
  
  //######### CREATE se_forums
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_forums'")) == 0) {
    $database->database_query("
      CREATE TABLE `se_forums` (
        `forum_id`            INT           UNSIGNED  NOT NULL auto_increment,
        `forum_forumcat_id`   INT           UNSIGNED  NOT NULL default '0',
	`forum_order`	 SMALLINT UNSIGNED NOT NULL default 0,
        `forum_title`         INT 	    UNSIGNED NOT NULL default 0,
        `forum_desc`          INT 	    UNSIGNED NOT NULL default 0,
        `forum_totaltopics` SMALLINT      UNSIGNED  NOT NULL default 0,
        `forum_totalreplies`  SMALLINT      UNSIGNED  NOT NULL default 0,
        PRIMARY KEY  (`forum_id`),
        KEY `INDEX` (`forum_forumcat_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ");
  }



  //######### CREATE se_forumtopics
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_forumtopics'")) == 0) {
    $database->database_query("
      CREATE TABLE `se_forumtopics` (
        `forumtopic_id`             int(9)                NOT NULL auto_increment,
        `forumtopic_forum_id`       int(9)                NOT NULL default '0',
        `forumtopic_creatoruser_id` int(9)                NOT NULL default '0',
        `forumtopic_date`           int(14)               NOT NULL default '0',
        `forumtopic_subject`        varchar(50)           NOT NULL default '',
        `forumtopic_excerpt`        varchar(100)          NOT NULL default '',
        `forumtopic_views`          int(9)                NOT NULL default '0',
        `forumtopic_sticky`         TINYINT(1)  UNSIGNED  NOT NULL default '0',
        `forumtopic_closed`         TINYINT(1)  UNSIGNED  NOT NULL default '0',
        `forumtopic_totalreplies`     SMALLINT    UNSIGNED  NOT NULL default '0',
        PRIMARY KEY  (`forumtopic_id`),
        KEY `INDEX` (`forumtopic_forum_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ");
  }



  //######### CREATE se_forumusers
  if($database->database_num_rows($database->database_query("SHOW TABLES FROM `$database_name` LIKE 'se_forumusers'")) == 0) {
    $database->database_query("
      CREATE TABLE `se_forumusers` (
        `forumuser_user_id`         int(9)                NOT NULL default '0',
        `forumuser_totalposts`     SMALLINT    UNSIGNED  NOT NULL default '0',
        PRIMARY KEY  (`forumuser_user_id`)
      )
      CHARACTER SET {$plugin_db_charset} COLLATE {$plugin_db_collation}
    ");
  }
  
  
  //######### INSERT se_actiontypes
  $actiontypes = array();
  if(!$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='forumtopic'"))) {
    $database->database_query("INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('forumtopic', 'forum_action_topic16.gif', '1', '1', '6000127', '6000128', '[username],[displayname],[forumid],[forumname],[topicid],[topicname],[postbody]', '1')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  
  if( !$database->database_num_rows($database->database_query("SELECT actiontype_id FROM se_actiontypes WHERE actiontype_name='forumpost'")) )
  {
    $database->database_query("
      INSERT INTO se_actiontypes
        (actiontype_name, actiontype_icon, actiontype_setting, actiontype_enabled, actiontype_desc, actiontype_text, actiontype_vars, actiontype_media)
      VALUES
        ('forumpost', 'forum_action_reply16.gif', '1', '1', '6000129', '6000130', '[username],[displayname],[forumid],[topicid],[topicname],[postid],[postbody]', '1')
    ");
    $actiontypes[] = $database->database_insert_id();
  }
  
  $actiontypes = array_filter($actiontypes);
  if( !empty($actiontypes) )
  {
    $database->database_query("UPDATE se_usersettings SET usersetting_actions_display = CONCAT(usersetting_actions_display, ',', '".implode(",", $actiontypes)."')");
  }

    
  //######### INSERT se_notifytypes
  if( !$database->database_num_rows($database->database_query("SELECT notifytype_id FROM se_notifytypes WHERE notifytype_name='forumreply'")) )
  {
    $database->database_query("
      INSERT INTO se_notifytypes
        (notifytype_name, notifytype_desc, notifytype_icon, notifytype_url, notifytype_title)
      VALUES
        ('forumreply', '6000136', 'forum_action_reply16.gif', 'forum_topic.php?forum_id=%1\$s&topic_id=%2\$s&post_id=%3\$s#post_%3\$s', '6000137')
    ");
  }

  //######### ADD COLUMNS/VALUES TO SYSTEM EMAILS TABLE
  if( !$database->database_num_rows($database->database_query("SELECT systememail_id FROM se_systememails WHERE systememail_name='forumreply'")) )
  {
    $database->database_query("
      INSERT INTO se_systememails
        (systememail_name, systememail_title, systememail_desc, systememail_subject, systememail_body, systememail_vars)
      VALUES
        ('forumreply', '6000138', '6000139', '6000140', '6000141', '[displayname],[commenter],[topicname],[link]')
    ");
  }

  
  //######### ADD COLUMNS/VALUES TO SETTINGS TABLE
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_settings` LIKE 'setting_permission_forum'")) == 0) {
    $database->database_query("ALTER TABLE se_settings 
					ADD COLUMN `setting_permission_forum` int(1) NOT NULL default '1',
					ADD COLUMN `setting_forum_code` int(1) NOT NULL default '1',
					ADD COLUMN `setting_forum_status` int(1) NOT NULL default '1',
					ADD COLUMN `setting_forum_modprivs` varchar(10) NOT NULL default '11111'");
  }

   //######### ADD COLUMNS/VALUES TO USER SETTINGS TABLE
  if($database->database_num_rows($database->database_query("SHOW COLUMNS FROM `$database_name`.`se_usersettings` LIKE 'usersetting_notify_forumreply'")) == 0) {
    $database->database_query("ALTER TABLE se_usersettings 
					ADD COLUMN `usersetting_notify_forumreply` int(1) NOT NULL default '1'");
  }
  
  
  //######### INSERT LANGUAGE VARS
  if( !$database->database_num_rows($database->database_query("SELECT languagevar_id FROM se_languagevars WHERE languagevar_language_id=1 && languagevar_id=6000001 LIMIT 1")) )
  {

    $database->database_query("INSERT INTO `se_languagevars` 
(`languagevar_id`, `languagevar_language_id`, `languagevar_value`, `languagevar_default`) VALUES 
(6000001, 1, 'Forum Settings', NULL),
(6000002, 1, 'Forum Manager', NULL),
(6000003, 1, 'Forum Settings', NULL),
(6000004, 1, 'Below, you can manage your forums, assign moderators, and restrict categories to certain user levels. Additional forum settings can be modified on the <a href=\'admin_forumsettings.php\'>Forum Settings</a> page. Please note that when you add a new <a href=\'admin_levels.php\'>user level</a>, you will need to return to this page to assign forum access permissions to the new user level for each forum.', NULL),
(6000005, 1, 'Add Category', NULL),
(6000006, 1, 'Add Forum', NULL),
(6000007, 1, 'Name', NULL),
(6000008, 1, 'Moderators:', NULL),
(6000009, 1, 'Please provide a title for this forum category.', NULL),
(6000010, 1, 'Are you sure you want to delete this category? All forums, along with all the topics and posts contained within those forums, within this category will also be deleted!', NULL),
(6000011, 1, 'Add Forum', NULL),
(6000012, 1, 'Edit Forum', NULL),
(6000013, 1, 'Are you sure you want to delete this forum? All the topics and posts contained within this forum will also be deleted!', NULL),
(6000014, 1, 'Delete Forum', NULL),
(6000015, 1, 'Please specify a forum title.', NULL),
(6000016, 1, 'Please provide some information about this forum.', NULL),
(6000017, 1, 'Forum:', NULL),
(6000018, 1, 'Description:', NULL),
(6000019, 1, 'None', NULL),
(6000020, 1, 'manage', NULL),
(6000021, 1, 'Manage Moderators', NULL),
(6000022, 1, 'Update Moderators', NULL),
(6000023, 1, 'Use the form below to manage your moderators for this forum. Un-check the checkbox beside a user\'s name to remove that moderator from this forum.', NULL),
(6000024, 1, 'To add a moderator, please search for their name or username below.', NULL),
(6000025, 1, 'Moderator', NULL),
(6000026, 1, 'There are no moderators currently assigned to this forum.', NULL),
(6000027, 1, 'Search', NULL),
(6000028, 1, 'Which <a href=\'admin_levels.php\' target=\'_blank\'>User Levels</a> can see this forum?', NULL),
(6000029, 1, 'Which <a href=\'admin_levels.php\' target=\'_blank\'>User Levels</a> can post in this forum?', NULL),
(6000030, 1, 'Global Forum Settings', NULL),
(6000031, 1, 'This page contains general forum settings that affect your entire social network.', NULL),
(6000032, 1, 'Select whether or not you want to let the public (visitors that are not logged-in) to view the forums. Please note that you can make individual forums private on the <a href=\'admin_forum.php\'>Forum Manager</a> page. For more permissions settings, please visit the <a href=\'admin_general.php\'>General Settings</a> page.', NULL),
(6000033, 1, 'Yes, the public can view the forums unless they are made private.', NULL),
(6000034, 1, 'No, the public cannot view the forums.', NULL),
(6000035, 1, 'Forum Status', NULL),
(6000036, 1, 'By toggling this setting, you can turn your forums on and off or put them into maintenance mode. If the forums are turned on, they will be visible to all those who have permission to view them. When turned off, no one will be able to access the forums. When the forums are in maintenance mode, only moderators will be allowed to view and make changes to the forums.', NULL),
(6000037, 1, 'Forums are <b>ON</b>.', NULL),
(6000038, 1, 'Forums are in <b>MAINTENANCE MODE</b>.', NULL),
(6000039, 1, 'Forums are <b>OFF</b>.', NULL),
(6000040, 1, 'Moderator Privileges', NULL),
(6000041, 1, 'This setting allows you to define what actions moderators are allowed to perform within the forums they moderate.', NULL),
(6000042, 1, 'Moderators can:', NULL),
(6000043, 1, 'Edit Topics and Posts', NULL),
(6000044, 1, 'Delete Topics and Posts', NULL),
(6000045, 1, 'Move Topics', NULL),
(6000046, 1, 'Close/Open Topics', NULL),
(6000047, 1, 'Stick/Unstick Topics', NULL),
(6000048, 1, 'Require users to enter validation code when starting or posting in a forum topic?', NULL),
(6000049, 1, 'If you have selected Yes, an image containing a random sequence of 6 numbers will be shown to users on the \"start a topic\" and \"post topic reply\" page. Users will be required to enter these numbers into the Verification Code field in order to post their topic/reply. This feature helps prevent users from trying to create forum spam. For this feature to work properly, your server must have the GD Libraries (2.0 or higher) installed and configured to work with PHP. If you are seeing errors, try turning this off.', NULL),
(6000050, 1, 'Yes, enable validation code for forum topics.', NULL),
(6000051, 1, 'No, disable validation code for forum topics.', NULL),
(6000052, 1, 'HTML in Forum Posts', NULL),
(6000053, 1, 'By default, the user may not enter any HTML tags into forum posts. If you want to allow specific tags, you can enter them below (separated by commas). Example: <i>b, img, a, embed, font</i>', NULL),
(6000054, 1, 'Please specify a search query.', NULL),
(6000055, 1, 'No User Level (Unregistered Users)', NULL),
(6000056, 1, 'Forums', NULL),
(6000057, 1, 'Forum', NULL),
(6000058, 1, 'Topics', NULL),
(6000059, 1, 'Replies', NULL),
(6000060, 1, 'Last Post', NULL),
(6000061, 1, 'Discussion Forums', NULL),
(6000062, 1, 'Post New Topic', NULL),
(6000063, 1, 'Topic', NULL),
(6000064, 1, 'Views', NULL),
(6000065, 1, 'New Topic', NULL),
(6000066, 1, 'Please enter a title for your topic.', NULL),
(6000067, 1, 'Please enter text for your post.', NULL),
(6000068, 1, 'No topics have been posted in this forum.', NULL),
(6000069, 1, '<a href=\'forum_new.php?forum_id=%1\$s\'>Click here</a> to be the first!', NULL),
(6000070, 1, 'Forum', NULL),
(6000071, 1, 'Post Reply', NULL),
(6000072, 1, 'Moderate Topic:', NULL),
(6000073, 1, 'Open', NULL),
(6000074, 1, 'Close', NULL),
(6000075, 1, 'Delete', NULL),
(6000076, 1, 'Move', NULL),
(6000077, 1, 'Un-Stick', NULL),
(6000078, 1, 'Make Sticky', NULL),
(6000079, 1, 'Total posts: %1\$s', NULL),
(6000080, 1, 'Joined: %1\$s', NULL),
(6000081, 1, 'Posted %1\$s', NULL),
(6000082, 1, 'Moderator', NULL),
(6000083, 1, 'This post has been deleted.', NULL),
(6000084, 1, 'Quick Reply', NULL),
(6000085, 1, 'Post Reply', NULL),
(6000086, 1, 'Attach Image:', NULL),
(6000087, 1, 'Post Topic', NULL),
(6000088, 1, 'Title:', NULL),
(6000089, 1, 'Reply', NULL),
(6000090, 1, 'Text:', NULL),
(6000091, 1, 'or', NULL),
(6000092, 1, 'This image will appear at the beginning of your post. Images must be less than 2000KB in size and must have one of the following extensions: jpg, jpeg, gif, png, bmp.', NULL),
(6000093, 1, 'Moderated by:', NULL),
(6000094, 1, 'Never', NULL),
(6000095, 1, 'by %1\$s', NULL),
(6000096, 1, 'Delete Topic', NULL),
(6000097, 1, 'Are you sure you want to delete this topic?', NULL),
(6000098, 1, 'Close Topic', NULL),
(6000099, 1, 'Open Topic', NULL),
(6000100, 1, 'Are you sure you want to close this topic? Users will no longer be able to post replies.', NULL),
(6000101, 1, 'Close', NULL),
(6000102, 1, 'Are you sure you want to re-open this topic?', NULL),
(6000103, 1, 'Open', NULL),
(6000104, 1, 'Stick Topic', NULL),
(6000105, 1, 'Un-Stick Topic', NULL),
(6000106, 1, 'Are you sure you want to make this topic sticky?', NULL),
(6000107, 1, 'Stick', NULL),
(6000108, 1, 'Are you sure you want to un-stick this topic?', NULL),
(6000109, 1, 'Un-Stick', NULL),
(6000110, 1, 'Move Topic', NULL),
(6000111, 1, 'Which forum do you want to move this topic to:', NULL),
(6000112, 1, 'Move', NULL),
(6000113, 1, 'The forums are currently in maintenance mode. Only moderators have access to the forums at this time. Because you are a moderator, you can continue to post, edit, and moderate the topics within your designated forums.', NULL),
(6000114, 1, 'The forums are currently down for maintenance. Please come back later.', NULL),
(6000115, 1, 'Forum Posts (%1\$d)', NULL),
(6000116, 1, 'quote', NULL),
(6000117, 1, '%1\$s said:', NULL),
(6000118, 1, 'This topic has been closed.', NULL),
(6000119, 1, 'Deleted User', NULL),
(6000120, 1, 'Are you sure you want to delete this post?', NULL),
(6000121, 1, 'Delete Post', NULL),
(6000122, 1, 'Forum Posts: %1\$d posts', NULL),
(6000123, 1, '[Untitled]', NULL),
(6000124, 1, 'Edit Post', NULL),
(6000125, 1, 'Edit Post', NULL),
(6000126, 1, 'Remove Image', NULL),
(6000127, 1, 'Creating a Forum Topic', NULL),
(6000128, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> posted a new forum topic \"<a href=\"forum_topic.php?forum_id=%3\$s&topic_id=%5\$s\">%6\$s</a>\" in the forum \"<a href=\'forum_view.php?forum_id=%3\$s\'>%4\$s</a>\":<div class=\"recentaction_div\">%7\$s</div>', NULL),
(6000129, 1, 'Replying to a Forum Topic', NULL),
(6000130, 1, '<a href=\"profile.php?user=%1\$s\">%2\$s</a> replied to the forum topic \"<a href=\"forum_topic.php?forum_id=%3\$s&topic_id=%4\$s&post_id=%6\$s#post_%6\$s\">%5\$s</a>\":<div class=\"recentaction_div\">%7\$s</div>', NULL),
(6000131, 1, '%1\$s', NULL),
(6000132, 1, 'Posted in <a href=\'forum_view.php?forum_id=%1\$s\'>%2\$s</a><br>%3\$s', NULL),
(6000133, 1, 'Forum Post in Topic: %1\$s', NULL),
(6000134, 1, '%1\$s', NULL),
(6000135, 1, '%1\$d forum posts', NULL),
(6000136, 1, '%1\$d New Forum Topic Replies: %2\$s', NULL),
(6000137, 1, 'When someone posts in a forum topic I created.', NULL),
(6000138, 1, 'Forum Topic Reply Email', NULL),
(6000139, 1, 'This is the email that gets sent to a user when someone replies to a forum topic they created.', NULL),
(6000140, 1, 'New Reply in Forum Topic: &quot;%3\$s&quot;', NULL),
(6000141, 1, 'Hello %1\$s,<br><br>A new reply has been posted by %2\$s on a forum topic you created. Please click the following link to view it:<br><br>%4\$s<br><br>Best Regards,<br>Social Network Administration', NULL)
    ");

  }

  

}  

?>
