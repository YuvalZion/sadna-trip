<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Retrieve trip_num from the URL parameter
if (!isset($_GET['trip_num'])) {
    die("trip_num is not set");
}
$trip_num = $_GET['trip_num'];

// Get the destination from the Trips table
$destination_query = $conn->prepare("SELECT destination FROM Trips WHERE trip_num = ?");
$destination_query->bind_param("i", $trip_num);
$destination_query->execute();
$destination_query->bind_result($destination);
$destination_query->fetch();
$destination_query->close();

// Get all subcategories from the subcategory table for the given trip_num
$subcategory_query = $conn->prepare("SELECT subcategory_name FROM subcategory WHERE trip_num = ?");
$subcategory_query->bind_param("i", $trip_num);
$subcategory_query->execute();
$subcategory_result = $subcategory_query->get_result();

// Collect subcategories
$subcategories = [];
while ($row = $subcategory_result->fetch_assoc()) {
    $subcategories[] = $row['subcategory_name'];
}
$subcategory_query->close();

// Get the filtered attractions from the attraction___new_data table for each subcategory
$attractions = [];
$unique_keys = [];
$attractions_query = $conn->prepare("
    SELECT `Attraction Name`, `Latitude`, `Longitude`, `Average Time`
    FROM attraction___new_data 
    WHERE `Destination` = ? AND `Subcategory` = ?
");

foreach ($subcategories as $subcategory) {
    $attractions_query->bind_param("ss", $destination, $subcategory);
    $attractions_query->execute();
    $result = $attractions_query->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Create a unique key based on Attraction Name, Latitude, and Longitude
        $unique_key = $row['Attraction Name'] . '_' . $row['Latitude'] . '_' . $row['Longitude'];
        
        // If this key is not already in the array, add it
        if (!isset($unique_keys[$unique_key])) {
            $unique_keys[$unique_key] = true;
            $attractions[] = [
                "Attraction Name" => $row['Attraction Name'],
                "Latitude" => $row['Latitude'],
                "Longitude" => $row['Longitude'],
                "Average Time" => $row['Average Time']
            ];
        }
    }
}
$attractions_query->close();

// Close the connection
$conn->close();

// Print the attractions array as JavaScript
header("Content-Type: application/javascript");
echo "let attractions = " . json_encode($attractions) . ";";

// Uncomment the following line if you want to redirect to another page after processing
// header("Location: success.php"); // Change this to the page you want to redirect to
// exit();
?>
