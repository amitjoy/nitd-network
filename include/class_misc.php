<?php

//  THIS CLASS CONTAINS MISC METHODS TO BE AVAILABLE TO SMARTY
//  METHODS IN THIS CLASS:
//    photo_size()





class se_misc
{
	// THIS METHOD RETURNS WIDTH OR HEIGHT, PROPORTIONALLY, BASED ON GIVEN MAX WIDTH AND MAX HEIGHT
	// INPUT: $photo REPRESENTING THE PATH TO THE PHOTO
	//	  $max_width REPRESENTING THE MAXIMUM WIDTH IN PIXELS
	//	  $max_height REPRESENTING THE MAXIMUM HEIGHT IN PIXELS
	//	  $return_value (OPTIONAL) REPRESENTING THE VALUE TO RETURN (CAN BE "w" FOR WIDTH OR "h" FOR HEIGHT)
	// OUTPUT: A WIDTH OR HEIGHT IN PIXELS THAT SCALES THE PHOTO BASED ON A MAX WIDTH AND HEIGHT
	function photo_size($photo, $max_width, $max_height, $return_value = "w")
  {
	  $dimensions = @getimagesize($photo);
	  $width = $dimensions[0];
	  $height = $dimensions[1];
    
	  if($width > $max_width || $height > $max_height)
    { 
	    if($width > $max_width)
      {
	      $height = $height*$max_width/$width;
	      $width = $max_width;
	    }
      
	    if($height > $max_height)
      {
	      $width = $width*$max_height/$height;
	      $height = $max_height;
	    }
	  }
    
	  if($return_value == "w") { $image_dimension = $width; } else { $image_dimension = $height; }
    
	  return round($image_dimension, 2);
	}
  
  // END photo_size() METHOD
}

?>