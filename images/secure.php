<?php
if(!function_exists('gd_info')) { exit(); }

//session_start();

header("Cache-Control: no-cache"); 
header("Content-type: image/png"); 

chdir('..');
include "header.php";

$img_handle = @ImageCreate(67, 20); 
$back_color = @ImageColorAllocate($img_handle, 255, 255, 255);
$transparent_bg = @ImageColorTransparent($img_handle, $back_color);

$count = 0;
$code = "";
while($count < 6)
{
  $count++;
  $x_axis = -5 + ($count * 10);
  $y_axis = rand(0, 7);
  $color1 = rand(001, 150);
  $color2 = rand(001, 150);
  $color3 = rand(001, 150);
  $txt_color[$count] = @ImageColorAllocate($img_handle, $color1, $color2, $color3); 
  $size = rand(3,5);
  $number = rand(0,9);
  $code .= "$number";
  @ImageString($img_handle, $size, $x_axis, $y_axis, "$number", $txt_color[$count]); 
}

$pixel_color = @ImageColorAllocate($img_handle, 100, 100, 100); 

$_SESSION['code'] = $code;
if( !isset($_SESSION['codes']) ) $_SESSION['codes'] = array();
$_SESSION['codes'][] = array(
  'expires' => time() + 300,
  'code' => $code,
);
@ImagePng($img_handle); 
exit();
?>