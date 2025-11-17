<?php
// ============================================
//  SECURE LOGIN SCRIPT (USERS PROCEDURES VERSION)
// ============================================

session_start();
require_once('includes/db.php'); // provides $conn (MySQLi object)

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --------------------------------------------
// 1️⃣ Validate Request
// --------------------------------------------
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php?error=" . urlencode("Invalid request method."));
    exit();
}

// Collect form input
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Validate fields
if (empty($username) || empty($password)) {
    header("Location: index.php?error=" . urlencode("Please fill in all fields."));
    exit();
}

// Hash the password before sending to DB (must match stored hash)
$password_hash = hash('sha256', $password);

try {
    // --------------------------------------------
    // 2️⃣ Authenticate User
    // --------------------------------------------
    $stmt = $conn->prepare("CALL sp_AuthenticateUser(?, ?)");
    $stmt->bind_param("ss", $username, $password_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        header("Location: index.php?error=" . urlencode("Invalid username or password."));
        exit();
    }

    if ($user['Status'] !== 'Active') {
        header("Location: index.php?error=" . urlencode("Account is inactive."));
        exit();
    }

    // --------------------------------------------
    // 3️⃣ Generate Token for Session Management
    // --------------------------------------------
    $token = bin2hex(random_bytes(32));  // secure session token
    $expiry_hours = 24;                  // valid for 24 hours

    $stmt = $conn->prepare("CALL sp_GenerateUserToken(?, ?, ?)");
    $stmt->bind_param("isi", $user['User_ID'], $token, $expiry_hours);
    $stmt->execute();
    $stmt->close();

    // --------------------------------------------
    // 4️⃣ Store Session and Cookie (if Remember Me)
    // --------------------------------------------
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['User_ID'];
    $_SESSION['username'] = $user['Username'];
    $_SESSION['role_id'] = $user['Role_ID'];
    $_SESSION['auth_token'] = $token;

    if ($remember) {
        // Secure, HTTP-only cookie valid for 30 days
        setcookie("login_token", $token, time() + (86400 * 30), "/", "", true, true);
    }

    // Redirect to home
    header("Location: home.php");
    exit();

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    header("Location: index.php?error=" . urlencode("A system error occurred during login."));
    exit();
}
?>
