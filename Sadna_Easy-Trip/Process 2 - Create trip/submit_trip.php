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

// Retrieve form data from POST request
$email = $_POST['email'];
$destination = $_POST['trip_destination'];
$date_start = $_POST['start_date'];
$date_end = $_POST['end_date'];
$land_time = $_POST['outbound_flight'];
$return_flight_time = $_POST['return_flight'];
$airline = $_POST['airline'];
$budget = $_POST['budget'];



// Insert data of the trip into database in Trips table
$sql = "INSERT INTO Trips (destination, date_start, date_end, land_time, return_flight_time, airline, budget, email)
        VALUES ('$destination', '$date_start', '$date_end', '$land_time', '$return_flight_time', '$airline', '$budget', '$email')";

// Execute the SQL statement and check if the insertion was successful
if ($conn->query($sql) === TRUE) {
    $trip_num = $conn->insert_id; // Get the last inserted id (trip_num)
    // If insertion was successful, redirect the user to the add_members.php page with the email  and trip_num as parameters
    header("Location: add_members.php?trip_num=" . $trip_num . "&email=" . urlencode($email));
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
?>