<?php

$page = 'video_conversion_cronjob';
include "header.php";


// Use this file to trigger the job queue handler via cronjob.
// Set a password to prevent calls, then use the following example URL to set your cronjob path:
// EX: video_conversion_cronjob.php?password=l53SDj6SGF2nb4
$password = 'l53SDj6SGF2nb4';



if( $_GET['password']==$password )
{
	video_manage_jobs();
}

exit();

?>