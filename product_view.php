<?php
session_start();
include './includes/db.php'; // Ensure this file sets up a MySQLi connection as $conn
include './includes/functions.php'; // Ensure this file includes your function definitions

// Check if the product ID is provided and valid
$product_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$product_id) {
    header("Location: index.php");
    exit;
}

// Fetch the product details from the database
$query = "SELECT * FROM Products WHERE product_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Database query failed: " . $conn->error);
}
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Product not found
    header("Location: index.php");
    exit;
}

$product = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 30px;
        }
        .card {
            width: 400px;
            border: none; /* Removes default border */
            border-radius: 10px; 
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease; 
            margin-bottom: 20px;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        }
        .card-img-top {
            height: 250px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .card-body h5 {
            font-weight: bold;
            color: #333;
        }
        .product-info p {
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        .product-info strong {
            color: #007bff;
        }
        .btn {
            margin-top: 20px;
        }
        .btn-warning {
            background-color: #f0ad4e;
            border: none;
        }
        .btn-danger {
            background-color: #d9534f;
            border: none;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .product-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .product-header h2 {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="product-header mb-3">
            <h2>Product Details</h2>
            <a href="home.php" class="btn btn-secondary">Back to Home</a>
        </div>
        <div class="card mb-4">
            <?php if (!empty($product['image_url'])): ?>
                <img src="./images/<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
            <?php else: ?>
                <img src="./images/default.png" class="card-img-top" alt="Default Image">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h5>
                <div class="product-info">
                    <p class="card-text"><?= nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')) ?></p>
                    <p class="card-text"><strong>Price:</strong> $<?= htmlspecialchars(number_format($product['price'], 2), ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="card-text"><strong>Location:</strong> <?= htmlspecialchars($product['location'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="card-text"><strong>Contact:</strong> <?= htmlspecialchars($product['contact_number'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($product['email'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <?php if (isLoggedIn() && $_SESSION['user_id'] == $product['user_id']): ?>
                    <a href="product_edit.php?id=<?= htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-warning">Edit Product</a>
                    <a href="product_delete.php?id=<?= htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete Product</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
