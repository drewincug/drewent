<?php
include 'db_connect.php'; // defines $conn (mysqli)

if (isset($_POST['submit'])) {
    $Client_ID = $_POST['Client_ID'];
    $Reference_Type = $_POST['Reference_Type'];
    $Reference_ID = $_POST['Reference_ID'];
    $Amount = $_POST['Amount'];
    $Payment_Method = $_POST['Payment_Method'];
    $Reference_No = $_POST['Reference_No'];
    $Recorded_By = $_POST['Recorded_By'];

    // Call the AddPayment stored procedure
    $sql = "CALL AddPayment(?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isidsss", 
        $Client_ID,
        $Reference_Type,
        $Reference_ID,
        $Amount,
        $Payment_Method,
        $Reference_No,
        $Recorded_By
    );

    if ($stmt->execute()) {
        echo "<script>alert('✅ Payment added successfully!'); window.location='add_payment.php';</script>";
    } else {
        echo "❌ Error adding payment: " . $stmt->error;
    }
}
?>

<form method="POST" style="max-width:600px;margin:auto;">
    <h2>Add Payment</h2>
    <label>Client ID:</label><input type="number" name="Client_ID" required><br>
    <label>Reference Type:</label><input type="text" name="Reference_Type" required><br>
    <label>Reference ID:</label><input type="number" name="Reference_ID" required><br>
    <label>Amount:</label><input type="number" step="0.01" name="Amount" required><br>
    <label>Payment Method:</label><input type="text" name="Payment_Method" required><br>
    <label>Reference No:</label><input type="text" name="Reference_No" required><br>
    <label>Recorded By:</label><input type="text" name="Recorded_By" required><br><br>
    <button type="submit" name="submit">💾 Save Payment</button>
</form>
