<?php
require_once "config.php";
session_start();
$id=$_SESSION["id"];
$query="SELECT SUM(quantity) as number, name FROM purchase WHERE sid='$id' GROUP BY name";
$result=mysqli_query($link,$query);
$num_poller = mysqli_num_rows($result);
$total_sale = 0;
while($row = mysqli_fetch_array($result))
{
  $total_sale += $row{'number'};
}
mysqli_data_seek($result,0);
putenv('GDFONTPATH=Fonts');
$font = 'fonts/arial.ttf';
//putenv('GDFONTPATH=' . realpath('.'));
//$font = "Arial";
$y = 50;
$width = 700;
$bar_height = 20; 
$height = $num_poller * $bar_height * 1.5 + 70;
$bar_unit = ($width - 400) / 100; 
$image = imagecreate($width, $height);
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
$red   = imagecolorallocate($image, 255, 0, 0);
$blue  = imagecolorallocate($image,0,0,255);
imagefill($image,$width,$height,$white);
imagerectangle($image, 0, 0, $width-1, $height-1, $black);
while($row = mysqli_fetch_object($result))
{
	if ($total_sale > 0)
		$percent = intval(round(($row->number/$total_sale)*100));
	else
		$percent = 0;
ImageTTFText($image,12,0,10, $y+($bar_height/2), $black,$font, $row->name);
ImageTTFText($image, 12, 0, 170, $y + ($bar_height/2),$red,$font,$percent.'%');
$bar_length = $percent * $bar_unit;
ImageRectangle($image, $bar_length+221, $y-2, (220+(100*$bar_unit)), $y+$bar_height, $black);
ImageFilledRectangle($image,220,$y-2,220+$bar_length, $y+$bar_height, $blue);
ImageTTFText($image, 12, 0, 250+100*$bar_unit, $y+($bar_height/2), $black, "fonts/arial.ttf", $row->number.' sold.');
$y = $y + ($bar_height * 1.5);
}
header("Content-Type: image/jpeg");
imagejpeg($image);
imagedestroy($image);
