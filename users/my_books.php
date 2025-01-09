<?php
// Initialize the session and include necessary files
include('base.php');

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include database configuration
require_once "../config.php";

// Fetch user's purchased books with a join to get book details
$sql = "SELECT orders.id AS order_id, books.title AS book_title, books.author, books.pdf_path, orders.status
        FROM orders
        JOIN books ON orders.book_id = books.id
        WHERE orders.user_id = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        // Check if the result has rows
        if (mysqli_num_rows($result) > 0) {
            $no_purchases = false;
        } else {
            $no_purchases = true;
        }
    } else {
        echo "Error executing query: " . mysqli_error($link);
    }
} else {
    echo "Error preparing query: " . mysqli_error($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Purchased Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>My Purchases</h2>
    
    <?php if (isset($no_purchases) && $no_purchases) : ?>
        <p>No purchases found.</p>
    <?php else : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <?php if ($row['status'] == 'approved') { ?>
                                <a href="<?php echo htmlspecialchars($row['pdf_path']); ?>" class="btn btn-primary" target="_blank">Read</a>
                                <a href="<?php echo htmlspecialchars($row['pdf_path']); ?>" class="btn btn-success" download>Download</a>
                            <?php } else { ?>
                                <span class="text-warning">Pending Approval</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
mysqli_stmt_close($stmt);
mysqli_close($link);
?>
