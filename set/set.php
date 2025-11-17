<?php
// Define constants for connection details
// NOTE: In a production environment, these should be stored securely (e.g., environment variables)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dr_ent');

$message = '';
$companies = [];
$conn = null;

try {
    // 1. Establish an object-oriented database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check for connection error
    if ($conn->connect_error) {
        throw new Exception("Connection Failed: " . $conn->connect_error);
    }
    
    // 2. Call the Stored Procedure using multi_query to handle multiple potential result sets
    if (!$conn->multi_query("CALL ListAllCompanies()")) {
         throw new Exception("Error executing procedure: " . $conn->error);
    }
    
    // 3. Process the Result Set(s)
    do {
        if ($result = $conn->store_result()) {
            while ($row = $result->fetch_assoc()) {
                $companies[] = $row;
            }
            $result->free(); // Free the memory associated with the result
        }
    } while ($conn->more_results() && $conn->next_result()); // Continue to next result set if available

} catch (Exception $e) {
    // Catch any connection or execution errors
    $message = "Database Error: " . $e->getMessage();
} finally {
    // 4. Ensure the connection is always closed
    if ($conn) {
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Company Directory</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
    .company-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08); /* Increased shadow for lift */
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: none;
    }
    .company-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .company-logo {
        max-height: 60px;
        width: 60px;
        object-fit: cover;
        border-radius: 50%; /* Make logos round for a modern look */
        border: 2px solid #4f46e5;
        background-color: #4f46e5;
    }
    .text-primary { color: #4f46e5 !important; } /* Consistent brand color (Indigo) */
</style>
</head>
<body>
<div class="container py-5">
    <h1 class="text-center text-primary fw-bold mb-5">Company Directory</h1>

    <?php if ($message): ?>
        <div class="alert alert-danger text-center rounded-3 shadow-sm"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!empty($companies)): ?>
        <div class="row g-4 justify-content-center">
            <!-- Set column width to stretch wider on desktop for better readability -->
            <?php foreach ($companies as $company): ?>
                <div class="col-12 col-lg-8"> 
                    <div class="card company-card p-4 h-100">
                        <div class="d-flex align-items-center mb-4">
                            <?php
                            // Use a placeholder image if logo_url is empty
                            $logo = htmlspecialchars($company['logo_url'] ?? '');
                            $placeholder_url = 'https://placehold.co/60x60/4f46e5/ffffff?text=C';
                            $final_logo_url = !empty($logo) ? $logo : $placeholder_url;
                            ?>
                            <img 
                                src="<?= $final_logo_url ?>" 
                                alt="<?= htmlspecialchars($company['brand_name'] ?? 'Company Logo') ?>" 
                                class="company-logo me-4"
                                onerror="this.onerror=null;this.src='<?= $placeholder_url ?>';"
                            >
                            <div>
                                <h4 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($company['brand_name'] ?? $company['legal_name'] ?? 'N/A') ?></h4>
                                <small class="text-muted"><?= htmlspecialchars($company['legal_name'] ?? '') ?></small>
                            </div>
                        </div>

                        <div class="row row-cols-2 g-2 small mb-4 border-top pt-3">
                            <div class="col"><strong>Code:</strong> <?= htmlspecialchars($company['comp_code'] ?? 'N/A') ?></div>
                            <div class="col"><strong>Tax ID:</strong> <?= htmlspecialchars($company['tax_id'] ?? 'N/A') ?></div>
                            <div class="col"><strong>Phone:</strong> <?= htmlspecialchars($company['phone_number'] ?? 'N/A') ?></div>
                            <div class="col"><strong>Email:</strong> <?= htmlspecialchars($company['email'] ?? 'N/A') ?></div>
                            <div class="col-12"><strong>Website:</strong>
                                <?php if (!empty($company['website_url'])): ?>
                                    <a href="<?= htmlspecialchars($company['website_url']) ?>" target="_blank" class="text-primary text-decoration-none"><?= htmlspecialchars($company['website_url']) ?></a>
                                <?php else: ?> N/A <?php endif; ?>
                            </div>
                        </div>

                        <p class="mb-3 border-bottom pb-3">
                            <strong class="text-secondary">Address:</strong><br>
                            <?= htmlspecialchars($company['address_line_1'] ?? 'N/A') ?>
                            <?= !empty($company['address_line_2']) ? ', ' . htmlspecialchars($company['address_line_2']) : '' ?>
                            <br><?= htmlspecialchars(($company['city'] ?? '') . ', ' . ($company['state_region'] ?? '') . ' ' . ($company['zip_code'] ?? '') . ', ' . ($company['country'] ?? '')) ?>
                        </p>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge rounded-pill bg-<?= (($company['status'] ?? '') === 'Active') ? 'success' : 'secondary' ?> py-2 px-3">
                                <?= htmlspecialchars($company['status'] ?? 'Unknown') ?>
                            </span>
                            <div class="d-flex align-items-center">
                                <small class="text-muted me-3">
                                    Added: <?= htmlspecialchars(!empty($company['created_at']) ? date('d M Y', strtotime($company['created_at'])) : 'N/A') ?>
                                </small>
                                <a href="home.php?tag=compedit&&comp_id=<?= (int)($company['comp_id'] ?? 0) ?>" class="btn btn-sm btn-primary rounded-3 px-4 shadow-sm">
                                    Edit Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center text-muted py-5 bg-white rounded-3 shadow-sm">
            <p class="fs-5 mb-1">No company records found.</p>
            <p class="small">Check your database connection and run the `ListAllCompanies` procedure manually if needed.</p>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
