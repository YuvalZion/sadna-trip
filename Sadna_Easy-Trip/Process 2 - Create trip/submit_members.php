<?php
// Display errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$servername = "localhost";
$username = "zlilma_admin_smy";
$password = "easy_trip123";
$dbname = "zlilma_Easy_Trip";

// Create and check connection
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get the trip_num obtained from the previous page using Post
$trip_num = $_POST['trip_num'];

// Check if required fields are missing
if (!isset($_POST['trip_num'], $_POST['friend_name'], $_POST['friend_email'])) {
    header("Location: add_friend.php?trip_num=" . $trip_num);
    exit();
}

$friend_names = $_POST['friend_name'];
$friend_emails = $_POST['friend_email'];

// Redirect to the previous page if no friend names and emails are provided
if (empty($friend_names[0]) && empty($friend_emails[0])) {
    // Redirect to the next page without adding friends
    header("Location: add_friend.php?trip_num=" . $trip_num);
    exit();
}

// Function to validate email addresses
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if all friend emails are valid and exist
foreach ($friend_emails as $email) {
    if (!isValidEmail($email)) {
        echo '<script>alert("Invalid email format: \'' . htmlspecialchars($email) . '\'."); window.history.back();</script>';
        exit();
    }
    // Check if the email exists in the User_profile table
    $stmt = $conn->prepare("SELECT COUNT(*) FROM User_profile WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $email_exists = $stmt->fetchColumn();
    
    // If email doesn't exist, show alert and redirect back
    if (!$email_exists) {
        echo '<script>alert("האימייל \'' . htmlspecialchars($email) . '\' לא קיים במערכת."); window.history.back();</script>';
        exit();
    }
}

// Prepare the statement for inserting friend data
$stmt = $conn->prepare("INSERT INTO trip_group (email, trip_num, name_member) VALUES (:email, :trip_num, :name_member)");

// Insert friend's data
for ($i = 0; $i < count($friend_names); $i++) {
    $name_member = $friend_names[$i];
    $email = $friend_emails[$i];
    $stmt->execute([':email' => $email, ':trip_num' => $trip_num, ':name_member' => $name_member]);
}

$conn = null;
// Redirect back to the add_friend.php page with the trip_num parameter
header("Location: add_friend.php?trip_num=" . $trip_num);
exit();
?>