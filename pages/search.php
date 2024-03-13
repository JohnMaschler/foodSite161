<?php
session_start();
include "../db/config.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$searched_recipes = [];

$search_term = '%' . (isset($_GET["search"]) ? $_GET["search"] : '') . '%';
$search_type = isset($_GET["search_type"]) ? $_GET["search_type"] : 'tag';

if ($search_type == 'tag') {
    $stmt = $conn->prepare("SELECT recipe_id, title, ingredients, amounts, directions, tags FROM recipes WHERE tags LIKE ?");
} else {
    $stmt = $conn->prepare("SELECT recipe_id, title, ingredients, amounts, directions, tags FROM recipes WHERE title LIKE ?");
}

$stmt->bind_param("s", $search_term);
$stmt->execute();
$result = $stmt->get_result();
$searched_recipes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - Foreign Flavors</title>
    <link rel="stylesheet" href="../assets/css/search.css">
    <!-- <link rel="stylesheet" href="../assets/css/styles.css"> -->
    <script defer src="../assets/js/search.js"></script>
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
    <div class="search-container">
        <form onsubmit="performSearch(); return true;">
            <input type="text" id="search-input" placeholder="Search recipes..." name="search" oninput="performSearch()">
            <div>
                <input type="radio" id="tag" name="search_type" value="tag" checked onchange="performSearch()">
                <label for="tag">Tag</label>
                <input type="radio" id="title" name="search_type" value="title">
                <label for="title">Title</label>
            </div>
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Results will be displayed here -->

    <div id="search-results">
        <h3>My Recipes</h3>
        <div class="recipes-container">
            <?php foreach ($searched_recipes as $recipe): ?>
                <div class="recipe-card">
                    <h4 class="recipe-title">
                        <a href="recipe.php?recipe_id=<?php echo $recipe["recipe_id"]; ?>" class="recipe-link">
                            <?php echo htmlspecialchars($recipe["title"]); ?>
                        </a>
                    </h4>
                    <div class="recipe-tags">
                        <?php
                        $tags = explode(',', $recipe["tags"]); //split tags into an array
                        foreach ($tags as $tag) {
                            echo "<a href='search.php?search=" . urlencode(trim($tag)) . "&search_type=tag' class='tag-link'>" . htmlspecialchars(trim($tag)) . "</a>";
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
