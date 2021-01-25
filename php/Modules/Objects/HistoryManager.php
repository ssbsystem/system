<?php

/**
 * History Manager
 */
class HistoryManager
{
    private $queries;
    private $values;
    private $type;

    function __construct($queries, $values)
    {
        //includes
        require_once('Enums/HistoryManager.php');

        $this->queries = explode(';', $queries);
        $this->values = $values;

        $this->create();
    }

    function create()
    {
    }
}
