<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root"; // default XAMPP username
$password = ""; // default XAMPP password is usually empty
$dbname = "flavors_db"; // Ensure this is the correct database name

// Temporarily echo variables to confirm they're correct
// echo "Server Name: $servername<br>";
// echo "Username: $username<br>";
// echo "Database Name: $dbname<br>";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// } else {
//     echo "Connected successfully";
// }

?>
