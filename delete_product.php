<?php
include './includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM Products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    // Execute and redirect back to the admin dashboard
    if ($stmt->execute()) {
        header("Location: adminDashboard.php");
    } else {
        echo "Error deleting product: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
