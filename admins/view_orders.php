<?php
// Start session
session_start();
include('base.php');

// Redirect if the user is not logged in or not an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../login.php");
    exit;
}

// Include database configuration
require_once "../config.php";

// Handle order approval
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve'])) {
    $order_id = $_POST['order_id'];

    // Prevent SQL injection by sanitizing the input
    $order_id = mysqli_real_escape_string($link, $order_id);

    // Update order status
    $sql = "UPDATE orders SET status = 'approved' WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Order approved successfully.";
        } else {
            $_SESSION['message'] = "Error approving the order.";
        }
        mysqli_stmt_close($stmt);
    }
    header("location: view_orders.php");
    exit;
}

// Fetch orders data
$sql = "SELECT orders.id AS order_id, users.username, books.title AS book_title, 
               orders.order_date, orders.status 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        JOIN books ON orders.book_id = books.id";
$result = mysqli_query($link, $sql);

if (!$result) {
    die("Error executing query: " . mysqli_error($link));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">View Orders</h1>

    <!-- Success/Error Message -->
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Username</th>
                <th>Book Title</th>
                <th>Purchase Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td>
                            <?php 
                                echo ($row['status'] === 'approved') 
                                    ? "<span class='text-success'>Approved</span>" 
                                    : "<span class='text-warning'>Pending</span>";
                            ?>
                        </td>
                        <td>
                            <?php if ($row['status'] === 'pending'): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>Approved</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No orders found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Close the database connection
mysqli_close($link);
?>
