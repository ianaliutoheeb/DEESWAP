<?php
session_start();
include './includes/db.php'; // Ensure this file sets up a MySQLi connection as $conn
include './includes/functions.php'; // Ensure this file includes your function definitions

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: product_list.php");
    exit;
}

$product_id = intval($_GET['id']);

// Initialize $product
$product = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];

    // Handle file upload
    $image_url = $product['image_url']; // Default to existing image if no new image is uploaded
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_url']['tmp_name'];
        $fileName = $_FILES['image_url']['name'];
        $fileSize = $_FILES['image_url']['size'];
        $fileType = $_FILES['image_url']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Define allowed file extensions
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowedExtensions)) {
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
            $error = "Upload failed. Allowed file types: jpg, jpeg, png, gif.";
        }
    }

    if (!isset($error)) {
        // Prepare and execute the update statement
        $stmt = $conn->prepare("UPDATE Products SET name = ?, description = ?, price = ?, image_url = ?, location = ?, contact_number = ?, email = ? WHERE product_id = ?");
        $stmt->bind_param("ssissssi", $name, $description, $price, $image_url, $location, $contact_number, $email, $product_id);
        $stmt->execute();

        // Redirect to the product list
        header("Location: product_list.php");
        exit;
    }
}

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM Products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: product_list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container {
            margin-top: 30px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 30px;
        }
        .btn{
            background-color: #004682;
            color:#fff; 
        }
        .btn-back {
            background-color: #004682;
            margin-bottom: 20px;
        }
        .btn-primary {
            margin-top: 20px;
        }
        .form-group img {
            margin-top: 10px;
            max-width: 100px;
            height: auto;
        }
        .product-header h2 {
            color: #004682;
            font-weight: bold;
        }
    </style>
</head>
<body>
     <!-- Navbar -->
     <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand fw-bold" href="index.php">DeeSwap</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="./login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./register.php">Register</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <a href="product_list.php" class="btn btn-secondary btn-back">Back to Product List</a>
        <div class="card">
            <div class="card-body">
                <div class="product-header">
                    <h2>Edit Product</h2>
                </div>
                <form action="product_edit.php?id=<?= $product_id ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="price">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image_url">Image</label>
                        <input type="file" class="form-control" id="image_url" name="image_url">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="./images/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="form-group mb-3">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($product['location']) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?= htmlspecialchars($product['contact_number']) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($product['email']) ?>" required>
                    </div>
                    <button type="submit" class="btn">Update Product</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
