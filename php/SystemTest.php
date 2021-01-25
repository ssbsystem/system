<?php

/**
 * System test
 */

 //includes
 require_once('Tests/SystemTest.php');

 $systemTest = new SystemTest();
 $systemTest->run();
 echo '<pre>';
 print_r(json_encode($systemTest->getResponse(), JSON_PRETTY_PRINT));
 echo '</pre>';
