<?php
// /part1/add_donor.php
require_once "../includes/db.php";

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $DonorID      = intval($_POST['DonorID']);
    $BusinessName = trim($_POST['BusinessName']);
    $ContactName  = trim($_POST['ContactName']);
    $ContactTitle = trim($_POST['ContactTitle']);
    $Address      = trim($_POST['Address']);
    $City         = trim($_POST['City']);
    $State        = trim($_POST['State']);
    $ZipCode      = trim($_POST['ZipCode']);
    $TaxReceipt   = ($_POST['TaxReceipt'] === '1') ? 1 : 0;

    if ($DonorID <= 0)          $errors[] = "Donor ID must be a positive number.";
    if ($BusinessName === '')   $errors[] = "Business Name is required.";
    if ($ContactName === '')    $errors[] = "Contact Name is required.";
    if ($Address === '')        $errors[] = "Address is required.";
    if ($City === '')           $errors[] = "City is required.";
    if (strlen($State) !== 2)   $errors[] = "State must be 2 characters.";
    if (strlen($ZipCode) !== 5) $errors[] = "Zip Code must be 5 digits.";

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "INSERT INTO Donor
             (DonorID, BusinessName, ContactName, ContactEmail, ContactTitle, Address, City, State, ZipCode, TaxReceipt)
             VALUES (?, ?, ?, '', ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "isssssssi",
            $DonorID, $BusinessName, $ContactName, $ContactTitle,
            $Address, $City, $State, $ZipCode, $TaxReceipt
        );

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
    <title>Enter New Donor</title>
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

<h1>Please Enter Donor Information Below</h1>

<?php if (!empty($errors)): ?>
    <div class="error">
        <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success">New donor record created successfully!</div>
<?php endif; ?>

<form method="post" action="add_donor.php">
    <label>Donor ID:
        <input type="number" name="DonorID" required>
    </label>

    <label>Business Name:
        <input type="text" name="BusinessName" required>
    </label>

    <label>Contact Name:
        <input type="text" name="ContactName" required>
    </label>

    <label>Contact Title:
        <input type="text" name="ContactTitle">
    </label>

    <label>Address Line 1:
        <input type="text" name="Address" required>
    </label>

    <label>City Name:
        <input type="text" name="City" required>
    </label>

    <label>State Name:
        <input type="text" name="State" maxlength="2" required>
    </label>

    <label>Zip Code:
        <input type="text" name="ZipCode" maxlength="5" required>
    </label>

    <label>Tax Receipt:
        <select name="TaxReceipt">
            <option value="0">Not Sent</option>
            <option value="1">Sent</option>
        </select>
    </label>

    <br>
    <button class="button button-green" type="submit">Submit</button>
    <a class="button button-red" href="index.php" style="text-decoration:none;display:inline-block;text-align:center;">Cancel</a>
</form>

</body>
</html>
<?php $conn->close(); ?>
