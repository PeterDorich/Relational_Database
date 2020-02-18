<!--Title: bike_sign_up.php -->
<!--Author: Peter Dorich -->
<!-- Purpose: This page allows a user to create an account -->

<?php
//Starts a session for when a user creates an account
  session_start();
?>
<hmtl>
<head>
<!-- The following style block is for the CSS navigation bar for the site, to make it look a little better-->
<style>
    body{
        margin: 0;
        front-family: Arial, Helvetica, sans-serif;

    }

    .topnav {
            overflow: hidden;
            background-color: #333;
    }   
    .topnav a {
            float: left;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
    }

    .topnav a:hover {
            background-color: #94CBDF;
            color: black;
    }

    </style>
</head>
    <div class="topnav">
<!-- below are the links to each of the active web pages for this website, visible on the top navigation bar -->
        <a class="active" href="http://web.engr.oregonstate.edu/~dorichp/bike_index.html">Main Menu</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/bike_sign_up.php">Sign-up</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/bike_login">Log-in</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/bike_rent.php">Rent</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/account_settings.php">Account Settings</a>

    </div>
<body>
<form action="bike_sign_up.php" style="border:1px solid #ccc" method="post">
  <div class="container">

<!-- The following are the data entry forms for each of the account criteria -->
 
    <h2><b>Welcome, you can create your account here!</b></h2>
    

    <label for="username">Username:</label>
    <input type="text" minlength="1" name="username" required>

    <br><br>

     <label for="firstname">First name:</label>
    <input type="text" name="firstname" required>

    <br><br>

     <label for="lastname">Last name:</label>
    <input type="text" name="lastname" required>

    <br><br>

    <label for="psw">Password (minimum length 6, maximum length 40):</label>
    <input type="password" name="psw" minlength ="6" maxlength ="40" required>

    <br><br>

    <label for="age">Age in years (optional):</label>
    <input type="text" name="age" >

    <div class="clearfix">

    </div>
    </div>
  <input type = "submit"/>
 <br><br>
</form>

<?php

//Database connection
$host = 'classmysql.engr.oregonstate.edu';
$db = 'cs340_dorichp';
$user = 'cs340_dorichp';
$charset = 'utf8mb4';
$pass = '1378';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    //connection made
    $new = $_POST["username"];
    $pdo = new PDO($dsn, $user, $pass, $opt); 
    $sql = "SELECT COUNT(*) FROM Users2 WHERE username = '$new'";
    foreach($pdo->query($sql) as $val) {
       foreach($val as $val2){
          if($val2 > 0){
              echo "ERROR, USERNAME IN USE. Please try again with a a different username";
          }
          else{
              
            //If the username isnt in the database, use an INSERT statement to enter the data
              $username = $_POST['username'];
              $firstname = $_POST['firstname'];
              $lastname = $_POST['lastname'];
            //Must hash the password before storing into the database
              $hashed_password = password_hash($_POST['psw'], PASSWORD_DEFAULT);
              $age = $_POST['age'];


             if(strlen($username) > 0){
              $sql_st = "SELECT COUNT(*) FROM Users2";
              foreach($pdo->query($sql_st) as $val) {
                foreach($val as $val2){
                  $c_id = $val2 + '1';
                }
              }
              //default rental number is 0 for a new account
              $rental_num = '0';

              //SQL statement to insert a new account tuple into the database
              $sql = "INSERT INTO Users2 (c_id, username, firstName, lastName, password_hash, age, rental_num) VALUES ('".$c_id."','".$username."','".$firstname."','".$lastname."','".$hashed_password."','".$age."','".$rental_num."')";


              $pdo->query($sql);
              echo "Successfully added to the database";

              //The following assigns a session "username" and provides a link for them to view their account settings.
                $_SESSION["username"] = $username;
                $_SESSION["role"]   = 'customer';
                echo '<br /><a href="account_settings.php">Click Here to view your account settings</a>';
            }
          }
       }
    }

} catch (\PDOException $e) {
    $error_message = $e->getMessage();
    echo "<tr><td>", $error_message, "</td></tr>\n";
}

?>

  </body>
</hmtl>






