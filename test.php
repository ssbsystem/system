<?php
//stop
$dirname = "php/uploads/img/desc/t_202-c_202_id-2/1 (1).jpg";/*
$imageArray = glob($dirname . "*.*");*/
$j = 0;

//foreach ($imageArray as $image_name) {
$image_name = $dirname;
$imgParts = pathinfo($image_name);
$imgExtension = $imgParts['extension'];

/* To reserve aspect ratio*/
list($width, $height) = getimagesize($image_name);
$newheight = 900;       // Fixed new heigth for every photo in px
$newwidth = $width / ($height / $newheight);

// Load image file
switch ($imgExtension) {
  case 'jpg':
    $image = imagecreatefromjpeg($image_name);
    break;
  case 'png':
    $image = imagecreatefrompng($image_name);
    break;
  case 'bmp':
    $image = imagecreatefrombmp($image_name);
    break;
  default:
    die('Faild!');
    break;
}

// Use imagescale() function to scale the image 
$img = imagescale($image, $newwidth, $newheight);

// Output image in the browser 
ob_start();
switch ($imgExtension) {
  case 'jpg':
    imagejpeg($img);
    break;
  case 'png':
    imagejpeg($img);
    break;
  case 'bmp':
    imagebmp($img);
    break;
  default:
    die('Faild!');
    break;
}
$i = ob_get_clean();

$targetFileM = "php/uploads/img/mob/t_$j.jpg";

file_put_contents($targetFileM, $i);
echo '<img src="data:image/' . $imgExtension . ';base64,' . base64_encode($i) . '"/>';

++$j;
//}
