<?php
//session_start(); // Ensure session is active
require_once('includes/db.php');

// ------------------------------
// Validate session and input
// ------------------------------
if (!isset($_SESSION['logged_in']) || empty($_GET['Unit_ID'])) {
    echo "<script>
        alert('Invalid request or you are not logged in.');
        window.location.href = 'home.php?tag=unitlist';
    </script>";
    exit();
}

$Unit_ID = intval($_GET['Unit_ID']);
$action_by = $_SESSION['username'] ?? 'Admin';

try {
    // ------------------------------
    // Call CloseUnit stored procedure
    // ------------------------------
    $stmt = $conn->prepare("CALL CloseUnit(?, ?)");
    $stmt->bind_param("is", $Unit_ID, $action_by);
    $stmt->execute();
    $stmt->close();

    // Clean up extra results for stored procedures
    while ($conn->more_results() && $conn->next_result()) {;}

    // ------------------------------
    // Success feedback via JS
    // ------------------------------
    echo "<script>
        alert('Unit closed successfully.');
        window.location.href = 'home.php?tag=unitlist';
    </script>";
    exit();

} catch (mysqli_sql_exception $e) {
    $error = addslashes($e->getMessage());
    echo "<script>
        alert('Error closing unit: {$error}');
        window.location.href = 'home.php?tag=unitlist';
    </script>";
    exit();
}
?>
