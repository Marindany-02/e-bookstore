<?php
session_start();
include ('base.php');
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../login.php");
    exit;
}

require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];

    // Handle file upload
    $upload_dir = "../uploads/";
    $pdf_file = $upload_dir . basename($_FILES['pdf']['name']);
    if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdf_file)) {
        $sql = "INSERT INTO books (title, author, price, pdf_path) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssds", $title, $author, $price, $pdf_file);
            if (mysqli_stmt_execute($stmt)) {
                echo "<div class='alert alert-success'>Book added successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error adding book.</div>";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        echo "<div class='alert alert-danger'>Error uploading PDF file.</div>";
    }
}


$sql = "SELECT * FROM books";
$result = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Manage Books</h1>
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <form action="" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <input type="text" name="title" placeholder="Book Title" class="form-control" required>
    </div>
    <div class="mb-3">
        <input type="text" name="author" placeholder="Author" class="form-control" required>
    </div>
    <div class="mb-3">
        <input type="number" step="0.01" name="price" placeholder="Price" class="form-control" required>
    </div>
    <div class="mb-3">
        <input type="file" name="pdf" accept="application/pdf" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Add Book</button>
</form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Price</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['pdf_path']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
mysqli_close($link);
?>
