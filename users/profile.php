<?php

// Include database configuration
include('base.php');
require_once '../config.php'; // Make sure this file connects to your MySQL database

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Initialize variables and error messages
$username = $email = $password = "";
$username_err = $email_err = $password_err = "";

// Fetch user data from the database
if ($stmt = $link->prepare("SELECT id, username, email FROM users WHERE id = ?")) {
    $stmt->bind_param("i", $_SESSION["id"]);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $username, $email);
        $stmt->fetch();
    } else {
        echo "Error: User not found.";
    }
    $stmt->close();
} else {
    echo "Error: Could not prepare SQL query.";
}

// Handle form submission for updating profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password (if provided)
    if (!empty(trim($_POST["password"]))) {
        if (strlen(trim($_POST["password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } else {
            $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
        }
    }

    // If no errors, update the user details in the database
    if (empty($username_err) && empty($email_err) && empty($password_err)) {
        if ($stmt = $link->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?")) {
            $password = empty($password) ? $password : $password; // Check if password was updated

            $stmt->bind_param("sssi", $username, $email, $password, $_SESSION["id"]);
            if ($stmt->execute()) {
                header("location: profile.php?update=success");
                exit;
            } else {
                echo "Error: Could not update profile.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Add Bootstrap or custom CSS here -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Update Profile</h2>

        <!-- Success message after update -->
        <?php if (isset($_GET['update']) && $_GET['update'] == 'success'): ?>
            <div class="alert alert-success">Profile updated successfully!</div>
        <?php endif; ?>

        <!-- Profile Update Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <!-- Username -->
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" required>
                <span class="text-danger"><?php echo $username_err; ?></span>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
                <span class="text-danger"><?php echo $email_err; ?></span>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label>Password (Leave blank to keep unchanged)</label>
                <input type="password" name="password" class="form-control">
                <span class="text-danger"><?php echo $password_err; ?></span>
            </div>

            <!-- Update Button -->
            <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
        </form>

        <!-- Logout Button -->
        <a href="dashboard.php?action=logout" class="btn btn-danger mt-3">Logout</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
