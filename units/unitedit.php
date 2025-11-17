<?php
require_once('includes/db.php');

// Ensure session is started for $_SESSION variables to work
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ------------------------------
// 1️⃣ Validate input and session
// ------------------------------
if (!isset($_SESSION['logged_in']) || empty($_GET['Unit_ID'])) {
    // Log error internally instead of relying only on alert
    error_log("Attempted access to edit_unit.php without login or Unit_ID.");
    
    echo "<script>
        alert('Invalid request or not logged in.');
        window.location.href = 'home.php?tag=unitlist';
    </script>";
    exit();
}

$unit_id = filter_input(INPUT_GET, 'Unit_ID', FILTER_VALIDATE_INT);
if ($unit_id === false || $unit_id <= 0) {
    echo "<script>
        alert('Invalid Unit ID.');
        window.location.href = 'home.php?tag=unitlist';
    </script>";
    exit();
}

$action_by = $_SESSION['username'] ?? 'System_Admin'; // Use a more explicit default
$message = '';

// ------------------------------
// 2️⃣ Fetch unit details using procedure
// ------------------------------
try {
    $stmt = $conn->prepare("CALL Get_Unit_By_ID(?)");
    $stmt->bind_param("i", $unit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $unit = $result->fetch_assoc();
    $stmt->close();
    $conn->next_result(); // Crucial for clearing results after stored proc call

    if (!$unit) {
        throw new Exception("Unit not found for ID: {$unit_id}");
    }
} catch (Exception $e) {
    error_log("Error fetching unit: " . $e->getMessage());
    $error = addslashes("Failed to load unit data.");
    echo "<script>
        alert('Error fetching unit: {$error}');
        window.location.href = 'home.php?tag=unitlist';
    </script>";
    exit();
}

// ------------------------------
// 3️⃣ Handle update form submission
// ------------------------------
if (isset($_POST['update_unit'])) {
    // --- Robust Server-Side Validation & Filtering ---
    $prop_id = filter_input(INPUT_POST, 'Prop_ID', FILTER_VALIDATE_INT);
    $rent = filter_input(INPUT_POST, 'Rent_Amount', FILTER_VALIDATE_FLOAT);
    $deposit = filter_input(INPUT_POST, 'Deposit_Amount', FILTER_VALIDATE_FLOAT);

    $unit_code = trim($_POST['Unit_Code'] ?? '');
    $unit_name = trim($_POST['Unit_Name'] ?? '');
    $status = $_POST['Occupancy_Status'] ?? '';
    $description = trim($_POST['Description'] ?? '');
    
    // Simple check for mandatory strings and filter validation failure
    if ($prop_id === false || $rent === false || $deposit === false || 
        empty($unit_code) || empty($unit_name) || empty($status)) 
    {
        $message = "❌ Invalid or incomplete input provided. Please check all fields.";
    } else {
        // --- Security Improvement: REMOVE the manual $call_preview and addslashes ---
        
        try {
            $stmt = $conn->prepare("CALL UpdateUnit(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "iisddssss",
                $unit_id,
                $prop_id,
                $unit_code,
                $unit_name,
                $rent,
                $deposit,
                $status,
                $description,
                $action_by
            );
            $stmt->execute();
            $stmt->close();
            $conn->next_result(); // Clear the result set

            $message = "✅ Unit updated successfully.";

            // --- IMPROVEMENT: Manually update the $unit array to reflect changes ---
            $unit['Prop_ID'] = $prop_id;
            $unit['Unit_Code'] = $unit_code;
            $unit['Unit_Name'] = $unit_name;
            $unit['Rent_Amount'] = $rent;
            $unit['Deposit_Amount'] = $deposit;
            $unit['Occupancy_Status'] = $status;
            $unit['Description'] = $description;

        } catch (Exception $e) {
            error_log("Unit Update Error (User: {$action_by}): " . $e->getMessage());
            $message = "❌ Error updating unit. Please try again.";
        } 
    }
}

// ------------------------------
// 4️⃣ Fetch all properties for dropdown
// ------------------------------
$properties = [];
$result = $conn->query("CALL Get_All_Properties()");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    $result->close();
}
$conn->next_result(); // Clear the result set
?>

<h3 class="mb-4 text-center text-primary fw-bold">Edit Unit</h3>

<?php if (!empty($message)): ?>
<div class="alert <?= str_contains($message, 'Error') || str_contains($message, '❌') ? 'alert-danger' : 'alert-success' ?> text-center">
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">Unit Details</div>
    <div class="card-body">
        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Property</label>
                    <select name="Prop_ID" class="form-select" required>
                        <?php foreach ($properties as $prop): ?>
                        <option value="<?= $prop['Prop_ID'] ?>" <?= ($unit['Prop_ID'] == $prop['Prop_ID']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($prop['Prop_Code'] . ' - ' . $prop['Prop_Name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Unit Code</label>
                    <input type="text" name="Unit_Code" class="form-control" value="<?= htmlspecialchars($unit['Unit_Code'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Unit Name</label>
                    <input type="text" name="Unit_Name" class="form-control" value="<?= htmlspecialchars($unit['Unit_Name'] ?? '') ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Rent (UGX)</label>
                    <input type="number" step="0.01" name="Rent_Amount" class="form-control" value="<?= htmlspecialchars($unit['Rent_Amount'] ?? '') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Deposit (UGX)</label>
                    <input type="number" step="0.01" name="Deposit_Amount" class="form-control" value="<?= htmlspecialchars($unit['Deposit_Amount'] ?? '') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="Occupancy_Status" class="form-select">
                        <option value="Vacant" <?= (($unit['Occupancy_Status'] ?? '') == 'Vacant') ? 'selected' : '' ?>>Vacant</option>
                        <option value="Occupied" <?= (($unit['Occupancy_Status'] ?? '') == 'Occupied') ? 'selected' : '' ?>>Occupied</option>
                        <option value="Closed" <?= (($unit['Occupancy_Status'] ?? '') == 'Closed') ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="Description" class="form-control" value="<?= htmlspecialchars($unit['Description'] ?? '') ?>">
                </div>
            </div>

            <button type="submit" name="update_unit" class="btn btn-primary">Update Unit</button>
        </form>
    </div>
</div>