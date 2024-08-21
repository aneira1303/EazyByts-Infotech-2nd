<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'inventory_system';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        html {
            background-color: rgb(255, 200, 200); /* Light red background */
        }
        * {
            box-sizing: border-box;
        }
        .selected {
            background-color: #ff9999; /* Light red for selection */
        }
        .header {
            color: white;
            font-size: 50px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
            height: 100px;
            background: linear-gradient(135deg, #ff3333, #ff6666, #ff9999); /* Shades of red */
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ff0000; /* Bright red border */
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0) 60%);
        }
        .header .icon {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 50px;
            height: 50px;
            background: url('path/to/red-rose-icon.png') no-repeat center center;
            background-size: cover;
        }
        .sidebar {
            background-color: #ffcccc; /* Light red */
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .sidebar a {
            display: block;
            margin-bottom: 10px;
            color: #ff3333; /* Bright red */
            text-decoration: none;
            font-size: 18px;
            padding: 10px;
            border-radius: 5px;
            background-color: #ffe6e6; /* Very light red */
        }
        .sidebar a:hover {
            background-color: #ff9999; /* Light red on hover */
        }
        .main-content {
            padding: 20px;
            background-color: #ffe6e6; /* Very light red */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="header">
            <div class="icon"></div>
            <h1>Inventory Management System</h1>
        </div>
        <div class="row mt-4">
            <div class="col-md-3 sidebar">
                <a href="categories.php" class="btn btn-light">Manage Categories</a>
                <a href="brands.php" class="btn btn-light">Manage Brands</a>
                <a href="products.php" class="btn btn-light">Manage Products</a>
                <a href="sales.php" class="btn btn-light">Manage Sales</a>
                <a href="invoices.php" class="btn btn-light">Generate Invoices</a>
                <a href="stock_reports.php" class="btn btn-light">Stock Reports</a>
                <a href="users.php" class="btn btn-light">Manage Users</a>
                <a href="settings.php" class="btn btn-light">Settings</a>
                <!-- New Modules -->
                <a href="orders.php" class="btn btn-light">Manage Orders</a>
                <a href="stock_management.php" class="btn btn-light">Stock Management</a>
            </div>
            <div class="col-md-9 main-content">
                <!-- Content of the selected module will be displayed here -->
                <h2>Welcome to the Inventory Management System</h2>
                <p>Select from the sidebar to get started.</p>
            </div>
        </div>
    </div>
</body>
</html>
