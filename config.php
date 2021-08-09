<?php
/*
* Example MySQL for PDO PHP
* PHP MySQL API
* PHP 5 >= 5.1.0, PHP 7, PHP 8
* 
* @package    MysqlPDO
* @author     Mohamad Zaki Mustafa <mzm@ns.gov.my>
*/

//Start Session
if (session_status() == PHP_SESSION_NONE) {
  //session has not started
  session_start();
}


/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'password');
define('DB_NAME', 'db_mysqli');

/* Attempt to connect to MySQL database */
try {
  $mysqlpdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
  // Set the PDO error mode to exception
  $mysqlpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("ERROR: Could not connect. " . $e->getMessage());
}
