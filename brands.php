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

// Add brand
if (isset($_POST['add_brand'])) {
    $brand_name = $_POST['brand_name'];

    // Image upload
    $target_dir = "uploads/";
    $brand_image = $target_dir . basename($_FILES["brand_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($brand_image, PATHINFO_EXTENSION));

    // Check if image file is a real image or fake image
    $check = getimagesize($_FILES["brand_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($brand_image)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["brand_image"]["size"] > 500000) { // 500KB limit
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["brand_image"]["tmp_name"], $brand_image)) {
            // Insert brand with image path
            $sql = "INSERT INTO brands (brand_name, brand_image) VALUES ('$brand_name', '$brand_image')";
            if ($conn->query($sql) === TRUE) {
                echo "Brand added successfully!";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Edit brand
if (isset($_POST['edit_brand'])) {
    $brand_id = $_POST['brand_id'];
    $brand_name = $_POST['brand_name'];

    // Check if a new image is uploaded
    if (!empty($_FILES["brand_image"]["name"])) {
        $target_dir = "uploads/";
        $brand_image = $target_dir . basename($_FILES["brand_image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($brand_image, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["brand_image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        if ($_FILES["brand_image"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1 && move_uploaded_file($_FILES["brand_image"]["tmp_name"], $brand_image)) {
            $sql = "UPDATE brands SET brand_name = '$brand_name', brand_image = '$brand_image' WHERE id = $brand_id";
        } else {
            echo "Error uploading the image.";
        }
    } else {
        $sql = "UPDATE brands SET brand_name = '$brand_name' WHERE id = $brand_id";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Brand updated successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Delete brand
if (isset($_GET['delete'])) {
    $brand_id = $_GET['delete'];

    // Delete the image file associated with the brand
    $sql = "SELECT brand_image FROM brands WHERE id = $brand_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if (file_exists($row['brand_image'])) {
        unlink($row['brand_image']);
    }

    $sql = "DELETE FROM brands WHERE id = $brand_id";
    if ($conn->query($sql) === TRUE) {
        echo "Brand deleted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch brands for display
$sql = "SELECT * FROM brands";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Brands</title>
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

.ui-tooltip {
    font-size: 10pt;
    padding: 1px 1px;
    max-width: 300px;
    color: black;
    font-family: myFont;
    border-radius: 20px;
    box-shadow: 0 0 7px black;
    background-color: #ffcccc; /* Light red */
}

div {
    font-family: myFont;
    font-size: 20px;
    border-radius: 5px;
    padding: 20px;
    text-align: center;
    background-color: #ffe6e6; /* Very light red */
}

.warning {
    color: darkred;
    margin: auto;
}

#small_icon {
    width: 4.5%;
    height: auto;
    position: absolute;
    top: 20px;
    left: 20px;
}

#choice_icon {
    width: 30%;
    height: auto;
}

#profile {
    width: 12%;
    height: auto;
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

.content {
    height: 80px;
}

.content_3 {
    font-size: 30px;
}

input[type=text], select {
    font-family: myFont;
    width: 40%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ff6666; /* Medium red */
    border-radius: 4px;
    box-sizing: border-box;
    background-color: #ffe6e6; /* Very light red */
}

input[type=password], select {
    font-family: myFont;
    width: 40%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ff6666; /* Medium red */
    border-radius: 4px;
    box-sizing: border-box;
    background-color: #ffe6e6; /* Very light red */
}

input[type=button] {
    font-family: myFont;
    width: 40%;
    background-color: #ff3333; /* Bright red */
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type=button]:hover {
    background-color: #cc0000; /* Darker red */
}

input[type=submit] {
    font-family: myFont;
    width: 40%;
    background-color: #ff3333; /* Bright red */
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type=submit]:hover {
    background-color: #cc0000; /* Darker red */
}

#small_button {
    margin-right: 220px;
}

#small_button_back {
    width: 15%;
    margin-top: 180px;
    float: left;
}

#small_button_last {
    width: 15%;
    margin-top: 0px;
    margin-bottom: 0px;
    float: left;
}

#join_button {
    margin: 15px 200px;
}

#submit_button {
    margin: auto;
}

.users-list {
    width: 100%;
}

.chat_button {
    font-family: Arial;
    width: 35px;
    height: 35px;
    padding: 0;
    float: right;
    border: 1px solid #ff6666; /* Medium red */
    border-radius: 5px;
    background-color: #ff9999; /* Light red */
    cursor: pointer;
}

.chat_button:hover {
    background: #ffcccc; /* Light red hover */
}

.column {
    float: left;
    font-size: 15px;
    width: 50%;
    padding: 15px;
}

.row:after {
    font-size: 20px;
    content: "";
    display: table;
    clear: both;
}

table {
    font-family: myFont;
    border-collapse: collapse;
    width: 60%;
    position: relative;
    margin-left: 300px;
}

th, td {
    border: 1px solid #ff6666; /* Medium red */
    text-align: left;
    padding: 4px;
}

tr {
    background-color: #ffe6e6; /* Very light red */
}

th {
    background-color: #ff3333; /* Bright red */
    color: white;
}

.users {
    width: 25%;
    float: left;
}

.texts {
    width: 75%;
    float: left;
    background-color: #ffcccc; /* Light red */
}

.log-out {
    font-family: myFont;
    width: 40%;
    background-color: #ff3333; /* Bright red */
    color: white;
    padding: 14px 20px;
    position: absolute;
    bottom: 30px;
    left: 10px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.log-out:hover {
    background-color: #cc0000; /* Darker red */
}

.log-out a {
    text-decoration: none;
    color: #fff;
}

#message {
    width: 100%;
    border-color: #ff6666; /* Medium red */
}

#msg {
    width: 100%;
    border-color: #ff6666; /* Medium red */
    font-family: myFont;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ff6666; /* Medium red */
    border-radius: 4px;
    box-sizing: border-box;
    resize: none;
    background-color: #ffe6e6; /* Very light red */
}

.container_messages {
    border: 2px solid #ff6666; /* Medium red */
    background-color: #ff9999; /* Light red */
    border-radius: 5px;
    padding: auto;
    margin: 10px 0;
    text-align: left;
}

.container_messages img {
    display: inline;
    position: relative;
    bottom: 10px;
    float: left;
    max-width: 60px;
    padding: 0px;
    width: 100%;
    margin-right: 20px;
    margin-bottom: 50px;
    border-radius: 50%;
}

.container_messages img.right {
    float: right;
    margin-left: 20px;
    margin-right: 0;
}

    </style>
</head>
<body>

    <h2>Manage Brands</h2>

    <!-- Add Brand Form -->
    <form method="POST" action="brands.php" enctype="multipart/form-data">
        <input type="text" name="brand_name" placeholder="Enter Brand Name" required>
        <input type="file" name="brand_image" required>
        <button type="submit" name="add_brand">Add Brand</button>
    </form>

    <h3>Existing Brands</h3>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Brand Name</th>
                <th>Brand Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['brand_name']; ?></td>
                <td><img src="<?php echo $row['brand_image']; ?>" width="100" alt="Brand Image"></td>
                <td>
                    <!-- Edit Brand Form -->
                    <form method="POST" action="brands.php" enctype="multipart/form-data" style="display:inline;">
                        <input type="hidden" name="brand_id" value="<?php echo $row['id']; ?>">
                        <input type="text" name="brand_name" value="<?php echo $row['brand_name']; ?>" required>
                        <input type="file" name="brand_image">
                        <button type="submit" name="edit_brand">Edit</button>
                    </form>

                    <!-- Delete Brand -->
                    <a href="brands.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this brand?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>

<?php
$conn->close();
?>
