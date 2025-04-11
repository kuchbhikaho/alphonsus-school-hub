
<?php
// Database connection parameters
$host = "localhost";
$username = "root"; // Default MySQL username
$password = ""; // Default MySQL password is empty
$database = "st_alphonsus";

// Create database connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read SQL file
$sqlFile = 'config/setup_database.sql';
$sql = file_get_contents($sqlFile);

// Execute multi query
if ($conn->multi_query($sql)) {
    echo "<h2>Database setup complete!</h2>";
    echo "<p>The database has been successfully created and populated with sample data.</p>";
    
    // Create a new MySQL user
    $conn = new mysqli($host, $username, $password);
    $createUserSQL = "
        CREATE USER IF NOT EXISTS 'school_admin'@'localhost' IDENTIFIED BY 'school_password';
        GRANT ALL PRIVILEGES ON st_alphonsus.* TO 'school_admin'@'localhost';
        FLUSH PRIVILEGES;
    ";
    if ($conn->multi_query($createUserSQL)) {
        echo "<p>Database user created.</p>";
    } else {
        echo "<p>Error creating database user: " . $conn->error . "</p>";
    }
} else {
    echo "Error setting up database: " . $conn->error;
}

// Close connection
$conn->close();
?>

<p><a href="index.html">Go to Homepage</a></p>
