<?php
// --- FETCH UNITS ---
$units = [];
$message = '';
 
try {
    $result = $conn->query("CALL Get_All_Units()");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $units[] = $row;
        }
        $result->free();
    }

    // Clean up extra stored procedure results
    while ($conn->more_results() && $conn->next_result()) {}
} catch (Exception $e) {
    $message = "Error fetching units: " . htmlspecialchars($e->getMessage());
}
?>

<h3 class="mb-4 text-center text-primary fw-bold">
    <i class="bi bi-building"></i> Unit Management
</h3>

<?php if (!empty($message)): ?>
<div class="alert <?= str_contains($message, 'Error') ? 'alert-danger' : 'alert-info' ?> text-center shadow-sm">
    <?= $message ?>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white fw-bold">
        <i class="bi bi-list-ul me-2"></i> All Units
    </div>
    <div class="card-body p-0">
        <!-- Responsive scrollable table container -->
        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
            <table class="table table-striped table-bordered align-middle text-center mb-0">
                <thead class="table-dark position-sticky top-0" style="z-index: 1;">
                    <tr>
                        <th>#</th>
                        <th>Property ID</th>
                        <th>Unit Code</th>
                        <th>Unit Name</th>
                        <th>Rent (UGX)</th>
                        <th>Deposit (UGX)</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($units)): ?>
                        <?php foreach ($units as $i => $unit): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($unit['Prop_ID']) ?></td>
                                <td><?= htmlspecialchars($unit['Unit_Code']) ?></td>
                                <td><?= htmlspecialchars($unit['Unit_Name']) ?></td>
                                <td><?= number_format($unit['Rent_Amount'], 0) ?></td>
                                <td><?= number_format($unit['Deposit_Amount'], 0) ?></td>
                                <td>
                                    <?php
                                    $status = strtolower($unit['Occupancy_Status']);
                                    if ($status === 'occupied') echo '<span class="badge bg-success">Occupied</span>';
                                    elseif ($status === 'closed') echo '<span class="badge bg-danger">Closed</span>';
                                    else echo '<span class="badge bg-secondary">Vacant</span>';
                                    ?>
                                </td>
                                <td class="text-start"><?= htmlspecialchars($unit['Description'] ?? '-') ?></td>
                                <td class="d-flex justify-content-center gap-1 flex-wrap">
                                    <a href="home.php?tag=unitview&Unit_ID=<?= $unit['Unit_ID'] ?>" 
                                       class="btn btn-sm btn-success" title="View Unit">
                                       <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="home.php?tag=unitedit&Unit_ID=<?= $unit['Unit_ID'] ?>" 
                                       class="btn btn-sm btn-warning" title="Edit Unit">
                                       <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="home.php?tag=unitclose&Unit_ID=<?= $unit['Unit_ID'] ?>" 
                                       class="btn btn-sm btn-secondary" 
                                       onclick="return confirm('Are you sure you want to close this unit?');"
                                       title="Close Unit">
                                       <i data-lucide="lock"></i>
                                    </a>
                                    <a href="home.php?tag=unitdelete&Unit_ID=<?= $unit['Unit_ID'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to close this unit?');"
                                       title="Delete Unit">
                                       <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted">No units found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> <!-- /.table-responsive -->
    </div> <!-- /.card-body -->
</div> <!-- /.card -->
