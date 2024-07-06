<?php
// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Retrieve trip_num from the URL parameter
if (!isset($_GET['trip_num'])) {
    die("trip_num is not set");
}
$trip_num = $_GET['trip_num'];

// Function to calculate age from birthdate
function calculate_age($birthdate) {
    $birthDate = new DateTime($birthdate);
    $currentDate = new DateTime();
    return $currentDate->diff($birthDate)->y;
}

// Get trip details from Trips table
$destination_query = $conn->prepare("SELECT destination, date_start, date_end, email FROM Trips WHERE trip_num = ?");
if (!$destination_query) {
    die("Preparation failed: " . $conn->error);
}
$destination_query->bind_param("i", $trip_num);
$destination_query->execute();
$destination_query->bind_result($destination, $date_start, $date_end, $creator_email);
$destination_query->fetch();
$destination_query->close();

// Calculate trip duration
$date_start = new DateTime($date_start);
$date_end = new DateTime($date_end);
$trip_duration = $date_start->diff($date_end)->days + 1; // +1 to include the end date
$destination_City = $destination;
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

if (!empty($emails)) {
    $in = str_repeat('?,', count($emails) - 1) . '?';
    $email_age_query = $conn->prepare("SELECT date_birth FROM User_profile WHERE email IN ($in)");
    $types = str_repeat('s', count($emails));
    if (!$email_age_query) {
        die("Preparation failed: " . $conn->error);
    }
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
    SELECT Attraction_Name, Latitude, Longitude, Average_Time, Recommended_Season, Min_Age, Max_Participants, Attraction_Number, 	Type_of_Attraction 
    FROM Attraction_Data 
    WHERE Destination = ? AND Subcategory = ?
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
        $unique_key = $row['Attraction_Name'] . '' . $row['Latitude'] . '' . $row['Longitude'];
        // If this key is not already in the array and the recommended season matches the filter, add it
        if (!isset($unique_keys[$unique_key]) && in_array($row['Recommended_Season'], $season_filters)) {
            if (($row['Min_Age'] <= $min_age || $row['Min_Age'] == -1) && ($row['Max_Participants'] >= $participant_count || $row['Max_Participants'] == -1)) {
                $unique_keys[$unique_key] = true;
                $attractions[] = [
                    "name" => $row['Attraction_Name'],
                    "lat" => $row['Latitude'],
                    "lng" => $row['Longitude'],
                    "avgTime" => $row['Average_Time'] * 60, // Convert to minutes
                    "num" => $row['Attraction_Number'],
                    "Subcategory" => $subcategory, 
                    "Type_of_Attraction" => $row['Type_of_Attraction']
                ];
            }
        }
    }
}
$attractions_query->close();

// Calculate average ratings for attractions
$attraction_ratings_query = $conn->prepare("
    SELECT Attraction_name, AVG(rate) as avg_rating 
    FROM Attractions_rate 
    WHERE email IN ($in) 
    GROUP BY Attraction_name
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

// Mapping subcategories to categories
$category_subcategories = [
    "extreme and sports" => ["Abseiling", "Hot Air Balloon", "Karting", "Mountain Climbing", "Paintball", "Skiing", "Skydiving", "Windsurfing"],
    "field and nature trips" => ["Jeeps", "Astronomy", "Bicycle", "Bird Watching", "Hiking", "Horseback Riding"],
    "museums" => ["History", "Architecture", "Design", "Fashion", "Art", "Air and Space", "Science", "Children", "Wax", "Sports"],
    "nightlife" => ["Dance Club", "Bar", "Casino"],
    "parks" => ["Amusement Park", "Water Park", "Nature and Gardens", "Zoo", "Botanical Gardens", "Adventure Park"],
    "religion and culture" => ["Mosque", "Church", "Synagogue", "Stadium", "Historical Site", "Observation Point"],
    "restaurants" => ["Italian", "Asian", "Thai", "Meat", "Cafe", "Vegan", "Indian", "Mediterranean", "Fast Food", "Sweets", "Fish" , "Mexican"],
    "shopping" => ["Mall", "Market", "Shopping Center"],
    "shows and plays" => ["Cinema", "Musical", "Play", "Opera", "Circus"],
    "water activities" => ["Streams", "Rafting", "Surfing", "Diving", "Beaches", "Sailing"]
];

// Map attractions to categories and calculate average ratings
$category_ratings = [];
$category_counts = [];

foreach ($attractions as $attraction) {
    $subcategory = $attraction['Subcategory'];
    foreach ($category_subcategories as $category => $subcategories) {
        if (in_array($subcategory, $subcategories)) {
            if (!isset($category_ratings[$category])) {
                $category_ratings[$category] = 0;
                $category_counts[$category] = 0;
            }
            // Add rating to category ratings
            foreach ($attraction_ratings as $rating) {
                if ($rating['Attraction_Name'] == $attraction['Type_of_Attraction']) {
                    $category_ratings[$category] += $rating['Average_Rating'];
                    $category_counts[$category]++;
                    break;
                }
            }
            break;
        }
    }
}

// Calculate average rating for each category
foreach ($category_ratings as $category => $total_rating) {
    if ($category_counts[$category] > 0) {
        $category_ratings[$category] = $total_rating / $category_counts[$category];
    }
}

// Calculate the sum of average ratings of categories
$sum_of_ratings = array_sum($category_ratings);

// Initialize an empty array to store reduced attractions
$reduced_attractions = [];

// Iterate through each category and calculate the number of attractions to include
foreach ($category_ratings as $category => $average_rating) {
    // Calculate percentage contribution of this category's average rating to the total ratings sum
    $X = $average_rating * 100 / $sum_of_ratings;
    // Calculate the number of attractions to select for this category based on its percentage and trip duration
    $num_attractions = round(($X * 6 * $trip_duration) / 100);
    
    // Filter attractions for the current category based on subcategories
    $category_attractions = array_filter($attractions, function($attraction) use ($category, $category_subcategories) {
        return in_array($attraction['Subcategory'], $category_subcategories[$category]);
    });
    
    // Shuffle the attractions and select a subset based on calculated number
    shuffle($category_attractions);
    $reduced_attractions = array_merge($reduced_attractions, array_slice($category_attractions, 0, $num_attractions));
}

// Ensure the array has at least 6 * trip_duration attractions
$min_attractions = 6 * $trip_duration;
if (count($reduced_attractions) < $min_attractions) {
    // Get remaining attractions that are not already in reduced_attractions
    $remaining_attractions = array_filter($attractions, function($attraction) use ($reduced_attractions) {
        return !in_array($attraction, $reduced_attractions);
    });
    // Shuffle the remaining attractions and add enough to meet the minimum requirement
    shuffle($remaining_attractions);
    // Add enough attractions to meet the minimum requirement
    $additional_attractions_needed = $min_attractions - count($reduced_attractions);
    $reduced_attractions = array_merge($reduced_attractions, array_slice($remaining_attractions, 0, $additional_attractions_needed));
}

$conn->close();
?>