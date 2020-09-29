<?php

/**
 * ImagetoBlob
 */
class ImagetoBlob
{
    function __construct()
    {
        require_once('Modules/Connect.php');
        $PDOConnect = new PDOConnect();
        $this->pdo = $PDOConnect->pdo;
    }

    public function Create($imgUrl)
    {
        $imgParts = pathinfo($imgUrl);
        $imgExtension = $imgParts['extension'];
        $imgFileName = $imgParts['basename'];

        /* To reserve aspect ratio*/
        list($width, $height) = getimagesize($imgUrl);
        $newheight = 180;       // Fixed new heigth for every photo in px
        $newwidth = $width / ($height / $newheight);

        // Load image file
        switch ($imgExtension) {
            case 'jpg':
                $image = imagecreatefromjpeg($imgUrl);
                break;
            case 'png':
                $image = imagecreatefrompng($imgUrl);
                break;
            case 'bmp':
                $image = imagecreatefrombmp($imgUrl);
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
        $base64 = 'data:image/' . $imgExtension . ';base64,' . base64_encode($i);

        $pData['BlobString'] = $base64;
        $pData['Alt'] = $imgFileName;

        return $pData;
    }
}
