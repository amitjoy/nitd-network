<?php

//  THIS CLASS CONTAINS DATE/TIME-RELATED METHODS.
//  IT IS USED TO FORMAT TIMESTAMPS
//  METHODS IN THIS CLASS:
//    cdate()
//    untimezone()
//    timezone()
//    time_since()
//    age()





class se_datetime {

	// INITIALIZE VARIABLES
	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT









	// THIS METHOD SETS INITIAL VARS SUCH AS PRELOADING LANG VARS
	// INPUT:  
	// OUTPUT: 
	function se_datetime() {
	  SE_Language::_preload_multi(773, 774, 775, 776, 777, 778, 779);
	} // END se_datetime() METHOD








	// THIS METHOD RETURNS A FORMATTED DATE (MULTILANGUAGE)
	// INPUT: $format REPRESENTING A DATE FORMAT BASED ON THE PHP DATE() FUNCTION FORMAT
	//	  $time (OPTIONAL) REPRESENTING A TIMESTAMP
	// OUTPUT: A STRING REPRESENTING A FORMATTED DATE BASED ON THE GIVEN TIMESTAMP
	function cdate($format, $time = "") {
	  global $multi_language;

	  if($time == "") { $time = time(); }

	  if(!$multi_language) {
	    return date($format, $time);
	  } else {
	    $date_letters = Array("a", "A", "B", "c", "D", "d", "F", "m", "M", "I", "i", "g", "h", "H", "G", "j", "l", "L", "n", "O", "r", "S", "s", "t", "U", "W", "w", "Y", "y", "z", "Z", "T");
	    $strftime_letters = Array("%p", "%p", "", "", "%a", "%d", "%B", "%m", "%b", "", "%M", "%I", "%I", "%H", "%H", "%e", "%A", "", "%m", "", "", "", "%S", "", "", "%V", "%w", "%Y", "%y", "%j", "", "%Z");
	    $new_format = str_replace($date_letters, $strftime_letters, $format);
	    return strftime($new_format, $time);
	  }

	} // END cdate() METHOD








	// THIS METHOD RETURNS A TIMESTAMP IN THE SERVER TIMEZONE
	// INPUT: $time REPRESENTING A TIMESTAMP IN USER'S TIME
	//	  $timezone REPRESENTING A TIMEZONE
	// OUTPUT: A TIMESTAMP IN THE SERVER TIMEZONE
	function untimezone($time, $timezone) {

	  switch($timezone) {
	    case -12: $new_time = $time + 43200; break;
	    case -11: $new_time = $time + 39600; break;
	    case -10: $new_time = $time + 33000; break;
	    case -9: $new_time = $time + 32400; break;
	    case -8: $new_time = $time + 28800; break;
	    case -7: $new_time = $time + 25200; break;
	    case -6: $new_time = $time + 21600; break;
	    case -5: $new_time = $time + 18000; break;
	    case -4: $new_time = $time + 14400; break;
	    case -3.3: $new_time = $time + 11880; break;
	    case -3: $new_time = $time + 10800; break;
	    case -2: $new_time = $time + 7200; break;
	    case -1: $new_time = $time + 3600; break;
	    case 0: $new_time = $time; break;
  	    case 1: $new_time = $time - 3600; break;
  	    case 2: $new_time = $time - 7200; break;
  	    case 3: $new_time = $time - 10800; break;
  	    case 3.3: $new_time = $time - 11880; break;
  	    case 4: $new_time = $time - 14400; break;
  	    case 4.3: $new_time = $time - 15480; break;
  	    case 5: $new_time = $time - 18000; break;
  	    case 5.5: $new_time = $time - 19800; break;
  	    case 6: $new_time = $time - 21600; break;
  	    case 7: $new_time = $time - 25200; break;
  	    case 8: $new_time = $time - 28800; break;
  	    case 9: $new_time = $time - 32400; break;
  	    case 9.3: $new_time = $time - 33480; break;
  	    case 10: $new_time = $time - 33000; break;
  	    case 11: $new_time = $time - 39600; break;
  	    case 12: $new_time = $time - 43200; break;
	  }

	  $new_time = $new_time+(date("Z")-(date("I")*3600));

	  return $new_time;
  
	} // END untimezone() METHOD








	// THIS METHOD RETURNS A TIMESTAMP IN THE CORRECT TIMEZONE
	// INPUT: $time REPRESENTING A TIMESTAMP IN SERVER TIME
	//	  $timezone REPRESENTING A TIMEZONE
	// OUTPUT: A TIMESTAMP IN THE CORRECT TIMEZONE
	function timezone($time, $timezone) {

	  $time = $time-(date("Z")-(date("I")*3600));

	  switch($timezone) {
	    case -12: $new_time = $time - 43200; break;
	    case -11: $new_time = $time - 39600; break;
	    case -10: $new_time = $time - 33000; break;
	    case -9: $new_time = $time - 32400; break;
	    case -8: $new_time = $time - 28800; break;
	    case -7: $new_time = $time - 25200; break;
	    case -6: $new_time = $time - 21600; break;
	    case -5: $new_time = $time - 18000; break;
	    case -4: $new_time = $time - 14400; break;
	    case -3.3: $new_time = $time - 11880; break;
	    case -3: $new_time = $time - 10800; break;
	    case -2: $new_time = $time - 7200; break;
	    case -1: $new_time = $time - 3600; break;
	    case 0: $new_time = $time; break;
  	    case 1: $new_time = $time + 3600; break;
  	    case 2: $new_time = $time + 7200; break;
  	    case 3: $new_time = $time + 10800; break;
  	    case 3.3: $new_time = $time + 11880; break;
  	    case 4: $new_time = $time + 14400; break;
  	    case 4.3: $new_time = $time + 15480; break;
  	    case 5: $new_time = $time + 18000; break;
  	    case 5.5: $new_time = $time + 19800; break;
  	    case 6: $new_time = $time + 21600; break;
  	    case 7: $new_time = $time + 25200; break;
  	    case 8: $new_time = $time + 28800; break;
  	    case 9: $new_time = $time + 32400; break;
  	    case 9.3: $new_time = $time + 33480; break;
  	    case 10: $new_time = $time + 33000; break;
  	    case 11: $new_time = $time + 39600; break;
  	    case 12: $new_time = $time + 43200; break;
	  }

	  return $new_time;
  
	} // END timezone() METHOD








	// THIS METHOD RETURNS A STRING SPECIFYING THE TIME SINCE THE SPECIFIED TIMESTAMP
	// INPUT: $time REPRESENTING A TIMESTAMP
	// OUTPUT: A STRING SPECIFYING THE TIME SINCE THE SPECIFIED TIMESTAMP
	function time_since($time) {

	  $now = time();
	  $now_day = date("j", $now);
	  $now_month = date("n", $now);
	  $now_year = date("Y", $now);

	  $time_day = date("j", $time);
	  $time_month = date("n", $time);
	  $time_year = date("Y", $time);
	  $time_since = "";
	  $lang_var = 0;

	  switch(TRUE) {
	  
	    case ($now-$time < 60):
	      // RETURNS SECONDS
	      $seconds = $now-$time;
	      $time_since = $seconds;
	      $lang_var = 773;
	      break;
	    case ($now-$time < 3600):
	      // RETURNS MINUTES
	      $minutes = round(($now-$time)/60);
	      $time_since = $minutes;
	      $lang_var = 774;
	      break;
	    case ($now-$time < 86400):
	      // RETURNS HOURS
	      $hours = round(($now-$time)/3600);
	      $time_since = $hours;
	      $lang_var = 775;
	      break;
	    case ($now-$time < 1209600):
	      // RETURNS DAYS
	      $days = round(($now-$time)/86400);
	      $time_since = $days;
	      $lang_var = 776;
	      break;
	    case (mktime(0, 0, 0, $now_month-1, $now_day, $now_year) < mktime(0, 0, 0, $time_month, $time_day, $time_year)):
	      // RETURNS WEEKS
	      $weeks = round(($now-$time)/604800);
	      $time_since = $weeks;
	      $lang_var = 777;
	      break;
	    case (mktime(0, 0, 0, $now_month, $now_day, $now_year-1) < mktime(0, 0, 0, $time_month, $time_day, $time_year)):
	      // RETURNS MONTHS
	      if($now_year == $time_year) { $subtract = 0; } else { $subtract = 12; }
	      $months = round($now_month-$time_month+$subtract);
	      $time_since = $months;
	      $lang_var = 778;
	      break;
	    default:
	      // RETURNS YEARS
	      if($now_month < $time_month) { 
	        $subtract = 1; 
	      } elseif($now_month == $time_month) {
	        if($now_day < $time_day) { $subtract = 1; } else { $subtract = 0; }
	      } else { 
	        $subtract = 0; 
	      }
	      $years = $now_year-$time_year-$subtract;
	      $time_since = $years;
	      $lang_var = 779;
	      if($years == 0) { $time_since = ""; $lang_var = 0; }
	      break;

	  }

	  return Array($lang_var, $time_since);
  
	} // END time_since() METHOD








	// THIS METHOD RETURNS AN AGE BASED ON A GIVEN MYSQL DATE
	// INPUT: $time REPRESENTING A MYSQL-FORMAT DATE (YYYY-MM-DD)
	// OUTPUT: AN INTEGER REPRESENTING AN AGE BASED ON THE DATE
	function age($time) {

	  $now = time();
	  $now_day = date("d", $now);
	  $now_month = date("m", $now);
	  $now_year = date("Y", $now);

	  $time_day = substr($time, 8, 2);
	  $time_month = substr($time, 5, 2);
	  $time_year = substr($time, 0, 4);

	  // RETURNS YEARS
	  if($now_month < $time_month) { 
	    $subtract = 1; 
	  } elseif($now_month == $time_month) {
	    if($now_day < $time_day) {
	      $subtract = 1;
	    } else {
	      $subtract = 0;
	    }
	  } else { 
	    $subtract = 0; 
	  }
	  $years = $now_year-$time_year-$subtract;
	  return $years;
  
	} // END age() METHOD

}
?>