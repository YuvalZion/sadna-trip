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

// Format the dates to dd/mm/yyyy
$start_date_formatted = (new DateTime($start_date))->format('d/m/Y');
$end_date_formatted = (new DateTime($end_date))->format('d/m/Y');

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
            SELECT '$email', $trip_num, item_name, 0 FROM items_checklist WHERE item_type = 'basic' OR item_type = '$email' AND category NOT IN ('פריטים שנוספו לטיול זה') ";
    $conn->query($sql);


    if ($is_winter_trip) {
        // Insert additional items for winter trips
        $sql = "INSERT INTO user_checklist (email, trip_num, item_name, isCheck)
                SELECT '$email', $trip_num, item_name, 0 FROM items_checklist WHERE item_type = 'winter'";
        $conn->query($sql);
    }

    // Insert additional items if the destination is New York
    if ($destination === 'New York') {
        $sql = "INSERT INTO user_checklist (email, trip_num, item_name, isCheck)
                SELECT '$email', $trip_num, item_name, 0 FROM items_checklist WHERE item_type = 'New York'";
        $conn->query($sql);
    }
}

// Add sport items based on attraction types
$sql = "
    SELECT ad.Attraction_Number 
    FROM Routes_trip rt
    JOIN Attraction_Data ad ON rt.Attraction_Number = ad.Attraction_Number
    WHERE rt.trip_num = $trip_num 
    AND ad.Type_of_Attraction IN ('parks', 'field and nature trips', 'extreme and sports')";
    
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $sql = "
        INSERT INTO user_checklist (email, trip_num, item_name, isCheck)
        SELECT '$email', $trip_num, item_name, 0 
        FROM items_checklist 
        WHERE item_type = 'Sport'";
    $conn->query($sql);
}

// Add Water items based on attraction types
$sql = "
    SELECT ad.Attraction_Number 
    FROM Routes_trip rt
    JOIN Attraction_Data ad ON rt.Attraction_Number = ad.Attraction_Number
    WHERE rt.trip_num = $trip_num 
    AND ad.Type_of_Attraction IN ('water activities') OR ad.Subcategory IN ('Water Park')";
    
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $sql = "
        INSERT INTO user_checklist (email, trip_num, item_name, isCheck)
        SELECT '$email', $trip_num, item_name, 0 
        FROM items_checklist 
        WHERE item_type = 'water'";
    $conn->query($sql);
}

// Add skiing items based on attraction types
$sql = "
    SELECT ad.Attraction_Number 
    FROM Routes_trip rt
    JOIN Attraction_Data ad ON rt.Attraction_Number = ad.Attraction_Number
    WHERE rt.trip_num = $trip_num 
    AND ad.Subcategory IN ('Skiing')";
    
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $sql = "
        INSERT INTO user_checklist (email, trip_num, item_name, isCheck)
        SELECT '$email', $trip_num, item_name, 0 
        FROM items_checklist 
        WHERE item_type = 'Ski'";
    $conn->query($sql);
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

// A query to retrieve the relevant items and unify them according to the categories
$sql = " 
SELECT DISTINCT 
    ic.category, 
    uc.isCheck, 
    uc.item_name
FROM 
    user_checklist uc
JOIN 
    items_checklist ic ON uc.item_name = ic.item_name
JOIN (
    SELECT 
        item_name, 
        MIN(
            CASE 
                WHEN category = 'פריטים שנוספו לטיול זה' THEN 1
                WHEN category = 'פריטים אישיים' THEN 2               
                ELSE 0
            END
        ) as category_priority
    FROM 
        items_checklist
    GROUP BY 
        item_name
) ic_prioritized ON ic.item_name = ic_prioritized.item_name
AND (
    (ic_prioritized.category_priority = 0 AND ic.category <> 'פריטים שנוספו לטיול זה')
    OR
    (ic_prioritized.category_priority = 1 AND ic.category = 'פריטים שנוספו לטיול זה')
    OR
    (ic_prioritized.category_priority = 2 AND ic.category = 'פריטים אישיים')
)
WHERE 
    uc.trip_num = $trip_num
    AND (
        (ic.category = 'משימות קבוצתיות')
        OR
        (uc.email = '$email' AND (ic.category NOT IN ('פריטים אישיים') OR ic.item_type = '$email'))
    )
ORDER BY 
    ic.category;
";

        
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>צ'ק ליסט</title>
    <link rel="stylesheet" href="checklist.css">
    <script>
        function toggleCategory(category) {
             // Select all elements with class 'category-content'
            var allContent = document.querySelectorAll('.category-content');
            // Loop through each element with class 'category-content'
            allContent.forEach(function(content) {
                // Hide content that does not match the specified category
                if (content.id !== 'category-content-' + category) {
                    content.style.display = 'none';
                }
            });
             // Select the content to toggle based on the given category
            var content = document.getElementById('category-content-' + category);
            // Toggle the display of the selected content
            if (content.style.display === 'block') {
                content.style.display = 'none';
            } else {
                content.style.display = 'block';
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="logo-image"> <img src="../images/logo.jpg" alt="Logo"></div>
        <h2>צ'ק ליסט</h2>
        <div class="form-container">
            <h3><?php echo htmlspecialchars($destination) . " " . htmlspecialchars($start_date_formatted) . " - " . htmlspecialchars($end_date_formatted); ?></h3>
            <div class="menu-container">
                <form method="post" action="save_checklist.php">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="hidden" name="trip_num" value="<?php echo htmlspecialchars($trip_num); ?>">
                    
                    <?php
                    // Display the categories in the center of the page as green buttons
                    foreach (array_keys($items_by_category) as $category) {
                        echo "<button type='button' class='category-btn' onclick=\"toggleCategory('" . htmlspecialchars($category) . "')\">" . htmlspecialchars($category) . "</button>";
                        echo "<div id='category-content-" . htmlspecialchars($category) . "' class='category-content'>";
                        echo "<ul class='items-list'>";
                        foreach ($items_by_category[$category] as $item) {
                            $checked = $item['isCheck'] ? 'checked' : '';
                            echo "<li class='item-container'>";
                            echo "<button type='submit' name='delete_item' value='" . htmlspecialchars($item['name']) . "' class='remove-btn'>הסר</button>";
                            echo "<input type='checkbox' name='items[]' value='" . htmlspecialchars($item['name']) . "' $checked> <span class='item-name'>" . htmlspecialchars($item['name']) . "</span>";
                            echo "</li>";
                        }
                        echo "</ul></div>";
                    }
                    ?>
                    <div>
                        <h3>הוספת פריטים</h3>
                        <input type="text" name="new_item" placeholder="שם פריט חדש">
                        <button type="submit" name="action" value="add_item">+</button>
                    </div>
                    <div>
                        <h3>הוספת משימות קבוצתיות</h3>
                        <input type="text" name="new_group_item" placeholder="שם פריט חדש">
                        <button type="submit" name="action" value="add_group_item">+</button>
                    </div>
                    <div class="form-footer">
                        <button type="submit" name="action" value="save_checklist"> שמירת שינויים <br>ומעבר לדף הקודם</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
