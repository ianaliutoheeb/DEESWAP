<?php
    session_start();
    include './includes/db.php'; // Ensure this file sets up a MySQLi connection as $conn
    include './includes/functions.php'; // Ensure this file includes your function definitions
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['erro-message'] = htmlspecialchars($trans['InvalidUser']); 
        header("Location: ./index.php");
        exit();
    }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $location = trim($_POST['location']);
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);
    $category_id = intval($_POST['category_id']); // Get the selected category ID

    $user_id = $_SESSION['user_id'];

    // Handle file upload
    $image_url = '';
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_url']['tmp_name'];
        $fileName = $_FILES['image_url']['name'];
        $fileSize = $_FILES['image_url']['size'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Define allowed file extensions and size limit (e.g., 5MB)
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (in_array($fileExtension, $allowedExtensions)) {
            if ($fileSize <= $maxFileSize) {
                // Generate a unique name for the image
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = './images/';
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $image_url = $newFileName;
                } else {
                    $error = "There was an error uploading the image.";
                }
            } else {
                $error = "File size exceeds the 5MB limit.";
            }
        } else {
            $error = "Upload failed. Allowed file types: jpg, jpeg, png, gif.";
        }
    }

    if (!isset($error)) {
        // Insert product information into the database
        $stmt = $conn->prepare("INSERT INTO products (user_id, name, description, price, image_url, location, contact_number, email, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssis", $user_id, $name, $description, $price, $image_url, $location, $contact_number, $email, $category_id);
        $stmt->execute();

        // Redirect to the home page or another page
        header("Location: home.php");
        exit;
    }
}
// Fetch categories for the dropdown
$categories_query = "SELECT * FROM Categories";
$categories_result = $conn->query($categories_query);
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - DeeSwap</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .card {
            border: none; /* Remove default border */
            border-radius: 10px; /* Rounded corners */
        }

        .card-body {
            padding: 30px; /* Add padding for better spacing */
        }

        h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600; /* Make heading bold */
        }

    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand fw-bold" href="./home.php">DeeSwap</a>
    </nav>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center fw-bold">Add Your Product</h2>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                    <form action="product_add.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['category_id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="tel" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="form-group">
                            <label for="image_url">Image</label>
                            <input type="file" class="form-control-file" id="image_url" name="image_url">
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
