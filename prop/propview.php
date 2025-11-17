<?php
include('includes/db.php'); // $conn is your mysqli connection

$prop_id = $_GET['prop_id'] ?? 0; // get property id from URL
$property = [];
$units = [];
$message = '';

// --- Fetch Property Data ---
if ($prop_id) {
    $stmt = $conn->prepare("CALL Get_Property_By_ID(?)");
    $stmt->bind_param("i", $prop_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $property = $result->fetch_assoc() ?: [];
    $stmt->close();
    $conn->next_result();

    // --- Fetch Units for this property ---
    if (!empty($property)) {
        $stmt2 = $conn->prepare("SELECT Unit_ID, Unit_Code, Unit_Name, Rent_Amount, Occupancy_Status FROM Units WHERE Property_ID = ?");
        $stmt2->bind_param("i", $prop_id);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        while ($row = $res2->fetch_assoc()) {
            $units[] = $row;
        }
        $stmt2->close();
    } else {
        $message = "Property not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Property Details</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .property-card { border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    .property-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
</style>
</head>
<body>
<div class="container py-5">

    <?php if ($message): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>


            <h2 class="text-primary fw-bold">Property View </h3>

    <?php if (!empty($property)): ?>
        <div class="card property-card p-4 mb-4">
            <h3 class="text-primary fw-bold"><?= htmlspecialchars($property['Prop_Name']) ?> (<?= htmlspecialchars($property['Prop_Code']) ?>)</h3>
            <p><strong>Type:</strong> <?= htmlspecialchars($property['Property_Type']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($property['Location']) ?></p>
            <p><strong>Size:</strong> <?= htmlspecialchars($property['Size']) ?></p>
            <p><strong>Status:</strong> <span class="badge bg-<?= $property['Status'] === 'Active' ? 'success' : 'secondary' ?>"><?= htmlspecialchars($property['Status']) ?></span></p>
            <p><strong>Value:</strong> <?= number_format($property['Value']) ?> UGX</p>
            <p><strong>Description:</strong> <?= htmlspecialchars($property['Description']) ?></p>
            <p><small>Created By: <?= htmlspecialchars($property['Created_By']) ?> on <?= htmlspecialchars($property['Created_On']) ?></small></p>
        </div>

        <h5 class="mb-3">Units in this Property</h5>
        <?php if (!empty($units)): ?>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Rent Amount (UGX)</th>
                        <th>Occupancy Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($units as $unit): ?>
                        <tr>
                            <td><?= $unit['Unit_ID'] ?></td>
                            <td><?= htmlspecialchars($unit['Unit_Code']) ?></td>
                            <td><?= htmlspecialchars($unit['Unit_Name']) ?></td>
                            <td><?= number_format($unit['Rent_Amount']) ?></td>
                            <td><?= htmlspecialchars($unit['Occupancy_Status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No units attached to this property.</div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
