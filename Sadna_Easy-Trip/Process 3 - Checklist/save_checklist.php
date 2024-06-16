<?php
// Database connection parameters
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

$email = $_POST['email'];
$trip_num = intval($_POST['trip_num']);
$action = isset($_POST['action']) ? $_POST['action'] : '';
$items = isset($_POST['items']) ? $_POST['items'] : [];
$new_item = isset($_POST['new_item']) ? trim($_POST['new_item']) : '';
$delete_item = isset($_POST['delete_item']) ? trim($_POST['delete_item']) : '';

// Sanitize input (already present in your code)

if ($action === 'add_item') {
    // If a new item is added
    if (!empty($new_item)) {
        $new_item = $conn->real_escape_string($new_item);

        // Add the new item to the user's checklist and User_item table
        $sql = "INSERT INTO user_checklist (email, trip_num, item_name, isCheck) VALUES ('$email', $trip_num, '$new_item', 0)";
        $conn->query($sql);
        $sql = "INSERT INTO User_item (email, item) VALUES ('$email', '$new_item')";
        $conn->query($sql);
    }

    // Redirect back to checklist.php after adding the item
    header("Location: checklist.php?email=$email&trip_num=$trip_num");
    exit();
} elseif ($action === 'save_checklist') {
    // Update the checked status of items
    $sql = "UPDATE user_checklist SET isCheck = 0 WHERE email='$email' AND trip_num=$trip_num";
    $conn->query($sql);

    if (!empty($items)) {
        foreach ($items as $item) {
            $item = $conn->real_escape_string($item);
            $sql = "UPDATE user_checklist SET isCheck = 1 WHERE email='$email' AND trip_num=$trip_num AND item_name='$item'";
            $conn->query($sql);
        }
    }

    // Redirect to my_trip.php after saving the checklist
    header("Location: ../Home-Page/my_trip.php?email=$email&trip_num=$trip_num");
    exit();
} elseif (!empty($delete_item)) {
    // If an item is removed
    $delete_item = $conn->real_escape_string($delete_item);

    // Remove the item from the user's checklist and User_item table
    $sql = "DELETE FROM user_checklist WHERE email='$email' AND trip_num=$trip_num AND item_name='$delete_item'";
    $conn->query($sql);
    $sql = "DELETE FROM User_item WHERE email='$email' AND item='$delete_item'";
    $conn->query($sql);

    // Redirect back to checklist.php after removing the item
    header("Location: checklist.php?email=$email&trip_num=$trip_num");
    exit();
}

$conn->close();
?>
?>