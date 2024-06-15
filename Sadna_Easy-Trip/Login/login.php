<?php
// Database connection settings
$servername = "localhost";
$username = "zlilma_admin_smy";
$password = "easy_trip123";
$dbname = "zlilma_Easy_Trip";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email and password from form
$email = $_POST['email'];
$password = $_POST['password'];

// Sanitize input
$email = $conn->real_escape_string($email);
$password = $conn->real_escape_string($password);

// Query to check if user exists
$sql = "SELECT * FROM User_profile WHERE email='$email' AND user_password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Redirect to home.php with email as query parameter
    header("Location: ../Home-Page/home_page.php?email=$email");
    exit();
} else {
    echo '<script>alert("האימייל או הסיסמה שגויים"); window.history.back();</script>';
}

$conn->close();
?>
