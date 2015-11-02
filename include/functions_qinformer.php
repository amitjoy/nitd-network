<?php

function get_qinformer_settings()
{
	global $database;
	
	$sql = "
		SELECT *
		FROM `se_qinformer_settings`";
	return $database->database_fetch_assoc($database->database_query($sql));
}

?>