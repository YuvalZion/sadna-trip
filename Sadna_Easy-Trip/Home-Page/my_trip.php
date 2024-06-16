<?php
// Get email from query parameter
$email = $_GET['email'];

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

// Sanitize input
$email = $conn->real_escape_string($email);

// Retrieve trip numbers for the user from trip_group table
$tripNumbers = [];
$sql = "SELECT trip_num FROM trip_group WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tripNumbers[] = $row['trip_num'];
    }
}

// Retrieve trip numbers for the user from Trips table
$sql = "SELECT trip_num FROM Trips WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tripNumbers[] = $row['trip_num'];
    }
}

// Remove duplicate trip numbers
$tripNumbers = array_unique($tripNumbers);

// Retrieve trips based on trip numbers
$trips = [];
if (!empty($tripNumbers)) {
    $tripNumbersStr = implode(',', array_map('intval', $tripNumbers)); // Ensure the trip numbers are safe integers
    $sql = "SELECT trip_num, destination, date_start, date_end FROM Trips WHERE trip_num IN ($tripNumbersStr) ORDER BY date_start ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $trips[] = $row;
        }
    }
}

$conn->close();

// Separate trips into future and past
$futureTrips = [];
$pastTrips = [];
$today = date('Y-m-d');

foreach ($trips as $trip) {
    if ($trip['date_end'] < $today) {
        $pastTrips[] = $trip;
    } else {
        $futureTrips[] = $trip;
    }
}
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הטיולים שלי</title>
    <link rel="stylesheet" href="home_page.css">
    <style>
        .trip-block {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
        }
        .trip-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .trip-buttons button {
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>הטיולים שלי</h2>
        <div class="form-container">
            <?php if (!empty($trips)): ?>
               
                

                <h3>טיולים נוכחיים</h3>
                <?php if (!empty($futureTrips)): ?>
                    <?php foreach ($futureTrips as $trip): ?>
                        <div class="trip-block">
                            <h3><?php echo htmlspecialchars($trip['destination'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p>תאריך התחלה: <?php echo htmlspecialchars($trip['date_start'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p>תאריך סיום: <?php echo htmlspecialchars($trip['date_end'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="trip-buttons">
                                <button onclick="window.location.href='itinerary.php?trip_num=<?php echo $trip['trip_num']; ?>&email=<?php echo urlencode($email); ?>'">מסלול</button>
                                <button onclick="window.location.href='members.php?trip_num=<?php echo $trip['trip_num']; ?>&email=<?php echo urlencode($email); ?>'">חברים</button>
                                <button onclick="window.location.href='../Process 3 - Checklist/checklist.php?trip_num=<?php echo $trip['trip_num']; ?>&email=<?php echo urlencode($email); ?>'">רשימת צ'ק</button>
                               
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>אין טיולים עתידיים להצגה.</p>
                <?php endif; ?>

                <h3>טיולים שהסתיימו</h3>
                <?php if (!empty($pastTrips)): ?>
                    <?php foreach ($pastTrips as $trip): ?>
                        <div class="trip-block">
                            <h3><?php echo htmlspecialchars($trip['destination'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p>תאריך התחלה: <?php echo htmlspecialchars($trip['date_start'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p>תאריך סיום: <?php echo htmlspecialchars($trip['date_end'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="trip-buttons">
                                <button onclick="window.location.href='itinerary.php?trip_num=<?php echo $trip['trip_num']; ?>&email=<?php echo urlencode($email); ?>'">מסלול</button>
                                <button onclick="window.location.href='members.php?trip_num=<?php echo $trip['trip_num']; ?>&email=<?php echo urlencode($email); ?>'">חברים</button>
                                <button onclick="window.location.href='../Process 3 - Checklist/checklist.php?trip_num=<?php echo $trip['trip_num']; ?>&email=<?php echo urlencode($email); ?>'">רשימת צ'ק</button>
                                <button onclick="window.location.href='rating.php?trip_num=<?php echo $trip['trip_num']; ?>&email=<?php echo urlencode($email); ?>'">דירוג</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>אין טיולים שהסתיימו להצגה.</p>
                <?php endif; ?>
            <?php else: ?>
                <p>אין טיולים להצגה.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
