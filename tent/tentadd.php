<?php
include('includes/db.php'); // ensure this defines $conn as a valid mysqli connection

$message = '';
$alert_class = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tent_code        = trim($_POST['tent_code'] ?? '');
    $tent_name        = trim($_POST['tent_name'] ?? '');
    $tent_size        = trim($_POST['tent_size'] ?? '');
    $tent_color       = trim($_POST['tent_color'] ?? '');
    $tent_description = trim($_POST['tent_description'] ?? '');
    $rent_price       = trim($_POST['rent_price'] ?? 0.00);
    $status           = trim($_POST['status'] ?? 'Available');
    $created_by       = $_SESSION['username'] ?? 'Admin';

    if (empty($tent_name)) {
        $message = 'Tent name is required.';
        $alert_class = 'alert-danger';
    } else {
        try {
            // Prepare stored procedure call
            $stmt = $conn->prepare("CALL sp_addTent(?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "sssssdss",
                $tent_code,
                $tent_name,
                $tent_size,
                $tent_color,
                $tent_description,
                $rent_price,
                $status,
                $created_by
            );

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result && $row = $result->fetch_assoc()) {
                    $message = $row['message'] ?? 'Tent added successfully!';
                    $alert_class = 'alert-success';
                } else {
                    $message = 'Tent added successfully!';
                    $alert_class = 'alert-success';
                }
            } else {
                $message = 'Error executing stored procedure: ' . $stmt->error;
                $alert_class = 'alert-danger';
            }

            $stmt->close();
        } catch (Exception $e) {
            $message = '❌ Error adding tent: ' . $e->getMessage();
            $alert_class = 'alert-danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Tent</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container col-md-8">
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
      <h4 class="mb-0">Add New Tent</h4>
    </div>
    <div class="card-body">

      <?php if (!empty($message)): ?>
        <div class="alert <?= htmlspecialchars($alert_class) ?>"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="row mb-3"> 
            <input type="hidden" name="tent_code" class="form-control" placeholder="Auto-generated if blank"> 
          <div class="col-md-3">
            <label class="form-label">Tent Name *</label>
            <input type="text" name="tent_name" class="form-control" required>
          </div> 
          <div class="col-md-2">
            <label class="form-label">Tent Size</label>
            <input type="text" name="tent_size" class="form-control" placeholder="e.g., 4x6m">
          </div>
          <div class="col-md-4">
            <label class="form-label">Color</label>
            <input type="text" name="tent_color" class="form-control" placeholder="e.g., White">
          </div>
          <div class="col-md-3">
            <label class="form-label">Rent Price (UGX)</label>
            <input type="number" name="rent_price" class="form-control" step="0.01" placeholder="0.00">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="tent_description" class="form-control" rows="3" placeholder="Enter tent details..."></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="Available" selected>Available</option>
            <option value="Rented">Rented</option>
            <option value="Under Maintenance">Under Maintenance</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary">➕ Add Tent</button>
      </form>

    </div>
  </div>
</div>

</body>
</html>
