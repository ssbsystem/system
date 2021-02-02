<?php

/**
 * System test
 */

 //includes
 require_once('Tests/SystemTest.php');
 require_once('Modules/Objects/VersionManager.php');
/*
 $test = new SystemTest();*/

 $test = new VersionManager();

 $test->run();
 echo '<pre>';
 print_r(json_encode($test->getResponse(), JSON_PRETTY_PRINT));
 echo '<br>';
 print_r(json_encode($test->getVersionData('1.0.2'), JSON_PRETTY_PRINT));
 echo '</pre>';
