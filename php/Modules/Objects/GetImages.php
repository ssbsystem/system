<?php
require_once("Modules/Objects/ImagetoBlob.php");

/**
 * Get Images
 */
class GetImages
{
    function __construct()
    {
        require_once('Modules/Connect.php');
        $PDOConnect = new PDOConnect();
        $this->pdo = $PDOConnect->pdo;
    }

    public function CreateData($uplodedData)
    {
        $main_data = array();
        $main_data['Title'] = "Galéria";

        if (!(isset($uplodedData['newItemId']) && isset($uplodedData['newItemColumn']))) {
            return $main_data;
        }

        $cardId = $uplodedData['newItemId'];
        $cardColumn = $uplodedData['newItemColumn'];
        $offset = $uplodedData['Offset'];
        list($table, $column) = explode(".", $cardColumn);

        $query = "SELECT 
                    c_112_id,
                    c_80 AS isGallery,
                    c_81 AS galleryURL,
                    c_108_fk
                FROM t_112 LEFT JOIN $table ON $table.c_112_FK=t_112.c_112_id
                WHERE $table.$column = :id";

        $statement = $this->pdo->prepare($query);
        $statement->execute(
            array(
                ':id'    =>    $cardId
            )
        );

        $galleryArray = $statement->fetchAll();

        if (sizeof($galleryArray) === 0) {
            $main_data['Images'] = [];
            return $main_data;
        }

        $galleryData = $galleryArray[0];
        $databaseURL = $galleryData['galleryURL'];

        $main_data = $this->CreateImage($databaseURL, $offset);

        return $main_data;
    }

    public function CreateImage($imgUrl, $offset)
    {
        $idNo = 0;
        $state = 'more';

        $pos = strpos($imgUrl, '.');
        if ($pos === false) {
            //URL is a directory
            $imagesBlobs = array();
            $imageArray = glob($imgUrl . "*.*");

            $length = $offset + 10;
            $array_legth = count($imageArray);

            if ($length > $array_legth) {
                $length = $array_legth;
                $state = 'end';
            } else if ($length == $array_legth) {
                $state = 'end';
            }

            for ($i = $offset; $i < $length; $i++) {
                $imgUrl = $imageArray[$i];

                $item = array();
                $imgParts = pathinfo($imgUrl);
                $item['URL'] = $imgUrl;
                $item['IdNo'] = $i;
                $item['Basename'] = $imgParts['basename'];

                array_push($imagesBlobs, $item);
            }

            $result = array();
            $result['Images'] = $imagesBlobs;
            $result['State'] = $state;

            return $result;
        } else {
            //URL is a file

            $item = array();
            $imgParts = pathinfo($imgUrl);
            $item['URL'] = $imgUrl;
            $item['IdNo'] = $idNo;
            $item['Basename'] = $imgParts['basename'];
            return $item;
        }
    }
}
