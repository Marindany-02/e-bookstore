<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "books";

// Create connection
$link = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($link->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
