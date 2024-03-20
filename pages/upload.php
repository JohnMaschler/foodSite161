<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$imagePath = '';

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $title = $_POST['title'];
    $ingredients = implode(", ", $_POST['ingredient_name']);
    $amounts = implode(", ", $_POST['ingredient_qty']);
    $directions = $_POST['directions'];
    $tags = $_POST['tags'];
    $userId = $_SESSION["user_id"];
    
    if (isset($_FILES['recipeImage']) && $_FILES['recipeImage']['error'] == 0){
        $target_directory = "../uploads/";
        $imageFileType = strtolower(pathinfo($_FILES['recipeImage']['name'], PATHINFO_EXTENSION));
        $target_file = $target_directory . uniqid() . "." . $imageFileType;

        $checkImg = getimagesize($_FILES["recipeImage"]["tmp_name"]);
        if($checkImg !== false && $_FILES["recipeImage"]["size"] <= 5000000 && in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])){
            if (move_uploaded_file($_FILES["recipeImage"]["tmp_name"], $target_file)){
                $imagePath = $target_file;
                echo $imagePath;
            }
            else{
                echo "error uploading the image";
                exit;
            }
        }
        else{
            echo "Invalid file. Only JPG, JPEG, PNG & GIF files are allowed and the file must be under 5MB.";
            exit;
        }

    }

    $stmt = $conn->prepare("INSERT INTO recipes (user_id, title, ingredients, amounts, directions, tags, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $userId, $title, $ingredients, $amounts, $directions, $tags, $imagePath);

    if ($stmt->execute()){
        header("Location: profile.php");
        exit;
    } else {
        echo "Recipe failed to upload: " . htmlspecialchars($conn->error);
    }
    $stmt->close();
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Recipe - Foreign Flavors</title>
    <link rel="stylesheet" href="../assets/css/upload.css">
    <script defer src="../assets/js/upload.js"></script>
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="explore.php">Explore</a></li>
            <li><a href="search.php">Search</a></li>
            <li><a href="upload.php">Upload Recipe</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </nav>
    <form id="recipe-form" class="recipe-form" action="upload.php" method="post" enctype="multipart/form-data">
        <label for="title">Recipe Title:</label>
        <input type="text" id="title" name="title" required>

        <div id="ingredients-list">
            <div class="ingredient">
                <button type="button" onclick="addIngredient()">Add Ingredient</button>
            </div>
        </div>

        <label for="directions">Cooking Directions:</label>
        <textarea id="directions" name="directions" rows="4" required></textarea>

        <label for="tags">Tags:</label>
        <input type="text" id="tags" name="tags" placeholder="e.g., Vegan, Dessert, Spicy">

        <span for="recipeImage">Upload a picture of your meal here :)</span>
        <input type="file" id="recipeImage" name="recipeImage" accept="image/*">

        <button type="submit">Post</button>
    </form>
</body>
</html>
