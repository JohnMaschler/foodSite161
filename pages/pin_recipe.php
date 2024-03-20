<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

include '../db/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['recipe_id'])) {
    $userId = $_SESSION['user_id'];
    $recipeId = $_POST['recipe_id'];

    $stmt = $conn->prepare("SELECT user_id FROM recipes WHERE recipe_id = ?");
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $stmt->bind_result($recipeUserId);
    $stmt->fetch();
    $stmt->close();

    if ($userId != $recipeUserId) {
        $stmt = $conn->prepare("INSERT INTO pinned_recipes (user_id, recipe_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE pinned_at = CURRENT_TIMESTAMP");
        $stmt->bind_param("ii", $userId, $recipeId);
        
        if ($stmt->execute()) {
            header("Location: recipe.php?recipe_id=" . $recipeId);
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>
