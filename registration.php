<?php
    error_reporting(E_ALL ^ E_NOTICE);
    require_once('dbConnect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title> Registration Page </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-sacle=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="index.css">
    <script src="https://ajax.googleleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

<?php include 'master.php';?>

    <div class="container text-center">
        <h1>Welcome to the Registration Page</h1>
    </div>
    <div style='margin-bottom:60px' class="container">
        <form class="padding-top" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="form-row">
                <div class="form-group col-md-6" id="no-padding-left">
                    <label for="inputEmail">Email</label>
                    <input type="email" class="form-control" id="inputEmail" placeholder="Email" autocomplete="off" name="email" required>
                </div>
                <div class="form-group col-md-6" id="no-padding-right">
                    <label for="inputPassword">Password</label>
                    <input type="password" class="form-control" id="inputPassword" placeholder="Password" autocomplete="off" name="password" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6" id="no-padding-left">
                    <label for="inputFirstName">First Name</label>
                    <input type="firstName" class="form-control" id="inputFirstName" placeholder="First Name" autocomplete="off" name="firstName" required>
                </div>
                <div class="form-group col-md-6" id="no-padding-right">
                    <label for="inputLastName">Last Name</label>
                    <input type="lastName" class="form-control" id="inputLastName" placeholder="Last Name" autocomplete="off" name="lastName" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputAddress1">Address</label>
                <input type="text" class="form-control" id="inputAddress1" placeholder="1234 Main St" name="address1" required>
            </div>
            <div class="form-group">
                <label for="inputAddress2">Address 2</label>
                <input type="text" class="form-control" id="inputAddress2" placeholder="Apartment, studio, or floor" name="address2">
            </div>
            <div class="form-row">
                <div class="form-group col-md-6" id="no-padding-left">
                    <label for="inputCity">City</label>
                    <input type="text" class="form-control" id="inputCity" name="city" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputState">State</label>
                    <select id="inputState" class="form-control" name="state" required>
                        <option>Choose...</option>
                        <?php
                        $states = ["AK", "AL", "AR", "AZ", "CA", "CO", "CT", "DE", "FL", "GA", "HI", "IA", "ID", "IL", "IN", "KS", "KY", "LA", "MA", "MD", "ME", "MI", "MN", "MO", "MS", "MT", "NC", "ND", "NE", "NH", "NJ", "NM", "NV", "NY", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VA", "VT", "WA", "WI", "WV", "WY"];
                        $stateLength = count($states);
                        for ($i=0; $i<$stateLength; $i++) {
                            echo "<option>$states[$i]</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-2" id="no-padding-right">
                    <label for="inputZip">Zip</label>
                    <input type="text" class="form-control" id="inputZip" name="zip" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6" id="no-padding-left">
                    <label for="inputPhone">Phone</label>
                    <input type="tel" class="form-control" id="inputPhone" placeholder="123-456-7890" name="phone" required>
                </div>
                <div class="form-group col-md-6" id="no-padding-right">
                    <label for="inputDegree">Degree</label>
                    <input type="text" class="form-control" id="inputDegree" placeholder="B.S. in Computer Software Technology" name="degree" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" name="register_user">Register</button>
            <?php 
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $email = test_input($_POST["email"]);
                    $password = test_input($_POST["password"]);
                    $firstName = test_input($_POST["firstName"]);
                    $lastName = test_input($_POST["lastName"]);
                    $address1 = test_input($_POST["address1"]);
                    $address2 = test_input($_POST["address2"]);
                    $city = test_input($_POST["city"]);
                    $state = test_input($_POST["state"]);
                    $zip = test_input($_POST["zip"]);
                    $phone = test_input($_POST["phone"]);
                    $degree = test_input($_POST["degree"]);
                    $address = $address1." ".$address2." ".$city." ".$state." ".$zip;
                }
                  
                function test_input($data) {
                    $data = trim($data);
                    $data = stripslashes($data);
                    $data = htmlspecialchars($data);
                    return $data;
                }

                if ($_POST != array()) {
                    $checkUserQuery = "SELECT * FROM student WHERE email= '$email' AND password='$password'";
                    $checkUserExists = mysqli_query($newConnection->connection,$checkUserQuery);
                    if(mysqli_num_rows($checkUserExists) == 0) {
                        $submitDataQuery = "INSERT INTO `student` (email, password, firstName, lastName, address, phone, degree) 
                        VALUES ('$email', '$password', '$firstName', '$lastName', '$address', '$phone', '$degree')";
                        $newConnection->executeQuery($newConnection->connection,$submitDataQuery);
                    } 
                }

                $loginQuery = "SELECT * FROM student WHERE email= '$email' AND password='$password'"; 
                if(isset($_POST['register_user'])) {
                    $results = mysqli_query($newConnection->connection, $loginQuery); 
                    if (mysqli_num_rows($results) == 1) { 
                        while($row = mysqli_fetch_assoc($results)) {
                            $_SESSION['username'] = $row['firstName'];
                            $_SESSION['email'] = $row['email'];
                            $_SESSION['password'] = $row['password'];
                            $_SESSION['firstName'] = $row['firstName'];
                            $_SESSION['lastName'] = $row['lastName'];
                            $_SESSION['address'] = $row['address'];
                            $_SESSION['phone'] = $row['phone'];
                            $_SESSION['degree'] = $row['degree'];
                        }
                         
                        echo "<h2>Thank you for registering.</h2>";
                    } 
                }
                
            ?>
        </form>
    </div>
<?php include 'footer.php';?>
</body>
</html>