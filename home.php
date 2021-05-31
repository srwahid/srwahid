<?php
    error_reporting(E_ALL ^ E_NOTICE);
    require_once('dbConnect.php');
    $myConnection = $newConnection->connection;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title> Home Page </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-sacle=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

<?php require 'master.php';?>

    <div style='margin-bottom:60px' class="container text-center">
        <?php 
            if(isset($_SESSION['username'])) {
                checkNotifications($myConnection,$_SESSION['studentId']);
                echo "<h1>Welcome to the Home Page, ".$_SESSION['username']."</h1>";
                if ($_SESSION['numNotifications'] != 0) {
                    $notificationsArray = array();
                    $notificationsArray = getNotifications($myConnection,$_SESSION['studentId']);
                    foreach($notificationsArray as $data) {
                        echo "<h3 style='padding-top:15px'>Notification: You have been registered for ".$data['courseName']." for ".$data['semester']." ".$data['year']."</h3>";
                    }
                }
            }
            else {
                echo "<h1>Welcome to the Home Page</h1>";
                echo "<h3>Please login or register</h3>";
            }
        ?>
    </div>

<?php require_once 'footer.php';?>
</body>
</html>

<?php

    function checkNotifications($connection,$studentId) {
        $numNotificationsQuery =  "SELECT COUNT(*) as notifications
            FROM notification
            WHERE student_id = $studentId";
        $results = mysqli_query($connection, $numNotificationsQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['numNotifications'] = $row['notifications'];
            };
        };
    }

    function getNotifications($connection,$studentId) {
        $items = array();

        $getNotificationsQuery =  "SELECT course.courseName, offering.year, offering.semester
            FROM ((notification
            INNER JOIN offering ON notification.offering_id = offering.offering_id
                AND notification.student_id = $studentId)
            INNER JOIN course ON course.course_id = offering.course_id)";
        $results = mysqli_query($connection, $getNotificationsQuery); 
        while($row = mysqli_fetch_assoc($results)) {
            $items[] = $row;
        }
        return $items;
    }

?>