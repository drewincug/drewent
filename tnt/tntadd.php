<?php
include 'db_connect.php'; // ensure $conn is defined (mysqli object)
 
// ======================= FETCH AVAILABLE UNITS =======================
$units = []; 
try {
    $query = "
        SELECT 
            u.Unit_ID, 
            u.Property_ID,
            p.Property_Name,
            u.Unit_Code,   
            u.Unit_Name,   
            u.Rent_Amount,    
            u.Deposit_Amount,  
            u.Occupancy_Status,   
            u.Description
        FROM Units u
        LEFT JOIN Properties p ON p.Property_ID = u.Property_ID
        ORDER BY p.Property_Name ASC, u.Unit_Name ASC";
    
    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $units[] = $row;
        }
    } else {
        traceError("Failed to fetch units: " . $conn->error, $conn);
    }
} catch (Exception $e) {
    traceError("Exception fetching units: " . $e->getMessage(), $conn);
}

// ======================= FORM HANDLER: ADD TENANT =======================
$message = ''; 

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_tenant'])) {
    try {
        $lease_id      = filter_input(INPUT_POST, 'Lease_ID', FILTER_VALIDATE_INT);
        $tenant_name   = trim($_POST['Tenant_Name']);
        $contact_number = trim($_POST['Contact_Number']);
        $email         = filter_input(INPUT_POST, 'Email', FILTER_VALIDATE_EMAIL) ?: null;
        $id_number     = trim($_POST['ID_Number']);
        $recorded_by   = $_POST['Recorded_By'] ?? 'System';

        if (!$lease_id || !$tenant_name || !$contact_number || !$id_number) {
            $message = "❌ Validation Error: Please fill all required fields.";
        } else {
            $stmt = $conn->prepare("CALL AddTenant(?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Preparation failed: " . $conn->error);
            }

            $stmt->bind_param("isssss", $lease_id, $tenant_name, $contact_number, $email, $id_number, $recorded_by);

            if ($stmt->execute()) {
                $message = "✅ Tenant **" . htmlspecialchars($tenant_name) . "** added successfully for Unit ID: **{$lease_id}**.";
                $_POST = []; // Clear POST data
            } else {
                $message = "❌ Error adding tenant. Check logs for details.";
                traceError("Execution failed for AddTenant: " . $stmt->error, $conn, $stmt);
            }

            $stmt->close();
        }
    } catch (Exception $e) {
        $message = "❌ Fatal error during tenant addition.";
        traceError("Exception during tenant addition: " . $e->getMessage(), $conn);
    }
}
?>

<!-- ======================= TENANT FORM ======================= -->

<h3 class="mb-4 text-center text-primary">🏢 Tenant Management</h3>

<?php if (!empty($message)): 
    $alert_class = (strpos($message, '✅') !== false) ? 'alert-success' : 'alert-danger';
?>
<div class="alert <?= $alert_class ?> text-center" role="alert"><?= $message ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white fw-bold">Add New Tenant</div>
    <div class="card-body">
        <form method="POST" novalidate>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Select Unit / Property *</label>
                    <select name="Lease_ID" class="form-select" required>
                        <option value="">-- Select Unit --</option> 
                        <?php foreach ($units as $unit): ?>
                            <option value="<?= htmlspecialchars($unit['Unit_ID']) ?>">
                                <?= htmlspecialchars($unit['Property_Name'] ?? 'Unknown Property') ?> → 
                                <?= htmlspecialchars($unit['Unit_Code']) ?> - <?= htmlspecialchars($unit['Unit_Name']) ?>
                            </option>
                        <?php endforeach; ?>  
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tenant Full Name *</label>
                    <input type="text" name="Tenant_Name" class="form-control" required 
                           value="<?= htmlspecialchars($_POST['Tenant_Name'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Contact Number *</label>
                    <input type="text" name="Contact_Number" class="form-control" required 
                           value="<?= htmlspecialchars($_POST['Contact_Number'] ?? '') ?>">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="Email" class="form-control" 
                           value="<?= htmlspecialchars($_POST['Email'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">ID Number *</label>
                    <input type="text" name="ID_Number" class="form-control" required 
                           value="<?= htmlspecialchars($_POST['ID_Number'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <input type="hidden" name="Recorded_By" value="Admin">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="submit" name="add_tenant" class="btn btn-success w-100 py-2">
                        💾 Submit New Tenant
                    </button>
                </div>
            </div>
            <small class="text-muted">* Required Fields</small>
        </form>
    </div>
</div>
