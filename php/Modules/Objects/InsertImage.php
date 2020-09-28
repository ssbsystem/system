<?php

/**
 * Insert Image
 */
class InsertImage
{
    function __construct()
    {
        require_once('Modules/Connect.php');
        $PDOConnect = new PDOConnect();
        $this->pdo = $PDOConnect->pdo;
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
                    c_108_fk
                FROM t_112 LEFT JOIN $table ON $table.c_112_FK=t_112.c_112_id
                WHERE $table.$column = :id";

        $statement = $this->pdo->prepare($query);
        $statement->execute(
            array(
                ':id' => $cardId
            )
        );

        $galleryArray = $statement->fetchAll();

        $entryDir = "$cardColumn.$cardId";
        $entryDir = str_replace(".", '-', $entryDir);
        $path = "uploads/img/$entryDir/";

        if (sizeof($galleryArray) === 0) {
            mkdir("uploads/img/$entryDir");

            $sql = "INSERT INTO t_112 (c_80, c_81, c_108_fk) VALUES (?,?,?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([true, $path, null]);
            $lastId = $this->pdo->lastInsertId();

            $params = [
                'c_112_fk' => $lastId,
                'cardId' => $cardId
            ];
            $sql = "UPDATE $table SET c_112_fk=:c_112_fk WHERE $column=:cardId";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        }

        $filesData = $data['FileToUpload'];

        for ($i = 0; $i < sizeof($filesData); $i++) {
            $itemName = $filesData[$i];
            $main_data[$i] = $this->uploadImage($itemName, $path);
        }

        return $main_data;
    }

    function uploadImage($name, $target_dir)
    {
        $result = [];
        $basename = strtolower($_FILES[$name]["name"]);
        $target_file = $target_dir . basename($basename);

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
                $result['Response'][] = "The file " . basename($_FILES[$name]["name"]) . " has been uploaded.";
            } else {
                $result['State'] = 'F';
                $result['Response'][] = "Sorry, there was an error uploading your file.";
            }
        }

        return $result;
    }
}
