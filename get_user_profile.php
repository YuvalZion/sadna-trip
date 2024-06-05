<?php
// Database connection settings
$servername = "localhost";
$username = "zlilma_admin_yz";
$password = "zlilyuval123";
$dbname = "zlilma_test";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$email = $_SESSION['email'];

$query = "SELECT up.user_name, up.phone, up.passport_expiration_date, up.date_birth, ui.user_image
          FROM User_profile up
          LEFT JOIN User_image ui ON up.email = ui.email
          WHERE up.email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $userData = $result->fetch_assoc();
    $userData['user_image'] = base64_encode($userData['user_image']);
    echo json_encode($userData);
} else {
    echo json_encode(['error' => 'User data not found']);
}
?>
