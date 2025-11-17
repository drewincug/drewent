<?php 
require_once 'includes/db.php';

// Fetch properties via procedure (assume you have sp_GetAllProperties)
$properties = [];
$result = $conn->query("CALL Get_All_Properties()");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    $result->free();
    $conn->next_result(); // Clear procedure results
}

// Handle form submission
if (isset($_POST['add_unit'])) {
    $property_id = intval($_POST['Property_ID']);
    $unit_name = trim($_POST['Unit_Name']);
    $rent = floatval($_POST['Rent_Amount']);
    $deposit = floatval($_POST['Deposit_Amount']);
    $description = trim($_POST['Description']);
    $recorded_by = $_SESSION['username'] ?? 'Admin';

    try {
        $stmt = $conn->prepare("CALL AddUnit(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isddss", $property_id, $unit_name, $rent, $deposit, $description, $recorded_by);

        if ($stmt->execute()) {
            $message = "✅ Unit <strong>" . htmlspecialchars($unit_name) . "</strong> added successfully.";
        } else {
            $message = "❌ Error adding unit: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
        $conn->next_result(); // Clear leftover results
    } catch (mysqli_sql_exception $e) {
        $message = "❌ Exception: " . htmlspecialchars($e->getMessage());
    }
}
?>

<h3 class="mb-4 text-center text-primary fw-bold">Unit Management</h3>

<?php if (!empty($message)): ?>
<div class="alert <?= str_contains($message, '❌') ? 'alert-danger' : 'alert-success' ?> text-center shadow-sm">
    <?= $message ?>
</div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-secondary text-white fw-bold">Add New Unit</div>
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Select Property</label>
                    <select name="Property_ID" class="form-select" required>
                        <option value="">-- Select Property --</option>
                        <?php foreach ($properties as $prop): ?>
                            <option value="<?= htmlspecialchars($prop['Prop_ID']) ?>">
                                <?= htmlspecialchars($prop['Prop_Code']) ?> - <?= htmlspecialchars($prop['Prop_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select a property.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Unit Name</label>
                    <input type="text" name="Unit_Name" class="form-control" required>
                    <div class="invalid-feedback">Unit name is required.</div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Rent Amount (UGX)</label>
                    <input type="number" step="0.01" name="Rent_Amount" class="form-control" required>
                    <div class="invalid-feedback">Rent amount is required.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Deposit Amount (UGX)</label>
                    <input type="number" step="0.01" name="Deposit_Amount" class="form-control" required>
                    <div class="invalid-feedback">Deposit amount is required.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <textarea name="Description" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <button type="submit" name="add_unit" class="btn btn-primary">Add Unit</button>
        </form>
    </div>
</div>

<script>
// Bootstrap 5 client-side validation
(() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})();
</script>
