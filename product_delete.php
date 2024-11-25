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

// Optional: Check if user is authorized to delete the product
// (This requires adding user authentication logic based on your requirements)

// Fetch the product details to get the image URL
$stmt = $conn->prepare("SELECT image_url FROM Products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if ($product) {
    // Delete the image file associated with the product
    if (!empty($product['image_url'])) {
        $imagePath = './images/' . $product['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // Delete the image file
        }
    }

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM Products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        // Redirect to the product list
        header("Location: product_list.php");
        exit;
    } else {
        echo "Error: " . $conn->error; // Handle query errors
    }
} else {
    // Product not found
    header("Location: product_list.php");
    exit;
}
?>
