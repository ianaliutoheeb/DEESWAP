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

// Fetch users from the database
$user_sql = "SELECT user_id, username, profile_picture, contact_number FROM Users";
$user_result = $conn->query($user_sql);

// Fetch products from the database
$product_sql = "SELECT name, price, description, image_url, contact_number, product_id, email FROM Products";
$product_result = $conn->query($product_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DeeSwap</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Base styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            width: 90%;
            margin: auto;
            overflow: hidden;
        }
        h1 {
            text-align: center;
            margin: 20px 0;
            color: #333;
        }

        /* User List Section */
        .user-table {
            width: 100%;
            margin-bottom: 50px;
            border-collapse: collapse;
        }
        .user-table th, .user-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .user-table th {
            background-color: #004682;
            color: white;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        /* Product Card Section */
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .product-card {
            background-color: white;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            width: calc(33.333% - 40px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .product-card h3 {
            margin: 10px 0;
            font-size: 18px;
        }
        .product-card p {
            margin: 10px 0;
            color: #555;
        }
        .product-card .price {
            color: #4CAF50;
            font-weight: bold;
        }
        .product-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand fw-bold" href="#">DeeSwap</a>
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
        <h1>Admin Dashboard</h1>

        <!-- User List Section -->
        <h2>Users</h2>
        <table class="user-table">
            <tr>
                <th>Profile Picture</th>
                <th>Username</th>
                <th>Contact Number</th>
                <th>Action</th>
            </tr>
            <?php while ($user = $user_result->fetch_assoc()): ?>
            <tr>
                <td><img src="profileImages/<?= $user['profile_picture']; ?>" alt="Profile Picture" class="profile-img"></td>
                <td><?= $user['username']; ?></td>
                <td><?= $user['contact_number']; ?></td>
                <td>
                    <form action="delete_user.php" method="POST">
                        <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <!-- Product Cards Section -->
        <h2>Products</h2>
        <div class="product-grid">
        <?php while ($product = $product_result->fetch_assoc()) { ?>
            <div class="product-card">
                <img src="images/<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p>Contact: <?php echo htmlspecialchars($product['contact_number']); ?></p>
                <p>Email: <?php echo htmlspecialchars($product['email']); ?></p>
                
                <!-- Delete Button for Product -->
                <form action="delete_product.php" method="POST">
                    <input type="hidden" name="product_id" value ='<?php echo htmlspecialchars($product['product_id']); ?>'>
                    <button type="submit" class="delete-btn">Delete</button>
                </form>

            </div>
        <?php } ?>

        </div>
    </div>
</body>
</html>
