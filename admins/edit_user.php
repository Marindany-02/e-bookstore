<?php
// Start session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../login.php");
    exit;
}

// Include database configuration
require_once "../config.php";

// Check if the user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: manage_users.php?error=Invalid User ID");
    exit;
}

$user_id = intval($_GET['id']);

// Fetch user details
$sql = "SELECT username, email, phone_number, role FROM users WHERE id = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $username = $row['username'];
    $email = $row['email'];
    $phone_number = $row['phone_number'];
    $role = $row['role']; // Fetched role
} else {
    header("location: manage_users.php?error=User not found");
    exit;
}

mysqli_stmt_close($stmt);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data including the role
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $role = trim($_POST['role']) ; // Safely retrieve role

    // Validate input
    if (empty($username) || empty($email) || empty($phone_number) || empty($role)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!in_array($role, ['user', 'admin'])) {
        $error = "Invalid role selected.";
    } else {
        // Update user
        $update_sql = "UPDATE users SET username = ?, email = ?, phone_number = ?, role = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($link, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "ssssi", $username, $email, $phone_number, $role, $user_id);

        if (mysqli_stmt_execute($update_stmt)) {
            header("location: manage_users.php?success=User updated successfully");
            exit;
        } else {
            $error = "Error updating user: " . mysqli_error($link);
        }

        mysqli_stmt_close($update_stmt);
    }
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Edit User</h1>
    <a href="manage_users.php" class="btn btn-secondary mb-3">Back to Manage Users</a>
    <?php if (isset($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>
    <form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($phone_number); ?>" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select id="role" name="role" class="form-select" required>
                <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
