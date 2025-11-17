<?php
include('includes/db.php');
// --- ADD PROPERTY ---
if (isset($_POST['add_property'])) {
    $stmt = $conn->prepare("CALL AddProperty(?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssssdss",
        $_POST['Property_Code'],
        $_POST['Property_Name'],
        $_POST['Property_Type'],
        $_POST['Location'],
        $_POST['Size'],
        $_POST['Value'],
        $_POST['Description'],
        $_POST['Recorded_By']
    );
    if ($stmt->execute()) {
        $message = "Property added successfully.";
    } else {
        $message = "Error adding property: " . $conn->error;
    }
    $stmt->close();
}

// --- UPDATE PROPERTY ---
if (isset($_POST['update_property'])) {
    $stmt = $conn->prepare("CALL UpdateProperty(?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "issssssds",
        $_POST['Property_ID'],
        $_POST['Property_Name'],
        $_POST['Property_Type'],
        $_POST['Location'],
        $_POST['Size'],
        $_POST['Status'],
        $_POST['Value'],
        $_POST['Description'],
        $_POST['Updated_By']
    );
    if ($stmt->execute()) {
        $message = "Property updated successfully.";
    } else {
        $message = "Error updating property: " . $conn->error;
    }
    $stmt->close();
}

// --- DELETE PROPERTY ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $deleted_by = "Admin";
    $stmt = $conn->prepare("CALL DeleteProperty(?, ?)");
    $stmt->bind_param("is", $id, $deleted_by);
    if ($stmt->execute()) {
        $message = "Property deleted successfully.";
    } else {
        $message = "Error deleting property: " . $conn->error;
    }
    $stmt->close();
}

// --- FETCH PROPERTIES ---
$properties = [];
$result = $conn->query("CALL Get_All_Properties()");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    $result->close();
}
$conn->next_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Drew Enterprises – Property Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .navbar { background-color: #003366; }
    .nav-tabs .nav-link.active { background-color: #003366; color: #fff !important; }
  </style>
</head>

  <div class="container my-4">
    <?php if (!empty($message)): ?>
      <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <!-- Dashboard Graph Area -->
    <div class="card mb-4 shadow-sm">
      <div class="card-header bg-primary text-white">Property Overview</div>
      <div class="card-body">
        <p class="text-muted">Dashboard graph placeholder (to integrate Chart.js later).</p>
      </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-3" id="propertyTabs">
      <li class="nav-item">
        <a class="nav-link <?= isset($_GET['tab']) && $_GET['tab'] == 'list' ? '' : 'active' ?>" href="?tab=add">➕ Add Property</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= isset($_GET['tab']) && $_GET['tab'] == 'list' ? 'active' : '' ?>" href="home.php?tag=prop&&tab=list">📋 Property List</a>
      </li>
    </ul>

    <div class="tab-content">
      <!-- Add Property -->
      <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'add'): ?>
      <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">Add New Property</div>
        <div class="card-body">
          <form method="POST">
            <div class="row mb-3">
              <div class="col-md-4">
                <label class="form-label">Property Code</label>
                <input type="text" name="Property_Code" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Property Name</label>
                <input type="text" name="Property_Name" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Property Type</label>
                <input type="text" name="Property_Type" class="form-control" required>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Location</label>
                <input type="text" name="Location" class="form-control" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Size</label>
                <input type="text" name="Size" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label">Value (UGX)</label>
                <input type="number" step="0.01" name="Value" class="form-control" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="Description" class="form-control" rows="3"></textarea>
            </div>
            <input type="hidden" name="Recorded_By" value="Admin">
            <button type="submit" name="add_property" class="btn btn-primary">Add Property</button>
          </form>
        </div>
      </div>

      <!-- Property List -->
      <?php else: ?>
      <div class="card shadow-sm">
        <div class="card-header bg-info text-white">Property List</div>
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Location</th>
                <th>Status</th>
                <th>Value (UGX)</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($properties)): ?>
                <tr><td colspan="8" class="text-center text-muted">No properties found.</td></tr>
              <?php else: ?>
                <?php foreach ($properties as $row): ?>
                  <tr>
                    <td><?= $row['Property_ID'] ?></td>
                    <td><?= $row['Property_Code'] ?></td>
                    <td><?= $row['Property_Name'] ?></td>
                    <td><?= $row['Property_Type'] ?></td>
                    <td><?= $row['Location'] ?></td>
                    <td><?= $row['Status'] ?></td>
                    <td><?= number_format($row['Value']) ?></td>
                    <td>
                      <a href="?tab=add&edit=<?= $row['Property_ID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                      <a href="?tab=list&delete=<?= $row['Property_ID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this property?')">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
