<?php
session_start();
include '../db/config.php';

// Ensure the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $title = $_POST['title'];
    // Ingredients and quantities are sent as arrays
    $ingredients = implode(", ", $_POST['ingredient_name']); // This will create a comma-separated string of ingredients
    $amounts = implode(", ", $_POST['ingredient_qty']); // This will create a comma-separated string of quantities
    $directions = $_POST['directions'];
    $tags = $_POST['tags'];
    $userId = $_SESSION["user_id"]; // User id is stored in session

    // Prepare an INSERT statement to insert info into recipes table
    $stmt = $conn->prepare("INSERT INTO recipes (user_id, title, ingredients, amounts, directions, tags) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $userId, $title, $ingredients, $amounts, $directions, $tags);

    // Attempt to execute the statement
    if ($stmt->execute()){
        // Redirect to the profile page upon success
        header("Location: profile.php");
        exit;
    } else {
        // If execution failed, output an error message
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
    <form id="recipe-form" class="recipe-form" action="upload.php" method="post">
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

        <button type="submit">Post</button>
    </form>
</body>
</html>
