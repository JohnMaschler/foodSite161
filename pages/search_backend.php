<?php

// search_backend.php
include "../db/config.php";

$searched_recipes = [];

$search_term = isset($_GET["search"]) ? '%' . $_GET["search"] . '%' : '%';
$search_type = isset($_GET["search_type"]) ? $_GET["search_type"] : 'tag';

//the SQL query will change based on whether the user is searching by tag or title.
if ($search_type == 'tag') {
    $stmt = $conn->prepare("SELECT recipe_id, title, ingredients, amounts, directions, tags FROM recipes WHERE tags LIKE ? LIMIT 2");
    $stmt->bind_param("s", $search_term);
} else {
    $stmt = $conn->prepare("SELECT recipe_id, title, ingredients, amounts, directions, tags FROM recipes WHERE title LIKE ? LIMIT 2");
    $stmt->bind_param("s", $search_term);
}

$stmt->execute();
$result = $stmt->get_result();

//fetching all results
$searched_recipes = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

//output the necessary HTML here:
foreach ($searched_recipes as $recipe) {
    echo "<div class='recipe-card'>";
    echo "<h4 class='recipe-title'>";
    echo "<a href='recipe.php?recipe_id=" . htmlspecialchars($recipe["recipe_id"]) . "' class='recipe-link'>";
    echo htmlspecialchars($recipe["title"]);
    echo "</a></h4>";
    echo "<div class='recipe-tags'>";

    $tags = explode(',', $recipe["tags"]); // Split tags into an array
    foreach ($tags as $tag) {
        echo "<a href='search.php?search=" . urlencode(trim($tag)) . "&search_type=tag' class='tag-link'>" . htmlspecialchars(trim($tag)) . "</a> ";
    }

    echo "</div>";
    echo "</div>";
}


?>
