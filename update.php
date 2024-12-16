<?php
session_start();
include 'connect.php'; // Ensure this file contains the correct PDO connection
// Check if `updateid` is passed in the URL
if (isset($_GET['updateid']) && is_numeric($_GET['updateid'])) {
    $id = (int)$_GET['updateid'];
    // Fetch data for the given ID using PDO
    $sql = "SELECT * FROM crud WHERE id = :id";
    $stmt = $con->prepare($sql);
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    if ($row) {
        $name = $row['name'];
        $email = $row['email'];
        $password = $row['password'];
        $category = $row['category'];
    } else {
        die("Record not found.");
    }
} else {
    die("Invalid ID.");
}
// Handle form submission for updating the record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch and sanitize POST data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']); // Assuming it's already hashed
    $category = htmlspecialchars($_POST['category']);
    // Check if email is duplicate (excluding the current record)
    $sqlEmailCheck = "SELECT id FROM crud WHERE email = :email AND id != :id";
    $stmtEmailCheck = $con->prepare($sqlEmailCheck);
    $stmtEmailCheck->execute(['email' => $email, 'id' => $id]);
    if ($stmtEmailCheck->rowCount() > 0) {
        echo "<script>alert('Email is already in use by another user.');</script>";
    } else {
        // Update the record using PDO
        $sqlUpdate = "UPDATE crud SET 
                        name = :name, 
                        email = :email, 
                        password = :password, 
                        category = :category 
                      WHERE id = :id";
        $stmtUpdate = $con->prepare($sqlUpdate);
        $resultUpdate = $stmtUpdate->execute([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'category' => $category,
            'id' => $id
        ]);
        if ($resultUpdate) {
            // Set success message in session
            $_SESSION['success_message'] = 'Record updated successfully!';
            // Redirect to display.php to show the success message
            header("Location: display.php");  // Ensure you're redirecting to display.php
            exit();
        } else {
            echo "<script>alert('Failed to update the record. Please try again.');</script>";
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="update.css" rel="stylesheet">
    <title>Update Record</title>
</head>

<body>
    <div class="container my-5">
        <!-- Display success message if session is set -->
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']); // Unset to avoid showing again
        }
        ?>
        <form method="post">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name="name" placeholder="Enter your name" autocomplete="off" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" placeholder="Enter your email" autocomplete="off" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter your password" autocomplete="off" value="<?php echo htmlspecialchars($password); ?>" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" class="form-control" name="category" placeholder="Enter your category" autocomplete="off" value="<?php echo htmlspecialchars($category); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update</button>
        </form>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#updateForm").on("submit", function(e) {
                e.preventDefault(); // Prevent default form submission

                // Serialize form data
                const formData = $(this).serialize() + "&ajax=true";

                // Send AJAX POST request
                $.ajax({
                    url: 'update.php', // Same PHP file
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "success") {
                            $("#message").html('<div class="alert alert-success">' + response.message + '</div>');
                        } else {
                            $("#message").html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    },
                    error: function(jqXHR) {
                        $("#message").html('<div class="alert alert-danger">An unknown error occurred.</div>');
                        console.error("Error response:", jqXHR.responseText);
                    }
                });
            });
        });
    </script>
</body>

</html>