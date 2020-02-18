<!--Title: account_settings.php -->
<!--Author: Peter Dorich -->
<!-- Purpose: This page is the major portion of the database functionality.
                This page allows deletion, reading, and updating of rentals in the database.
                Creation is under the "bike_rent.php".  -->
<!--php block makes sure that a non-logged in user cannot access this page.-->
<?php
session_start();
if(!isset($_SESSION['username']))
{
    // not logged in
    header('Location:bike_login.php');
}
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
        <a class="active" href="http://web.engr.oregonstate.edu/~dorichp/bike_index.html">Main Menu</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/bike_sign_up.php">Sign-up</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/bike_login">Log-in</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/bike_rent.php">Rent</a>
        <a href="http://web.engr.oregonstate.edu/~dorichp/account_settings.php">Account Settings</a>

    </div>

<?php
//ini_set ( 'display_errors', 'On');

//Welcome message that corresponds with the logged-in user
echo "Hi, welcome: ", $_SESSION["username"];

//Database access/information
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


   //DB query to find the corresponding customer ID to the signed in user.
   $sql = "SELECT c_id FROM Users2 WHERE username = '".$_SESSION['username']."'";
    foreach ($pdo->query($sql) as $value) { foreach($value as $s_val){}} 
  

//The following Query joins 3 tables together to extract all useful information to the customer, 
//including the rental number, for reference, the bike type, the brand, color, and the employee name
//that is delivering the bike
   $rental_query = "SELECT Rental.r_id, Bike.b_id, Bike.type, Bike.brand, Bike.color, Employee.name FROM Rental INNER JOIN Bike ON Bike.b_id = Rental.FK_b_id INNER JOIN Delivery ON Delivery.d_id = Rental.FK_d_id INNER JOIN Employee ON Employee.e_id = Delivery.FK_e_id WHERE FK_c_id = '$s_val'";

    foreach($pdo->query($rental_query) as $value){
        //
    }

} catch (\PDOException $e) {
    $error_message = $e->getMessage();
    echo "<tr><td>", $error_message, "</td></tr>\n";
}
?>

<!--This is the same styled table in bike_rent.php that instead loads only active rentals
    for the specified user -->
<body>
<br>
    <h3><b>Welcome to your account! Here you can view, delete, or update your rentals!</b></h3>

    <h4><b>Here are your current rentals:</b></h4>
    <br>

    <table border = "0">
        <tr COLSPAN=2 BGCOLOR="#94CBDF">
            <td>Rental ID number</td>
            <td>Bike ID number</td>
            <td>Bike Type</td>
            <td>Bike Brand</td>
            <td>Color</td>
            <td>Delivery Employee</td>

        </tr>
    <?php
    //This runs the long rental_query from above with the 3 joins on it.
        foreach($pdo->query($rental_query) as $row){
            echo "<tr>" .
                "<td>" . $row["r_id"] . "</td>" .
                "<td>" . $row["b_id"] . "</td>" .
                "<td>" . $row["type"] . "</td>" .
                "<td>" . $row["brand"] . "</td>" .
                "<td>" . $row["color"] . "</td>" .
                "<td>" . $row["name"] . "</td>" .
                "</tr>";
        }
     ?>
    </table>
<!--This form is for the CANCEL ORDER. The user will input the RENTAL id they'd like to cancel -->
<form action="account_settings.php" style="border:1px solid #ccc" method ="post">
  <div class="container">

     <h5><b>If you'd like to CANCEL your rental, enter the rental ID below<b><h5>

        <label for="r_id">Order Cancel: Rental Number:</label>
        <input type="Number" name="r_id">
   
         <div class="clearfix">
    
        <br><br>

        </div>
      </div>
    <input name="submit_btn" type = "submit"/>
</form>
<!-- This is the form if the user wants to EDIT a current rental. -->
<form action="account_settings.php" style="border:1px solid #ccc" method ="post">
  <div class="container">

     <h5><b>If you'd like to EDIT your rental, enter the rental ID below, along with the NEW Bike ID<b><h5>

        <label for="rental_id">Order Edit: Rental Number:</label>
        <input type="Number" name="rental_id">
   

        <label for="bike_id"> New Bike ID:</label>
        <input type="Number" name="bike_id">

        <div class="clearfix">
    
        <br><br>

        </div>
      </div>
    <input name="submit_btn2" type = "submit"/>
</form>


<?php
    //PHP code for the delete rentals option
    //First it checks which submit button was pressed, deciding which action to take
    if(isset($_POST['submit_btn'])){
    
    //The query below will find any rentals the customer made
    $verify_query = "SELECT r_id FROM Rental WHERE FK_c_id = $s_val";

    foreach($pdo->query($verify_query) as $value){
    //verifies the user has the rental
        if($value["r_id"] == $_POST["r_id"]){
            $val = $_POST["r_id"];
            $cancel_query = "DELETE FROM Rental WHERE r_id = $val";
            $pdo->query($cancel_query);
            echo "Alrighty, your rental has been canceled. Reload the page to confirm.";
        }
     }   

    }
    //This php code is for the UPDATE option/functionality. 
    if(isset($_POST['submit_btn2'])){

        //Check if he/she has the rented bike
        $chosen_id = $_POST["bike_id"];
        $rental_id = $_POST["rental_id"];
        $s_value = $s_val;
        //First make sure the bike is not checked out
        $sql2 = "SELECT * from Bike WHERE b_id = '$chosen_id'";
        foreach($pdo->query($sql2) as $val) {
            if($val["b_id"] == $chosen_id && $val["checked_out"] == 'N'){
        //Next, verify the user actually rented that bike
                $verify_rental = "SELECT r_id FROM Rental WHERE FK_c_id = $s_val";
                foreach($pdo->query($verify_rental) as $value){
                    if($value["r_id"] == $_POST["rental_id"]){
        //Last, update the database with the new bike number
                        $update_query = "UPDATE Rental SET r_id=$rental_id,FK_b_id=$chosen_id WHERE Rental.r_id = '$rental_id'";
                        $pdo->query($update_query);
                    }
                }
            }
        }
    }
?>
    </body>
</html>

