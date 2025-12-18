<?php
require_once __DIR__ . "/../includes/db.php";

/* --- GET DONORS --- */
$donors = $conn->query("SELECT * FROM Donor ORDER BY DonorID");

/* --- GET ITEMS --- */
$items = $conn->query("SELECT * FROM Item ORDER BY ItemID");

/* --- GET DONORS WITHOUT RECEIPT --- */
$noReceipt = $conn->query("SELECT * FROM Donor WHERE TaxReceipt = 0 ORDER BY DonorID");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Donations Directory</title>

    <!-- ===== APP STATE BLACK & GOLD THEME ===== -->
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #ffffff;
            color: #111111;
        }

        .container {
            width: 92%;
            max-width: 980px;
            margin: 0 auto;
            padding: 18px 0 40px;
        }

        h1 {
            font-size: 32px;
            font-weight: 800;
            color: #FFCC00;
            margin-bottom: 10px;
        }

        h2 {
            margin-top: 18px;
            color: #D4A500;
        }

        /* NAVBAR */
        a.nav-link {
            display: inline-block;
            padding: 6px 14px;
            margin-right: 6px;
            border-radius: 16px;
            background: #FFCC00;
            color: #111111;
            font-weight: 700;
            text-decoration: none;
            border: 2px solid #D4A500;
        }
        a.nav-link:hover {
            background: #D4A500;
            color: white;
        }

        hr {
            border: none;
            border-top: 2px solid #FFCC00;
            margin: 10px 0 20px;
        }

        /* TABLES */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 24px;
            font-size: 14px;
        }
        th {
            background: #111111;
            color: #FFCC00;
            padding: 8px 10px;
            border: 1px solid #D4A500;
            font-weight: 800;
            text-align: left;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #d0d0d0;
        }
        tr:nth-child(even) td { background: #f4f4f4; }
        tr:nth-child(odd) td { background: #ffffff; }

        /* TABLE ACTION BUTTONS */
        table a {
            padding: 4px 8px;
            border-radius: 4px;
            min-width: 64px;
            display: inline-block;
            font-size: 13px;
            color: white;
            text-align: center;
            text-decoration: none;
        }

        /* UPDATE */
        table a[href*="update"] {
            background: #D4A500;
        }
        table a[href*="update"]:hover {
            background: #b28900;
        }

        /* DELETE */
        table a[href*="delete"] {
            background: #cc0000;
        }
        table a[href*="delete"]:hover {
            background: #a40000;
        }

        /* BLUE BUTTONS (letter, receipt, add item) */
        table a[href*="generate"],
        table a[href*="mark"],
        table a[href*="add_item"] {
            background: #007bff;
        }
        table a[href*="generate"]:hover,
        table a[href*="mark"]:hover,
        table a[href*="add_item"]:hover {
            background: #0063c7;
        }
    </style>
</head>

<body>
<div class="container">

    <!-- NAVIGATION BAR -->
    <a class="nav-link" href="../index.php">Home</a>
    <a class="nav-link" href="index.php">Directory</a>
    <a class="nav-link" href="add_donor.php">Enter a New Donor</a>
    <a class="nav-link" href="add_item.php">Enter a New Donated Item</a>

    <hr>

    <h1>Donations Directory</h1>

    <!-- ===================== DONORS ===================== -->
    <h2>List of Donors</h2>
    <table>
        <tr>
            <th>Donor ID</th>
            <th>Business Name</th>
            <th>Contact Name</th>
            <th>Contact Title</th>
            <th>Address</th>
            <th>City</th>
            <th>State</th>
            <th>Zip Code</th>
            <th>Tax Receipt</th>
            <th>Generate Letter</th>
            <th>Add Item</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>

        <?php while ($d = $donors->fetch_assoc()): ?>
        <tr>
            <td><?= $d['DonorID'] ?></td>
            <td><?= $d['BusinessName'] ?></td>
            <td><?= $d['ContactName'] ?></td>
            <td><?= $d['ContactTitle'] ?></td>
            <td><?= $d['Address'] ?></td>
            <td><?= $d['City'] ?></td>
            <td><?= $d['State'] ?></td>
            <td><?= $d['ZipCode'] ?></td>
            <td><?= $d['TaxReceipt'] ? "Sent" : "Not Sent" ?></td>

            <td><a href="generate_letter.php?DonorID=<?= $d['DonorID'] ?>">Make Letter</a></td>
            <td><a href="add_item.php?DonorID=<?= $d['DonorID'] ?>">Add Item</a></td>
            <td><a href="update_donor.php?DonorID=<?= $d['DonorID'] ?>">Update</a></td>
            <td><a href="delete_donor.php?DonorID=<?= $d['DonorID'] ?>">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- ===================== ITEMS ===================== -->
    <h2>List of Donated Items</h2>
    <table>
        <tr>
            <th>Item ID</th>
            <th>Description</th>
            <th>Retail Value</th>
            <th>Donor ID</th>
            <th>Update Item</th>
            <th>Delete Item</th>
        </tr>

        <?php while ($i = $items->fetch_assoc()): ?>
        <tr>
            <td><?= $i['ItemID'] ?></td>
            <td><?= $i['Description'] ?></td>
            <td><?= number_format($i['RetailValue'], 2) ?></td>
            <td><?= $i['DonorID'] ?></td>

            <td><a href="update_item.php?ItemID=<?= $i['ItemID'] ?>">Update</a></td>
            <td><a href="delete_item.php?ItemID=<?= $i['ItemID'] ?>">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- ===================== DONORS WITHOUT RECEIPT ===================== -->
    <h2>List of Donors Without Receipt</h2>
    <table>
        <tr>
            <th>Donor ID</th>
            <th>Business Name</th>
            <th>Contact Name</th>
            <th>Contact Title</th>
            <th>Address</th>
            <th>City</th>
            <th>State</th>
            <th>Zip Code</th>
            <th>Mark Receipt as Sent</th>
            <th>Generate Receipt</th>
        </tr>

        <?php while ($r = $noReceipt->fetch_assoc()): ?>
        <tr>
            <td><?= $r['DonorID'] ?></td>
            <td><?= $r['BusinessName'] ?></td>
            <td><?= $r['ContactName'] ?></td>
            <td><?= $r['ContactTitle'] ?></td>
            <td><?= $r['Address'] ?></td>
            <td><?= $r['City'] ?></td>
            <td><?= $r['State'] ?></td>
            <td><?= $r['ZipCode'] ?></td>

            <td><a href="mark_receipt.php?DonorID=<?= $r['DonorID'] ?>">Mark Receipt</a></td>
            <td><a href="generate_receipt.php?DonorID=<?= $r['DonorID'] ?>">Make Receipt</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>
</body>
</html>
