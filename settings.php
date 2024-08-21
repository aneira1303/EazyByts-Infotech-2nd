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

// Fetch current settings
$query = "SELECT * FROM settings WHERE id = 1 LIMIT 1";
$result = $conn->query($query);
$settings = $result->fetch_assoc();

// Update settings
$message = '';
if (isset($_POST['update_settings'])) {
    $company_name = $_POST['company_name'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $vat_percentage = $_POST['vat_percentage'];
    $service_charge = $_POST['service_charge'];

    if ($settings) {
        // Update existing settings
        $query = "UPDATE settings SET 
                    company_name='$company_name',
                    address='$address',
                    contact_number='$contact_number',
                    email='$email',
                    vat_percentage='$vat_percentage',
                    service_charge='$service_charge',
                    updated_at=CURRENT_TIMESTAMP 
                  WHERE id=1";
    } else {
        // Insert new settings if none exist
        $query = "INSERT INTO settings (company_name, address, contact_number, email, vat_percentage, service_charge) 
                  VALUES ('$company_name', '$address', '$contact_number', '$email', '$vat_percentage', '$service_charge')";
    }

    if ($conn->query($query) === TRUE) {
        $message = "Settings updated successfully.";
    } else {
        $message = "Error updating settings: " . $conn->error;
    }

    // Refresh settings
    $result = $conn->query("SELECT * FROM settings WHERE id = 1 LIMIT 1");
    $settings = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            max-width: 700px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Manage Company Settings</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-info message">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Company Name</label>
            <input type="text" name="company_name" class="form-control" value="<?php echo $settings['company_name'] ?? ''; ?>" required>
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="3" required><?php echo $settings['address'] ?? ''; ?></textarea>
        </div>
        <div class="form-group">
            <label>Contact Number</label>
            <input type="text" name="contact_number" class="form-control" value="<?php echo $settings['contact_number'] ?? ''; ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo $settings['email'] ?? ''; ?>" required>
        </div>
        <div class="form-group">
            <label>VAT Percentage (%)</label>
            <input type="number" name="vat_percentage" class="form-control" step="0.01" value="<?php echo $settings['vat_percentage'] ?? '0.00'; ?>" required>
        </div>
        <div class="form-group">
            <label>Service Charge (%)</label>
            <input type="number" name="service_charge" class="form-control" step="0.01" value="<?php echo $settings['service_charge'] ?? '0.00'; ?>" required>
        </div>
        <button type="submit" name="update_settings" class="btn btn-primary">Update Settings</button>
    </form>
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
