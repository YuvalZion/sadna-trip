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

// Get the JSON data sent from the client
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $trip_num = $data['trip_num'];

    // Check if the hotel data is available
    if (isset($data['hotel'])) {
        $hotel = $data['hotel'];

        // Prepare SQL statement to update the hotel name
        $update_stmt = $conn->prepare("UPDATE Trips SET hotel = ? WHERE trip_num = ?");
        if ($update_stmt === false) {
            // Handle prepare error
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Prepare statement error: ' . $conn->error]);
            exit();
        }

        // Bind parameters
        $update_stmt->bind_param("si", $hotel, $trip_num);

        // Execute the statement
        if (!$update_stmt->execute()) {
            // Handle execute error
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Execute statement error: ' . $update_stmt->error]);
            exit();
        }

        // Close update statement
        $update_stmt->close();
    }

    // Prepare SQL statement for insertion or update
    $stmt = $conn->prepare("INSERT INTO Routes_trip (trip_num, Attraction_Number, day_trip) VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE Attraction_Number = VALUES(Attraction_Number), day_trip = VALUES(day_trip)");

    if ($stmt === false) {
        // Handle prepare error
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Prepare statement error: ' . $conn->error]);
        exit();
    }

    // Bind parameters
    $stmt->bind_param("iii", $trip_num, $Attraction_Number, $day_trip);

    // Iterate through the data and execute the statement
    foreach ($data['route'] as $item) {
        $Attraction_Number = $item['Attraction_Number'];
        $day_trip = $item['day'];

        if (!$stmt->execute()) {
            // Handle execute error
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Execute statement error: ' . $stmt->error]);
            exit();
        }
    }

    // Close statement
    $stmt->close();
    
    // Close connection
    $conn->close();

    // Return success response
    echo json_encode(['success' => true]);
} else {
    // If no data received
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit();
}
?>