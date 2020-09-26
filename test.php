<?php

$dirname = "php/uploads/img/";
$imageArray = glob($dirname . "*.jpg");

foreach ($imageArray as $image_name) {
  $imgParts = pathinfo($image_name);
  $imgExtension = $imgParts['extension'];

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
  $img = imagescale($image, 500, 500);

  // Output image in the browser 
  ob_start();
  imagepng($img);
  $i = ob_get_clean();
  echo '<img src="data:image/' . $imgExtension . ';base64,' . base64_encode($i) . '"/>';
}
