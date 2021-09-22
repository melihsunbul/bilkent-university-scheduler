<?php
// data source name
$dsn = "mysql:host=localhost;port=8889;dbname=schedule;charset=utf8mb4";
$dbuser = "root" ;
$dbpass = 'root' ;

try {
  $db = new PDO($dsn, $dbuser, $dbpass) ;
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) ;
} catch( PDOException $ex) {
    echo "<p>Connection Error:</p>" ;
    echo "<p>", $ex->getMessage(), "</p>" ;
    exit ;
}