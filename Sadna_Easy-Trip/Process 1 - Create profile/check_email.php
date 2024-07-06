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

// Retrieve email from POST data
$email = $conn->real_escape_string($_POST['email']); // Sanitize email input

// SQL query to check if email exists in User_profile table
$sql = "SELECT email FROM User_profile WHERE email = '$email'";
$result = $conn->query($sql);

// Check if any rows were returned
if ($result->num_rows > 0) {
    echo 'exists'; // Email exists in the database
} else {
    echo 'not exists'; // Email does not exist in the database
}

// Close database connection
$conn->close();
?>