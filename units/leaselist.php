<?php
// Ensure session is started if needed
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Assuming 'includes/db.php' initializes the $conn object as a MySQLi connection
include('includes/db.php'); 

$leases = [];
$error_message = null;

try {
    // 1. Prepare and Execute the stored procedure call (MySQLi method)
    // No parameters needed, so prepare is still fine, though query() might also work.
    $stmt = $conn->prepare("CALL Get_All_Leases()");
    $stmt->execute();
    
    // 2. Get the result set from the executed statement (MySQLi method)
    $result = $stmt->get_result();
    
    // 3. Fetch all results from the result set (MySQLi method)
    // fetch_all(MYSQLI_ASSOC) is the equivalent of PDO::FETCH_ASSOC
    $leases = $result->fetch_all(MYSQLI_ASSOC);
    
    // 4. Close the statement
    $stmt->close();
    
    // 5. Crucial: Clear the result set buffer for subsequent queries/calls
    $conn->next_result(); 

} catch (mysqli_sql_exception $e) { // Catch MySQLi specific exception
    // Log the detailed error internally
    error_log("MySQLi Error fetching leases: " . $e->getMessage());
    
    // Present a user-friendly error message via alert
    $safe_error = json_encode("Error fetching leases: Database query failed.");

    echo "<script>
        alert(" . $safe_error . ");
        window.location.href = 'home.php?tag=leaselist'; 
    </script>";
    exit(); 
}
?>

<div class="  mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">Lease Records 📋</h4>
        <a href="home.php?tag=leaseadd" class="btn btn-success">➕ Add New Lease</a>
    </div>

    <div class="table-responsive shadow-lg rounded">
        <table id="leaseTable" class="table table-bordered table-striped table-hover align-middle caption-top">
            <caption>Total Leases: <?= count($leases) ?></caption>
            <thead class="bg-primary text-white">
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Client</th>
                    <th>Property / Unit</th> <th>Rent / Deposit (UGX)</th> <th>Start Date</th>
                    <th>End Date</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($leases)): ?>
                    <?php $i = 1; foreach ($leases as $lease): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($lease['Lease_Code']) ?></td>
                            <td><?= htmlspecialchars($lease['Client_Name']) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($lease['Prop_Name']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($lease['Unit_Name']) ?></small>
                            </td>
                            <td>
                                R: <?= number_format($lease['Rent_Amount']) ?><br>
                                D: <small class="text-secondary"><?= number_format($lease['Deposit_Amount']) ?></small>
                            </td>
                            <td><?= date('d M Y', strtotime($lease['Lease_Start'])) ?></td>
                            <td><?= date('d M Y', strtotime($lease['Lease_End'])) ?></td>
                            
                            <td>
                                <span class="badge bg-<?= $lease['Payment_Status'] == 'Paid' ? 'success' : 'warning' ?> text-uppercase">
                                    <?= htmlspecialchars($lease['Payment_Status']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $lease['Lease_Status'] == 'Active' ? 'info' : 'secondary' ?> text-uppercase">
                                    <?= htmlspecialchars($lease['Lease_Status']) ?>
                                </span>
                            </td>

                            <td><small><?= date('Y-m-d', strtotime($lease['Created_At'])) ?></small></td>
                            
                            <td class="text-center">
                                <a href="home.php?tag=leaseedit&id=<?= $lease['Lease_ID'] ?>" class="btn btn-sm btn-outline-primary" title="Edit Lease">
                                    ✏️
                                </a>
                                <a href="lease_delete.php?id=<?= $lease['Lease_ID'] ?>" class="btn btn-sm btn-outline-danger" 
                                    onclick="return confirm('WARNING! Are you sure you want to delete Lease <?= htmlspecialchars($lease['Lease_Code']) ?>? This action cannot be undone.');" title="Delete Lease">
                                    🗑️
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" class="text-center text-danger py-4">
                            🚫 **No leases found in the system.** Start by adding a new lease.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>