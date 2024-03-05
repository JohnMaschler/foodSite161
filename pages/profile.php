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
                        <h4 class="recipe-title"><?php echo htmlspecialchars($recipe["title"]); ?></h4>
                        <div class="recipe-body">
                        <p class="recipe-ingredients"><strong>Ingredients:</strong></p>
                        <ul>
                            <?php
                            $ingredient_array = explode(',', $recipe["ingredients"]); // Split ingredients into an array
                            $amounts_array = explode(',', $recipe["amounts"]); // Split amounts into an array
                            foreach ($ingredient_array as $index => $ingredient) {
                                // Check if there is a corresponding amount for this ingredient
                                $amount = isset($amounts_array[$index]) ? $amounts_array[$index] : '';
                                echo "<li>" . htmlspecialchars(trim($amount)) . " of " . htmlspecialchars(trim($ingredient)) . "</li>"; // Display each amount with ingredient
                            }
                            ?>
                        </ul>
                            <p class="recipe-directions"><strong>Directions:</strong><br><?php echo nl2br(htmlspecialchars($recipe["directions"])); ?></p>
                            <p class="recipe-tags"><strong>Tags:</strong>
                            <?php
                            $tags = explode(',', $recipe["tags"]); // Split tags into an array
                            foreach ($tags as $tag) {
                                echo "<span>" . htmlspecialchars(trim($tag)) . "</span>"; // Display each tag
                            }
                            ?>
                        </p>    
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    </main>
</body>
</html>
