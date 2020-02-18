<!--Title: bike_login.php -->
<!--Author: Peter Dorich -->
<!-- Purpose: This page allows a user to sign into the account they created -->

<!--This php block starts the session for when the user signs in -->
<?php
session_start();
?>
<hmtl>
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
        <a class="active" href="http://web.engr.oregonstate.edu/~dorichp/bike_index.html">Main Menu</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/bike_sign_up.php">Sign-up</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/bike_login">Log-in</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/bike_rent.php">Rent</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/account_settings.php">Account Settings</a>

    </div>
<body>
<!-- This form allows a user to sign in with their username and password -->
<form style="border:1px solid #ccc" method ="post">
  <div class="container">

<!--Form for signing in -->

       <h2><b>Log in to access your account!</b></h2>
  

        <label for="username">Username:</label>
        <input type="text" name="username" required>

        <br><br>


        <label for="psw">Password:</label>
        <input type="password" name="psw" required>

   
         <div class="clearfix">
    
        <br><br>


        </div>
      </div>
    <input type = "submit"/>
</form>


<!-- Php block is responsible for checking the hash stored in the DB and comparing it to the entered password.-->
<?php
//Database info for connection
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
    //Gets the user inputed data, queries the database for validation
    $usen = $_POST["username"];
    $passw = $_POST["psw"];
    $pdo = new PDO($dsn, $user, $pass, $opt); 
    $sql = "SELECT password_hash FROM Users2 WHERE username = '$usen'";
    foreach($pdo->query($sql) as $val) {
        foreach($val as $val2){
            //must use password_verify function to compare entered password to the database hash
            if (password_verify($_POST["psw"], $val2)) {
                echo "Successfully signed in!";
                //After sign in, assign session values for them
                $_SESSION["username"] = $usen;
                $_SESSION["role"]   = 'customer';

                //Link the customer to account_settings
               echo '<br /><a href="account_settings.php">Click Here to view your account settings</a>';
            }
            //Header never worked for me, but this block attempts to redirect to the same page
            //  if the user messed up his sign in. Either way, this is true. 
            else{
                echo "Invalid Username or Password";
                header("Location:http://web.engr.oregonstate.edu/~dorichp/bike_login.php"); 
                session_destroy();
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

