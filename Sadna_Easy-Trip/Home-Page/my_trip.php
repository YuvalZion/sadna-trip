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
    $sql = "SELECT destination, date_start, date_end FROM Trips WHERE trip_num IN ($tripNumbersStr) ORDER BY date_start ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $trips[] = $row;
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הטיולים שלי</title>
    <link rel="stylesheet" href="home_page.css">
</head>
<body>
    <div class="container">
        <h2>הטיולים שלי</h2>
        <div class="form-container">
            <?php if (!empty($trips)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>יעד</th>
                            <th>תאריך התחלה</th>
                            <th>תאריך סיום</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trips as $trip): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($trip['destination'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($trip['date_start'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($trip['date_end'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>אין טיולים להצגה.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
