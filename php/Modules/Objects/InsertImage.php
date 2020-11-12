<?php

/**
 * Insert Image
 */
class InsertImage
{
    private $pdo;
    private $isMobile;

    function __construct()
    {
        require_once('Modules/Connect.php');
        $PDOConnect = new PDOConnect();
        $this->pdo = $PDOConnect->pdo;

        $this->isMobile = true;
    }
    public function Create($data)
    {
        $main_data = array();

        $data = json_decode($data, true);
        $entryId = $data['EntryId'];
        $cardId = $entryId['Id'];
        $cardColumn = $entryId['IdColumn'];
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
                ':id' => $cardId
            )
        );

        $galleryArray = $statement->fetchAll();

        $entryDir = "$cardColumn.$cardId";
        $entryDir = str_replace(".", '-', $entryDir);
        $path = "uploads/img/desc/$entryDir/";
        $pathM = "uploads/img/mob/$entryDir/";

        if (sizeof($galleryArray) === 0) {
            if (!file_exists("uploads/img/desc")) {
                mkdir("uploads/img/desc");
            }

            if (!file_exists("uploads/img/desc/$entryDir")) {
                mkdir("uploads/img/desc/$entryDir");
            }

            $sql = "INSERT INTO t_113 (`c_113_id`) VALUES (?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([null]);
            $c113Id = $this->pdo->lastInsertId();

            $this->insertDataToDB($path, $cardId, $table, $column, $c113Id, 1);

            if ($this->isMobile) {
                if (!file_exists("uploads/img/mob")) {
                    mkdir("uploads/img/mob");
                }

                if (!file_exists("uploads/img/mob/$entryDir")) {
                    mkdir("uploads/img/mob/$entryDir");
                }

                $this->insertDataToDB($pathM, $cardId, $table, $column, $c113Id, 2);
            }
        }

        $filesData = $data['FileToUpload'];

        for ($i = 0; $i < sizeof($filesData); $i++) {
            $itemName = $filesData[$i];
            $main_data[$i] = $this->uploadImage($itemName, $path, $pathM);
        }

        return $main_data;
    }

    function insertDataToDB($path, $cardId, $table, $column, $c113Id, $num)
    {
        $sql = "INSERT INTO t_112 (c_80, c_81, c_96, c_108_fk, c_113_fk) VALUES (?,?,?,?,?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([true, $path, $num, null, $c113Id]);

        $params = [
            'c_113_fk' => $c113Id,
            'cardId' => $cardId
        ];
        $sql = "UPDATE $table SET c_113_fk=:c_113_fk WHERE $column=:cardId";
        $stmt = $this->pdo->prepare($sql);

        if ($stmt->execute($params)) {
            return true;
        } else {
            return  false;
        }
    }

    function uploadImage($name, $targetDir, $pathM)
    {
        $result = [];
        $basename = strtolower($_FILES[$name]["name"]);
        $target_file = $targetDir . basename($basename);

        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES[$name]["tmp_name"]);
        if ($check !== false) {
            $result['Response'][] = "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            $result['Response'][] = "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $result['Response'][] = "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES[$name]["size"] > 10000000) {
            $result['Response'][] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            $result['Response'][] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $result['State'] = 'F';
            $result['Response'][] = "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES[$name]["tmp_name"], $target_file)) {
                $result['State'] = 'S';
                $result['FilePath'] = $target_file;
                $result['FileName'] = basename($_FILES[$name]["name"]);
                $result['Response'][] = "The file " . basename($_FILES[$name]["name"]) . " has been uploaded.";
            } else {
                $result['State'] = 'F';
                $result['Response'][] = "Sorry, there was an error uploading your file.";
            }

            if ($this->isMobile) {
                require_once('Modules/Objects/Image.php');;

                $mobileImageFile = "$pathM/" . pathinfo($target_file)['basename'];

                $mobileImage = new Image($target_file);
                $mobileImage->scaleImage(900, 900);
                $mobileImage->saveToServer($mobileImageFile);
            }
        }

        return $result;
    }
}
