<?php
session_start();
include '../db/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["profilePic"]) && $_FILES["profilePic"]["error"] !== UPLOAD_ERR_OK) {
        echo "An error occurred while uploading the file.";
        exit;
    }

    if (!isset($_SESSION["user_id"])) {
        echo "User is not logged in.";
        exit;
    }

    $userId = $_SESSION["user_id"];
    $target_dir = "../assets/images/profile_pics/";
    $target_file = $target_dir . basename($_FILES["profilePic"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["profilePic"]["tmp_name"]);
    if($check === false) {
        echo "File is not an image.";
        exit;
    }

    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        exit;
    }

    if ($_FILES["profilePic"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        exit;
    }

    if($imageFileType !== "jpg" && $imageFileType !== "png" && $imageFileType !== "jpeg" && $imageFileType !== "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        exit;
    }

    if (!move_uploaded_file($_FILES["profilePic"]["tmp_name"], $target_file)) {
        echo "Sorry, there was an error uploading your file.";
        exit;
    }

    $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE user_id = ?");
    $newProfilePicPath = substr($target_file, 3);
    $stmt->bind_param("si", $newProfilePicPath, $userId);
    if($stmt->execute()){
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