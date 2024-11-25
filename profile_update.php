<?php
session_start();
include './includes/db.php'; // Ensure this file sets up a MySQLi connection as $conn
include './includes/functions.php'; // Ensure this file includes your function definitions

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the user's current details
$stmt = $conn->prepare("SELECT profile_picture FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $location = $_POST['location'];
    $bio = $_POST['bio'];
    
    // Handle file upload
    $profile_picture = $user['profile_picture']; // Keep existing profile picture if not updated
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Define allowed file extensions
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowedExtensions)) {
            // Generate a unique name for the image
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './profileImages/';
            $dest_path = $uploadFileDir . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_picture = $newFileName;
            } else {
                // Handle file upload error
                $error = "There was an error uploading the profile picture.";
            }
        } else {
            $error = "Upload failed. Allowed file types: jpg, jpeg, png, gif.";
        }
    }

    if (!isset($error)) {
        // Prepare and execute the update statement
        $stmt = $conn->prepare("UPDATE Users SET username = ?, email = ?, contact_number = ?, location = ?, profile_picture = ?, bio = ? WHERE user_id = ?");
        $stmt->bind_param("ssssssi", $username, $email, $contact_number, $location, $profile_picture, $bio, $user_id);
        $stmt->execute();
        
        // Redirect to the profile page
        header("Location: profile.php");
        exit;
    } else {
        echo $error; // Handle the error
    }
}
?>
