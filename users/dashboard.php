<?php
// Initialize the session
include('base.php');

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include database configuration
require_once "../config.php";

// Fetch all available books
$sql = "SELECT id, title, author, price FROM books";
$result = mysqli_query($link, $sql);

// Handle purchase requests
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['purchase'])) {
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION["id"];
    $quantity = $_POST['quantity'];
    $total_price = $_POST['total_price'];
    $mpesa_code = $_POST['mpesa_code'];

    // Fetch book details for the purchase
    $book_check = "SELECT title, price FROM books WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $book_check)) {
        mysqli_stmt_bind_param($stmt, "i", $book_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $book_title, $price);
                mysqli_stmt_fetch($stmt);

                // Insert the purchase into the orders table
                $sql_insert = "INSERT INTO orders (user_id, book_id, book_title, quantity, total_price, mpesa_code, status) 
                               VALUES (?, ?, ?, ?, ?, ?, 'pending')";
                if ($insert_stmt = mysqli_prepare($link, $sql_insert)) {
                    mysqli_stmt_bind_param($insert_stmt, "iisids", $user_id, $book_id, $book_title, $quantity, $total_price, $mpesa_code);
                    if (mysqli_stmt_execute($insert_stmt)) {
                        $message = "Purchase request submitted successfully. Awaiting approval.";
                    } else {
                        $message = "Error submitting purchase request.";
                    }
                    mysqli_stmt_close($insert_stmt);
                }
            } else {
                $message = "Book not available.";
            }
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
    <p class="text-center">This is your dashboard where you can view and purchase books.</p>

    <!-- Display Books -->
    <h3 class="mt-4">Available Books</h3>
    <?php if (isset($message)) : ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>Book ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?> USD</td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#purchaseModal" 
                                    data-book-id="<?php echo $row['id']; ?>" 
                                    data-book-title="<?php echo $row['title']; ?>" 
                                    data-price="<?php echo $row['price']; ?>">Purchase</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" class="text-center">No books available at the moment.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="purchaseModalLabel">Purchase Book</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="dashboard.php">
            <input type="hidden" name="book_id" id="book_id">
            <div class="mb-3">
                <label for="book_title" class="form-label">Book Title</label>
                <input type="text" class="form-control" id="book_title" name="book_title" readonly>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required min="1" value="1">
            </div>
            <div class="mb-3">
                <label for="total_price" class="form-label">Total Price</label>
                <input type="text" class="form-control" id="total_price" name="total_price" readonly>
            </div>
            <div class="mb-3">
                <label for="mpesa_code" class="form-label">MPESA Code</label>
                <input type="text" class="form-control" id="mpesa_code" name="mpesa_code" required>
            </div>
            <button type="submit" name="purchase" class="btn btn-primary">Submit Purchase</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Get modal elements and set values
    var purchaseModal = document.getElementById('purchaseModal');
    purchaseModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var bookId = button.getAttribute('data-book-id');
        var bookTitle = button.getAttribute('data-book-title');
        var price = button.getAttribute('data-price');
        
        // Populate the modal fields with data
        var modalBookId = document.getElementById('book_id');
        var modalBookTitle = document.getElementById('book_title');
        var modalTotalPrice = document.getElementById('total_price');

        modalBookId.value = bookId;
        modalBookTitle.value = bookTitle;
        modalTotalPrice.value = price;
    });
</script>
</body>
</html>

<?php
// Close the database connection
mysqli_close($link);
?>
