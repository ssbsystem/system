<?php

/** includes **/
require_once('Interfaces/Test.php');

/**
 * History Manager test
 */
class HistoryManagerT implements Test
{
    public $status;
    public $response;

    function __construct()
    {
        //includes
        require_once('Enums/Status.php');

        //definitions
        $this->response = [];
    }

    /**
     * Run
     */
    function run()
    {
        $this->status =Status::getSumStatus(Status::Success, Status::Warning);
        $this->response['Name'] = 'History manager test';
        $this->response['Status'] = $this->status;

        $test1 = [];
        $test1['Status'] = Status::Success;
        $test1['Message'] = '';
        $this->response['ResponseList'][] = $test1;

        $test2 = [];
        $test2['Status'] = Status::Warning;
        $test2['Message'] = 'Valami nem jó!';
        $this->response['ResponseList'][] = $test2;

        $test3 = [];
        $test3['Status'] = Status::Error;
        $test3['Message'] = 'Hiba!';
        $this->response['ResponseList'][] = $test3;
    }

    /**
     * Get the value of response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }
}
