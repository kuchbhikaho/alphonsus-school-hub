
<?php
// Database connection parameters
$host = "localhost";
$username = "school_admin";
$password = "school_password";
$database = "st_alphonsus";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8mb4");
?>
