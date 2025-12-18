<?php
// /silentauction/index.php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Silent Auction System - Home</title>

    <!-- App State black & gold home page theme -->
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
            padding: 40px 0 60px;
            text-align: center;
        }

        .site-title {
            font-size: 40px;
            font-weight: 800;
            color: #FFCC00;
            margin: 0 0 10px;
            text-shadow: 0 0 2px #333;
        }

        h2 {
            margin: 0 0 4px;
        }

        h3 {
            margin: 0 0 26px;
            color: #555555;
        }

        /* Big home button */
        .btn-home {
            display: block;
            width: 100%;
            max-width: 820px;
            margin: 14px auto;
            padding: 14px 18px;
            font-size: 20px;
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            border-radius: 6px;
            border: 2px solid #FFCC00;
            background: #111111;
            color: #FFCC00;
            transition: background 0.2s, color 0.2s, transform 0.1s;
        }

        .btn-home:hover {
            background: #FFCC00;
            color: #111111;
            transform: translateY(-1px);
        }

        .subtext {
            margin-top: 28px;
            font-size: 13px;
            color: #777777;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="site-title">Welcome to the W.H. Taylor Elementary School</h1>
    <h2>Silent Auction System</h2>
    <h3>Home Page!</h3>

    <a class="btn-home" href="part1/index.php">
        Solicit and Gather Donations
    </a>

    <div class="subtext">
        Use the button above to manage donors, items, and donation letters.
    </div>
</div>

</body>
</html>
