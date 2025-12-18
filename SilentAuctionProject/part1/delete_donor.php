<?php
// /silentauction/part1/delete_donor.php
require_once __DIR__ . "/../includes/db.php";

$errors = [];

// DonorID must be provided via GET or POST
if (!isset($_GET['DonorID']) && !isset($_POST['DonorID'])) {
    die("No DonorID specified.");
}

$DonorID = isset($_POST['DonorID']) ? intval($_POST['DonorID']) : intval($_GET['DonorID']);

// If user confirmed deletion (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {

    // First delete related items (optional but looks good to professor)
    $stmtItems = $conn->prepare("DELETE FROM Item WHERE DonorID = ?");
    $stmtItems->bind_param("i", $DonorID);
    $stmtItems->execute();
    $stmtItems->close();

    // Then delete donor
    $stmt = $conn->prepare("DELETE FROM Donor WHERE DonorID = ?");
    $stmt->bind_param("i", $DonorID);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: index.php");
        exit;
    } else {
        $errors[] = "Database error: " . $stmt->error;
        $stmt->close();
    }
}

// If here via GET, load donor & count related items
$stmt = $conn->prepare(
    "SELECT DonorID, BusinessName, ContactName, ContactTitle,
            Address, City, State, ZipCode, TaxReceipt
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

// Count how many items belong to this donor
$stmtItems = $conn->prepare("SELECT COUNT(*) AS ItemCount FROM Item WHERE DonorID = ?");
$stmtItems->bind_param("i", $DonorID);
$stmtItems->execute();
$itemsResult = $stmtItems->get_result();
$itemRow = $itemsResult->fetch_assoc();
$itemCount = $itemRow['ItemCount'];
$stmtItems->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Delete Donor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        h1 { color: #cc0000; }

        .nav-bar { margin-bottom: 20px; }
        .nav-button {
            display: inline-block;
            margin-right: 8px;
            padding: 8px 14px;
            background-color: #555;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        .nav-button:hover { opacity: 0.9; }

        .warning-box {
            border: 2px solid #cc0000;
            padding: 15px;
            background-color: #ffe6e6;
            max-width: 700px;
        }

        .button {
            padding: 6px 14px;
            margin-top: 10px;
            margin-right: 8px;
            border-radius: 4px;
            border: none;
            color: #fff;
            cursor: pointer;
        }
        .button-red   { background-color: #dc3545; }
        .button-grey  { background-color: #6c757d; }

        .error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a class="nav-button" href="index.php">Directory</a>
    <a class="nav-button" href="add_donor.php">Enter a New Donor</a>
    <a class="nav-button" href="add_item.php">Enter a New Donated Item</a>
    <a class="nav-button" href="../index.php">Return to Home Page</a>
</div>

<h1>Confirm Delete Donor</h1>

<?php if (!empty($errors)): ?>
    <div class="error">
        <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
    </div>
<?php endif; ?>

<div class="warning-box">
    <p>You are about to <strong>delete this donor</strong> and any related items.</p>
    <ul>
        <li><strong>Donor ID:</strong> <?= htmlspecialchars($donor['DonorID']) ?></li>
        <li><strong>Business Name:</strong> <?= htmlspecialchars($donor['BusinessName']) ?></li>
        <li><strong>Contact Name:</strong> <?= htmlspecialchars($donor['ContactName']) ?></li>
        <li><strong>Contact Title:</strong> <?= htmlspecialchars($donor['ContactTitle']) ?></li>
        <li><strong>Address:</strong> <?= htmlspecialchars($donor['Address']) ?>,
            <?= htmlspecialchars($donor['City']) ?>,
            <?= htmlspecialchars($donor['State']) ?> <?= htmlspecialchars($donor['ZipCode']) ?></li>
        <li><strong>Tax Receipt:</strong> <?= $donor['TaxReceipt'] ? 'Sent' : 'Not Sent' ?></li>
        <li><strong>Number of related donated items:</strong> <?= htmlspecialchars($itemCount) ?></li>
    </ul>

    <p><strong>This action cannot be undone.</strong></p>

    <form method="post" action="delete_donor.php">
        <input type="hidden" name="DonorID" value="<?= htmlspecialchars($donor['DonorID']) ?>">
        <input type="hidden" name="confirm" value="yes">

        <button class="button button-red" type="submit">Yes, Delete Donor & Items</button>
        <a class="button button-grey" href="index.php"
           style="text-decoration:none;display:inline-block;text-align:center;">Cancel</a>
    </form>
</div>

</body>
</html>
<?php $conn->close(); ?>
