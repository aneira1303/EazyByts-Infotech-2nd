<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'inventory_system';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions for adding or editing orders
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_order'])) {
        // Add new order
        $customer_name = $_POST['customer_name'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $order_date = $_POST['order_date'];

        $stmt = $conn->prepare("INSERT INTO orders (customer_name, product_id, quantity, order_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $customer_name, $product_id, $quantity, $order_date);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Order added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding order: " . $conn->error . "</div>";
        }
    } elseif (isset($_POST['edit_order'])) {
        // Edit existing order
        $order_id = $_POST['order_id'];
        $customer_name = $_POST['customer_name'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $order_date = $_POST['order_date'];

        $stmt = $conn->prepare("UPDATE orders SET customer_name=?, product_id=?, quantity=?, order_date=? WHERE order_id=?");
        $stmt->bind_param("ssisi", $customer_name, $product_id, $quantity, $order_date, $order_id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Order updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating order: " . $conn->error . "</div>";
        }
    }
}

// Handle order deletion
if (isset($_GET['delete'])) {
    $order_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id=?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Order deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting order: " . $conn->error . "</div>";
    }
}

// Fetch all orders
$result = $conn->query("SELECT * FROM orders");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
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
        <h1>Manage Orders</h1>

        <!-- Add Order Form -->
        <div class="mb-4">
            <h2>Add New Order</h2>
            <form action="orders.php" method="post">
                <div class="form-group">
                    <label for="customer_name">Customer Name</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                </div>
                <div class="form-group">
                    <label for="product_id">Product ID</label>
                    <input type="number" class="form-control" id="product_id" name="product_id" required>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                </div>
                <div class="form-group">
                    <label for="order_date">Order Date</label>
                    <input type="date" class="form-control" id="order_date" name="order_date" required>
                </div>
                <button type="submit" name="add_order" class="btn btn-primary">Add Order</button>
            </form>
        </div>

        <!-- Display Orders Table -->
        <h2>All Orders</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Product ID</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                    <td>
                        <a href="orders.php?edit=<?php echo $row['order_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="orders.php?delete=<?php echo $row['order_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php
        // Handle order editing
        if (isset($_GET['edit'])) {
            $order_id = $_GET['edit'];
            $result = $conn->query("SELECT * FROM orders WHERE order_id=$order_id");
            $order = $result->fetch_assoc();
            ?>
            <div class="mt-4">
                <h2>Edit Order</h2>
                <form action="orders.php" method="post">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                    <div class="form-group">
                        <label for="customer_name">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($order['customer_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="product_id">Product ID</label>
                        <input type="number" class="form-control" id="product_id" name="product_id" value="<?php echo htmlspecialchars($order['product_id']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($order['quantity']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="order_date">Order Date</label>
                        <input type="date" class="form-control" id="order_date" name="order_date" value="<?php echo htmlspecialchars($order['order_date']); ?>" required>
                    </div>
                    <button type="submit" name="edit_order" class="btn btn-warning">Update Order</button>
                </form>
            </div>
            <?php
        }
        ?>

    </div>
</body>
</html>

<?php
$conn->close();
?>
