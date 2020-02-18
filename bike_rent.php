<!--Title: bike_rental.php -->
<!--Author: Peter Dorich -->
<!-- Purpose: This page allows a signed-in user to rent a bike from the database -->
<!--          This page accesses the db via sql statement to show available bikes -->

<?php
//Checks to make sure there is an active session with a logged-in user. 
//A non logged-in user may NOT rent a bike.
session_start();
if(!isset($_SESSION['username']))
{
    header('Location:bike_login.php');   
}
?>
<!-- The following style block is for the CSS navigation bar for the site, to make it look a little better-->
<html>        
<head>
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
<body>
<!-- below are the links to each of the active web pages for this website, visible on the top navigation bar -->
    <div class="topnav">
        <a class="active" href="http://web.engr.oregonstate.edu/~dorichp/bike_index.html">Main Menu</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/bike_sign_up.php">Sign-up</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/bike_login">Log-in</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/bike_rent.php">Rent</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/account_settings.php">Account Settings</a>

    </div>

    <h2><b>Rent a Bike for the Day!</b></h2>

        </table>
<?php
//This prints out a welcome statement to the user so that they can see they are lgged in. 
echo "Hi, welcome: ";
echo $_SESSION["username"];
echo "\n";



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
    $pdo = new PDO($dsn, $user, $pass, $opt); 

//SQL STMT to grab all bikes that are NOT checked out. These are the available bikes.
    //Statement used on line 161
    $sql = "SELECT * from Bike WHERE checked_out = 'N'";
    $pdo2 = new PDO($dsn, $user, $pass, $opt);

//This block will query the database looking first to see if the user-chosen bike ID is valid
//Then the result is validated to make sure that the bike is NOT checked OUT ('N')
    $chosen_id = $_POST["b_id"];
    $sql2 = "SELECT * from Bike WHERE b_id = '$chosen_id'";
    foreach($pdo->query($sql2) as $val) {
        if($val["b_id"] == $chosen_id && $val["checked_out"] == 'N'){
            echo "Good Choice, your rental is completed! Visit Account Settings to see your rental!";

            //Randomly chooses employee ID. There are 3 employees:
            $e_rand = mt_rand(1, 3);

            //This block counts the number of RENTAL tuples in the database. The unique ID is an incrementing
            //value.
            $rental_count = "SELECT COUNT(*) FROM Rental";  
                foreach($pdo->query($rental_count)as $val) {
                    foreach($val as $val2) {
                        $r_id = $val2 + '1';
                    }
                }

            //The following block counts the Delivery Tuples, and generates a unique ID.
            //To ensure that every rental has a delivery, the rental tuple and delivery tuple
            //  are inserted one after the other.
            //Employee ID is generated and added to the delivery tuple.
            $delivery_count = "SELECT COUNT(*) FROM Delivery";
                foreach($pdo->query($delivery_count) as $val) {
                    foreach($val as $val2){
                        $d_id = $val2 + '1';
                    }
                 }
            //this SQL statement inserts a new delivery into the database. 
            //Each delivery has an incrementing ID, and the random employee ID generated earlier.
            $deliverySQL = "INSERT INTO Delivery(d_id, FK_e_id) VALUES ('".$d_id."','".$e_rand."')";
            $pdo->query($deliverySQL);
           
            //The following query finds the corresponding customer_id that corresponds with SESSION username
            $c_query = "SELECT c_id FROM Users2 WHERE username = '".$_SESSION['username']."'";
            foreach ($pdo->query($c_query) as $value) { foreach($value as $s_val){}} 

            $c_id = $s_val;

            //This SQL statement inserts a new Rental with a new rental ID, bike id, delivery id, and customer
            //id.
            $rentalSQL = "INSERT INTO Rental (r_id, FK_b_id, FK_d_id, FK_c_id) VALUES ('".$r_id."','".$chosen_id."','".$d_id."','".$c_id."')";
            $pdo->query($rentalSQL);

        }
        else{
            echo "Sorry, that bike doesn't exist or is currently checked out";
        }
    }


} catch (\PDOException $e) {
    $error_message = $e->getMessage();
    echo "<tr><td>", $error_message, "</td></tr>\n";
}
?>
<!--The following html outputs a simple table with the information about the available bikes -->
    <h3><b>Welcome to our Bike Rental Service!</b></h3>
    <h4><b>Below is a list of our available bikes. Choose your favorite
     and fill out the rental form below!</b><h4>

    <table border = "0">
        <tr COLSPAN=2 BGCOLOR="#94CBDF">
            <td>Bike Number</td>
            <td>Brand</td>
            <td>Type</td>
            <td>Color</td>
            <td>Checked Out</td>
        </tr>
        <?php
        foreach($pdo->query($sql) as $row){
            echo "<tr>" .
                "<td>" . $row["b_id"] . "</td>" .
                "<td>" . $row["brand"] . "</td>" .
                "<td>" . $row["type"] . "</td>" .
                "<td>" . $row["color"] . "</td>" .
                "<td>" . $row["checked_out"] . "</td>" .
                "</tr>";
        }
        ?>
    </table>
<!--The following form is where the user will submit the bike_ID number that they wish to rent -->
<form action="bike_rent.php" style="border:1px solid #ccc" method ="post">
  <div class="container">

        <h4><b>Put in the bike number that you'd like to rent for today!</b><h4>


        <label for="b_id">Bike Number:</label>
        <input type="Number" name="b_id" required>
   
        <div class="clearfix">
    
        <br><br>

        </div>
      </div>
    <input type = "submit"/>
</form>

    </body>
</html>
