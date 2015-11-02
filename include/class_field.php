<?php

//  THIS CLASS CONTAINS FIELD-RELATED METHODS.
//  IT IS USED DURING THE CREATION, MODIFICATION AND DELETION OF FIELDS
//  METHODS IN THIS CLASS:
//    se_field()
//    cat_list()
//    field_list()
//    field_get()
//    field_save()
//    field_delete()
//    cat_delete()
//    cat_modify()



class se_field {

	// INITIALIZE VARIABLES
	var $is_error;			// DETERMINES WHETHER THERE IS AN ERROR OR NOT, CONTAINS RELEVANT ERROR CODE
	
	var $type;			// CONTAINS THE FIELD TYPE (PROFILE, PLUGIN-RELATED, ETC)
	var $value_info;		// CONTAINS THE VALUE INFO OF THE SPECIFIC OBJECT

	var $cats;			// CONTAINS ARRAY OF FIELD CATEGORIES WITH CORRESPONDING FIELD ARRAYS
	var $subcats;			// CONTAINS ARRAY OF FIELD SUB-CATEGORIES WITH CORRESPONDING FIELD ARRAYS
	var $fields;			// CONTAINS ARRAY OF FIELDS FROM CAT SPECIFIED
	var $fields_new;		// CONTAINS ARRAY OF NEW (UNSAVED) FIELD VALUES
	var $field_query;		// CONTAINS A PARTIAL DATABASE QUERY TO SAVE/RETRIEVE FIELD VALUES
	var $field_values;		// CONTAINS AN ARRAY OF FORMATTED FIELD VALUES (USED FOR GLOBAL META DESCRIPTIONS)
	var $fields_all;		// CONTAINS ARRAY OF FIELDS FROM ALL LOOPED CATS

	var $url_string;		// CONTAINS VARIOUS PARTIAL URL STRINGS (SITUATION DEPENDENT)

	var $field_special;		// CONTAINS VALUES FOR SPECIAL FIELDS



	// THIS METHOD SETS INITIAL VARS (SUCH AS FIELD TYPE)
	// INPUT: $type REPRESENTING THE TYPE OF FIELD (PROFILE, PLUGIN-RELATED, ETC)
	//	  $value_info (OPTIONAL) REPRESENTING THE VALUE INFO FOR THE GIVEN TYPE
	// OUTPUT:
	function se_field($type, $value_info = "") {

	  $this->type = $type;
	  $this->value_info = $value_info;

	} // END se_field() METHOD









	// THIS METHOD LOOPS AND/OR VALIDATES FIELD INPUT AND CREATES A PARTIAL QUERY TO UPDATE VALUE TABLE
	// INPUT: $validate (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER TO VALIDATE POST VARS OR NOT
	//	  $format (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER TO CREATE FORMATTED FIELD VALUES
	//	  $search (OPTIONAL) REPRESENTING WHETHER TO CREATE A SEARCH QUERY OR NOT
	//	  $cat_where (OPTIONAL) REPRESENTING A WHERE CLAUSE FOR THE CATEGORY QUERY
	//	  $subcat_where (OPTIONAL) REPRESENTING A WHERE CLAUSE FOR THE SUBCATEGORY QUERY
	//	  $field_where (OPTIONAL) REPRESENTING A WHERE CLAUSE FOR THE FIELD QUERY
	// OUTPUT: 
	function cat_list($validate = 0, $format = 0, $search = 0, $cat_where = "", $subcat_where = "", $field_where = "") {
	  global $database, $datetime, $setting;

	  // SET CATEGORY VARIABLES
	  $this->fields_all = Array();
	  $cat_query = "SELECT ".$this->type."cat_id AS cat_id, ".$this->type."cat_title AS cat_title, ".$this->type."cat_order AS cat_order, ".$this->type."cat_signup AS cat_signup FROM se_".$this->type."cats WHERE ".$this->type."cat_dependency='0'"; if($cat_where != "") { $cat_query .= " AND ($cat_where)"; } $cat_query .= " ORDER BY ".$this->type."cat_order";
	  $cats = $database->database_query($cat_query);

	  // LOOP THROUGH CATS
	  while($cat_info = $database->database_fetch_assoc($cats)) {

	    // GET LIST OF FIELDS
	    $cat_fields = "";
	    $new_field_where = $this->type."field_".$this->type."cat_id='$cat_info[cat_id]'";
	    if($field_where != "") { $new_field_where .= " AND ($field_where)"; }
	    $this->field_list($validate, $format, $search, $new_field_where);
	    $cat_fields = $this->fields;

	    // GET DEPENDENT CATS
	    $this->subcats = "";
	    $subcat_query = "SELECT ".$this->type."cat_id AS cat_id, ".$this->type."cat_title AS cat_title, ".$this->type."cat_order AS cat_order FROM se_".$this->type."cats WHERE ".$this->type."cat_dependency='$cat_info[cat_id]'"; if($subcat_where != "") { $subcat_query .= " AND ($subcat_where)"; } $subcat_query .= " ORDER BY ".$this->type."cat_order";
	    $subcats = $database->database_query($subcat_query);
	
	    // LOOP THROUGH SUBCATS
	    while($subcat_info = $database->database_fetch_assoc($subcats)) {

	      // GET LIST OF FIELDS
	      $new_field_where = $this->type."field_".$this->type."cat_id='$subcat_info[cat_id]'";
	      if($field_where != "") { $new_field_where .= " AND ($field_where)"; }
	      $this->field_list($validate, $format, $search, $new_field_where);

	      // SET CAT ARRAY
	      if($format == 0 || ($format == 1 && count($this->fields) != 0)) {
	        SE_Language::_preload($subcat_info[cat_title]);
	        $this->subcats[] = Array('subcat_id' => $subcat_info[cat_id], 
					'subcat_title' => $subcat_info[cat_title], 
					'subcat_order' => $subcat_info[cat_order],
					'subcat_signup' => $subcat_info[cat_signup],
					'fields' => $this->fields);
	      }
	    }

	    // SET CAT ARRAY
	    SE_Language::_preload($cat_info[cat_title]);
	    $this->cats[] = Array('cat_id' => $cat_info[cat_id], 
				'cat_title' => $cat_info[cat_title], 
				'cat_order' => $cat_info[cat_order],
				'cat_signup' => $cat_info[cat_signup],
				'fields' => $cat_fields,
				'subcats' => $this->subcats);
	  }

	} // END cat_list() METHOD









	// THIS METHOD LOOPS AND/OR VALIDATES FIELD INPUT AND CREATES A PARTIAL QUERY TO UPDATE VALUE TABLE
	// INPUT: $validate (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER TO VALIDATE POST VARS OR NOT
	//	  $format (OPTIONAL) REPRESENTING A BOOLEAN THAT DETERMINES WHETHER TO CREATE FORMATTED FIELD VALUES
	//	  $search (OPTIONAL) REPRESENTING WHETHER TO CREATE A SEARCH QUERY OR NOT
	//	  $field_where (OPTIONAL) REPRESENTING A WHERE CLAUSE FOR THE FIELD QUERY
	// OUTPUT: 
	function field_list($validate = 0, $format = 0, $search = 0, $field_where = "") {
	  global $database, $datetime, $setting;

	  // GET NON DEPENDENT FIELDS IN CAT IF NECESSARY
	  $field_count = 0;
	  $this->fields = Array();
	  $field_query = "SELECT ".$this->type."field_id AS field_id, ".$this->type."field_order AS field_order, ".$this->type."field_title AS field_title, ".$this->type."field_desc AS field_desc, ".$this->type."field_signup AS field_signup, ".$this->type."field_error AS field_error, ".$this->type."field_type AS field_type, ".$this->type."field_style AS field_style, ".$this->type."field_maxlength AS field_maxlength, ".$this->type."field_link AS field_link, ".$this->type."field_options AS field_options, ".$this->type."field_required AS field_required, ".$this->type."field_regex AS field_regex, ".$this->type."field_special AS field_special, ".$this->type."field_html AS field_html, ".$this->type."field_search AS field_search, ".$this->type."field_display AS field_display FROM se_".$this->type."fields WHERE ".$this->type."field_dependency='0'"; if($field_where != "") { $field_query .= " AND ($field_where)"; } $field_query .= " ORDER BY ".$this->type."field_order";
	  $fields = $database->database_query($field_query);

	  while($field_info = $database->database_fetch_assoc($fields)) {

	    // SET FIELD VARS
	    $is_field_error = 0;
	    $field_value = "";
	    $field_value_formatted = "";
	    $field_value_min = "";
	    $field_value_max = "";
	    $field_options = Array();

	    // FIELD TYPE SWITCH
	    switch($field_info[field_type]) {

	      case 1: // TEXT FIELD
	      case 2: // TEXTAREA

	        // VALIDATE POSTED FIELD VALUE
	        if($validate == 1) {

	          // RETRIEVE POSTED FIELD VALUE AND FILTER FOR ADMIN-SPECIFIED HTML TAGS
	          $var = "field_".$field_info[field_id];
	          $field_value = security(cleanHTML(censor($_POST[$var]), $field_info[field_html]));

	          if($field_info[field_type] == 2) { $field_value = str_replace("\r\n", "<br>", $field_value); }

	          // CHECK FOR REQUIRED
	          if($field_info[field_required] != 0 && trim($field_value) == "") {
	            $this->is_error = 96;
	            $is_field_error = 1;
	          }

	          // RUN PREG MATCH (ONLY FOR TEXT FIELDS)
	          if($field_info[field_regex] != "" && trim($field_value) != "") {
	            if(!preg_match($field_info[field_regex], $field_value)) {
	              $this->is_error = 97;
	              $is_field_error = 1;
	            }
	          }

	          // UPDATE SAVE VALUE QUERY
	          if($this->field_query != "") { $this->field_query .= ", "; }
		  if($field_info[field_special] == 2 || $field_info[field_special] == 3) { $field_value = ucwords($field_value); }
	          $this->field_query .= $this->type."value_$field_info[field_id]='$field_value'";


		// CREATE A SEARCH QUERY FROM POSTED FIELD VALUE
		} elseif($search == 1) {
		  if($field_info[field_search] == 2) {
		    $var1 = "field_".$field_info[field_id]."_min";
		    if(isset($_POST[$var1])) { $field_value_min = $_POST[$var1]; } elseif(isset($_GET[$var1])) { $field_value_min = $_GET[$var1]; } else { $field_value_min = ""; }
		    $var2 = "field_".$field_info[field_id]."_max";
		    if(isset($_POST[$var2])) { $field_value_max = $_POST[$var2]; } elseif(isset($_GET[$var2])) { $field_value_max = $_GET[$var2]; } else { $field_value_max = ""; }
		    if($field_value_min != "") { 
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= $this->type."value_$field_info[field_id] >= $field_value_min"; 
		      $this->url_string .= $var1."=".urlencode($field_value_min)."&";
		    }
		    if($field_value_max != "") { 
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= $this->type."value_$field_info[field_id] <= $field_value_max"; 
		      $this->url_string .= $var2."=".urlencode($field_value_max)."&";
		    }
		  } elseif($field_info[field_search] == 1) {
		    $var = "field_".$field_info[field_id];
		    if(isset($_POST[$var])) { $field_value = $_POST[$var]; } elseif(isset($_GET[$var])) { $field_value = $_GET[$var]; } else { $field_value = ""; }
		    if($field_value != "") { 
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= $this->type."value_$field_info[field_id] LIKE '%$field_value%'"; 
		      $this->url_string .= $var."=".urlencode($field_value)."&";
		    }
		  } else {
		    $field_value = "";
		  }

		// DO NOT VALIDATE FIELD VALUE AND DON'T CREATE SEARCH VALUE
	        } else {
	          // RETRIEVE DATABASE FIELD VALUE
	          if($this->value_info != "") {
	            $value_column = $this->type."value_".$field_info[field_id];
	            $field_value = $this->value_info[$value_column];
	          }
	        }

		// FORMAT VALUE FOR DISPLAY
		if($format == 1 && $field_info[field_display] != 0) {

		  // LINK BROWSABLE FIELD VALUES IF NECESSARY
		  if($field_info[field_display] == 2) {
		    $br_exploded_field_values = explode("<br>", trim($field_value));
		    $exploded_field_values = Array();
		    foreach($br_exploded_field_values as $key => $value) {
		      $comma_exploded_field_values = explode(",", trim($value));
		      array_walk($comma_exploded_field_values, 'link_field_values', Array($field_info[field_id], "", $field_info[field_link], $field_info[field_display]));
		      $exploded_field_values[$key] = implode(", ", $comma_exploded_field_values);
		    }
		    $field_value_formatted = implode("<br>", $exploded_field_values);

		  // MAKE SURE TO LINK FIELDS WITH A LINK TAG
		  } else {
		    $exploded_field_values = Array(trim($field_value));
		    array_walk($exploded_field_values, 'link_field_values', Array($field_info[field_id], "", $field_info[field_link], $field_info[field_display]));
		    $field_value_formatted = implode("", $exploded_field_values);
		  }

		  // DECODE TO MAKE HTML TAGS FOR FIELDS VALID
		  $field_value_formatted = htmlspecialchars_decode($field_value_formatted, ENT_QUOTES);

		// FORMAT VALUE FOR FORM
		} else {
		  if($field_info[field_type] == 1) { 
		    $options = unserialize($field_info[field_options]);
		    for($i=0,$max=count($options);$i<$max;$i++) {
		      SE_Language::_preload_multi($options[$i][label]);
		      SE_Language::load();
		      $field_options[] = Array('label'=>SE_Language::_get($options[$i][label]));
		    }
		  }
		  if($field_info[field_type] == 2) { $field_value = str_replace("<br>", "\r\n", $field_value); }
		}
	        break;



	      case 3: // SELECT BOX
	      case 4: // RADIO BUTTON

	        // VALIDATE POSTED FIELD
	        if($validate == 1) {

	          // RETRIEVE POSTED FIELD VALUE
	          $var = "field_".$field_info[field_id];
	          $field_value = censor($_POST[$var]);

	          // CHECK FOR REQUIRED
	          if($field_info[field_required] != 0 && ($field_value == "-1" || $field_value == "")) {
	            $this->is_error = 96;
	            $is_field_error = 1;
	          }

	          // UPDATE SAVE VALUE QUERY
	          if($this->field_query != "") { $this->field_query .= ", "; }
	          $this->field_query .= $this->type."value_$field_info[field_id]='$field_value'";

		// CREATE A SEARCH QUERY FROM POSTED FIELD VALUE
		} elseif($search == 1) {
		  if($field_info[field_search] == 2) {
		    $var1 = "field_".$field_info[field_id]."_min";
		    if(isset($_POST[$var1])) { $field_value_min = $_POST[$var1]; } elseif(isset($_GET[$var1])) { $field_value_min = $_GET[$var1]; } else { $field_value_min = ""; }
		    $var2 = "field_".$field_info[field_id]."_max";
		    if(isset($_POST[$var2])) { $field_value_max = $_POST[$var2]; } elseif(isset($_GET[$var2])) { $field_value_max = $_GET[$var2]; } else { $field_value_max = ""; }
		    if($field_value_min != "" && $field_value_min != "-1") { 
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= $this->type."value_$field_info[field_id] >= $field_value_min"; 
		      $this->url_string .= $var1."=".urlencode($field_value_min)."&";
		    }
		    if($field_value_max != "" && $field_value_max != "-1") { 
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= $this->type."value_$field_info[field_id] <= $field_value_max"; 
		      $this->url_string .= $var2."=".urlencode($field_value_max)."&";
		    }
		  } elseif($field_info[field_search] == 1) {
		    $var = "field_".$field_info[field_id];
		    if(isset($_POST[$var])) { $field_value = $_POST[$var]; } elseif(isset($_GET[$var])) { $field_value = $_GET[$var]; } else { $field_value = ""; }
	            if($field_value != "-1" && $field_value != "") { 
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= $this->type."value_$field_info[field_id]='$field_value'"; 
		      $this->url_string .= $var."=".urlencode($field_value)."&";
		    }
		  } else {
		    $field_value = "";
		  }

		// DO NOT VALIDATE FIELD VALUE AND DON'T CREATE SEARCH VALUE
	        } else {
	          // RETRIEVE DATABASE FIELD VALUE
	          if($this->value_info != "") {
	            $value_column = $this->type."value_".$field_info[field_id];
	            $field_value = $this->value_info[$value_column];
	          }
	        }

	        // LOOP OVER FIELD OPTIONS
	        $options = unserialize($field_info[field_options]);
	        for($i=0,$max=count($options);$i<$max;$i++) {
	          $dep_field_info = "";
	          $dep_field_value = "";
		  $dep_field_options = "";

	          // OPTION HAS DEPENDENCY
	          if($options[$i][dependency] == "1") { 
		    $dep_field_query = "SELECT ".$this->type."field_id AS field_id, ".$this->type."field_type AS field_type, ".$this->type."field_title AS field_title, ".$this->type."field_style AS field_style, ".$this->type."field_options AS field_options, ".$this->type."field_maxlength AS field_maxlength, ".$this->type."field_link AS field_link, ".$this->type."field_required AS field_required, ".$this->type."field_regex AS field_regex, ".$this->type."field_display AS field_display FROM se_".$this->type."fields WHERE ".$this->type."field_id='".$options[$i][dependent_id]."' AND ".$this->type."field_dependency='$field_info[field_id]'";
	            $dep_field = $database->database_query($dep_field_query);
	            if($database->database_num_rows($dep_field) != "1") {
	              $options[$i][dependency] = 0;
	            } else {
	              $dep_field_info = $database->database_fetch_assoc($dep_field);
	
	              // VALIDATE POSTED FIELD VALUE
	              if($validate == 1) {
	                // OPTION SELECTED
	                if($field_value == $options[$i][value]) {
	                  $dep_var = "field_".$dep_field_info[field_id];
	                  $dep_field_value = censor($_POST[$dep_var]);

			  // DEP FIELD TYPE
			  switch($dep_field_info[field_type]) {

			    // TEXT FIELD
			    case "1":
  
		              // CHECK FOR REQUIRED
	                      if($dep_field_info[field_required] != 0 && trim($dep_field_value) == "") {
	                        $this->is_error = 96;
	                        $is_field_error = 1;
	                      }

	                      // RUN PREG MATCH
	                      if($dep_field_info[field_regex] != "" && trim($dep_field_value) != "") {
	                        if(!preg_match($dep_field_info[field_regex], $dep_field_value)) {
	                          $this->is_error = 97;
	                          $is_field_error = 1;
	                        }
	                      }
			      break;

			    // SELECT BOX
			    case "3":
			    
		              // CHECK FOR REQUIRED
		              if( $dep_field_info['field_required'] != 0 && ($dep_field_value == "-1" || $dep_field_value == "") )
                  { 
		                $this->is_error = 96;
                    $is_field_error = 1;
		              }
			      break;
			  }	

	                // OPTION NOT SELECTED
	                } else {
	                  $dep_field_value = "";
	                }

	 	        // UPDATE SAVE VALUE QUERY
	    	        if($this->field_query != "") { $this->field_query .= ", "; }
	  	        $this->field_query .= $this->type."value_$dep_field_info[field_id]='$dep_field_value'";

		      // DO NOT VALIDATE POSTED FIELD VALUE
	              } else {
	                // RETRIEVE DATABASE FIELD VALUE
	                if($this->value_info != "") {
	                  $value_column = $this->type."value_".$dep_field_info[field_id];
	                  $dep_field_value = $this->value_info[$value_column];
	                }
	              }

		      // RETRIEVE DEP FIELD OPTIONS
		      $dep_options = unserialize($dep_field_info[field_options]);
		      for($i2=0,$max2=count($dep_options);$i2<$max2;$i2++) {
			SE_Language::_preload($dep_options[$i2][label]);
			$dep_field_options[] = Array('value' => $dep_options[$i2][value],
							'label' => $dep_options[$i2][label]);
			if($dep_options[$i2][value] == $dep_field_value) { $dep_field_value_formatted = $dep_options[$i2][label]; }
		      }
	            }
		  }

		  // FORMAT VALUE FOR DISPLAY IF OPTION IS SELECTED
		  if($format == 1 && $field_value == $options[$i][value] && $field_info[field_display] != 0) {
	            SE_Language::_preload_multi($dep_field_info[field_title], $options[$i][label]);
		    SE_Language::load();
		    $field_value_formatted = SE_Language::_get($options[$i][label]);

		    // LINK FIELD VALUES IF NECESSARY
		    if($field_info[field_display] == 2) { 
		      link_field_values($field_value_formatted, "", Array($field_info[field_id], $options[$i][value], "", $field_info[field_display])); 
		    }

		    // ADD DEPENDENT VALUE TO FIELD VALUE
		    if($dep_field_value != "" && $dep_field_info[field_display] != 0) { 
		      if($dep_field_info[field_type] == 3) { $dep_field_value_formatted = SE_Language::_get($dep_field_value_formatted); } else { $dep_field_value_formatted = $dep_field_value; }
		      link_field_values($dep_field_value_formatted, "", Array($dep_field_info[field_id], $dep_field_value, $dep_field_info[field_link], $dep_field_info[field_display]));
		      $field_value_formatted .= " ".SE_Language::_get($dep_field_info[field_title])." ".$dep_field_value_formatted;
		    }

		  }
          
	          // SET OPTIONS ARRAY
		  SE_Language::_preload_multi($dep_field_info[field_title], $options[$i][label]);
	          $field_options[] = Array('value' => $options[$i][value],
						'label' => $options[$i][label],
						'dependency' => $options[$i][dependency],
						'dep_field_id' => $dep_field_info[field_id],
						'dep_field_title' => $dep_field_info[field_title],
						'dep_field_type' => $dep_field_info[field_type],
						'dep_field_required' => $dep_field_info[field_required],
						'dep_field_maxlength' => $dep_field_info[field_maxlength],
						'dep_field_options' => $dep_field_options,
						'dep_field_style' => $dep_field_info[field_style],
						'dep_field_value' => $dep_field_value,
						'dep_field_error' => $dep_field_error);
	        }
	        break;


	      case 5: // DATE FIELD

		// SET MONTH, DAY, AND YEAR FORMAT FROM SETTINGS
		switch($setting[setting_dateformat]) {
		  case "n/j/Y": case "n.j.Y": case "n-j-Y": $month_format = "n"; $day_format = "j"; $year_format = "Y"; $date_order = "mdy"; break;
		  case "Y/n/j": case "Ynj": $month_format = "n"; $day_format = "j"; $year_format = "Y"; $date_order = "ymd"; break;
		  case "Y-n-d": $month_format = "n"; $day_format = "d"; $year_format = "Y"; $date_order = "ymd"; break;
		  case "Y-m-d": $month_format = "m"; $day_format = "d"; $year_format = "Y"; $date_order = "ymd"; break;
		  case "j/n/Y": case "j.n.Y": $month_format = "n"; $day_format = "j"; $year_format = "Y"; $date_order = "dmy"; break;
		  case "M. j, Y": $month_format = "M"; $day_format = "j"; $year_format = "Y"; $date_order = "mdy"; break;
		  case "F j, Y": case "l, F j, Y": $month_format = "F"; $day_format = "j"; $year_format = "Y"; $date_order = "mdy"; break;
		  case "j F Y": case "D j F Y": case "l j F Y": $month_format = "F"; $day_format = "j"; $year_format = "Y"; $date_order = "dmy"; break;
		  case "D-j-M-Y": case "D j M Y": case "j-M-Y": $month_format = "M"; $day_format = "j"; $year_format = "Y"; $date_order = "dmy"; break;
		  case "Y-M-j": $month_format = "M"; $day_format = "j"; $year_format = "Y"; $date_order = "ymd"; break;
		}
  

	        // VALIDATE POSTED VALUE
	        if($validate == 1) {
	          // RETRIEVE POSTED FIELD VALUE
	          $var1 = "field_".$field_info[field_id]."_1";
	          $var2 = "field_".$field_info[field_id]."_2";
	          $var3 = "field_".$field_info[field_id]."_3";
	          $field_1 = $_POST[$var1];
	          $field_2 = $_POST[$var2];
	          $field_3 = $_POST[$var3];

	          // ORDER DATE VALUES PROPERLY
	          switch($date_order) {
	            case "mdy": $month = $field_1; $day = $field_2; $year = $field_3; break;
	            case "ymd": $year = $field_1; $month = $field_2; $day = $field_3; break;
	            case "dmy": $day = $field_1; $month = $field_2; $year = $field_3; break;
	          }
  
		  // CONSTRUCT FIELD VALUE
		  $field_value = str_pad($year, 4, '0', STR_PAD_LEFT)."-".str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($day, 2, '0', STR_PAD_LEFT);

	          // CHECK FOR REQUIRED
            if( $field_info['field_required'] && ($month == "00" || $day == "00" || $year == "00") )
            { 
	            $this->is_error = 96;
	            $is_field_error = 1;
	          }

	          // UPDATE SAVE VALUE QUERY
	          if($this->field_query != "") { $this->field_query .= ", "; }
	          $this->field_query .= $this->type."value_$field_info[field_id]='$field_value'";


		// CREATE A SEARCH QUERY FROM POSTED FIELD VALUE
		} elseif($search == 1) {
		  
		  // DATE IS A BIRTHDAY
		  if($field_info[field_special] == 1) { 

		    // RESET DATE ORDER SO MONTH IS LAST
		    $date_order = "mdy"; 

		    // RETRIEVE MIN/MAX YEARS
		    $var3_min = "field_".$field_info[field_id]."_3_min";
		    $var3_max = "field_".$field_info[field_id]."_3_max";
		    if(isset($_POST[$var3_min])) { $field_3_min = $_POST[$var3_min]; } elseif(isset($_GET[$var3_min])) { $field_3_min = $_GET[$var3_min]; } else { $field_3_min = ""; }
		    if(isset($_POST[$var3_max])) { $field_3_max = $_POST[$var3_max]; } elseif(isset($_GET[$var3_max])) { $field_3_max = $_GET[$var3_max]; } else { $field_3_max = ""; }

		    $this->url_string .= $var3_min."=".urlencode($field_3_min)."&";
		    $this->url_string .= $var3_max."=".urlencode($field_3_max)."&";

		    // CONSTRUCT SEARCH VALUES (MIN YEAR)
		    // IMPORTANT NOTE - BECAUSE IT DISPLAYS THE AGE (NOT THE YEAR) TO THE SEARCHER, THIS ACTUALLY CORRESPONDS TO THE MINIMUM AGE (MAXIMUM YEAR)
		    $field_value_min = str_pad($field_3_min, 4, '0', STR_PAD_LEFT);
	            if($field_value_min != "0000") {
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= $this->type."value_$field_info[field_id]<='$field_value_min-".date('m', time())."-".date('d', time())."'"; 
		    }

		    // CONSTRUCT SEARCH VALUES (MAX YEAR)
		    // IMPORTANT NOTE - BECAUSE IT DISPLAYS THE AGE (NOT THE YEAR) TO THE SEARCHER, THIS ACTUALLY CORRESPONDS TO THE MAXIMUM AGE (MINIMUM YEAR)
		    $field_value_max = str_pad($field_3_max, 4, '0', STR_PAD_LEFT);
	            if($field_value_max != "0000") {
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= $this->type."value_$field_info[field_id]>=DATE_ADD('".($field_value_max-1)."-".date('m', time())."-".date('d', time())."', INTERVAL 1 DAY)"; 
		    }

		    // EXCLUDE USERS WHO HAVE NOT ENTERED A BIRTH YEAR
		    if($field_value_min != "0000" || $field_value_max != "0000") {
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= "YEAR(".$this->type."value_$field_info[field_id])<>'0000'"; 
		    }


		  // DATE IS NOT A BIRTHDAY
		  } else {

		    // RETRIEVE VALUES
	            $var1 = "field_".$field_info[field_id]."_1";
	            $var2 = "field_".$field_info[field_id]."_2";
	            $var3 = "field_".$field_info[field_id]."_3";
		    if(isset($_POST[$var1])) { $field_1 = $_POST[$var1]; } elseif(isset($_GET[$var1])) { $field_1 = $_GET[$var1]; } else { $field_1 = ""; }
		    if(isset($_POST[$var2])) { $field_2 = $_POST[$var2]; } elseif(isset($_GET[$var2])) { $field_2 = $_GET[$var2]; } else { $field_2 = ""; }
		    if(isset($_POST[$var3])) { $field_3 = $_POST[$var3]; } elseif(isset($_GET[$var3])) { $field_3 = $_GET[$var3]; } else { $field_3 = ""; }

		    $this->url_string .= $var1."=".urlencode($field_1)."&";
		    $this->url_string .= $var2."=".urlencode($field_2)."&";
		    $this->url_string .= $var3."=".urlencode($field_3)."&";

	            // ORDER DATE VALUES PROPERLY
	            switch($date_order) {
	              case "mdy": $month = str_pad($field_1, 2, '0', STR_PAD_LEFT); $day = str_pad($field_2, 2, '0', STR_PAD_LEFT); $year = str_pad($field_3, 4, '0', STR_PAD_LEFT); break;
	              case "ymd": $year = str_pad($field_1, 4, '0', STR_PAD_LEFT); $month = str_pad($field_2, 2, '0', STR_PAD_LEFT); $day = str_pad($field_3, 2, '0', STR_PAD_LEFT); break;
	              case "dmy": $day = str_pad($field_1, 2, '0', STR_PAD_LEFT); $month = str_pad($field_2, 2, '0', STR_PAD_LEFT); $year = str_pad($field_3, 4, '0', STR_PAD_LEFT); break;
	            }
  
		    // CONSTRUCT FIELD VALUE
		    $field_value = $year."-".$month.'-'.$day;

	            if($month != "00") {
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= "MONTH(".$this->type."value_$field_info[field_id])='$month'"; 
		    }
	            if($day != "00") {
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= "DAY(".$this->type."value_$field_info[field_id])='$day'"; 
		    }
	            if($year != "0000") {
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= "YEAR(".$this->type."value_$field_info[field_id])='$year'"; 
		    }

		  }

		// DO NOT VALIDATE FIELD VALUE AND DON'T CREATE SEARCH VALUE
	        } else {
	          // RETRIEVE DATABASE FIELD VALUE
	          if($this->value_info != "") {
	            $value_column = $this->type."value_".$field_info[field_id];
	            $field_value = $this->value_info[$value_column];
	          } else {
	            $field_value = "0000-00-00";
	          }
	        }

	        $year = substr($field_value, 0, 4); 
		$month = substr($field_value, 5, 2); 
		$day = substr($field_value, 8, 2); 

		// FORMAT VALUE FOR DISPLAY
		if($format == 1 && $field_info[field_display] != 0) {
	   	  if($field_value != "0000-00-00") { 
		    if($year == "0000") { $year = ""; }
		    if($month == "00") { $month = ""; } else { $month = $datetime->cdate("F", mktime(0, 0, 0, $month, 1, 1990)); }
		    if($day == "00") { $day = ""; } else { $day = $datetime->cdate("$day_format", mktime(0, 0, 0, 1, $day, 1990)); }
	            switch($date_order) {
	              case "mdy": $field_value_formatted = "$month $day $year"; break;
	              case "ymd": $field_value_formatted = "$year $month $day"; break;
	              case "dmy": $field_value_formatted = "$day $month $year"; break;
	            }
		    if($field_info[field_display] == 2) { link_field_values($field_value_formatted, "", Array($field_info[field_id], $field_value, "", $field_info[field_display])); }
		  }


		// FORMAT VALUE FOR FORM
	    	} else {

		  // GET LANGUAGE VARS
		  SE_Language::_preload_multi(579, 580, 581);

	          // CONSTRUCT MONTH ARRAY
	          $month_array = Array();
	          $month_array[0] = Array('name' => "579", 'value' => "0", 'selected' => "");
	          for($m=1;$m<=12;$m++) {
	            if($month == $m) { $selected = " SELECTED"; } else { $selected = ""; }
	            $month_array[$m] = Array('name' => $datetime->cdate("$month_format", mktime(0, 0, 0, $m, 1, 1990)),
	  					'value' => $m,
	    					'selected' => $selected);
	          }
  
	          // CONSTRUCT DAY ARRAY
	          $day_array = Array();
	          $day_array[0] = Array('name' => "580", 'value' => "0", 'selected' => "");
	          for($d=1;$d<=31;$d++) {
	            if($day == $d) { $selected = " SELECTED"; } else { $selected = ""; }
	            $day_array[$d] = Array('name' => $datetime->cdate("$day_format", mktime(0, 0, 0, 1, $d, 1990)),
	    					'value' => $d,
	    					'selected' => $selected);
	          }

	          // CONSTRUCT YEAR ARRAY
	          $year_array = Array();
	          $year_count = 1;
	          $current_year = $datetime->cdate("Y", time());
	          $year_array[0] = Array('name' => "581", 'value' => "0", 'selected' => "");
	          for($y=$current_year;$y>=1920;$y--) {
	            if($year == $y) { $selected = " SELECTED"; } else { $selected = ""; }
	            $year_array[$year_count] = Array('name' => $y,
	    						'value' => $y,
	    						'selected' => $selected);
	            $year_count++;
	          }

	          // ORDER DATE ARRAYS PROPERLY
	          switch($date_order) {
	            case "mdy": $date_array1 = $month_array; $date_array2 = $day_array; $date_array3 = $year_array; break;
	            case "ymd": $date_array1 = $year_array; $date_array2 = $month_array; $date_array3 = $day_array; break;
	            case "dmy": $date_array1 = $day_array; $date_array2 = $month_array; $date_array3 = $year_array; break;
	          }
		}

	        break;



	      case 6: // CHECKBOXES

	        // VALIDATE POSTED FIELD
	        if($validate == 1) {
	          // RETRIEVE POSTED FIELD VALUE
	          $var = "field_".$field_info[field_id];
	          $field_value = $_POST[$var];

	          // CHECK FOR REQUIRED
	          if($field_info[field_required] != 0 && count($field_value) == 0) {
	            $this->is_error = 96;
	            $is_field_error = 1;
	          }

	          // UPDATE SAVE VALUE QUERY
	          if($this->field_query != "") { $this->field_query .= ", "; }
	          $this->field_query .= $this->type."value_$field_info[field_id]='".implode(",", $field_value)."'";

		// CREATE A SEARCH QUERY FROM POSTED FIELD VALUE
		} elseif($search == 1) {
		  $var = "field_".$field_info[field_id];
		  if(isset($_POST[$var])) { $field_value = $_POST[$var]; } elseif(isset($_GET[$var])) { $field_value = $_GET[$var]; } else { $field_value = ""; }
	          if(count($field_value) != 0 && $field_value != "") { 
		    for($o=0;$o<count($field_value);$o++) {
		      if($this->field_query != "") { $this->field_query .= " AND "; } 
		      $this->field_query .= "FIND_IN_SET('".$field_value[$o]."', ".$this->type."value_$field_info[field_id])"; 
		      $this->url_string .= $var."[]=".urlencode($field_value[$o])."&";
		    }
		  }


		// DO NOT VALIDATE FIELD VALUE AND DON'T CREATE SEARCH VALUE
	        } else {
	          // RETRIEVE DATABASE FIELD VALUE
	          if($this->value_info != "") {
	            $value_column = $this->type."value_".$field_info[field_id];
	            $field_value = explode(",", $this->value_info[$value_column]);
	          }
	        }

	        // LOOP OVER FIELD OPTIONS
	        $options = unserialize($field_info[field_options]);
	        for($i=0,$max=count($options);$i<$max;$i++) {
	          $dep_field_info = "";
	          $dep_field_value = "";
		  $dep_field_options = "";

	          // OPTION HAS DEPENDENCY
	          if($options[$i][dependency] == "1") { 
		    $dep_field_query = "SELECT ".$this->type."field_id AS field_id, ".$this->type."field_type AS field_type, ".$this->type."field_title AS field_title, ".$this->type."field_style AS field_style, ".$this->type."field_options AS field_options, ".$this->type."field_maxlength AS field_maxlength, ".$this->type."field_link AS field_link, ".$this->type."field_required AS field_required, ".$this->type."field_regex AS field_regex, ".$this->type."field_display AS field_display FROM se_".$this->type."fields WHERE ".$this->type."field_id='".$options[$i][dependent_id]."' AND ".$this->type."field_dependency='$field_info[field_id]'";
	            $dep_field = $database->database_query($dep_field_query);
	            if($database->database_num_rows($dep_field) != "1") {
	              $options[$i][dependency] = 0;
	            } else {
	              $dep_field_info = $database->database_fetch_assoc($dep_field);
	
	              // VALIDATE POSTED FIELD VALUE
	              if($validate == 1) {
	                // OPTION SELECTED
	                if(in_array($options[$i][value], $field_value)) {
	                  $dep_var = "field_".$dep_field_info[field_id];
	                  $dep_field_value = censor($_POST[$dep_var]);
  
			  // DEP FIELD TYPE
			  switch($dep_field_info[field_type]) {

			    // TEXT FIELD
			    case "1":
  
		              // CHECK FOR REQUIRED
	                      if($dep_field_info[field_required] != 0 && trim($dep_field_value) == "") {
	                        $this->is_error = 96;
	                        $is_field_error = 1;
	                      }

	                      // RUN PREG MATCH
	                      if($dep_field_info[field_regex] != "" && trim($dep_field_value) != "") {
	                        if(!preg_match($dep_field_info[field_regex], $dep_field_value)) {
	                          $this->is_error = 97;
	                          $is_field_error = 1;
	                        }
	                      }
			      break;

			    // SELECT BOX
			    case "3":
			    
		              // CHECK FOR REQUIRED
		              if( $dep_field_info['field_required'] != 0 && ($dep_field_value == "-1" || $dep_field_value == "") )
                  {
		                $this->is_error = 96;
                    $is_field_error = 1;
		              }
			      break;
			  }

	                // OPTION NOT SELECTED
	                } else {
	                  $dep_field_value = "";
	                }

	 	        // UPDATE SAVE VALUE QUERY
	    	        if($this->field_query != "") { $this->field_query .= ", "; }
	  	        $this->field_query .= $this->type."value_$dep_field_info[field_id]='$dep_field_value'";

		      // DO NOT VALIDATE POSTED FIELD VALUE
	              } else {
	                // RETRIEVE DATABASE FIELD VALUE
	                if($this->value_info != "") {
	                  $value_column = $this->type."value_".$dep_field_info[field_id];
	                  $dep_field_value = $this->value_info[$value_column];
	                }
	              }

		      // RETRIEVE DEP FIELD OPTIONS
		      $dep_options = unserialize($dep_field_info[field_options]);
		      for($i2=0,$max2=count($dep_options);$i2<$max2;$i2++) {
			SE_Language::_preload($dep_options[$i2][label]);
			$dep_field_options[] = Array('value' => $dep_options[$i2][value],
							'label' => $dep_options[$i2][label]);
			if($dep_options[$i2][value] == $dep_field_value) { $dep_field_value_formatted = $dep_options[$i2][label]; }
		      }
	            }
		  }

		  // FORMAT VALUE FOR DISPLAY IF OPTION IS SELECTED
		  if($format == 1 && in_array($options[$i][value], $field_value) && $field_info[field_display] != 0) {
	            SE_Language::_preload_multi($dep_field_info[field_title], $options[$i][label]);
		    SE_Language::load();
		    $formatted_prelim = SE_Language::_get($options[$i][label]);

		    // LINK FIELD VALUES IF NECESSARY
		    if($field_info[field_display] == 2) { 
		      link_field_values($formatted_prelim, "", Array($field_info[field_id], $options[$i][value], "", $field_info[field_display])); 
		    }

		    // ADD DEPENDENT VALUE TO FIELD VALUE
		    if($dep_field_value != "" && $dep_field_info[field_display] != 0) { 
		      if($dep_field_info[field_type] == 3) { $dep_field_value_formatted = SE_Language::_get($dep_field_value_formatted); } else { $dep_field_value_formatted = $dep_field_value; }
		      link_field_values($dep_field_value_formatted, "", Array($dep_field_info[field_id], $dep_field_value, $dep_field_info[field_link], $dep_field_info[field_display]));
		      $field_value_formatted .= " ".SE_Language::_get($dep_field_info[field_title])." ".$dep_field_value_formatted;
		    }

		    if(trim($field_value_formatted) != "") { $field_value_formatted .= ", "; }
		    $field_value_formatted .= $formatted_prelim;
		  }
          
	          // SET OPTIONS ARRAY
		  SE_Language::_preload_multi($dep_field_info[field_title], $options[$i][label]);
	          $field_options[] = Array('value' => $options[$i][value],
						'label' => $options[$i][label],
						'dependency' => $options[$i][dependency],
						'dep_field_id' => $dep_field_info[field_id],
						'dep_field_title' => $dep_field_info[field_title],
						'dep_field_type' => $dep_field_info[field_type],
						'dep_field_required' => $dep_field_info[field_required],
						'dep_field_maxlength' => $dep_field_info[field_maxlength],
						'dep_field_options' => $dep_field_options,
						'dep_field_style' => $dep_field_info[field_style],
						'dep_field_value' => $dep_field_value,
						'dep_field_error' => $dep_field_error);
	        }
	        break;

	    }

	    // SET FIELD ERROR IF ERROR OCCURRED
	    if($is_field_error == 1) { $field_error = $field_info[field_error]; } else { $field_error = 0; }

	    // SET FIELD VALUE ARRAY FOR LATER USE 
// FIX THIS FOR CHECKBOXES (USED FOR SUBNETS?)
	    $this->fields_new[$this->type."value_".$field_info[field_id]] = $field_value;

	    // SET SPECIAL FIELDS, IF NECESSARY
	    if($field_info[field_special] != 0) { $this->field_special[$field_info[field_special]] = $field_value; }

	    // SAVE FORMATTED FIELD VALUE IN ARRAY
	    if($field_value_formatted != "") { $this->field_values[] = $field_value_formatted; }

	    // SET FIELD ARRAY AND INCREMENT FIELD COUNT
	    if(($format == 0 && $search == 0) || ($format == 1 && $field_value_formatted != "") || ($search == 1 && $field_info[field_search] != 0)) {
	      SE_Language::_preload_multi($field_info[field_title], $field_info[field_desc], $field_info[field_error]);
	      
	      $this->fields[] = 
	      $this->fields_all[] = Array('field_id' => $field_info[field_id], 
					'field_title' => $field_info[field_title], 
					'field_desc' => $field_info[field_desc],
					'field_type' => $field_info[field_type],
					'field_required' => $field_info[field_required],
					'field_style' => $field_info[field_style],
					'field_maxlength' => $field_info[field_maxlength],
					'field_special' => $field_info[field_special],
					'field_signup' => $field_info[field_signup],
					'field_search' => $field_info[field_search],
					'field_options' => $field_options,
					'field_value' => $field_value,
					'field_value_formatted' => $field_value_formatted,
					'field_value_min' => $field_value_min,
					'field_value_max' => $field_value_max,
					'field_error' => $field_error,
					'date_array1' => $date_array1,
					'date_array2' => $date_array2,
					'date_array3' => $date_array3);
	      $field_count++;
	    }

	  } 
	} // END field_list() METHOD









	// THIS METHOD RETRIEVES FIELD INFO ABOUT A FIELD AND RETURNS IT AS AN ASSOCIATIVE ARRAY
	// INPUT: $field_id REPRESENTING THE FIELD'S ID
	// OUTPUT: AN ASSOCIATIVE ARRAY CONTAINING THE FIELD INFORMATION (WITHOUT TYPE PREFIX)
	function field_get($field_id) {
	  global $database;

	  $field_info = $database->database_fetch_assoc($database->database_query("SELECT ".$this->type."field_id AS field_id, ".$this->type."field_".$this->type."cat_id AS field_cat_id, ".$this->type."field_order AS field_order, ".$this->type."field_dependency AS field_dependency, ".$this->type."field_title AS field_title, ".$this->type."field_desc AS field_desc, ".$this->type."field_error AS field_error, ".$this->type."field_type AS field_type, ".$this->type."field_style AS field_style, ".$this->type."field_maxlength AS field_maxlength, ".$this->type."field_link AS field_link, ".$this->type."field_options AS field_options, ".$this->type."field_required AS field_required, ".$this->type."field_regex AS field_regex, ".$this->type."field_special AS field_special, ".$this->type."field_search AS field_search, ".$this->type."field_display AS field_display, ".$this->type."field_html AS field_html FROM se_".$this->type."fields WHERE ".$this->type."field_id='$field_id'"));

	  // PULL OPTIONS INTO NEW ARRAY
	  $new_field_options = "";
	  $field_options = unserialize($field_info[field_options]);
	  for($i=0;$i<count($field_options);$i++) {
	    SE_Language::_preload_multi($field_options[$i][label]);
	    SE_Language::load();
	    $field_options[$i][label] = SE_Language::_get($field_options[$i][label]);
	    if($field_options[$i][dependency] == 1) { 
	      $dep_field = $database->database_query("SELECT ".$this->type."field_id AS field_id, ".$this->type."field_title AS field_title FROM se_".$this->type."fields WHERE ".$this->type."field_id='".$field_options[$i][dependent_id]."'");
	      if($database->database_num_rows($dep_field) != "1") {
	        $field_options[$i][dependency] = 0;
	      } else {
	        $field_options[$i][dependency] = 1;
	        $dep_field_info = $database->database_fetch_assoc($dep_field);
		SE_Language::_preload_multi($dep_field_info[field_title]);
		SE_Language::load();
		$dep_field_info[field_title] = SE_Language::_get($dep_field_info[field_title]);
	        $field_options[$i][dependent_label] = $dep_field_info[field_title];
	      }
	    }
	  }

	  // LOAD FIELD TITLE
	  SE_Language::_preload_multi($field_info[field_title], $field_info[field_desc], $field_info[field_error]);
	  SE_Language::load();
	  $field_info[field_title] = SE_Language::_get($field_info[field_title]);
	  $field_info[field_desc] = SE_Language::_get($field_info[field_desc]);
	  $field_info[field_error] = SE_Language::_get($field_info[field_error]);

	  $field_info[field_options_detailed] = $field_options;
	  return $field_info;

	} // END field_get() METHOD









	// THIS METHOD SAVES FIELD DATA
	// INPUT: $field_info REPRESENTING AN ARRAY CONTAINING THE FIELD INFO TO SAVE
	// OUTPUT: 
	function field_save($field_info) {
	  global $database;

	  $old_field_query = $database->database_query("SELECT ".$this->type."field_id AS field_id, ".$this->type."field_".$this->type."cat_id AS field_cat_id, ".$this->type."field_dependency AS field_dependency, ".$this->type."field_order AS field_order, ".$this->type."field_title AS field_title, ".$this->type."field_desc AS field_desc, ".$this->type."field_error AS field_error, ".$this->type."field_options AS field_options, ".$this->type."field_special AS field_special FROM se_".$this->type."fields WHERE ".$this->type."field_id='$field_info[field_id]'");
	  if($database->database_num_rows($old_field_query) != 0) { $old_field_info = $database->database_fetch_assoc($old_field_query); } else { $old_field_info = ""; $old_field_info[field_dependency] = 0; }
	  if($old_field_info[field_dependency] != 0) { $field_info[field_type] = ($field_info[field_type] == 3) ? 3: 1; $field_info[field_cat_id] = $old_field_info[field_cat_id]; }

	  // FIELD TYPE IS TEXT FIELD
	  if($field_info[field_type] == "1") {
	    $column_type = "varchar(250)";
	    $column_default = "default ''";
	    $field_info[field_html] = str_replace("&gt;", "", str_replace("&lt;", "", str_replace(" ", "", $field_info[field_html])));
	    $suggestions = explode("\r\n", $field_info[field_suggestions]);
	    for($i=0;$i<count($suggestions);$i++) {
	      if(trim($suggestions[$i]) != "") {
	        $options[] = Array('value'=>$i, 'label'=>$suggestions[$i], 'dependency'=>'0', 'dependent_label'=>'', 'dependent_id'=>'');
	      }
	    }

	  // FIELD TYPE IS TEXTAREA
	  } elseif($field_info[field_type] == "2") {
	    $column_type = "text";
	    $column_default = "";
	    $field_info[field_html] = str_replace("&gt;", "", str_replace("&lt;", "", str_replace(" ", "", $field_info[field_html])));

	  // FIELD TYPE IS SELECT BOX OR RADIO BUTTONS
	  } elseif($field_info[field_type] == "3" || $field_info[field_type] == "4" || $field_info[field_type] == "6") {
	    $field_info[field_html] = "";
	    for($i=0;$i<count($field_info[field_options]);$i++) {
	      if(trim($field_info[field_options][$i][value]) != "" && trim($field_info[field_options][$i][label]) != "") {
	        $set_values[] = $field_info[field_options][$i][value];
	        $options[] = $field_info[field_options][$i];
	        if(ereg("^[0-9]+$", $field_info[field_options][$i][value]) === FALSE) { $this->is_error = 146; break; }
	      } elseif($field_info[field_options][$i][dependent_id] != "") {
	        $dependent_ids[] = $field_info[field_options][$i][dependent_id];
	      }
	    }
      
	    if( !empty($set_values) && $field_info[field_type] == "6" ) {
	      $column_type = "set('".implode("', '", $set_values)."')";
	      $column_default = "";
	    } else {
	      $column_type = "int(2)";
	      $column_default = "default '-1'";
	    }

	    // IF NO OPTIONS HAVE BEEN SPECIFIED
	    if(count($options) == 0) { $this->is_error = 143; }

	  // FIELD TYPE IS DATE FIELD
	  } elseif($field_info[field_type] == "5") {
	    $box5_display = "block";
	    $column_type = "date";
	    $column_default = "default '0000-00-00'";
	    $field_info[field_html] = "";


	  // FIELD TYPE NOT SPECIFIED
	  } else {
	    $this->is_error = 85;
	  }

	  // FIELD TITLE IS EMPTY
	  if(trim($field_info[field_title]) == "" && $old_field_info[field_dependency] == 0) { $this->is_error = 94; }

	  // NO ERROR 
	  if($this->is_error == 0) {

	    // OLD FIELD (SAVE)
	    if($database->database_num_rows($old_field_query)) { 

	      if($old_field_info[field_cat_id] != $field_info[field_cat_id]) {
	        $field_order_info = $database->database_fetch_assoc($database->database_query("SELECT max(".$this->type."field_order) as f_order FROM se_".$this->type."fields WHERE ".$this->type."field_dependency='0' AND ".$this->type."field_".$this->type."cat_id='$field_info[field_cat_id]'"));
	        $field_info[field_order] = $field_order_info[f_order]+1;
	      } else {
	        $field_info[field_order] = $old_field_info[field_order];
	      }

	      SE_Language::edit($old_field_info[field_title], $field_info[field_title]);
	      SE_Language::edit($old_field_info[field_desc], $field_info[field_desc]);
	      SE_Language::edit($old_field_info[field_error], $field_info[field_error]);
	      $database->database_query("UPDATE se_".$this->type."fields SET ".$this->type."field_".$this->type."cat_id='$field_info[field_cat_id]', ".$this->type."field_order='$field_info[field_order]', ".$this->type."field_type='$field_info[field_type]', ".$this->type."field_style='$field_info[field_style]', ".$this->type."field_maxlength='$field_info[field_maxlength]', ".$this->type."field_link='$field_info[field_link]', ".$this->type."field_required='$field_info[field_required]', ".$this->type."field_regex='$field_info[field_regex]', ".$this->type."field_html='$field_info[field_html]', ".$this->type."field_search='$field_info[field_search]', ".$this->type."field_display='$field_info[field_display]', ".$this->type."field_special='$field_info[field_special]' WHERE ".$this->type."field_id='$field_info[field_id]'");
	      $column_name = $this->type."value_".$field_info[field_id];
	      $database->database_query("ALTER TABLE se_".$this->type."values MODIFY $column_name $column_type $column_default");

	      // ENSURE FIRST DISPLAY NAME GETS CLEARED IF NECESSARY
	      if($this->type == "profile" && $old_field_info[field_special] == 2 && $field_info[field_special] != 2) {
		$database->database_query("UPDATE se_users SET user_fname='' WHERE user_fname<>''");
	      // ENSURE LAST DISPLAY NAME GETS CLEARED IF NECESSARY
	      } elseif($this->type == "profile" && $old_field_info[field_special] == 3 && $field_info[field_special] != 3) {
		$database->database_query("UPDATE se_users SET user_lname='' WHERE user_lname<>''");
	      }

	      // GET OLD LABEL LANGUAGE VARS
	      $old_field_options = unserialize($old_field_info[field_options]);
	      for($o=0;$o<count($old_field_options);$o++) { $old_language_ids[$old_field_options[$o][value]] = $old_field_options[$o][label]; }

	      // EDIT DEPENDENT FIELDS
	      for($d=0;$d<count($options);$d++) {
	        if($old_language_ids[$options[$d][value]] != "") {
		  $options[$d][label] = SE_Language::edit($old_language_ids[$options[$d][value]], $options[$d][label]);
		  unset($old_language_ids[$options[$d][value]]);
	        } else {
		  $options[$d][label] = SE_Language::edit(0, $options[$d][label], NULL, LANGUAGE_INDEX_FIELDS);
		}

	        $dep_field = $database->database_query("SELECT ".$this->type."field_id AS field_id, ".$this->type."field_title AS field_title FROM se_".$this->type."fields WHERE ".$this->type."field_id='".$options[$d][dependent_id]."'");

	        if($database->database_num_rows($dep_field) == "1") {
	          $dep_field_info = $database->database_fetch_assoc($dep_field);
	          if($options[$d][dependency] == "1") {
	            SE_Language::edit($dep_field_info[field_title], $options[$d][dependent_label]);
	            $database->database_query("UPDATE se_".$this->type."fields SET ".$this->type."field_".$this->type."cat_id='$field_info[field_cat_id]' WHERE ".$this->type."field_id='$dep_field_info[field_id]'");
	          } else {
	            $database->database_query("DELETE FROM se_".$this->type."fields, se_languagevars USING se_".$this->type."fields JOIN se_languagevars ON se_".$this->type."fields.".$this->type."field_title=se_languagevars.languagevar_id WHERE ".$this->type."field_id='$dep_field_info[field_id]'");
	            $column_name = $this->type."value_".$dep_field_info[field_id];
	            $database->database_query("ALTER TABLE se_".$this->type."values DROP COLUMN $column_name");
	          }
	        } else {
	          if($options[$d][dependency] == "1") {
		    $dep_languagevar_id = SE_Language::edit(0, $options[$d][dependent_label], NULL, LANGUAGE_INDEX_FIELDS);
	            $database->database_query("INSERT INTO se_".$this->type."fields (".$this->type."field_".$this->type."cat_id, ".$this->type."field_title, ".$this->type."field_order, ".$this->type."field_type, ".$this->type."field_style, ".$this->type."field_dependency, ".$this->type."field_maxlength, ".$this->type."field_link, ".$this->type."field_options, ".$this->type."field_required, ".$this->type."field_regex) VALUES ('$field_info[field_cat_id]', '".$dep_languagevar_id."', '0', '1', '', '$field_info[field_id]', '100', '', '', '0', '')");
	            $dep_field_id = $database->database_insert_id();
	            $options[$d][dependent_id] = $dep_field_id;
	            $column_name = $this->type."value_".$dep_field_id;
	            $database->database_query("ALTER TABLE se_".$this->type."values ADD $column_name varchar(250) NOT NULL");
	          }
	        }
	      }

	      // DELETE OLD DEPENDENT FIELDS
	      for($d=0;$d<count($dependent_ids);$d++) {
	        $database->database_query("DELETE FROM se_".$this->type."fields, se_languagevars USING se_".$this->type."fields JOIN se_languagevars ON se_".$this->type."fields.".$this->type."field_title=se_languagevars.languagevar_id WHERE ".$this->type."field_id='$dependent_ids[$d]'");
	        $column_name = $this->type."value_".$dependent_ids[$d];
	        $database->database_query("ALTER TABLE se_".$this->type."values DROP COLUMN $column_name");
	      }

	      // DELETE OLD LANGUAGE VARS
        if( !empty($old_language_ids) && is_array($old_language_ids) )
          $database->database_query("DELETE FROM se_languagevars WHERE languagevar_id IN('".join("', '", $old_language_ids)."')");

	      // INSERT OPTIONS
	      $field_info[field_options] = $options;
	      $database->database_query("UPDATE se_".$this->type."fields SET ".$this->type."field_options='".serialize($options)."' WHERE ".$this->type."field_id='$field_info[field_id]'");


	    // NEW FIELD (ADD)
	    } else {

	      $field_order_info = $database->database_fetch_assoc($database->database_query("SELECT max(".$this->type."field_order) as f_order FROM se_".$this->type."fields WHERE ".$this->type."field_dependency='0' AND ".$this->type."field_".$this->type."cat_id='$field_info[field_cat_id]'"));
	      $field_order = $field_order_info[f_order]+1;
	      $field_info[field_title_id] = SE_Language::edit(0, $field_info[field_title], NULL, LANGUAGE_INDEX_FIELDS);
	      $field_info[field_desc_id] = SE_Language::edit(0, $field_info[field_desc], NULL, LANGUAGE_INDEX_FIELDS);
	      $field_info[field_error_id] = SE_Language::edit(0, $field_info[field_error], NULL, LANGUAGE_INDEX_FIELDS);
	      $database->database_query("INSERT INTO se_".$this->type."fields (".$this->type."field_".$this->type."cat_id, ".$this->type."field_title, ".$this->type."field_desc, ".$this->type."field_error, ".$this->type."field_order, ".$this->type."field_type, ".$this->type."field_style, ".$this->type."field_dependency, ".$this->type."field_maxlength, ".$this->type."field_link, ".$this->type."field_required, ".$this->type."field_regex, ".$this->type."field_html, ".$this->type."field_search, ".$this->type."field_display, ".$this->type."field_special) VALUES ('$field_info[field_cat_id]', '$field_info[field_title_id]', '$field_info[field_desc_id]', '$field_info[field_error_id]', '$field_order', '$field_info[field_type]', '$field_info[field_style]', '0', '$field_info[field_maxlength]', '$field_info[field_link]', '$field_info[field_required]', '$field_info[field_regex]', '$field_info[field_html]', '$field_info[field_search]', '$field_info[field_display]', '$field_info[field_special]')");
	      $field_info[field_id] = $database->database_insert_id();
	      $column_name = $this->type."value_".$field_info[field_id];
	      $database->database_query("ALTER TABLE se_".$this->type."values ADD $column_name $column_type NOT NULL $column_default");

	      // ADD DEPENDENT FIELDS
	      $field_options = "";
	      for($d=0;$d<count($options);$d++) {
		$label_languagevar_id = SE_Language::edit(0, $options[$d][label], NULL, LANGUAGE_INDEX_FIELDS);
		$options[$d][label] = $label_languagevar_id;
	        if($options[$d][dependency] == "1") {
		  $dep_languagevar_id = SE_Language::edit(0, $options[$d][dependent_label], NULL, LANGUAGE_INDEX_FIELDS);
	          $database->database_query("INSERT INTO se_".$this->type."fields (".$this->type."field_".$this->type."cat_id, ".$this->type."field_title, ".$this->type."field_order, ".$this->type."field_type, ".$this->type."field_style, ".$this->type."field_dependency, ".$this->type."field_maxlength, ".$this->type."field_link, ".$this->type."field_options, ".$this->type."field_required, ".$this->type."field_regex) VALUES ('$field_info[field_cat_id]', '".$dep_languagevar_id."', '$d', '1', '', '$field_info[field_id]', '100', '', '', '0', '')");
	          $dep_field_id = $database->database_insert_id();
	          $options[$d][dependent_id] = $dep_field_id;
	          $column_name = $this->type."value_".$dep_field_id;
	          $database->database_query("ALTER TABLE se_".$this->type."values ADD $column_name varchar(250) NOT NULL");
	        }
	      }

	      // INSERT OPTIONS
	      $field_info[field_options] = $options;
	      $database->database_query("UPDATE se_".$this->type."fields SET ".$this->type."field_options='".serialize($options)."' WHERE ".$this->type."field_id='$field_info[field_id]'");

	    }
	  }

	  return $field_info;

	} // END field_save() METHOD









	// THIS METHOD DELETES A FIELD AND ITS DEPENDENT FIELDS
	// INPUT: $field_id REPRESENTING THE FIELD'S ID
	// OUTPUT: 
	function field_delete($field_id) {
	  global $database;

	  // DELETE ALL FIELD COLUMNS
	  $fields = $database->database_query("SELECT ".$this->type."field_id AS field_id, ".$this->type."field_title AS field_title, ".$this->type."field_desc AS field_desc, ".$this->type."field_error AS field_error, ".$this->type."field_options AS field_options FROM se_".$this->type."fields WHERE ".$this->type."field_id='$field_id' OR ".$this->type."field_dependency='$field_id'");
	  while($field = $database->database_fetch_assoc($fields)) {

	    $languagevars_delete[] = $field[field_title];
	    $languagevars_delete[] = $field[field_desc];
	    $languagevars_delete[] = $field[field_error];

	    // DELETE OPTION LABELS
	    $field_options = unserialize($field[field_options]);
	    for($i=0;$i<count($field_options);$i++) { if($field_options[$i][label] != "") { $languagevars_delete[] = $field_options[$i][label]; }}

	    $column = $this->type."value_".$field[field_id];
	    $database->database_query("ALTER TABLE se_".$this->type."values DROP COLUMN $column");
	  }

	  // DELETE ALL FIELDS
	  $database->database_query("DELETE FROM se_languagevars WHERE languagevar_id IN(".implode(",", $languagevars_delete).")");
	  $database->database_query("DELETE FROM se_".$this->type."fields WHERE ".$this->type."field_id='$field_id' OR ".$this->type."field_dependency='$field_id'");

	} // END field_delete() METHOD









	// THIS METHOD DELETES A CATEGORY AND ITS SUBCATEGORIES/FIELDS
	// INPUT: $cat_id REPRESENTING THE CATEGORY ID OF THE CATEGORY TO DELETE
	// OUTPUT: 
	function cat_delete($cat_id) {
	  global $database;

	  $fields = $database->database_query("SELECT ".$this->type."field_id AS field_id, ".$this->type."field_title AS field_title, ".$this->type."field_desc AS field_desc, ".$this->type."field_error AS field_error FROM se_".$this->type."fields LEFT JOIN se_".$this->type."cats ON se_".$this->type."fields.".$this->type."field_".$this->type."cat_id=se_".$this->type."cats.".$this->type."cat_id WHERE se_".$this->type."cats.".$this->type."cat_id='$cat_id' OR se_".$this->type."cats.".$this->type."cat_dependency='$cat_id'");
	  while($field = $database->database_fetch_assoc($fields)) {
	    $column = $this->type."value_".$field[field_id];
	    $database->database_query("ALTER TABLE se_".$this->type."values DROP COLUMN $column");
	    $database->database_query("DELETE FROM se_languagevars WHERE languagevar_id='$field[field_title]' OR languagevar_id='$field[field_desc]' OR languagevar_id='$field[field_error]'");
	  }
	  $database->database_query("DELETE FROM se_languagevars USING se_".$this->type."cats JOIN se_languagevars ON se_".$this->type."cats.".$this->type."cat_title=se_languagevars.languagevar_id WHERE se_".$this->type."cats.".$this->type."cat_id='$cat_id' OR se_".$this->type."cats.".$this->type."cat_dependency='$cat_id'");
	  $database->database_query("DELETE FROM se_".$this->type."fields, se_".$this->type."cats USING se_".$this->type."cats LEFT JOIN se_".$this->type."fields ON se_".$this->type."fields.".$this->type."field_".$this->type."cat_id=se_".$this->type."cats.".$this->type."cat_id WHERE se_".$this->type."cats.".$this->type."cat_id='$cat_id' OR se_".$this->type."cats.".$this->type."cat_dependency='$cat_id'");

	} // END cat_delete() METHOD









	// THIS METHOD ADDS/EDIT A CATEGORY
	// INPUT: $cat_id REPRESENTING THE CATEGORY ID OF THE CATEGORY TO ADD/EDIT
	// OUTPUT: RETURNS THE CATEGORY ID
	function cat_modify($cat_id, $cat_title, $cat_dependency) {
	  global $database;

	  // NEW CATEGORY
	  if($cat_id == "new") {
	    $cat_order = $database->database_fetch_assoc($database->database_query("SELECT max(".$this->type."cat_order) AS cat_order FROM se_".$this->type."cats WHERE ".$this->type."cat_dependency='$cat_dependency'"));
	    $cat_order = $cat_order[cat_order]+1;
	    $cat_title = SE_Language::edit(0, $cat_title, NULL, LANGUAGE_INDEX_FIELDS);
	    $database->database_query("INSERT INTO se_".$this->type."cats (".$this->type."cat_dependency, ".$this->type."cat_title, ".$this->type."cat_order) VALUES ('$cat_dependency', '$cat_title', '$cat_order')");
	    $newcat_id = $database->database_insert_id();
  
	  // EDIT CATEGORY
	  } else {
	    $cat_info = $database->database_fetch_assoc($database->database_query("SELECT ".$this->type."cat_title AS cat_title FROM se_".$this->type."cats WHERE ".$this->type."cat_id='$cat_id'"));
	    SE_Language::edit($cat_info[cat_title], $cat_title);
	    $newcat_id = $cat_id;
	  }
	
	  return $newcat_id;

	} // END cat_modify() METHOD





}

?>