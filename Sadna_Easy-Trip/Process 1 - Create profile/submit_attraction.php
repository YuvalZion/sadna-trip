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

// Get form data
$email = $_POST['email'];

// Retrieve form data using POST method
$attractionNames = array(
    'museums' => 'museums',
    'restaurants' => 'restaurants',
    'parks' => 'parks',
    'water_activities' => 'water activities',
    'nature_trips' => 'field and nature trips',
    'extreme_sports' => 'extreme and sports',
    'religion_culture' => 'religion and culture',
    'shopping' => 'shopping',
    'night_life' => 'nightlife',
    'shows_and_plays' => 'shows and plays'

);

$response = array();

// Iterate through each attraction
foreach ($attractionNames as $attractionKey => $attractionName) {
    if (isset($_POST[$attractionKey])) {
        $rate = intval($_POST[$attractionKey]); // Convert rating to integer

        // SQL query to insert data into the attractions_rate table
        $sql = "INSERT INTO Attractions_rate (email, Attraction_name, rate) VALUES ('$email', '$attractionName', $rate)";

        if ($conn->query($sql) === TRUE) {
            $response[] = "הדירוג עבור $attractionName הוכנס בהצלחה";
        } else {
            $response[] = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Close connection
$conn->close();

// Output response (optional, for debugging)
// foreach ($response as $message) {
//     echo $message . "<br>";
//}

// Redirect to user_img.php with email parameter
header("Location: add_item.php?email=" . urlencode($email));
exit();
?>