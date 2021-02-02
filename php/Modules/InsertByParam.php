<?php

/** Includes **/
/* paths - first include in all php file */
require_once('Enums/Path.php');

/* more */
require_once(Path::Message_php);
require_once(Path::Status_php);
require_once(Path::Insert_php);
/**
 * Insert by structure
 */
class InsertByParam
{
    //Status list
    private $response = [
        "Object" => "InsertByParam class"
    ];

    public function DefaultUpload($data)
    {
        $main_data = array();
        foreach ($data as $object) {
            $main_data[] =  $this->UploadOneObject($object);
        }
        return $main_data;
    }

    function UploadOneObject($object)
    {
        require_once('Connect.php');
        $PDOConnect = new PDOConnect();
        $pdo = $PDOConnect->pdo;

        $main_data = array();

        $finalSQL = '';

        foreach ($object as $table => $columns) {
            $finalSQL = 'INSERT INTO ' . $table;
            $columnNames = '';
            $values = '';
            $params = [];

            $isFirst = true;
            foreach ($columns as $column => $value) {
                if ($isFirst) {
                    $isFirst = false;
                } else {
                    $columnNames .= ', ';
                    $values .= ', ';
                }
                $columnNames .= $column;
                /*
                if ($value === 'null') {
                    $values .= 'null';
                } else {
                    $values .= '"' . $value . '"';
                }*/

                $params[":$column"] = $value;
            }

            $insert = new Insert();
            $insert->setData($table, $columnNames, $params);
            $insert->run();
            print_r($insert->getResponse());
            $lastId = $insert->getInsertedId();
            $tableIdColumn = $insert->getKeyField();

            if ($finalQueary) {
                $main_data[$table]['Result'] = 'S';
                $main_data[$table]['LastIdColumn'] = $tableIdColumn;
                $main_data[$table]['LastId'] = $lastId;
            } else {
                $main_data[$table]['result'] = 'F';
            }
        }

        return $main_data;
    }

    /**
     * Get the value of response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
/*** Example ***/
/*
$data = [
    array(
        "table1" => array(
            "column1" => "data1",
            "column2" => "data2",
            "column3" => "data3",
        ),
        "table2" => array(
            "column1" => "data4",
            "column2" => "data5",
            "column3" => "data6",
        )
    ),
    array(
        "table1" => array(
            "column1" => "data1",
            "column2" => "data2",
            "column3" => "data3",
        ),
        "table2" => array(
            "column1" => "data4",
            "column2" => "data5",
            "column3" => "data6",
        )
    )
]
*/
