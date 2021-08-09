<?php
/*
* Example MySQL for PDO PHP
* PHP MySQL API
* PHP 5 >= 5.1.0, PHP 7, PHP 8
* 
* @package    MysqlPDO
* @author     Mohamad Zaki Mustafa <mzm@ns.gov.my>
*/

// Include config file
require_once "config.php";

// Check if the user is already logged in, if no then redirect him to login page
if (!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"]) {
    header("location: login.php");
    exit;
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (
        isset($_POST["value"]) && !empty($_POST["value"]) //semak jika value wujud
        && filter_var($_POST["value"], FILTER_VALIDATE_INT) //semak jika value adalah integer 
        && $_POST["value"] != $_SESSION["id"] //semak jika value bukan id session user
    ) {

        // 1. Query SQL statement
        $sql = "DELETE FROM users WHERE id = :fid";

        // 2. Prepare a select statement
        $stmt = $mysqlpdo->prepare($sql);

        // 3. Set parameters        
        $param_id = $_POST["value"];

        // 4. Bind variables to the prepared statement as parameters
        $stmt->bindParam(":fid", $param_id, PDO::PARAM_INT);

        // 5. Attempt to execute the prepared statement
        if ($stmt->execute()) {

            header("location: lists.php");
            exit;
        } else {
            //for development purpose
            echo $stmt->error;
            die;
        }

        // Close statement
        unset($stmt);

        // Close connection
        unset($mysqlpdo);
    }

    header("location: lists.php");
    exit;
}

header("location: welcome.php");
exit;
