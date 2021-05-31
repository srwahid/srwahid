<?php
    error_reporting(E_ALL ^ E_NOTICE);
    require_once('dbConnect.php');
    unset($_SESSION['selectedSemester']);
    unset($_SESSION['selectedYear']);
    unset($_SESSION['selectedCourse']);
    unset($_SESSION['selectedOfferingId']);
    require 'master.php';
    $myConnection = $newConnection->connection;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title> Search Courses </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-sacle=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="index.css">
    <script src="https://ajax.googleleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

    <div class="container text-center">
        <h1>Search for Courses</h1>
        <h3>Please select the semester and year that you would like to register for<h3>
    </div>
    <div style='margin-bottom:60px' class="container">
        <form class="padding-top" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="form-row">
                <div class="form-group col-md-6" id="no-padding-left">
                    <label for="inputSemester">Semester</label>
                    <select id="inputSemester" class="form-control" name="semester" required>
                        <option>Choose...</option>
                        <?php
                            $semestersArray = array();
                            $semestersArray = getSemestersAvailable($myConnection);
                            foreach($semestersArray as $data) {
                                echo "<option>".$data['semester']."</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-6" id="no-padding-left">
                    <label for="inputYear">Year</label>
                    <select id="inputYear" class="form-control" name="year" required>
                        <option>Choose...</option>
                        <?php
                            $yearsArray = array();
                            $yearsArray = getYearsAvailable($myConnection);
                            foreach($yearsArray as $data) {
                                echo "<option>".$data['year']."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" name="search_courses">Submit</button>
            <?php 
                if (isset($_POST['search_courses'])) {
                    $_SESSION['selectedSemester'] = test_input($_POST["semester"]);
                    $_SESSION['selectedYear'] = test_input($_POST["year"]);

                    header('location: addCourse.php');
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
    
    function getSemestersAvailable($connection) {
        $items = array();

        $getSemestersQuery =  "SELECT DISTINCT semester FROM offering";
        $results = mysqli_query($connection, $getSemestersQuery); 
        while($row = mysqli_fetch_assoc($results)) {
            $items[] = $row;
        }
        print_r($items);
        return $items;
    }

    function getYearsAvailable($connection) {
        $items = array();

        $getSemestersQuery =  "SELECT DISTINCT year FROM offering";
        $results = mysqli_query($connection, $getSemestersQuery); 
        while($row = mysqli_fetch_assoc($results)) {
            $items[] = $row;
        }
        print_r($items);
        return $items;
    }
?>