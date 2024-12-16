<?php
// session_start();
// include 'connect.php';
                                             
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $email = addslashes($_POST['email']);
//     $password = addslashes($_POST['password']);

//     // $sql = "SELECT * FROM crud WHERE email='$email' AND password='$password'";
//     $sql="select * from crud where email='$email' AND password ='$password'";
//     $result = mysqli_query($con, $sql);
//     $row = mysqli_fetch_assoc($result);

//     if ($row) {
//         $_SESSION['loggedin'] = true;
//         $_SESSION['email'] = $row['email'];

//         header('Location: display.php');
//     } else {
//         echo "Invalid email or password";
//     }
// } 
    ?>
    <?php 
//     session_start();
//  include 'connect.php';

//  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//      $email = $_POST['email'];
//      $password = $_POST['password'];

//      // Using a prepared statement to prevent SQL injection
//      $sql = "SELECT * FROM crud WHERE email = ?";
//      $stmt = mysqli_prepare($con, $sql);
//      mysqli_stmt_bind_param($stmt, "s", $email);
//      mysqli_stmt_execute($stmt);
//      $result = mysqli_stmt_get_result($stmt);
//      $row = mysqli_fetch_assoc($result);

//      // Password verification with hashed password
//      if ($row && password_verify($password, $row['password'])) {
//          $_SESSION['loggedin'] = true;
//          $_SESSION['email'] = $row['email'];
//          header('Location: display.php');
//          exit(); // Stop further code execution
//      } else {
//          echo "Invalid email or password";
//      }
//  } 
 ?>
<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL query with placeholders
    $sql = "SELECT * FROM crud WHERE email = :email";
    $stmt = $con->prepare($sql);

    // Check if prepare was successful
    if (!$stmt) {
        // If prepare failed, output the error info
        echo "Error in preparing the statement: " . implode(":", $con->errorInfo());
        exit;
    }

    // Bind the email parameter
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the email exists
    if ($row) {
        $storedPassword = $row['password'];

        // Determine if the password is hashed
        if (strlen($storedPassword) === 60 && password_verify($password, $storedPassword)) {
            // Password is hashed and verified
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $row['email'];
            header('Location: display.php');
            exit;
        } elseif ($storedPassword === $password) {
            // Password is plain text and matches
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $row['email'];
            header('Location: display.php');
            exit;
        } else {
            echo "Invalid email or password";
        }
    } else {
        echo "Invalid email or password";
    }
}
?>


















<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="login.css" rel="stylesheet">
    <title>login</title>
</head>
<body>
    <div class="container" >
        <form method="post" action="loginn.php">
        <h3 class="ter">sign in</h3>
        <br> 
        <input type="email" class="form-control" placeholder="enter your email" required name="email" autocomplete="off">
        <input type="password" class="form-control" placeholder="enter your password" required name="password" autocomplete="off">
   
        <button type="submit" class="btn-btn-primary signin" name="signin">login</button>
        </form>
        <div class="mt-3 text-center">
                    <p>Don't have an account?</p>
                    <!-- Redirect to user.php -->
                    <a href="user.php" class="btn btn-secondary">Register</a>
                </div>
    </div>
</body>
</html>