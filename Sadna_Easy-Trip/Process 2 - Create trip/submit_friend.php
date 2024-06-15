<?php
$servername = "localhost";
$username = "zlilma_admin_smy";
$password = "easy_trip123";
$dbname = "zlilma_Easy_Trip";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve trip_num from the form
$trip_num = $_POST['trip_num'];

// Retrieve members' data from the form
$friend_names = $_POST['friend_name'];
$friend_ages = $_POST['friend_age'];

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
