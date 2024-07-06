<?php
// Database connection
$servername = "localhost";
$username = "zlilma_admin_smy";
$password = "easy_trip123";
$dbname = "zlilma_Easy_Trip";

// Create and Check  connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the user's email, trip number, and ratings from the request
$email = $_POST['email'];
$trip_num = $_POST['trip_num'];
$rate = $_POST['rate'];

// Check existing ratings and update or insert accordingly
$sql_check = "SELECT rate FROM Rating WHERE email = ? AND trip_num = ? AND Attraction_Number = ?";
$sql_insert = "INSERT INTO Rating (email, trip_num, Attraction_Number, rate) VALUES (?, ?, ?, ?)";
$sql_update = "UPDATE Rating SET rate = ? WHERE email = ? AND trip_num = ? AND Attraction_Number = ?";

$stmt_check = $conn->prepare($sql_check);
$stmt_insert = $conn->prepare($sql_insert);
$stmt_update = $conn->prepare($sql_update);

foreach ($rate as $Attraction_Number => $rating) {
    if ($rating > 0) {
        $stmt_check->bind_param("sii", $email, $trip_num, $Attraction_Number);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $stmt_update->bind_param("isis", $rating, $email, $trip_num, $Attraction_Number);
            $stmt_update->execute();
        } else {
            $stmt_insert->bind_param("siid", $email, $trip_num, $Attraction_Number, $rating);
            $stmt_insert->execute();
        }
    }
}

$stmt_check->close();
$stmt_insert->close();
$stmt_update->close();
$conn->close();
header("Location: my_trip.php?email=" . urlencode($email));
exit();
?>
