<?php
error_reporting(E_ALL ^ E_NOTICE);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<?php
    ini_set('session.use_only_cookies','1');
    session_start();
    
    if( isset($_SESSION['username'])) {
        echo "Welcome ".$_SESSION['username'];
    }
    else {
        echo "Welcome Guest - If you are a current student, please login or create an account.";
    }        
?>
    
<body>

<body style="background-color:lightgray">
</body>

<div class="jumbotron">
    <div class="container text-center">
        <h2>Student Online Enrollment Portal</h2>
    </div>
</div>
    
<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-togggle="collapse" data-target="#myNavbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
        </div>
        
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">
                <li class="active"><a href="home.php"><span class="glyphicon glyphicon-home"></span> Home</a> </li>
                <li class="#"><a href="contactus.php"><span class="glyphicon glyphicon-earphone"></span> ContactUs</a> </li>
            </ul>
        
            <ul class="nav navbar-nav navbar-right">
        <?php
            if( isset($_SESSION['username']))
                {
            
                echo '<li><a href="profile.php"><span class="glyphicon glyphicon-briefcase"></span> Profile</a></li>';
                echo '<li><a href="viewSchedule.php"><span class="glyphicon glyphicon-th-list"></span> Schedule</a></li>';
                echo '<li><a href="searchCourses.php"><span class="glyphicon glyphicon-plus"></span> Add Course</a></li>';
                echo '<li><a href="logout.php"><span class="glyphicon glyphicon-user"></span> Logout</a></li>';
                }
                 else
                 {
                     echo '<li><a href="login.php"><span class="glyphicon glyphicon-user"></span> Login</a></li>';
                     echo '<li><a href="registration.php"><span class="glyphicon glyphicon-pencil"></span> Registration</a></li>';
                 }
                    
        ?>
            
            </ul>
        </div>
    </div>
</nav>
     
    
</body>