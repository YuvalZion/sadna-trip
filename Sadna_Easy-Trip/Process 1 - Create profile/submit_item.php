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

$success = true; // Flag to check if all items were successfully inserted

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $items = $_POST['items'];

    // Check if all items are empty
    if (empty(array_filter($items))) {
        // Redirect to the next page without adding items
        header("Location: success_profile.php?email=" . urlencode($email));
        exit();
    }

    foreach ($items as $item) {
        if (!empty($item)) {
            // SQL query to insert data into the items_checklist table
            $sql = "INSERT INTO items_checklist (item_name, item_type, trip_num, category) VALUES ('$item', '$email', -1, 'פריטים אישיים')";

            if ($conn->query($sql) !== TRUE) {
                $success = false;
                break; // Exit loop if any insertion fails
            }
        }
    }
}

// Close connection
$conn->close();

header("Location: success_profile.php?email=" . urlencode($email));
exit();
?>