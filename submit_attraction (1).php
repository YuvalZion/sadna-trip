<?php
// Database connection settings
$servername = "localhost";
$username = "zlilma_admin_yz";
$password = "zlilyuval123";
$dbname = "zlilma_test";

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
    'museums' => 'מוזיאונים וגלריות',
    'restaurants' => 'מסעדות',
    'parks' => 'פארקים',
    'water_activities' => 'פעילויות מים',
    'nature_trips' => 'טיולי שטח וטבע',
    'extreme_sports' => 'אקסטרים וספורט',
    'religion_culture' => 'דת ותרבות ',
    'shopping' => 'קניות',
    'night_life' => 'חיי לילה',
    'shows_and_plays' => 'מופעים והצגות'

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
header("Location: user_img.php?email=" . urlencode($email));
exit();
?>