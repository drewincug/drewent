<?php 
require_once('includes/db.php');

if (!isset($_SESSION['logged_in']) || empty($_GET['Prop_ID'])) {
   echo "<script>
     alert('Error: {$error}');
    window.location.href = 'home.php?tag=proplist';
    </script>";

   // header("Location: prop/proplist.php?error=" . urlencode("Invalid request or not logged in."));
    exit();
}

$Prop_ID = intval($_GET['Prop_ID']);
$action_by = $_SESSION['username'] ?? 'Admin';

try {
    $stmt = $conn->prepare("CALL DeleteProperty(?, ?)");
    $stmt->bind_param("is", $Prop_ID, $action_by);
    $stmt->execute();
    $stmt->close();
    $conn->next_result();
    echo "<script>
     alert('Error: {$error}');
    window.location.href = 'home.php?tag=proplist';
    </script>";
   // header("Location: prop/proplist.php?success=" . urlencode("Property deleted successfully."));
    exit();
} catch (Exception $e) {
    echo "<script>
     alert('Error: {$error}');
    window.location.href = 'home.php?tag=proplist';
    </script>";


    //header("Location: prop/proplist.php?error=" . urlencode("Error: " . $e->getMessage()));
    exit();
}
