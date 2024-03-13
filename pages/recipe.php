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
    $stmt = $conn->prepare("SELECT r.user_id, r.title, r.ingredients, r.amounts, r.directions, r.tags, r.posted_at, r.image_path, u.username FROM recipes r LEFT JOIN users u ON r.user_id = u.user_id WHERE r.recipe_id = ?");
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();
    $stmt->close();
} else {
    //redirect to another page or show an error if the recipe_id is not set or not valid
    header("location: profile.php");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recipe['title']); ?> - Recipe</title>
    <link rel="stylesheet" href="../assets/css/recipe.css">
    <script defer src="../assets/js/recipe.js"></script>
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
    <div class="main-container">
    <!-- the recipe array has been filled by the PHP code above -->
    <article class="recipe">
        <h1 class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></h1>
        <?php if(!empty($recipe['image_path'])): ?>
            <img src="<?php echo htmlspecialchars($recipe['image_path']); ?>" alt="Image of <?php echo htmlspecialchars($recipe['title']); ?>" class="recipe-image">
        <?php endif; ?>
        <p class="author-date">Posted by <?php echo htmlspecialchars($recipe['username']); ?> on <?php echo date('F j, Y', strtotime($recipe['posted_at'])); ?></p>
        <!-- Inside your <article class="recipe"> after the author-date paragraph -->
        <?php if ($_SESSION['user_id'] != $recipe['user_id']): ?>
            <form method="post" action="pin_recipe.php">
                <input type="hidden" name="recipe_id" value="<?php echo $recipeId; ?>">
                <button id="pin" type="submit" name="pin_recipe">Pin Recipe</button>
            </form>
        <?php endif; ?>

        <section class="ingredients">
            <h2>Ingredients</h2>
            <ul>
                <?php
                $ingredients = explode(',', $recipe['ingredients']);
                $amounts = explode(',', $recipe['amounts']);
                foreach ($ingredients as $index => $ingredient) {
                    echo "<li>" . htmlspecialchars($amounts[$index]) . " of " . htmlspecialchars($ingredient) . "</li>";
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
                    echo "<a href='search.php?search=" . urlencode(trim($tag)) . "&search_type=tag' class='tag-link'>" . htmlspecialchars(trim($tag)) . "</a>";
                }                
            ?></p>
        </section>
    </article>
    </div>
</body>
</html>
