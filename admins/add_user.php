<?php
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    $sql = "INSERT INTO users (username, email, phone_number, role, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $username, $email, $phone_number, $role, $password);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: manage_users.php?success=User added successfully");
    } else {
        echo "Error: " . mysqli_error($link);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
}
?>

<div class="mb-4">
    <h4>Add New User</h4>
    <form action="add_user.php" method="POST" class="row g-3">
        <div class="col-md-2">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="col-md-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="col-md-2">
            <input type="text" name="phone_number" class="form-control" placeholder="Phone Number" required>
        </div>
        <div class="col-md-2">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="col-md-2">
            <select name="role" class="form-select" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-success">Add</button>
        </div>
    </form>
</div>
