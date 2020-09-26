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

        $photos = array();
        // $main_data = $getData->Create($fModulePluginFK, $fPluginPluginFK, $fCustomPluginId, $pluginTable);

        $imgUrl = 'uploads/img/';

        $photo = array();

        $photo['PhotoId'] = '1';
        $photo['PhotoURL'] = $imgUrl;
        $photo['PhotoName'] = '551.jpg';
        array_push($photos, $photo);

        $photo['PhotoId'] = '2';
        $photo['PhotoURL'] = $imgUrl;
        $photo['PhotoName'] = 'CB042.jpg';
        array_push($photos, $photo);

        $main_data = array();

        $imagetoBlob = new ImagetoBlob();
        $main_data[1]['Title'] = "Galéria";

        if (isset(ModuleMetaData::$uplodedData['isDownload'])) {
            if (ModuleMetaData::$uplodedData['isDownload'] == true) {
                $main_data[1]['Display']['Data'] = $imagetoBlob->Create($photos);
            }
        } else {
            $main_data[1]['Display']['Data'] = [];
        }

        return $main_data;
    }
}
