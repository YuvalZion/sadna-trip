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
// Get the trip_num obtained from the previous page using Post
$trip_num = $_POST['trip_num'];

// Define an array to hold the selected subcategories
$selected_subcategories = [];

// Check and add selected subcategories to the array
$categories = ['museums', 'parks', 'Water_activities', 'Terrain_nature', 'sports', 'religion-culture', 'shopping', 'nightlife', 'shows', 'restaurants'];
foreach ($categories as $category) {
    if (isset($_POST[$category])) {
        foreach ($_POST[$category] as $subcategory) {
            $selected_subcategories[] = $subcategory;
        }
    }
}

// Prepare and bind the statement
$stmt = $conn->prepare("INSERT INTO subcategory (subcategory_name, trip_num) VALUES (?, ?)");
$stmt->bind_param("si", $subcategory_name, $trip_num);

// Insert each selected subcategory into the database
foreach ($selected_subcategories as $subcategory_name) {
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Redirect to a success page or another page
header("Location: route.php?trip_num=" . ($trip_num));
exit();

?>