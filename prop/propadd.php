<?php
// --- Database Connection ---
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dr_ent');

$message = '';
$property_types = [];
$conn = null;

try {
    // 1️⃣ Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // 2️⃣ Handle form submission to add new property
    if (isset($_POST['add_property'])) {
        $prop_name     = trim($_POST['prop_name'] ?? '');
        $prop_type_id  = trim($_POST['prop_type_id'] ?? '');
        $location      = trim($_POST['location'] ?? '');
        $size          = trim($_POST['size'] ?? '');
        $status        = trim($_POST['status'] ?? '');
        $value         = (float)($_POST['value'] ?? 0);
        $description   = trim($_POST['description'] ?? '');
        $created_by    = trim($_POST['created_by'] ?? 'Admin');

        $stmt = $conn->prepare("CALL AddProperty(?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Bind parameters: s=string, i=integer, d=double
        if (!$stmt->bind_param("sisssdss", $prop_name, $prop_type_id, $location, $size, $status, $value, $description, $created_by)) {
            throw new Exception("Binding parameters failed: " . $stmt->error);
        }

        if ($stmt->execute()) {
            $message = "✅ Property '{$prop_name}' added successfully!";
        } else {
            throw new Exception("Execution failed: " . $stmt->error);
        }

        $stmt->close();
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

} catch (Exception $e) {
    $message = "🔴 An error occurred: " . $e->getMessage();
} finally {
    if ($conn) $conn->close();
}
?>

<div class="card-form">
    <h2 class="mb-4 text-center fw-bold text-dark">Add New Property</h2>
    <hr class="mb-4">

    <?php if ($message): ?>
        <div class="alert <?= strpos($message, '🔴') !== false ? 'alert-danger' : 'alert-success' ?> rounded-3 shadow-sm">
            <?= nl2br($message) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Property Name <span class="text-danger">*</span></label>
                <input type="text" name="prop_name" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Property Type <span class="text-danger">*</span></label>
                <select name="prop_type_id" class="form-select" required>
                    <option value="">-- Select Type --</option>
                    <?php foreach ($property_types as $type): ?>
                        <option value="<?= htmlspecialchars($type['PropType_ID']) ?>">
                            <?= htmlspecialchars($type['PropType_Name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Location</label>
                <input type="text" name="location" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Size</label>
                <input type="text" name="size" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Value (UGX)</label>
                <input type="number" step="0.01" name="value" class="form-control">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Description</label>
            <textarea name="description" class="form-control" rows="4"></textarea>
        </div>

        <input type="hidden" name="created_by" value="Admin">
        <button type="submit" name="add_property" class="btn btn-success w-100 fw-bold">Add Property</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
