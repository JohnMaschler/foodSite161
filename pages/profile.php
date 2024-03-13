<?php
session_start(); //start the session

//check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

include '../db/config.php'; // database config file

//variable to store user's recipes
$user_recipes = [];

//fetch user information based on session user_id
$userId = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT username, profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($username, $profilePic);
$stmt->fetch();
$stmt->close();

//fetch user's recipes from the database
$stmt = $conn->prepare("SELECT recipe_id, title, ingredients, amounts, directions, tags FROM recipes WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$user_recipes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

//fetch pinned recipes from the database
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Foreign Flavors</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
    <!-- <link rel="stylesheet" href="../assets/css/styles.css"> -->
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
            <!--prepend project so it doesn't add '/pages' to the path-->
            <img src="/csen161finalproj/<?php echo htmlspecialchars($profilePic); ?>" alt="———————————————————————————press the button below to add an image" class="profile-pic">
            <h2 class="username"><?php echo htmlspecialchars($username); ?></h2>
            
            <!--button that triggers the file input -->
            <button onclick="document.getElementById('profile-pic-input').click()">Change Profile Picture</button>

            <hr />
            
            <!-- The file input field is hidden; it's triggered by the button above -->
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
                                $tags = explode(',', $recipe["tags"]); //split tags into an array
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
                <h3>Recipes You've Pinned</h3>
                <div class="recipes-container">
                    <?php foreach ($pinned_recipes as $recipe): ?>
                        <div class="recipe-card">
                            <h4 class="recipe-title">
                                <a href="recipe.php?recipe_id=<?php echo $recipe["recipe_id"]; ?>" class="recipe-link">
                                    <?php echo htmlspecialchars($recipe["title"]); ?>
                                </a>
                            </h4>
                            <!-- display recipe tags or other info we want here... for now it's just title linked to the recipe-->
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
