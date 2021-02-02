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
 * Select
 */
class Select implements GeneralClass
{
    //Status list
    private $response = [
        "Object" => "Select class"
    ];

    private string $table;
    private string $fields;
    private array $params;
    private string $where;
    private string $order;
    private bool $single;

    private PDO $pdo;
    private string $sql;
    private array $result;

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
        $stmt = $this->pdo->prepare($this->sql);
        print_r($this->sql);
        print_r($this->params);
        $stmt->execute($this->params);

        if ($this->single) {
            $this->result = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $this->result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Set class main data
     */
    function setData(
        string $table,
        string $fields,
        array $params = [],
        string $where = '',
        string $order = '',
        bool $single = false
    ) {
        $this->table = $table;
        $this->fields = $fields;
        $this->params = $params;
        $this->where = $where;
        $this->order = $order;
        $this->single = $single;

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

        if (!isset($where) || $where == '') {
            $this->response['run'][] = [
                "Status" => Status::Information,
                "Message" => 'Where condition is not defined!'
            ];
        } else {
            $where = "WHERE $where";
        }

        if (!isset($order) || $order == '') {
            $this->response['run'][] = [
                "Status" => Status::Information,
                "Message" => 'Order is not defined!'
            ];
        } else {
            $order = "ORDER BY $order";
        }

        $this->sql = "SELECT $fields FROM $table $where $order";
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
     * Set the value of where
     *
     * @return  self
     */
    public function setWhere($where)
    {
        $this->where = $where;
        $this->run();

        return $this;
    }

    /**
     * Set the value of order
     *
     * @return  self
     */
    public function setOrder($order)
    {
        $this->order = $order;
        $this->run();

        return $this;
    }

    /**
     * Get the value of result
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set the value of sql
     *
     * @return  self
     */
    public function setSql($sql, $params)
    {
        $this->sql = $sql;
        $this->params = $params;

        return $this;
    }
}
