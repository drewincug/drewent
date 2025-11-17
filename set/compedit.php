<?php
include('includes/db.php'); // $conn = new mysqli(...)

// Initialize variables
$message = '';
$company = [];
$comp_id = $_GET['comp_id'] ?? '';

// --- Fetch Company Data ---
if ($comp_id) {
    $stmt = $conn->prepare("SELECT * FROM Companies WHERE comp_id = ?");
    $stmt->bind_param("i", $comp_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $company = $result->fetch_assoc() ?: [];
    $stmt->close();
}

// --- Handle Form Submission ---
if (isset($_POST['update_company'])) {

    // Handle logo upload
    $logo_url = $company['logo_url'] ?? '';
    $uploadDir = 'uploads/logos/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $fileName = time() . '_' . basename($_FILES['logo']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
            $logo_url = $targetFile;
        } else {
            $message = "Failed to upload logo. Check folder permissions.";
        }
    }

    // Only call SP if no upload error
    if (empty($message)) {
        $stmt = $conn->prepare("CALL UpdateCompany(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $updated_at = date('Y-m-d H:i:s');
        "string";   $updated_by = 'Admin'; // replace with session username if available

        $stmt->bind_param(
            "sssssssssssssssss",
            $_POST['comp_code'],
            $_POST['legal_name'],
            $_POST['brand_name'],
            $_POST['tax_id'],
            $_POST['address_line_1'],
            $_POST['address_line_2'],
            $_POST['city'],
            $_POST['state_region'],
            $_POST['country'],
            $_POST['zip_code'],
            $_POST['phone_number'],
            $_POST['email'],
            $_POST['website_url'],
            $logo_url,
            $_POST['status'],
            $updated_by,
            $updated_at
        );

        if ($stmt->execute()) {
            $message = "Company updated successfully!";
        } else {
            $message = "Error updating company: " . $conn->error;
        }

        $stmt->close();
        $conn->next_result();

        // Refresh company data
        $stmt = $conn->prepare("SELECT * FROM Companies WHERE comp_code = ?");
        $stmt->bind_param("s", $_POST['comp_code']);
        $stmt->execute();
        $company = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Company</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .logo-preview { max-height: 100px; margin-top: 10px; }
</style>
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Update Company</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!empty($company)): ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="comp_code" value="<?= htmlspecialchars($company['comp_code']) ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Legal Name</label>
                <input type="text" name="legal_name" class="form-control" value="<?= htmlspecialchars($company['legal_name']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Brand Name</label>
                <input type="text" name="brand_name" class="form-control" value="<?= htmlspecialchars($company['brand_name']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Tax ID</label>
                <input type="text" name="tax_id" class="form-control" value="<?= htmlspecialchars($company['tax_id']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($company['phone_number']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($company['email']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Website</label>
                <input type="url" name="website_url" class="form-control" value="<?= htmlspecialchars($company['website_url']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="Active" <?= $company['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= $company['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Logo</label>
                <input type="file" name="logo" class="form-control">
                <?php if (!empty($company['logo_url'])): ?>
                    <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="Logo" class="logo-preview">
                <?php endif; ?>
            </div>
        </div>

        <input type="hidden" name="updated_by" value="Admin">
        <button type="submit" name="update_company" class="btn btn-primary mt-3">Update Company</button>
    </form>
    <?php else: ?>
        <div class="alert alert-warning">Company not found.</div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
