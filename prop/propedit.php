<?php
// --- Database Connection ---
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dr_ent');

$message = '';
$property = [];
$property_types = [];
$conn = null;

// Determine property ID (GET for initial load, POST after update)
$prop_id = $_GET['prop_id'] ?? null;

try {
    // 1️⃣ Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // 2️⃣ Handle property update
    if (isset($_POST['update_property'])) {
        $prop_id = $_POST['prop_id'] ?? $prop_id;
        $prop_id_clean = (int)$prop_id;

        $prop_name     = trim($_POST['prop_name'] ?? '');
        $prop_type_id  = trim($_POST['prop_type_id'] ?? '');
        $location      = trim($_POST['location'] ?? '');
        $size          = trim($_POST['size'] ?? '');
        $status        = trim($_POST['status'] ?? '');
        $value         = (float)($_POST['value'] ?? 0);
        $description   = trim($_POST['description'] ?? '');
        $updated_by    = trim($_POST['updated_by'] ?? 'Admin');

        $stmt = $conn->prepare("CALL UpdateProperty(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Correct bind_param: i=integer, s=string, d=double
        if (!$stmt->bind_param("isssssdss", $prop_id_clean, $prop_name, $prop_type_id, $location, $size, $status, $value, $description, $updated_by)) {
            throw new Exception("Binding parameters failed: " . $stmt->error);
        }

        if ($stmt->execute()) {
            $message = "✅ Property '{$prop_name}' updated successfully!";
        } else {
            throw new Exception("Execution failed: " . $stmt->error);
        }
        $stmt->close();

        // Clear extra result sets
        while ($conn->more_results() && $conn->next_result()) {;}
    }

    // 3️⃣ Fetch property types for dropdown
    $result_types = $conn->query("SELECT PropType_ID, PropType_Name FROM PropertyTypes ORDER BY PropType_Name ASC");
    if ($result_types) {
        while ($row = $result_types->fetch_assoc()) {
            $property_types[] = $row;
        }
        $result_types->free();
    }
    while ($conn->more_results() && $conn->next_result()) {;}

    // 4️⃣ Fetch property details
    if ($prop_id) {
        $stmt = $conn->prepare("CALL Get_Property_By_ID(?)");
        $stmt->bind_param("i", $prop_id);
        $stmt->execute();
        $result_prop = $stmt->get_result();
        $property = $result_prop->fetch_assoc() ?: [];
        $stmt->close();

        while ($conn->more_results() && $conn->next_result()) {;}
    }

} catch (Exception $e) {
    $message = "🔴 An error occurred: " . $e->getMessage();
} finally {
    if ($conn) $conn->close();
}
?>
<div class="container-fluid" style="background: ;">
<div class="card-form">
    <h2 class="mb-4 text-center fw-bold text-dark">
        Edit Property
        <?php if (!empty($property)): ?>
            <span class="text-secondary small d-block mt-1"><?= htmlspecialchars($property['Prop_Name'] ?? 'Details') ?></span>
        <?php endif; ?>
    </h2>
    <hr class="mb-4">

    <?php if ($message): ?>
        <div class="alert <?= strpos($message, '🔴') !== false ? 'alert-danger' : 'alert-success' ?> rounded-3 shadow-sm">
            <?= nl2br($message) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($property)): ?>
        <form method="post">
            <input type="hidden" name="prop_id" value="<?= htmlspecialchars($property['Prop_ID'] ?? '') ?>">

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Property Name <span class="text-danger">*</span></label>
                    <input type="text" name="prop_name" class="form-control" value="<?= htmlspecialchars($property['Prop_Name'] ?? '') ?>" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Property Type <span class="text-danger">*</span></label>
                    <select name="prop_type_id" class="form-select" required>
                        <option value="">-- Select Type --</option>
                        <?php foreach ($property_types as $type): ?>
                            <option value="<?= htmlspecialchars($type['PropType_ID']) ?>" <?= ($type['PropType_ID'] == ($property['PropType_ID'] ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['PropType_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Location</label>
                    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($property['Location'] ?? '') ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Size</label>
                    <input type="text" name="size" class="form-control" value="<?= htmlspecialchars($property['Size'] ?? '') ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="Active" <?= (($property['Status'] ?? '') === 'Active') ? 'selected' : '' ?>>Active</option>
                        <option value="Inactive" <?= (($property['Status'] ?? '') === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Value (UGX)</label>
                    <input type="number" step="0.01" name="value" class="form-control" value="<?= htmlspecialchars($property['Value'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($property['Description'] ?? '') ?></textarea>
            </div>

            <input type="hidden" name="updated_by" value="Admin">
            <button type="submit" name="update_property" class="btn btn-primary w-100 fw-bold">Save Changes</button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning text-center rounded-3 shadow-sm">
            <h5 class="alert-heading">Property Not Found</h5>
            <p class="mb-0">Please ensure a valid `prop_id` is passed in the URL (e.g., `?prop_id=1`).</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($property)): ?>
        <div class="mt-4 pt-3 border-top text-muted small">
            Last updated: <?= htmlspecialchars($property['Updated_At'] ?? 'N/A') ?> by <?= htmlspecialchars($property['Updated_By'] ?? 'Unknown') ?>
        </div>
    <?php endif; ?>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
