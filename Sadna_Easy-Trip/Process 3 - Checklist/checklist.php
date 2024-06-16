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

// Get the email and trip number from the previous page
$email = isset($_GET['email']) ? $_GET['email'] : '';
$trip_num = isset($_GET['trip_num']) ? intval($_GET['trip_num']) : 0;

if (empty($email) || empty($trip_num)) {
    die("Email or Trip Number not provided.");
}

// Sanitize input
$email = $conn->real_escape_string($email);
$trip_num = intval($trip_num);

// Fetch the start date and destination of the trip
$sql = "SELECT date_start, date_end, destination FROM Trips WHERE trip_num=$trip_num";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Trip not found.");
}
$row = $result->fetch_assoc();
$start_date = $row['date_start'];
$end_date = $row['date_end'];
$destination = $row['destination'];

// Extract month and day from the start date
$start_date_obj = new DateTime($start_date);
$start_month_day = $start_date_obj->format('m-d');

// Define the winter season ranges
$winter_start = '12-21';
$winter_end = '03-20';

// Check if the start date falls within the winter season
$is_winter_trip = false;
if (($start_month_day >= $winter_start && $start_month_day <= '12-31') || 
    ($start_month_day >= '01-01' && $start_month_day <= $winter_end)) {
    $is_winter_trip = true;
}

// Check if the user already has entries in the user_checklist table for this trip
$sql = "SELECT COUNT(*) AS count FROM user_checklist WHERE email='$email' AND trip_num=$trip_num";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // If no entries, insert all items with isCheck set to 0
    $sql = "INSERT INTO user_checklist (email, trip_num, item_name, isCheck)
            SELECT '$email', $trip_num, item_name, 0 FROM items_checklist WHERE item_type = 'basic'";
    $conn->query($sql);

    if ($is_winter_trip) {
        // Insert additional items for winter trips
        $sql = "INSERT INTO user_checklist (email, trip_num, item_name, isCheck)
                SELECT '$email', $trip_num, item_name, 0 FROM items_checklist WHERE item_type IN ('winter')";
        $conn->query($sql);
    }

    // Insert additional items if the destination is New York
    if ($destination === 'New York') {
        $sql = "INSERT INTO user_checklist (email, trip_num, item_name, isCheck)
                SELECT '$email', $trip_num, item_name, 0 FROM items_checklist WHERE item_type = 'New York'";
        $conn->query($sql);
    }
}

// Fetch the passport expiration date from the User_profile table
$sql = "SELECT passport_expiration_date FROM User_profile WHERE email='$email'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $passport_expiration_date = new DateTime($row['passport_expiration_date']);
    $trip_end_date = new DateTime($end_date);
    $interval = $passport_expiration_date->diff($trip_end_date);

    // Check if the passport's expiration date is less than the trip's end date by more than six months
    if ($interval->invert == 0 || ($interval->y * 12 + $interval->m) <= 6) {
        $sql = "INSERT INTO user_checklist (email, trip_num, item_name, isCheck)
                SELECT '$email', $trip_num, item_name, 0 FROM items_checklist WHERE item_type = 'profile'";
        $conn->query($sql);
    }
}

// SQL query to retrieve items and their checked status for this email and trip
$sql = "SELECT ic.category, uc.item_name, uc.isCheck
        FROM user_checklist uc
        JOIN items_checklist ic ON uc.item_name = ic.item_name
        WHERE uc.email='$email' AND uc.trip_num=$trip_num
        ORDER BY ic.category";

$result = $conn->query($sql);

// Initialize an array to hold items grouped by category
$items_by_category = [];

// Fetch results and group by category
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items_by_category[$row["category"]][] = [
            'name' => $row["item_name"],
            'isCheck' => $row["isCheck"]
        ];
    }
}

// Fetch user-specific items from the User_item table
$sql = "SELECT item FROM User_item WHERE email='$email'";
$result = $conn->query($sql);

// Initialize an array for personal items
$personal_items = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $personal_items[] = [
            'name' => $row['item'],
            'isCheck' => 0
        ];
    }
}

// Add personal items to the items_by_category array under the 'personal items' category
if (!empty($personal_items)) {
    $items_by_category['פריטים אישיים'] = $personal_items;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>Checklist</title>
    <link rel="stylesheet" href="checklist.css">
    <style>
        #menu {
            width: 200px;
            border-right: 1px solid #ccc;
            padding: 20px;
            background-color: #f8f9fa;
        }
        #content {
            padding: 20px;
            flex-grow: 1;
        }
        .hidden {
            display: none;
        }
        .remove-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 12px;
        }
    </style>
    <script>
        function showCategory(category) {
            var categories = document.getElementsByClassName('category');
            for (var i = 0; i < categories.length; i++) {
                categories[i].classList.add('hidden');
            }
            document.getElementById('category-' + category).classList.remove('hidden');
        }

        // Automatically show the first category if any
        document.addEventListener("DOMContentLoaded", function() {
            var firstCategory = document.querySelector(".category");
            if (firstCategory) {
                firstCategory.classList.remove('hidden');
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="form-container">
        <div id="menu">
            <h2>קטגוריות</h2>
            <ul>
                <?php
                // Display the categories in the side menu
                foreach (array_keys($items_by_category) as $category) {
                    echo "<li><a href='#' onclick=\"showCategory('" . htmlspecialchars($category) . "')\">" . htmlspecialchars($category) . "</a></li>";
                }
                ?>
            </ul>
        </div>
        <div id="content">
            <form method="post" action="save_checklist.php">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="trip_num" value="<?php echo htmlspecialchars($trip_num); ?>">
                <?php
                // Display items grouped by category with checkboxes
                foreach ($items_by_category as $category => $items) {
                    echo "<div id='category-" . htmlspecialchars($category) . "' class='category hidden'>";
                    echo "<h2>" . htmlspecialchars($category) . "</h2><ul>";
                    foreach ($items as $item) {
                        $checked = $item['isCheck'] ? 'checked' : '';
                        echo "<li><input type='checkbox' name='items[]' value='" . htmlspecialchars($item['name']) . "' $checked> " . htmlspecialchars($item['name']) . " <button type='submit' name='delete_item' value='" . htmlspecialchars($item['name']) . "' class='remove-btn'>הסר</button></li>";
                    }
                    echo "</ul></div>";
                }
                ?>
                <div>
                    <h3>הוספת פריטים</h3>
                    <input type="text" name="new_item" placeholder="New item name">
                    <button type="submit" name="action" value="add_item">הוסף פריט</button>
                </div>
                <button type="submit" name="action" value="save_checklist">שמירת צ'ק ליסט</button>
            </form>
        </div>
    </div>
    </div>
</body>
</html>
