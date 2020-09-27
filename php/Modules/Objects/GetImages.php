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

        $imagetoBlob = new ImagetoBlob();
        $main_data['Images'] = $imagetoBlob->Create($databaseURL);

        return $main_data;
    }
}
