<?php
// /silentauction/part1/mark_receipt.php
require_once __DIR__ . "/../includes/db.php";

if (!isset($_GET['DonorID'])) {
    die("No DonorID specified.");
}

$DonorID = intval($_GET['DonorID']);

$stmt = $conn->prepare("UPDATE Donor SET TaxReceipt = 1 WHERE DonorID = ?");
$stmt->bind_param("i", $DonorID);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: index.php");
    exit;
} else {
    echo "Error updating receipt status: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
