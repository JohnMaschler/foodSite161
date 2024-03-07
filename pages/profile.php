<?php
session_start(); // Start the session

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

include '../db/config.php'; // Include your database config file

// Variable to store user's recipes
$user_recipes = [];

// Fetch user information based on session user_id
$userId = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT username, profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($username, $profilePic);
$stmt->fetch();
$stmt->close();

// Fetch user's recipes from the database
$stmt = $conn->prepare("SELECT recipe_id, title, ingredients, amounts, directions, tags FROM recipes WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$user_recipes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

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
            <li><a href="explore.html">Explore</a></li>
            <li><a href="search.html">Search</a></li>
            <li><a href="upload.php">Upload Recipe</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </nav>

    <main class="profile-content">
        <section class="user-info">
            <!--had to prepend project so it doesn't add pages to the path-->
            <img src="/csen161finalproj/<?php echo htmlspecialchars($profilePic); ?>" alt="———————————————————————————press the button below to add an image" class="profile-pic">
            <h2 class="username"><?php echo htmlspecialchars($username); ?></h2>
            
            <!-- Add a button that triggers the file input -->
            <button onclick="document.getElementById('profile-pic-input').click()">Change Profile Picture</button>
            
            <!-- The file input field is hidden; it's triggered by the button above -->
            <form action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
                <input type="file" name="profilePic" id="profile-pic-input" style="display:none;" onchange="this.form.submit()">
                <input type="hidden" name="userId" value="<?php echo $userId; ?>">
            </form>
        </section>

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
                            $tags = explode(',', $recipe["tags"]); // Split tags into an array
                            foreach ($tags as $tag) {
                                echo "<a href='search.php?tag=" . urlencode(trim($tag)) . "' class='tag-link'>" . htmlspecialchars(trim($tag)) . "</a>"; // Display each tag as a link
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    </main>
</body>
</html>
