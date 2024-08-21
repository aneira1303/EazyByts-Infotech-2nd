<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'inventory_system';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions for adding or updating adjustments
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_adjustment'])) {
        // Add new stock adjustment entry
        $stock_id = $_POST['stock_id'];
        $adjustment_type = $_POST['adjustment_type'];
        $quantity = $_POST['quantity'];

        $stmt = $conn->prepare("INSERT INTO stock_adjustments (stock_id, adjustment_type, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $stock_id, $adjustment_type, $quantity);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Stock adjustment added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding adjustment: " . $conn->error . "</div>";
        }
    }
}

// Handle adjustment deletion
if (isset($_GET['delete'])) {
    $adjustment_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM stock_adjustments WHERE adjustment_id=?");
    $stmt->bind_param("i", $adjustment_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Stock adjustment deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting adjustment: " . $conn->error . "</div>";
    }
}

// Fetch all stock adjustments
$result = $conn->query("SELECT sa.adjustment_id, s.product_id, s.quantity AS stock_quantity, sa.adjustment_date, sa.adjustment_type, sa.quantity AS adjustment_quantity
FROM stock_adjustments sa
JOIN stock s ON sa.stock_id = s.stock_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Adjustments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .alert {
            margin-top: 20px;
        }
        table th, table td {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Stock Adjustments</h1>

        <!-- Add Stock Adjustment Form -->
        <div class="mb-4">
            <h2>Add New Adjustment</h2>
            <form action="stock_adjustments.php" method="post">
                <div class="form-group">
                    <label for="stock_id">Stock ID</label>
                    <input type="number" class="form-control" id="stock_id" name="stock_id" required>
                </div>
                <div class="form-group">
                    <label for="adjustment_type">Adjustment Type</label>
                    <select class="form-control" id="adjustment_type" name="adjustment_type" required>
                        <option value="increase">Increase</option>
                        <option value="decrease">Decrease</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                </div>
                <button type="submit" name="add_adjustment" class="btn btn-primary">Add Adjustment</button>
            </form>
        </div>

        <!-- Display Adjustments Table -->
        <h2>Current Adjustments</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Adjustment ID</th>
                    <th>Stock ID</th>
                    <th>Product ID</th>
                    <th>Stock Quantity</th>
                    <th>Adjustment Date</th>
                    <th>Adjustment Type</th>
                    <th>Adjustment Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['adjustment_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['stock_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['stock_quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['adjustment_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['adjustment_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['adjustment_quantity']); ?></td>
                    <td>
                        <a href="stock_adjustments.php?delete=<?php echo $row['adjustment_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</body>
</html>

<?php
$conn->close();
?>
