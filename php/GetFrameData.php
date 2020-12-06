<?php
session_start();
if (!isset($_SESSION['UserId'])) {
    die('No session!');
}

$userId = $_SESSION['UserId'];

require_once('Modules/Connect.php');
$PDOConnect = new PDOConnect();
$pdo = $PDOConnect->pdo;

$userModules = $pdo->query(
    "SELECT 
        c_110_id, 
        t_116.c_200_fk, 
        c_6_fk AS TabId, 
        c_25 AS TabName, 
        c_53 AS TabIcon, 
        c_3_fk, 
        c_24 AS ModuleName, 
        c_49 AS ModuleDescription,
        c_72 AS 'Number'
     FROM t_110 
     INNER JOIN t_6 
     ON c_6_id = c_6_fk
     INNER JOIN t_3 
     ON c_3_id = c_3_fk
     INNER JOIN t_114 
     ON t_114.c_110_fk = c_110_id
     INNER JOIN t_115 
     ON t_115.c_115_id = t_114.c_115_fk
     INNER JOIN t_116 
     ON t_116.c_115_fk = c_115_id
     WHERE t_116.c_200_fk=$userId
     ORDER BY t_110.c_72"
)->fetchAll(PDO::FETCH_ASSOC);

foreach ($userModules as $key => $entry) {
    $nUserModules[$entry['Number']]['TabId'] = $entry['TabId'];
    $nUserModules[$entry['Number']]['TabName'] = $entry['TabName'];
    $nUserModules[$entry['Number']]['TabIcon'] = $entry['TabIcon'];
    $nUserModules[$entry['Number']]['Modules'][$entry['c_3_fk']]['FUserModuleId'] = $entry['c_110_id'];
    $nUserModules[$entry['Number']]['Modules'][$entry['c_3_fk']]['Name'] = $entry['ModuleName'];
}

print_r(json_encode($nUserModules));
