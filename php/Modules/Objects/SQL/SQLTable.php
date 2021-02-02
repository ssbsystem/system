<?php

/** Includes **/
/* paths - first include in all php file */
require_once('Enums/Path.php');

/* more */
require_once(Path::GeneralClass_php);
require_once(Path::Message_php);
require_once(Path::Status_php);
require_once(Path::Connect_php);

/**
 * SQL Table
 */
class SQLTable implements GeneralClass
{
    //Status list
    private $response = [
        "ObjectName" => "SQLTable class"
    ];

    private string $tableName;

    private string $tableKey;
    private PDO $pdo;

    function __construct($tableName)
    {
        $PDOConnect = new PDOConnect();
        $this->pdo = $PDOConnect->pdo;

        $this->tableName = $tableName;
    }

    /**
     * Run
     */
    function run()
    {
        //empty

        $this->response[] = Message::Create('run', Status::Information, 'Szia');
    }

    /**
     * Get the value of response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get table key
     */
    function getTableKey()
    {
        if (isset($this->tableKey)) {
            return $this->tableKey;
        }

        $table = $this->tableName;
        $queary = $this->pdo->prepare("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
        $queary->execute();
        $tableIdArr = $queary->fetchAll();

        $this->tableKey = $tableIdArr[0]['Column_name'];

        return $this->tableKey;
    }
}

/**
 * Examples
 */
/* 
$params = [
    'John','Doe', 22,
    'Jane','Roe', 19,
];*/