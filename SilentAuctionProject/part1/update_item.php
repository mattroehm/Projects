<?php
// /silentauction/part1/update_item.php
require_once __DIR__ . "/../includes/db.php";

$errors = [];

// Make sure we have an ItemID
if (!isset($_GET['ItemID']) && !isset($_POST['ItemID'])) {
    die("No ItemID specified.");
}

$ItemID = isset($_POST['ItemID']) ? intval($_POST['ItemID']) : intval($_GET['ItemID']);

// If form submitted, process update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Description = trim($_POST['Description']);
    $RetailValue = trim($_POST['RetailValue']);
    $DonorID     = trim($_POST['DonorID']);
    $LotID       = trim($_POST['LotID']);

    if ($ItemID <= 0)          $errors[] = "Item ID is invalid.";
    if ($Description === '')   $errors[] = "Description is required.";
    if ($RetailValue === '' || !is_numeric($RetailValue)) $errors[] = "Retail Value must be a number.";
    if ($DonorID === '' || !is_numeric($DonorID))         $errors[] = "Donor ID must be a number.";
    // LotID can be blank or numeric; we’ll allow blank

    if (empty($errors)) {
        // Null for LotID if blank
        $LotIDValue = ($LotID === '') ? null : intval($LotID);
        $RetailValueNum = floatval($RetailValue);
        $DonorIDNum = intval($DonorID);

        $stmt = $conn->prepare(
            "UPDATE Item
             SET Description = ?, RetailValue = ?, DonorID = ?, LotID = ?
             WHERE ItemID = ?"
        );
        $stmt->bind_param(
            "sdiii",
            $Description,
            $RetailValueNum,
            $DonorIDNum,
            $LotIDValue,
            $ItemID
        );

        if ($stmt->execute()) {
            // Go back to directory after successful update
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Load existing item so we can show form
$stmt = $conn->prepare(
    "SELECT ItemID, Description, RetailValue, DonorID, LotID
     FROM Item
     WHERE ItemID = ?"
);
$stmt->bind_param("i", $ItemID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Item not found.");
}

$item = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Update Item</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        h1 { color: #ccaa00; }

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

        label { display: block; margin-top: 8px; }
        input { width: 250px; padding: 4px; }

        .button {
            padding: 6px 14px;
            margin-top: 10px;
            border-radius: 4px;
            border: none;
            color: #fff;
            cursor: pointer;
        }
        .button-green { background-color: #28a745; }
        .button-red   { background-color: #dc3545; }

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

<h1>Update Donated Item</h1>

<?php if (!empty($errors)): ?>
    <div class="error">
        <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
    </div>
<?php endif; ?>

<form method="post" action="update_item.php">
    <!-- ItemID read-only so they don't change primary key -->
    <label>Item ID:
        <input type="number" name="ItemID"
               value="<?= htmlspecialchars($item['ItemID']) ?>" readonly>
    </label>

    <label>Description:
        <input type="text" name="Description"
               value="<?= htmlspecialchars($item['Description']) ?>" required>
    </label>

    <label>Retail Value:
        <input type="text" name="RetailValue"
               value="<?= htmlspecialchars($item['RetailValue']) ?>" required>
    </label>

    <label>Donor ID:
        <input type="number" name="DonorID"
               value="<?= htmlspecialchars($item['DonorID']) ?>" required>
    </label>

    <label>Lot ID:
        <input type="number" name="LotID"
               value="<?= htmlspecialchars($item['LotID']) ?>">
    </label>

    <br>
    <button class="button button-green" type="submit">Save Changes</button>
    <a class="button button-red" href="index.php"
       style="text-decoration:none;display:inline-block;text-align:center;">Cancel</a>
</form>

</body>
</html>
<?php $conn->close(); ?>
