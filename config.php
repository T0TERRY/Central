<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'budget_managers');
define('DB_USER', 'root'); // Change this to your database username
define('DB_PASS', ''); // Change this to your database password

// Create database connection
function getDBConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Close database connection
function closeDBConnection($conn) {
    $conn = null;
}
?>