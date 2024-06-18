<?php
$servername = "localhost";
$username = "zlilma_admin_smy";
$password = "easy_trip123";
$dbname = "zlilma_Easy_Trip";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$destination = $_POST['trip_destination'];
$date_start = $_POST['start_date'];
$date_end = $_POST['end_date'];
$hotel = $_POST['hotel'];
$land_time = $_POST['outbound_flight'];
$return_flight_time = $_POST['return_flight'];
$airline = $_POST['airline'];
$budget = $_POST['budget'];
$trip_type = $_POST['trip_type'];


// Insert data into database
$sql = "INSERT INTO Trips (destination, date_start, date_end, hotel, land_time, return_flight_time, airline, budget, trip_type, email)
        VALUES ('$destination', '$date_start', '$date_end', '$hotel', '$land_time', '$return_flight_time', '$airline', '$budget', '$trip_type', '$email')";

if ($conn->query($sql) === TRUE) {
    $trip_num = $conn->insert_id; // Get the last inserted id (trip_num)
    header("Location: add_members.php?trip_num=" . $trip_num . "&email=" . urlencode($email));
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
