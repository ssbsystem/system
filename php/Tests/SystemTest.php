<?php

/** Includes */
require_once('Enums/Status.php');
require_once('Tests/Objects/HistoryManagerT.php');

/**
 * System Test
 */
class SystemTest
{
    public $status;
    public $response;

    public $historyManagerT;

    function __construct()
    {
        //definitions
        $this->response = [];
        $this->status = Status::Success;

        $this->historyManagerT = new HistoryManagerT();
    }

    /**
     * Run
     */
    function run()
    {
        $this->runOneTest($this->historyManagerT, 'HistoryManagerT');

        $this->response['Status'] = $this->status;
    }

    /**
     * Run one test
     */
    function runOneTest(Test $object, string $name)
    {
        $object->run();
        $this->response[$name] = $object->getResponse();
        $this->status = Status::getSumStatus($this->status, $object->getStatus());
    }

    /**
     * Get the value of response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
