<?php

    error_reporting(E_ALL ^ E_NOTICE);
    require_once('dbConnect.php');
    $myConnection = $newConnection->connection;
    require 'master.php';

    session_start();
    deleteNotifications($myConnection,$_SESSION['studentId']);
    unset($_SESSION['username']);  
    header("Location: home.php");

    function deleteNotifications($connection,$studentId) {
        $deleteFromNotificationsQuery =  "DELETE FROM notification 
            WHERE student_id = $studentId";
        $results = mysqli_query($connection, $deleteFromNotificationsQuery);
    };

?>