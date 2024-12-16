<?php 
// $con=new mysqli('localhost','root','','crudoperation');

// if(!$con){
//     die(mysqli_error($con));
// } 
?>


 
<?php
// Database configuration
$host = 'localhost';
$dbname = 'crudoperation';
$username = 'root';
$password = '';

try {
    // Create a new PDO instance with UTF-8 charset for proper encoding
    $con = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Set PDO attributes for better error handling and performance
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable exceptions for errors
    $con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Fetch associative arrays by default

    // Optional: Uncomment for debugging successful connection during development
    // echo "Database connection established successfully!";
} catch (PDOException $e) {
    // Log or display error message
    die("Database connection failed: " . $e->getMessage());
}
?>
