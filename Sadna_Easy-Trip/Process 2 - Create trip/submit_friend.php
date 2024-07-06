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

$trip_num = isset($_POST['trip_num']) ? $_POST['trip_num'] : '';

// Retrieve members' data from the form
$friend_names = isset($_POST['friend_name']) ? $_POST['friend_name'] : [];
$friend_ages = isset($_POST['friend_age']) ? $_POST['friend_age'] : [];

// Check if all friend names and ages are empty
if (empty(array_filter($friend_names)) && empty(array_filter($friend_ages))) {
    // Redirect to the next page without adding friends
    header("Location: sub_attractions.php?trip_num=" . $trip_num);
    exit();
}

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO trip_friend (age, trip_num, name_member) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $age, $trip_num, $name);

// Insert each member's data into the database
for ($i = 0; $i < count($friend_names); $i++) {
    $name = $friend_names[$i];
    $age = $friend_ages[$i];
    $stmt->execute();
}

$stmt->close();
$conn->close();

// Redirect to a confirmation page or back to the form
header("Location: sub_attractions.php?trip_num=" . $trip_num);
exit();
?>
