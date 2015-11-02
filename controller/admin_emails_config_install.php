<?php


$page = "admin_emails_config_install";
include "admin_header.php";



// SAVE SETTINGS
$query = "CREATE TABLE `se_settings_email` (
  `id` int(11) NOT NULL auto_increment,
  `email_method` varchar(10) default 'smtp',
  `smtp_host` varchar(255) default NULL,
  `smtp_user` varchar(255) default NULL,
  `smtp_pass` varchar(255) default NULL,
  `smtp_port` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1";
$database->database_query($query);
$database->database_query("insert into `se_settings_email`(`id`,`email_method`,`smtp_host`,`smtp_user`,`smtp_pass`,`smtp_port`) values ('1','mail','','','','25')");



include "admin_footer.php";
?>