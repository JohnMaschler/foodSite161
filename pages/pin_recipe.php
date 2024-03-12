<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

include '../db/config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['recipe_id'])) {
    $userId = $_SESSION['user_id'];
    $recipeId = $_POST['recipe_id'];

    // Prevent users from pinning their own recipe
    $stmt = $conn->prepare("SELECT user_id FROM recipes WHERE recipe_id = ?");
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $stmt->bind_result($recipeUserId);
    $stmt->fetch();
    $stmt->close();

    if ($userId != $recipeUserId) {
        // Insert the pin
        $stmt = $conn->prepare("INSERT INTO pinned_recipes (user_id, recipe_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE pinned_at = CURRENT_TIMESTAMP");
        $stmt->bind_param("ii", $userId, $recipeId);
        
        if ($stmt->execute()) {
            // Success, redirect back to the recipe page or to the profile
            header("Location: recipe.php?recipe_id=" . $recipeId);
        } else {
            // Handle errors here, e.g., recipe already pinned, etc.
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>
