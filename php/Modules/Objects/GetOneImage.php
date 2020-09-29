<?php
require_once("Modules/Objects/ImagetoBlob.php");

/**
 * Get One Image
 */
class GetOneImage
{
    function __construct()
    {
        require_once('Modules/Connect.php');
        $PDOConnect = new PDOConnect();
        $this->pdo = $PDOConnect->pdo;
    }

    public function Create($imgUrl)
    {
        $imageToBlob = new ImagetoBlob();
        return $imageToBlob->Create($imgUrl);
    }
}
