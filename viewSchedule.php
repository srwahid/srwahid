<?php

    error_reporting(E_ALL ^ E_NOTICE);
    require_once('dbConnect.php');
    unset($_SESSION['dropOfferingId']);
    unset($_SESSION['droppedCourseName']);
    unset($_SESSION['droppedSemester']);
    unset($_SESSION['droppedYear']);
    unset($_SESSION['numStudentsEnrolled']);
    unset($_SESSION['maxStudents']);
    unset($_SESSION['numStudentsOnWaitlist']);
    unset($_SESSION['waitlistedStudentId']);
    unset($_SESSION['dateTimeAdded']);
    $myConnection = $newConnection->connection;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title> View Schedule </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-sacle=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="index.css">
    <script src="https://ajax.googleleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

<?php include 'master.php';?>

    <div style='margin-bottom:60px' class="container text-center">
        <?php 

            if(isset($_SESSION['username'])) {
                echo "<h1>Here is your course schedule, ".$_SESSION['username']."</h1>";
                echo "<br>";
                echo "<h2>You are registered for:</h2>";

                displayCourseSchedule($myConnection,$_SESSION['studentId']);
            }
            else {
                echo "<h1>Course Schedule Page</h1>";
                echo "<h3>Please login or register</h3>";
            };

            if (isset($_POST['dropButton'])) {  
                echo "<meta http-equiv='refresh' content='0'>";
                $_SESSION['dropOfferingId'] = test_input($_POST["drop"]);                
                dropCourse($myConnection,$_SESSION['studentId'],$_SESSION['dropOfferingId']);
                echo "<p style='padding-top:15px'>You have successfully dropped ".$_SESSION['droppedCourseName']." from ".$_SESSION['droppedSemester']." ".$_SESSION['droppedYear']."</p>";
                echo "<p>Please wait while your schedule is updated.</p>";
                numStudentsEnrolled($myConnection,$_SESSION['dropOfferingId']);
                maxStudentsForCourse($myConnection,$_SESSION['dropOfferingId']);
                if ($_SESSION['numStudentsEnrolled'] == $_SESSION['maxStudents'] - 1) {
                    numStudentsOnWaitlist($myConnection,$_SESSION['dropOfferingId']);
                    if ($_SESSION['numStudentsOnWaitlist'] != 0) {
                        getWaitlistedStudent($myConnection,$_SESSION['dropOfferingId']);
                        registerForCourse($myConnection,$_SESSION['waitlistedStudentId'],$_SESSION['dropOfferingId']);
                        removeStudentFromWaitlist($myConnection,$_SESSION['waitlistedStudentId'],$_SESSION['dropOfferingId'],$_SESSION['dateTimeAdded']);
                        notifyStudent($myConnection,$_SESSION['waitlistedStudentId'],$_SESSION['dropOfferingId']);
                    }
                }
            };

        ?>

    </div>

<?php include 'footer.php';?>

</body>
</html>

<?php

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function displayCourseSchedule($connection,$studentId) {
        $getScheduleQuery =  "SELECT enrollment.student_id, offering.offering_id, course.courseName, offering.year, offering.semester
            FROM ((enrollment
                INNER JOIN offering ON enrollment.offering_id = offering.offering_id
                    AND enrollment.student_id = $studentId)
                INNER JOIN course ON course.course_id = offering.course_id)";
        $results = mysqli_query($connection, $getScheduleQuery); 
        if (mysqli_num_rows($results) != 0) { 
            while($row = mysqli_fetch_assoc($results)) {
                $offeringId = $row['offering_id'];
                $courseName = $row['courseName'];
                $courseYear = $row['year'];
                $courseSemester = $row['semester'];

                echo "<div class='row'>";
                    echo "<div class='col-md-6 text-left'>";
                        echo "<h3>".$courseName."</h3>";
                    echo "</div>";
                    echo "<div class='col-md-2 text-left'>";
                        echo "<h3>".$courseSemester."</h3>";
                    echo "</div>";
                    echo "<div class='col-md-2 text-left'>";
                        echo "<h3>".$courseYear."</h3>";
                    echo "</div>";
                    echo "<div style='padding-top:15px' class='col-md-2 text-left'>";
                        echo "<form method='post'>";
                            echo "<input type='hidden' name='drop' value=".$offeringId.">";
                            echo "<button style='font-family:sans-serif' type='submit' class='btn btn-danger' name='dropButton'>DROP</button>";
                        echo "</form>";
                    echo "</div>";
                echo "</div>";
            }
        } 
    };

    function dropCourse($connection,$studentId,$offeringId) {
        $dropQuery =  "DELETE FROM enrollment
            WHERE student_id = $studentId AND offering_id = $offeringId";
        $results = mysqli_query($connection, $dropQuery);

        $getCourseInfoQuery =  "SELECT course.courseName, offering.semester, offering.year
            FROM course
            INNER JOIN offering ON course.course_id = offering.course_id
                AND offering.offering_id = $offeringId";
        $results = mysqli_query($connection, $getCourseInfoQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['droppedCourseName'] = $row['courseName'];
                $_SESSION['droppedSemester'] = $row['semester'];
                $_SESSION['droppedYear'] = $row['year'];
            };
        };
    };

    function numStudentsEnrolled($connection,$offeringId) {
        $numStuEnrolledQuery =  "SELECT COUNT(enrollment.offering_id) as 'count'
            FROM enrollment
            WHERE offering_id = $offeringId";
        $results = mysqli_query($connection, $numStuEnrolledQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['numStudentsEnrolled'] = $row['count'];
            };
        };
    };

    function maxStudentsForCourse($connection,$offeringId) {
        $maxStudentsQuery =  "SELECT course.maxStudents
            FROM course
            INNER JOIN offering ON offering.course_id = course.course_id
                AND offering.offering_id = $offeringId";
        $results = mysqli_query($connection, $maxStudentsQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['maxStudents'] = $row['maxStudents'];
            };
        };
    };

    function numStudentsOnWaitlist($connection,$offeringId) {
        $numStuWaitlistQuery =  "SELECT COUNT(*) as students
            FROM waitlist
            WHERE offering_id = $offeringId";
        $results = mysqli_query($connection, $numStuWaitlistQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['numStudentsOnWaitlist'] = $row['students'];
            };
        };
    };

    function getWaitlistedStudent($connection,$offeringId) {
        $waitlistedStudentQuery =  "SELECT student_id, dateTimeAdded
            FROM waitlist
            WHERE offering_id = $offeringId
            ORDER BY dateTimeAdded LIMIT 1";
        $results = mysqli_query($connection, $waitlistedStudentQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['waitlistedStudentId'] = $row['student_id'];
                $_SESSION['dateTimeAdded'] = $row['dateTimeAdded'];
            };
        };
    };

    function registerForCourse($connection,$studentId,$offeringId) {
        $registerQuery =  "INSERT INTO enrollment (student_id, offering_id)
            VALUES 
                ($studentId,$offeringId)";
        $results = mysqli_query($connection, $registerQuery);
    }
    
    function removeStudentFromWaitlist($connection,$studentId,$offeringId,$dateTimeAdded) {
        $removeFromWaitlistQuery =  "DELETE FROM waitlist 
            WHERE student_id = $studentId
                AND offering_id = $offeringId
                AND dateTimeAdded = '$dateTimeAdded'";
        $results = mysqli_query($connection, $removeFromWaitlistQuery);
    };

    function notifyStudent($connection,$studentId,$offeringId) {
        $createNotificationQuery =  "INSERT INTO notification (student_id, offering_id)
            VALUES 
                ($studentId,$offeringId)";
        $results = mysqli_query($connection, $createNotificationQuery);
    };

?>