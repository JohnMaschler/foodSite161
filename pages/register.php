<?php
//start output buffering (don't forget to flush it at the end)
ob_start();
include '../db/config.php'; // Adjust the path as needed

//check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    if ($password !== $confirmPassword) {
        $error_message = "Passwords do not match.";
    } else {//user_id is the primary key
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username=? OR email=? LIMIT 1");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if (!$stmt){
            die("prepare failed: " . $conn->error);
        }
        if ($stmt->num_rows > 0) {
            $error_message = "Username or Email already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);//hash the password
            //insert the info into the USERS table
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashedPassword);
            if ($stmt->execute()) {
                //redirect on successful registration
                //user will then login
                header("Location: login.php");
                exit;
            } else {
                $error_message = "Error: " . $conn->error;
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Foreign Flavors</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post"> <!-- this will later handle the registration logic -->
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>
