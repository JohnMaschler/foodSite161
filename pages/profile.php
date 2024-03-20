<?php
session_start();



if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

include '../db/config.php';

$user_recipes = [];

$profileUsername = isset($_GET['user']) ? $_GET['user'] : '';
$userId = $_SESSION["user_id"];

if ($profileUsername != '') {
    $stmt = $conn->prepare("SELECT user_id, username, profile_pic FROM users WHERE username = ?");
    $stmt->bind_param("s", $profileUsername);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $userId = $user['user_id'];
        $username = $user['username'];
        $profilePic = $user['profile_pic'];
    } else {
        echo "No user found.";
        exit;
    }
    $stmt->close();
} else {
    $stmt = $conn->prepare("SELECT username, profile_pic FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($username, $profilePic);
    $stmt->fetch();
    $stmt->close();
}

$stmt = $conn->prepare("SELECT recipe_id, title, ingredients, amounts, directions, tags FROM recipes WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$user_recipes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$pinned_stmt = $conn->prepare("
    SELECT r.recipe_id, r.title, r.ingredients, r.amounts, r.directions, r.tags
    FROM pinned_recipes pr
    JOIN recipes r ON pr.recipe_id = r.recipe_id
    WHERE pr.user_id = ?
");
$pinned_stmt->bind_param("i", $userId);
$pinned_stmt->execute();
$pinned_result = $pinned_stmt->get_result();

$pinned_recipes = $pinned_result->fetch_all(MYSQLI_ASSOC);
$pinned_stmt->close();

$total_pins = 0;

$pinned_count = $conn->prepare("
    select sum(pinned_count) as total_pins
    from (
        select recipes.recipe_id, COUNT(pinned_recipes.recipe_id) as pinned_count
        from recipes
        left join pinned_recipes on recipes.recipe_id = pinned_recipes.recipe_id
        where recipes.user_id = ?
        group by recipes.recipe_id
    ) as pin_counts
");
$pinned_count->bind_param("i", $userId);
$pinned_count->execute();
$resultCount = $pinned_count->get_result();
if ($row = $resultCount->fetch_assoc()){
    $total_pins = $row['total_pins'];
}
$pinned_count->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Foreign Flavors</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
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

    <main class="profile-content">
        <section class="user-info">
            <img src="/csen161finalproj/<?php echo htmlspecialchars($profilePic); ?>" class="profile-pic">
            <h2 class="username"><?php echo htmlspecialchars($username); ?></h2>
            
            <button onclick="document.getElementById('profile-pic-input').click()">Change Profile Picture</button>

            <h4>Users have saved <?php echo htmlspecialchars($total_pins ?? '0')?> of your recipes</h4>
            <br />

            <hr />
            
            <form action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
                <input type="file" name="profilePic" id="profile-pic-input" style="display:none;" onchange="this.form.submit()" accept="image/*">
                <input type="hidden" name="userId" value="<?php echo $userId; ?>">
            </form>
        </section>

        <div class="recipes-section">
            <section class="user-recipes">
                <h3>My Recipes</h3>
                <div class="recipes-container">
                    <?php foreach ($user_recipes as $recipe): ?>
                        <div class="recipe-card">
                            <h4 class="recipe-title">
                                <a href="recipe.php?recipe_id=<?php echo $recipe["recipe_id"]; ?>" class="recipe-link">
                                    <?php echo htmlspecialchars($recipe["title"]); ?>
                                </a>
                            </h4>
                            <div class="recipe-tags">
                                <?php
                                $tags = explode(',', $recipe["tags"]);
                                foreach ($tags as $tag) {
                                    echo "<a href='search.php?search=" . urlencode(trim($tag)) . "&search_type=tag' class='tag-link'>" . htmlspecialchars(trim($tag)) . "</a>";
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <section class="pinned-recipes">
                <h3>Saved Recipes</h3>
                <div class="recipes-container">
                    <?php foreach ($pinned_recipes as $recipe): ?>
                        <div class="recipe-card">
                            <h4 class="recipe-title">
                                <a href="recipe.php?recipe_id=<?php echo $recipe["recipe_id"]; ?>" class="recipe-link">
                                    <?php echo htmlspecialchars($recipe["title"]); ?>
                                </a>
                            </h4>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
