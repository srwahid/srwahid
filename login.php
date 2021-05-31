<?php

    error_reporting(E_ALL ^ E_NOTICE);
    require_once('dbConnect.php');
    unset($_SESSION['username']);
    require 'master.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title> Login Page </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-sacle=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="index.css">
    <script src="https://ajax.googleleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

    <div class="container text-center">
        <h1>Welcome to the Login Page</h1>
    </div>
    <div style='margin-bottom:60px' class="container">
        <form class="padding-top" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="form-row">
                <div class="form-group col-md-12" id="no-padding-left">
                    <label for="inputEmail">Email</label>
                    <input type="email" class="form-control" id="inputEmail" placeholder="Email" autocomplete="off" name="email" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12" id="no-padding-left">
                    <label for="inputPassword">Password</label>
                    <input type="password" class="form-control" id="inputPassword" placeholder="Password" autocomplete="off" name="password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" name="login_user">Submit</button>
            <?php 

                if (isset($_POST['login_user'])) {
                    $email = test_input($_POST["email"]);
                    $password = test_input($_POST["password"]);
                }
                  
                function test_input($data) {
                    $data = trim($data);
                    $data = stripslashes($data);
                    $data = htmlspecialchars($data);
                    return $data;
                }

                $loginQuery = "SELECT * FROM student WHERE email= '$email' AND password='$password'"; 
                if(isset($_POST['login_user'])) {
                    $results = mysqli_query($newConnection->connection, $loginQuery); 
                    if (mysqli_num_rows($results) == 1) { 
                        while($row = mysqli_fetch_assoc($results)) {
                            $_SESSION['studentId'] = $row['student_id'];
                            $_SESSION['username'] = $row['firstName'];
                            $_SESSION['email'] = $row['email'];
                            $_SESSION['password'] = $row['password'];
                            $_SESSION['firstName'] = $row['firstName'];
                            $_SESSION['lastName'] = $row['lastName'];
                            $_SESSION['address'] = $row['address'];
                            $_SESSION['phone'] = $row['phone'];
                            $_SESSION['degree'] = $row['degree'];
                        }
                         
                        header('location: home.php'); 
                    } else {
                        echo "<p>Please enter a valid username and password.</p>";
                    } 
                }
                
            ?>
        </form>
    </div>

<?php require_once 'footer.php';?>

</body>
</html>