<?php
session_start();

//check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

include '../db/config.php';

//fetch recipe information based on the recipe_id passed in the URL
if (isset($_GET['recipe_id']) && is_numeric($_GET['recipe_id'])) {
    $recipeId = $_GET['recipe_id'];
    $stmt = $conn->prepare("SELECT r.title, r.ingredients, r.amounts, r.directions, r.tags, r.posted_at, u.username FROM recipes r LEFT JOIN users u ON r.user_id = u.user_id WHERE r.recipe_id = ?");
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();
    $stmt->close();
} else {
    //redirect to another page or show an error if the recipe_id is not set or not valid
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recipe['title']); ?> - Recipe</title>
    <link rel="stylesheet" href="../assets/css/recipe.css">
</head>
<body>
    <!-- the recipe array has been filled by the PHP code above -->
    <article class="recipe">
        <h1 class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></h1>
        <p class="author-date">Posted by <?php echo htmlspecialchars($recipe['username']); ?> on <?php echo date('F j, Y', strtotime($recipe['posted_at'])); ?></p>
        <section class="ingredients">
            <h2>Ingredients</h2>
            <ul>
                <?php
                $ingredients = explode(',', $recipe['ingredients']);
                $amounts = explode(',', $recipe['amounts']);
                foreach ($ingredients as $index => $ingredient) {
                    echo "<li>" . htmlspecialchars($amounts[$index]) . " " . htmlspecialchars($ingredient) . "</li>";
                }
                ?>
            </ul>
        </section>
        <section class="directions">
            <h2>Directions</h2>
            <p><?php echo nl2br(htmlspecialchars($recipe['directions'])); ?></p>
        </section>
        <section class="tags">
            <h2>Tags</h2>
            <p><?php
                $tags = explode(',', $recipe['tags']);
                foreach ($tags as $tag) {
                    echo "<span class='tag'>" . htmlspecialchars(trim($tag)) . "</span> ";
                }
            ?></p>
        </section>
    </article>
</body>
</html>
