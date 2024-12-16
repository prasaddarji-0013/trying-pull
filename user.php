<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Real-time email validation
    if (isset($_POST['emailCheck']) && $_POST['emailCheck'] === 'true') {
        $email = $_POST['email'];
        $stmt = $con->prepare("SELECT * FROM crud WHERE email = ?");
        $stmt->execute([$email]);
        echo $stmt->rowCount() > 0 ? "This email is already registered." : "";
        exit;
    }

    // Main form submission
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $category = htmlspecialchars($_POST['category']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        // Check for duplicate email
        $stmt = $con->prepare("SELECT * FROM crud WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'This email is already registered.']);
        } else {
            $stmt = $con->prepare("INSERT INTO crud (name, email, password, category) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $password, $category])) {
                // Set a session message for successful addition
                $_SESSION['success_message'] = 'User added successfully!';
                echo json_encode(['success' => true, 'redirect' => 'display.php']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to insert user into database.']);
            }
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="user.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container my-5">
        <form id="registerForm" method="post">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" placeholder="Enter your name" name="name" autocomplete="off" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" placeholder="Enter your email" name="email" autocomplete="off" id="email" required>
                <span id="emailError" style="color: red;"></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" placeholder="Enter your password" name="password" autocomplete="off" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" class="form-control" placeholder="Enter your category" name="category" autocomplete="off" required>
            </div>
            <button type="submit" class="btn btn-primary my-4">Submit</button>
        </form>
        <div id="response"></div>
    </div>

    <script>
        // Real-time email validation
        $('#email').on('blur', function() {
            var email = $(this).val();
            $.ajax({
                url: 'user.php', // Same file for simplicity
                type: 'POST',
                data: {
                    emailCheck: 'true',
                    email: email
                },
                success: function(response) {
                    $('#emailError').html(response);
                }
            });
        });

        // Submit form with AJAX
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: 'user.php', // Backend file
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json', // Expect JSON response
                success: function(response) {
                    console.log("Server Response:", response); // Debugging response

                    if (response.success) {
                        // Redirect to the provided URL
                        window.location.href = response.redirect;
                    } else {
                        // Display error message
                        $('#response').html('<p style="color: red;">' + response.message + '</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error); // Log the error
                    console.error('Response Text:', xhr.responseText); // Full server response
                    $('#response').html('<p style="color: red;">An error occurred. Please try again later.</p>');
                }
            });
        });
    </script>
</body>

</html>