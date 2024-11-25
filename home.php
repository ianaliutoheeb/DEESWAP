<?php
session_start();
include './includes/db.php'; 
include './includes/functions.php'; 
 // Check if the user is logged in
 if (!isset($_SESSION['user_id'])) {
    $_SESSION['erro-message'] = htmlspecialchars($trans['InvalidUser']); 
    header("Location: ./index.php");
    exit();
}

// Get categories for the dropdown
$categories_query = "SELECT * FROM Categories";
$categories_result = $conn->query($categories_query);
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

// Handle search and category filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Prepare query with optional filters
$query = "SELECT * FROM Products WHERE 1=1";
$params = [];

// Apply search filter
if ($search) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Apply category filter
if ($category_id > 0) {
    $query .= " AND category_id = ?";
    $params[] = $category_id;
}


    // Fetch products to display on the home page
    $query = "SELECT * FROM Products ORDER BY created_at DESC LIMIT 10";
    $result = $conn->query($query);

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
    <title>User Dashboard - DeeSwap</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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

    <section class="hero-section">
        <div class="container">
            <h1 id="hero-text-h1">Welcome to DeeSwap</h1>
            <h2 id="hero-text">Where Every Item Finds a New Home!</h2>
            <p id="hero-text" class="d-lg-block d-none">Find the best deals on used products, or sell your own items today!</p>
            <?php if (isLoggedIn()): ?>
            <a href="product_add.php" class="btn btn-danger btn-lg">Add New Product</a>
            <a href="product_list.php" class="btn btn-warning btn-lg">My Products</a>
            <?php else: ?>
            <!-- <a href="./views/register.php" class="btn btn-danger btn-lg">Get Started</a> -->
            <?php endif; ?>

            <form action="index.php" method="get" class="mt-4 mb-1" >
            <div class="form-row col-6 mb-2 rounded" id="searchandfilter">
                <div class="col-12">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </div>
            </div>              
        </form>
        </div>
    </section>
    
    <div class="container">
        <h1 class="text-center mt-5 mb-4 fw-bold">Browse by Content Type</h1>
        <!-- Search and Filters -->
         <div class="row justify-content-center" id="category">
            <?php foreach ($categories as $category): ?>
                <div class="col-2">
                    <a class="text-dark" href="index.php?category_id=<?= htmlspecialchars($category['category_id']) ?>">
                        <div class="card pt-2">
                            <h5 class="text-center">
                            <?= htmlspecialchars($category['name']) ?>
                            </h5>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
         </div><hr class=" mb-4">

        <!-- Products Section -->
        <h1 class="mt-5 mb-5 text-center fw-bold">Products Lists</h1>
        <div class="row">
            <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    No products found. <?= isset($search) ? 'Try a different search or category.' : 'Be the first to upload a product!' ?>
                </div>
            </div>
            <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-3">
                    <div class="card mb-4 shadow-sm">
                        <img src="<?= !empty($product['image_url']) ? './images/' . htmlspecialchars($product['image_url']) : 'path/to/default/image.jpg' ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                            <p class="card-text"><strong>Price:</strong> $<?= htmlspecialchars($product['price']) ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="views/product_view.php?id=<?= htmlspecialchars($product['product_id']) ?>" class="btn btn-primary">View Product</a>
                                <a href="https://wa.me/<?php echo htmlspecialchars($product['contact_number']); ?>?text=I'm%20interested%20in%20your%20product%20<?php echo urlencode($product['name']); ?>" class="btn btn-success">
                                    <i class="bi bi-cart4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
