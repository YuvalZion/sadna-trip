<?php
// Get email and trip number from query parameters
$email = $_GET['email'];
$trip_num = $_GET['trip_num'];

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
$trip_num = $conn->real_escape_string($trip_num);

// Function to calculate age
function calculateAge($birthDate) {
    $birthDate = new DateTime($birthDate);
    $currentDate = new DateTime();
    $age = $currentDate->diff($birthDate);
    return $age->y;
}

// Fetch trip members details from trip_group
$sql = "
    SELECT up.user_name, up.date_birth, up.email, up.phone 
    FROM trip_group tg
    JOIN User_profile up ON tg.email = up.email
    WHERE tg.trip_num = '$trip_num'
    ";
$result = $conn->query($sql);

$members = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['age'] = calculateAge($row['date_birth']);
        $members[] = $row;
    }
}

// Fetch the additional member from the Trips table
$sql = "SELECT email FROM Trips WHERE trip_num = '$trip_num'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $additional_member_email = $row['email'];
    
    // Fetch the additional member's details
    $sql = "SELECT user_name, date_birth, email, phone FROM User_profile WHERE email = '$additional_member_email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $row['age'] = calculateAge($row['date_birth']);
        $members[] = $row;
    }
}

// Fetch additional trip friends from trip_friend
$sql = "SELECT name_member, age FROM trip_friend WHERE trip_num = '$trip_num'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['user_name'] = $row['name_member'];
        $row['age'] = $row['age'];
        $row['phone'] = '-';
        $members[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>חברי קבוצת הטיול</title>
    <link rel="stylesheet" href="home_page.css">
</head>
<body>
    <div class="container">
        <div class="logo-image"> <img src="../images/logo.jpg" alt="Logo"></div>
        <h2>חברי הקבוצה</h2>
        <div class="table-container">
            <?php if (empty($members)): ?>
                <p>אין חברי קבוצה להצגה.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>חבר טיול</th>
                            <th>גיל</th>
                            <th>טלפון</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($member['age']); ?></td>
                                <td><?php echo htmlspecialchars($member['phone']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div class="form-footer">
            <a href="my_trip.php?email=<?php echo urlencode($email); ?>" class="home-button">חזרה לטיולים שלי</a>
        </div>
    </div>
</body>
</html>
