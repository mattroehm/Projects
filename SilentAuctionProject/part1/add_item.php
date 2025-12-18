<?php
// /part1/add_item.php
require_once "../includes/db.php";

$errors = [];
$success = false;

// prefill DonorID when coming from "Add Item" button
$prefillDonorID = isset($_GET['DonorID']) ? intval($_GET['DonorID']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ItemID      = intval($_POST['ItemID']);
    $Description = trim($_POST['Description']);
    $RetailValue = trim($_POST['RetailValue']);
    $DonorID     = intval($_POST['DonorID']);
    $LotID       = ($_POST['LotID'] === '') ? null : intval($_POST['LotID']);

    if ($ItemID <= 0)                      $errors[] = "Item ID must be a positive number.";
    if ($Description === '')               $errors[] = "Description is required.";
    if ($RetailValue === '' || !is_numeric($RetailValue)) $errors[] = "Retail Value must be numeric.";
    if ($DonorID <= 0)                     $errors[] = "Donor ID must be a positive number.";

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "INSERT INTO Item (ItemID, Description, RetailValue, DonorID, LotID)
             VALUES (?, ?, ?, ?, ?)"
        );
        if ($LotID === null) {
            $null = null;
            $stmt->bind_param("isdii", $ItemID, $Description, $RetailValue, $DonorID, $null);
        } else {
            $stmt->bind_param("isdii", $ItemID, $Description, $RetailValue, $DonorID, $LotID);
        }

        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Enter New Item</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
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
        .error { color: red; }
        .success { color: green; margin-top: 10px; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a class="nav-button" href="index.php">Directory</a>
    <a class="nav-button" href="add_donor.php">Enter a New Donor</a>
    <a class="nav-button" href="add_item.php">Enter a New Donated Item</a>
    <a class="nav-button" href="../index.php">Return to Home Page</a>
</div>

<h1>Please Enter Item Information Below</h1>

<?php if (!empty($errors)): ?>
    <div class="error">
        <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success">New item record created successfully!</div>
<?php endif; ?>

<form method="post" action="add_item.php">
    <label>Item ID:
        <input type="number" name="ItemID" required>
    </label>

    <label>Description:
        <input type="text" name="Description" required>
    </label>

    <label>Retail Value:
        <input type="text" name="RetailValue" required>
    </label>

    <label>Donor ID:
        <input type="number" name="DonorID" value="<?= htmlspecialchars($prefillDonorID) ?>" required>
    </label>

    <label>Lot ID:
        <input type="number" name="LotID">
    </label>

    <br>
    <button class="button button-green" type="submit">Submit</button>
    <a class="button button-red" href="index.php" style="text-decoration:none;display:inline-block;text-align:center;">Cancel</a>
</form>

</body>
</html>
<?php $conn->close(); ?>
