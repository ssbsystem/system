<?php
require_once("Modules/Objects/ImagetoBlob.php");

/**
 * Display Gallery
 */
class DisplayGallery
{
    function __construct()
    {
        require_once('Modules/Connect.php');
        $PDOConnect = new PDOConnect();
        $this->pdo = $PDOConnect->pdo;
    }

    public function createData($fModulePluginFK, $fPluginPluginFK, $fCustomPluginId, $pluginTable)
    {
        /** Includes */
        //GetData
        // require_once('Modules/GetData.php');
        // $getData = new GetData('ManualFiltering', false);


        // $main_data = $getData->Create($fModulePluginFK, $fPluginPluginFK, $fCustomPluginId, $pluginTable);

        // if (isset(ModuleMetaData::$uplodedData['IdOfData'])) {

        //     $id=ModuleMetaData::$uplodedData['IdOfData'];
        //     $imgUrl = 'C:/ssbs/system/php/uploads/img/';
        //     switch ($id) {
        //         case '1':
        //             $imgUrl = 'C:/ssbs/system/php/uploads/img/';
        //             break;
        //         default:
        //             $imgUrl = 'C:/ssbs/system/php/uploads/img/551.jpg';
        //             break;
        //     }
        // }

        // $imgUrl = 'uploads/img/551.jpg';

        // $photo = array();
        // $photos = array();

        // $photo['PhotoId'] = '1';
        // $photo['PhotoURL'] = $imgUrl;
        // $photo['PhotoName'] = '551.jpg';
        // array_push($photos, $photo);

        // $photo['PhotoId'] = '2';
        // $photo['PhotoURL'] = $imgUrl;
        // $photo['PhotoName'] = 'CB042.jpg';
        // array_push($photos, $photo);

        $main_data = array();
        $main_data[1]['Title'] = "Galéria";

        if (!(isset(ModuleMetaData::$uplodedData['newItemId']) && isset(ModuleMetaData::$uplodedData['newItemColumn']))) {
            return $main_data;
        }

        $cardId = ModuleMetaData::$uplodedData['newItemId'];
        $cardColumn = ModuleMetaData::$uplodedData['newItemColumn'];
        list($table, $column) = explode(".", $cardColumn);

        $query = "SELECT 
                    c_112_id,
                    c_80 AS isGallery,
                    c_81 AS galleryURL,
                    c_96 AS 'Number',
                    c_108_fk
                FROM t_112 
                LEFT JOIN t_113 
                ON t_112.c_113_fk=t_113.c_113_id
                LEFT JOIN $table 
                ON $table.c_113_fk=t_113.c_113_id
                WHERE $table.$column = :id
                AND c_96=1";

        $statement = $this->pdo->prepare($query);
        $statement->execute(
            array(
                ':id'    =>    $cardId
            )
        );
        foreach ($statement as $gallery) {
            $databaseURL = $gallery['galleryURL'];
        }

        $imagetoBlob = new ImagetoBlob();

        if (isset(ModuleMetaData::$uplodedData['isDownload'])) {
            if (ModuleMetaData::$uplodedData['isDownload'] == true) {
                $main_data[1]['Display']['Data'] = $imagetoBlob->Create($databaseURL);
            }
        } else {
            $main_data[1]['Display']['Data'] = [];
        }

        return $main_data;
    }
}
