<?php
$servername = "localhost";   // Usually 'localhost'
$username = "root";          // XAMPP default
$password = "";              // XAMPP default
$database = "ims_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>