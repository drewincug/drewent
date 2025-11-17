<?php
// ============================================
// Company Registration via Stored Procedure
// ============================================
include('includes/db.php'); // Defines $servername, $username, $password, $dbname

$message = '';
$alert_type = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- Sanitize Inputs ---
    $fields = [
        'legal_name', 'brand_name', 'tax_id', 'address_line_1', 'address_line_2',
        'city', 'state_region', 'country', 'zip_code', 'phone_number',
        'email', 'website_url', 'logo_url', 'status', 'action_by', 'remarks'
    ];
    foreach ($fields as $f) {
        $$f = htmlspecialchars(trim($_POST[$f] ?? ''), ENT_QUOTES);
    }

    // Default Values
    $country   = $country ?: 'Uganda';
    $status    = $status ?: 'Active';
    $action_by = $action_by ?: 'WebFormUser';
    $remarks   = $remarks ?: 'Added via PHP web form.';

    // --- Database Connection ---
    $conn = new mysqli('localhost', 'root', '', 'dr_ent');
    if ($conn->connect_error) {
        $alert_type = 'danger';
        $message = "Database connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("CALL AddCompany(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param(
                "ssssssssssssssss",
                $legal_name, $brand_name, $tax_id,
                $address_line_1, $address_line_2, $city, $state_region, $country,
                $zip_code, $phone_number, $email, $website_url, $logo_url,
                $status, $action_by, $remarks
            );

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result && $row = $result->fetch_assoc()) {
                    $alert_type = 'success';
                    $message = "✅ " . htmlspecialchars($row['Message']) . 
                               " <br><strong>Generated Code:</strong> " . htmlspecialchars($row['Generated_Code']);
                } else {
                    $alert_type = 'success';
                    $message = "✅ Company added successfully.";
                }
                if ($result) $result->free();
                while ($conn->more_results() && $conn->next_result()) { /* clear extra results */ }
            } else {
                $alert_type = 'danger';
                $message = "❌ Failed to execute stored procedure: " . htmlspecialchars($stmt->error);
            }

            $stmt->close();
        } else {
            $alert_type = 'danger';
            $message = "❌ Unable to prepare stored procedure: " . htmlspecialchars($conn->error);
        }
        $conn->close();
    }
}
?>

<!-- =========================
     HTML FORM INTERFACE
========================= -->
<div class="container-fluid py-4">
    <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5" style="max-height: 85vh; overflow-y: auto;">
        <h1 class="h3 fw-bold text-center mb-4 text-primary">
            <i class="bi bi-building me-2"></i> Register New Company
        </h1>

        <!-- Feedback Message -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" class="row g-4">

            <!-- General Info -->
            <h2 class="fs-5 fw-semibold text-secondary border-bottom pb-2 mt-4">
                <i class="bi bi-info-circle me-2"></i> General Information
            </h2>

            <div class="col-md-3">
                <label for="legal_name" class="form-label">Legal Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="legal_name" name="legal_name" required>
            </div>

            <div class="col-md-3">
                <label for="brand_name" class="form-label">Brand / Display Name</label>
                <input type="text" class="form-control form-control-sm" id="brand_name" name="brand_name">
            </div>

            <div class="col-md-2">
                <label for="tax_id" class="form-label">Tax ID / TIN</label>
                <input type="text" class="form-control form-control-sm" id="tax_id" name="tax_id">
            </div>

            <div class="col-md-2">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control form-control-sm" id="phone_number" name="phone_number">
            </div>

            <div class="col-md-2">
                <label for="logo_url" class="form-label">Logo URL</label>
                <input type="url" class="form-control form-control-sm" id="logo_url" name="logo_url" placeholder="https://...">
                <input type="hidden" name="status" value="Active">
            </div>

            <!-- Address Section -->
            <h2 class="fs-5 fw-semibold text-secondary border-bottom pb-2 mt-5">
                <i class="bi bi-geo-alt me-2"></i> Address Details
            </h2>

            <div class="col-md-3">
                <label for="address_line_1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="address_line_1" name="address_line_1" required>
            </div>

            <div class="col-md-3">
                <label for="address_line_2" class="form-label">Address Line 2</label>
                <input type="text" class="form-control form-control-sm" id="address_line_2" name="address_line_2">
            </div>

            <div class="col-md-2">
                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="city" name="city" required>
            </div>

            <div class="col-md-2">
                <label for="state_region" class="form-label">State / Region <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="state_region" name="state_region" required>
            </div>

            <div class="col-md-1">
                <label for="zip_code" class="form-label">ZIP</label>
                <input type="text" class="form-control form-control-sm" id="zip_code" name="zip_code">
            </div>

            <div class="col-md-1">
                <label for="country" class="form-label">Country</label>
                <input type="text" class="form-control form-control-sm" id="country" name="country" value="Uganda">
            </div>

            <!-- Audit Section -->
            <h2 class="fs-5 fw-semibold text-secondary border-bottom pb-2 mt-5">
                <i class="bi bi-journal-check me-2"></i> Audit Details
            </h2>

            <div class="col-12">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea class="form-control form-control-sm" id="remarks" name="remarks" rows="3">Initial company creation via web form.</textarea>
                <div class="form-text">Provide a brief description or note for this entry.</div>
                <input type="hidden" id="action_by" name="action_by" value="WebFormUser">
            </div>

            <!-- Submit -->
            <div class="col-12 pt-4">
                <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 shadow-sm">
                    <i class="bi bi-check-circle me-2"></i> Register Company & Log Audit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
