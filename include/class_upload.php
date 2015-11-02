<?php

//  THIS CLASS CONTAINS UPLOAD-RELATED METHODS.
//  IT IS USED DURING THE UPLOAD OF A FILE.
//  METHODS IN THIS CLASS:
//    new_upload()
//    upload_file()
//    upload_photo()
//    upload_thumb()
//    image_resize_on()
//    ConvertBMP2GD()
//    imagecreatefrombmp()


class se_upload
{
	// INITIALIZE VARIABLES
	var $is_error = 0;		// DETERMINES WHETHER THERE IS AN ERROR OR NOT, CONTAINS RELEVANT ERROR CODE

	var $file_name;			// CONTAINS NAME OF UPLOADED FILE
	var $file_type;			// CONTAINS UPLOADED FILE MIME TYPE
	var $file_size;			// CONTAINS UPLOADED FILE SIZE
	var $file_tempname;		// CONTAINS TEMP NAME OF UPLOADED FILE
	var $file_error;		// CONTAINS UPLOADED FILE ERROR
	var $file_ext;			// CONTAINS UPLOADED FILE EXTENSION
	var $file_width;		// CONTAINS UPLOADED IMAGE WIDTH
	var $file_height;		// CONTAINS UPLOADED IMAGE HEIGHT

	var $is_image;			// DETERMINES WHETHER FILE IS AN IMAGE OR NOT
	var $file_maxwidth;		// CONTAINS THE MAXIMUM WIDTH OF AN UPLOADED IMAGE
	var $file_maxheight;		// CONTAINS THE MAXIMUM HEIGHT OF AN UPLOADED IMAGE






	// THIS METHOD SETS INITIAL VARS SUCH AS FILE NAME
	// INPUT: $file REPRESENTING THE NAME OF THE FILE INPUT
	//	  $file_maxsize REPRESENTING THE MAXIMUM ALLOWED FILESIZE
	//	  $file_exts REPRESENTING AN ARRAY OF LOWERCASE ALLOWABLE EXTENSIONS
	//	  $file_types REPRESENTING AN ARRAY OF LOWERCASE ALLOWABLE MIME TYPES
	//	  $file_maxwidth (OPTIONAL) REPRESENTING THE MAXIMUM WIDTH OF THE UPLOADED PHOTO
	//	  $file_maxheight (OPTIONAL) REPRESENTING THE MAXIMUM HEIGHT OF THE UPLOADED PHOTO
	// OUTPUT: 
  
	function new_upload($file, $file_maxsize, $file_exts, $file_types, $file_maxwidth = "", $file_maxheight = "")
  {
	  // GET FILE VARS
	  $this->file_name = $_FILES[$file]['name'];
	  $this->file_type = strtolower($_FILES[$file]['type']);
	  $this->file_size = $_FILES[$file]['size'];
	  $this->file_tempname = $_FILES[$file]['tmp_name'];
	  $this->file_error = $_FILES[$file]['error'];
	  $this->file_ext = strtolower(str_replace(".", "", strrchr($this->file_name, "."))); 
    
	  $file_dimensions = @getimagesize($this->file_tempname);
	  $this->file_width = $file_dimensions[0];
	  $this->file_height = $file_dimensions[1];
	  if($file_maxwidth == "") { $file_maxwidth = $this->file_width; }
	  if($file_maxheight == "") { $file_maxheight = $this->file_height; }
	  $this->file_maxwidth = $file_maxwidth;
	  $this->file_maxheight = $file_maxheight;
    
	  // ENSURE THE FILE IS AN UPLOADED FILE
	  if( !is_uploaded_file($this->file_tempname) )
      $this->is_error = 718;
    
	  // CHECK THAT FILESIZE IS LESS THAN GIVEN FILE MAXSIZE
	  if( $this->file_size > $file_maxsize )
      $this->is_error = 719;
    
	  // CHECK EXTENSION OF FILE TO MAKE SURE ITS ALLOWED
	  if( !in_array($this->file_ext, $file_exts) )
      $this->is_error = 720;
    
	  // CHECK MIME TYPE OF FILE TO MAKE SURE ITS ALLOWED
	  if( !in_array($this->file_type, $file_types) )
      $this->is_error = 720;
    
	  // DETERMINE IF FILE IS A PHOTO (AND IF GD CAN BE USED) - DO NOT COUNT GIFs AS IMAGES, OTHERWISE ANIMATION WON'T WORK!!
	  if( $file_dimensions !== FALSE && in_array($this->file_ext, Array('jpg', 'jpeg', 'png', 'bmp', 'gif')) !== FALSE )
    {
	    $this->is_image = 1;
	    // ENSURE THE UPLOADED FILE IS NOT LARGER THAN MAX WIDTH AND HEIGHT IF GD IS NOT AVAILABLE
	    if( !$this->image_resize_on() )
      {
	      $this->is_image = 0;
	      if($this->file_width > $this->file_maxwidth || $this->file_height > $this->file_maxheight)
          $this->is_error = 721;
	    }
      
	    // IF THIS IS A GIF, RESIZE ONLY IF IT IS GREATER THAN THE MAX WIDTH/HEIGHT, OTHERWISE SIMPLY MOVE
	    if($this->file_ext == 'gif' && $this->file_width <= $this->file_maxwidth && $this->file_height <= $this->file_maxheight)
      {
	      $this->is_image = 0;
	    }
	  }
    else
    {
	    $this->is_image = 0;
	  }
	}
  
  // END new_upload() METHOD









	// THIS METHOD UPLOADS A FILE
	// INPUT: $file_dest REPRESENTS THE DESTINATION OF THE UPLOADED FILE
	// OUTPUT: BOOLEAN INDICATING WHETHER UPLOAD SUCCEEDED OR FAILED
  
	function upload_file($file_dest)
  { 
	  // TRY MOVING UPLOADED FILE, RETURN ERROR UPON FAILURE
    if( !move_uploaded_file($this->file_tempname, $file_dest) )
    { 
	    $this->is_error = 718; 
	    return false;
	  }
    else
    {
	    chmod($file_dest, 0777);
	    return true;
	  }
	}
  
  // END upload_file() METHOD









	// THIS METHOD UPLOADS A PHOTO
	// INPUT: $photo_dest REPRESENTS THE DESTINATION OF THE UPLOADED PHOTO
	//	  $file_maxwidth (OPTIONAL) REPRESENTING THE MAXIMUM WIDTH OF THE UPLOADED PHOTO
	//	  $file_maxheight (OPTIONAL) REPRESENTING THE MAXIMUM HEIGHT OF THE UPLOADED PHOTO
	// OUTPUT: BOOLEAN INDICATING WHETHER UPLOAD SUCCEEDED OR FAILED
	function upload_photo($photo_dest, $file_maxwidth = "", $file_maxheight = "")
  {
	  // SET MAX WIDTH AND HEIGHT
	  if( !$file_maxwidth  ) $file_maxwidth  = $this->file_maxwidth ;
	  if( !$file_maxheight ) $file_maxheight = $this->file_maxheight;
    
	  // CHECK IF DIMENSIONS ARE LARGER THAN ADMIN SPECIFIED SETTINGS
	  // AND SET DESIRED WIDTH AND HEIGHT
    $width  = $this->file_width ;
    $height = $this->file_height;
    if( $height > $file_maxheight )
    { 
      $width = floor($width * $file_maxheight / $height); 
      $height = $file_maxheight; 
    } 
    if( $width > $file_maxwidth )
    {
      $height = floor($height * $file_maxwidth / $width);
      $width = $file_maxwidth;
    }
    
    
	  // RESIZE IMAGE AND PUT IN USER DIRECTORY
	  switch($this->file_ext)
    {
	    case "gif":
	      $file = imagecreatetruecolor($width, $height);
	      $new = imagecreatefromgif($this->file_tempname);
	      $kek=imagecolorallocate($file, 255, 255, 255);
	      imagefill($file,0,0,$kek);
	      imagecopyresampled($file, $new, 0, 0, 0, 0, $width, $height, $this->file_width, $this->file_height);
	      imagejpeg($file, $photo_dest, 100);
	      ImageDestroy($new);
	      ImageDestroy($file);
	      break;
      
	    case "bmp":
	      $file = imagecreatetruecolor($width, $height);
	      $new = $this->imagecreatefrombmp($this->file_tempname);
	      for($i=0; $i<256; $i++) { imagecolorallocate($file, $i, $i, $i); }
	      imagecopyresampled($file, $new, 0, 0, 0, 0, $width, $height, $this->file_width, $this->file_height); 
	      imagejpeg($file, $photo_dest, 100);
	      ImageDestroy($new);
	      ImageDestroy($file);
	      break;
      
	    case "jpeg":
	    case "jpg":
	      $file = imagecreatetruecolor($width, $height);
	      $new = imagecreatefromjpeg($this->file_tempname);
	      for($i=0; $i<256; $i++) { imagecolorallocate($file, $i, $i, $i); }
	      imagecopyresampled($file, $new, 0, 0, 0, 0, $width, $height, $this->file_width, $this->file_height);
	      imagejpeg($file, $photo_dest, 100);
	      ImageDestroy($new);
	      ImageDestroy($file);
	      break;
      
	    case "png":
	      $file = imagecreatetruecolor($width, $height);
	      $new = imagecreatefrompng($this->file_tempname);
	      for($i=0; $i<256; $i++) { imagecolorallocate($file, $i, $i, $i); }
	      imagecopyresampled($file, $new, 0, 0, 0, 0, $width, $height, $this->file_width, $this->file_height); 
	      imagejpeg($file, $photo_dest, 100);
	      ImageDestroy($new);
	      ImageDestroy($file);
	      break;
	  } 
    
	  chmod($photo_dest, 0777);
    
	  return true;
	}
  
  // END upload_photo() METHOD









	// THIS METHOD CREATES A SQUARE THUMBNAIL
	// INPUT: $photo_dest REPRESENTS THE DESTINATION OF THE UPLOADED PHOTO
	//	  $file_maxdim (OPTIONAL) REPRESENTING THE MAXIMUM WIDTH AND HEIGHT OF THE UPLOADED PHOTO
	// OUTPUT: BOOLEAN INDICATING WHETHER UPLOAD SUCCEEDED OR FAILED
  
	function upload_thumb($photo_dest, $file_maxdim = "60")
  {
	  // SET DESIRED WIDTH AND HEIGHT
    $x = 0;
    $y = 0;
	  $width = $this->file_width;
	  $height = $this->file_height;
	  if($width > $height)
    { 
	    $x = ceil(($width - $height) / 2);
	    $width = $height;
	  }
    elseif($width < $height)
    {
	    $y = ceil(($height - $width) / 2);
	    $height = $width;
	  }
    
	  // RESIZE IMAGE AND PUT IN USER DIRECTORY
	  switch($this->file_ext)
    {
	    case "gif":
	      $file = imagecreatetruecolor($file_maxdim, $file_maxdim);
	      $new = imagecreatefromgif($this->file_tempname);
	      $kek=imagecolorallocate($file, 255, 255, 255);
	      imagefill($file,0,0,$kek);
	      imagecopyresampled($file, $new, 0, 0, $x, $y, $file_maxdim, $file_maxdim, $width, $height);
	      imagejpeg($file, $photo_dest, 100);
	      ImageDestroy($new);
	      ImageDestroy($file);
	      break;
      
	    case "bmp":
	      $file = imagecreatetruecolor($file_maxdim, $file_maxdim);
	      $new = $this->imagecreatefrombmp($this->file_tempname);
	      for($i=0; $i<256; $i++) { imagecolorallocate($file, $i, $i, $i); }
	      imagecopyresampled($file, $new, 0, 0, $x, $y, $file_maxdim, $file_maxdim, $width, $height); 
	      imagejpeg($file, $photo_dest, 100);
	      ImageDestroy($new);
	      ImageDestroy($file);
	      break;
      
	    case "jpeg":
	    case "jpg":
	      $file = imagecreatetruecolor($file_maxdim, $file_maxdim);
	      $new = imagecreatefromjpeg($this->file_tempname);
	      for($i=0; $i<256; $i++) { imagecolorallocate($file, $i, $i, $i); }
	      imagecopyresampled($file, $new, 0, 0, $x, $y, $file_maxdim, $file_maxdim, $width, $height);
	      imagejpeg($file, $photo_dest, 100);
	      ImageDestroy($new);
	      ImageDestroy($file);
	      break;
      
	    case "png":
	      $file = imagecreatetruecolor($file_maxdim, $file_maxdim);
	      $new = imagecreatefrompng($this->file_tempname);
	      for($i=0; $i<256; $i++) { imagecolorallocate($file, $i, $i, $i); }
	      imagecopyresampled($file, $new, 0, 0, $x, $y, $file_maxdim, $file_maxdim, $width, $height); 
	      imagejpeg($file, $photo_dest, 100);
	      ImageDestroy($new);
	      ImageDestroy($file);
	      break;
	  } 
    
	  chmod($photo_dest, 0777);
    
	  return true;
	}
  
  // END upload_thumb() METHOD









  // THIS METHOD CHECKS FOR NECESSARY IMAGE RESIZING SUPPORT
  // INPUT:
  // OUTPUT: BOOLEAN INDICATING WHETHER GD CAN BE USED TO RESIZE IMAGES

  function image_resize_on()
  {
    // CHECK IF GD LIBRARY IS INSTALLED
    if( !is_callable('gd_info') ) return false;

    $gd_info = gd_info();
    preg_match('/\d/', $gd_info['GD Version'], $match);
    $gd_ver = $match[0];

    if( $gd_ver < 2 ) return false;
    if( empty($gd_info['GIF Read Support']) ) return false;
    if( empty($gd_info['JPG Support']) && empty($gd_info['JPEG Support']) ) return false;
    if( empty($gd_info['PNG Support']) ) return false;

    return true;
  }
  
  // END image_resize_on() METHOD









	// THIS METHOD CONVERTS BMP TO GD
	// INPUT: $src REPRESENTING THE SOURCE OF THE BMP
	//	  $dest (OPTIONAL) REPRESENTING THE DESTINATION OF THE GD
	// OUTPUT: BOOLEAN INDICATING WHETHER THE CONVERSION SUCCEEDED OR FAILED
  
	function ConvertBMP2GD($src, $dest = false)
  {
	  if(!($src_f = fopen($src, "rb")))
    {
	    return false;
	  }
    
	  if(!($dest_f = fopen($dest, "wb")))
    {
	    return false;
	  }
    
	  $header = unpack("vtype/Vsize/v2reserved/Voffset", fread($src_f, 14));
	  $info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant", fread($src_f, 40));
    
	  extract($info);
	  extract($header);
    
	  if($type != 0x4D42) {  // signature "BM"
	    return false;
	  }
    
	  $palette_size = $offset - 54;
	  $ncolor = $palette_size / 4;
	  $gd_header = "";
	  // true-color vs. palette
	  $gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF"; 
	  $gd_header .= pack("n2", $width, $height);
	  $gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
	  if($palette_size) {
	    $gd_header .= pack("n", $ncolor);
	  }
	  // no transparency
	  $gd_header .= "\xFF\xFF\xFF\xFF";     
    
	  fwrite($dest_f, $gd_header);
    
	  if($palette_size)
    {
	    $palette = fread($src_f, $palette_size);
	    $gd_palette = "";
	    $j = 0;
	    while($j < $palette_size) {
	      $b = $palette{$j++};
	      $g = $palette{$j++};
	      $r = $palette{$j++};
	      $a = $palette{$j++};
	      $gd_palette .= "$r$g$b$a";
	    }
	    $gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);
	    fwrite($dest_f, $gd_palette);
	  }
    
	  $scan_line_size = (($bits * $width) + 7) >> 3;
	  $scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size & 0x03) : 0;
    
	  for($i = 0, $l = $height - 1; $i < $height; $i++, $l--)
    {
	    // BMP stores scan lines starting from bottom
	    fseek($src_f, $offset + (($scan_line_size + $scan_line_align) * $l));
	    $scan_line = fread($src_f, $scan_line_size);
	    if($bits == 24) {
	      $gd_scan_line = "";
	      $j = 0;
	      while($j < $scan_line_size) {
	        $b = $scan_line{$j++};
	        $g = $scan_line{$j++};
	        $r = $scan_line{$j++};
	        $gd_scan_line .= "\x00$r$g$b";
	      }
	    } elseif($bits == 8) {
	      $gd_scan_line = $scan_line;
	    } elseif($bits == 4) {
	      $gd_scan_line = "";
	      $j = 0;
	      while($j < $scan_line_size) {
	        $byte = ord($scan_line{$j++});
	        $p1 = chr($byte >> 4);
	        $p2 = chr($byte & 0x0F);
	        $gd_scan_line .= "$p1$p2";
	      } 
	      $gd_scan_line = substr($gd_scan_line, 0, $width);
	    } elseif($bits == 1) {
	      $gd_scan_line = "";
	      $j = 0;
	      while($j < $scan_line_size) {
	        $byte = ord($scan_line{$j++});
	        $p1 = chr((int) (($byte & 0x80) != 0));
	        $p2 = chr((int) (($byte & 0x40) != 0));
	        $p3 = chr((int) (($byte & 0x20) != 0));
	        $p4 = chr((int) (($byte & 0x10) != 0)); 
	        $p5 = chr((int) (($byte & 0x08) != 0));
	        $p6 = chr((int) (($byte & 0x04) != 0));
	        $p7 = chr((int) (($byte & 0x02) != 0));
	        $p8 = chr((int) (($byte & 0x01) != 0));
	        $gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
	      } 
	      $gd_scan_line = substr($gd_scan_line, 0, $width);
	    }
	    
	    fwrite($dest_f, $gd_scan_line);
	  }
    
	  fclose($src_f);
	  fclose($dest_f);
    
	  return true;
	}
  
  // END ConvertBMP2GD() METHOD
	








	// THIS METHOD CREATES IMAGE FROM BMP FUNCTION
	// INPUT: $filename REPRESENTING THE NAME OF THE FILE TO BE USED FOR CREATION
	// OUTPUT: BOOLEAN INDICATING WHETHER THE CREATION SUCCEEDED OR FAILED
	function imagecreatefrombmp($filename)
  {
	  $tmp_name = tempnam("/tmp", "GD");
	  if($this->ConvertBMP2GD($filename, $tmp_name))
    {
	    $img = imagecreatefromgd($tmp_name);
	    unlink($tmp_name);
	    return $img;
	  }
    else
    {
	    return false;
	  }
	}
  
  //END imagecreatefrombmp() METHOD
}

?>