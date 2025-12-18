<?php
// /silentauction/part1/generate_receipt.php
require_once __DIR__ . "/../includes/db.php";

if (!isset($_GET['DonorID'])) {
    die("No DonorID specified.");
}

$DonorID = intval($_GET['DonorID']);

$stmt = $conn->prepare(
    "SELECT DonorID, BusinessName, ContactName, Address, City, State, ZipCode
     FROM Donor
     WHERE DonorID = ?"
);
$stmt->bind_param("i", $DonorID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Donor not found.");
}

$donor = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Donation Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #444; }
        .box { border: 1px solid #ccc; padding: 20px; width: 500px; }
        .print { margin-top: 20px; padding: 8px 15px; background-color: #444; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<h1>Donation Receipt</h1>

<div class="box">
    <p><strong>Donor ID:</strong> <?= htmlspecialchars($donor['DonorID']) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($donor['BusinessName']) ?></p>
    <p><strong>Contact:</strong> <?= htmlspecialchars($donor['ContactName']) ?></p>
    <p><strong>Address:</strong>
        <?= htmlspecialchars($donor['Address']) ?>,
        <?= htmlspecialchars($donor['City']) ?>,
        <?= htmlspecialchars($donor['State']) ?> <?= htmlspecialchars($donor['ZipCode']) ?>
    </p>

    <p>Thank you for your generous contribution to the 2013 Taylor Silent Auction.  
       Please keep this receipt for your records.</p>
</div>

<button class="print" onclick="window.print()">Print Receipt</button>

</body>
</html>

<?php $conn->close(); ?>
