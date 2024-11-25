<?php
session_start();
include './includes/db.php'; // Ensure this file sets up a MySQLi connection as $conn
include './includes/functions.php'; // Ensure this file includes your function definitions

if (!isset($_SESSION['user_id'])) {
    $_SESSION['erro-message'] = htmlspecialchars($trans['InvalidUser']); 
    header("Location: ./index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch products for the logged-in user
$query = "SELECT * FROM Products WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("Error: " . $conn->error); // Handle query errors
}

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Product Listings - DeeSwap</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand fw-bold" href="home.php">DeeSwap</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="product_add.php">Add New Product</a>
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

<div class="container mt-5">
    <h2 class="text-center fw-bold">My Product Listings</h2><hr>
    
    <?php if (empty($products)): ?>
        <div class="alert alert-info text-center" role="alert">
            You have not uploaded any products yet. <a href="product_add.php" class="btn btn-primary">Add a Product</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm">
                        <img src="<?= !empty($product['image_url']) ? './images/' . htmlspecialchars($product['image_url']) : 'path/to/default/image.jpg' ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                            <p class="card-text"><strong>Price:</strong> $<?= htmlspecialchars($product['price']) ?></p>
                            <a href="product_view.php?id=<?= $product['product_id'] ?>" class="btn btn-primary">View Product</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
