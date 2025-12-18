<?php
$host = "cis3870-2504.mysql.database.azure.com";
$user = "roehmmc_fc";        // your Azure MySQL username
$pass = "7758915199b26883840998ee";  // your Azure MySQL password
$dbname = "roehmmc_db"; // your actual database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
