<?php
// /silentauction/part1/generate_letter.php
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
    <title>Thank You Letter</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        h1 { color: #444; }
        .print { margin-top: 20px; padding: 8px 15px; background-color: #444; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<h1>Thank You Letter</h1>

<p><?= htmlspecialchars($donor['ContactName']) ?><br>
<?= htmlspecialchars($donor['BusinessName']) ?><br>
<?= htmlspecialchars($donor['Address']) ?><br>
<?= htmlspecialchars($donor['City']) ?>,
<?= htmlspecialchars($donor['State']) ?> <?= htmlspecialchars($donor['ZipCode']) ?>
</p>

<p>Dear <?= htmlspecialchars($donor['ContactName']) ?>,</p>

<p>Thank you for your generous donation to the 2013 Taylor Silent Auction.  
Your contribution plays a vital role in supporting this year’s mission and  
helping make the event a success.</p>

<p>We truly appreciate your support and commitment to our community.</p>

<p>Sincerely,<br>
<strong>Taylor Silent Auction Committee</strong></p>

<button class="print" onclick="window.print()">Print Letter</button>

</body>
</html>

<?php $conn->close(); ?>
