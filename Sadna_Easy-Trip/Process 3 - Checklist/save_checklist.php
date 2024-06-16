<?php
// Database connection parameters
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

// Get the email and items from the POST request
$email = isset($_POST['email']) ? $_POST['email'] : '';
$items = isset($_POST['items']) ? $_POST['items'] : [];

if (empty($email)) {
    die("Email not provided.");
}

// Sanitize input
$email = $conn->real_escape_string($email);

// Mark all items as unchecked first
$sql = "UPDATE user_checklist SET isCheck=0 WHERE email='$email'";
$conn->query($sql);

// Prepare and bind
$stmt = $conn->prepare("UPDATE user_checklist SET isCheck=1 WHERE email=? AND item_name=?");

// Loop through checked items and update them in the database
foreach ($items as $item) {
    $item_name = $conn->real_escape_string($item);
    $stmt->bind_param("ss", $email, $item_name);
    $stmt->execute();
}

$stmt->close();
$conn->close();

echo "Checklist saved successfully.";
?>
