<?php
// Include config file
require_once 'config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username, email, and new password from the form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    // Validate user input
    if (!empty($username) && !empty($email) && !empty($new_password)) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Check if the username and email match in the database
        if ($stmt = $link->prepare("SELECT id FROM users WHERE username = ? AND email = ?")) {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // Update the password in the database
                if ($update_stmt = $link->prepare("UPDATE users SET password = ? WHERE username = ? AND email = ?")) {
                    $update_stmt->bind_param("sss", $hashed_password, $username, $email);
                    $update_stmt->execute();

                    echo "<div class='alert alert-success'>Your password has been updated successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: Could not update password.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Error: Username or email does not match.</div>";
            }

            $stmt->close();
        }
    } else {
        echo "<div class='alert alert-warning'>Please fill in all fields.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Reset Your Password</h4>
                </div>
                <div class="card-body">
                    <!-- Form starts here -->
                    <form action="reset_password.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Password</button>
                        <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register here</a>.</p>
                        <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a>.</p>
                    </form>
                    <!-- Form ends here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

</body>
</html>
