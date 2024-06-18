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

// Get the destination and end_date from the Trips table
$destination_query = $conn->prepare("SELECT destination, date_end FROM Trips WHERE trip_num = ?");
if (!$destination_query) {
    die("Preparation failed: " . $conn->error);
}
$destination_query->bind_param("i", $trip_num);
$destination_query->execute();
$destination_query->bind_result($destination, $date_end);
$destination_query->fetch();
$destination_query->close();

// Determine the season filter based on end_date
$date_end = new DateTime($date_end);
$season_filters = [];

if ($date_end >= new DateTime("March 21") && $date_end <= new DateTime("June 20")) {
    $season_filters = ["-1", "Spring, Summer", "Spring, Autumn", "Spring, Summer, Autumn", "Winter, Spring"];
} elseif ($date_end >= new DateTime("June 21") && $date_end <= new DateTime("September 22")) {
    $season_filters = ["-1", "Spring, Summer, Autumn", "Spring, Summer", "Summer", "Summer, Autumn"];
} elseif ($date_end >= new DateTime("September 23") && $date_end <= new DateTime("December 20")) {
    $season_filters = ["-1", "Spring, Summer, Autumn", "Spring, Autumn", "Summer, Autumn"];
} elseif ($date_end >= new DateTime("December 21") || $date_end <= new DateTime("March 20")) {
    $season_filters = ["-1", "Winter", "Winter, Spring"];
}

// Get all subcategories from the subcategory table for the given trip_num
$subcategory_query = $conn->prepare("SELECT subcategory_name FROM subcategory WHERE trip_num = ?");
if (!$subcategory_query) {
    die("Preparation failed: " . $conn->error);
}
$subcategory_query->bind_param("i", $trip_num);
$subcategory_query->execute();
$subcategory_result = $subcategory_query->get_result();

// Collect subcategories
$subcategories = [];
while ($row = $subcategory_result->fetch_assoc()) {
    $subcategories[] = $row['subcategory_name'];
}
$subcategory_query->close();

// Get the filtered attractions from the attraction_data table for each subcategory
$attractions = [];
$unique_keys = [];
$attractions_query = $conn->prepare("
    SELECT `Attraction_Name`, `Latitude`, `Longitude`, `Average_Time`, `Recommended_Season`, `Attraction_Number`
    FROM Attraction_Data 
    WHERE `Destination` = ? AND `Subcategory` = ?
");

if (!$attractions_query) {
    die("Preparation failed: " . $conn->error);
}

foreach ($subcategories as $subcategory) {
    $attractions_query->bind_param("ss", $destination, $subcategory);
    $attractions_query->execute();
    $result = $attractions_query->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Create a unique key based on Attraction Name, Latitude, and Longitude
        $unique_key = $row['Attraction_Name'] . '_' . $row['Latitude'] . '_' . $row['Longitude'];
        
        // If this key is not already in the array and the recommended season matches the filter, add it
        if (!isset($unique_keys[$unique_key]) && in_array($row['Recommended_Season'], $season_filters)) {
            $unique_keys[$unique_key] = true;
            $attractions[] = [
                "Attraction_Name" => $row['Attraction_Name'],
                "Latitude" => $row['Latitude'],
                "Longitude" => $row['Longitude'],
                "Average_Time" => $row['Average_Time'],
                "Attraction_Number" => $row['Attraction_Number']
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
