<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - Foreign Flavors</title>
    <link rel="stylesheet" href="../assets/css/search.css">
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
    <div class="search-container">
        <input type="text" id="search-input" placeholder="Search by tags or title..." name="search">
        <button onclick="performSearch()">Search</button>
    </div>

    <!-- Results will be displayed here -->
    <div id="search-results"></div>

    <script src="../assets/js/search.js" defer></script> <!-- Link to your JavaScript file for search functionality -->
</body>
</html>
