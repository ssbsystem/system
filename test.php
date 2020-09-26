<?php

$dirname = "php/uploads/img/";
$imageArray = glob($dirname . "*.*");

foreach ($imageArray as $image_name) {
  $imgParts = pathinfo($image_name);
  $imgExtension = $imgParts['extension'];

  /* To reserve aspect ratio*/
  list($width, $height) = getimagesize($image_name);
  $newheight = 200;       // Fixed new heigth for every photo in px
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
  imagepng($img);
  $i = ob_get_clean();
  echo '<img src="data:image/' . $imgExtension . ';base64,' . base64_encode($i) . '"/>';
}
