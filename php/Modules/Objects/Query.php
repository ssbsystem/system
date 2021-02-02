<?php

/** Includes **/
/* paths - first include in all php file */
require_once('Enums/Path.php');

/* more */
require_once(Path::GeneralClass_php);
require_once(Path::Message_php);
require_once(Path::Connect_php);
require_once(Path::Select_php);
require_once(Path::SQLStatement_php);

/**
 * Query
 */
class Query implements GeneralClass
{
    //Status list
    private $response = [];

    private string $statment;
    private string $table;
    private string $fields;
    private string $where;
    private string $order;
    private array $params;
    private bool $isSingle;

    private string $sql;
    private PDO $pdo;
    private string $status = Status::Success;
    private array $data = [];

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
        if (isset($this->sql)) {
            $stmt = $this->pdo->prepare($this->sql);
            $stmt->execute($this->params);

            if ($this->statment == SQLStatement::Select) {
                $this->data = $stmt->fetchAll();
            }
        }
    }

    /**
     * Set SQL in parts
     */
    function setInParts(
        string $statement,
        string $table,
        string $fields,
        array $params,
        string $where,
        string $order,
        bool $isSingle = false
    ) {
        $this->statment = $statement;
        $this->table = $table;
        $this->fields = $fields;
        $this->params = $params;
        $this->where = $where;
        $this->order = $order;
        $this->isSingle = $isSingle;

        $this->sql = $this->getStatmentSQL($this->statment);
    }

    /**
     * Get SQL of statment
     */
    function getStatmentSQL($statement)
    {
        switch ($statement) {
            case SQLStatement::Select:
                $statmentObject = new Select(
                    $this->table,
                    $this->fields,
                    $this->where,
                    $this->order
                );
                return $statmentObject->getSql();
            default:
                $this->response = Message::Create(
                    'getStatmentSQL',
                    Status::Error,
                    'Unkown statment!'
                );
                return;
        }
    }

    /**
     * Get the value of response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the value of data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of sql
     */
    public function setSql($sql, $statement, $params, $isSingle = false)
    {
        $this->sql = $sql;
        $this->statment = $statement;
        $this->params = $params;
        $this->isSingle = $isSingle;

        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }
}
