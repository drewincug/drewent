<?php
include('includes/db.php');

$message = '';
$deleted_by = $_SESSION['username'] ?? 'Admin';

// ------------------------------
// 1️⃣ DELETE PROPERTY
// ------------------------------
if (!empty($_GET['delete'])) {
    $id = intval($_GET['delete']);

    try {
        $stmt = $conn->prepare("CALL DeleteProperty(?, ?)");
        $stmt->bind_param("is", $id, $deleted_by);

        if ($stmt->execute()) {
            $message = "✅ Property deleted successfully.";
        } else {
            $message = "❌ Error deleting property: " . $stmt->error;
        }

        $stmt->close();
        $conn->next_result();
    } catch (Exception $e) {
        $message = "❌ Exception: " . $e->getMessage();
    }
}

// ------------------------------
// 2️⃣ FETCH ALL PROPERTIES
// ------------------------------
$properties = [];
try {
    $result = $conn->query("CALL Get_All_Properties()");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $properties[] = $row;
        }
        $result->close();
    }
    $conn->next_result();
} catch (Exception $e) {
    $message = "❌ Failed to fetch properties: " . $e->getMessage();
}
?>

<!-- ✅ Display message -->
<?php if (!empty($message)): ?>
<div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
    <?= htmlspecialchars($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- ✅ Responsive Property Table -->
<div class="tab-content">
    <div class="card shadow-sm mt-3">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i data-lucide="home" class="me-2"></i>Property List</h6>
        </div>
        <div class="card-body p-2">

            <!-- Scrollable container for smaller screens -->
            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                <table class="table table-hover table-bordered align-middle text-nowrap">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Value (UGX)</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($properties)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No properties found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($properties as $row): ?>
                            <tr>
                                <td><?= $row['Prop_ID'] ?></td>
                                <td><?= htmlspecialchars($row['Prop_Code']) ?></td>
                                <td><?= htmlspecialchars($row['Prop_Name']) ?></td>
                                <td><?= htmlspecialchars($row['PropType_Name'] ?? $row['Property_Type']) ?></td>
                                <td><?= htmlspecialchars($row['Location']) ?></td>
                                <td><?= htmlspecialchars($row['Status']) ?></td>
                                <td><?= number_format($row['Value']) ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- View -->
                                        <a href="home.php?tag=propview&prop_id=<?= $row['Prop_ID'] ?>" 
                                           class="btn btn-success" title="View">
                                           <i data-lucide="eye"></i>
                                        </a>
                                        <!-- Edit -->
                                        <a href="home.php?tag=propedit&prop_id=<?= $row['Prop_ID'] ?>" 
                                           class="btn btn-warning" title="Edit">
                                           <i data-lucide="edit"></i>
                                        </a>
                                        <!-- Close -->
                                        <a href="home.php?tag=propclose&Prop_ID=<?= urlencode($row['Prop_ID']) ?>" 
                                           class="btn btn-secondary" 
                                           onclick="return confirm('Are you sure you want to close this property?')" 
                                           title="Close">
                                           <i data-lucide="lock"></i>
                                        </a>
                                        <!-- Delete -->
                                        <a href="home.php?tag=propdelete&Prop_ID=<?= urlencode($row['Prop_ID']) ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this property?')" 
                                           title="Delete">
                                           <i data-lucide="trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Activate Lucide Icons -->
<script>
    lucide.createIcons();
</script>

<!-- ✅ Mobile-friendly scroll improvement -->
<style>
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table th, .table td {
            white-space: nowrap;
        }
    }
</style>
