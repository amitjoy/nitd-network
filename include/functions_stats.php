<?php


//  THIS FILE CONTAINS STAT-RELATED FUNCTIONS
//  FUNCTIONS IN THIS CLASS:
//    update_stats()
//    update_refurls()













// THIS FUNCTION UPDATES THE LATEST ROW IN THE STATS TABLE
// INPUT: $type REPRESENTING WHICH STAT TO INCREMENT
// OUTPUT: 

function update_stats($type)
{
	global $database;

	// INCREASE REQUESTED STAT VALUE
	$database->database_query("
    INSERT INTO se_stats
      (`stat_date`, `stat_{$type}`)
    VALUES
      (UNIX_TIMESTAMP(CURDATE()), 1) 
    ON DUPLICATE KEY UPDATE
      `stat_{$type}`=`stat_{$type}`+1
  ");
}

// END update_stats() FUNCTION









// THIS FUNCTION GETS THE CURRENT VIEWER'S REFERRING URL AND ADDS IT TO REF URL STATS TABLE
// INPUT:
// OUTPUT: 
function update_refurls()
{
	global $database;

	// IF URL IS NOT EMPTY
	$referring_url = $_SERVER["HTTP_REFERER"];
	if(strpos(strtolower($referring_url), strtolower($_SERVER["HTTP_HOST"])) !== FALSE) { return; }

	if( $referring_url )
  {
	  // IS URL ALREADY IN DATABASE? IF YES, ADD TO HITS. IF NO, ADD NEW ROW
	  $referring_url = str_replace("http://www.", "http://", $referring_url);
	  $database->database_query("
      INSERT INTO se_statrefs
        (statref_hits, statref_url)
      VALUES
        ('1', '{$referring_url}')
			ON DUPLICATE KEY UPDATE
        statref_hits=statref_hits+1
    ");
    
	  // IF 1000 ROWS REACHED, DELETE ONE TO MAKE ROOM
	  $refurl_totalrows = $database->database_num_rows($database->database_query("SELECT statref_id FROM se_statrefs"));
    
	  if( $refurl_totalrows > 1000 )
      $database->database_query("DELETE FROM se_statrefs WHERE statref_hits='1' ORDER BY statref_id ASC LIMIT 1");
	}
}

// END update_refurls FUNCTION

?>