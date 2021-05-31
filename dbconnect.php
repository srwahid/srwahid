<?php

class Connect { 
    public $connection;
 
    function __construct() 
    { 
       $this->connection = new mysqli('localhost', 'employeeweb', 'pwd0001', 'studentenrollment') or die("Unable to connect");
    }      

    function executeQuery($con,$sql) {
        $result = mysqli_query($con, $sql);
    }
} 

$newConnection = new Connect();
?>