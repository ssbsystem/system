<?php

/** Includes **/
/* paths - first include in all php file */
require_once('Enums/Path.php');

/* more */
require_once(Path::GeneralClass_php);
require_once(Path::Message_php);
require_once(Path::Status_php);
require_once(Path::Connect_php);
require_once(Path::SQLTable_php);
require_once(Path::Session_php);
require_once(Path::IndexingType_php);
require_once(Path::Select_php);

/**
 * Insert
 */
class Insert implements GeneralClass
{
    //Status list
    private $response = [
        "Object" => "Insert class"
    ];

    private string $table;
    private string $fields;
    private array $params;

    private PDO $pdo;
    private string $sql;
    private string $keyField;
    private string $insertedId;

    function __construct()
    {
        $PDOConnect = new PDOConnect();
        $this->pdo = $PDOConnect->pdo;
    }

    /**
     * Run
     */
    function run()
    {
        $table = $this->table;
        $fields = $this->fields;
        $params = $this->params;

        if (!isset($table)) {
            $this->response['run'][] = [
                "Status" => Status::Error,
                "Message" => 'Table is not defined!'
            ];
            return;
        }

        if (!isset($fields)) {
            $this->response['run'][] = [
                "Status" => Status::Error,
                "Message" => 'Fields are not defined!'
            ];
            return;
        }

        if (!isset($params)) {
            $this->response['run'][] = [
                "Status" => Status::Error,
                "Message" => 'Params are not defined!'
            ];
            return;
        }

        $sqlTable = new SQLTable($this->table);
        $keyField = $sqlTable->getTableKey();
        $this->keyField = $keyField;

        try {
            $this->pdo->beginTransaction();

            if (strpos($this->sql, ":$keyField") === false) {
                $select = new Select();
                $select->setData(
                    't_5',
                    'c_104, c_105, c_106',
                    ['c_5_id' => $table],
                    "c_5_id=:c_5_id",
                    '',
                    true
                );
                $select->run();
                $tableData = $select->getResult();

                $index = 'null';
                switch ($_SESSION[Session::IndexingType]) {
                    case IndexingType::Standard:
                        $index = $tableData['c_104'];
                        break;
                    case IndexingType::Module:
                        $index = $tableData['c_105'];
                        break;
                    case IndexingType::Company:
                        $index = $tableData['c_106'];
                        break;
                }

                $table = $this->table;
                $fields = "$keyField, " + $this->fields;
                $params = $this->params;
                $params[":$keyField"] = $index;
                $this->createSQL($table, $fields, $params);
            }

            $stmt = $this->pdo->prepare($this->sql);
            $stmt->execute($this->params);
            $this->pdo->commit();
            $this->insertedId = $this->pdo->lastInsertId();

            $response[] = Message::Create('run', Status::Success, 'Insertion success!');
        } catch (Exception $e) {
            $this->pdo->rollback();
            $response[] = Message::Create('run', Status::Error, 'Insertion failed!');

            throw $e;
        }
    }

    /**
     * Set data
     */
    function setData(
        string $table,
        string $fields,
        array $params
    ) {
        $this->table = $table;
        $this->fields = $fields;
        $this->params = $params;

        $this->createSQL($table, $fields, $params);
    }

    /**
     * Create SQL
     */
    function createSQL(
        string $table,
        string $fields,
        array $params
    ) {
        if (!isset($table)) {
            $this->response['run'][] = [
                "Status" => Status::Error,
                "Message" => 'Table is not defined!'
            ];
            return;
        }

        if (!isset($fields)) {
            $this->response['run'][] = [
                "Status" => Status::Error,
                "Message" => 'Fields are not defined!'
            ];
            return;
        }

        if (!isset($params)) {
            $this->response['run'][] = [
                "Status" => Status::Error,
                "Message" => 'Params are not defined!'
            ];
            return;
        }

        $paramString = $this->getParamString($params);
        $this->sql = "INSERT INTO $table ($fields) VALUES ($paramString)";
    }

    /**
     * Get parameter string
     */
    function getParamString(array $params)
    {
        $paramString = '';
        $first = true;

        foreach ($params as $key => $param) {
            if ($first) {
                $first = false;
                $paramString .= ":$key";
            } else {
                $paramString .= ", :$key";
            }
        }

        return $paramString;
    }

    /**
     * Get the value of response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the value of sql
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * Set the value of sql
     *
     * @return  self
     */
    public function setSql(string $sql, string $table)
    {
        $this->table = $table;
        $this->sql = $sql;

        return $this;
    }

    /**
     * Set the value of table
     *
     * @return  self
     */
    public function setTable($table)
    {
        $this->table = $table;
        $this->run();

        return $this;
    }

    /**
     * Get the value of table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get the value of fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get the value of insertedId
     */
    public function getInsertedId()
    {
        return $this->insertedId;
    }

    /**
     * Get the value of keyField
     */
    public function getKeyField()
    {
        return $this->keyField;
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