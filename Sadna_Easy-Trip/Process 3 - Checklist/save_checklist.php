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
// Retrieve form data from POST request
$email = $_POST['email'];
$trip_num = intval($_POST['trip_num']);
$action = isset($_POST['action']) ? $_POST['action'] : '';
$items = isset($_POST['items']) ? $_POST['items'] : [];
$new_item = isset($_POST['new_item']) ? trim($_POST['new_item']) : '';
$new_group_item = isset($_POST['new_group_item']) ? trim($_POST['new_group_item']) : '';
$delete_item = isset($_POST['delete_item']) ? trim($_POST['delete_item']) : '';

// Sanitize input
$email = $conn->real_escape_string($email);
$trip_num = intval($trip_num);
$new_item = $conn->real_escape_string($new_item);
$new_group_item = $conn->real_escape_string($new_group_item);
$delete_item = $conn->real_escape_string($delete_item);

if ($action === 'add_item') {
    // If a new item is added
    if (!empty($new_item)) {
        // Add the new item to items_checklist first
        $stmt = $conn->prepare("INSERT INTO items_checklist (trip_num, item_name, item_type, category) VALUES (?, ?, ?, 'פריטים שנוספו לטיול זה')");
        $stmt->bind_param("iss", $trip_num, $new_item, $email);
        if ($stmt->execute()) {
            // After successfully adding to items_checklist, add to user_checklist
            $stmt = $conn->prepare("INSERT INTO user_checklist (email, trip_num, item_name, isCheck) VALUES (?, ?, ?, 0)");
            $stmt->bind_param("sis", $email, $trip_num, $new_item);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Redirect back to checklist.php after adding the item
    header("Location: checklist.php?email=$email&trip_num=$trip_num");
    exit();
} elseif ($action === 'add_group_item') {
    // If a new group item is added
    if (!empty($new_group_item)) {
        // Add the new item to items_checklist only
        $stmt = $conn->prepare("INSERT INTO items_checklist (trip_num, item_name, item_type, category) VALUES (?, ?, ?, 'משימות קבוצתיות')");
        $stmt->bind_param("iss", $trip_num, $new_group_item, $email);
        if ($stmt->execute()) {
            // After successfully adding to items_checklist, add to user_checklist
            $stmt = $conn->prepare("INSERT INTO user_checklist (email, trip_num, item_name, isCheck) VALUES (?, ?, ?, 0)");
            $stmt->bind_param("sis", $email, $trip_num, $new_group_item);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Redirect back to checklist.php after adding the group item
    header("Location: checklist.php?email=$email&trip_num=$trip_num");
    exit();
} elseif ($action === 'save_checklist') {
    // Reset all checks for the current user and trip
    $stmt = $conn->prepare("UPDATE user_checklist SET isCheck = 0 WHERE email = ? AND trip_num = ?");
    $stmt->bind_param("si", $email, $trip_num);
    $stmt->execute();

    if (!empty($items)) {
        // Prepare statement for updating individual tasks
        $stmt_individual = $conn->prepare("UPDATE user_checklist SET isCheck = 1 WHERE email = ? AND trip_num = ? AND item_name = ?");
        
        // Prepare statement for updating group tasks
        $stmt_group = $conn->prepare("UPDATE user_checklist SET isCheck = 1 WHERE trip_num = ? AND item_name = ? AND EXISTS (SELECT 1 FROM items_checklist ic WHERE ic.item_name = user_checklist.item_name AND ic.category = 'משימות קבוצתיות')");

        foreach ($items as $item) {
            $item = $conn->real_escape_string($item);

            // Check if the item is a group task
            $category_check_stmt = $conn->prepare("SELECT category FROM items_checklist WHERE item_name = ? LIMIT 1");
            $category_check_stmt->bind_param("s", $item);
            $category_check_stmt->execute();
            $category_check_stmt->bind_result($category);
            $category_check_stmt->fetch();
            $category_check_stmt->close();

            if ($category === 'משימות קבוצתיות') {
                // Update group task for all members associated with the trip_num
                $stmt_group->bind_param("is", $trip_num, $item);
                $stmt_group->execute();
            } else {
                // Update individual task for the specific user and trip
                $stmt_individual->bind_param("sis", $email, $trip_num, $item);
                $stmt_individual->execute();
            }
        }
    }

    // Redirect to my_trip.php after saving the checklist
    header("Location: ../Home-Page/my_trip.php?email=$email&trip_num=$trip_num");
    exit();

} elseif (!empty($delete_item)) {
    // If an item is removed
    $stmt = $conn->prepare("DELETE FROM user_checklist WHERE email = ? AND trip_num = ? AND item_name = ?");
    $stmt->bind_param("sis", $email, $trip_num, $delete_item);
    $stmt->execute();

    // Redirect back to checklist.php after removing the item
    header("Location: checklist.php?email=$email&trip_num=$trip_num");
    exit();
}

$conn->close();
?>
