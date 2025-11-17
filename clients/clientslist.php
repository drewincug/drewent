<?php
// Include database connection
include('includes/db.php');

// Fetch clients
$sql = "SELECT client_id, client_code, firstname, lastname, gender, contact_number, email, client_type, status 
        FROM clients 
        ORDER BY client_id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Clients List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .container { margin-top: 40px; }
    .table th { background-color: #343a40; color: #fff; }
    .search-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    input[type="text"] {
        width: 300px;
        padding: 6px 10px;
    }
    @media (max-width: 768px) {
      .search-box { flex-direction: column; align-items: flex-start; }
      input[type="text"] { width: 100%; margin-bottom: 10px; }
    }
  </style>
</head>
<body>

<div class="container">
  <div class="search-box">
      <h4 class="fw-bold">📋 List of Clients</h4>
      <input type="text" id="searchInput" class="form-control" placeholder="Search clients...">
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Client Code</th>
          <th>Full Name</th>
          <th>Gender</th>
          <th>Contact</th>
          <th>Email</th>
          <th>Client Type</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="clientTable">
        <?php
        if ($result && $result->num_rows > 0) {
            $i = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$i}</td>
                        <td>{$row['client_code']}</td>
                        <td>{$row['firstname']} {$row['lastname']}</td>
                        <td>{$row['gender']}</td>
                        <td>{$row['contact_number']}</td>
                        <td>{$row['email']}</td>
                        <td class='text-capitalize'>{$row['client_type']}</td>
                        <td><span class='badge bg-" . 
                            ($row['status'] == 'Active' ? 'success' : 'secondary') . "'>{$row['status']}</span></td>
                        <td>
                            <a href='client_view.php?id={$row['client_id']}' class='btn btn-sm btn-info'>View</a>
                            <a href='client_edit.php?id={$row['client_id']}' class='btn btn-sm btn-warning'>Edit</a>
                            <a href='client_delete.php?id={$row['client_id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete this client?\")'>Delete</a>
                        </td>
                      </tr>";
                $i++;
            }
        } else {
            echo "<tr><td colspan='9' class='text-center text-muted'>No clients found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<script>
// 🔍 Client-side table search
document.getElementById('searchInput').addEventListener('keyup', function() {
  var value = this.value.toLowerCase();
  var rows = document.querySelectorAll('#clientTable tr');
  rows.forEach(function(row) {
    row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
  });
});
</script>

</body>
</html>
