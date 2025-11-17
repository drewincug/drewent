<?php
require_once('includes/db.php'); 

if (!isset($_SESSION['logged_in']) || empty($_GET['Prop_ID'])) {
    echo "<script>
       alert('Invalid request or not logged in.');
      window.location.href = 'home.php?tag=proplist';
    </script>";
    exit();
}

$Prop_ID = intval($_GET['Prop_ID']);
$action_by = $_SESSION['username'] ?? 'Admin';

try {
    // Prepare and call the stored procedure
    $stmt = $conn->prepare("CALL CloseProperty(?, ?)");
    $stmt->bind_param("is", $Prop_ID, $action_by);
    $stmt->execute();
    $stmt->close();
    $conn->next_result();

    // Success feedback
    echo "<script>
       alert('Property closed successfully.');
        window.location.href = 'home.php?tag=proplist';
    </script>";
    exit();

} catch (mysqli_sql_exception $e) {
   $error = addslashes($e->getMessage());
   echo "<script>
     alert('Error: {$error}');
    window.location.href = 'home.php?tag=proplist';
    </script>";
    exit();
}
?>
