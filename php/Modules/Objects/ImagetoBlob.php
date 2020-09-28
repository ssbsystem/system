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
        $main_data = array();

        $pos = strpos($imgUrl, '.');
        if ($pos === false) {
            //URL is a directory
            $main_data = $this->CreateBlobFromDir($imgUrl);
        } else {
            //URL is a file
            $main_data = $this->CreateBlobFromImage($imgUrl);
        }
        
        return $main_data;
    }
    public function CreateBlobFromImage($imgUrl)
    {
        $imagesBlobs = array();
        $pData = array();

        $type = pathinfo($imgUrl, PATHINFO_EXTENSION);
        $photodata = file_get_contents($imgUrl);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($photodata);
        $imgFileName = pathinfo($imgUrl, PATHINFO_BASENAME);
        
        $pData['imgId'] = '_img';
        $pData['imgBlob'] = $base64;
        $pData['imgAlt'] = $imgFileName;

        array_push($imagesBlobs, $pData);
        return $imagesBlobs;

    }
    public function CreateBlobFromDir($imgUrl)
    {
        $imagesBlobs = array();
        $pData = array();

        $imageArray = glob($imgUrl . "*.*");
        $idNo = 0;

        foreach ($imageArray as $image_name) {
            $imgParts = pathinfo($image_name);
            $imgExtension = $imgParts['extension'];
            $imgFileName = $imgParts['basename'];

            /* To reserve aspect ratio*/
            list($width, $height) = getimagesize($image_name);
            $newheight = 180;       // Fixed new heigth for every photo in px
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
            $base64 = 'data:image/' . $imgExtension . ';base64,' . base64_encode($i);

            $pData['imgId'] = '_img_' . $idNo;
            $pData['imgBlob'] = $base64;
            $pData['imgAlt'] = $imgFileName;

            array_push($imagesBlobs, $pData);

            $idNo = $idNo + 1;

        }
        return $imagesBlobs;
    }
}
