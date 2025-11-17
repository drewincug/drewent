<?php
// ========================================
//  SYSTEM MAIN PAGE
//  - Handles secure session management
//  - Token-based auto-login
//  - Page routing
// ========================================

declare(strict_types=1);
session_start();

// Enable debugging (disable in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db.php'; // MySQLi connection ($conn)

// ------------------------------
// 1️⃣ Token-Based Auto Login
// ------------------------------
if (empty($_SESSION['logged_in']) && !empty($_COOKIE['login_token'])) {
    $token = $_COOKIE['login_token'];

    try {
        $stmt = $conn->prepare("CALL sp_ValidateToken(?)");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->next_result();

        if ($user) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id']   = $user['User_ID'];
            $_SESSION['username']  = $user['Username'];
            $_SESSION['role_id']   = $user['Role_ID'];
        } else {
            setcookie("login_token", "", time() - 3600, "/");
            header("Location: index.php?error=" . urlencode("Session expired. Please log in again."));
            exit();
        }
    } catch (Throwable $e) {
        error_log("Token validation error: " . $e->getMessage());
        header("Location: index.php?error=" . urlencode("Session error. Please log in again."));
        exit();
    }
}

// ------------------------------
// 2️⃣ Ensure User is Logged In
// ------------------------------
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php?error=" . urlencode("Please login to access the system."));
    exit();
}

// ------------------------------
// 3️⃣ Fetch Logged-In User Info
// ------------------------------
$logged_in_user = null;
if (!empty($_SESSION['user_id'])) {
    try {
        $stmt = $conn->prepare("CALL sp_GetUserByID(?)");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $logged_in_user = $result->fetch_assoc();
        $stmt->close();
        $conn->next_result();
    } catch (Throwable $e) {
        error_log("Fetch user error: " . $e->getMessage());
    }
}

// ------------------------------
// 4️⃣ Include Template Sections
// ------------------------------
include 'includes/head.php';
include 'includes/style.php';
?>
</head>
<body>
<?php
include 'includes/aside.php';   // Sidebar
include 'includes/topbar.php';  // Top Navigation
?>

<main class="flex-grow-1 p-4">
<?php
// ------------------------------
// 5️⃣ Dynamic Page Routing
// ------------------------------
$tag = $_GET['tag'] ?? 'dash';

// Map route tags to their file paths
$pages = [
    "dash"        => "dash.php",
    "proplist"    => ["prop/propnav.php", "prop/proplist.php"],
    "propview"    => ["prop/propnav.php", "prop/propview.php"],
    "propadd"     => ["prop/propnav.php", "prop/propadd.php"],
    "proptypeadd"    => ["prop/propnav.php", "prop/proptypeadd.php"],
    "proptypelist"    => ["prop/propnav.php", "prop/proptypelist.php"],
    "propdelete"  => ["prop/propdelete.php"],
    "propclose"   => ["prop/propclose.php"], 

    "unitadd"     => ["units/unitnav.php", "units/unitadd.php"],
    "unitlist"    => ["units/unitnav.php", "units/unitlist.php"],
    "unitedit"    => ["units/unitnav.php", "units/unitedit.php"],
    "unitclose"    => ["units/unitclose.php"],
    "unitdelete"    => ["units/unitdelete.php"],

    "leaseadd"    => ["units/unitnav.php", "units/leaseadd.php"],
    "leaselist"    => ["units/unitnav.php", "units/leaselist.php"],

    "tntlist"     => ["tnt/tntnav.php", "tnt/tntlist.php"],
    "tntadd"      => ["tnt/tntnav.php", "tnt/tntadd.php"],

    "pay"         => ["pay/paynav.php", "pay/pay.php"],

    "set"         => ["set/setnav.php", "set/set.php"],
    "userlist"    => ["set/setnav.php", "set/userlist.php"],
    "compadd"     => ["set/setnav.php", "set/compadd.php"],
    "changepass"  => ["set/setnav.php", "set/changepass.php"],
    "myprofile"   => ["set/setnav.php", "set/myprofile.php"],
    "compedit"    => ["set/setnav.php", "set/compedit.php"],

    "clientsadd"   => ["clients/clientsnav.php", "clients/clientsadd.php"],
    "clientslist"  => ["clients/clientsnav.php", "clients/clientslist.php"],
    "clientsedit"  => ["clients/clientsnav.php", "clients/clientsedit.php"],
    "clientsedit"  => ["clients/clientsedit.php"],
    "clientsedit"  => ["clients/clientsedit.php"],


    "tentlist"   => ["tent/tentnav.php", "tent/tentlist.php"],
    "tentadd"   => ["tent/tentnav.php", "tent/tentadd.php"],
    "tentedit"   => ["tent/tentnav.php", "tent/tentedit.php"],
    "tentlease"   => ["tent/tentnav.php", "tent/tentlease.php"],

    "profile"     => "includes/user/profile.php"
];
 

// Route to matching page
if (isset($pages[$tag])) {
    foreach ((array)$pages[$tag] as $file) {
        include $file;
    }
} else {
    echo "<div class='alert alert-warning text-center my-5'>
            <strong>⚠️ Page Not Found:</strong> The requested page does not exist.
          </div>";
}
?>
</main>

<?php
include 'includes/foot.php';
include 'includes/js.php';
?>
</body>
</html>
