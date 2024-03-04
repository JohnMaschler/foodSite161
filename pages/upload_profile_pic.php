<?php
session_start();
include '../db/config.php';  // Adjust the path as necessary

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check for file upload error
    if (isset($_FILES["profilePic"]) && $_FILES["profilePic"]["error"] !== UPLOAD_ERR_OK) {
        echo "An error occurred while uploading the file.";
        // You can add error handling based on the specific error codes here
        exit;
    }

    // Assuming user_id is stored in the session during login
    if (!isset($_SESSION["user_id"])) {
        echo "User is not logged in.";
        exit;
    }

    $userId = $_SESSION["user_id"];
    $target_dir = "../assets/images/profile_pics/"; // Make sure this directory exists and is writable
    $target_file = $target_dir . basename($_FILES["profilePic"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profilePic"]["tmp_name"]);
    if($check === false) {
        echo "File is not an image.";
        exit;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        exit;
    }

    // Check file size - 5MB maximum
    if ($_FILES["profilePic"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        exit;
    }

    // Allow certain file formats
    if($imageFileType !== "jpg" && $imageFileType !== "png" && $imageFileType !== "jpeg" && $imageFileType !== "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        exit;
    }

    // Try to upload file
    if (!move_uploaded_file($_FILES["profilePic"]["tmp_name"], $target_file)) {
        echo "Sorry, there was an error uploading your file.";
        exit;
    }

    // File is uploaded successfully, now update user's profile picture in the database
    $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE user_id = ?");
    $newProfilePicPath = substr($target_file, 3); // Remove '../' to store the relative path in the database
    $stmt->bind_param("si", $newProfilePicPath, $userId);
    if($stmt->execute()){
        // Redirect back to profile page
        header("Location: profile.php");
        exit;
    } else {
        echo "There was an error updating your profile.";
        $stmt->close();
    }
} else {
    echo "Invalid request method.";
}
?>
