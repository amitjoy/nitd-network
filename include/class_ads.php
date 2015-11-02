<?php

//  THIS CLASS IS USED TO DISPLAY AND MANAGE AD CAMPAIGN BANNERS
//  METHODS IN THIS CLASS:
//    se_ads()
//    ad_display()





class se_ads
{
	var $ad_top; 			// VARIABLE REPRESENTING PAGE TOP BANNER HTML
	var $ad_belowmenu; 		// VARIABLE REPRESENTING BELOW MENU BANNER HTML
	var $ad_left; 			// VARIABLE REPRESENTING LEFT SIDE BANNER HTML
	var $ad_right; 			// VARIABLE REPRESENTING RIGHT SIDE BANNER HTML
	var $ad_bottom; 		// VARIABLE REPRESENTING PAGE BOTTOM BANNER HTML
	var $ad_feed;	 		// VARIABLE REPRESENTING ACTIVITY FEED BANNER HTML
	var $ad_custom;			// VARIABLE REPRESENTING AN ARRAY OF CUSTOM BANNER HTML



	// THIS METHOD IS USED TO DETERMINE WHAT ADS SHOULD BE SHOWN ON THE PAGE
	// THIS ONLY INCLUDES AD CAMPAIGNS THAT HAVE BEEN GIVEN A POSITION BY THE ADMIN
	// OUTPUT: AD BANNER HTML (IF AVAILABLE) FOR PAGE TOP, BELOW MENU, LEFT, RIGHT, AND BOTTOM
	//function se_ads() {
	function load()
  {
	  global $database, $datetime, $setting, $user;
    
	  // GET CURRENT TIME IN ADMINS TIMEZONE
	  $nowtime = time();
    
	  // BEGIN BUILDING AD QUERY 
	  $ad_querystring = "SELECT ad_id, ad_position, ad_html FROM se_ads WHERE ad_date_start<'{$nowtime}' AND (ad_date_end>'{$nowtime}' OR ad_date_end='0')";
    
	  // MAKE SURE AD IS NOT PAUSED
	  $ad_querystring .= " AND ad_paused!='1'";
    
	  // MAKE SURE AD HAS NOT REACHED ITS VIEW LIMIT
	  $ad_querystring .= " AND (ad_limit_views=0 OR ad_limit_views>ad_total_views)";
    
	  // MAKE SURE AD HAS NOT REACHED ITS CLICK LIMIT
	  $ad_querystring .= " AND (ad_limit_clicks=0 OR ad_limit_clicks>ad_total_clicks)";
    
	  // MAKE SURE AD HAS NOT REACHED ITS CTR LIMIT
    $ad_querystring .= " AND (ad_limit_ctr=0 OR ad_limit_ctr<(ad_total_clicks/(ad_total_views+1))*100)";
    
	  // IF VIEWER IS NOT LOGGED-IN, ONLY SHOW PUBLIC AD CAMPAIGNS
    if( !$user->user_exists )
    {
	    $ad_querystring .= " AND ad_public='1'";
    }
    
	  // IF VIEWER IS LOGGED-IN, ONLY SHOW AD IF VIEWER'S LEVEL AND SUBNETS MATCH
	  else
    { 
	    $level_id = $user->level_info['level_id'];
	    $subnet_id = $user->subnet_info['subnet_id'];
	    $ad_querystring .= " AND (ad_levels LIKE '%,{$level_id},%' AND ad_subnets LIKE '%,{$subnet_id},%')";
	  }
    
	  // RANDOMIZE QUERY RESULTS
	  $ad_querystring .= " ORDER BY RAND()";
    
	  // DETERMINE WHICH ADS SHOULD BE SHOWN
	  $ad_query = $database->database_query($ad_querystring);
    
	  // PREPARE STAT UPDATE QUERY
	  $stats_id_array = array();
    
	  // SET AD HTML FOR EACH POSITION
	  while( $ad_info = $database->database_fetch_assoc($ad_query) )
    {
	    // CONVERT TO HTML AND ADD CLICK-TRACKING JAVASCRIPT
	    $ad_info['ad_html'] = htmlspecialchars_decode($ad_info['ad_html'], ENT_QUOTES);
	    $ad_info['ad_html'] = "<div onClick=\"document.getElementById('doclickimage{$ad_info['ad_id']}').src='ad.php?ad_id={$ad_info['ad_id']}';\">{$ad_info['ad_html']}<img src='images/trans.gif' border='0' id='doclickimage{$ad_info['ad_id']}' style='display: none;'></div>";
      
	    $this->ad_custom[$ad_info['ad_id']] = $ad_info['ad_html'];
      
	    if( $ad_info['ad_position'] == "top" && !$this->ad_top )
      {
        $this->ad_top = $ad_info['ad_html'];
        $stats_id_array[] = $ad_info['ad_id'];
	    }
	    elseif( $ad_info['ad_position'] == "belowmenu" && !$this->ad_belowmenu )
      {
	      $this->ad_belowmenu = $ad_info['ad_html'];
        $stats_id_array[] = $ad_info['ad_id'];
	    }
	    elseif( $ad_info['ad_position'] == "left" && !$this->ad_left )
      {
        $this->ad_left = $ad_info['ad_html'];
        $stats_id_array[] = $ad_info['ad_id'];
	    }
	    elseif( $ad_info['ad_position'] == "right" && !$this->ad_right )
      {
        $this->ad_right = $ad_info['ad_html'];
        $stats_id_array[] = $ad_info['ad_id'];
	    }
	    elseif( $ad_info['ad_position'] == "feed" && !$this->ad_feed )
      {
        $this->ad_feed = $ad_info['ad_html'];
        $stats_id_array[] = $ad_info['ad_id'];
	    }
	    elseif( $ad_info['ad_position'] == "bottom" && !$this->ad_bottom )
      {
        $this->ad_bottom = $ad_info['ad_html'];
        $stats_id_array[] = $ad_info['ad_id'];
	    }
	  }
    
	  // UPDATE THE ADS VIEW STATS
    if( !empty($stats_id_array) )
    {
      $database->database_query("UPDATE se_ads SET ad_total_views=ad_total_views+1 WHERE ad_id IN('".join("', '", $stats_id_array)."')");
	  }
	}
  
  // END se_ads() METHOD






	// THIS METHOD IS DISPLAYS THE CUSTOM AD AND UPDATES THE VIEWS
	// INPUT: $ad_id REPRESENTING AN AD ID
	// OUTPUT: AD BANNER HTML (IF AVAILABLE) FOR GIVEN AD ID
  
	function ads_display($ad_id)
  {
	  global $database;
    
	  // UPDATE THE ADS VIEW STATS
    $database->database_query("UPDATE se_ads SET ad_total_views=ad_total_views+1 WHERE ad_id='{$ad_id}' LIMIT 1");
	  
	  // DISPLAY AD
	  return $this->ad_custom[$ad_id];
	}
  
  // END ads_display() METHOD
}

?>