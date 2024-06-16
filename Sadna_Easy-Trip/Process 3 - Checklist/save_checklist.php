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

// Get the email and trip number from the POST request
$email = isset($_POST['email']) ? $_POST['email'] : '';
$trip_num = isset($_POST['trip_num']) ? intval($_POST['trip_num']) : 0;
$items = isset($_POST['items']) ? $_POST['items'] : [];

if (empty($email) || empty($trip_num)) {
    die("Email or Trip Number not provided.");
}

// Sanitize input
$email = $conn->real_escape_string($email);
$trip_num = intval($trip_num);

// Mark all items as unchecked first for this email and trip
$sql = "UPDATE user_checklist SET isCheck=0 WHERE email='$email' AND trip_num=$trip_num";
$conn->query($sql);

// Prepare and bind
$stmt = $conn->prepare("UPDATE user_checklist SET isCheck=1 WHERE email=? AND trip_num=? AND item_name=?");

// Loop through checked items and update them in the database
foreach ($items as $item) {
    $item_name = $conn->real_escape_string($item);
    $stmt->bind_param("sis", $email, $trip_num, $item_name);
    $stmt->execute();
}

$stmt->close();
$conn->close();

header("Location: ../Home-Page/my_trip.php?email=" . urlencode($email));
?>
