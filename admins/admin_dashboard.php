<?php
// Start session
session_start();
include ('base.php');
// Check if the user is logged in and is an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    // Redirect to login page if not logged in or not an admin
    header("location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
    <p class="text-center">This is the Admin Dashboard.</p>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Manage Users</div>
                <div class="card-body">
                    <h5 class="card-title">View and Edit Users</h5>
                    <p class="card-text">Admin can manage user accounts here.</p>
                    <a href="manage_users.php" class="btn btn-light">Manage Users</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">View Orders</div>
                <div class="card-body">
                    <h5 class="card-title">Monitor Orders</h5>
                    <p class="card-text">View and process book orders.</p>
                    <a href="view_orders.php" class="btn btn-light">View Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Manage Books</div>
                <div class="card-body">
                    <h5 class="card-title">Add or Edit Books</h5>
                    <p class="card-text">Manage the books available for sale.</p>
                    <a href="manage_books.php" class="btn btn-light">Manage Books</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
