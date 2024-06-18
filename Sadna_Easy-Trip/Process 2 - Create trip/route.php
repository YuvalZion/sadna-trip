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

// Get the destination, start_date, end_date, and email from the Trips table
$destination_query = $conn->prepare("SELECT destination, date_start, date_end, email FROM Trips WHERE trip_num = ?");
if (!$destination_query) {
    die("Preparation failed: " . $conn->error);
}
$destination_query->bind_param("i", $trip_num);
$destination_query->execute();
$destination_query->bind_result($destination, $date_start, $date_end, $creator_email);
$destination_query->fetch();
$destination_query->close();

// Calculate the number of days in the trip
$date_start = new DateTime($date_start);
$date_end = new DateTime($date_end);
$trip_duration = $date_start->diff($date_end)->days + 1; // +1 to include the end date

// Calculate the age of the user who created the trip
function calculate_age($birthdate) {
    $birthDate = new DateTime($birthdate);
    $currentDate = new DateTime();
    $age = $currentDate->diff($birthDate)->y;
    return $age;
}

// Get the age of the trip creator
$user_age_query = $conn->prepare("SELECT date_birth FROM User_profile WHERE email = ?");
if (!$user_age_query) {
    die("Preparation failed: " . $conn->error);
}
$user_age_query->bind_param("s", $creator_email);
$user_age_query->execute();
$user_age_query->bind_result($date_birth);
$user_age_query->fetch();
$creator_age = calculate_age($date_birth);
$user_age_query->close();

// Get the ages of the trip group members
$group_ages = [$creator_age];

$group_emails_query = $conn->prepare("SELECT email FROM trip_group WHERE trip_num = ?");
if (!$group_emails_query) {
    die("Preparation failed: " . $conn->error);
}
$group_emails_query->bind_param("i", $trip_num);
$group_emails_query->execute();
$group_emails_query->bind_result($group_email);

$emails = [$creator_email];
while ($group_emails_query->fetch()) {
    $emails[] = $group_email;
}
$group_emails_query->close();

// Get ages of all group members using a single query
if (!empty($emails)) {
    $in = str_repeat('?,', count($emails) - 1) . '?';
    $email_age_query = $conn->prepare("SELECT date_birth FROM User_profile WHERE email IN ($in)");
    $types = str_repeat('s', count($emails));
    $email_age_query->bind_param($types, ...$emails);
    $email_age_query->execute();
    $email_age_query->bind_result($date_birth);
    while ($email_age_query->fetch()) {
        $group_ages[] = calculate_age($date_birth);
    }
    $email_age_query->close();
}

// Get the ages of the trip friends
$friend_ages_query = $conn->prepare("SELECT age FROM trip_friend WHERE trip_num = ?");
if (!$friend_ages_query) {
    die("Preparation failed: " . $conn->error);
}
$friend_ages_query->bind_param("i", $trip_num);
$friend_ages_query->execute();
$friend_ages_query->bind_result($friend_age);

while ($friend_ages_query->fetch()) {
    $group_ages[] = $friend_age;
}
$friend_ages_query->close();

// Determine the minimum age in the group
$min_age = min($group_ages);

// Count the number of participants in the trip
$group_count_query = $conn->prepare("SELECT COUNT(*) FROM trip_group WHERE trip_num = ?");
if (!$group_count_query) {
    die("Preparation failed: " . $conn->error);
}
$group_count_query->bind_param("i", $trip_num);
$group_count_query->execute();
$group_count_query->bind_result($group_count);
$group_count_query->fetch();
$group_count_query->close();

$friend_count_query = $conn->prepare("SELECT COUNT(*) FROM trip_friend WHERE trip_num = ?");
if (!$friend_count_query) {
    die("Preparation failed: " . $conn->error);
}
$friend_count_query->bind_param("i", $trip_num);
$friend_count_query->execute();
$friend_count_query->bind_result($friend_count);
$friend_count_query->fetch();
$friend_count_query->close();

$participant_count = $group_count + $friend_count + 1; // +1 for the trip creator

// Determine the season filter based on end_date
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
    SELECT `Attraction_Name`, `Latitude`, `Longitude`, `Average_Time`, `Recommended_Season`, `Min_Age`, `Attraction_Number`
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
            if ($row['Min_Age'] <= $min_age || $row['Min_Age'] == -1) {
                $unique_keys[$unique_key] = true;
                $attractions[] = [
                    "Attraction_Name" => $row['Attraction_Name'],
                    "Latitude" => $row['Latitude'],
                    "Longitude" => $row['Longitude'],
                    "Average_Time" => $row['Average_Time']*60,
                    "Attraction_Number" => $row['Attraction_Number']
                ];
            }
        }
    }
}
$attractions_query->close();

// Calculate average ratings for attractions
$attraction_ratings_query = $conn->prepare("
    SELECT `Attraction_name`, AVG(`rate`) as avg_rating
    FROM `Attractions_rate`
    WHERE `email` IN ($in)
    GROUP BY `Attraction_name`
");

if (!$attraction_ratings_query) {
    die("Preparation failed: " . $conn->error);
}
$attraction_ratings_query->bind_param($types, ...$emails);
$attraction_ratings_query->execute();
$attraction_ratings_result = $attraction_ratings_query->get_result();

$attraction_ratings = [];
while ($row = $attraction_ratings_result->fetch_assoc()) {
    $attraction_ratings[] = [
        "Attraction_Name" => $row['Attraction_name'],
        "Average_Rating" => $row['avg_rating']
    ];
}
$attraction_ratings_query->close();

// Close the connection
$conn->close();

// Print the attractions array as JavaScript
header("Content-Type: application/javascript");
echo "let attractions = " . json_encode($attractions) . ";";
echo "let attraction_ratings = " . json_encode($attraction_ratings) . ";";
echo "let trip_duration = " . json_encode($trip_duration) . ";";
echo "let participant_count = " . json_encode($participant_count) . ";";
?>
