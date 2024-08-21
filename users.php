<?php
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'inventory_system';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user operations
$message = '';

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password
        $role = $_POST['role'];

        $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
        if ($conn->query($query) === TRUE) {
            $message = "User added successfully.";
        } else {
            $message = "Error adding user: " . $conn->error;
        }
    } elseif ($action === 'edit') {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        $query = "UPDATE users SET username='$username', email='$email', role='$role' WHERE id='$user_id'";
        if ($conn->query($query) === TRUE) {
            $message = "User updated successfully.";
        } else {
            $message = "Error updating user: " . $conn->error;
        }
    } elseif ($action === 'delete') {
        $user_id = $_POST['user_id'];
        $query = "DELETE FROM users WHERE id='$user_id'";
        if ($conn->query($query) === TRUE) {
            $message = "User deleted successfully.";
        } else {
            $message = "Error deleting user: " . $conn->error;
        }
    }
}

// Fetch all users
$query = "SELECT * FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        table {
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .message {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Manage Users</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-info message">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Add User Form -->
    <form method="POST" class="mb-4">
        <input type="hidden" name="action" value="add">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="user">User</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add User</button>
    </form>

    <!-- Users List Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <td>
                        <!-- Edit Form -->
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="username" value="<?php echo $row['username']; ?>" class="form-control" required>
                            <input type="email" name="email" value="<?php echo $row['email']; ?>" class="form-control" required>
                            <select name="role" class="form-control" required>
                                <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                <option value="staff" <?php if ($row['role'] == 'staff') echo 'selected'; ?>>Staff</option>
                                <option value="user" <?php if ($row['role'] == 'user') echo 'selected'; ?>>User</option>
                            </select>
                            <button type="submit" class="btn btn-warning btn-sm mt-2">Update</button>
                        </form>

                        <!-- Delete Form -->
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm mt-2">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

