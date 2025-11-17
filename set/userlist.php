<?php
include('includes/db.php');

// --- FETCH USERS ---
$users = [];
$result = $conn->query("CALL Get_All_users()");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->close();
}
$conn->next_result();
?>

<?php if (!empty($message)): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="tab-content">
  <div class="card shadow-sm">
    <div class="card-header bg-info text-white">User List</div>
    <div class="card-body">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Full Name</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr>
              <td colspan="7" class="text-center text-muted">No users found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($users as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['User_ID']) ?></td>
                <td><?= htmlspecialchars($row['Username']) ?></td>
                <td><?= htmlspecialchars($row['Full_Name']) ?></td>
                <td><?= htmlspecialchars($row['Role_ID']) ?></td>
                <td><?= htmlspecialchars($row['Status']) ?></td>
                <td><?= htmlspecialchars($row['Created_At']) ?></td>
                <td>
                  <a href="?tab=edit_user&id=<?= $row['User_ID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                  <a href="?tab=list_users&delete=<?= $row['User_ID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
