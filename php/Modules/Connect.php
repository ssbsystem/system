<?php
class PDOConnect
{
    public $pdo;
    function __construct()
    {
        $host = $_SERVER['HTTP_HOST'];
        if ($host == "localhost") {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "ssbsyste_huszaiphoto";
        } else {
            $subdomain = explode('.', $host)[0];

            $servername = "ssbsystem.com";
            $username = "ssbsyste_server";
            $password = "Sport2018";
            $database = "ssbsyste_$subdomain";
        }

        $_SERVER['Database'] = $database;

        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
            // set the PDO error mode to exception
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo = $pdo;
        } catch (PDOException $e) {
            echo "Connection failed!" . " " . $e->getMessage();
        }
    }
}
