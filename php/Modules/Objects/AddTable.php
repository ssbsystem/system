<?php

/**
 * AddPlugin
 */
class AddTable
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

        $tName = $data['Name'];
        $connTableIds = $data['ConnTableIds'];

        $cTSql = "SELECT 
                    c_5_id,
                    c_31 AS TName,
                    c_51 AS TableName,
                    c_52 AS TableIdName
                  FROM t_5 
                  WHERE c_5_id 
                  IN (" . implode(',', $connTableIds) . ")";
        $cTQueary = $this->pdo->prepare($cTSql);
        $cTQueary->execute();
        $cTData = $cTQueary->fetchAll(PDO::FETCH_ASSOC);

        //Upload to list
        $finalSQL = "INSERT INTO `t_5`(`c_31`) VALUES ('$tName');";
        $finalQueary = $this->pdo->prepare($finalSQL);
        $finalQueary->execute();
        $lastId = $this->pdo->lastInsertId();

        $tableName = "t_$lastId";
        $tableIdName = "c_$lastId" . "_id";

        $ctResult = "";
        $aKResult = "";
        $aCResult = "";
        $first = true;
        foreach ($cTData as $cTRow) {
            //$ctResult .= ", c_" . $cTRow['c_5_id'] . "_fk INT(11) DEFAULT NULL, INDEX fk_" . $cTRow['c_5_id'] . " (c_" . $cTRow['c_5_id'] . "_fk), FOREIGN KEY (c_" . $cTRow['c_5_id'] . "_fk) REFERENCES " . $cTRow['TableName'] . "(" . $cTRow['TableIdName'] . ") ON DELETE SET NULL";
            $fkTable = "t_" . $cTRow['c_5_id'];
            $fkName = "c_" . $cTRow['c_5_id'] . "_fk";
            $fkIdName = "c_" . $cTRow['c_5_id'] . "_id";

            $ctResult .= ", $fkName INT(11) DEFAULT NULL";
            $aKResult .= ", ADD KEY `$fkName` (`$fkName`)";

            $constraintName = $tableName . "_ibfk_1";
            if ($first) {
                $aCResult .= "ALTER TABLE `$tableName` ";
                $$first = false;
            } else {
                $aCResult .= ", ";
            }

            $aCResult .= "ADD CONSTRAINT `$constraintName` FOREIGN KEY (`$fkName`) REFERENCES `$fkTable` (`$fkIdName`)";
        }

        //Create table
        $newTableSQL = "CREATE TABLE `$tableName` (
            `$tableIdName` INT(11) NOT NULL" . $ctResult . ");";
        //Constrains
        $newTableSQL .= "ALTER TABLE `$tableName`
                            ADD PRIMARY KEY (`$tableIdName`) 
                            $aKResult;
                            $aCResult;
                        COMMIT;";
        
        $this->pdo->exec($newTableSQL);

        //Update list by id
        $updateSQL = "UPDATE `t_5` SET `c_51`='$tableName',`c_52`='$tableIdName' WHERE `c_5_id`='$lastId';";
        $updateQueary = $this->pdo->prepare($updateSQL);
        $updateQueary->execute();

        return $main_data;
    }
}
