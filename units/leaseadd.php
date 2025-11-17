<?php 
require_once('includes/db.php'); // Ensure $conn (MySQLi) is available

// ------------------------------
// 1️⃣ Validate session
// ------------------------------
if (!isset($_SESSION['logged_in'])) {
    echo "<script>
        alert('Please login to access this page.');
        window.location.href = 'index.php';
    </script>";
    exit();
}

// ------------------------------
// 2️⃣ Fetch clients and units for dropdowns
// ------------------------------
$clients = [];
$units = [];

$result = $conn->query("CALL Get_All_Clients()");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
    $result->free();
    $conn->next_result();
}

$result = $conn->query("CALL Get_All_Units()");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Only show vacant units
        if (strtolower($row['Occupancy_Status']) === 'vacant') {
            $units[] = $row;
        }
    }
    $result->free();
    $conn->next_result();
}

$message = '';

// ------------------------------
// 3️⃣ Handle form submission
// ------------------------------
if (isset($_POST['add_lease'])) {
    $client_id = intval($_POST['Client_ID']);
    $unit_id = intval($_POST['Unit_ID']);
    $lease_start = $_POST['Lease_Start'];
    $lease_end = $_POST['Lease_End'];
    $rent = floatval($_POST['Rent_Amount']);
    $deposit = floatval($_POST['Deposit_Amount']);
    $recorded_by = $_SESSION['username'] ?? 'Admin';

    // Preview the stored procedure call
    $preview_call = sprintf(
        "CALL AddLease(%d, %d, '%s', '%s', %.2f, %.2f, '%s')",
        $client_id,
        $unit_id,
        $lease_start,
        $lease_end,
        $rent,
        $deposit,
        addslashes($recorded_by)
    );

    try {
        $stmt = $conn->prepare("CALL AddLease(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissdds", $client_id, $unit_id, $lease_start, $lease_end, $rent, $deposit, $recorded_by);
        $stmt->execute();
        $stmt->close();
        $conn->next_result();

        $message = "✅ Lease added successfully!<br><small>Preview of procedure call: <code>$preview_call</code></small>";
    } catch (Exception $e) {
        $message = "❌ Error adding lease: " . addslashes($e->getMessage()) . "<br><small>Preview: <code>$preview_call</code></small>";
    }
}
?>

<h3 class="mb-4 text-center text-primary fw-bold">Add New Lease</h3>

<?php if (!empty($message)): ?>
<div class="alert <?= str_contains($message, 'Error') ? 'alert-danger' : 'alert-success' ?> text-center">
    <?= $message ?>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">Lease Details</div>
    <div class="card-body">
        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Select Client</label>
                    <select name="Client_ID" class="form-select" required>
                        <option value="">-- Select Client --</option>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= $c['Client_ID'] ?>"><?= htmlspecialchars($c['Client_Name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Select Unit (Vacant Only)</label>
                    <select name="Unit_ID" class="form-select" required>
                        <option value="">-- Select Unit --</option>
                        <?php foreach ($units as $u): ?>
                            <option value="<?= $u['Unit_ID'] ?>"><?= htmlspecialchars($u['Unit_Code'] . ' - ' . $u['Unit_Name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Lease Start Date</label>
                    <input type="date" name="Lease_Start" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Lease End Date</label>
                    <input type="date" name="Lease_End" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rent Amount (UGX)</label>
                    <input type="number" step="0.01" name="Rent_Amount" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Deposit Amount (UGX)</label>
                    <input type="number" step="0.01" name="Deposit_Amount" class="form-control" required>
                </div>
            </div>

            <button type="submit" name="add_lease" class="btn btn-primary">Add Lease</button>
        </form>
    </div>
</div>
