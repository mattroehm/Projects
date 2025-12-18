<?php
// /silentauction/setup_tables.php
// Run this ONCE to create all required tables in your database.

require_once __DIR__ . "/includes/db.php";

$queries = [];

// Donor table
$queries[] = "
CREATE TABLE IF NOT EXISTS Donor (
    DonorID INT NOT NULL,
    BusinessName VARCHAR(75),
    ContactName VARCHAR(75),
    ContactEmail VARCHAR(200),
    ContactTitle VARCHAR(75),
    Address VARCHAR(75),
    City VARCHAR(30),
    State VARCHAR(2),
    ZipCode VARCHAR(5),
    TaxReceipt BOOLEAN,
    PRIMARY KEY (DonorID)
);";

// Bidder table
$queries[] = "
CREATE TABLE IF NOT EXISTS Bidder (
    BidderID INT NOT NULL,
    Name VARCHAR(75),
    Address VARCHAR(75),
    CellNumber VARCHAR(10),
    HomeNumber VARCHAR(10),
    Email VARCHAR(200),
    Paid BOOLEAN,
    PRIMARY KEY (BidderID)
);";

// Category table
$queries[] = "
CREATE TABLE IF NOT EXISTS Category (
    CategoryID INT NOT NULL,
    Description VARCHAR(75),
    PRIMARY KEY (CategoryID)
);";

// Item table
$queries[] = "
CREATE TABLE IF NOT EXISTS Item (
    ItemID INT NOT NULL,
    Description VARCHAR(75),
    RetailValue DECIMAL(10,2),
    DonorID INT,
    LotID INT,
    PRIMARY KEY (ItemID)
);";

// Lot table
$queries[] = "
CREATE TABLE IF NOT EXISTS Lot (
    LotID INT NOT NULL,
    Description VARCHAR(125),
    CategoryID INT,
    WinningBid DECIMAL(10,2),
    WinningBidder INT,
    Delivered BOOLEAN,
    PRIMARY KEY (LotID)
);";

// Bid table
$queries[] = "
CREATE TABLE IF NOT EXISTS Bid (
    LotID INT NOT NULL,
    BidderID INT NOT NULL,
    BidTime DATETIME,
    Bid DECIMAL(10,2),
    PRIMARY KEY (LotID, BidderID, BidTime)
);";

echo "<h2>Creating tables...</h2>";

foreach ($queries as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "<p>Query OK:</p><pre>" . htmlspecialchars($sql) . "</pre><hr>";
    } else {
        echo "<p style='color:red;'>Error:</p><pre>" . htmlspecialchars($sql) . "</pre>";
        echo "<p style='color:red;'>MySQL says: " . htmlspecialchars($conn->error) . "</p><hr>";
    }
}

echo "<h3>Done. You can now go back to the <a href='part1/index.php'>Donations Directory</a>.</h3>";

$conn->close();
?>
