<?php
include('includes/db.php'); // $conn must be defined

$tents = [];
$message = '';

try {
    $stmt = $conn->prepare("CALL Get_All_Tents()");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tents[] = $row;
    }
    $stmt->close();
    $conn->next_result();
} catch (Exception $e) {
    $message = "❌ Error fetching tents: " . $e->getMessage();
}
?>

<h3 class="mb-4 text-center text-primary fw-bold">Tents Management</h3>

<?php if (!empty($message)): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
        All Tents
    </div>
    <div class="card-body">
        <div class="table-responsive" style="max-height:70vh; overflow-y:auto;">
            <table class="table table-bordered table-striped table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Tent Code</th>
                        <th>Name</th>
                        <th>Type / Size</th>
                        <th>Color</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Rent Price (UGX)</th>
                        <th>Current Value (UGX)</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tents)): ?>
                        <?php foreach ($tents as $i => $tent): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($tent['Tent_Code'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($tent['Tent_Name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($tent['Tent_Type'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($tent['Color'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($tent['Capacity'] ?? 0) ?></td>
                                <td>
                                    <?php
                                        $status = strtolower($tent['Condition_Status'] ?? '');
                                        if ($status === 'available') {
                                            echo '<span class="badge bg-success">Available</span>';
                                        } elseif ($status === 'rented') {
                                            echo '<span class="badge bg-warning">Rented</span>';
                                        } elseif ($status === 'under maintenance') {
                                            echo '<span class="badge bg-danger">Under Maintenance</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">' . htmlspecialchars($tent['Condition_Status']) . '</span>';
                                        }
                                    ?>
                                </td>
                                <td><?= number_format($tent['Rent_Price'] ?? 0, 2) ?></td>
                                <td><?= number_format($tent['Current_Value'] ?? 0, 2) ?></td>
                                <td><?= htmlspecialchars($tent['Notes'] ?? '-') ?></td>
                                <td>
                                    <a href="tent_edit.php?Tent_ID=<?= $tent['Tent_ID'] ?>" class="btn btn-sm btn-warning mb-1">Edit</a>
                                    <a href="tent_delete.php?Tent_ID=<?= $tent['Tent_ID'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Delete this tent?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">No tents found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
