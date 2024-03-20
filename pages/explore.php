<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

include '../db/config.php';

$limit = 15;

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$stmt = $conn->prepare("SELECT r.recipe_id, r.title, r.ingredients, r.tags, u.username, u.profile_pic, r.image_path FROM recipes r LEFT JOIN users u ON r.user_id = u.user_id ORDER BY r.posted_at DESC LIMIT ?, ?");
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();

$recipes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Recipes</title>
    <link rel="stylesheet" href="../assets/css/explore.css">
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
    
    <div class="recipe-feed">
    <?php foreach ($recipes as $recipe): ?>
        <div class="recipe-card">
            <h3 class="recipe-title"><?= htmlspecialchars($recipe['title']) ?></h3>
            <div class="recipe-user-info">
                <img src="<?= htmlspecialchars('/csen161finalproj/' . ($recipe['profile_pic'] ?: 'defaultProfilePic.png')) ?>" alt="Profile pic" class="mini-profile-pic">
                <a href="profile.php?user=<?= urlencode($recipe['username']) ?>" class="recipe-user"><?= htmlspecialchars($recipe['username']) ?></a>
            </div>
            <?php if(!empty($recipe['image_path'])): ?>
                <img src="<?= htmlspecialchars($recipe['image_path']) ?>" alt="Image of <?= htmlspecialchars($recipe['title']) ?>" class="recipe-image">
            <?php endif; ?>
            <div class="recipe-tags">
                <?php foreach(array_slice(explode(',', $recipe['tags']), 0, 3) as $tag): ?>
                    <a href="search.php?search=<?= urlencode(trim($tag)) ?>&search_type=tag" class="tag-link"><?= htmlspecialchars(trim($tag)) ?></a>
                <?php endforeach; ?>
            </div>

            <p class="recipe-ingredients">Includes: <?= implode(', ', array_slice(explode(',', $recipe['ingredients']), 0, 2)) ?></p>
            <a href="recipe.php?recipe_id=<?= $recipe['recipe_id'] ?>" class="see-more-link">See More</a>
        </div>
    <?php endforeach; ?>
    </div>


    <div class="load-more">
        <?php if (count($recipes) < $limit): ?>
            <p>That's all the posts we have for now.</p>
        <?php else: ?>
            <a href="explore.php?page=<?= $page + 1 ?>" class="load-more-button">Load More</a>
        <?php endif; ?>
    </div>
</body>
</html>
