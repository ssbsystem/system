<?php
require_once("Modules/Objects/Image.php");

/**
 * Get One Image
 */
class GetOneImage
{
    private $imgUrl;

    function __construct($imgUrl)
    {
        $this->imgUrl = $imgUrl;
    }

    public function Create()
    {
        $result = array();

        $info = pathinfo($this->imgUrl);
        $extension = $info['extension'];
        $fileName = $info['basename'];

        $image = new Image($this->imgUrl);
        $image->scaleImage(400, 400);
        $base64 = $image->getBase64File();

        $blobString = 'data:image/' . $extension . ';base64,' . base64_encode($base64);

        $result['BlobString'] = $blobString;
        $result['Alt'] = $fileName;
        return $result;
    }
}
