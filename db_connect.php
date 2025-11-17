<?php
/**
 * Database Connection Script
 * This file defines constants for database access and establishes a connection using mysqli.
 * Other scripts should include this file to get the $conn object.
 */

// Define database credentials
// NOTE: You must replace these simulated values with your actual database host, username, and password.
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dr_ent'); // The database requested by the user

// --- Establish Connection ---

// Create a new mysqli connection object
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($conn->connect_error) {
    // Log the error (best practice) and then terminate the script gracefully.
    error_log("Failed to connect to MySQL: " . $conn->connect_error);
    
    // In a production environment, avoid showing direct error messages to users.
    die("Database connection error. Please try again later.");
}

// Set character set to UTF-8 for proper data handling
$conn->set_charset("utf8mb4");

// The $conn variable is now ready to use for queries and stored procedures.
?>
