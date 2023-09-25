<?php

header('Content-Type: application/json; charset =utf-8');

define("DBHOST",isset($_ENV["DBHOST"]) ? $_ENV["DBHOST"] : "mysql-db");
define("DBUSER",isset($_ENV["DBUSER"]) ? $_ENV["DBUSER"] : "root");
define("DBPWD",isset($_ENV["DBPWD"]) ? $_ENV["DBPWD"] : "1TimePass!");
define("DBNAME",isset($_ENV["DBNAME"]) ? $_ENV["DBNAME"] : "awt");

require_once './classes/class.handler.php';

$request = new Request();
$request->process($_SERVER)

?>


