<?php 
require_once 'includes/db.php'; // $conn (mysqli)

$message = '';

// Only allow logged in staff (optional)
if (!isset($_SESSION['logged_in'])) {
    // Redirect or show message
    // header('Location: index.php');
    // exit();
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect + sanitize inputs
    $firstname              = trim($_POST['firstname'] ?? '');
    $lastname               = trim($_POST['lastname'] ?? '');
    $gender                 = trim($_POST['gender'] ?? '');
    $date_of_birth          = trim($_POST['date_of_birth'] ?? null); // yyyy-mm-dd
    $national_id            = trim($_POST['national_id'] ?? '');
    $phone                  = trim($_POST['phone'] ?? '');
    $email                  = trim($_POST['email'] ?? '');
    $physical_address       = trim($_POST['address'] ?? '');
    $client_type            = trim($_POST['client_type'] ?? 'tenant'); // must match account_types.acctype_name
    $introducer_staff_id    = intval($_POST['introducer_staff_id'] ?? 0);
    $created_by_staff_id    = intval($_SESSION['staff_id'] ?? ($_POST['created_by_staff_id'] ?? 0));

    // Preview the CALL (safe for review)
    $preview_call = sprintf(
        "CALL sp_addclientAndAccounts('%s','%s','%s','%s','%s','%s','%s','%s','%s',%d,%d)",
        addslashes($firstname),
        addslashes($lastname),
        addslashes($gender),
        addslashes($date_of_birth ?? ''),
        addslashes($national_id),
        addslashes($phone),
        addslashes($email),
        addslashes($physical_address),
        addslashes($client_type),
        $introducer_staff_id,
        $created_by_staff_id
    );

    // Uncomment to display preview and stop (for manual verification)
    // echo "<pre>$preview_call</pre>"; exit;

    // Call stored procedure (11 parameters)
    try {
        $stmt = $conn->prepare("CALL sp_addclientAndAccounts(?,?,?,?,?,?,?,?,?,?,?)");
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Types: 9 strings (s), 2 ints (i) => "sssssssssii"
        $bind = $stmt->bind_param(
            "sssssssssii",
            $firstname,
            $lastname,
            $gender,
            $date_of_birth,
            $national_id,
            $phone,
            $email,
            $physical_address,
            $client_type,
            $introducer_staff_id,
            $created_by_staff_id
        );
        if ($bind === false) {
            throw new Exception("bind_param failed: " . $stmt->error);
        }

        $exec = $stmt->execute();
        if ($exec === false) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Try to fetch result set (procedure SELECTs new_client_id, client_code)
        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            $new_client_id = $row['new_client_id'] ?? null;
            $client_code = $row['client_code'] ?? null;
            $message = "✅ Client created. ID: " . intval($new_client_id) . " Code: " . htmlspecialchars($client_code);
        } else {
            // Some setups don't return get_result() for CALL — try fetching next result
            // (If your procedure SELECTs values, above should work; else you can query last inserted client)
            $message = "✅ Client created (no returned row). Preview: <code>" . htmlspecialchars($preview_call) . "</code>";
        }

        $stmt->close();
        // clear stored-proc leftovers
        while ($conn->more_results() && $conn->next_result()) { /* noop */ }

    } catch (Exception $e) {
        $message = "❌ Error adding client: " . addslashes($e->getMessage()) . "<br><small>Preview: <code>" . htmlspecialchars($preview_call) . "</code></small>";
    }
}
?>

<!-- SIMPLE FORM -->
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Add Client</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3 class="mb-3">Add New Client</h3>

    <?php if (!empty($message)): ?>
        <div class="alert <?= strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">First name</label>
            <input class="form-control" name="firstname" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Last name</label>
            <input class="form-control" name="lastname" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
                <option>Male</option>
                <option>Female</option>
                <option>Other</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Date of birth</label>
            <input type="date" name="date_of_birth" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">National ID</label>
            <input class="form-control" name="national_id">
        </div>
        <div class="col-md-3">
            <label class="form-label">Phone</label>
            <input class="form-control" name="phone" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email">
        </div>
        <div class="col-md-6">
            <label class="form-label">Client Type</label>
            <select name="client_type" class="form-select" required>
                <option value="tenant">tenant</option>
                <option value="tent_leaser">tent_leaser</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Address</label>
            <textarea class="form-control" name="address" rows="2"></textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">Introducer Staff ID</label>
            <input class="form-control" name="introducer_staff_id" type="number">
        </div>

        <div class="col-12">
            <button class="btn btn-primary" type="submit">Add Client</button>
        </div>
    </form>
</div>
</body>
</html>
