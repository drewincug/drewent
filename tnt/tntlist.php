<?php
include 'db_connect.php'; // Make sure this defines $conn

 

// ======================= FETCH ALL LEASES =======================
$leases = [];
try {
    $result = $conn->query("SELECT * FROM Leases ORDER BY Lease_ID ASC");
    while ($row = $result->fetch_assoc()) {
        $leases[] = $row;
    }
} catch (Exception $e) {
    traceError("Error loading leases: " . $e->getMessage(), $conn);
}

   // Tenant_ID   Lease_ID    Tenant_Name     Contact_Number  Email   ID_Number   Tenant_Status   Created_At  

   // Lease_ID    Lease_Code  Client_ID   Unit_ID     Lease_Start     Lease_End   Rent_Amount     Deposit_Amount  Payment_Status  Lease_Status    Created_At  
// ======================= ADD TENANT FORM HANDLER =======================
if (isset($_POST['add_tenant'])) {
    try {
        $lease_id = (int)$_POST['Lease_ID'];
        $tenant_name = $_POST['Tenant_Name'];
        $contact_number = $_POST['Contact_Number'];
        $email = $_POST['Email'];
        $id_number = $_POST['ID_Number'];
        $recorded_by = $_POST['Recorded_By'];

        $stmt = $conn->prepare("CALL AddTenant(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $lease_id, $tenant_name, $contact_number, $email, $id_number, $recorded_by);

        if ($stmt->execute()) {
            $message = "✅ Tenant added successfully.";
        } else {
            $message = "❌ Error adding tenant.";
            traceError($message, $conn, $stmt);
        }

        $stmt->close();
    } catch (Exception $e) {
        traceError("Exception during tenant addition: " . $e->getMessage(), $conn);
    }
}

// ======================= FETCH ALL TENANTS =======================
$tenants = [];
try {
    $stmt = $conn->prepare("CALL Get_All_Tenants()");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tenants[] = $row;
    }
    $stmt->close();
    $conn->next_result(); // Clear buffer for next query
} catch (Exception $e) {
    traceError("Error fetching tenants: " . $e->getMessage(), $conn);
}
?>

<!-- ======================= FRONT-END SECTION ======================= -->

<h3 class="mb-4 text-center text-primary">Tenant Management</h3>

<?php if (isset($message)): ?>
<div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- TENANT LIST TABLE -->
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">All Tenants</div>
    <div class="card-body">
        <?php if (!empty($tenants)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tenant Name</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>ID Number</th>
                            <th>Lease ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tenants as $index => $tenant): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($tenant['Tenant_Name']) ?></td>
                                <td><?= htmlspecialchars($tenant['Contact_Number']) ?></td>
                                <td><?= htmlspecialchars($tenant['Email']) ?></td>
                                <td><?= htmlspecialchars($tenant['ID_Number']) ?></td>
                                <td><?= htmlspecialchars($tenant['Lease_ID']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">No tenants found.</p>
        <?php endif; ?>
    </div>
</div>
