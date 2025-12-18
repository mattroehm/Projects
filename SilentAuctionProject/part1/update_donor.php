<?php
// /silentauction/part1/update_donor.php
require_once __DIR__ . "/../includes/db.php";

$errors = [];
$success = false;

// Get DonorID from query string
if (!isset($_GET['DonorID']) && !isset($_POST['DonorID'])) {
    die("No DonorID specified.");
}

// DonorID is passed either via GET (first load) or POST (when submitting)
$DonorID = isset($_POST['DonorID']) ? intval($_POST['DonorID']) : intval($_GET['DonorID']);

// When the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $BusinessName = trim($_POST['BusinessName']);
    $ContactName  = trim($_POST['ContactName']);
    $ContactTitle = trim($_POST['ContactTitle']);
    $Address      = trim($_POST['Address']);
    $City         = trim($_POST['City']);
    $State        = trim($_POST['State']);
    $ZipCode      = trim($_POST['ZipCode']);
    $TaxReceipt   = ($_POST['TaxReceipt'] === '1') ? 1 : 0;

    if ($DonorID <= 0)          $errors[] = "Donor ID is invalid.";
    if ($BusinessName === '')   $errors[] = "Business Name is required.";
    if ($ContactName === '')    $errors[] = "Contact Name is required.";
    if ($Address === '')        $errors[] = "Address is required.";
    if ($City === '')           $errors[] = "City is required.";
    if (strlen($State) !== 2)   $errors[] = "State must be 2 characters.";
    if (strlen($ZipCode) !== 5) $errors[] = "Zip Code must be 5 digits.";

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE Donor
             SET BusinessName = ?, ContactName = ?, ContactTitle = ?,
                 Address = ?, City = ?, State = ?, ZipCode = ?, TaxReceipt = ?
             WHERE DonorID = ?"
        );
        $stmt->bind_param(
            "sssssssii",
            $BusinessName, $ContactName, $ContactTitle,
            $Address, $City, $State, $ZipCode, $TaxReceipt, $DonorID
        );

        if ($stmt->execute()) {
            $success = true;
            // After successful update, send back to directory
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// If we're here (GET or validation failed), load existing donor data
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
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Update Donor</title>
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
        input, select { width: 250px; padding: 4px; }

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

<h1>Update Donor Information</h1>

<?php if (!empty($errors)): ?>
    <div class="error">
        <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
    </div>
<?php endif; ?>

<form method="post" action="update_donor.php">
    <!-- DonorID is read-only so they don't change the primary key -->
    <label>Donor ID:
        <input type="number" name="DonorID"
               value="<?= htmlspecialchars($donor['DonorID']) ?>" readonly>
    </label>

    <label>Business Name:
        <input type="text" name="BusinessName"
               value="<?= htmlspecialchars($donor['BusinessName']) ?>" required>
    </label>

    <label>Contact Name:
        <input type="text" name="ContactName"
               value="<?= htmlspecialchars($donor['ContactName']) ?>" required>
    </label>

    <label>Contact Title:
        <input type="text" name="ContactTitle"
               value="<?= htmlspecialchars($donor['ContactTitle']) ?>">
    </label>

    <label>Address Line 1:
        <input type="text" name="Address"
               value="<?= htmlspecialchars($donor['Address']) ?>" required>
    </label>

    <label>City Name:
        <input type="text" name="City"
               value="<?= htmlspecialchars($donor['City']) ?>" required>
    </label>

    <label>State Name:
        <input type="text" name="State" maxlength="2"
               value="<?= htmlspecialchars($donor['State']) ?>" required>
    </label>

    <label>Zip Code:
        <input type="text" name="ZipCode" maxlength="5"
               value="<?= htmlspecialchars($donor['ZipCode']) ?>" required>
    </label>

    <label>Tax Receipt:
        <select name="TaxReceipt">
            <option value="0" <?= !$donor['TaxReceipt'] ? 'selected' : '' ?>>Not Sent</option>
            <option value="1" <?= $donor['TaxReceipt'] ? 'selected' : '' ?>>Sent</option>
        </select>
    </label>

    <br>
    <button class="button button-green" type="submit">Save Changes</button>
    <a class="button button-red" href="index.php"
       style="text-decoration:none;display:inline-block;text-align:center;">Cancel</a>
</form>

</body>
</html>
<?php $conn->close(); ?>
