<?php
    error_reporting(E_ALL ^ E_NOTICE);
    require_once('Connect.php');
    unset($_SESSION['selectedOfferingId']);
    unset($_SESSION['registered']);
    unset($_SESSION['waitlisted']);
    unset($_SESSION['numStudentsEnrolled']);
    unset($_SESSION['maxStudents']);
    require 'master.php';
    $myConnection = $newConnection->connection;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title> Add Course </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-sacle=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="index.css">
    <script src="https://ajax.googleleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

    <div class="container text-center">
        <?php

            if($_SESSION['selectedYear'] == 2020 && $_SESSION['selectedSemester'] == 'Spring') {
                echo "<h1>Sorry, registration for ".$_SESSION['selectedSemester']." ".$_SESSION['selectedYear']." is closed.</h1>";
            } else {
                echo "<h1>Register for ".$_SESSION['selectedSemester']." ".$_SESSION['selectedYear']."</h1>";
                echo "<h3>Please select the course that you would like to register for</h3>";
            }
            
        ?>
    </div>
    <div style='margin-bottom:60px' class="container">
        <form class="padding-top" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="form-row">
                <div class="form-group col-md-12" id="no-padding-left">
                    <label for="inputCourse">Course</label>
                    <select id="inputCourse" class="form-control" name="course" required>
                        <option>Choose...</option>
                        <?php

                            $availableCoursesArray = array();
                            $availableCoursesArray = getAvailableCourses($myConnection,$_SESSION['selectedYear'],$_SESSION['selectedSemester']);
                            foreach($availableCoursesArray as $data) {
                                echo "<option>".$data['courseName']."</option>";
                            }

                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" name="select_course">Submit</button>
            <?php 
                if (isset($_POST['select_course'])) {
                    $_SESSION['selectedCourse'] = test_input($_POST["course"]);
                    getOfferingId($myConnection,$_SESSION['selectedCourse'],$_SESSION['selectedYear'],$_SESSION['selectedSemester']);
                    checkIfRegistered($myConnection,$_SESSION['studentId'],$_SESSION['selectedOfferingId']);
                    if ($_SESSION['registered'] == 1) {
                        echo "<p style='padding-top:15px'>You are already registered for this course.  Please make another selection.</p>";
                    } else if ($_SESSION['registered'] == 0) {
                        numStudentsEnrolled($myConnection,$_SESSION['selectedOfferingId']);
                        maxStudentsForCourse($myConnection,$_SESSION['selectedOfferingId']);
                        if ($_SESSION['numStudentsEnrolled'] < $_SESSION['maxStudents']) {
                            registerForCourse($myConnection,$_SESSION['studentId'],$_SESSION['selectedOfferingId']);
                            echo "<p style='padding-top:15px'>You have successfully registered for ".$_SESSION['selectedCourse']." for ".$_SESSION['selectedSemester']." ".$_SESSION['selectedYear'].".</p>";
                        } else if ($_SESSION['numStudentsEnrolled'] == $_SESSION['maxStudents']) {
                            checkIfWaitlisted($myConnection,$_SESSION['studentId'],$_SESSION['selectedOfferingId']);
                            if ($_SESSION['waitlisted'] == 1) {
                                echo "<p style='padding-top:15px'>You are already on the waitlist for ".$_SESSION['selectedCourse']." for ".$_SESSION['selectedSemester']." ".$_SESSION['selectedYear'].".  Please make another selection.</p>";
                            } else {
                                addToWaitlist($myConnection,$_SESSION['studentId'],$_SESSION['selectedOfferingId']);
                                echo "<p style='padding-top:15px'>This course is full.  You have been successfully added to the waitlist for ".$_SESSION['selectedCourse']." for ".$_SESSION['selectedSemester']." ".$_SESSION['selectedYear'].".</p>";
                            }                         
                        }
                    }
                }                
            ?>
        </form>
    </div>
<?php require_once 'footer.php';?>
</body>
</html>

<?php
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    function getAvailableCourses($connection,$year,$semester) {
        $items = array();

        $getSemestersQuery =  "SELECT course.courseName
            FROM course
            INNER JOIN offering ON course.course_id = offering.course_id
                AND offering.year = $year
                AND offering.semester = '$semester'";
        $results = mysqli_query($connection, $getSemestersQuery); 
        while($row = mysqli_fetch_assoc($results)) {
            $items[] = $row;
        }
        print_r($items);
        return $items;
    };

    function getOfferingId($connection,$courseName,$year,$semester) {
        $offeringIdQuery = "SELECT offering.offering_id
            FROM offering
            INNER JOIN course ON offering.course_id = course.course_id
                AND offering.year = $year
                AND offering.semester = '$semester'
                AND course.courseName = '$courseName'";
        $results = mysqli_query($connection, $offeringIdQuery);
        if (mysqli_num_rows($results) != 0) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['selectedOfferingId'] = $row['offering_id'];
            }
        }
    }

    function checkIfRegistered($connection,$studentId,$offeringId) {
        $checkIfRegisteredQuery =  "SELECT COUNT(*) as count
        FROM enrollment
        WHERE student_id = $studentId AND offering_id = $offeringId";
        $results = mysqli_query($connection, $checkIfRegisteredQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['registered'] = $row['count'];
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

    function checkIfWaitlisted($connection,$studentId,$offeringId) {
        $checkIfWaitlistedQuery =  "SELECT COUNT(*) as count
        FROM waitlist
        WHERE student_id = $studentId AND offering_id = $offeringId";
        $results = mysqli_query($connection, $checkIfWaitlistedQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['waitlisted'] = $row['count'];
            };
        };
    };

    function addToWaitlist($connection,$studentId,$offeringId) {
        $addToWaitlistQuery =  "INSERT INTO waitlist (student_id, offering_id, dateTimeAdded)
            VALUES 
                ($studentId,$offeringId,NOW())";
        $results = mysqli_query($connection, $addToWaitlistQuery);
    }
    
    function registerForCourse($connection,$studentId,$offeringId) {
        $registerQuery =  "INSERT INTO enrollment (student_id, offering_id)
            VALUES 
                ($studentId,$offeringId)";
        $results = mysqli_query($connection, $registerQuery);
    }
?>