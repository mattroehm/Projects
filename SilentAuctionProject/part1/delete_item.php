<?php
// /silentauction/part1/delete_item.php
require_once __DIR__ . "/../includes/db.php";

$errors = [];

// Make sure we have an ItemID either via GET (first load) or POST (after confirm)
if (!isset($_GET['ItemID']) && !isset($_POST['ItemID'])) {
    die("No ItemID specified.");
}

$ItemID = isset($_POST['ItemID']) ? intval($_POST['ItemID']) : intval($_GET['ItemID']);

// If user confirmed deletion (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {

    $stmt = $conn->prepare("DELETE FROM Item WHERE ItemID = ?");
    $stmt->bind_param("i", $ItemID);

    if ($stmt->execute()) {
        $stmt->close();
        // Go back to directory after delete
        header("Location: index.php");
        exit;
    } else {
        $errors[] = "Database error: " . $stmt->error;
        $stmt->close();
    }
}

// If we get here via GET, load item so we can show confirmation
$stmt = $conn->prepare(
    "SELECT ItemID, Description, RetailValue, DonorID
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
    <title>Delete Item</title>
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
            max-width: 600px;
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

<h1>Confirm Delete Item</h1>

<?php if (!empty($errors)): ?>
    <div class="error">
        <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
    </div>
<?php endif; ?>

<div class="warning-box">
    <p>Are you sure you want to <strong>delete this item</strong>?</p>
    <ul>
        <li><strong>Item ID:</strong> <?= htmlspecialchars($item['ItemID']) ?></li>
        <li><strong>Description:</strong> <?= htmlspecialchars($item['Description']) ?></li>
        <li><strong>Retail Value:</strong> <?= htmlspecialchars($item['RetailValue']) ?></li>
        <li><strong>Donor ID:</strong> <?= htmlspecialchars($item['DonorID']) ?></li>
    </ul>

    <form method="post" action="delete_item.php">
        <input type="hidden" name="ItemID" value="<?= htmlspecialchars($item['ItemID']) ?>">
        <input type="hidden" name="confirm" value="yes">

        <button class="button button-red" type="submit">Yes, Delete Item</button>
        <a class="button button-grey" href="index.php"
           style="text-decoration:none;display:inline-block;text-align:center;">Cancel</a>
    </form>
</div>

</body>
</html>
<?php $conn->close(); ?>
